CREATE DATABASE HarasDB;
USE HarasDB;

-- 1. Roles
CREATE TABLE Roles (
    idRol INT PRIMARY KEY AUTO_INCREMENT,
    nombreRol VARCHAR(100) NOT NULL
);

-- 2. TipoInventarios
CREATE TABLE TipoInventarios (
    idTipoinventario INT PRIMARY KEY AUTO_INCREMENT,
    nombreInventario VARCHAR(100) NOT NULL
);

-- 3. TipoMovimientos
CREATE TABLE TipoMovimientos (
    idTipomovimiento INT PRIMARY KEY AUTO_INCREMENT,
    movimiento ENUM('entrada', 'salida') NOT NULL
);

-- 4. TipoEquinos
CREATE TABLE TipoEquinos (
    idTipoEquino INT PRIMARY KEY AUTO_INCREMENT,
    tipoEquino ENUM('yegua', 'padrillo', 'potranca', 'potrillo') NOT NULL
);

-- 5. EstadoMonta
CREATE TABLE EstadoMonta (
    idEstado INT PRIMARY KEY AUTO_INCREMENT,
    genero ENUM('macho', 'hembra') NOT NULL,
    nombreEstado ENUM('S/S', 'Servida', 'por servir', 'preñada', 'vacia', 'activo', 'inactivo') NOT NULL
);

-- 6. Personal
CREATE TABLE Personal (
    idPersonal INT PRIMARY KEY AUTO_INCREMENT,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    direccion VARCHAR(255) NOT NULL,
    tipodoc VARCHAR(20) NOT NULL,
    nrodocumento VARCHAR(50) NOT NULL UNIQUE,
    numeroHijos INT NOT NULL,
    fechaIngreso DATE NULL
);

-- 7. Usuarios
CREATE TABLE Usuarios (
    idUsuario INT PRIMARY KEY AUTO_INCREMENT,
    idPersonal INT NOT NULL,
    correo VARCHAR(50) NOT NULL,
    clave VARCHAR(100) NOT NULL,
	idRol INT,
    inactive_at DATETIME NULL,
    CONSTRAINT fk_usuario_personal FOREIGN KEY (idPersonal) REFERENCES Personal(idPersonal),
    CONSTRAINT fk_usuario_rol FOREIGN KEY (idRol) REFERENCES Roles(idRol)
);

-- 8. Implementos
CREATE TABLE Implementos (
    idInventario INT PRIMARY KEY AUTO_INCREMENT,
    idTipoinventario INT NOT NULL,
    nombreProducto VARCHAR(100) NOT NULL,
    descripcion TEXT NOT NULL,
    precioUnitario DECIMAL(10,2) NOT NULL,
    idTipomovimiento INT NOT NULL,
    cantidad INT NOT NULL,
    stockFinal INT NOT NULL,
    CONSTRAINT fk_implemento_inventario FOREIGN KEY (idTipoinventario) REFERENCES TipoInventarios(idTipoinventario),
    CONSTRAINT fk_implemento_movimiento FOREIGN KEY (idTipomovimiento) REFERENCES TipoMovimientos(idTipomovimiento)
);

-- 9. Alimentos
CREATE TABLE Alimentos (
    idAlimento INT PRIMARY KEY AUTO_INCREMENT,
    idUsuario INT NOT NULL,
    nombreAlimento VARCHAR(100) NOT NULL,
    cantidad DECIMAL(10,2) NOT NULL,
    costo DECIMAL(10,2) NOT NULL,
    idTipoEquino INT NOT NULL,
    idTipomovimiento INT NOT NULL,
    stockFinal INT NOT NULL,
    fechaIngreso DATETIME NULL,
    compra DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_alimento_movimiento FOREIGN KEY (idTipomovimiento) REFERENCES TipoMovimientos(idTipomovimiento),
    CONSTRAINT fk_alimento_usuario FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario),
    CONSTRAINT fk_alimento_tipoequino FOREIGN KEY (idTipoEquino) REFERENCES TipoEquinos(idTipoEquino)
);

-- 10. Medicamentos
CREATE TABLE Medicamentos (
    idMedicamento INT PRIMARY KEY AUTO_INCREMENT,
    nombreMedicamento VARCHAR(100) NOT NULL,
    cantidad DECIMAL(10,2) NOT NULL,
    caducidad DATE NOT NULL,
    precioUnitario DECIMAL(10,2) NOT NULL,
    idTipomovimiento INT NOT NULL,
    idUsuario INT NOT NULL,
    visita TEXT,
    tratamiento TEXT,
    CONSTRAINT fk_medicamento_movimiento FOREIGN KEY (idTipomovimiento) REFERENCES TipoMovimientos(idTipomovimiento),
    CONSTRAINT fk_medicamento_usuario FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario)
);

-- 11. DetalleMedicamentos
CREATE TABLE DetalleMedicamentos (
    idDetalleMed INT PRIMARY KEY AUTO_INCREMENT,
    idMedicamento INT NOT NULL,
    dosis DECIMAL(10,2) NOT NULL,
    fechaInicio DATE NOT NULL,
    fechaFin DATE NOT NULL,
    CONSTRAINT fk_detallemed_medicamento FOREIGN KEY (idMedicamento) REFERENCES Medicamentos(idMedicamento)
);

-- 12. Propietarios
CREATE TABLE Propietarios (  
    idPropietario INT PRIMARY KEY AUTO_INCREMENT,  
    nombreHaras VARCHAR(100) NOT NULL,  
    nombreEquino VARCHAR(100) NOT NULL, 
    genero ENUM('macho', 'hembra') NOT NULL,  
    costoServicio DECIMAL(10,2) NOT NULL
);

-- 13. Equinos
CREATE TABLE Equinos (
    idEquino INT PRIMARY KEY AUTO_INCREMENT,
    nombreEquino VARCHAR(100) NOT NULL,
    fechaNacimiento DATETIME NULL,
    sexo ENUM('macho', 'hembra') NOT NULL,
    idTipoEquino INT NOT NULL,
    detalles TEXT,
    idEstadoMonta INT NULL,
    idPropietario INT,
    generacion VARCHAR(50),
    nacionalidad VARCHAR(50) NOT NULL,
    CONSTRAINT fk_equino_tipoequino FOREIGN KEY (idTipoEquino) REFERENCES TipoEquinos(idTipoEquino),
    CONSTRAINT fk_equino_propietario FOREIGN KEY (idPropietario) REFERENCES Propietarios(idPropietario),
    CONSTRAINT fk_equino_estado_monta FOREIGN KEY (idEstadoMonta) REFERENCES EstadoMonta(idEstado)
);


-- 14. Servicios 
CREATE TABLE Servicios (
    idServicio INT PRIMARY KEY AUTO_INCREMENT,  -- ID único del servicio
    idEquino INT NOT NULL,                      -- ID del equino que recibe el servicio (clave foránea a la tabla Equinos)
    fechaServicio DATE NOT NULL,                -- Fecha del servicio
    tipoServicio ENUM('propio', 'mixto') NOT NULL, -- Tipo de servicio
    detalles TEXT NOT NULL,                     -- Detalles del servicio
    idDetalleMed INT NULL,                      -- ID del medicamento (puede ser NULL)
    horaEntrada TIME NOT NULL,                  -- Hora de inicio del servicio
    horaSalida TIME NOT NULL,                   -- Hora de fin del servicio
    CONSTRAINT fk_servicio_equino FOREIGN KEY (idEquino) REFERENCES Equinos(idEquino),
    CONSTRAINT fk_servicio_medicamento FOREIGN KEY (idDetalleMed) REFERENCES DetalleMedicamentos(idDetalleMed)
);

-- 15. Entrenamientos
CREATE TABLE Entrenamientos (
    idEntrenamiento INT PRIMARY KEY AUTO_INCREMENT,
    idEquino INT NOT NULL,
    fecha DATETIME NULL,
    tipoEntrenamiento VARCHAR(100) NOT NULL,
    duracion DECIMAL(5,2) NOT NULL,
    intensidad ENUM('baja', 'media', 'alta') NOT NULL,
    comentarios TEXT,
    CONSTRAINT fk_entrenamiento_equino FOREIGN KEY (idEquino) REFERENCES Equinos(idEquino)
);

-- 16. HistorialMedico
CREATE TABLE HistorialMedico (
    idHistorial INT PRIMARY KEY AUTO_INCREMENT,
    idEquino INT NOT NULL,
    idUsuario INT NOT NULL,
    fecha DATE NOT NULL,
    diagnostico TEXT NOT NULL,
    tratamiento TEXT NOT NULL,
    observaciones TEXT NOT NULL,
    recomendaciones TEXT NOT NULL,
    CONSTRAINT fk_historial_equino FOREIGN KEY (idEquino) REFERENCES Equinos(idEquino),
    CONSTRAINT fk_historial_usuario FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario)
);

-- 17. HistorialHerrero
CREATE TABLE HistorialHerrero (
    idHistorialHerrero INT PRIMARY KEY AUTO_INCREMENT,
    idEquino INT NOT NULL,
    idUsuario INT NOT NULL,
    fecha DATE NOT NULL,
    trabajoRealizado TEXT NOT NULL,
    herramientasUsadas TEXT,
    observaciones TEXT,
    CONSTRAINT fk_historialherrero_equino FOREIGN KEY (idEquino) REFERENCES Equinos(idEquino),
    CONSTRAINT fk_historialherrero_usuario FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario)
);

-- 18. Campos
CREATE TABLE Campos (
    idCampo INT PRIMARY KEY AUTO_INCREMENT,
    numeroCampo INT NOT NULL,
    tamanoCampo DECIMAL(10,2) NOT NULL, -- Reemplazo de 'tamaño' por 'tamano'
    tipoSuelo VARCHAR(100) NOT NULL,
    estado VARCHAR(50) NOT NULL,
    riego VARCHAR(100) NOT NULL
);

-- 19. TipoRotaciones
CREATE TABLE TipoRotaciones (
    idTipoRotacion INT PRIMARY KEY AUTO_INCREMENT,
    nombreRotacion VARCHAR(100) NOT NULL,
    detalles TEXT
);

-- 20. RotacionCampos
CREATE TABLE RotacionCampos (
    idRotacion INT PRIMARY KEY AUTO_INCREMENT,
    idCampo INT NOT NULL,
    idTipoRotacion INT NOT NULL,
    fechaRotacion DATETIME NULL,
    estadoRotacion VARCHAR(50) NOT NULL,
    detalleRotacion TEXT,
    CONSTRAINT fk_rotacioncampo_campo FOREIGN KEY (idCampo) REFERENCES Campos(idCampo),
    CONSTRAINT fk_rotacioncampo_tiporotacion FOREIGN KEY (idTipoRotacion) REFERENCES TipoRotaciones(idTipoRotacion)
);

-- 21. CampanaPotrillos
CREATE TABLE CampanaPotrillos (  -- Reemplazo de 'Campaña' por 'Campana'
    idCampana INT PRIMARY KEY AUTO_INCREMENT,
    idPotrillo INT NOT NULL,
    registroPrecio DECIMAL(10,2) NOT NULL,
    precioSubasta DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_campanapotrillos_equino FOREIGN KEY (idPotrillo) REFERENCES Equinos(idEquino)
);

-- 22. AsistenciaPersonal
CREATE TABLE AsistenciaPersonal (
    idAsistencia INT PRIMARY KEY AUTO_INCREMENT,
    idPersonal INT NOT NULL,
    fecha DATE NOT NULL,
    horaEntrada TIME NOT NULL,
    horaSalida TIME NOT NULL,
    observaciones TEXT,
    CONSTRAINT fk_asistencia_personal FOREIGN KEY (idPersonal) REFERENCES Personal(idPersonal)
);

