-- Registrar Equino
DROP PROCEDURE IF EXISTS `spu_equino_registrar`;
DELIMITER $$
CREATE PROCEDURE `spu_equino_registrar`(
    IN _nombreEquino VARCHAR(100),
    IN _fechaNacimiento DATE,
    IN _sexo ENUM('Macho', 'Hembra'),
    IN _detalles TEXT,
    IN _idTipoEquino INT,
    IN _idPropietario INT,
    IN _pesokg DECIMAL(5,1),
    IN _idNacionalidad INT
)
BEGIN
    DECLARE _errorMsg VARCHAR(255);
    DECLARE _edadMeses INT;
    DECLARE _edadAnios INT;
    DECLARE _idEquino INT;
    DECLARE _idEstadoMonta INT;

    SET _edadMeses = TIMESTAMPDIFF(MONTH, _fechaNacimiento, CURDATE());
    SET _edadAnios = TIMESTAMPDIFF(YEAR, _fechaNacimiento, CURDATE());

    -- Validaciones de fecha y propietario
    IF _fechaNacimiento > CURDATE() THEN
        SET _errorMsg = 'Error: La fecha de nacimiento no puede ser posterior a la fecha actual.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
    END IF;

    IF _idPropietario IS NOT NULL AND NOT EXISTS (SELECT * FROM Propietarios WHERE idPropietario = _idPropietario) THEN
        SET _errorMsg = 'Error: El propietario no existe.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
    END IF;

    IF EXISTS (SELECT * FROM Equinos WHERE nombreEquino = _nombreEquino) THEN
        SET _errorMsg = 'Error: Ya existe un equino con ese nombre.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
    END IF;

    -- Validaciones de edad y tipo de equino
    IF _idPropietario IS NULL THEN
        -- Recién nacido (<= 6 meses)
        IF _edadMeses <= 6 THEN
            IF _idTipoEquino NOT IN (5) THEN
                SET _errorMsg = 'Error: Verifica la fecha de nacimiento, sexo y tipo de equino.';
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
            END IF;
        -- Destete (<= 12 meses)
        ELSEIF _edadMeses > 6 AND _edadMeses <= 12 THEN
            IF _idTipoEquino NOT IN (6) THEN
                SET _errorMsg = 'Error: Un equino destete debe ser registrado como macho o hembra.';
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
            END IF;
        -- Potrillo o potranca (<= 24 meses)
        ELSEIF _edadMeses <= 24 THEN
            IF _sexo = 'Macho' AND _idTipoEquino != 4 THEN
                SET _errorMsg = 'Error: Un macho de esta edad debe ser registrado como potrillo.';
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
            END IF;
            IF _sexo = 'Hembra' AND _idTipoEquino != 3 THEN
                SET _errorMsg = 'Error: Una hembra de esta edad debe ser registrada como potranca.';
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
            END IF;
        -- Equinos mayores de 4 años
        ELSEIF _edadAnios > 4 THEN
            IF _sexo = 'Macho' AND _idTipoEquino NOT IN (2, 4) THEN
                SET _errorMsg = 'Error: Un macho mayor de 4 años debe ser registrado como padrillo o potrillo.';
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
            END IF;
            IF _sexo = 'Hembra' AND _idTipoEquino NOT IN (1, 3) THEN
                SET _errorMsg = 'Error: Una hembra mayor de 4 años debe ser registrada como yegua o potranca.';
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
            END IF;
        END IF;
    END IF;

    IF NOT EXISTS (SELECT 1 FROM nacionalidades WHERE idNacionalidad = _idNacionalidad) THEN
        SET _errorMsg = 'Error: La nacionalidad seleccionada no existe.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
    END IF;

    SET _idEstadoMonta = CASE 
        WHEN _sexo = 'Macho' AND _idTipoEquino = 2 THEN (SELECT idEstadoMonta FROM EstadoMonta WHERE genero = 'Macho' AND nombreEstado = 'Inactivo' LIMIT 1)
        WHEN _sexo = 'Hembra' AND _idTipoEquino = 1 THEN (SELECT idEstadoMonta FROM EstadoMonta WHERE genero = 'Hembra' AND nombreEstado = 'S/S' LIMIT 1)
        ELSE NULL
    END;

    INSERT INTO Equinos (
        nombreEquino, 
        fechaNacimiento, 
        sexo, 
        idTipoEquino, 
        detalles, 
        idPropietario,
        pesokg,
        idNacionalidad,
        idEstadoMonta
    ) 
    VALUES (
        _nombreEquino, 
        _fechaNacimiento, 
        _sexo, 
        _idTipoEquino, 
        _detalles, 
        _idPropietario,
        _pesokg,
        _idNacionalidad,
        _idEstadoMonta
    );
    -- Obtener el ID del equino recién insertado
    SET _idEquino = LAST_INSERT_ID();
    -- Retornar el ID del equino registrado
    SELECT _idEquino AS idEquino;
END $$
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_buscar_nacionalidad`;
DELIMITER $$
CREATE PROCEDURE `spu_buscar_nacionalidad`(IN _nacionalidad VARCHAR(255))
BEGIN
    SELECT idNacionalidad, nacionalidad
    FROM nacionalidades
    WHERE nacionalidad LIKE CONCAT('%', _nacionalidad, '%');
END $$
DELIMITER ;

-- Registrar Servicio
DROP PROCEDURE IF EXISTS `registrarServicio`;
DELIMITER $$
CREATE PROCEDURE registrarServicio(
    IN p_idEquinoMacho INT,
    IN p_idEquinoHembra INT,
    IN p_idPropietario INT,
    IN p_idEquinoExterno INT,
    IN p_fechaServicio DATE,
    IN p_tipoServicio ENUM('propio', 'mixto'),
    IN p_detalles TEXT,
    IN p_idMedicamento INT,
    IN p_horaEntrada TIME,
    IN p_horaSalida TIME,
    IN p_costoServicio DECIMAL(10, 2)
)
BEGIN
    DECLARE v_mensajeError VARCHAR(255);
    DECLARE v_idEstadoServida INT;
    DECLARE v_idEstadoActivo INT;
    DECLARE v_idEstadoSS INT;

    -- Obtener los ID de estados correspondientes
    SELECT idEstadoMonta INTO v_idEstadoServida FROM EstadoMonta WHERE genero = 'Hembra' AND nombreEstado = 'Servida' LIMIT 1;
    SELECT idEstadoMonta INTO v_idEstadoActivo FROM EstadoMonta WHERE genero = 'Macho' AND nombreEstado = 'Activo' LIMIT 1;
    SELECT idEstadoMonta INTO v_idEstadoSS FROM EstadoMonta WHERE genero = 'Hembra' AND nombreEstado = 'S/S' LIMIT 1;

    -- Validación para la fecha de servicio
    IF p_fechaServicio > CURDATE() THEN
        SET v_mensajeError = 'Error: La fecha de servicio no puede ser mayor que la fecha actual.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
    END IF;

    -- Registrar el servicio y actualizar estados según el tipo de servicio
    IF p_tipoServicio = 'propio' THEN
        -- Servicio propio entre equinos del haras
        INSERT INTO Servicios (
            idEquinoMacho, idEquinoHembra, fechaServicio, tipoServicio, detalles, idMedicamento, horaEntrada, horaSalida, idPropietario, costoServicio
        ) VALUES (
            p_idEquinoMacho, p_idEquinoHembra, p_fechaServicio, p_tipoServicio, p_detalles, p_idMedicamento, p_horaEntrada, p_horaSalida, NULL, p_costoServicio
        );
        
        -- Cambiar estado del padrillo a "Activo"
        UPDATE Equinos
        SET idEstadoMonta = v_idEstadoActivo
        WHERE idEquino = p_idEquinoMacho;

    ELSEIF p_tipoServicio = 'mixto' THEN
        -- Servicio mixto (con propietario externo)
        INSERT INTO Servicios (
            idEquinoMacho, idEquinoHembra, fechaServicio, tipoServicio, detalles, idMedicamento, horaEntrada, horaSalida, idPropietario, costoServicio
        ) VALUES (
            NULL, p_idEquinoHembra, p_fechaServicio, p_tipoServicio, p_detalles, p_idMedicamento, p_horaEntrada, p_horaSalida, p_idPropietario, p_costoServicio
        );
    END IF;

    -- Cambiar estado de la yegua a "Servida" después del servicio
    UPDATE Equinos
    SET idEstadoMonta = v_idEstadoServida
    WHERE idEquino = p_idEquinoHembra;

    -- Actualizar el estado de monta de las yeguas no servidas recientemente a "S/S"
    UPDATE Equinos
    SET idEstadoMonta = v_idEstadoSS
    WHERE sexo = 'Hembra'
      AND idEquino NOT IN (
          SELECT idEquinoHembra
          FROM Servicios
          WHERE fechaServicio >= DATE_SUB(CURDATE(), INTERVAL 2 DAY)
      );

END $$
DELIMITER ;


-- --------- listar equinos en estado monta 
DELIMITER $$

CREATE PROCEDURE spu_contar_equinos_por_categoria()
BEGIN
    SELECT 
        CASE 
            WHEN te.tipoEquino = 'Yegua' AND em.nombreEstado = 'S/S' THEN 'Yegua Vacía'
            WHEN te.tipoEquino = 'Yegua' AND em.nombreEstado = 'Preñada' THEN 'Yegua Preñada'
            WHEN te.tipoEquino = 'Yegua' AND em.nombreEstado = 'Con Cria' THEN 'Yegua Con Cria'  -- Nueva condición para Yegua con Cria
            WHEN te.tipoEquino = 'Padrillo' AND em.nombreEstado = 'Activo' THEN 'Padrillo Activo'
            WHEN te.tipoEquino = 'Padrillo' AND em.nombreEstado = 'Inactivo' THEN 'Padrillo Inactivo'
            WHEN te.tipoEquino = 'Potranca' THEN 'Potranca'
            WHEN te.tipoEquino = 'Potrillo' THEN 'Potrillo'
        END AS Categoria,
        COUNT(e.idEquino) AS Cantidad
    FROM 
        Equinos e
    JOIN 
        TipoEquinos te ON e.idTipoEquino = te.idTipoEquino
    LEFT JOIN 
        EstadoMonta em ON e.idEstadoMonta = em.idEstadoMonta
    WHERE 
        (te.tipoEquino = 'Yegua' AND em.nombreEstado IN ('S/S', 'Preñada', 'Con Cria'))  -- Incluimos 'Con Cria' en el filtro
        OR (te.tipoEquino = 'Padrillo' AND em.nombreEstado IN ('Activo', 'Inactivo'))
        OR te.tipoEquino IN ('Potranca', 'Potrillo')
    GROUP BY 
        Categoria
    ORDER BY 
        Categoria;
END $$

DELIMITER ;