-- Registrar Equino
DELIMITER $$
CREATE PROCEDURE spu_equino_registrar(
    IN _nombreEquino VARCHAR(100),
    IN _fechaNacimiento DATE,
    IN _sexo ENUM('Macho', 'Hembra'),
    IN _detalles TEXT,
    IN _idTipoEquino INT,
    IN _idPropietario INT,
    IN _nacionalidad VARCHAR(50)
    -- IN _fotografia LONGBLOB
)
BEGIN
    DECLARE _errorMsg VARCHAR(255);
    DECLARE _tipoEquinoNombre VARCHAR(50);
    DECLARE _edadMeses INT;
    DECLARE _edadAnios INT;

    -- Calcular la edad en meses y años
    SET _edadMeses = TIMESTAMPDIFF(MONTH, _fechaNacimiento, CURDATE());
    SET _edadAnios = TIMESTAMPDIFF(YEAR, _fechaNacimiento, CURDATE());

    -- Validar si la fecha de nacimiento no es futura
    IF _fechaNacimiento > CURDATE() THEN
        SET _errorMsg = 'Error: La fecha de nacimiento no puede ser posterior a la fecha actual.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
    END IF;

    -- Validar si el propietario proporcionado existe (si se proporcionó uno)
    IF _idPropietario IS NOT NULL AND NOT EXISTS (SELECT * FROM Propietarios WHERE idPropietario = _idPropietario) THEN
        SET _errorMsg = 'Error: El propietario no existe.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
    END IF;

    -- Reglas de validación de tipo de equino según la edad y sexo
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

    -- Insertar un nuevo registro en la tabla 'Equinos'
    INSERT INTO Equinos (
        nombreEquino, 
        fechaNacimiento, 
        sexo, 
        idTipoEquino, 
        detalles, 
        idPropietario,
        nacionalidad
        -- fotografia
    ) 
    VALUES (
        _nombreEquino, 
        _fechaNacimiento, 
        _sexo, 
        _idTipoEquino, 
        _detalles, 
        _idPropietario, 
        _nacionalidad
        -- _fotografia
    );
    
    -- Devolver el ID del equino recién insertado
    SELECT LAST_INSERT_ID() AS idEquino;
END $$
DELIMITER ;

-- Registrar Servicio
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
    IN p_costoServicio INT
)
BEGIN
    DECLARE v_sexoMacho ENUM('Macho', 'Hembra');
    DECLARE v_sexoHembra ENUM('Macho', 'Hembra');
    DECLARE v_sexoExterno ENUM('Macho', 'Hembra');
    DECLARE v_mensajeError VARCHAR(255);
    DECLARE v_count INT;

    -- Validación para la fecha de servicio
    IF p_fechaServicio > CURDATE() THEN
        SET v_mensajeError = 'Error: La fecha de servicio no puede ser mayor que la fecha actual.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
    END IF;
    
        -- Validación para evitar servicios duplicados en la misma fecha y hora
    SELECT COUNT(*) INTO v_count
    FROM Servicios
    WHERE DATE(fechaServicio) = p_fechaServicio
      AND ((horaEntrada = p_horaEntrada AND idEquinoHembra = p_idEquinoHembra) OR
           (horaSalida = p_horaSalida AND idEquinoMacho = p_idEquinoMacho));

    IF v_count > 0 THEN
        SET v_mensajeError = 'Error: Ya existe un servicio registrado a la misma hora, por favor verifica nuevamente.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
    END IF;

    -- Validación para evitar conflictos de horario
    SELECT COUNT(*) INTO v_count
    FROM Servicios
    WHERE DATE(fechaServicio) = p_fechaServicio
      AND ((horaEntrada < p_horaSalida AND horaSalida > p_horaEntrada) AND 
           (idEquinoHembra = p_idEquinoHembra OR 
            idEquinoMacho = p_idEquinoMacho));

    IF v_count > 0 THEN
        SET v_mensajeError = 'Error: Ya existe un servicio registrado en el intervalo de tiempo especificado.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
    END IF;


    -- Validación para evitar que una yegua tenga más de un servicio en el mismo día
    SELECT COUNT(*) INTO v_count
    FROM Servicios
    WHERE idEquinoHembra = p_idEquinoHembra
      AND DATE(fechaServicio) = p_fechaServicio;

    IF v_count > 0 THEN
        SET v_mensajeError = 'Error: La yegua ya tiene un servicio registrado en esta fecha.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
    END IF;

    -- Validación para la hora de entrada solo si la fecha es hoy
    IF p_fechaServicio = CURDATE() THEN
        IF p_horaEntrada >= CURRENT_TIME THEN
            SET v_mensajeError = 'Error: La hora de entrada no puede ser mayor o igual a la hora actual.';
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
        END IF;
    END IF;

    -- Validación para la hora de salida
    IF p_horaSalida <= p_horaEntrada THEN
        SET v_mensajeError = 'Error: La hora de salida debe ser mayor que la hora de entrada.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
    END IF;

    -- Validación para la hora de salida solo si la fecha es hoy
    IF p_fechaServicio = CURDATE() THEN
        IF p_horaSalida > CURRENT_TIME THEN
            SET v_mensajeError = 'Error: La hora de salida no puede ser mayor que la hora actual si la fecha es hoy.';
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
        END IF;
    END IF;

    -- Validaciones y lógica para los servicios propios y mixtos
    IF p_tipoServicio = 'propio' THEN
        -- Validaciones de género para servicio propio
        SELECT sexo INTO v_sexoMacho
        FROM Equinos
        WHERE idEquino = p_idEquinoMacho;

        SELECT sexo INTO v_sexoHembra
        FROM Equinos
        WHERE idEquino = p_idEquinoHembra;

        IF v_sexoMacho IS NULL OR v_sexoHembra IS NULL THEN
            SET v_mensajeError = 'Uno o ambos equinos no existen.';
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
        END IF;

        IF v_sexoMacho = v_sexoHembra THEN
            SET v_mensajeError = 'Los equinos deben ser de géneros opuestos.';
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
        END IF;

        -- Registro del servicio propio
        INSERT INTO Servicios (
            idEquinoMacho,
            idEquinoHembra,
            fechaServicio,
            tipoServicio,
            detalles,
            idMedicamento,
            horaEntrada,
            horaSalida,
            idPropietario
        ) VALUES (
            p_idEquinoMacho,
            p_idEquinoHembra,
            p_fechaServicio,
            p_tipoServicio,
            p_detalles,
            p_idMedicamento,
            NULL,
            NULL,
            NULL  -- No hay propietario externo para servicios propios
        );

    ELSEIF p_tipoServicio = 'mixto' THEN
        -- Validaciones para servicio mixto
        IF p_idPropietario IS NULL THEN
            SET v_mensajeError = 'Debe seleccionar el ID del propietario para servicios mixtos.';
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
        END IF;

        -- Obtener el sexo del equino externo
        SELECT sexo INTO v_sexoExterno
        FROM Equinos
        WHERE idEquino = p_idEquinoExterno;

        IF v_sexoExterno IS NULL THEN
            SET v_mensajeError = 'No se encontró un equino externo con el ID proporcionado.';
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
        END IF;

        -- Validaciones para asegurar géneros opuestos
        IF p_idEquinoMacho IS NOT NULL THEN
            SELECT sexo INTO v_sexoMacho
            FROM Equinos
            WHERE idEquino = p_idEquinoMacho;

            IF v_sexoMacho = v_sexoExterno THEN
                SET v_mensajeError = 'El equino externo debe tener el género opuesto al equino propio.';
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
            END IF;

            -- Registro del servicio mixto
            INSERT INTO Servicios (
                idEquinoMacho,
                idEquinoHembra,
                fechaServicio,
                tipoServicio,
                detalles,
                idMedicamento,
                horaEntrada,
                horaSalida,
                idPropietario,
                costoServicio
            ) VALUES (
                p_idEquinoMacho,
                p_idEquinoExterno,
                p_fechaServicio,
                p_tipoServicio,
                p_detalles,
                p_idMedicamento,
                p_horaEntrada,
                p_horaSalida,
                p_idPropietario,
                p_costoServicio
            );
        ELSE
            INSERT INTO Servicios (
                idEquinoMacho,
                idEquinoHembra,
                fechaServicio,
                tipoServicio,
                detalles,
                idMedicamento,
                horaEntrada,
                horaSalida,
                idPropietario,
                costoServicio
            ) VALUES (
                p_idEquinoExterno,
                p_idEquinoHembra,
                p_fechaServicio,
                p_tipoServicio,
                p_detalles,
                p_idMedicamento,
                p_horaEntrada,
                p_horaSalida,
                p_idPropietario,
                p_costoServicio
            );
        END IF;
    END IF;

END $$
DELIMITER ;