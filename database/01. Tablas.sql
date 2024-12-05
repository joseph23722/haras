DROP DATABASE IF EXISTS HarasDB;
CREATE DATABASE HarasDB;
USE HarasDB;

DROP TABLE IF EXISTS Roles;
DROP TABLE IF EXISTS Personal;
DROP TABLE IF EXISTS Usuarios;
DROP TABLE IF EXISTS TipoInventarios;
DROP TABLE IF EXISTS TipoMovimientos;
DROP TABLE IF EXISTS TipoEquinos;
DROP TABLE IF EXISTS EstadoMonta;
DROP TABLE IF EXISTS Propietarios;
DROP TABLE IF EXISTS Nacionalidades;
DROP TABLE IF EXISTS Equinos;
DROP TABLE IF EXISTS Implementos;
DROP TABLE IF EXISTS HistorialImplemento;
DROP TABLE IF EXISTS UnidadesMedidaAlimento;
DROP TABLE IF EXISTS TipoAlimentos;
DROP TABLE IF EXISTS LotesAlimento;
DROP TABLE IF EXISTS TipoAlimento_UnidadMedida;
DROP TABLE IF EXISTS Alimentos;
DROP TABLE IF EXISTS HistorialMovimientos;
DROP TABLE IF EXISTS TiposMedicamentos;
DROP TABLE IF EXISTS PresentacionesMedicamentos;
DROP TABLE IF EXISTS UnidadesMedida;
DROP TABLE IF EXISTS CombinacionesMedicamentos;
DROP TABLE IF EXISTS LotesMedicamento;
DROP TABLE IF EXISTS Medicamentos;
DROP TABLE IF EXISTS DetalleMedicamentos;
DROP TABLE IF EXISTS ViasAdministracion;
DROP TABLE IF EXISTS HistorialMovimientosMedicamentos;
DROP TABLE IF EXISTS Servicios;
DROP TABLE IF EXISTS Entrenamientos;
DROP TABLE IF EXISTS TiposTrabajos;
DROP TABLE IF EXISTS Herramientas;
DROP TABLE IF EXISTS HistorialHerrero;
DROP TABLE IF EXISTS HerramientasUsadasHistorial;
DROP TABLE IF EXISTS TipoSuelo;
DROP TABLE IF EXISTS Campos;
DROP TABLE IF EXISTS TipoRotaciones;
DROP TABLE IF EXISTS RotacionCampos;
DROP TABLE IF EXISTS HistorialEquinos;
DROP TABLE IF EXISTS AsistenciaPersonal;
DROP TABLE IF EXISTS modulos;
DROP TABLE IF EXISTS vistas;
DROP TABLE IF EXISTS permisos;
DROP TABLE IF EXISTS bostas;


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
    estado				BIT NOT NULL DEFAULT 1,
    create_at			TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
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
    nombreEstado 		ENUM('S/S', 'Servida', 'Por Servir', 'Preñada', 'Vacia', 'Con Cria', 'Activo', 'Inactivo') NOT NULL
) ENGINE = INNODB;

-- 8. Propietarios
CREATE TABLE Propietarios (  
    idPropietario 		INT PRIMARY KEY AUTO_INCREMENT,  
    nombreHaras 		VARCHAR(100) NOT NULL
) ENGINE = INNODB;

-- 9. Nacionalidades
CREATE TABLE Nacionalidades
(
	idNacionalidad		INT PRIMARY KEY AUTO_INCREMENT,
    nacionalidad		VARCHAR(30) NOT NULL
) ENGINE = INNODB;

-- 10. Equinos
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
    fotografia			VARCHAR(255)				NULL,
    estado				BIT							NOT NULL, -- Vivo o muerto
    fechaentrada		DATE						NULL,
    fechasalida			DATE						NULL,
    created_at 			TIMESTAMP DEFAULT NOW()		NOT NULL,  -- Fecha y hora de creación
	updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_equino_tipoequino FOREIGN KEY (idTipoEquino) REFERENCES TipoEquinos(idTipoEquino),
    CONSTRAINT fk_equino_propietario FOREIGN KEY (idPropietario) REFERENCES Propietarios(idPropietario),
    CONSTRAINT fk_equino_estado_monta FOREIGN KEY (idEstadoMonta) REFERENCES EstadoMonta(idEstadoMonta),
	CONSTRAINT fk_equino_nacionalidad FOREIGN KEY (idNacionalidad) REFERENCES nacionalidades(idNacionalidad)
) ENGINE = INNODB;

-- 11. fotografias equinos COLECCION FOTOS
CREATE TABLE fotografiaequinos 
(
	idfotografia	INT PRIMARY KEY AUTO_INCREMENT,
    idEquino		INT NOT NULL,
    public_id		VARCHAR(255) NOT NULL,
    created_at		TIMESTAMP DEFAULT NOW() NOT NULL,
    updated_at		TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_public_id_ft FOREIGN KEY (idEquino) REFERENCES Equinos(idEquino) ON DELETE CASCADE
-- ON DELETE CASCADE evita que queden datos huerfanos en la tabla
)ENGINE = INNODB;

-- 12. Implementos
CREATE TABLE Implementos (
    idInventario 		INT PRIMARY KEY AUTO_INCREMENT,
    idTipoinventario 	INT NOT NULL,
    nombreProducto 		VARCHAR(100) NOT NULL,
    descripcion 		TEXT NOT NULL,
    precioUnitario 		DECIMAL(10,2) NULL,
    precioTotal			DECIMAL(10,2) NULL,
    idTipomovimiento 	INT NOT NULL,
    cantidad 			INT NOT NULL,
    stockFinal 			INT NOT NULL,
    estado				BIT NOT NULL,
    create_at			DATETIME 	NOT NULL DEFAULT NOW(),
    CONSTRAINT fk_implemento_nombreProducto UNIQUE(nombreProducto),
    CONSTRAINT fk_implemento_inventario FOREIGN KEY (idTipoinventario) REFERENCES TipoInventarios(idTipoinventario),
    CONSTRAINT fk_implemento_movimiento FOREIGN KEY (idTipomovimiento) REFERENCES TipoMovimientos(idTipomovimiento)
) ENGINE = INNODB;

-- 13. Historial Implemento
CREATE TABLE HistorialImplemento (
    idHistorial        INT PRIMARY KEY AUTO_INCREMENT,
    idInventario       INT NOT NULL,
    idTipoinventario   INT NOT NULL,
    idTipomovimiento   INT NOT NULL,
    cantidad           INT NOT NULL,
    precioUnitario     DECIMAL(10,2) NULL,
    precioTotal		   DECIMAL(10,2) NULL,
    descripcion		   VARCHAR(100)	NULL,
    fechaMovimiento    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_historial_inventario FOREIGN KEY (idInventario) REFERENCES Implementos(idInventario),
    CONSTRAINT fk_historial_tipoinventario FOREIGN KEY (idTipoinventario) REFERENCES TipoInventarios(idTipoinventario)
) ENGINE = INNODB;

-- 14. Tabla Unidades de Medida - alimento
CREATE TABLE UnidadesMedidaAlimento (
    idUnidadMedida 		INT PRIMARY KEY AUTO_INCREMENT,
    nombreUnidad 		VARCHAR(10) NOT NULL UNIQUE      -- Ejemplo: 'Kg', 'L'
) ENGINE = INNODB;

-- 15. Tabla de Tipos de Alimentos
CREATE TABLE TipoAlimentos (
    idTipoAlimento 		INT PRIMARY KEY AUTO_INCREMENT,
    tipoAlimento 		VARCHAR(50) NOT NULL UNIQUE      -- Ejemplo: 'Granos', 'Suplemento'
) ENGINE = INNODB;

-- 16. Tabla Lotes de Alimentos
CREATE TABLE LotesAlimento (
    idLote 				INT PRIMARY KEY AUTO_INCREMENT,
    lote 				VARCHAR(50) NOT NULL,
    fechaCaducidad 		DATE DEFAULT NULL,
    fechaIngreso 		DATETIME DEFAULT NOW(),
    estadoLote 			ENUM('No Vencido', 'Vencido', 'Agotado') DEFAULT 'No Vencido'
) ENGINE = INNODB;

-- 17. Tabla Intermedia para la Relación entre Tipos de Alimento y Unidades de Medida
CREATE TABLE TipoAlimento_UnidadMedida (
    idTipoAlimento 		INT NOT NULL,
    idUnidadMedida 		INT NOT NULL,
    PRIMARY KEY (idTipoAlimento, idUnidadMedida),
    CONSTRAINT fk_tipoalimento FOREIGN KEY (idTipoAlimento) REFERENCES TipoAlimentos(idTipoAlimento) ON DELETE CASCADE,
    CONSTRAINT fk_unidadmedida FOREIGN KEY (idUnidadMedida) REFERENCES UnidadesMedidaAlimento(idUnidadMedida) ON DELETE CASCADE,
	CONSTRAINT uq_tipo_unidad UNIQUE (idTipoAlimento, idUnidadMedida)
) ENGINE = INNODB;


-- 18. Tabla Alimentos
CREATE TABLE Alimentos (
    idAlimento           INT PRIMARY KEY AUTO_INCREMENT,
    idUsuario            INT NOT NULL,
    nombreAlimento       VARCHAR(100) NOT NULL,
    idTipoAlimento       INT NOT NULL,
    stockActual          DECIMAL(10,2) NOT NULL,
    stockMinimo          DECIMAL(10,2) DEFAULT 0,
    estado               ENUM('Disponible', 'Por agotarse', 'Agotado', 'Vencido') DEFAULT 'Disponible',
    idUnidadMedida       INT NOT NULL,
    costo                DECIMAL(10,2) NOT NULL,
    idLote               INT NOT NULL,
    idEquino             INT NULL,
    compra               DECIMAL(10,2) NOT NULL,
    fechaMovimiento      DATETIME DEFAULT NOW(),
    CONSTRAINT fk_alimento_usuario FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario),
    CONSTRAINT fk_alimento_tipoalimento FOREIGN KEY (idTipoAlimento) REFERENCES TipoAlimentos(idTipoAlimento),
    CONSTRAINT fk_alimento_unidadmedida FOREIGN KEY (idUnidadMedida) REFERENCES UnidadesMedidaAlimento(idUnidadMedida),
    CONSTRAINT fk_alimento_lote FOREIGN KEY (idLote) REFERENCES LotesAlimento(idLote),
    CONSTRAINT fk_alimento_equino FOREIGN KEY (idEquino) REFERENCES Equinos(idEquino)
) ENGINE = INNODB;

-- 19. Tabla MermasAlimento
CREATE TABLE MermasAlimento (
    idMerma       	INT PRIMARY KEY AUTO_INCREMENT,
    idAlimento     	INT NOT NULL,
    cantidadMerma 	DECIMAL(10,2) NOT NULL,
    fechaMerma     	DATETIME DEFAULT NOW(),
    motivo         	VARCHAR(255) NULL,
    CONSTRAINT fk_merma_alimento FOREIGN KEY (idAlimento) REFERENCES Alimentos(idAlimento) ON DELETE CASCADE
) ENGINE = INNODB;

-- 20. Tabla HistorialMovimientos - alimento
CREATE TABLE HistorialMovimientos (
    idMovimiento 		INT AUTO_INCREMENT PRIMARY KEY,
    idAlimento 			INT NOT NULL,
    tipoMovimiento 		VARCHAR(50) NOT NULL,
    cantidad 			DECIMAL(10,2) NOT NULL,
    idEquino 			INT NULL,
    idUsuario 			INT NOT NULL,
    unidadMedida 		VARCHAR(50) NOT NULL,
    fechaMovimiento 	DATE DEFAULT NOW(),
    merma 				DECIMAL(10,2) NULL,
    FOREIGN KEY (idAlimento) REFERENCES Alimentos(idAlimento),
    FOREIGN KEY (idEquino) REFERENCES Equinos(idEquino),
    FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario)
) ENGINE=InnoDB;

-- 21. TiposMedicamentos ---- admedi
CREATE TABLE TiposMedicamentos (
    idTipo 				INT AUTO_INCREMENT PRIMARY KEY,
    tipo 				VARCHAR(100) NOT NULL UNIQUE 
) ENGINE = INNODB;

-- 22. PresentacionesMedicamentos ---- admedi
CREATE TABLE PresentacionesMedicamentos (
    idPresentacion 		INT AUTO_INCREMENT PRIMARY KEY,
    presentacion 		VARCHAR(100) NOT NULL UNIQUE
) ENGINE = INNODB;

-- 23. Unidades Medida ---- admedi
CREATE TABLE UnidadesMedida (
    idUnidad 			INT AUTO_INCREMENT PRIMARY KEY,
    unidad 				VARCHAR(50) NOT NULL UNIQUE
) ENGINE = INNODB;

-- 24. CombinacionesMedicamentos ---- admedi
CREATE TABLE CombinacionesMedicamentos (
    idCombinacion 		INT AUTO_INCREMENT PRIMARY KEY,
    idTipo 				INT NOT NULL,
    idPresentacion 		INT NOT NULL,
    dosis 				DECIMAL(10, 2) NOT NULL, 
    idUnidad 			INT NOT NULL, 
    FOREIGN KEY (idTipo) REFERENCES TiposMedicamentos(idTipo),
    FOREIGN KEY (idPresentacion) REFERENCES PresentacionesMedicamentos(idPresentacion),
    FOREIGN KEY (idUnidad) REFERENCES UnidadesMedida(idUnidad),
    UNIQUE (idTipo, idPresentacion, dosis, idUnidad) 
) ENGINE = INNODB;

-- 25. Tabla de Lotes de Medicamentos - admedi
CREATE TABLE LotesMedicamento (
    idLoteMedicamento 	INT PRIMARY KEY AUTO_INCREMENT,  
    lote              	VARCHAR(100) NOT NULL,             
    fechaCaducidad    	DATE NOT NULL,                      
    fechaIngreso 		DATE DEFAULT (CURDATE()),           
    CONSTRAINT UQ_lote_medicamento UNIQUE (lote)  
) ENGINE = INNODB;

-- 26.  Tabla de Medicamentos
CREATE TABLE Medicamentos (
    idMedicamento         	INT PRIMARY KEY AUTO_INCREMENT,
    idUsuario             	INT NOT NULL,
    nombreMedicamento     	VARCHAR(255) NOT NULL, 
    descripcion           	TEXT NULL,
    idCombinacion        	INT NOT NULL,
    cantidad_stock        	INT NOT NULL,
    stockMinimo           	INT DEFAULT 0,
    estado               	ENUM('Disponible', 'Por agotarse', 'Agotado') DEFAULT 'Disponible',
    idEquino 				INT NULL,
    idLoteMedicamento     	INT NOT NULL,
    precioUnitario        	DECIMAL(10,2) NOT NULL,
    motivo 					TEXT  NULL,
    fecha_registro        	DATE NOT NULL,
    ultima_modificacion   	TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_medicamento_usuario FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario),
    CONSTRAINT fk_medicamento_combinacion FOREIGN KEY (idCombinacion) REFERENCES CombinacionesMedicamentos(idCombinacion),
    CONSTRAINT fk_medicamento_lote FOREIGN KEY (idLoteMedicamento) REFERENCES LotesMedicamento(idLoteMedicamento),
    CONSTRAINT fk_medicamento_equino FOREIGN KEY (idEquino) REFERENCES Equinos(idEquino)
) ENGINE = INNODB;

-- 27. ViasAdministracion - veterinario 
CREATE TABLE ViasAdministracion (
    idViaAdministracion 	INT PRIMARY KEY AUTO_INCREMENT,
    nombreVia 				VARCHAR(50) NOT NULL UNIQUE,
    descripcion 			TEXT NULL
) ENGINE = INNODB;

-- 28. DetalleMedicamentos -- veterinario
CREATE TABLE DetalleMedicamentos (
    idDetalleMed            	INT PRIMARY KEY AUTO_INCREMENT,
    idMedicamento           	INT NOT NULL,
    idEquino                	INT NOT NULL,
    dosis                   	VARCHAR(50) NOT NULL,
    frecuenciaAdministracion 	VARCHAR(50) NOT NULL,
    idViaAdministracion 		INT NOT NULL,
    fechaInicio             	DATE NOT NULL,
    fechaFin                	DATE NOT NULL,
    observaciones           	TEXT NULL,
    reaccionesAdversas      	TEXT NULL,
    idUsuario               	INT NOT NULL,
    tipoTratamiento         	ENUM('Primario', 'Complementario') DEFAULT 'Primario',
    estadoTratamiento       	ENUM('Activo', 'Finalizado', 'En pausa') DEFAULT 'Activo',
    CONSTRAINT fk_detallemed_medicamento FOREIGN KEY (idMedicamento) REFERENCES Medicamentos(idMedicamento),
    CONSTRAINT fk_detallemed_equino FOREIGN KEY (idEquino) REFERENCES Equinos(idEquino),
    CONSTRAINT fk_detallemed_usuario FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario),
    CONSTRAINT fk_detallemed_via FOREIGN KEY (idViaAdministracion) REFERENCES ViasAdministracion(idViaAdministracion)
) ENGINE = INNODB;

-- 29. HistorialMedicamentosMedi ---- admedi..
CREATE TABLE HistorialMovimientosMedicamentos (
    idMovimiento 		INT PRIMARY KEY AUTO_INCREMENT,
    idMedicamento 		INT NOT NULL,
    tipoMovimiento 		VARCHAR(50) NOT NULL,
    cantidad 			INT NOT NULL,
    motivo 				TEXT NOT NULL,
    idEquino 			INT NULL,
    idUsuario 			INT NOT NULL,
    fechaMovimiento 	DATE DEFAULT NOW(), -- Fecha del movimiento
    FOREIGN KEY (idMedicamento) REFERENCES Medicamentos(idMedicamento),
    CONSTRAINT fk_historialmedicamentos_equino FOREIGN KEY (idEquino) REFERENCES Equinos(idEquino),
    FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario)
) ENGINE = INNODB;

-- 30. Historial dosis
CREATE TABLE HistorialDosisAplicadas (
    idDosis 			INT PRIMARY KEY AUTO_INCREMENT,
    idMedicamento 		INT NOT NULL,
    idEquino 			INT NOT NULL,
    cantidadAplicada 	DECIMAL(10, 2) NOT NULL,
    cantidadRestante 	DECIMAL(10, 2) NULL,
    fechaAplicacion 	DATE NOT NULL, -- Fecha obligatoria, sin valor predeterminado
    idUsuario 			INT NOT NULL,
    CONSTRAINT fk_idMedicamento FOREIGN KEY (idMedicamento) REFERENCES Medicamentos(idMedicamento),
    CONSTRAINT fk_idEquino_dosis FOREIGN KEY (idEquino) REFERENCES Equinos(idEquino),
    CONSTRAINT fk_idUsuario_dosis FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario)
)ENGINE = INNODB;

-- 31. servicios
CREATE TABLE Servicios (
    idServicio               INT PRIMARY KEY AUTO_INCREMENT,
    idEquinoMacho            INT NULL,
    idEquinoHembra           INT NULL,
    idEquinoExterno          INT NULL,
    fechaServicio            DATE NOT NULL,
    tipoServicio             ENUM('Propio', 'Mixto') NOT NULL,
    detalles                 TEXT NULL,
    idMedicamento            INT NULL,
    horaEntrada              TIME NULL,
    horaSalida               TIME NULL,
    idPropietario            INT NULL,
    idEstadoMonta            INT NULL,
    costoServicio            DECIMAL(10,2) NULL,
    CONSTRAINT fk_servicio_equino_macho FOREIGN KEY (idEquinoMacho) REFERENCES Equinos(idEquino),
    CONSTRAINT fk_servicio_equino_hembra FOREIGN KEY (idEquinoHembra) REFERENCES Equinos(idEquino),
    CONSTRAINT fk_servicio_equino_externo FOREIGN KEY (idEquinoExterno) REFERENCES Equinos(idEquino),
    CONSTRAINT fk_servicio_medicamento FOREIGN KEY (idMedicamento) REFERENCES Medicamentos(idMedicamento),
    CONSTRAINT fk_servicio_propietario FOREIGN KEY (idPropietario) REFERENCES Propietarios(idPropietario)
) ENGINE = INNODB;

-- 32. Entrenamientos
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


-- 33. tipos de trabajo (herreo)
CREATE TABLE TiposTrabajos (
    idTipoTrabajo 		INT PRIMARY KEY AUTO_INCREMENT,
    nombreTrabajo 		VARCHAR(255) NOT NULL UNIQUE
);

-- 34. herramientas (herreo)
CREATE TABLE Herramientas (
    idHerramienta 			INT PRIMARY KEY AUTO_INCREMENT,
    nombreHerramienta 		VARCHAR(255) NOT NULL UNIQUE
);

-- 35. Tabla para el Historial del Trabajo de Herrería (herreo)
CREATE TABLE HistorialHerrero (
    idHistorialHerrero 		INT PRIMARY KEY AUTO_INCREMENT,
    idEquino 				INT NOT NULL,
    idUsuario 				INT NOT NULL,
    fecha 					DATE NOT NULL,
    idTrabajo 				INT NOT NULL, 
    observaciones 			TEXT,
    CONSTRAINT fk_historialherrero_equino FOREIGN KEY (idEquino) REFERENCES Equinos(idEquino),
    CONSTRAINT fk_historialherrero_usuario FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario),
    CONSTRAINT fk_historialherrero_trabajo FOREIGN KEY (idTrabajo) REFERENCES TiposTrabajos(idTipoTrabajo)
);

-- 36. Tabla para Herramientas Usadas en Cada Trabajo de Herrería (sin estados) - (herreo)
CREATE TABLE HerramientasUsadasHistorial (
    idHerramientasUsadas 		INT PRIMARY KEY AUTO_INCREMENT,
    idHistorialHerrero 			INT NOT NULL,
    idHerramienta 				INT NOT NULL,
    CONSTRAINT fk_herramienta_historial FOREIGN KEY (idHistorialHerrero) REFERENCES HistorialHerrero(idHistorialHerrero),
    CONSTRAINT fk_herramienta FOREIGN KEY (idHerramienta) REFERENCES Herramientas(idHerramienta)
);

-- 37. Tipo Suelo
CREATE TABLE tipoSuelo (
	idTipoSuelo				INT PRIMARY KEY AUTO_INCREMENT,
    nombreTipoSuelo			VARCHAR(50) UNIQUE NOT NULL
) ENGINE = INNODB;

-- 38. Campos
CREATE TABLE Campos (
    idCampo 				INT PRIMARY KEY AUTO_INCREMENT,
    numeroCampo 			INT NOT NULL,
    tamanoCampo				DECIMAL(10,2) NOT NULL,
    idTipoSuelo 			INT NOT NULL,
    estado 					VARCHAR(50) NOT NULL
) ENGINE = INNODB;

-- 39. TipoRotaciones
CREATE TABLE TipoRotaciones (
    idTipoRotacion 			INT PRIMARY KEY AUTO_INCREMENT,
    nombreRotacion 			VARCHAR(100) NOT NULL,
    detalles 				TEXT
) ENGINE = INNODB;

-- 40. RotacionCampos
CREATE TABLE RotacionCampos (
    idRotacion 				INT PRIMARY KEY AUTO_INCREMENT,
    idCampo 				INT NOT NULL,
    idTipoRotacion 			INT NOT NULL,
    fechaRotacion 			DATE NULL,
    estadoRotacion 			VARCHAR(50) NOT NULL,
    detalleRotacion 		TEXT,
    CONSTRAINT fk_rotacioncampo_campo FOREIGN KEY (idCampo) REFERENCES Campos(idCampo),
    CONSTRAINT fk_rotacioncampo_tiporotacion FOREIGN KEY (idTipoRotacion) REFERENCES TipoRotaciones(idTipoRotacion)
) ENGINE = INNODB;

-- 39. CampanaPotrillos
CREATE TABLE HistorialEquinos (
    idHistorial				INT PRIMARY KEY AUTO_INCREMENT,
    idEquino 				INT NOT NULL,
    descripcion				TEXT NOT NULL,
    CONSTRAINT fk_idEquino_historial FOREIGN KEY (idEquino) REFERENCES Equinos(idEquino)
) ENGINE = INNODB;

-- 40. AsistenciaPersonal
CREATE TABLE AsistenciaPersonal (
    idAsistencia 			INT PRIMARY KEY AUTO_INCREMENT,
    idPersonal 				INT NOT NULL,
    fecha					DATE NOT NULL,
    horaEntrada 			TIME NOT NULL,
    horaSalida 				TIME NOT NULL,
    observaciones 			TEXT,
    CONSTRAINT fk_asistencia_personal FOREIGN KEY (idPersonal) REFERENCES Personal(idPersonal)
) ENGINE = INNODB;

-- 41. modulos 28/10/2024 ACCESO AL SISTEMA - LOGIN
CREATE TABLE modulos
(
	idmodulo		INT AUTO_INCREMENT PRIMARY KEY,
    modulo			VARCHAR(30)			NOT NULL,
    create_at		DATETIME			NOT NULL DEFAULT NOW(),
    CONSTRAINT uk_modulo_mod UNIQUE (modulo)
) ENGINE = INNODB;

-- 42. vistas
CREATE TABLE vistas
(
	idvista			INT AUTO_INCREMENT PRIMARY KEY,
    idmodulo		INT					NULL,
    ruta			VARCHAR(50)			NOT NULL,
    sidebaroption	CHAR(1)				NOT NULL,
    texto			VARCHAR(40)			NULL,
    icono			VARCHAR(35)			NULL,
    CONSTRAINT fk_idmodulo_vis FOREIGN KEY (idmodulo) REFERENCES modulos (idmodulo),
    CONSTRAINT uk_ruta_vis	UNIQUE(ruta),
    CONSTRAINT ck_sidebaroption_vis CHECK (sidebaroption IN ('S', 'N'))
) ENGINE = INNODB;

-- 43. permisos
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

-- 44. bostas
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

-- 45. revision equinos, PARA UNA REVISION INTERDIARIA
CREATE TABLE revisionequinos (
	idRevision 			INT PRIMARY KEY AUTO_INCREMENT,
	idEquino 			INT NOT NULL,					-- FILTRAR SOLO YEGUAS PROPIAS O AJENAS QUE CUENTEN CON ALMENOS 1 SERVICIO
    idPropietario		INT NULL,
	tiporevision 		ENUM('Ecografía', 'Examen ginecológico', 'Citología', 'Cultivo bacteriológico', 'Biopsia endometrial'),
	fecharevision 		DATE NOT NULL,
	observaciones 		TEXT NOT NULL,
	costorevision 		DECIMAL (10,2) NULL,
    create_at			DATETIME DEFAULT NOW() NOT NULL,
	CONSTRAINT fk_idEquino_revision FOREIGN KEY (idEquino) REFERENCES Equinos(idEquino),
    CONSTRAINT fk_idPropietario_revision FOREIGN KEY (idPropietario) REFERENCES Propietarios(idPropietario)
)ENGINE = INNODB;