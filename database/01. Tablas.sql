DROP DATABASE IF EXISTS HarasDB;
CREATE DATABASE HarasDB;
USE HarasDB;

DROP TABLE IF EXISTS Roles;
DROP TABLE IF EXISTS TipoInventarios;
DROP TABLE IF EXISTS TipoMovimientos;
DROP TABLE IF EXISTS TipoEquinos;
DROP TABLE IF EXISTS EstadoMonta;
DROP TABLE IF EXISTS Personal;
DROP TABLE IF EXISTS Usuarios;
DROP TABLE IF EXISTS Implementos;
DROP TABLE IF EXISTS Alimentos;
DROP TABLE IF EXISTS HistorialMovimientos;
DROP TABLE IF EXISTS Medicamentos;
DROP TABLE IF EXISTS DetalleMedicamentos;
DROP TABLE IF EXISTS Propietarios;
DROP TABLE IF EXISTS Equinos;
DROP TABLE IF EXISTS Servicios;
DROP TABLE IF EXISTS Entrenamientos;
DROP TABLE IF EXISTS HistorialMedico;
DROP TABLE IF EXISTS HistorialHerrero;
DROP TABLE IF EXISTS Campos;
DROP TABLE IF EXISTS TipoRotaciones;
DROP TABLE IF EXISTS RotacionCampos;



-- 1. Roles
CREATE TABLE Roles (
    idRol 				INT PRIMARY KEY AUTO_INCREMENT,
    nombreRol 			VARCHAR(100) NOT NULL
) ENGINE = INNODB;

-- 2. Personal
CREATE TABLE Personal (
    idPersonal 			INT PRIMARY KEY AUTO_INCREMENT,
    nombres 			VARCHAR(100) NOT NULL,
    apellidos 			VARCHAR(100) NOT NULL,
    direccion 			VARCHAR(255) NOT NULL,
    tipodoc 			VARCHAR(20) NOT NULL,
    nrodocumento 		VARCHAR(50) NOT NULL UNIQUE,
    numeroHijos 		INT NOT NULL,
    fechaIngreso 		DATE  NOT NULL,
    fechaSalida			DATE NULL,
	tipoContrato 		ENUM('Parcial', 'Completo', 'Por Prácticas', 'Otro') NOT NULL
) ENGINE = INNODB;

-- 3. Usuarios
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

-- 4. TipoInventarios
CREATE TABLE TipoInventarios (
    idTipoinventario 	INT PRIMARY KEY AUTO_INCREMENT,
    nombreInventario 	VARCHAR(100) NOT NULL
) ENGINE = INNODB;

-- 5. TipoMovimientos
CREATE TABLE TipoMovimientos (
    idTipomovimiento 	INT PRIMARY KEY AUTO_INCREMENT,
    movimiento 			ENUM('Entrada', 'Salida') NOT NULL
) ENGINE = INNODB;

-- 6. TipoEquinos
CREATE TABLE TipoEquinos (
    idTipoEquino 		INT PRIMARY KEY AUTO_INCREMENT,
    tipoEquino 			ENUM('Yegua', 'Padrillo', 'Potranca', 'Potrillo', 'Recién nacido', 'Destete') NOT NULL
) ENGINE = INNODB;

-- 7. EstadoMonta
CREATE TABLE EstadoMonta (
    idEstado 			INT PRIMARY KEY AUTO_INCREMENT,
    genero 				ENUM('Macho', 'Hembra') NOT NULL,
    nombreEstado 		ENUM('S/S', 'Servida', 'Por Servir', 'Preñada', 'Vacia', 'Activo', 'Inactivo') NOT NULL
) ENGINE = INNODB;

-- 8. Propietarios
CREATE TABLE Propietarios (  
    idPropietario 		INT PRIMARY KEY AUTO_INCREMENT,  
    nombreHaras 		VARCHAR(100) NOT NULL
) ENGINE = INNODB;

-- 9. Equinos
CREATE TABLE Equinos (
    idEquino 			INT PRIMARY KEY AUTO_INCREMENT,
    nombreEquino 		VARCHAR(100) NOT NULL,
    fechaNacimiento 	DATE NULL,
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

-- 10. Implementos
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

-- 11. Alimentos
CREATE TABLE Alimentos (
    idAlimento           INT PRIMARY KEY AUTO_INCREMENT,
    idUsuario            INT NOT NULL,
    nombreAlimento       VARCHAR(100) NOT NULL,
    tipoAlimento         VARCHAR(50), -- Grano, Heno, Suplementos, etc.
    cantidad             DECIMAL(10,2) NOT NULL, -- Manejar diferentes unidades
    unidadMedida         VARCHAR(10) NOT NULL, -- Kilos, Gramos, Litros, etc.
    costo                DECIMAL(10,2) NOT NULL,
    lote                 VARCHAR(50), -- Registro de lote
    fechaCaducidad       DATE NULL, -- Fecha de caducidad por lote
    idTipoEquino         INT NULL, -- Solo para salidas
    merma                DECIMAL(10,2), -- Registro de la merma en salidas
    stockFinal           DECIMAL(10,2) NOT NULL, -- Stock final del lote
    stockMinimo          DECIMAL(10,2) DEFAULT 0,
    fechaIngreso         DATETIME NULL,
    compra               DECIMAL(10,2) NOT NULL,
    fechaMovimiento      DATETIME DEFAULT NOW(), -- Registrar cuándo ocurrió la entrada o salida
    CONSTRAINT UQ_nombreAlimento UNIQUE (nombreAlimento, lote), -- Duplicados por lote
    CONSTRAINT fk_alimento_usuario FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario),
    CONSTRAINT fk_alimento_tipoequino FOREIGN KEY (idTipoEquino) REFERENCES TipoEquinos(idTipoEquino)
) ENGINE = INNODB;

-- 12. HistorialMovimientos
CREATE TABLE HistorialMovimientos (
    idMovimiento INT AUTO_INCREMENT PRIMARY KEY, -- Identificador único para cada movimiento
    idAlimento INT NOT NULL, -- ID del alimento relacionado con el movimiento
    tipoMovimiento VARCHAR(50) NOT NULL, -- Tipo de movimiento ('Entrada' o 'Salida')
    cantidad DECIMAL(10,2) NOT NULL, -- Cantidad involucrada en el movimiento
    idUsuario INT NOT NULL, -- Usuario que realizó el movimiento
    fechaMovimiento DATETIME DEFAULT NOW(), -- Fecha y hora del movimiento
    merma DECIMAL(10,2) DEFAULT 0,
    FOREIGN KEY (idAlimento) REFERENCES Alimentos(idAlimento), -- Relación con la tabla Alimentos
    FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario) -- Relación con la tabla Usuarios
) ENGINE=InnoDB;

-- 13. TiposMedicamentos ----°°°
CREATE TABLE TiposMedicamentos (
    idTipo INT AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(100) NOT NULL UNIQUE  -- Tipo de medicamento, debe ser único
) ENGINE = INNODB;

-- 14. PresentacionesMedicamentos ----°°°
CREATE TABLE PresentacionesMedicamentos (
    idPresentacion INT AUTO_INCREMENT PRIMARY KEY,
    presentacion VARCHAR(100) NOT NULL UNIQUE
) ENGINE = INNODB;

-- 15. CombinacionesMedicamentos ----°°°
CREATE TABLE CombinacionesMedicamentos (
    idCombinacion INT AUTO_INCREMENT PRIMARY KEY,
    idTipo INT NOT NULL,
    idPresentacion INT NOT NULL,
    dosis VARCHAR(50) NOT NULL,
    FOREIGN KEY (idTipo) REFERENCES TiposMedicamentos(idTipo),
    FOREIGN KEY (idPresentacion) REFERENCES PresentacionesMedicamentos(idPresentacion),
    UNIQUE (idTipo, idPresentacion, dosis)
) ENGINE = INNODB;

-- 16. Medicamentos
CREATE TABLE Medicamentos (
    idMedicamento        INT PRIMARY KEY AUTO_INCREMENT,
    nombreMedicamento    VARCHAR(255) NOT NULL,  -- Nombre del medicamento
    descripcion          TEXT NULL,              -- Descripción del medicamento
    lote                 VARCHAR(100) NOT NULL,  -- Número de lote del medicamento
    idCombinacion        INT NOT NULL,           -- ID de combinación de medicamento
    cantidad_stock       INT NOT NULL,           -- Cantidad disponible en stock
    stockMinimo          INT DEFAULT 0,          -- Stock mínimo antes de lanzar alertas
    fecha_registro       DATE NOT NULL,          -- Fecha de registro del medicamento
    fecha_caducidad      DATE NOT NULL,          -- Fecha de caducidad
    precioUnitario       DECIMAL(10,2) NOT NULL, -- Precio unitario del medicamento
    estado               ENUM('Disponible', 'Por agotarse', 'Agotado') DEFAULT 'Disponible', -- Estado del medicamento en el inventario
    idUsuario            INT NOT NULL,           -- Usuario que registró el medicamento
    ultima_modificacion  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Última modificación
    CONSTRAINT fk_medicamento_usuario FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario),
    CONSTRAINT fk_combinacion_medicamento FOREIGN KEY (idCombinacion) REFERENCES CombinacionesMedicamentos(idCombinacion),
    UNIQUE (lote, nombreMedicamento) -- El lote y el nombre del medicamento deben ser únicos
) ENGINE = INNODB;

-- 17. DetalleMedicamentos
CREATE TABLE DetalleMedicamentos (
    idDetalleMed            INT PRIMARY KEY AUTO_INCREMENT,
    idMedicamento           INT NOT NULL, -- Relación con la tabla Medicamentos
    idEquino                INT NOT NULL, -- Relación con la tabla Equinos (quién recibe el medicamento)
    dosis                   VARCHAR(50) NOT NULL, -- Dosis administrada (en mg, ml, etc.)
    frecuenciaAdministracion VARCHAR(50) NOT NULL, -- Frecuencia de administración (cada cuántas horas/días)
    viaAdministracion       VARCHAR(50) NOT NULL, -- Vía de administración (Oral, Intramuscular, etc.)
    pesoEquino              DECIMAL(10,2) NULL, -- Peso del equino al momento de la administración (opcional)
    fechaInicio             DATE NOT NULL, -- Fecha de inicio del tratamiento
    fechaFin                DATE NOT NULL, -- Fecha de finalización del tratamiento
    observaciones           TEXT NULL, -- Observaciones adicionales (opcional)
    reaccionesAdversas      TEXT NULL, -- Registrar posibles reacciones adversas (opcional)
    idUsuario               INT NOT NULL, -- Usuario que administró el medicamento
    CONSTRAINT fk_detallemed_medicamento FOREIGN KEY (idMedicamento) REFERENCES Medicamentos(idMedicamento),
    CONSTRAINT fk_detallemed_equino FOREIGN KEY (idEquino) REFERENCES Equinos(idEquino),
    CONSTRAINT fk_detallemed_usuario FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario)
) ENGINE = INNODB;

-- 18. HistorialMovimientosMedicamentos ----°°°
CREATE TABLE HistorialMovimientosMedicamentos (
    idMovimiento INT PRIMARY KEY AUTO_INCREMENT,     -- ID único del movimiento
    idMedicamento INT NOT NULL,                      -- Relación con el medicamento
    tipoMovimiento ENUM('Entrada', 'Salida', 'Lote Eliminado') NOT NULL, -- Tipo de movimiento
    cantidad DECIMAL(10,2) NOT NULL,                 -- Cantidad que entra o sale
    idUsuario INT NOT NULL,                          -- Usuario que realiza el movimiento
    fechaMovimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Fecha y hora del movimiento
    CONSTRAINT fk_movimiento_medicamento FOREIGN KEY (idMedicamento) REFERENCES Medicamentos(idMedicamento),
    CONSTRAINT fk_movimiento_usuario FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario)
) ENGINE = INNODB;

-- 19. HistorialMedicamentosMedi ----°°°
CREATE TABLE HistorialMedicamentosMedi (
    idHistorial INT PRIMARY KEY AUTO_INCREMENT,
    idMedicamento INT NOT NULL, -- Relación con la tabla Medicamentos
    accion VARCHAR(50) NOT NULL, -- Acción registrada (Agregar, Eliminar, Actualizar, etc.)
    fecha DATETIME DEFAULT NOW(), -- Fecha de la acción
    CONSTRAINT fk_historial_medicamento FOREIGN KEY (idMedicamento) REFERENCES Medicamentos(idMedicamento)
) ENGINE = INNODB;

-- 20. HistorialMovimientosMedi ----°°°
CREATE TABLE HistorialMovimientosMedi (
    idMovimiento INT PRIMARY KEY AUTO_INCREMENT,
    idMedicamento INT NOT NULL, -- Relación con la tabla Medicamentos
    tipoMovimiento VARCHAR(50) NOT NULL, -- Tipo de movimiento (Entrada o Salida)
    cantidad INT NOT NULL, -- Cantidad involucrada en el movimiento
    fechaMovimiento DATETIME DEFAULT NOW(), -- Fecha del movimiento
    CONSTRAINT fk_historial_movimiento_medicamento FOREIGN KEY (idMedicamento) REFERENCES Medicamentos(idMedicamento)
) ENGINE = INNODB;

-- 21. SugerenciasMedicamentos ----°°°
CREATE TABLE SugerenciasMedicamentos (
    idSugerencia INT AUTO_INCREMENT PRIMARY KEY,
    idMedicamento INT,
    sugerencia TEXT,
    fechaRegistro DATETIME DEFAULT NOW(),
    FOREIGN KEY (idMedicamento) REFERENCES Medicamentos(idMedicamento)
) ENGINE = INNODB;

-- 22. CombinacionesValidas ----°°°
CREATE TABLE CombinacionesValidas (
    idCombinacion INT AUTO_INCREMENT PRIMARY KEY,
    nombreMedicamento VARCHAR(255),
    presentacion VARCHAR(100),
    dosis VARCHAR(50),
    tipoMedicamento VARCHAR(100),
    fechaRegistro DATETIME DEFAULT NOW(),
    UNIQUE (nombreMedicamento, presentacion, dosis, tipoMedicamento)
) ENGINE = INNODB;

-- 23. Servicios 
CREATE TABLE Servicios (
    idServicio 				INT PRIMARY KEY AUTO_INCREMENT,
    idEquinoMacho 			INT NOT NULL,               -- ID del equino macho
    idEquinoHembra 			INT NOT NULL,              -- ID del equino hembra
    fechaServicio 			DATE NOT NULL,
    tipoServicio 			ENUM('Propio', 'Mixto') NOT NULL,
    detalles 				TEXT NOT NULL,
    idMedicamento 			INT NULL,
    horaEntrada 			TIMESTAMP NOT NULL,
    horaSalida 				TIME NOT NULL,
    idPropietario 			INT NULL,                   -- Permitir NULL para servicios propios
	costoServicio			DECIMAL(10,2) NULL,
    CONSTRAINT fk_servicio_equino_macho FOREIGN KEY (idEquinoMacho) REFERENCES Equinos(idEquino),
    CONSTRAINT fk_servicio_equino_hembra FOREIGN KEY (idEquinoHembra) REFERENCES Equinos(idEquino),
    CONSTRAINT fk_servicio_medicamento FOREIGN KEY (idMedicamento) REFERENCES Medicamentos(idMedicamento),
    CONSTRAINT fk_servicio_propietario FOREIGN KEY (idPropietario) REFERENCES Propietarios(idPropietario)
) ENGINE = INNODB;

-- 24. Entrenamientos
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

-- 25. HistorialMedico
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

-- 26. HistorialHerrero
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

-- 27. Campos
CREATE TABLE Campos (
    idCampo 				INT PRIMARY KEY AUTO_INCREMENT,
    numeroCampo 			INT NOT NULL,
    tamanoCampo				DECIMAL(10,2) NOT NULL, -- Reemplazo de 'tamaño' por 'tamano'
    tipoSuelo 				VARCHAR(100) NOT NULL,
    estado 					VARCHAR(50) NOT NULL
) ENGINE = INNODB;

-- 28. TipoRotaciones
CREATE TABLE TipoRotaciones (
    idTipoRotacion 			INT PRIMARY KEY AUTO_INCREMENT,
    nombreRotacion 			VARCHAR(100) NOT NULL,
    detalles 				TEXT
) ENGINE = INNODB;

-- 29. RotacionCampos
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

-- 30. CampanaPotrillos
CREATE TABLE CampanaPotrillos (  -- Reemplazo de 'Campaña' por 'Campana'
    idCampana 				INT PRIMARY KEY AUTO_INCREMENT,
    idPotrillo 				INT NOT NULL,
    registroPrecio 			DECIMAL(10,2) NOT NULL,
    precioSubasta 			DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_campanapotrillos_equino FOREIGN KEY (idPotrillo) REFERENCES Equinos(idEquino)
) ENGINE = INNODB;

-- 31. AsistenciaPersonal
CREATE TABLE AsistenciaPersonal (
    idAsistencia 			INT PRIMARY KEY AUTO_INCREMENT,
    idPersonal 				INT NOT NULL,
    fecha					DATE NOT NULL,
    horaEntrada 			TIME NOT NULL,
    horaSalida 				TIME NOT NULL,
    observaciones 			TEXT,
    CONSTRAINT fk_asistencia_personal FOREIGN KEY (idPersonal) REFERENCES Personal(idPersonal)
) ENGINE = INNODB;
