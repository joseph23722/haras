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

-- 7. Estado Monta
CREATE TABLE EstadoMonta (
    idEstadoMonta		INT PRIMARY KEY AUTO_INCREMENT,
    genero 				ENUM('Macho', 'Hembra') NOT NULL,
    nombreEstado 		ENUM('S/S', 'Servida', 'Por Servir', 'Preñada', 'Vacia', 'Activo', 'Inactivo') NOT NULL
) ENGINE = INNODB;

-- 8. Propietarios
CREATE TABLE Propietarios (  
    idPropietario 		INT PRIMARY KEY AUTO_INCREMENT,  
    nombreHaras 		VARCHAR(100) NOT NULL
) ENGINE = INNODB;

CREATE TABLE Nacionalidades
(
	idNacionalidad		INT PRIMARY KEY AUTO_INCREMENT,
    nacionalidad		VARCHAR(30) NOT NULL
) ENGINE = INNODB;

-- 9. Equinos
CREATE TABLE Equinos (
    idEquino 			INT PRIMARY KEY AUTO_INCREMENT,
    nombreEquino 		VARCHAR(100) 				NOT NULL,
    fechaNacimiento 	DATE 						NULL,
    sexo 				ENUM('Macho', 'Hembra') 	NOT NULL,
    idTipoEquino 		INT 						NOT NULL,
    detalles			TEXT						NULL,
    idEstadoMonta 		INT							NULL,
    idNacionalidad      INT                         NULL,
    idPropietario 		INT							NULL,  -- Relación con propietarios (puede ser NULL para indicar propiedad del haras propio)
    pesokg				DECIMAL (5,1)				NULL,
    fotografia			LONGBLOB 					NULL,
    CONSTRAINT fk_equino_tipoequino FOREIGN KEY (idTipoEquino) REFERENCES TipoEquinos(idTipoEquino),
    CONSTRAINT fk_equino_propietario FOREIGN KEY (idPropietario) REFERENCES Propietarios(idPropietario),
    CONSTRAINT fk_equino_estado_monta FOREIGN KEY (idEstadoMonta) REFERENCES EstadoMonta(idEstadoMonta),
	CONSTRAINT fk_equino_nacionalidad FOREIGN KEY (idNacionalidad) REFERENCES nacionalidades(idNacionalidad)
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

-- 
CREATE TABLE LotesAlimento (
    idLote       INT PRIMARY KEY AUTO_INCREMENT,
    lote         VARCHAR(50) NOT NULL,          -- Número del lote
    unidadMedida VARCHAR(10) NOT NULL,          -- Unidad de medida asociada al lote (Kilos, Litros, etc.)
    fechaCaducidad DATE NULL,                   -- Fecha de caducidad del lote (opcional)
    fechaIngreso DATETIME DEFAULT NOW(),        -- Fecha de ingreso del lote
    CONSTRAINT UQ_lote_unidad UNIQUE (lote, unidadMedida)  -- Unicidad de lote por unidad de medida
);

-- 11. Alimentos
CREATE TABLE Alimentos (
    idAlimento           INT PRIMARY KEY AUTO_INCREMENT,
    idUsuario            INT NOT NULL,
    nombreAlimento       VARCHAR(100) NOT NULL,
    tipoAlimento         VARCHAR(50),
    stockActual          DECIMAL(10,2) NOT NULL,   -- Stock actual del alimento
    stockMinimo          DECIMAL(10,2) DEFAULT 0,  -- Stock mínimo para alerta
    estado               ENUM('Disponible', 'Por agotarse', 'Agotado') DEFAULT 'Disponible',  -- Estado del alimento
    unidadMedida         VARCHAR(10) NOT NULL,     -- Unidad de medida del alimento (Kilos, Litros, etc.)
    costo                DECIMAL(10,2) NOT NULL,   -- Precio unitario del alimento
    idLote               INT NOT NULL,             -- Referencia al lote
    idTipoEquino         INT NULL,                 -- Solo para salidas (referencia a tipo de equino)
    merma                DECIMAL(10,2) NULL,       -- Registro de la merma en salidas
    compra               DECIMAL(10,2) NOT NULL,   -- Costo total de compra (costo * cantidad)
    fechaMovimiento      DATETIME DEFAULT NOW(),   -- Fecha del último movimiento
    CONSTRAINT fk_alimento_usuario FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario),  -- Relación con la tabla Usuarios
    CONSTRAINT fk_alimento_lote FOREIGN KEY (idLote) REFERENCES LotesAlimento(idLote),  -- Relación con la tabla Lotes
    CONSTRAINT fk_alimento_tipoequino FOREIGN KEY (idTipoEquino) REFERENCES TipoEquinos(idTipoEquino)  -- Relación con la tabla TipoEquinos
) ENGINE = INNODB;


-- 12. HistorialMovimientos alimentos
CREATE TABLE HistorialMovimientos (
    idMovimiento INT AUTO_INCREMENT PRIMARY KEY,
    idAlimento INT NOT NULL,            -- ID del alimento (relación con Alimentos)
    tipoMovimiento VARCHAR(50) NOT NULL,-- Tipo de movimiento (Entrada/Salida)
    cantidad DECIMAL(10,2) NOT NULL,    -- Cantidad de alimento
    idTipoEquino INT NULL,              -- ID del tipo de equino (solo para Salida)
    idUsuario INT NOT NULL,             -- ID del usuario que realiza el movimiento
    unidadMedida VARCHAR(50) NOT NULL,  -- Unidad de medida (Kilos, Litros, etc.)
    fechaMovimiento DATE DEFAULT NOW(), -- Fecha del movimiento
    merma DECIMAL(10,2) NULL,           -- Merma (solo si aplica)
    FOREIGN KEY (idAlimento) REFERENCES Alimentos(idAlimento),
    FOREIGN KEY (idTipoEquino) REFERENCES TipoEquinos(idTipoEquino), -- Relación con TipoEquinos
    FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario)
) ENGINE=InnoDB;


-- 13. TiposMedicamentos ----°°° admedi
CREATE TABLE TiposMedicamentos (
    idTipo INT AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(100) NOT NULL UNIQUE  -- Tipo de medicamento, debe ser único
) ENGINE = INNODB;

-- 14. PresentacionesMedicamentos ----°°° admedi
CREATE TABLE PresentacionesMedicamentos (
    idPresentacion INT AUTO_INCREMENT PRIMARY KEY,
    presentacion VARCHAR(100) NOT NULL UNIQUE
) ENGINE = INNODB;

CREATE TABLE UnidadesMedida (
    idUnidad INT AUTO_INCREMENT PRIMARY KEY,
    unidad VARCHAR(50) NOT NULL UNIQUE
) ENGINE = INNODB;

-- 15. CombinacionesMedicamentos ----°°° admedi
CREATE TABLE CombinacionesMedicamentos (
    idCombinacion INT AUTO_INCREMENT PRIMARY KEY,
    idTipo INT NOT NULL,
    idPresentacion INT NOT NULL,
    dosis DECIMAL(10, 2) NOT NULL, -- solo la cantidad numérica
    idUnidad INT NOT NULL, -- referencia a la unidad
    FOREIGN KEY (idTipo) REFERENCES TiposMedicamentos(idTipo),
    FOREIGN KEY (idPresentacion) REFERENCES PresentacionesMedicamentos(idPresentacion),
    FOREIGN KEY (idUnidad) REFERENCES UnidadesMedida(idUnidad),
    UNIQUE (idTipo, idPresentacion, dosis, idUnidad) -- combinación única
) ENGINE = INNODB;

-- Tabla de Lotes de Medicamentos - admedi
CREATE TABLE LotesMedicamento (
    idLoteMedicamento INT PRIMARY KEY AUTO_INCREMENT,      -- ID único del lote de medicamento
    lote              VARCHAR(100) NOT NULL,               -- Código o número de lote
    fechaCaducidad    DATE NOT NULL,                       -- Fecha de caducidad del lote
    fechaIngreso DATE DEFAULT (CURDATE()),           -- Fecha en la que se ingresó el lote
    CONSTRAINT UQ_lote_medicamento UNIQUE (lote)  -- Unicidad por lote y unidad de medida
) ENGINE = INNODB;

-- 16  Tabla de Medicamentos
CREATE TABLE Medicamentos (
    idMedicamento         INT PRIMARY KEY AUTO_INCREMENT,
    idUsuario             INT NOT NULL,                    -- Usuario responsable del registro
    nombreMedicamento     VARCHAR(255) NOT NULL, 
    descripcion           TEXT NULL,
    idCombinacion         INT NOT NULL,                    -- Referencia a la combinación de ingredientes del medicamento
    cantidad_stock        INT NOT NULL,                    -- Stock actual del medicamento
    stockMinimo           INT DEFAULT 0,                   -- Stock mínimo para generar alertas
    estado                ENUM('Disponible', 'Por agotarse', 'Agotado') DEFAULT 'Disponible', -- Estado del medicamento
    idTipoEquino INT NULL,
    idLoteMedicamento     INT NOT NULL,                    -- Referencia al lote específico de medicamento
    precioUnitario        DECIMAL(10,2) NOT NULL,          -- Precio unitario
    motivo TEXT  NULL,
    fecha_registro        DATE NOT NULL,                   -- Fecha de registro en el sistema
    ultima_modificacion   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Última modificación
    CONSTRAINT fk_medicamento_usuario FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario),    -- Relación con Usuarios
    CONSTRAINT fk_medicamento_combinacion FOREIGN KEY (idCombinacion) REFERENCES CombinacionesMedicamentos(idCombinacion), -- Relación con combinaciones
    CONSTRAINT fk_medicamento_lote FOREIGN KEY (idLoteMedicamento) REFERENCES LotesMedicamento(idLoteMedicamento), -- Relación con LotesMedicamento
    CONSTRAINT fk_medicamento_tipoequino FOREIGN KEY (idTipoEquino) REFERENCES TipoEquinos(idTipoEquino)
) ENGINE = INNODB;

-- 17. DetalleMedicamentos -- veterinario
CREATE TABLE DetalleMedicamentos (
    idDetalleMed            INT PRIMARY KEY AUTO_INCREMENT,
    idMedicamento           INT NOT NULL,
    idEquino                INT NOT NULL,
    dosis                   VARCHAR(50) NOT NULL,
    frecuenciaAdministracion VARCHAR(50) NOT NULL,
    viaAdministracion       VARCHAR(50) NOT NULL,
    fechaInicio             DATE NOT NULL,
    fechaFin                DATE NOT NULL,
    observaciones           TEXT NULL,
    reaccionesAdversas      TEXT NULL,
    idUsuario               INT NOT NULL,
    tipoTratamiento         ENUM('Primario', 'Complementario') DEFAULT 'Primario',  -- Nueva columna para tipo de tratamiento
    estadoTratamiento       ENUM('Activo', 'Finalizado', 'En pausa') DEFAULT 'Activo',  -- Nueva columna para estado de tratamiento
    CONSTRAINT fk_detallemed_medicamento FOREIGN KEY (idMedicamento) REFERENCES Medicamentos(idMedicamento),
    CONSTRAINT fk_detallemed_equino FOREIGN KEY (idEquino) REFERENCES Equinos(idEquino),
    CONSTRAINT fk_detallemed_usuario FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario)
) ENGINE = INNODB;

-- 19. HistorialMedicamentosMedi ----°°° admedi..
CREATE TABLE HistorialMovimientosMedicamentos (
    idMovimiento INT PRIMARY KEY AUTO_INCREMENT,
    idMedicamento INT NOT NULL,                -- ID del medicamento (relación con Medicamentos)
    tipoMovimiento VARCHAR(50) NOT NULL,       -- Tipo de movimiento (Entrada/Salida)
    cantidad INT NOT NULL,                     -- Cantidad de medicamento
    motivo TEXT NOT NULL,
    idTipoEquino INT NULL,                     -- ID del tipo de equino (solo para Salida)
    idUsuario INT NOT NULL,                    -- ID del usuario que realiza el movimiento
    fechaMovimiento DATE DEFAULT NOW(), -- Fecha del movimiento
    FOREIGN KEY (idMedicamento) REFERENCES Medicamentos(idMedicamento),
    FOREIGN KEY (idTipoEquino) REFERENCES TipoEquinos(idTipoEquino), -- Relación con TipoEquinos
    FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario)
) ENGINE = INNODB;

-- 23. Servicios 
CREATE TABLE Servicios (
    idServicio 				INT PRIMARY KEY AUTO_INCREMENT,
    idEquinoMacho 			INT NOT NULL,
    idEquinoHembra 			INT NOT NULL,
    fechaServicio 			DATE NOT NULL,
    tipoServicio 			ENUM('Propio', 'Mixto') NOT NULL,
    detalles 				TEXT NOT NULL,
    idMedicamento 			INT NULL,
    horaEntrada 			TIME NULL,
    horaSalida 				TIME NULL,
    idPropietario 			INT NULL,
    idEstadoMonta			INT NOT NULL,
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

-- Tipo Suelo
CREATE TABLE tipoSuelo (
	idTipoSuelo				INT PRIMARY KEY AUTO_INCREMENT,
    nombreTipoSuelo			VARCHAR(50) UNIQUE NOT NULL
) ENGINE = INNODB;

-- 27. Campos
CREATE TABLE Campos (
    idCampo 				INT PRIMARY KEY AUTO_INCREMENT,
    numeroCampo 			INT NOT NULL,
    tamanoCampo				DECIMAL(10,2) NOT NULL,
    idTipoSuelo 			INT NOT NULL,
    estado 					VARCHAR(50) NOT NULL
) ENGINE = INNODB;

-- 28. TipoRotaciones
CREATE TABLE TipoRotaciones (
    idTipoRotacion 			INT PRIMARY KEY AUTO_INCREMENT,
    nombreRotacion 			VARCHAR(100) NOT NULL,
    detalles 				TEXT
) ENGINE = INNODB;

/*
NOTA:

NO SELECT, SOLO UN CAMPO TIPO NUMBER QUE SOLO PERMITA NUMEROS
LA FECHA DE ROTACION PUEDE SER ANTES Y DESPUES
ANTES: PASADO
DESPUES, FUTURO Y QUE GENERE UN CALENDARIO, ENVIE NOTIFICACION CUANDO LLEGUE LA FECHA Y VERIFIQUE SI SE CUMPLIO

*/

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

/* CREATE TABLE Carreras (
    idCarrera 				INT PRIMARY KEY AUTO_INCREMENT,
    nombreCarrera 			VARCHAR(100) NOT NULL,
    fechaCarrera 			DATE NOT NULL,
    distancia 				INT NOT NULL,
    lugar 					VARCHAR(100) NOT NULL,
    tipoCarrera 			ENUM('Local', 'Nacional', 'Internacional') NULL,
    premio 					DECIMAL(10,2) NULL,
    tipoSuperficie 			ENUM('Tierra', 'Pasto', 'Sintética', 'Arena') NULL
) ENGINE = INNODB; 

CREATE TABLE ResultadosCarreras (
    idResultado 			INT PRIMARY KEY AUTO_INCREMENT,
    idEquino 				INT NOT NULL,
    idCarrera 				INT NOT NULL,
    posicion 				INT NOT NULL,
    valorPremio 			DECIMAL(10,2) NULL,
    CONSTRAINT fk_resultados_equino FOREIGN KEY (idEquino) REFERENCES Equinos(idEquino),
    CONSTRAINT fk_resultados_carrera FOREIGN KEY (idCarrera) REFERENCES Carreras(idCarrera)
) ENGINE = INNODB; */

-- 30. CampanaPotrillos
CREATE TABLE CampaniaPotrillos (  -- Reemplazo de 'Campaña' por 'Campana'
    idCampania 				INT PRIMARY KEY AUTO_INCREMENT,
    idEquino 				INT NOT NULL,
    carrerascorridas		INT NOT NULL,
    nombrecarreras			VARCHAR(100) NULL,
    resultadoCarrera 		ENUM('1º', '2º', '3º', '4º', 'No clasificado') NULL,
	fechaCarrera         	DATE NULL,
    valorpromedio			DECIMAL(10,2) NULL,
    valormercado			DECIMAL(10,2) NOT NULL,
    rankingmundial			INT NULL,
    CONSTRAINT fk_campanapotrillos_equino FOREIGN KEY (idEquino) REFERENCES Equinos(idEquino)
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


-- 28/10/2024
CREATE TABLE modulos
(
	idmodulo		INT AUTO_INCREMENT PRIMARY KEY,
    modulo			VARCHAR(30)			NOT NULL,
    create_at		DATETIME			NOT NULL DEFAULT NOW(),
    CONSTRAINT uk_modulo_mod UNIQUE (modulo)
) ENGINE = INNODB;

CREATE TABLE vistas
(
	idvista			INT AUTO_INCREMENT PRIMARY KEY,
    idmodulo		INT					NULL,
    ruta			VARCHAR(50)			NOT NULL,
    sidebaroption	CHAR(1)				NOT NULL,
    texto			VARCHAR(20)			NULL,
    icono			VARCHAR(20)			NULL,
    CONSTRAINT fk_idmodulo_vis FOREIGN KEY (idmodulo) REFERENCES modulos (idmodulo),
    CONSTRAINT uk_ruta_vis	UNIQUE(ruta),
    CONSTRAINT ck_sidebaroption_vis CHECK (sidebaroption IN ('S', 'N'))
) ENGINE = INNODB;

CREATE TABLE permisos
(
	idpermiso			INT AUTO_INCREMENT PRIMARY KEY,
    idRol				INT 				NOT NULL,
    idvista				INT 				NOT NULL,
    create_at			DATETIME			NOT NULL DEFAULT NOW(),
    CONSTRAINT fk_idRol_per FOREIGN KEY (idRol) REFERENCES roles (idRol),
    CONSTRAINT fk_idvisita_per FOREIGN KEY (idvista) REFERENCES vistas (idvista),
    CONSTRAINT uk_vista_per UNIQUE (idRol, idvista)
)ENGINE = INNODB;

CREATE TABLE bostas
(
	idbosta				INT AUTO_INCREMENT PRIMARY KEY,
    fecha				DATE 			NOT NULL UNIQUE,
    cantidadsacos		INT				NOT NULL,
    pesoaprox			DECIMAL(4,2)	NOT NULL,
	peso_diario			DECIMAL(7,2) 	NULL,
    peso_semanal		DECIMAL(9,2)	NULL,
    peso_mensual		DECIMAL(12,2)   NULL,
    numero_semana		INT NULL
) ENGINE = INNODB;