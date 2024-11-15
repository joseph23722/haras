-- Procedimiento para registrar un nuevo historial médico de un equino-------------------------------------------------------------------------------------------------
DELIMITER $$
CREATE PROCEDURE spu_historial_medico_registrarMedi(
    IN _idEquino INT,
    IN _idUsuario INT,
    IN _idMedicamento INT,
    IN _dosis VARCHAR(50),
    IN _frecuenciaAdministracion VARCHAR(50),
    IN _viaAdministracion VARCHAR(50),
    IN _fechaFin DATE,
    IN _observaciones TEXT,
    IN _reaccionesAdversas TEXT, -- Permitir NULL
    IN _tipoTratamiento VARCHAR(20) -- Cambiado a VARCHAR para evitar el error
)
BEGIN
    DECLARE _errorMensaje VARCHAR(255);
    DECLARE _dosisCantidad DECIMAL(10, 2);
    DECLARE _unidadMedida VARCHAR(50);

    -- Manejador de errores para revertir la transacción si hay algún error
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMensaje;
    END;

    -- Iniciar la transacción
    START TRANSACTION;

    -- Verificar que el equino existe
    IF NOT EXISTS (SELECT 1 FROM Equinos WHERE idEquino = _idEquino) THEN
        SET _errorMensaje = 'El equino especificado no existe en la base de datos.';
        SIGNAL SQLSTATE '45000';
    END IF;
    
    -- Verificar que el usuario existe
    IF NOT EXISTS (SELECT 1 FROM Usuarios WHERE idUsuario = _idUsuario) THEN
        SET _errorMensaje = 'El usuario especificado no existe en la base de datos.';
        SIGNAL SQLSTATE '45000';
    END IF;
    
    -- Verificar que el medicamento existe
    IF NOT EXISTS (SELECT 1 FROM Medicamentos WHERE idMedicamento = _idMedicamento) THEN
        SET _errorMensaje = 'El medicamento especificado no existe en la base de datos.';
        SIGNAL SQLSTATE '45000';
    END IF;

    -- Validar que la fecha de fin sea una fecha futura
    IF _fechaFin < CURDATE() THEN
        SET _errorMensaje = 'La fecha de fin no puede ser anterior a la fecha actual.';
        SIGNAL SQLSTATE '45000';
    END IF;

    -- Validar el tipo de tratamiento (debe ser 'Primario' o 'Complementario')
    IF _tipoTratamiento NOT IN ('Primario', 'Complementario') THEN
        SET _errorMensaje = 'El tipo de tratamiento debe ser "Primario" o "Complementario".';
        SIGNAL SQLSTATE '45000';
    END IF;

    -- Verificar que el equino no tenga un tratamiento primario activo si se va a registrar un nuevo tratamiento primario
    IF _tipoTratamiento = 'Primario' AND EXISTS (
        SELECT 1 FROM DetalleMedicamentos 
        WHERE idEquino = _idEquino 
        AND tipoTratamiento = 'Primario' 
        AND estadoTratamiento = 'Activo'
    ) THEN
        SET _errorMensaje = 'El equino ya tiene un tratamiento primario activo. No se permite registrar otro tratamiento primario hasta que el tratamiento actual esté finalizado o en pausa.';
        SIGNAL SQLSTATE '45000';
    END IF;

    -- Separar la dosis y la unidad de medida, similar al registro de medicamentos
    SET _dosisCantidad = CAST(SUBSTRING_INDEX(_dosis, ' ', 1) AS DECIMAL(10,2));
    SET _unidadMedida = TRIM(SUBSTRING_INDEX(_dosis, ' ', -1));

    -- Verificar que la unidad de medida esté registrada en la tabla UnidadesMedida
    IF NOT EXISTS (SELECT 1 FROM UnidadesMedida WHERE unidad = _unidadMedida) THEN
        SET _errorMensaje = CONCAT('La unidad de medida "', _unidadMedida, '" no está registrada. Verifica que sea correcta.');
        SIGNAL SQLSTATE '45000';
    END IF;

    -- Insertar el detalle del medicamento administrado al equino con la fecha de inicio como la fecha actual
    INSERT INTO DetalleMedicamentos (
        idMedicamento,
        idEquino,
        dosis,
        frecuenciaAdministracion,
        viaAdministracion,
        fechaInicio,
        fechaFin,
        observaciones,
        reaccionesAdversas,
        idUsuario,
        tipoTratamiento,         -- Insertar el tipo de tratamiento
        estadoTratamiento        -- Insertar el estado del tratamiento
    ) VALUES (
        _idMedicamento,
        _idEquino,
        _dosis,
        _frecuenciaAdministracion,
        _viaAdministracion,
        NOW(),                   -- Fecha de inicio se asigna a la fecha y hora actual
        _fechaFin,
        _observaciones,
        IFNULL(_reaccionesAdversas, NULL),
        _idUsuario,
        _tipoTratamiento,        -- Asignar el tipo de tratamiento (Primario o Complementario)
        'Activo'                 -- El tratamiento comienza con el estado 'Activo'
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
        pesokg,
		idTipoEquino
	FROM 
		Equinos
	WHERE 
		idPropietario IS NULL
		AND idTipoEquino IN (1, 2, 3, 4)
		AND estado = 1;
END $$
DELIMITER ;

-- 
DELIMITER $$
CREATE PROCEDURE spu_consultar_historial_medicoMedi()
BEGIN
    -- Actualizar el estado de los tratamientos en la tabla DetalleMedicamentos
    -- Cambiar a 'Finalizado' los tratamientos cuya fecha de fin ha pasado y que están en estado 'Activo'
    UPDATE DetalleMedicamentos
    SET estadoTratamiento = 'Finalizado'
    WHERE fechaFin < CURDATE() AND estadoTratamiento = 'Activo';

    -- Seleccionar la información detallada de todos los registros de historial médico, incluyendo el estado del tratamiento
    SELECT 
        DM.idDetalleMed AS idRegistro,
        DM.idEquino,
        E.nombreEquino,
        DM.idMedicamento,
        M.nombreMedicamento,
        DM.dosis,
        DM.frecuenciaAdministracion,
        DM.viaAdministracion,
        E.pesokg,
        DM.fechaInicio,
        DM.fechaFin,
        DM.observaciones,
        DM.reaccionesAdversas,
        DM.idUsuario AS responsable,
        DM.tipoTratamiento,
        DM.estadoTratamiento,       -- Incluir el estado del tratamiento
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

-- ------------

DELIMITER $$
CREATE PROCEDURE spu_gestionar_tratamiento(
    IN _idDetalleMed INT,         -- ID del tratamiento a gestionar
    IN _accion VARCHAR(10)        -- Acción a realizar: 'pausar', 'eliminar' o 'continuar'
)
BEGIN
    -- Verificar la acción solicitada
    IF _accion = 'pausar' THEN
        -- Cambiar el estado del tratamiento a 'En pausa' solo si actualmente está 'Activo'
        UPDATE DetalleMedicamentos
        SET estadoTratamiento = 'En pausa'
        WHERE idDetalleMed = _idDetalleMed
          AND estadoTratamiento = 'Activo';

        -- Verificar si el estado fue actualizado a 'En pausa'
        IF ROW_COUNT() = 0 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'El tratamiento no está activo o no se encontró.';
        END IF;

    ELSEIF _accion = 'eliminar' THEN
        -- Eliminar el tratamiento solo si está en estado 'Finalizado' o 'En pausa'
        DELETE FROM DetalleMedicamentos
        WHERE idDetalleMed = _idDetalleMed
          AND estadoTratamiento IN ('Finalizado', 'En pausa');

        -- Verificar si el tratamiento fue eliminado
        IF ROW_COUNT() = 0 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'El tratamiento no está en estado Finalizado o En pausa, o no se encontró.';
        END IF;

    ELSEIF _accion = 'continuar' THEN
        -- Cambiar el estado del tratamiento a 'Activo' solo si actualmente está 'En pausa'
        UPDATE DetalleMedicamentos
        SET estadoTratamiento = 'Activo'
        WHERE idDetalleMed = _idDetalleMed
          AND estadoTratamiento = 'En pausa';

        -- Verificar si el estado fue actualizado a 'Activo'
        IF ROW_COUNT() = 0 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'El tratamiento no está en pausa o no se encontró.';
        END IF;

    ELSE
        -- Si la acción no es válida, lanzar un error
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Acción no válida. Use "pausar", "eliminar" o "continuar".';
    END IF;
END $$
DELIMITER ;