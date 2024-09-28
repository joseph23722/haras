-- procedimientos faltantes---------------------------------------------------------------------------------------------------------------------------------
-- Procedimiento para registrar un nuevo entrenamiento realizado a un equino------------------------------------------
DELIMITER $$
CREATE PROCEDURE spu_entrenamientos_registrar(
    IN _idEquino INT,
    IN _fecha DATETIME,
    IN _tipoEntrenamiento VARCHAR(100),
    IN _duracion DECIMAL(5,2),
    IN _intensidad ENUM('baja', 'media', 'alta'),
    IN _comentarios TEXT
)
BEGIN
    INSERT INTO Entrenamientos (
        idEquino, 
        fecha, 
        tipoEntrenamiento, 
        duracion, 
        intensidad, 
        comentarios
    ) 
    VALUES (
        _idEquino, 
        _fecha, 
        _tipoEntrenamiento, 
        _duracion, 
        _intensidad, 
        _comentarios
    );
END $$
DELIMITER ;

-- Procedimiento para registrar la asistencia del personal------------------------------------------------------------------------------------------------------------------------------------------------
DELIMITER $$
CREATE PROCEDURE spu_asistencia_personal_registrar(
    IN _idPersonal INT,
    IN _fecha DATETIME,
    IN _horaEntrada TIME,
    IN _horaSalida TIME,
    IN _horasTrabajadas DECIMAL(5,2),
    IN _tipoJornada ENUM('completa', 'parcial'),
    IN _comentarios TEXT
)
BEGIN
    INSERT INTO AsistenciaPersonal (
        idPersonal, 
        fecha, 
        horaEntrada, 
        horaSalida, 
        horasTrabajadas, 
        tipoJornada, 
        comentarios
    ) 
    VALUES (
        _idPersonal, 
        _fecha, 
        _horaEntrada, 
        _horaSalida, 
        _horasTrabajadas, 
        _tipoJornada, 
        _comentarios
    );
END $$
DELIMITER ;


-- Procedimiento para registrar la rotación de campos---------------------------------------------------------------------------------------------------------------------------
DELIMITER $$
CREATE PROCEDURE spu_rotacion_campos_registrar(
    IN _idCampo INT,
    IN _idTipoRotacion INT,
    IN _fechaRotacion DATETIME,
    IN _estadoRotacion VARCHAR(50),
    IN _detalleRotacion TEXT
)
BEGIN
    INSERT INTO RotacionCampos (
        idCampo, 
        idTipoRotacion, 
        fechaRotacion, 
        estadoRotacion, 
        detalleRotacion
    ) 
    VALUES (
        _idCampo, 
        _idTipoRotacion, 
        _fechaRotacion, 
        _estadoRotacion, 
        _detalleRotacion
    );
END $$
DELIMITER ;