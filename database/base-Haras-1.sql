CREATE DATABASE HarasDB;
USE HarasDB;

-- 1. Roles
CREATE TABLE Roles (
    idRol 				INT PRIMARY KEY AUTO_INCREMENT,
    nombreRol 			VARCHAR(100) NOT NULL
) ENGINE = INNODB;

-- 2. TipoInventarios
CREATE TABLE TipoInventarios (
    idTipoinventario 	INT PRIMARY KEY AUTO_INCREMENT,
    nombreInventario 	VARCHAR(100) NOT NULL
) ENGINE = INNODB;

-- 3. TipoMovimientos
CREATE TABLE TipoMovimientos (
    idTipomovimiento 	INT PRIMARY KEY AUTO_INCREMENT,
    movimiento 			ENUM('Entrada', 'Salida') NOT NULL
) ENGINE = INNODB;

-- 4. TipoEquinos
CREATE TABLE TipoEquinos (
    idTipoEquino 		INT PRIMARY KEY AUTO_INCREMENT,
    tipoEquino 			ENUM('Yegua', 'Padrillo', 'Potranca', 'Potrillo') NOT NULL
) ENGINE = INNODB;

-- 5. EstadoMonta
CREATE TABLE EstadoMonta (
    idEstado 			INT PRIMARY KEY AUTO_INCREMENT,
    genero 				ENUM('Macho', 'Hembra') NOT NULL,
    nombreEstado 		ENUM('S/S', 'Servida', 'Por Servir', 'Preñada', 'Vacia', 'Activo', 'Inactivo') NOT NULL
) ENGINE = INNODB;

-- 6. Personal
CREATE TABLE Personal (
    idPersonal 			INT PRIMARY KEY AUTO_INCREMENT,
    nombres 			VARCHAR(100) NOT NULL,
    apellidos 			VARCHAR(100) NOT NULL,
    direccion 			VARCHAR(255) NOT NULL,
    tipodoc 			VARCHAR(20) NOT NULL,
    nrodocumento 		VARCHAR(50) NOT NULL UNIQUE,
    numeroHijos 		INT NOT NULL,
    fechaIngreso 		DATE NULL
) ENGINE = INNODB;

-- 7. Usuarios
CREATE TABLE Usuarios (
    idUsuario 			INT PRIMARY KEY AUTO_INCREMENT,
    idPersonal 			INT NOT NULL,
    correo 				VARCHAR(50) NOT NULL,
    clave 				VARCHAR(100) NOT NULL,
	idRol 				INT,
    inactive_at 		DATETIME NULL,
    CONSTRAINT uk_correo UNIQUE (correo),
    CONSTRAINT fk_usuario_personal FOREIGN KEY (idPersonal) REFERENCES Personal(idPersonal),
    CONSTRAINT fk_usuario_rol FOREIGN KEY (idRol) REFERENCES Roles(idRol)
) ENGINE = INNODB;

-- 8. Implementos
CREATE TABLE Implementos (
    idInventario 		INT PRIMARY KEY AUTO_INCREMENT,
    idTipoinventario 	INT NOT NULL,
    nombreProducto 		VARCHAR(100) NOT NULL,
    descripcion 		TEXT NOT NULL,
    precioUnitario 		DECIMAL(10,2) NOT NULL,
    idTipomovimiento 	INT NOT NULL,
    cantidad 			INT NOT NULL,
    stockFinal 			INT NOT NULL,
    CONSTRAINT fk_implemento_inventario FOREIGN KEY (idTipoinventario) REFERENCES TipoInventarios(idTipoinventario),
    CONSTRAINT fk_implemento_movimiento FOREIGN KEY (idTipomovimiento) REFERENCES TipoMovimientos(idTipomovimiento)
) ENGINE = INNODB;

-- 9. Alimentos
CREATE TABLE Alimentos (
    idAlimento           INT PRIMARY KEY AUTO_INCREMENT,
    idUsuario            INT NOT NULL,
    nombreAlimento       VARCHAR(100) NOT NULL,
    tipoAlimento         VARCHAR(50), -- Grano, Heno, Suplementos, etc.
    cantidad             DECIMAL(10,2) NOT NULL, -- Manejar diferentes unidades
    unidadMedida         VARCHAR(10) NOT NULL, -- Kilos, Gramos, Litros, etc.
    costo                DECIMAL(10,2) NOT NULL,
    lote                 VARCHAR(50), -- Registro de lote
    fechaCaducidad       DATE, -- Fecha de caducidad por lote
    idTipomovimiento     INT NOT NULL, -- '1' para entrada, '2' para salida
    idTipoEquino         INT NULL, -- Solo para salidas
    merma                DECIMAL(10,2), -- Registro de la merma en salidas
    stockFinal           DECIMAL(10,2) NOT NULL, -- Stock final del lote
    fechaIngreso         DATETIME NULL,
    compra               DECIMAL(10,2) NOT NULL,
    fechaMovimiento      DATETIME DEFAULT NOW(), -- Registrar cuándo ocurrió la entrada o salida
    CONSTRAINT UQ_nombreAlimento UNIQUE (nombreAlimento, lote), -- Duplicados por lote
    CONSTRAINT fk_alimento_movimiento FOREIGN KEY (idTipomovimiento) REFERENCES TipoMovimientos(idTipomovimiento),
    CONSTRAINT fk_alimento_usuario FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario),
    CONSTRAINT fk_alimento_tipoequino FOREIGN KEY (idTipoEquino) REFERENCES TipoEquinos(idTipoEquino)
) ENGINE = INNODB;



-- 10. Medicamentos
CREATE TABLE Medicamentos (
    idMedicamento 		INT PRIMARY KEY AUTO_INCREMENT,
    nombreMedicamento 	VARCHAR(100) NOT NULL,
    cantidad 			INT NOT NULL, -- Cambiado de DECIMAL a INT
    caducidad 			DATE NOT NULL,
    precioUnitario 		DECIMAL(10,2) NOT NULL,
    idTipomovimiento 	INT NOT NULL,
    idUsuario 			INT NOT NULL,
    tratamiento 		TEXT,
    CONSTRAINT fk_medicamento_movimiento FOREIGN KEY (idTipomovimiento) REFERENCES TipoMovimientos(idTipomovimiento),
    CONSTRAINT fk_medicamento_usuario FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario)
) ENGINE = INNODB;

-- 11. DetalleMedicamentos
CREATE TABLE DetalleMedicamentos (
    idDetalleMed 		INT PRIMARY KEY AUTO_INCREMENT,
    idMedicamento 		INT NOT NULL,
    dosis 				INT NOT NULL, -- Cambiado de DECIMAL a INT
    fechaInicio 		DATE NOT NULL,
    fechaFin 			DATE NOT NULL,
    CONSTRAINT fk_detallemed_medicamento FOREIGN KEY (idMedicamento) REFERENCES Medicamentos(idMedicamento)
) ENGINE = INNODB;

-- 12. Propietarios
CREATE TABLE Propietarios (  
    idPropietario 		INT PRIMARY KEY AUTO_INCREMENT,  
    nombreHaras 		VARCHAR(100) NOT NULL
) ENGINE = INNODB;

-- 13. Equinos
CREATE TABLE Equinos (
    idEquino 			INT PRIMARY KEY AUTO_INCREMENT,
    nombreEquino 		VARCHAR(100) NOT NULL,
    fechaNacimiento 	DATETIME NULL,
    sexo 				ENUM('Macho', 'Hembra') NOT NULL,
    idTipoEquino 		INT NOT NULL,
    detalles			TEXT,
    idEstadoMonta 		INT NULL,
	nacionalidad 		VARCHAR(50) NULL,
    idPropietario 		INT,  -- Relación con propietarios (puede ser NULL para indicar propiedad del haras propio)
    fotografia			LONGBLOB NULL,
    CONSTRAINT fk_equino_tipoequino FOREIGN KEY (idTipoEquino) REFERENCES TipoEquinos(idTipoEquino),
    CONSTRAINT fk_equino_propietario FOREIGN KEY (idPropietario) REFERENCES Propietarios(idPropietario),
    CONSTRAINT fk_equino_estado_monta FOREIGN KEY (idEstadoMonta) REFERENCES EstadoMonta(idEstado)
) ENGINE = INNODB;

-- 14. Servicios 
CREATE TABLE Servicios (
    idServicio 				INT PRIMARY KEY AUTO_INCREMENT,
    idEquinoMacho 			INT NOT NULL,               -- ID del equino macho
    idEquinoHembra 			INT NOT NULL,              -- ID del equino hembra
    fechaServicio 			DATE NOT NULL,
    tipoServicio 			ENUM('Propio', 'Mixto') NOT NULL,
    detalles 				TEXT NOT NULL,
    idMedicamento 			INT NULL,
    horaEntrada 			TIME NOT NULL,
    horaSalida 				TIME NOT NULL,
    idPropietario 			INT NULL,                   -- Permitir NULL para servicios propios
	costoServicio			DECIMAL(10,2) NULL,
    CONSTRAINT fk_servicio_equino_macho FOREIGN KEY (idEquinoMacho) REFERENCES Equinos(idEquino),
    CONSTRAINT fk_servicio_equino_hembra FOREIGN KEY (idEquinoHembra) REFERENCES Equinos(idEquino),
    CONSTRAINT fk_servicio_medicamento FOREIGN KEY (idMedicamento) REFERENCES Medicamentos(idMedicamento),
    CONSTRAINT fk_servicio_propietario FOREIGN KEY (idPropietario) REFERENCES Propietarios(idPropietario)
) ENGINE = INNODB;

-- 15. Entrenamientos
CREATE TABLE Entrenamientos (
    idEntrenamiento 		INT PRIMARY KEY AUTO_INCREMENT,
    idEquino 				INT NOT NULL,
    fecha					DATETIME NULL,
    tipoEntrenamiento 		VARCHAR(100) NOT NULL,
    duracion 				DECIMAL(5,2) NOT NULL,
    intensidad 				ENUM('baja', 'media', 'alta') NOT NULL,
    comentarios 			TEXT,
    CONSTRAINT fk_entrenamiento_equino FOREIGN KEY (idEquino) REFERENCES Equinos(idEquino)
) ENGINE = INNODB;

-- 16. HistorialMedico
CREATE TABLE HistorialMedico (
    idHistorial 			INT PRIMARY KEY AUTO_INCREMENT,
    idEquino 				INT NOT NULL,
    idUsuario 				INT NOT NULL,
    fecha 					DATE NOT NULL,
    diagnostico 			TEXT NOT NULL,
    tratamiento 			TEXT NOT NULL,
    observaciones 			TEXT NOT NULL,
    recomendaciones 		TEXT NOT NULL,
    CONSTRAINT fk_historial_equino FOREIGN KEY (idEquino) REFERENCES Equinos(idEquino),
    CONSTRAINT fk_historial_usuario FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario)
) ENGINE = INNODB;

-- 17. HistorialHerrero
CREATE TABLE HistorialHerrero (
    idHistorialHerrero 		INT PRIMARY KEY AUTO_INCREMENT,
    idEquino 				INT NOT NULL,
    idUsuario 				INT NOT NULL,
    fecha 					DATE NOT NULL,
    trabajoRealizado 		TEXT NOT NULL,
    herramientasUsadas 		TEXT,
    observaciones 			TEXT,
    CONSTRAINT fk_historialherrero_equino FOREIGN KEY (idEquino) REFERENCES Equinos(idEquino),
    CONSTRAINT fk_historialherrero_usuario FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario)
) ENGINE = INNODB;

-- 18. Campos
CREATE TABLE Campos (
    idCampo 				INT PRIMARY KEY AUTO_INCREMENT,
    numeroCampo 			INT NOT NULL,
    tamanoCampo				DECIMAL(10,2) NOT NULL, -- Reemplazo de 'tamaño' por 'tamano'
    tipoSuelo 				VARCHAR(100) NOT NULL,
    estado 					VARCHAR(50) NOT NULL
) ENGINE = INNODB;

-- 19. TipoRotaciones
CREATE TABLE TipoRotaciones (
    idTipoRotacion 			INT PRIMARY KEY AUTO_INCREMENT,
    nombreRotacion 			VARCHAR(100) NOT NULL,
    detalles 				TEXT
);

-- 20. RotacionCampos
CREATE TABLE RotacionCampos (
    idRotacion 				INT PRIMARY KEY AUTO_INCREMENT,
    idCampo 				INT NOT NULL,
    idTipoRotacion 			INT NOT NULL,
    fechaRotacion 			DATETIME NULL,
    estadoRotacion 			VARCHAR(50) NOT NULL,
    detalleRotacion 		TEXT,
    CONSTRAINT fk_rotacioncampo_campo FOREIGN KEY (idCampo) REFERENCES Campos(idCampo),
    CONSTRAINT fk_rotacioncampo_tiporotacion FOREIGN KEY (idTipoRotacion) REFERENCES TipoRotaciones(idTipoRotacion)
) ENGINE = INNODB;

-- 21. CampanaPotrillos
CREATE TABLE CampanaPotrillos (  -- Reemplazo de 'Campaña' por 'Campana'
    idCampana 				INT PRIMARY KEY AUTO_INCREMENT,
    idPotrillo 				INT NOT NULL,
    registroPrecio 			DECIMAL(10,2) NOT NULL,
    precioSubasta 			DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_campanapotrillos_equino FOREIGN KEY (idPotrillo) REFERENCES Equinos(idEquino)
) ENGINE = INNODB;

-- 22. AsistenciaPersonal
CREATE TABLE AsistenciaPersonal (
    idAsistencia 			INT PRIMARY KEY AUTO_INCREMENT,
    idPersonal 				INT NOT NULL,
    fecha					DATE NOT NULL,
    horaEntrada 			TIME NOT NULL,
    horaSalida 				TIME NOT NULL,
    observaciones 			TEXT,
    CONSTRAINT fk_asistencia_personal FOREIGN KEY (idPersonal) REFERENCES Personal(idPersonal)
) ENGINE = INNODB;