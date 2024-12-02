DROP PROCEDURE IF EXISTS `spu_historial_medico_registrarMedi`;
DELIMITER $$
CREATE PROCEDURE spu_historial_medico_registrarMedi(
    IN _idEquino INT,
    IN _idUsuario INT,
    IN _idMedicamento INT,
    IN _dosis VARCHAR(50),
    IN _frecuenciaAdministracion VARCHAR(50),
    IN _idViaAdministracion INT, -- Ahora usamos el ID de la vía
    IN _fechaInicio DATE, -- Nuevo parámetro para la fecha de inicio
    IN _fechaFin DATE,
    IN _observaciones TEXT,
    IN _reaccionesAdversas TEXT, -- Permitir NULL
    IN _tipoTratamiento VARCHAR(20) -- Cambiado a VARCHAR para evitar errores
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

    -- Verificar que la vía de administración existe
    IF NOT EXISTS (SELECT 1 FROM ViasAdministracion WHERE idViaAdministracion = _idViaAdministracion) THEN
        SET _errorMensaje = 'La vía de administración especificada no existe en la base de datos.';
        SIGNAL SQLSTATE '45000';
    END IF;

    -- Validar que la fecha de fin sea una fecha futura
    IF _fechaFin < CURDATE() THEN
        SET _errorMensaje = 'La fecha de fin no puede ser anterior a la fecha actual.';
        SIGNAL SQLSTATE '45000';
    END IF;

    -- Validar que la fecha de inicio no sea posterior a la fecha de fin
    IF _fechaInicio > _fechaFin THEN
        SET _errorMensaje = 'La fecha de inicio no puede ser posterior a la fecha de fin.';
        SIGNAL SQLSTATE '45000';
    END IF;

    -- Validar que la fecha de fin no sea anterior a la fecha de inicio
    IF _fechaFin < _fechaInicio THEN
        SET _errorMensaje = 'La fecha de fin no puede ser anterior a la fecha de inicio.';
        SIGNAL SQLSTATE '45000';
    END IF;

    -- Validar el tipo de tratamiento (debe ser 'Primario' o 'Complementario')
    IF _tipoTratamiento NOT IN ('Primario', 'Complementario') THEN
        SET _errorMensaje = 'El tipo de tratamiento debe ser "Primario" o "Complementario".';
        SIGNAL SQLSTATE '45000';
    END IF;

    -- Verificar si el equino tiene algún registro de tratamiento
    IF NOT EXISTS (SELECT 1 FROM DetalleMedicamentos WHERE idEquino = _idEquino) THEN
        -- Si no tiene registros, el primer tratamiento debe ser 'Primario'
        IF _tipoTratamiento != 'Primario' THEN
            SET _errorMensaje = 'El primer registro del equino debe ser un tratamiento primario, no complementario.';
            SIGNAL SQLSTATE '45000';
        END IF;
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

    -- Insertar el detalle del medicamento administrado al equino con la fecha de inicio proporcionada
    INSERT INTO DetalleMedicamentos (
        idMedicamento,
        idEquino,
        dosis,
        frecuenciaAdministracion,
        idViaAdministracion, -- Usar el ID en lugar del texto plano
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
        _idViaAdministracion,    -- Insertar el ID de la vía
        _fechaInicio,            -- Usar la fecha de inicio proporcionada
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



DROP PROCEDURE IF EXISTS `spu_listar_equinos_propiosMedi`;
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

DROP PROCEDURE IF EXISTS `spu_consultar_historial_medicoMedi`;
DELIMITER $$
CREATE PROCEDURE spu_consultar_historial_medicoMedi()
BEGIN
    -- Actualizar el estado de los tratamientos en la tabla DetalleMedicamentos
    -- Cambiar a 'Finalizado' los tratamientos cuya fecha de fin ha pasado y que están en estado 'Activo'
    UPDATE DetalleMedicamentos
    SET estadoTratamiento = 'Finalizado'
    WHERE fechaFin < CURDATE() AND estadoTratamiento = 'Activo';

    -- Seleccionar la información detallada de todos los registros de historial médico, incluyendo el nombre de la vía de administración y el estado del tratamiento
    SELECT 
        DM.idDetalleMed AS idRegistro,
        DM.idEquino,
        E.nombreEquino,
        DM.idMedicamento,
        M.nombreMedicamento,
        DM.dosis,
        DM.frecuenciaAdministracion,
        VA.nombreVia AS viaAdministracion,  -- Obtener solo el nombre de la vía desde la tabla ViasAdministracion
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
    LEFT JOIN 
        ViasAdministracion VA ON DM.idViaAdministracion = VA.idViaAdministracion  -- Vincular con la tabla ViasAdministracion
    ORDER BY 
        DM.fechaInicio DESC;
END $$
DELIMITER ;


DROP PROCEDURE IF EXISTS `spu_gestionar_tratamiento`;
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
            SET MESSAGE_TEXT = 'El tratamiento ya está pausado o no se encontró.';
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
            SET MESSAGE_TEXT = 'El tratamiento ya está activo o no se encontró.';
        END IF;

    ELSE
        -- Si la acción no es válida, lanzar un error
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Acción no válida. Use "pausar", "eliminar" o "continuar".';
    END IF;
END $$
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_notificar_tratamientos_veterinarios`;
DELIMITER $$
CREATE PROCEDURE spu_notificar_tratamientos_veterinarios()
BEGIN
    -- Seleccionar tratamientos próximos a finalizar (dentro de los próximos 3 días)
    SELECT 
        CONCAT(
            'El tratamiento del equino "', E.nombreEquino, 
            '" con el medicamento "', M.nombreMedicamento, 
            '" finaliza pronto el ', DATE_FORMAT(DM.fechaFin, '%d-%m-%Y'), '.'
        ) AS Notificacion,
        DM.idDetalleMed AS idTratamiento,
        E.idEquino,
        E.nombreEquino AS nombreEquino, -- Alias explícito
        M.idMedicamento,
        M.nombreMedicamento AS nombreMedicamento, -- Alias explícito
        DM.fechaFin,
        'PRONTO' AS TipoNotificacion
    FROM 
        DetalleMedicamentos DM
    INNER JOIN 
        Medicamentos M ON DM.idMedicamento = M.idMedicamento
    INNER JOIN 
        Equinos E ON DM.idEquino = E.idEquino
    WHERE 
        DM.estadoTratamiento = 'Activo' 
        AND DM.fechaFin BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 DAY)

    UNION ALL

    -- Seleccionar tratamientos finalizados recientemente (últimos 7 días)
    SELECT 
        CONCAT(
            'El tratamiento del equino "', E.nombreEquino, 
            '" con el medicamento "', M.nombreMedicamento, 
            '" ha finalizado el ', DATE_FORMAT(DM.fechaFin, '%d-%m-%Y'), '.'
        ) AS Notificacion,
        DM.idDetalleMed AS idTratamiento,
        E.idEquino,
        E.nombreEquino AS nombreEquino, -- Alias explícito
        M.idMedicamento,
        M.nombreMedicamento AS nombreMedicamento, -- Alias explícito
        DM.fechaFin,
        'FINALIZADO' AS TipoNotificacion
    FROM 
        DetalleMedicamentos DM
    INNER JOIN 
        Medicamentos M ON DM.idMedicamento = M.idMedicamento
    INNER JOIN 
        Equinos E ON DM.idEquino = E.idEquino
    WHERE 
        DM.estadoTratamiento = 'Finalizado' 
        AND DM.fechaFin BETWEEN DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND CURDATE();
END $$
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_Listar_ViasAdministracion`;
DELIMITER $$
CREATE PROCEDURE spu_Listar_ViasAdministracion()
BEGIN
    SELECT idViaAdministracion, nombreVia, descripcion
    FROM ViasAdministracion;
END $$
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_Agregar_Via_Administracion`;
DELIMITER $$
CREATE PROCEDURE spu_Agregar_Via_Administracion(
    IN p_nombreVia VARCHAR(50),
    IN p_descripcion TEXT
)
BEGIN
    -- Verificar si ya existe una vía con el mismo nombre
    IF EXISTS (SELECT 1 FROM ViasAdministracion WHERE nombreVia = p_nombreVia) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Ya existe una vía de administración con este nombre.';
    ELSE
        -- Insertar la nueva vía en la tabla
        INSERT INTO ViasAdministracion (nombreVia, descripcion)
        VALUES (p_nombreVia, p_descripcion);
    END IF;
END $$
DELIMITER ;


INSERT INTO ViasAdministracion (nombreVia, descripcion)
VALUES 
('Oral', 'Por la boca.'),
('Intravenosa', 'En una vena.'),
('Intramuscular', 'En un músculo.'),
('Sublingual', 'Bajo la lengua.'),
('Tópica', 'Sobre la piel.'),
('Rectal', 'Por el recto.'),
('Inhalatoria', 'Por las vías respiratorias.');

