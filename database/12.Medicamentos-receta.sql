select * from DetalleMedicamentos;
-- Procedimiento para registrar un nuevo historial médico de un equino-------------------------------------------------------------------------------------------------
DELIMITER $$

CREATE PROCEDURE spu_historial_medico_registrarMedi(
    IN _idEquino INT,
    IN _idUsuario INT,
    IN _idMedicamento INT,
    IN _dosis VARCHAR(50),
    IN _frecuenciaAdministracion VARCHAR(50),
    IN _viaAdministracion VARCHAR(50),
    IN _pesoEquino DECIMAL(10,2), -- Permitir NULL
    IN _fechaFin DATE,
    IN _observaciones TEXT,
    IN _reaccionesAdversas TEXT -- Permitir NULL
)
BEGIN
    DECLARE _unidadDosis VARCHAR(50);
    DECLARE _errorMensaje VARCHAR(255);

    -- Manejador de errores para revertir la transacción si hay algún error
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error en la transacción de historial médico. Registro cancelado.';
    END;

    -- Iniciar la transacción
    START TRANSACTION;

    -- Verificar que el equino existe
    IF NOT EXISTS (SELECT 1 FROM Equinos WHERE idEquino = _idEquino) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El equino no existe.';
    END IF;
    
    -- Verificar que el usuario existe
    IF NOT EXISTS (SELECT 1 FROM Usuarios WHERE idUsuario = _idUsuario) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El usuario no existe.';
    END IF;
    
    -- Verificar que el medicamento existe
    IF NOT EXISTS (SELECT 1 FROM Medicamentos WHERE idMedicamento = _idMedicamento) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El medicamento no existe.';
    END IF;

    -- Validar que la fecha de fin sea una fecha futura
    IF _fechaFin < CURDATE() THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La fecha de fin no puede ser anterior a hoy.';
    END IF;

    -- Insertar el detalle del medicamento administrado al equino con la fecha de inicio como la fecha actual
    INSERT INTO DetalleMedicamentos (
        idMedicamento,
        idEquino,
        dosis,
        frecuenciaAdministracion,
        viaAdministracion,
        pesoEquino,
        fechaInicio,
        fechaFin,
        observaciones,
        reaccionesAdversas,
        idUsuario
    ) VALUES (
        _idMedicamento,
        _idEquino,
        _dosis,
        _frecuenciaAdministracion,
        _viaAdministracion,
        IFNULL(_pesoEquino, NULL),
        NOW(),  -- Fecha de inicio se asigna a la fecha y hora actual
        _fechaFin,
        _observaciones,
        IFNULL(_reaccionesAdversas, NULL),
        _idUsuario
    );

    -- Confirmar la transacción
    COMMIT;

END $$

DELIMITER ;



-- listar equinos propios para medicamentos;
DELIMITER $$
CREATE PROCEDURE spu_listar_equinos_propiosMedi()
BEGIN
	SELECT 
		idEquino,
		nombreEquino,
		sexo,
		idTipoEquino
	FROM 
		Equinos
	WHERE 
		idPropietario IS NULL  -- Filtrar solo los equinos que no tienen propietario
		AND idTipoEquino IN (1, 2, 3, 4);
END $$
DELIMITER ;



-- 
DELIMITER $$

CREATE PROCEDURE spu_consultar_historial_medicoMedi()
BEGIN
    SELECT 
        DM.idDetalleMed AS idRegistro,
        DM.idEquino,
        E.nombreEquino,
        DM.idMedicamento,
        M.nombreMedicamento,
        DM.dosis,
        DM.frecuenciaAdministracion,
        DM.viaAdministracion,
        DM.pesoEquino,
        DM.fechaInicio,
        DM.fechaFin,
        DM.observaciones,
        DM.reaccionesAdversas,
        DM.idUsuario AS responsable,
        DM.fechaInicio AS fechaRegistro
    FROM 
        DetalleMedicamentos DM
    INNER JOIN 
        Medicamentos M ON DM.idMedicamento = M.idMedicamento
    INNER JOIN 
        Equinos E ON DM.idEquino = E.idEquino
    INNER JOIN 
        Usuarios U ON DM.idUsuario = U.idUsuario
    ORDER BY 
        DM.fechaInicio DESC;
END $$

DELIMITER ;





