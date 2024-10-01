-- Registrar Equino
DELIMITER $$
CREATE PROCEDURE spu_equino_registrar(
    IN _nombreEquino VARCHAR(100),         -- Nombre del equino
    IN _fechaNacimiento DATE,               -- Fecha de nacimiento del equino (puede ser NULL)
    IN _sexo ENUM('macho', 'hembra'),      -- Sexo del equino
    IN _detalles TEXT,                     -- Detalles adicionales del equino
    IN _idTipoEquino INT,                  -- ID del tipo de equino seleccionado
    IN _idPropietario INT,                  -- ID del propietario del equino (puede ser NULL si es propio)
    IN _nacionalidad VARCHAR(50)            -- Nacionalidad del equino (puede ser NULL)
    -- IN _fotografia LONGBLOB
)
BEGIN
    DECLARE _errorMsg VARCHAR(255);
    DECLARE _tipoEquinoNombre VARCHAR(50);

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

    -- Obtener el nombre del tipo de equino para validación
    SELECT tipoequino INTO _tipoEquinoNombre FROM TipoEquinos WHERE idTipoEquino = _idTipoEquino;

    -- Validar tipo de equino según el sexo
    IF _sexo = 'macho' AND _tipoEquinoNombre NOT IN ('padrillo', 'potrillo') THEN
        SET _errorMsg = 'Error: Un macho debe ser un padrillo o un potrillo.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
    END IF;

    IF _sexo = 'hembra' AND _tipoEquinoNombre NOT IN ('yegua', 'potranca') THEN
        SET _errorMsg = 'Error: Una hembra debe ser una yegua o una potranca.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
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
    DECLARE v_sexoMacho ENUM('macho', 'hembra');
    DECLARE v_sexoHembra ENUM('macho', 'hembra');
    DECLARE v_sexoExterno ENUM('macho', 'hembra');
    DECLARE v_mensajeError VARCHAR(255);
    DECLARE v_count INT;

    -- Validación para la fecha de servicio
    IF p_fechaServicio > CURDATE() THEN
        SET v_mensajeError = 'Error: La fecha de servicio no puede ser mayor que la fecha actual.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
    END IF;

    -- Validación para evitar que un padrillo y una yegua realicen más de un servicio en el mismo día
    IF p_tipoServicio = 'propio' THEN
        SELECT COUNT(*) INTO v_count
        FROM Servicios
        WHERE idEquinoHembra = p_idEquinoHembra
          AND DATE(fechaServicio) = p_fechaServicio;

        IF v_count > 0 THEN
            SET v_mensajeError = 'Error: La yegua ya tiene un servicio registrado en esta fecha.';
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
        END IF;

    ELSEIF p_tipoServicio = 'mixto' THEN
        SELECT COUNT(*) INTO v_count
        FROM Servicios
        WHERE (idEquinoHembra = p_idEquinoHembra OR idEquinoHembra IS NULL)
          AND DATE(fechaServicio) = p_fechaServicio;

        IF v_count > 0 THEN
            SET v_mensajeError = 'Error: La yegua ya tiene un servicio registrado en esta fecha.';
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
        END IF;
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
            p_horaEntrada,
            p_horaSalida,
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