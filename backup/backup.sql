-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-10-2024 a las 21:33:27
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `harasdb`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `listarMedicamentos` ()   BEGIN
    SELECT idMedicamento, nombreMedicamento
    FROM Medicamentos;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `registrarServicio` (IN `p_idEquinoMacho` INT, IN `p_idEquinoHembra` INT, IN `p_idPropietario` INT, IN `p_idEquinoExterno` INT, IN `p_fechaServicio` DATE, IN `p_tipoServicio` ENUM('propio','mixto'), IN `p_detalles` TEXT, IN `p_idMedicamento` INT, IN `p_horaEntrada` TIME, IN `p_horaSalida` TIME, IN `p_costoServicio` INT)   BEGIN
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

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_agregar_presentacion_medicamento` (IN `_presentacion` VARCHAR(100))   BEGIN
    DECLARE _exists INT DEFAULT 0;
    
    -- Verificar si la presentación ya existe
    SELECT COUNT(*) INTO _exists 
    FROM PresentacionesMedicamentos
    WHERE LOWER(presentacion) = LOWER(_presentacion);
    
    IF _exists = 0 THEN
        -- Insertar nueva presentación si no existe
        INSERT INTO PresentacionesMedicamentos (presentacion) VALUES (_presentacion);
    ELSE
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La presentación ya existe.';
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_agregar_tipo_medicamento` (IN `_tipo` VARCHAR(100))   BEGIN
    DECLARE _exists INT DEFAULT 0;
    
    -- Verificar si el tipo ya existe
    SELECT COUNT(*) INTO _exists 
    FROM TiposMedicamentos
    WHERE LOWER(tipo) = LOWER(_tipo);
    
    IF _exists = 0 THEN
        -- Insertar nuevo tipo si no existe
        INSERT INTO TiposMedicamentos (tipo) VALUES (_tipo);
    ELSE
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El tipo ya existe.';
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_alimentos_entrada` (IN `_idUsuario` INT, IN `_nombreAlimento` VARCHAR(100), IN `_unidadMedida` VARCHAR(10), IN `_lote` VARCHAR(50), IN `_cantidad` DECIMAL(10,2))   BEGIN
    DECLARE _idAlimento INT;
    DECLARE _idLote INT;
    DECLARE _currentStock DECIMAL(10,2);
    DECLARE _debugInfo VARCHAR(255) DEFAULT '';

    -- Iniciar transacción
    START TRANSACTION;

    -- Verificar si el lote existe y obtener su ID
    SELECT idLote INTO _idLote
    FROM LotesAlimento
    WHERE lote = _lote;

    -- Si el lote no existe, generar un error
    IF _idLote IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El lote especificado no existe.';
    END IF;

    -- Buscar el `idAlimento` correspondiente al nombre, lote y unidad de medida
    SELECT idAlimento, stockActual INTO _idAlimento, _currentStock
    FROM Alimentos
    WHERE LOWER(nombreAlimento) = LOWER(_nombreAlimento)
      AND idLote = _idLote
      AND LOWER(unidadMedida) = LOWER(_unidadMedida)
    LIMIT 1 FOR UPDATE;

    -- Si el alimento no existe para el lote y la unidad de medida, generar un error
    IF _idAlimento IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El alimento con este lote y unidad de medida no está registrado.';
    END IF;

    -- Actualizar el `stockActual` sumando la cantidad
    UPDATE Alimentos
    SET stockActual = stockActual + _cantidad,
        fechaMovimiento = NOW()
    WHERE idAlimento = _idAlimento;

    -- Registrar la entrada en el historial de movimientos
    INSERT INTO HistorialMovimientos (idAlimento, tipoMovimiento, cantidad, idUsuario, fechaMovimiento, unidadMedida)
    VALUES (_idAlimento, 'Entrada', _cantidad, _idUsuario, NOW(), _unidadMedida);

    -- Confirmar la transacción
    COMMIT;

    -- Confirmación de éxito
    SET _debugInfo = 'Transacción completada exitosamente.';
    SIGNAL SQLSTATE '01000' SET MESSAGE_TEXT = _debugInfo;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_alimentos_nuevo` (IN `_idUsuario` INT, IN `_nombreAlimento` VARCHAR(100), IN `_tipoAlimento` VARCHAR(50), IN `_unidadMedida` VARCHAR(10), IN `_lote` VARCHAR(50), IN `_costo` DECIMAL(10,2), IN `_fechaCaducidad` DATE, IN `_stockActual` DECIMAL(10,2), IN `_stockMinimo` DECIMAL(10,2))   BEGIN
    DECLARE _exists INT DEFAULT 0;
    DECLARE _idLote INT;              
    DECLARE _estado ENUM('Disponible', 'Por agotarse', 'Agotado');

    -- Determinar el estado inicial del alimento según el stock actual y mínimo
    IF _stockActual = 0 THEN
        SET _estado = 'Agotado';
    ELSEIF _stockActual <= _stockMinimo THEN
        SET _estado = 'Por agotarse';
    ELSE
        SET _estado = 'Disponible';
    END IF;

    -- Iniciar transacción
    START TRANSACTION;

    -- Verificar si el lote ya está registrado en la tabla LotesAlimento con el mismo valor de lote y unidad de medida
    SELECT idLote INTO _idLote 
    FROM LotesAlimento
    WHERE lote = _lote AND unidadMedida = _unidadMedida;

    -- Si el lote no existe, registrarlo en la tabla LotesAlimento usando el valor enviado desde el formulario
    IF _idLote IS NULL THEN
        INSERT INTO LotesAlimento (lote, unidadMedida, fechaCaducidad, fechaIngreso) 
        VALUES (_lote, _unidadMedida, IFNULL(_fechaCaducidad, NULL), NOW());

        -- Obtener el idLote recién insertado
        SET _idLote = LAST_INSERT_ID();
    END IF;

    -- Verificar si el alimento ya está registrado con ese lote y unidad de medida
    SELECT COUNT(*) INTO _exists 
    FROM Alimentos
    WHERE nombreAlimento = _nombreAlimento AND idLote = _idLote;

    -- Si el alimento no existe con ese lote y unidad de medida, registrar el nuevo alimento
    IF _exists = 0 THEN
        INSERT INTO Alimentos (
            idUsuario, nombreAlimento, tipoAlimento, unidadMedida, idLote, costo, 
            stockActual, stockMinimo, estado, fechaMovimiento, compra
        ) 
        VALUES (
            _idUsuario, _nombreAlimento, _tipoAlimento, _unidadMedida, _idLote, _costo, 
            _stockActual, _stockMinimo, _estado, NOW(), _costo * _stockActual
        );
        COMMIT; -- Confirmar la transacción
    ELSE
        -- Si el alimento ya existe con ese lote, deshacer la transacción
        ROLLBACK;  
    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_alimentos_salida` (IN `_idUsuario` INT, IN `_nombreAlimento` VARCHAR(100), IN `_unidadMedida` VARCHAR(10), IN `_cantidad` DECIMAL(10,2), IN `_idTipoEquino` INT, IN `_lote` VARCHAR(50), IN `_merma` DECIMAL(10,2))   BEGIN
    DECLARE _idAlimento INT;
    DECLARE _currentStock DECIMAL(10,2);
    DECLARE _unidadMedidaLote VARCHAR(10);
    DECLARE _cantidadNecesaria DECIMAL(10,2);
    DECLARE _debugInfo VARCHAR(255) DEFAULT '';  -- Variable para depuración

    -- Iniciar transacción
    START TRANSACTION;

    -- Validar que la cantidad a retirar sea mayor que cero
    IF _cantidad <= 0 THEN
        SET _debugInfo = 'La cantidad a retirar debe ser mayor que cero.';
        ROLLBACK;
    ELSE
        -- Asignar valor por defecto a la merma si es NULL
        IF _merma IS NULL THEN
            SET _merma = 0;
        END IF;

        -- Calcular la cantidad total necesaria (cantidad + merma)
        SET _cantidadNecesaria = _cantidad + _merma;

        -- Buscar el alimento usando el campo `lote` directamente en la tabla `LotesAlimento`
        SET _debugInfo = 'Buscando el lote proporcionado por el usuario...';
        SELECT a.idAlimento, a.stockActual, a.unidadMedida
        INTO _idAlimento, _currentStock, _unidadMedidaLote
        FROM Alimentos a
        JOIN LotesAlimento l ON a.idLote = l.idLote
        WHERE LOWER(a.nombreAlimento) = LOWER(_nombreAlimento)
          AND l.lote = _lote
          AND LOWER(a.unidadMedida) = LOWER(_unidadMedida)
        LIMIT 1 FOR UPDATE;

        -- Verificar si el lote proporcionado existe y la unidad de medida coincide
        IF _idAlimento IS NULL THEN
            SET _debugInfo = 'El lote proporcionado no existe o la unidad de medida no coincide.';
            ROLLBACK;
        ELSEIF _currentStock < _cantidadNecesaria THEN
            SET _debugInfo = 'No hay suficiente stock disponible en el lote seleccionado.';
            ROLLBACK;
        ELSE
            -- Realizar la salida del stock en el lote seleccionado
            UPDATE Alimentos
            SET stockActual = stockActual - _cantidadNecesaria,
                idTipoEquino = _idTipoEquino,
                fechaMovimiento = NOW()
            WHERE idAlimento = _idAlimento;

            -- Registrar la salida y la merma en el historial de movimientos
            INSERT INTO HistorialMovimientos (idAlimento, tipoMovimiento, cantidad, merma, idUsuario, fechaMovimiento, idTipoEquino, unidadMedida)
            VALUES (_idAlimento, 'Salida', _cantidad, _merma, _idUsuario, NOW(), _idTipoEquino, _unidadMedida);

            -- Confirmar la transacción
            COMMIT;

            -- Confirmación de éxito
            SET _debugInfo = 'Transacción completada exitosamente.';
            SIGNAL SQLSTATE '01000' SET MESSAGE_TEXT = _debugInfo;
        END IF;
    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_campos_listar` ()   BEGIN
    SELECT 
        C.idCampo,
        C.numeroCampo,
        C.tamanoCampo,
        TS.nombreTipoSuelo,
        C.estado,
        (SELECT TR.nombreRotacion
         FROM RotacionCampos RC
         JOIN TipoRotaciones TR ON RC.idTipoRotacion = TR.idTipoRotacion
         WHERE RC.idCampo = C.idCampo
         ORDER BY RC.fechaRotacion DESC
         LIMIT 1) AS ultimaAccionRealizada,
        MAX(RC.fechaRotacion) AS fechaUltimaAccion
    FROM 
        Campos C
    LEFT JOIN 
        RotacionCampos RC ON C.idCampo = RC.idCampo
    LEFT JOIN 
        TipoSuelo TS ON C.idTipoSuelo = TS.idTipoSuelo
    GROUP BY 
        C.idCampo, C.numeroCampo, C.tamanoCampo, TS.nombreTipoSuelo, C.estado
    ORDER BY 
        C.numeroCampo DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_consultar_historial_medicoMedi` (IN `_idEquino` INT)   BEGIN
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
			-- No hay columna nombreUsuario, mostrar solo idUsuario como responsable
			DM.idUsuario AS responsable,
			DM.fechaInicio AS fechaRegistro
		FROM 
			DetalleMedicamentos DM
		JOIN 
			Medicamentos M ON DM.idMedicamento = M.idMedicamento
		JOIN 
			Equinos E ON DM.idEquino = E.idEquino
		JOIN 
			Usuarios U ON DM.idUsuario = U.idUsuario -- Aquí mostramos el idUsuario como responsable
		WHERE 
			DM.idEquino = _idEquino
		ORDER BY 
			DM.fechaInicio DESC;
	END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_editar_bosta` (IN `p_idbosta` INT, IN `p_fecha` DATE, IN `p_cantidadsacos` INT, IN `p_pesoaprox` DECIMAL(4,2))   BEGIN
    -- Verificar si la fecha es mayor a la fecha actual
    IF p_fecha > CURRENT_DATE THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La fecha no puede ser mayor a la fecha actual.';
    END IF;

    -- Verificar si la fecha es domingo
    IF DAYOFWEEK(p_fecha) = 1 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se permite registrar datos los domingos';
    ELSE
        -- Actualizar el registro
        UPDATE bostas
        SET 
            fecha = p_fecha,
            cantidadsacos = p_cantidadsacos,
            pesoaprox = p_pesoaprox,
            peso_diario = p_cantidadsacos * p_pesoaprox,
            numero_semana = WEEK(p_fecha, 1) -- Actualiza el número de semana
        WHERE idbosta = p_idbosta;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_editar_campo` (IN `p_idCampo` INT, IN `p_numeroCampo` INT, IN `p_tamanoCampo` DECIMAL(10,2), IN `p_idTipoSuelo` INT, IN `p_estado` ENUM('Activo','Inactivo'))   BEGIN
    DECLARE campoExistente INT;

    SELECT COUNT(*) INTO campoExistente
    FROM Campos
    WHERE idCampo = p_idCampo;

    IF campoExistente = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error: El campo no existe.';
    ELSE
        UPDATE Campos
        SET 
            numeroCampo = p_numeroCampo,
            tamanoCampo = p_tamanoCampo,
            idTipoSuelo = p_idTipoSuelo,  -- Cambiado a idTipoSuelo
            estado = p_estado
        WHERE idCampo = p_idCampo;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_eliminar_bosta` (IN `p_idbosta` INT)   BEGIN
    -- Eliminar el registro
    DELETE FROM bostas
    WHERE idbosta = p_idbosta;

    -- Opcional: verificar si la eliminación fue exitosa
    IF ROW_COUNT() = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se encontró un registro con el ID proporcionado.';
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_eliminar_campo` (IN `p_idCampo` INT)   BEGIN
    -- Eliminar las rotaciones asociadas
    DELETE FROM rotacioncampos WHERE idCampo = p_idCampo;

    -- Ahora eliminar el campo
    DELETE FROM campos WHERE idCampo = p_idCampo;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_equinos_listar` ()   BEGIN
    SELECT
        E.idEquino,
        E.nombreEquino,
        E.fechaNacimiento,
        E.sexo,
        TE.tipoEquino,
        E.detalles,
        EM.nombreEstado,
        E.nacionalidad,
        E.fotografia
    FROM
        Equinos E
    LEFT JOIN TipoEquinos TE ON E.idTipoEquino = TE.idTipoEquino
    LEFT JOIN EstadoMonta EM ON E.idEstadoMonta = EM.idEstado
    WHERE
        E.idPropietario IS NULL
    ORDER BY E.idEquino DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_equino_registrar` (IN `_nombreEquino` VARCHAR(100), IN `_fechaNacimiento` DATE, IN `_sexo` ENUM('Macho','Hembra'), IN `_detalles` TEXT, IN `_idTipoEquino` INT, IN `_idPropietario` INT, IN `_nacionalidad` VARCHAR(50))   BEGIN
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

    -- Validar si ya existe un equino con el mismo nombre
    IF EXISTS (SELECT * FROM Equinos WHERE nombreEquino = _nombreEquino) THEN
        SET _errorMsg = 'Error: Ya existe un equino con ese nombre.';
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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_historial_completo` (IN `tipoMovimiento` VARCHAR(50), IN `fechaInicio` DATE, IN `fechaFin` DATE, IN `idUsuario` INT, IN `limite` INT, IN `desplazamiento` INT)   BEGIN
    -- Validar los límites de la paginación
    IF limite <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El límite de registros debe ser mayor que cero.';
    END IF;

    IF desplazamiento < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El desplazamiento no puede ser negativo.';
    END IF;

    -- Si el tipo de movimiento es 'Entrada', mostrar campos específicos para entradas, incluyendo la cantidad
    IF tipoMovimiento = 'Entrada' THEN
        SELECT 
            h.idAlimento,
            a.nombreAlimento,               -- Nombre del alimento
            a.tipoAlimento,                 -- Tipo de alimento (Grano, Heno, etc.)
            a.unidadMedida,                 -- Unidad de medida del alimento
            l.lote,                         -- Lote del alimento (desde LotesAlimento)
            l.fechaCaducidad,               -- Fecha de caducidad del lote
            a.stockActual,                  -- Stock actual para entradas
            h.cantidad,                     -- Cantidad de entrada
            h.unidadMedida,                 -- Unidad de medida para la cantidad
            h.fechaMovimiento               -- Fecha del movimiento
        FROM 
            HistorialMovimientos h
        JOIN 
            Alimentos a ON h.idAlimento = a.idAlimento  -- Unimos ambas tablas por idAlimento
        JOIN
            LotesAlimento l ON a.idLote = l.idLote      -- Unimos con la tabla LotesAlimento por idLote
        WHERE 
            h.tipoMovimiento = 'Entrada'  
            AND h.fechaMovimiento >= fechaInicio   -- Usar la variable de entrada
            AND h.fechaMovimiento <= fechaFin      -- Usar la variable de entrada
            AND (idUsuario = 0 OR h.idUsuario = idUsuario)  -- Usar la variable de entrada
        ORDER BY 
            h.fechaMovimiento DESC
        LIMIT 
            limite OFFSET desplazamiento;
        
    -- Si el tipo de movimiento es 'Salida', mostrar campos específicos para salidas, incluyendo la cantidad y la merma
    ELSEIF tipoMovimiento = 'Salida' THEN
        SELECT 
            h.idAlimento,
            a.nombreAlimento,               -- Nombre del alimento
            te.tipoEquino,                  -- Tipo de equino (Yegua, Padrillo, Potranca, Potrillo)
            h.cantidad,                     -- Cantidad de salida
            h.unidadMedida,                 -- Unidad de medida
            h.merma,                        -- Merma (si aplica)
            l.lote,                         -- Lote del alimento (desde LotesAlimento)
            h.fechaMovimiento               -- Fecha del movimiento
        FROM 
            HistorialMovimientos h
        JOIN 
            Alimentos a ON h.idAlimento = a.idAlimento  -- Unimos ambas tablas por idAlimento
        LEFT JOIN
            TipoEquinos te ON h.idTipoEquino = te.idTipoEquino  -- Unimos con la tabla TipoEquinos (para la salida)
        JOIN
            LotesAlimento l ON a.idLote = l.idLote      -- Unimos con la tabla LotesAlimento por idLote
        WHERE 
            h.tipoMovimiento = 'Salida'
            AND h.fechaMovimiento >= fechaInicio   -- Usar la variable de entrada
            AND h.fechaMovimiento <= fechaFin      -- Usar la variable de entrada
            AND (idUsuario = 0 OR h.idUsuario = idUsuario)  -- Usar la variable de entrada
        ORDER BY 
            h.fechaMovimiento DESC
        LIMIT 
            limite OFFSET desplazamiento;
    ELSE
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Tipo de movimiento no válido.';
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_historial_completo_medicamentos` (IN `tipoMovimiento` VARCHAR(50), IN `fechaInicio` DATE, IN `fechaFin` DATE, IN `idUsuario` INT, IN `limite` INT, IN `desplazamiento` INT)   BEGIN
    -- Validar los límites de la paginación
    IF limite <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El límite de registros debe ser mayor que cero.';
    END IF;

    IF desplazamiento < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El desplazamiento no puede ser negativo.';
    END IF;

    -- Si el tipo de movimiento es 'Entrada', mostrar campos específicos para entradas
    IF tipoMovimiento = 'Entrada' THEN
        SELECT 
            h.idMedicamento,
            m.nombreMedicamento,           -- Nombre del medicamento
            m.descripcion,                 -- Descripción del medicamento
            m.cantidad_stock AS stockActual, -- Stock actual
            h.cantidad,                    -- Cantidad de entrada
            h.fechaMovimiento              -- Fecha del movimiento
        FROM 
            HistorialMovimientosMedicamentos h
        JOIN 
            Medicamentos m ON h.idMedicamento = m.idMedicamento -- Unimos ambas tablas por idMedicamento
        WHERE 
            h.tipoMovimiento = 'Entrada'  
            AND h.fechaMovimiento >= fechaInicio   -- Usar la variable de entrada
            AND h.fechaMovimiento <= fechaFin      -- Usar la variable de entrada
            AND (idUsuario = 0 OR h.idUsuario = idUsuario)  -- Usar la variable de entrada
        ORDER BY 
            h.fechaMovimiento DESC
        LIMIT 
            limite OFFSET desplazamiento;
        
    -- Si el tipo de movimiento es 'Salida', mostrar campos específicos para salidas
    ELSEIF tipoMovimiento = 'Salida' THEN
        SELECT 
            h.idMedicamento,
            m.nombreMedicamento,           -- Nombre del medicamento
            m.descripcion,                 -- Descripción del medicamento
            te.tipoEquino,                 -- Tipo de equino (solo en salidas, si aplica)
            h.cantidad,                    -- Cantidad de salida
            h.fechaMovimiento              -- Fecha del movimiento
        FROM 
            HistorialMovimientosMedicamentos h
        JOIN 
            Medicamentos m ON h.idMedicamento = m.idMedicamento -- Unimos ambas tablas por idMedicamento
        LEFT JOIN
            TipoEquinos te ON h.idTipoEquino = te.idTipoEquino  -- Unimos con la tabla TipoEquinos (solo para salidas)
        WHERE 
            h.tipoMovimiento = 'Salida'
            AND h.fechaMovimiento >= fechaInicio   -- Usar la variable de entrada
            AND h.fechaMovimiento <= fechaFin      -- Usar la variable de entrada
            AND (idUsuario = 0 OR h.idUsuario = idUsuario)  -- Usar la variable de entrada
        ORDER BY 
            h.fechaMovimiento DESC
        LIMIT 
            limite OFFSET desplazamiento;
    ELSE
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Tipo de movimiento no válido.';
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_historial_medico_registrarMedi` (IN `_idEquino` INT, IN `_idUsuario` INT, IN `_idMedicamento` INT, IN `_dosis` VARCHAR(50), IN `_cantidad` INT, IN `_frecuenciaAdministracion` VARCHAR(50), IN `_viaAdministracion` VARCHAR(50), IN `_pesoEquino` DECIMAL(10,2), IN `_fechaInicio` DATE, IN `_fechaFin` DATE, IN `_observaciones` TEXT, IN `_reaccionesAdversas` TEXT)   BEGIN
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

    -- Validar la combinación de medicamento y dosis usando solo la unidad
    SET _unidadDosis = TRIM(LOWER(REPLACE(_dosis, '[0-9]+', '')));
    
    IF NOT EXISTS (
        SELECT 1 
        FROM CombinacionesMedicamentos
        WHERE idMedicamento = _idMedicamento
          AND LOWER(SUBSTRING_INDEX(dosis, ' ', -1)) = _unidadDosis
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La dosis no coincide con la unidad de dosis del medicamento.';
    END IF;

    -- Validar que la fecha de inicio sea menor o igual a la fecha de fin
    IF _fechaInicio > _fechaFin THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La fecha de inicio no puede ser posterior a la fecha de fin.';
    END IF;

    -- Insertar el detalle del medicamento administrado al equino
    INSERT INTO DetalleMedicamentos (
        idMedicamento,
        idEquino,
        dosis,
        cantidad,
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
        _cantidad,
        _frecuenciaAdministracion,
        _viaAdministracion,
        IFNULL(_pesoEquino, NULL),
		NOW(),  -- Fecha de inicio como la fecha actual
        _fechaFin,
        _observaciones,
        IFNULL(_reaccionesAdversas, NULL),
        _idUsuario
    );

    -- Confirmar la transacción
    COMMIT;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_listarServiciosPorTipo` (IN `p_tipoServicio` ENUM('Propio','Mixto','General'))   BEGIN
    SELECT 
        s.idServicio,
        em.nombreEquino AS nombrePadrillo,
        eh.nombreEquino AS nombreYegua,
        s.fechaServicio,
        s.detalles,
        s.horaEntrada,
        s.horaSalida,
        s.costoServicio,
        CASE 
            WHEN s.tipoServicio = 'Mixto' THEN p.nombreHaras 
            ELSE NULL 
        END AS nombreHaras
    FROM 
        Servicios s
    LEFT JOIN Equinos em ON s.idEquinoMacho = em.idEquino
    LEFT JOIN Equinos eh ON s.idEquinoHembra = eh.idEquino
    LEFT JOIN Propietarios p ON s.idPropietario = p.idPropietario
    WHERE 
        (p_tipoServicio = 'General' OR s.tipoServicio = p_tipoServicio)
    ORDER BY 
        s.fechaServicio DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_listar_bostas` ()   BEGIN
    SELECT 
        b.idbosta,
        b.fecha,
        b.cantidadsacos,
        b.pesoaprox,
        b.peso_diario,
        CASE 
            WHEN ROW_NUMBER() OVER (PARTITION BY b.numero_semana ORDER BY b.fecha) = 1 THEN 
                (SELECT SUM(peso_diario) 
                 FROM bostas 
                 WHERE WEEK(fecha, 1) = b.numero_semana 
                   AND YEAR(fecha) = YEAR(b.fecha)) 
            ELSE NULL 
        END AS peso_semanal,
        b.numero_semana,
        (SELECT SUM(peso_diario) 
         FROM bostas 
         WHERE fecha <= CURDATE()) AS total_acumulado
    FROM 
        bostas b
    ORDER BY 
        b.numero_semana DESC,
        b.fecha ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_listar_equinos_por_propietario` (IN `_idPropietario` INT, IN `_genero` INT)   BEGIN
    SELECT 
        e.idEquino,           
        e.nombreEquino,         
        p.nombreHaras            
    FROM 
        Equinos e
    JOIN 
        Propietarios p ON e.idPropietario = p.idPropietario 
    WHERE 
        e.idPropietario = _idPropietario AND  
        e.sexo = _genero;                      
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_listar_equinos_propios` ()   BEGIN
    SELECT 
        idEquino,
        nombreEquino,
        sexo,
        idTipoEquino
    FROM 
        Equinos
    WHERE 
        idPropietario IS NULL
        AND idTipoEquino IN (1, 2);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_listar_equinos_propiosMedi` ()   BEGIN
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
	END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_listar_haras` ()   BEGIN
		SELECT DISTINCT 
        idPropietario,
        nombreHaras
    FROM Propietarios;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_listar_lotes_alimentos` ()   BEGIN
    -- Seleccionar todos los lotes registrados junto con la información de los alimentos asociados
    SELECT 
        l.idLote,               -- Incluir idLote para referencia única
        l.lote,                 -- Lote desde la tabla LotesAlimento
        l.fechaCaducidad,       -- Fecha de caducidad del lote
        l.fechaIngreso,         -- Fecha de ingreso del lote
        a.nombreAlimento,       -- Nombre del alimento
        a.tipoAlimento,         -- Tipo de alimento
        a.stockActual,          -- Stock actual del alimento
        a.stockMinimo,          -- Stock mínimo para alerta
        a.estado,               -- Estado del alimento
        a.unidadMedida,         -- Unidad de medida del alimento
        a.costo                 -- Costo unitario del alimento
    FROM 
        Alimentos a
    JOIN 
        LotesAlimento l ON a.idLote = l.idLote  -- Relacionar los lotes con los alimentos
    ORDER BY 
        l.idLote ASC;  -- Ordenar los resultados por idLote para mantener consistencia
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_listar_lotes_medicamentos` ()   BEGIN
    -- Seleccionar todos los lotes registrados en la tabla LotesMedicamento junto con los datos asociados de Medicamentos
    SELECT 
        lm.lote,                         -- Lote del medicamento
        m.nombreMedicamento,             -- Nombre del medicamento
        m.descripcion,                   -- Descripción del medicamento
        m.cantidad_stock,                -- Stock actual del medicamento
        m.stockMinimo,                   -- Stock mínimo del medicamento
        lm.fechaCaducidad,               -- Fecha de caducidad del lote
        m.estado                         -- Estado del medicamento
    FROM 
        LotesMedicamento lm
    JOIN 
        Medicamentos m ON lm.idLoteMedicamento = m.idLoteMedicamento; -- Unir con Medicamentos según el lote
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_listar_medicamentosMedi` ()   BEGIN
    -- Mostrar la información detallada de todos los medicamentos registrados
    SELECT 
        m.idMedicamento,
        m.nombreMedicamento,
        m.descripcion,
        lm.lote,                         -- Lote del medicamento (desde LotesMedicamento)
        p.presentacion,
        c.dosis,
        t.tipo AS nombreTipo,            -- Mostrar el nombre del tipo de medicamento
        m.cantidad_stock,
        m.stockMinimo,
        lm.fechaIngreso,                 -- Fecha de ingreso del lote
        lm.fechaCaducidad,               -- Fecha de caducidad del lote
        m.precioUnitario,
        m.estado
    FROM 
        Medicamentos m
    JOIN 
        CombinacionesMedicamentos c ON m.idCombinacion = c.idCombinacion
    JOIN 
        TiposMedicamentos t ON c.idTipo = t.idTipo
    JOIN 
        PresentacionesMedicamentos p ON c.idPresentacion = p.idPresentacion
    JOIN
        LotesMedicamento lm ON m.idLoteMedicamento = lm.idLoteMedicamento -- Relación con LotesMedicamento
    ORDER BY 
        m.nombreMedicamento ASC; -- Ordenar alfabéticamente por nombre de medicamento
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_listar_presentaciones_medicamentos` ()   BEGIN
    -- Selecciona todas las presentaciones de medicamentos
    SELECT 
        idPresentacion, 
        presentacion 
    FROM 
        PresentacionesMedicamentos
    ORDER BY 
        presentacion ASC;  -- Ordena por el nombre de la presentación
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_listar_rotaciones` ()   BEGIN
    SELECT 
    c.numeroCampo,
    tr.nombreRotacion, 
    rc.fechaRotacion 
	FROM 
		RotacionCampos rc
	JOIN 
		TipoRotaciones tr ON rc.idTipoRotacion = tr.idTipoRotacion
	JOIN
        Campos c ON rc.idCampo = c.idCampo;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_listar_tipoequinos` ()   BEGIN
    -- Listar todos los tipos de equinos disponibles
    SELECT idTipoEquino, tipoEquino
    FROM TipoEquinos;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_listar_tipos_presentaciones_dosis` ()   BEGIN
    -- Selecciona los tipos de medicamentos junto con la presentación y la dosis, agrupados
    SELECT 
        t.tipo, 
        GROUP_CONCAT(DISTINCT p.presentacion ORDER BY p.presentacion ASC SEPARATOR ', ') AS presentaciones,
        GROUP_CONCAT(DISTINCT c.dosis ORDER BY c.dosis ASC SEPARATOR ', ') AS dosis
    FROM 
        CombinacionesMedicamentos c
    JOIN 
        TiposMedicamentos t ON c.idTipo = t.idTipo
    JOIN 
        PresentacionesMedicamentos p ON c.idPresentacion = p.idPresentacion
    GROUP BY 
        t.tipo
    ORDER BY 
        t.tipo ASC;  -- Ordena por tipo de medicamento
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_medicamentos_entrada` (IN `_idUsuario` INT, IN `_nombreMedicamento` VARCHAR(255), IN `_lote` VARCHAR(100), IN `_cantidad` INT)   BEGIN
    DECLARE _idMedicamento INT;
    DECLARE _idLoteMedicamento INT;
    DECLARE _currentStock INT;
    DECLARE _debugInfo VARCHAR(255) DEFAULT '';

    -- Iniciar transacción para asegurar la consistencia de la operación
    START TRANSACTION;

    -- Verificar si el lote existe en LotesMedicamento y obtener su ID
    SELECT idLoteMedicamento INTO _idLoteMedicamento
    FROM LotesMedicamento
    WHERE lote = _lote;

    -- Si el lote no existe, generar un error
    IF _idLoteMedicamento IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El lote especificado no existe en LotesMedicamento.';
    END IF;

    -- Buscar el `idMedicamento` correspondiente al nombre y lote
    SELECT idMedicamento, cantidad_stock INTO _idMedicamento, _currentStock
    FROM Medicamentos
    WHERE LOWER(nombreMedicamento) = LOWER(_nombreMedicamento)
      AND idLoteMedicamento = _idLoteMedicamento
    LIMIT 1 FOR UPDATE;

    -- Si el medicamento no existe para el lote, generar un error
    IF _idMedicamento IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El medicamento con este lote no está registrado.';
    END IF;

    -- Actualizar el `cantidad_stock` sumando la cantidad
    UPDATE Medicamentos
    SET cantidad_stock = cantidad_stock + _cantidad,
        ultima_modificacion = NOW()
    WHERE idMedicamento = _idMedicamento;

    -- Registrar la entrada en el historial de movimientos de medicamentos
    INSERT INTO HistorialMovimientosMedicamentos (idMedicamento, tipoMovimiento, cantidad, idUsuario, fechaMovimiento)
    VALUES (_idMedicamento, 'Entrada', _cantidad, _idUsuario, NOW());

    -- Confirmar la transacción
    COMMIT;

    -- Confirmación de éxito
    SET _debugInfo = 'Transacción completada exitosamente.';
    SIGNAL SQLSTATE '01000' SET MESSAGE_TEXT = _debugInfo;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_medicamentos_registrar` (IN `_nombreMedicamento` VARCHAR(255), IN `_descripcion` TEXT, IN `_lote` VARCHAR(100), IN `_presentacion` VARCHAR(100), IN `_dosis` VARCHAR(50), IN `_tipo` VARCHAR(100), IN `_cantidad_stock` INT, IN `_stockMinimo` INT, IN `_fechaCaducidad` DATE, IN `_precioUnitario` DECIMAL(10,2), IN `_idUsuario` INT)   BEGIN
    DECLARE _idCombinacion INT;
    DECLARE _idLoteMedicamento INT;

    -- Manejador de errores: si ocurre un error, se revierte la transacción
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    -- Iniciar la transacción
    START TRANSACTION;

    -- Verificar si el lote ya está registrado en la tabla LotesMedicamento
    SELECT idLoteMedicamento INTO _idLoteMedicamento 
    FROM LotesMedicamento
    WHERE lote = _lote;

    -- Si el lote no existe, insertarlo en la tabla LotesMedicamento
    IF _idLoteMedicamento IS NULL THEN
        INSERT INTO LotesMedicamento (lote, fechaCaducidad, fechaIngreso) 
        VALUES (_lote, _fechaCaducidad, NOW());
        SET _idLoteMedicamento = LAST_INSERT_ID();
    END IF;

    -- Validar la combinación de tipo, presentación y dosis usando el procedimiento de validación
    CALL spu_validar_registrar_combinacion(_tipo, _presentacion, _dosis);

    -- Obtener el ID de la combinación de tipo, presentación y dosis, ignorando el número en la dosis
    SELECT idCombinacion INTO _idCombinacion
    FROM CombinacionesMedicamentos
    WHERE idTipo = (SELECT idTipo FROM TiposMedicamentos WHERE LOWER(tipo) = LOWER(_tipo))
      AND idPresentacion = (SELECT idPresentacion FROM PresentacionesMedicamentos WHERE LOWER(presentacion) = LOWER(_presentacion))
      AND LOWER(SUBSTRING_INDEX(dosis, ' ', -1)) = LOWER(SUBSTRING_INDEX(_dosis, ' ', -1))
    LIMIT 1;

    -- Insertar el medicamento en la tabla Medicamentos
    INSERT INTO Medicamentos (
        nombreMedicamento, 
        descripcion, 
        idLoteMedicamento, 
        idCombinacion,
        cantidad_stock,
        stockMinimo, 
        fecha_registro,
        precioUnitario, 
        estado, 
        idUsuario
    ) 
    VALUES (
        _nombreMedicamento, 
        _descripcion, 
        _idLoteMedicamento, 
        _idCombinacion,
        _cantidad_stock, 
        _stockMinimo, 
        CURDATE(), 
        _precioUnitario, 
        'Disponible', 
        _idUsuario
    );

    -- Confirmar la transacción
    COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_medicamentos_salida` (IN `_idUsuario` INT, IN `_nombreMedicamento` VARCHAR(255), IN `_cantidad` DECIMAL(10,2), IN `_idTipoEquino` INT, IN `_lote` VARCHAR(100))   BEGIN
    DECLARE _idMedicamento INT;
    DECLARE _idLoteMedicamento INT;
    DECLARE _currentStock DECIMAL(10,2);
    DECLARE _debugInfo VARCHAR(255) DEFAULT '';  -- Variable para depuración

    -- Iniciar transacción para asegurar consistencia de la operación
    START TRANSACTION;

    -- Validar que la cantidad a retirar sea mayor que cero
    IF _cantidad <= 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La cantidad a retirar debe ser mayor que cero.';
    END IF;

    -- Verificar si el lote existe en LotesMedicamento y obtener su ID
    SELECT idLoteMedicamento INTO _idLoteMedicamento
    FROM LotesMedicamento
    WHERE lote = _lote;

    -- Si el lote no existe, generar un error
    IF _idLoteMedicamento IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El lote especificado no existe en LotesMedicamento.';
    END IF;

    -- Buscar el medicamento usando el nombre y lote
    SELECT idMedicamento, cantidad_stock INTO _idMedicamento, _currentStock
    FROM Medicamentos
    WHERE LOWER(nombreMedicamento) = LOWER(_nombreMedicamento)
      AND idLoteMedicamento = _idLoteMedicamento
    LIMIT 1 FOR UPDATE;

    -- Si el medicamento no existe para el lote, generar un error
    IF _idMedicamento IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El medicamento con este lote no está registrado.';
    ELSEIF _currentStock < _cantidad THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No hay suficiente stock disponible en el lote seleccionado.';
    ELSE
        -- Realizar la salida del stock en el lote seleccionado
        UPDATE Medicamentos
        SET cantidad_stock = cantidad_stock - _cantidad,
            ultima_modificacion = NOW()
        WHERE idMedicamento = _idMedicamento;

        -- Registrar la salida en el historial de movimientos
        INSERT INTO HistorialMovimientosMedicamentos (idMedicamento, tipoMovimiento, cantidad, idUsuario, fechaMovimiento, idTipoEquino)
        VALUES (_idMedicamento, 'Salida', _cantidad, _idUsuario, NOW(), _idTipoEquino);

        -- Confirmar la transacción
        COMMIT;

        -- Confirmación de éxito
        SET _debugInfo = 'Transacción completada exitosamente.';
        SIGNAL SQLSTATE '01000' SET MESSAGE_TEXT = _debugInfo;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_notificar_stock_bajo_alimentos` ()   BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE alimentoNombre VARCHAR(100);
    DECLARE loteAlimento VARCHAR(50);
    DECLARE stockActual DECIMAL(10,2);
    DECLARE stockMinimo DECIMAL(10,2);

    -- Cursor para seleccionar los alimentos con stock bajo o agotados, limitando a 5
    DECLARE cur CURSOR FOR
        SELECT a.nombreAlimento, l.lote, a.stockActual, a.stockMinimo 
        FROM Alimentos a
        JOIN LotesAlimento l ON a.idLote = l.idLote
        WHERE a.stockActual <= a.stockMinimo
        ORDER BY a.stockActual ASC
        LIMIT 5;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    -- Abrir el cursor
    OPEN cur;

    -- Bucle para recorrer los resultados
    read_loop: LOOP
        FETCH cur INTO alimentoNombre, loteAlimento, stockActual, stockMinimo;
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Imprimir el mensaje de notificación
        IF stockActual = 0 THEN
            -- Notificación de alimentos agotados
            SELECT CONCAT('Alimento agotado: ', alimentoNombre, ', Lote: ', loteAlimento, ', Stock: ', stockActual) AS Notificacion;
        ELSE
            -- Notificación de alimentos con stock bajo
            SELECT CONCAT('Alimento con stock bajo: ', alimentoNombre, ', Lote: ', loteAlimento, ', Stock: ', stockActual, ' (Stock mínimo: ', stockMinimo, ')') AS Notificacion;
        END IF;
    END LOOP;

    -- Cerrar cursor
    CLOSE cur;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_notificar_stock_bajo_medicamentos` ()   BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE medicamentoNombre VARCHAR(100);
    DECLARE lote VARCHAR(50);
    DECLARE cantidadStock INT;

    -- Cursor para seleccionar medicamentos con stock bajo o agotado, limitando a 5 resultados
    DECLARE cur CURSOR FOR
        SELECT m.nombreMedicamento, lm.lote, m.cantidad_stock
        FROM Medicamentos m
        JOIN LotesMedicamento lm ON m.idLoteMedicamento = lm.idLoteMedicamento
        WHERE m.cantidad_stock < m.stockMinimo OR m.cantidad_stock = 0
        LIMIT 5;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    -- Abrir el cursor
    OPEN cur;

    -- Bucle para recorrer los resultados
    read_loop: LOOP
        FETCH cur INTO medicamentoNombre, lote, cantidadStock;
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Imprimir el mensaje de notificación
        IF cantidadStock = 0 THEN
            -- Notificación de medicamentos agotados
            SELECT CONCAT('Medicamento agotado: ', medicamentoNombre, ', Lote: ', lote, ', Stock: ', cantidadStock) AS Notificacion;
        ELSE
            -- Notificación de medicamentos con stock bajo
            SELECT CONCAT('Medicamento con stock bajo: ', medicamentoNombre, ', Lote: ', lote, ', Stock: ', cantidadStock) AS Notificacion;
        END IF;
    END LOOP;

    -- Cerrar el cursor
    CLOSE cur;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_obtenerAlimentosConLote` ()   BEGIN
    SELECT 
        A.idAlimento,
        A.idUsuario,
        A.nombreAlimento,
        A.tipoAlimento,
        A.stockActual,
        A.stockMinimo,
        A.estado,
        A.unidadMedida,
        A.costo,
        A.idLote,
        A.idTipoEquino,
        A.merma,
        A.compra,
        A.fechaMovimiento,
        L.idLote AS loteId,
        L.lote,
        L.unidadMedida AS loteUnidadMedida,
        L.fechaCaducidad,
        L.fechaIngreso
    FROM 
        Alimentos A
    INNER JOIN 
        LotesAlimento L ON A.idLote = L.idLote;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_obtener_acceso_usuario` (IN `_idRol` INT)   BEGIN
    SELECT 
       PE.idpermiso,
       MO.modulo,
       VI.ruta,
       VI.sidebaroption,
       VI.texto,
       VI.icono
       FROM permisos PE
       INNER JOIN vistas VI ON VI.idvista = PE.idvista
       LEFT JOIN modulos MO ON MO.idmodulo = VI.idmodulo
       WHERE PE.idRol = _idRol;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_obtener_campoID` (IN `p_idCampo` INT)   BEGIN
    SELECT 
        c.idCampo,
        c.numeroCampo,
        c.tamanoCampo,
        c.idTipoSuelo,  -- Asegúrate de incluir idTipoSuelo aquí
        ts.nombreTipoSuelo,
        c.estado
    FROM Campos c
    LEFT JOIN tipoSuelo ts ON c.idTipoSuelo = ts.idTipoSuelo
    WHERE c.idCampo = p_idCampo;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_obtener_pesos` ()   BEGIN
    DECLARE v_peso_semanal DECIMAL(9,2);
    DECLARE v_peso_mensual DECIMAL(12,2);
    
    -- Calcular el peso semanal (desde el lunes hasta la fecha actual)
    SELECT COALESCE(SUM(peso_diario), 0)
    INTO v_peso_semanal
    FROM bostas
    WHERE WEEK(fecha, 1) = WEEK(CURDATE(), 1) 
      AND YEAR(fecha) = YEAR(CURDATE())
      AND fecha <= CURDATE(); -- Solo hasta la fecha actual

    -- Calcular el peso mensual (todo el mes actual)
    SELECT COALESCE(SUM(peso_diario), 0)
    INTO v_peso_mensual
    FROM bostas
    WHERE MONTH(fecha) = MONTH(CURDATE())
      AND YEAR(fecha) = YEAR(CURDATE());

    -- Devolver resultados sin el peso diario
    SELECT v_peso_semanal AS peso_semanal,
           v_peso_mensual AS peso_mensual;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_obtener_tipo_equino_alimento` ()   BEGIN
    SELECT idTipoEquino, tipoEquino
    FROM TipoEquinos
    WHERE tipoEquino IN ('Yegua', 'Padrillo', 'Potranca', 'Potrillo');
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_obtener_ultima_accion` (IN `idCampo` INT)   BEGIN
    DECLARE nombreRotacion VARCHAR(100);
    
    SELECT tr.nombreRotacion INTO nombreRotacion
    FROM RotacionCampos rc
    JOIN TipoRotaciones tr ON rc.idTipoRotacion = tr.idTipoRotacion
    WHERE rc.idCampo = idCampo
    ORDER BY rc.fechaRotacion DESC
    LIMIT 1;

    IF nombreRotacion IS NULL THEN
        SELECT 'No hay acciones registradas' AS mensaje;
    ELSE
        SELECT nombreRotacion;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_personal_listar` ()   BEGIN
    -- Consulta para listar personal y verificar si ya tienen un usuario
    SELECT 
        p.idPersonal, 
        p.nombres, 
        p.apellidos, 
        p.direccion,
        p.tipodoc,
        p.nrodocumento,
        CASE 
            WHEN u.idUsuario IS NOT NULL THEN 1 
            ELSE 0 
        END AS tieneUsuario
    FROM 
        Personal p
    LEFT JOIN 
        Usuarios u ON p.idPersonal = u.idPersonal;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_personal_registrar` (OUT `_idPersonal` INT, IN `_nombres` VARCHAR(100), IN `_apellidos` VARCHAR(100), IN `_direccion` VARCHAR(255), IN `_tipodoc` VARCHAR(20), IN `_nrodocumento` VARCHAR(50), IN `_numeroHijos` INT, IN `_fechaIngreso` DATE, IN `_tipoContrato` ENUM('Parcial','Completo','Por Prácticas','Otro'))   BEGIN
    -- Declaración de variables
    DECLARE existe_error INT DEFAULT 0;
    
    -- Manejador de errores
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
    BEGIN
        SET existe_error = 1;
    END;
    
    -- Intentar insertar los datos en la tabla Personal
    INSERT INTO Personal (nombres, apellidos, direccion, tipodoc, nrodocumento, numeroHijos, fechaIngreso, tipoContrato)
    VALUES (_nombres, _apellidos, _direccion, _tipodoc, _nrodocumento, _numeroHijos, _fechaIngreso, _tipoContrato);
    
    -- Verificar si ocurrió un error
    IF existe_error = 1 THEN
        SET _idPersonal = -1;  -- Devuelve -1 si hay error
    ELSE
        SET _idPersonal = LAST_INSERT_ID();  -- Devuelve el ID del nuevo registro
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_registrar_bosta` (IN `p_fecha` DATE, IN `p_cantidadsacos` INT, IN `p_pesoaprox` DECIMAL(4,2))   BEGIN
    DECLARE v_peso_diario DECIMAL(9,2);
    DECLARE v_peso_semanal DECIMAL(9,2);
    DECLARE v_peso_mensual DECIMAL(12,2);
    DECLARE v_numero_semana INT;
    DECLARE v_mensaje_error VARCHAR(255);

    -- Verificar si la fecha es mayor a la fecha actual
    IF p_fecha > CURRENT_DATE THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La fecha no puede ser mayor a la fecha actual.';
    END IF;

    -- Verificar si la fecha es domingo
    IF DAYOFWEEK(p_fecha) = 1 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se permite registrar datos los domingos';
    ELSE
        -- Verificar si ya existe un registro para esta fecha
        IF EXISTS (SELECT 1 FROM bostas WHERE fecha = p_fecha) THEN
            SET v_mensaje_error = CONCAT('Ya existe un registro para esta fecha: ', p_fecha);
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensaje_error;
        ELSE
            -- Calcular el número de semana y el peso diario
            SET v_numero_semana = WEEK(p_fecha, 1);
            SET v_peso_diario = p_cantidadsacos * p_pesoaprox;

            -- Calcular el peso semanal (incluyendo el peso diario actual)
            SELECT COALESCE(SUM(peso_diario), 0) + v_peso_diario
            INTO v_peso_semanal
            FROM bostas
            WHERE WEEK(fecha, 1) = v_numero_semana
              AND YEAR(fecha) = YEAR(p_fecha);  -- Coincide con el año

            -- Calcular el peso mensual (incluyendo el peso diario actual)
            SELECT COALESCE(SUM(peso_diario), 0) + v_peso_diario
            INTO v_peso_mensual
            FROM bostas
            WHERE MONTH(fecha) = MONTH(p_fecha)
              AND YEAR(fecha) = YEAR(p_fecha);  -- Coincide con el año

            -- Insertar el registro con el peso calculado
            INSERT INTO bostas (fecha, cantidadsacos, pesoaprox, peso_diario, peso_semanal, peso_mensual, numero_semana)
            VALUES (
                p_fecha,
                p_cantidadsacos,
                p_pesoaprox,
                v_peso_diario,
                v_peso_semanal,
                v_peso_mensual,
                v_numero_semana
            );
        END IF;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_registrar_campo` (IN `p_numeroCampo` INT, IN `p_tamanoCampo` DECIMAL(10,2), IN `p_idTipoSuelo` INT, IN `p_estado` VARCHAR(50))   BEGIN
    DECLARE campoExistente INT;

    SELECT COUNT(*) INTO campoExistente
    FROM Campos
    WHERE numeroCampo = p_numeroCampo;

    IF campoExistente > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error: Ya existe un campo con el mismo número.';
    ELSE
        INSERT INTO Campos (numeroCampo, tamanoCampo, idTipoSuelo, estado)  -- Cambiado a idTipoSuelo
        VALUES (p_numeroCampo, p_tamanoCampo, p_idTipoSuelo, p_estado);
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_registrar_rotacion_campos` (IN `p_idCampo` INT, IN `p_idTipoRotacion` INT, IN `p_fechaRotacion` DATETIME, IN `p_detalleRotacion` TEXT)   BEGIN
    DECLARE v_count INT;

    -- Verificar si ya existe una rotación del mismo tipo en la misma fecha
    SELECT COUNT(*) INTO v_count
    FROM RotacionCampos
    WHERE idCampo = p_idCampo
      AND idTipoRotacion = p_idTipoRotacion
      AND DATE(fechaRotacion) = DATE(p_fechaRotacion);

    IF v_count > 0 THEN
        -- Si existe, se puede lanzar un error o manejarlo como desees
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Ya existe una rotación del mismo tipo en la misma fecha.';
    ELSE
        -- Si no existe, proceder a insertar
        INSERT INTO RotacionCampos (idCampo, idTipoRotacion, fechaRotacion, detalleRotacion)
        VALUES (p_idCampo, p_idTipoRotacion, p_fechaRotacion, p_detalleRotacion);
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_tiposuelo_listar` ()   BEGIN
    SELECT 
        C.idTipoSuelo,
        C.nombreTipoSuelo
    FROM 
        tipoSuelo C;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_tipos_rotaciones_listar` ()   BEGIN
    SELECT 
        TR.idTipoRotacion,
        TR.nombreRotacion,
        TR.detalles
    FROM 
        TipoRotaciones TR
    ORDER BY 
        TR.nombreRotacion;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_usuarios_listar` ()   BEGIN
    SELECT 
        USU.idUsuario,
        PER.nombres,
        PER.apellidos,
        PER.tipodoc,
        PER.nrodocumento,
        PER.direccion,
        USU.correo,
        USU.idRol
    FROM 
        Usuarios USU
    INNER JOIN 
        Personal PER ON USU.idPersonal = PER.idPersonal
    ORDER BY 
        USU.idUsuario DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_usuarios_login` (IN `_correo` VARCHAR(100))   BEGIN
    SELECT 
        USU.idUsuario,
        PER.apellidos,
        PER.nombres,
        USU.correo,
        USU.clave,
        USU.idRol
    FROM 
        Usuarios USU
    INNER JOIN 
        Personal PER ON PER.idPersonal = USU.idPersonal
    WHERE 
        USU.correo = _correo;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_usuarios_registrar` (OUT `_idUsuario` INT, IN `_idPersonal` INT, IN `_correo` VARCHAR(50), IN `_clave` VARCHAR(100), IN `_idRol` INT)   BEGIN
    -- Declaración de variables
    DECLARE existe_error INT DEFAULT 0;
    
    -- Manejador de errores
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
    BEGIN
        SET existe_error = 1;
    END;
    
    INSERT INTO Usuarios (idPersonal, correo, clave, idRol)
    VALUES (_idPersonal, _correo, _clave, _idRol);
    
    IF existe_error = 1 THEN
        SET _idUsuario = -1; 
    ELSE
        SET _idUsuario = LAST_INSERT_ID(); 
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_validar_registrar_combinacion` (IN `_tipoMedicamento` VARCHAR(100), IN `_presentacionMedicamento` VARCHAR(100), IN `_dosisMedicamento` VARCHAR(50))   BEGIN
    DECLARE _idTipo INT;
    DECLARE _idPresentacion INT;
    DECLARE _idCombinacion INT;
    DECLARE _unidadDosis VARCHAR(50);
    DECLARE _errorMensaje VARCHAR(255);

    -- Manejador de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMensaje;
    END;

    -- Iniciar la transacción
    START TRANSACTION;

    -- Validar tipo de medicamento
    SELECT idTipo INTO _idTipo
    FROM TiposMedicamentos
    WHERE LOWER(tipo) = LOWER(_tipoMedicamento)
    LIMIT 1;

    IF _idTipo IS NULL THEN
        SET _errorMensaje = CONCAT('Error: El tipo de medicamento "', _tipoMedicamento, '" no es válido o no existe.');
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMensaje;
    END IF;

    -- Validar presentación
    SELECT idPresentacion INTO _idPresentacion
    FROM PresentacionesMedicamentos
    WHERE LOWER(presentacion) = LOWER(_presentacionMedicamento)
    LIMIT 1;

    IF _idPresentacion IS NULL THEN
        SET _errorMensaje = CONCAT('Error: La presentación "', _presentacionMedicamento, '" no es válida o no existe.');
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMensaje;
    END IF;

    -- Obtener solo la unidad de medida de la dosis
    SET _unidadDosis = TRIM(LOWER(REPLACE(_dosisMedicamento, '[0-9]+', '')));

    -- Buscar si ya existe una combinación con el mismo tipo, presentación y unidad de dosis
    SELECT idCombinacion INTO _idCombinacion
    FROM CombinacionesMedicamentos
    WHERE idTipo = _idTipo
      AND idPresentacion = _idPresentacion
      AND LOWER(REPLACE(dosis, '[0-9]+', '')) = _unidadDosis
    LIMIT 1;

    IF _idCombinacion IS NOT NULL THEN
        -- Si la combinación ya existe, confirma la transacción y devuelve el ID de combinación existente
        COMMIT;
        SELECT 'Combinación válida, reutilizada' AS mensaje, _idCombinacion AS idCombinacion;
    ELSE
        -- Insertar una nueva combinación si no existe
        INSERT INTO CombinacionesMedicamentos (idTipo, idPresentacion, dosis)
        VALUES (_idTipo, _idPresentacion, _dosisMedicamento);

        SET _idCombinacion = LAST_INSERT_ID();

        COMMIT;

        -- Devolver el ID de la nueva combinación registrada
        SELECT 'Nueva combinación registrada y válida' AS mensaje, _idCombinacion AS idCombinacion;
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alimentos`
--

CREATE TABLE `alimentos` (
  `idAlimento` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `nombreAlimento` varchar(100) NOT NULL,
  `tipoAlimento` varchar(50) DEFAULT NULL,
  `stockActual` decimal(10,2) NOT NULL,
  `stockMinimo` decimal(10,2) DEFAULT 0.00,
  `estado` enum('Disponible','Por agotarse','Agotado') DEFAULT 'Disponible',
  `unidadMedida` varchar(10) NOT NULL,
  `costo` decimal(10,2) NOT NULL,
  `idLote` int(11) NOT NULL,
  `idTipoEquino` int(11) DEFAULT NULL,
  `merma` decimal(10,2) DEFAULT NULL,
  `compra` decimal(10,2) NOT NULL,
  `fechaMovimiento` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistenciapersonal`
--

CREATE TABLE `asistenciapersonal` (
  `idAsistencia` int(11) NOT NULL,
  `idPersonal` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `horaEntrada` time NOT NULL,
  `horaSalida` time NOT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bostas`
--

CREATE TABLE `bostas` (
  `idbosta` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `cantidadsacos` int(11) NOT NULL,
  `pesoaprox` decimal(4,2) NOT NULL,
  `peso_diario` decimal(7,2) DEFAULT NULL,
  `peso_semanal` decimal(9,2) DEFAULT NULL,
  `peso_mensual` decimal(12,2) DEFAULT NULL,
  `numero_semana` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campanapotrillos`
--

CREATE TABLE `campanapotrillos` (
  `idCampana` int(11) NOT NULL,
  `idPotrillo` int(11) NOT NULL,
  `registroPrecio` decimal(10,2) NOT NULL,
  `precioSubasta` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campos`
--

CREATE TABLE `campos` (
  `idCampo` int(11) NOT NULL,
  `numeroCampo` int(11) NOT NULL,
  `tamanoCampo` decimal(10,2) NOT NULL,
  `idTipoSuelo` int(11) NOT NULL,
  `estado` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `campos`
--

INSERT INTO `campos` (`idCampo`, `numeroCampo`, `tamanoCampo`, `idTipoSuelo`, `estado`) VALUES
(1, 1, 12.50, 1, 'Activo'),
(2, 1, 12.50, 1, 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `combinacionesmedicamentos`
--

CREATE TABLE `combinacionesmedicamentos` (
  `idCombinacion` int(11) NOT NULL,
  `idTipo` int(11) NOT NULL,
  `idPresentacion` int(11) NOT NULL,
  `dosis` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `combinacionesmedicamentos`
--

INSERT INTO `combinacionesmedicamentos` (`idCombinacion`, `idTipo`, `idPresentacion`, `dosis`) VALUES
(1, 1, 1, '500 mg'),
(41, 1, 2, '1 ng/mL/h'),
(4, 1, 4, '1 g'),
(46, 1, 4, '1 pmol/L'),
(20, 1, 4, '5 g/L'),
(34, 1, 4, '5 mOsm/kg'),
(28, 1, 6, '100 mg'),
(16, 1, 15, '250 mg'),
(47, 2, 1, '100 UI/L'),
(5, 2, 1, '50 mg'),
(2, 2, 2, '10 ml'),
(29, 2, 5, '50 mg/dL'),
(21, 2, 7, '50 mcg'),
(51, 2, 8, '500mg'),
(38, 2, 10, '10 ng/dL'),
(31, 3, 1, '10 mm Hg'),
(22, 3, 2, '1 mcg/dL'),
(3, 3, 3, '200 mg'),
(42, 3, 4, '5 nmol'),
(6, 3, 5, '5 mg/ml'),
(27, 3, 10, '5 mEq/L'),
(45, 3, 10, '5 pg/mL'),
(15, 3, 14, '15 mg/ml'),
(35, 3, 14, '20 mUI/L'),
(50, 3, 15, '200 U/mL'),
(17, 3, 16, '100 mcg'),
(7, 4, 6, '300 mg'),
(23, 5, 1, '10 mL'),
(39, 5, 1, '5 ng/L'),
(48, 5, 5, '50 UI/mL'),
(8, 5, 7, '100 g'),
(14, 5, 13, '50 mg'),
(30, 5, 15, '5 mm'),
(18, 6, 1, '10 fL'),
(40, 6, 5, '200 ng/mL'),
(9, 6, 8, '1 ml'),
(32, 6, 8, '1 mmol'),
(49, 6, 8, '10 U/L'),
(10, 7, 9, '0.5 ml'),
(11, 8, 10, '20 mg/ml'),
(24, 8, 14, '200 mcl'),
(44, 8, 15, '20 pg'),
(37, 8, 16, '100 mU/L'),
(26, 9, 1, '15 mEq'),
(12, 9, 11, '5 mg'),
(13, 10, 12, '1 g'),
(33, 11, 2, '250 mmol/L'),
(19, 11, 4, '200 g/dL'),
(43, 12, 1, '10 nmol/L'),
(25, 12, 4, '300 mcmol/L'),
(36, 12, 11, '50 mU/g');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detallemedicamentos`
--

CREATE TABLE `detallemedicamentos` (
  `idDetalleMed` int(11) NOT NULL,
  `idMedicamento` int(11) NOT NULL,
  `idEquino` int(11) NOT NULL,
  `dosis` varchar(50) NOT NULL,
  `frecuenciaAdministracion` varchar(50) NOT NULL,
  `viaAdministracion` varchar(50) NOT NULL,
  `pesoEquino` decimal(10,2) DEFAULT NULL,
  `fechaInicio` date NOT NULL,
  `fechaFin` date NOT NULL,
  `observaciones` text DEFAULT NULL,
  `reaccionesAdversas` text DEFAULT NULL,
  `idUsuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entrenamientos`
--

CREATE TABLE `entrenamientos` (
  `idEntrenamiento` int(11) NOT NULL,
  `idEquino` int(11) NOT NULL,
  `fecha` datetime DEFAULT NULL,
  `tipoEntrenamiento` varchar(100) NOT NULL,
  `duracion` decimal(5,2) NOT NULL,
  `intensidad` enum('baja','media','alta') NOT NULL,
  `comentarios` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equinos`
--

CREATE TABLE `equinos` (
  `idEquino` int(11) NOT NULL,
  `nombreEquino` varchar(100) NOT NULL,
  `fechaNacimiento` date DEFAULT NULL,
  `sexo` enum('Macho','Hembra') NOT NULL,
  `idTipoEquino` int(11) NOT NULL,
  `detalles` text DEFAULT NULL,
  `idEstadoMonta` int(11) DEFAULT NULL,
  `nacionalidad` varchar(50) DEFAULT NULL,
  `idPropietario` int(11) DEFAULT NULL,
  `fotografia` longblob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `equinos`
--

INSERT INTO `equinos` (`idEquino`, `nombreEquino`, `fechaNacimiento`, `sexo`, `idTipoEquino`, `detalles`, `idEstadoMonta`, `nacionalidad`, `idPropietario`, `fotografia`) VALUES
(1, 'Rayo Veloz', '2024-10-30', 'Macho', 5, NULL, NULL, 'Peruana', NULL, NULL),
(2, 'Southdale', '2000-01-01', 'Macho', 2, NULL, NULL, 'Argentina', NULL, NULL),
(3, 'Q\'Orianka', '2000-01-01', 'Hembra', 1, NULL, NULL, 'Estadounidense', NULL, NULL),
(4, 'Caleta', NULL, 'Hembra', 1, NULL, NULL, NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estadomonta`
--

CREATE TABLE `estadomonta` (
  `idEstado` int(11) NOT NULL,
  `genero` enum('Macho','Hembra') NOT NULL,
  `nombreEstado` enum('S/S','Servida','Por Servir','Preñada','Vacia','Activo','Inactivo') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estadomonta`
--

INSERT INTO `estadomonta` (`idEstado`, `genero`, `nombreEstado`) VALUES
(1, 'Macho', 'Activo'),
(2, 'Macho', 'Inactivo'),
(3, 'Hembra', 'Preñada'),
(4, 'Hembra', 'Servida'),
(5, 'Hembra', 'S/S'),
(6, 'Hembra', 'Por Servir'),
(7, 'Hembra', 'Vacia');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historialherrero`
--

CREATE TABLE `historialherrero` (
  `idHistorialHerrero` int(11) NOT NULL,
  `idEquino` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `trabajoRealizado` text NOT NULL,
  `herramientasUsadas` text DEFAULT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historialmedico`
--

CREATE TABLE `historialmedico` (
  `idHistorial` int(11) NOT NULL,
  `idEquino` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `diagnostico` text NOT NULL,
  `tratamiento` text NOT NULL,
  `observaciones` text NOT NULL,
  `recomendaciones` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historialmovimientos`
--

CREATE TABLE `historialmovimientos` (
  `idMovimiento` int(11) NOT NULL,
  `idAlimento` int(11) NOT NULL,
  `tipoMovimiento` varchar(50) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `idTipoEquino` int(11) DEFAULT NULL,
  `idUsuario` int(11) NOT NULL,
  `unidadMedida` varchar(50) NOT NULL,
  `fechaMovimiento` date DEFAULT current_timestamp(),
  `merma` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historialmovimientosmedicamentos`
--

CREATE TABLE `historialmovimientosmedicamentos` (
  `idMovimiento` int(11) NOT NULL,
  `idMedicamento` int(11) NOT NULL,
  `tipoMovimiento` varchar(50) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `idTipoEquino` int(11) DEFAULT NULL,
  `idUsuario` int(11) NOT NULL,
  `fechaMovimiento` date DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `implementos`
--

CREATE TABLE `implementos` (
  `idInventario` int(11) NOT NULL,
  `idTipoinventario` int(11) NOT NULL,
  `nombreProducto` varchar(100) NOT NULL,
  `descripcion` text NOT NULL,
  `precioUnitario` decimal(10,2) NOT NULL,
  `idTipomovimiento` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `stockFinal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lotesalimento`
--

CREATE TABLE `lotesalimento` (
  `idLote` int(11) NOT NULL,
  `lote` varchar(50) NOT NULL,
  `unidadMedida` varchar(10) NOT NULL,
  `fechaCaducidad` date DEFAULT NULL,
  `fechaIngreso` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lotesmedicamento`
--

CREATE TABLE `lotesmedicamento` (
  `idLoteMedicamento` int(11) NOT NULL,
  `lote` varchar(100) NOT NULL,
  `fechaCaducidad` date NOT NULL,
  `fechaIngreso` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `lotesmedicamento`
--

INSERT INTO `lotesmedicamento` (`idLoteMedicamento`, `lote`, `fechaCaducidad`, `fechaIngreso`) VALUES
(1, '3010', '0000-00-00', '2024-10-30 15:29:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medicamentos`
--

CREATE TABLE `medicamentos` (
  `idMedicamento` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `nombreMedicamento` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `idCombinacion` int(11) NOT NULL,
  `cantidad_stock` int(11) NOT NULL,
  `stockMinimo` int(11) DEFAULT 0,
  `estado` enum('Disponible','Por agotarse','Agotado') DEFAULT 'Disponible',
  `idTipoEquino` int(11) DEFAULT NULL,
  `idLoteMedicamento` int(11) NOT NULL,
  `precioUnitario` decimal(10,2) NOT NULL,
  `fecha_registro` date NOT NULL,
  `ultima_modificacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `medicamentos`
--

INSERT INTO `medicamentos` (`idMedicamento`, `idUsuario`, `nombreMedicamento`, `descripcion`, `idCombinacion`, `cantidad_stock`, `stockMinimo`, `estado`, `idTipoEquino`, `idLoteMedicamento`, `precioUnitario`, `fecha_registro`, `ultima_modificacion`) VALUES
(1, 3, 'Ibuprofeno', '', 51, 20, 5, 'Disponible', NULL, 1, 10.00, '2024-10-30', '2024-10-30 20:29:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos`
--

CREATE TABLE `modulos` (
  `idmodulo` int(11) NOT NULL,
  `modulo` varchar(30) NOT NULL,
  `create_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `modulos`
--

INSERT INTO `modulos` (`idmodulo`, `modulo`, `create_at`) VALUES
(1, 'campos', '2024-10-30 15:03:55'),
(2, 'equinos', '2024-10-30 15:03:55'),
(3, 'historialMedico', '2024-10-30 15:03:55'),
(4, 'inventarios', '2024-10-30 15:03:55'),
(5, 'servicios', '2024-10-30 15:03:55'),
(6, 'usuarios', '2024-10-30 15:03:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `idpermiso` int(11) NOT NULL,
  `idRol` int(11) NOT NULL,
  `idvista` int(11) NOT NULL,
  `create_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`idpermiso`, `idRol`, `idvista`, `create_at`) VALUES
(1, 1, 1, '2024-10-30 15:03:55'),
(2, 1, 4, '2024-10-30 15:03:55'),
(3, 1, 13, '2024-10-30 15:03:55'),
(4, 2, 1, '2024-10-30 15:03:55'),
(5, 2, 4, '2024-10-30 15:03:55'),
(6, 2, 13, '2024-10-30 15:03:55'),
(7, 3, 1, '2024-10-30 15:03:55'),
(8, 3, 4, '2024-10-30 15:03:55'),
(9, 3, 5, '2024-10-30 15:03:55'),
(10, 3, 8, '2024-10-30 15:03:55'),
(11, 3, 9, '2024-10-30 15:03:55'),
(12, 3, 10, '2024-10-30 15:03:55'),
(13, 3, 11, '2024-10-30 15:03:55'),
(14, 3, 12, '2024-10-30 15:03:55'),
(15, 3, 13, '2024-10-30 15:03:55'),
(16, 3, 14, '2024-10-30 15:03:55'),
(17, 3, 15, '2024-10-30 15:03:55'),
(18, 4, 1, '2024-10-30 15:03:55'),
(19, 4, 2, '2024-10-30 15:03:55'),
(20, 4, 3, '2024-10-30 15:03:55'),
(21, 4, 6, '2024-10-30 15:03:55'),
(22, 4, 7, '2024-10-30 15:03:55'),
(23, 5, 1, '2024-10-30 15:03:55'),
(24, 5, 8, '2024-10-30 15:03:55'),
(25, 6, 1, '2024-10-30 15:03:55'),
(26, 6, 4, '2024-10-30 15:03:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal`
--

CREATE TABLE `personal` (
  `idPersonal` int(11) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `tipodoc` varchar(20) NOT NULL,
  `nrodocumento` varchar(50) NOT NULL,
  `numeroHijos` int(11) NOT NULL,
  `fechaIngreso` date NOT NULL,
  `fechaSalida` date DEFAULT NULL,
  `tipoContrato` enum('Parcial','Completo','Por Prácticas','Otro') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `personal`
--

INSERT INTO `personal` (`idPersonal`, `nombres`, `apellidos`, `direccion`, `tipodoc`, `nrodocumento`, `numeroHijos`, `fechaIngreso`, `fechaSalida`, `tipoContrato`) VALUES
(1, 'Gerente', 'Mateo', 'San Agustin ', 'DNI', '11111111', 1, '2024-08-27', NULL, 'Completo'),
(2, 'Administrador', 'Marcos', 'Calle Fatima', 'DNI', '22222222', 2, '2024-08-27', NULL, 'Completo'),
(3, 'SupervisorE', 'Gereda', 'AV. Los Angeles', 'DNI', '33333333', 3, '2024-08-27', NULL, 'Completo'),
(4, 'SupervisorC', 'Mamani', 'Calle Fatima', 'DNI', '44444444', 0, '2024-08-27', NULL, 'Completo'),
(5, 'Medico', 'Paullac', 'Calle Fatima', 'DNI', '55555555', 0, '2024-08-27', NULL, 'Completo'),
(6, 'Herrero', 'Nuñez', 'Calle Fatima', 'DNI', '66666666', 2, '2024-08-27', NULL, 'Parcial');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presentacionesmedicamentos`
--

CREATE TABLE `presentacionesmedicamentos` (
  `idPresentacion` int(11) NOT NULL,
  `presentacion` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `presentacionesmedicamentos`
--

INSERT INTO `presentacionesmedicamentos` (`idPresentacion`, `presentacion`) VALUES
(16, 'aerosoles'),
(8, 'ampollas'),
(3, 'cápsulas'),
(9, 'colirios'),
(12, 'comprimidos'),
(13, 'enemas'),
(10, 'gotas nasales'),
(14, 'goteros'),
(6, 'grageas'),
(4, 'inyectable'),
(2, 'jarabes'),
(11, 'píldoras'),
(15, 'polvos medicinales'),
(7, 'pomadas'),
(17, 'spray'),
(5, 'suspensión'),
(1, 'tabletas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `propietarios`
--

CREATE TABLE `propietarios` (
  `idPropietario` int(11) NOT NULL,
  `nombreHaras` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `propietarios`
--

INSERT INTO `propietarios` (`idPropietario`, `nombreHaras`) VALUES
(1, 'Los Eucaliptos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `idRol` int(11) NOT NULL,
  `nombreRol` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`idRol`, `nombreRol`) VALUES
(1, 'Gerente'),
(2, 'Administrador'),
(3, 'Supervisor Equino'),
(4, 'Supervisor Campo'),
(5, 'Médico'),
(6, 'Herrero');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rotacioncampos`
--

CREATE TABLE `rotacioncampos` (
  `idRotacion` int(11) NOT NULL,
  `idCampo` int(11) NOT NULL,
  `idTipoRotacion` int(11) NOT NULL,
  `fechaRotacion` datetime DEFAULT NULL,
  `estadoRotacion` varchar(50) NOT NULL,
  `detalleRotacion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rotacioncampos`
--

INSERT INTO `rotacioncampos` (`idRotacion`, `idCampo`, `idTipoRotacion`, `fechaRotacion`, `estadoRotacion`, `detalleRotacion`) VALUES
(1, 1, 2, '2024-10-21 00:00:00', '', 'Deshierve del campo número 1'),
(2, 1, 2, '2024-10-21 00:00:00', '', 'Deshierve del campo número 1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `idServicio` int(11) NOT NULL,
  `idEquinoMacho` int(11) NOT NULL,
  `idEquinoHembra` int(11) NOT NULL,
  `fechaServicio` date NOT NULL,
  `tipoServicio` enum('Propio','Mixto') NOT NULL,
  `detalles` text NOT NULL,
  `idMedicamento` int(11) DEFAULT NULL,
  `horaEntrada` timestamp NULL DEFAULT NULL,
  `horaSalida` time DEFAULT NULL,
  `idPropietario` int(11) DEFAULT NULL,
  `costoServicio` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`idServicio`, `idEquinoMacho`, `idEquinoHembra`, `fechaServicio`, `tipoServicio`, `detalles`, `idMedicamento`, `horaEntrada`, `horaSalida`, `idPropietario`, `costoServicio`) VALUES
(1, 2, 3, '2024-10-30', 'Propio', '', NULL, NULL, NULL, NULL, NULL),
(2, 2, 4, '2024-10-30', 'Mixto', '', NULL, '2024-10-30 20:20:00', '15:21:00', 1, 3000.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipoequinos`
--

CREATE TABLE `tipoequinos` (
  `idTipoEquino` int(11) NOT NULL,
  `tipoEquino` enum('Yegua','Padrillo','Potranca','Potrillo','Recién nacido','Destete') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipoequinos`
--

INSERT INTO `tipoequinos` (`idTipoEquino`, `tipoEquino`) VALUES
(1, 'Yegua'),
(2, 'Padrillo'),
(3, 'Potranca'),
(4, 'Potrillo'),
(5, 'Recién nacido');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipoinventarios`
--

CREATE TABLE `tipoinventarios` (
  `idTipoinventario` int(11) NOT NULL,
  `nombreInventario` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipoinventarios`
--

INSERT INTO `tipoinventarios` (`idTipoinventario`, `nombreInventario`) VALUES
(1, 'Implementos Equinos'),
(2, 'Implementos Campos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipomovimientos`
--

CREATE TABLE `tipomovimientos` (
  `idTipomovimiento` int(11) NOT NULL,
  `movimiento` enum('Entrada','Salida') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipomovimientos`
--

INSERT INTO `tipomovimientos` (`idTipomovimiento`, `movimiento`) VALUES
(1, 'Entrada'),
(2, 'Salida');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiporotaciones`
--

CREATE TABLE `tiporotaciones` (
  `idTipoRotacion` int(11) NOT NULL,
  `nombreRotacion` varchar(100) NOT NULL,
  `detalles` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tiporotaciones`
--

INSERT INTO `tiporotaciones` (`idTipoRotacion`, `nombreRotacion`, `detalles`) VALUES
(1, 'Riego', NULL),
(2, 'Deshierve', NULL),
(3, 'Arado', NULL),
(4, 'Gradeado', NULL),
(5, 'Rufiado', NULL),
(6, 'Potrillo', NULL),
(7, 'Potranca', NULL),
(8, 'Yeguas Preñadas', NULL),
(9, 'Yeguas con Crías', NULL),
(10, 'Yeguas Vacías', NULL),
(11, 'Destetados', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiposmedicamentos`
--

CREATE TABLE `tiposmedicamentos` (
  `idTipo` int(11) NOT NULL,
  `tipo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tiposmedicamentos`
--

INSERT INTO `tiposmedicamentos` (`idTipo`, `tipo`) VALUES
(2, 'Analgésico'),
(1, 'Antibiótico'),
(8, 'Antifúngico'),
(3, 'Antiinflamatorio'),
(11, 'Antiparasitario'),
(7, 'Broncodilatador'),
(5, 'Desparasitante'),
(4, 'Gastroprotector'),
(9, 'Sedante'),
(6, 'Suplemento'),
(10, 'Vacuna'),
(12, 'Vitaminas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiposuelo`
--

CREATE TABLE `tiposuelo` (
  `idTipoSuelo` int(11) NOT NULL,
  `nombreTipoSuelo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tiposuelo`
--

INSERT INTO `tiposuelo` (`idTipoSuelo`, `nombreTipoSuelo`) VALUES
(1, 'Arcilloso'),
(2, 'Arenoso'),
(3, 'Calizo'),
(4, 'Humiferos'),
(5, 'Mixto'),
(6, 'Pedregoso'),
(7, 'Salino'),
(8, 'Urbano');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `idUsuario` int(11) NOT NULL,
  `idPersonal` int(11) NOT NULL,
  `correo` varchar(50) NOT NULL,
  `clave` varchar(100) NOT NULL,
  `idRol` int(11) DEFAULT NULL,
  `inactive_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`idUsuario`, `idPersonal`, `correo`, `clave`, `idRol`, `inactive_at`) VALUES
(1, 1, 'gerente', '$2y$10$RaoPTBz9oVETRVocodEaWuwxQPjshzARRmDnGZcWcDY43YxNF/sIa', 1, NULL),
(2, 2, 'admin', '$2y$10$RaoPTBz9oVETRVocodEaWuwxQPjshzARRmDnGZcWcDY43YxNF/sIa', 2, NULL),
(3, 3, 'superE', '$2y$10$RaoPTBz9oVETRVocodEaWuwxQPjshzARRmDnGZcWcDY43YxNF/sIa', 3, NULL),
(4, 4, 'superC', '$2y$10$RaoPTBz9oVETRVocodEaWuwxQPjshzARRmDnGZcWcDY43YxNF/sIa', 4, NULL),
(5, 5, 'medico', '$2y$10$RaoPTBz9oVETRVocodEaWuwxQPjshzARRmDnGZcWcDY43YxNF/sIa', 5, NULL),
(6, 6, 'herrero', '$2y$10$RaoPTBz9oVETRVocodEaWuwxQPjshzARRmDnGZcWcDY43YxNF/sIa', 6, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vistas`
--

CREATE TABLE `vistas` (
  `idvista` int(11) NOT NULL,
  `idmodulo` int(11) DEFAULT NULL,
  `ruta` varchar(50) NOT NULL,
  `sidebaroption` char(1) NOT NULL,
  `texto` varchar(20) DEFAULT NULL,
  `icono` varchar(20) DEFAULT NULL
) ;

--
-- Volcado de datos para la tabla `vistas`
--

INSERT INTO `vistas` (`idvista`, `idmodulo`, `ruta`, `sidebaroption`, `texto`, `icono`) VALUES
(1, NULL, 'home', 'S', 'Inicio', 'fas fa-home'),
(2, 1, 'rotar-campo', 'S', 'Campos', 'fa-solid fa-group-ar'),
(3, 1, 'programar-rotacion', 'S', 'Rotacion Campos', 'fa-solid fa-calendar'),
(4, 2, 'listar-equino', 'S', 'Listado Equinos', 'fa-solid fa-list'),
(5, 2, 'registrar-equino', 'S', 'Registro Equinos', 'fa-solid fa-horse'),
(6, 2, 'registrar-bostas', 'S', 'Registro Bostas', 'fas fa-poop'),
(7, 2, 'listar-bostas', 'S', 'Listado Bostas', 'fa-solid fa-list'),
(8, 3, 'diagnosticar-equino', 'S', 'Diagnóstico', 'fa-solid fa-file-wav'),
(9, 4, 'administrar-alimento', 'S', 'Alimentos', 'fas fa-apple-alt'),
(10, 4, 'administrar-medicamento', 'S', 'Medicamentos', 'fas fa-pills'),
(11, 5, 'servir-propio', 'S', 'Servicio Propio', 'fas fa-tools'),
(12, 5, 'servir-mixto', 'S', 'Servicio Mixto', 'fas fa-exchange-alt'),
(13, 5, 'listar-servicio', 'S', 'Listado Servicios', 'fa-solid fa-list'),
(14, 6, 'registrar-personal', 'S', 'Registrar Personal', 'fa-solid fa-wallet'),
(15, 6, 'registrar-usuario', 'N', NULL, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alimentos`
--
ALTER TABLE `alimentos`
  ADD PRIMARY KEY (`idAlimento`),
  ADD KEY `fk_alimento_usuario` (`idUsuario`),
  ADD KEY `fk_alimento_lote` (`idLote`),
  ADD KEY `fk_alimento_tipoequino` (`idTipoEquino`);

--
-- Indices de la tabla `asistenciapersonal`
--
ALTER TABLE `asistenciapersonal`
  ADD PRIMARY KEY (`idAsistencia`),
  ADD KEY `fk_asistencia_personal` (`idPersonal`);

--
-- Indices de la tabla `bostas`
--
ALTER TABLE `bostas`
  ADD PRIMARY KEY (`idbosta`),
  ADD UNIQUE KEY `fecha` (`fecha`);

--
-- Indices de la tabla `campanapotrillos`
--
ALTER TABLE `campanapotrillos`
  ADD PRIMARY KEY (`idCampana`),
  ADD KEY `fk_campanapotrillos_equino` (`idPotrillo`);

--
-- Indices de la tabla `campos`
--
ALTER TABLE `campos`
  ADD PRIMARY KEY (`idCampo`);

--
-- Indices de la tabla `combinacionesmedicamentos`
--
ALTER TABLE `combinacionesmedicamentos`
  ADD PRIMARY KEY (`idCombinacion`),
  ADD UNIQUE KEY `idTipo` (`idTipo`,`idPresentacion`,`dosis`),
  ADD KEY `idPresentacion` (`idPresentacion`);

--
-- Indices de la tabla `detallemedicamentos`
--
ALTER TABLE `detallemedicamentos`
  ADD PRIMARY KEY (`idDetalleMed`),
  ADD KEY `fk_detallemed_medicamento` (`idMedicamento`),
  ADD KEY `fk_detallemed_equino` (`idEquino`),
  ADD KEY `fk_detallemed_usuario` (`idUsuario`);

--
-- Indices de la tabla `entrenamientos`
--
ALTER TABLE `entrenamientos`
  ADD PRIMARY KEY (`idEntrenamiento`),
  ADD KEY `fk_entrenamiento_equino` (`idEquino`);

--
-- Indices de la tabla `equinos`
--
ALTER TABLE `equinos`
  ADD PRIMARY KEY (`idEquino`),
  ADD KEY `fk_equino_tipoequino` (`idTipoEquino`),
  ADD KEY `fk_equino_propietario` (`idPropietario`),
  ADD KEY `fk_equino_estado_monta` (`idEstadoMonta`);

--
-- Indices de la tabla `estadomonta`
--
ALTER TABLE `estadomonta`
  ADD PRIMARY KEY (`idEstado`);

--
-- Indices de la tabla `historialherrero`
--
ALTER TABLE `historialherrero`
  ADD PRIMARY KEY (`idHistorialHerrero`),
  ADD KEY `fk_historialherrero_equino` (`idEquino`),
  ADD KEY `fk_historialherrero_usuario` (`idUsuario`);

--
-- Indices de la tabla `historialmedico`
--
ALTER TABLE `historialmedico`
  ADD PRIMARY KEY (`idHistorial`),
  ADD KEY `fk_historial_equino` (`idEquino`),
  ADD KEY `fk_historial_usuario` (`idUsuario`);

--
-- Indices de la tabla `historialmovimientos`
--
ALTER TABLE `historialmovimientos`
  ADD PRIMARY KEY (`idMovimiento`),
  ADD KEY `idAlimento` (`idAlimento`),
  ADD KEY `idTipoEquino` (`idTipoEquino`),
  ADD KEY `idUsuario` (`idUsuario`);

--
-- Indices de la tabla `historialmovimientosmedicamentos`
--
ALTER TABLE `historialmovimientosmedicamentos`
  ADD PRIMARY KEY (`idMovimiento`),
  ADD KEY `idMedicamento` (`idMedicamento`),
  ADD KEY `idTipoEquino` (`idTipoEquino`),
  ADD KEY `idUsuario` (`idUsuario`);

--
-- Indices de la tabla `implementos`
--
ALTER TABLE `implementos`
  ADD PRIMARY KEY (`idInventario`),
  ADD KEY `fk_implemento_inventario` (`idTipoinventario`),
  ADD KEY `fk_implemento_movimiento` (`idTipomovimiento`);

--
-- Indices de la tabla `lotesalimento`
--
ALTER TABLE `lotesalimento`
  ADD PRIMARY KEY (`idLote`),
  ADD UNIQUE KEY `UQ_lote_unidad` (`lote`,`unidadMedida`);

--
-- Indices de la tabla `lotesmedicamento`
--
ALTER TABLE `lotesmedicamento`
  ADD PRIMARY KEY (`idLoteMedicamento`),
  ADD UNIQUE KEY `UQ_lote_medicamento` (`lote`);

--
-- Indices de la tabla `medicamentos`
--
ALTER TABLE `medicamentos`
  ADD PRIMARY KEY (`idMedicamento`),
  ADD KEY `fk_medicamento_usuario` (`idUsuario`),
  ADD KEY `fk_medicamento_combinacion` (`idCombinacion`),
  ADD KEY `fk_medicamento_lote` (`idLoteMedicamento`),
  ADD KEY `fk_medicamento_tipoequino` (`idTipoEquino`);

--
-- Indices de la tabla `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`idmodulo`),
  ADD UNIQUE KEY `uk_modulo_mod` (`modulo`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`idpermiso`),
  ADD UNIQUE KEY `uk_vista_per` (`idRol`,`idvista`),
  ADD KEY `fk_idvisita_per` (`idvista`);

--
-- Indices de la tabla `personal`
--
ALTER TABLE `personal`
  ADD PRIMARY KEY (`idPersonal`),
  ADD UNIQUE KEY `nrodocumento` (`nrodocumento`);

--
-- Indices de la tabla `presentacionesmedicamentos`
--
ALTER TABLE `presentacionesmedicamentos`
  ADD PRIMARY KEY (`idPresentacion`),
  ADD UNIQUE KEY `presentacion` (`presentacion`);

--
-- Indices de la tabla `propietarios`
--
ALTER TABLE `propietarios`
  ADD PRIMARY KEY (`idPropietario`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`idRol`);

--
-- Indices de la tabla `rotacioncampos`
--
ALTER TABLE `rotacioncampos`
  ADD PRIMARY KEY (`idRotacion`),
  ADD KEY `fk_rotacioncampo_campo` (`idCampo`),
  ADD KEY `fk_rotacioncampo_tiporotacion` (`idTipoRotacion`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`idServicio`),
  ADD KEY `fk_servicio_equino_macho` (`idEquinoMacho`),
  ADD KEY `fk_servicio_equino_hembra` (`idEquinoHembra`),
  ADD KEY `fk_servicio_medicamento` (`idMedicamento`),
  ADD KEY `fk_servicio_propietario` (`idPropietario`);

--
-- Indices de la tabla `tipoequinos`
--
ALTER TABLE `tipoequinos`
  ADD PRIMARY KEY (`idTipoEquino`);

--
-- Indices de la tabla `tipoinventarios`
--
ALTER TABLE `tipoinventarios`
  ADD PRIMARY KEY (`idTipoinventario`);

--
-- Indices de la tabla `tipomovimientos`
--
ALTER TABLE `tipomovimientos`
  ADD PRIMARY KEY (`idTipomovimiento`);

--
-- Indices de la tabla `tiporotaciones`
--
ALTER TABLE `tiporotaciones`
  ADD PRIMARY KEY (`idTipoRotacion`);

--
-- Indices de la tabla `tiposmedicamentos`
--
ALTER TABLE `tiposmedicamentos`
  ADD PRIMARY KEY (`idTipo`),
  ADD UNIQUE KEY `tipo` (`tipo`);

--
-- Indices de la tabla `tiposuelo`
--
ALTER TABLE `tiposuelo`
  ADD PRIMARY KEY (`idTipoSuelo`),
  ADD UNIQUE KEY `nombreTipoSuelo` (`nombreTipoSuelo`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`idUsuario`),
  ADD UNIQUE KEY `uk_correo` (`correo`),
  ADD KEY `fk_usuario_personal` (`idPersonal`),
  ADD KEY `fk_usuario_rol` (`idRol`);

--
-- Indices de la tabla `vistas`
--
ALTER TABLE `vistas`
  ADD PRIMARY KEY (`idvista`),
  ADD UNIQUE KEY `uk_ruta_vis` (`ruta`),
  ADD KEY `fk_idmodulo_vis` (`idmodulo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alimentos`
--
ALTER TABLE `alimentos`
  MODIFY `idAlimento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `asistenciapersonal`
--
ALTER TABLE `asistenciapersonal`
  MODIFY `idAsistencia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `bostas`
--
ALTER TABLE `bostas`
  MODIFY `idbosta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `campanapotrillos`
--
ALTER TABLE `campanapotrillos`
  MODIFY `idCampana` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `campos`
--
ALTER TABLE `campos`
  MODIFY `idCampo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `combinacionesmedicamentos`
--
ALTER TABLE `combinacionesmedicamentos`
  MODIFY `idCombinacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT de la tabla `detallemedicamentos`
--
ALTER TABLE `detallemedicamentos`
  MODIFY `idDetalleMed` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `entrenamientos`
--
ALTER TABLE `entrenamientos`
  MODIFY `idEntrenamiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `equinos`
--
ALTER TABLE `equinos`
  MODIFY `idEquino` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `estadomonta`
--
ALTER TABLE `estadomonta`
  MODIFY `idEstado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `historialherrero`
--
ALTER TABLE `historialherrero`
  MODIFY `idHistorialHerrero` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historialmedico`
--
ALTER TABLE `historialmedico`
  MODIFY `idHistorial` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historialmovimientos`
--
ALTER TABLE `historialmovimientos`
  MODIFY `idMovimiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historialmovimientosmedicamentos`
--
ALTER TABLE `historialmovimientosmedicamentos`
  MODIFY `idMovimiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `implementos`
--
ALTER TABLE `implementos`
  MODIFY `idInventario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `lotesalimento`
--
ALTER TABLE `lotesalimento`
  MODIFY `idLote` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `lotesmedicamento`
--
ALTER TABLE `lotesmedicamento`
  MODIFY `idLoteMedicamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `medicamentos`
--
ALTER TABLE `medicamentos`
  MODIFY `idMedicamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `idmodulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `idpermiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `personal`
--
ALTER TABLE `personal`
  MODIFY `idPersonal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `presentacionesmedicamentos`
--
ALTER TABLE `presentacionesmedicamentos`
  MODIFY `idPresentacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `propietarios`
--
ALTER TABLE `propietarios`
  MODIFY `idPropietario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `idRol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `rotacioncampos`
--
ALTER TABLE `rotacioncampos`
  MODIFY `idRotacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `idServicio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tipoequinos`
--
ALTER TABLE `tipoequinos`
  MODIFY `idTipoEquino` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tipoinventarios`
--
ALTER TABLE `tipoinventarios`
  MODIFY `idTipoinventario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tipomovimientos`
--
ALTER TABLE `tipomovimientos`
  MODIFY `idTipomovimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tiporotaciones`
--
ALTER TABLE `tiporotaciones`
  MODIFY `idTipoRotacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `tiposmedicamentos`
--
ALTER TABLE `tiposmedicamentos`
  MODIFY `idTipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `tiposuelo`
--
ALTER TABLE `tiposuelo`
  MODIFY `idTipoSuelo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `idUsuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `vistas`
--
ALTER TABLE `vistas`
  MODIFY `idvista` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alimentos`
--
ALTER TABLE `alimentos`
  ADD CONSTRAINT `fk_alimento_lote` FOREIGN KEY (`idLote`) REFERENCES `lotesalimento` (`idLote`),
  ADD CONSTRAINT `fk_alimento_tipoequino` FOREIGN KEY (`idTipoEquino`) REFERENCES `tipoequinos` (`idTipoEquino`),
  ADD CONSTRAINT `fk_alimento_usuario` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`);

--
-- Filtros para la tabla `asistenciapersonal`
--
ALTER TABLE `asistenciapersonal`
  ADD CONSTRAINT `fk_asistencia_personal` FOREIGN KEY (`idPersonal`) REFERENCES `personal` (`idPersonal`);

--
-- Filtros para la tabla `campanapotrillos`
--
ALTER TABLE `campanapotrillos`
  ADD CONSTRAINT `fk_campanapotrillos_equino` FOREIGN KEY (`idPotrillo`) REFERENCES `equinos` (`idEquino`);

--
-- Filtros para la tabla `combinacionesmedicamentos`
--
ALTER TABLE `combinacionesmedicamentos`
  ADD CONSTRAINT `combinacionesmedicamentos_ibfk_1` FOREIGN KEY (`idTipo`) REFERENCES `tiposmedicamentos` (`idTipo`),
  ADD CONSTRAINT `combinacionesmedicamentos_ibfk_2` FOREIGN KEY (`idPresentacion`) REFERENCES `presentacionesmedicamentos` (`idPresentacion`);

--
-- Filtros para la tabla `detallemedicamentos`
--
ALTER TABLE `detallemedicamentos`
  ADD CONSTRAINT `fk_detallemed_equino` FOREIGN KEY (`idEquino`) REFERENCES `equinos` (`idEquino`),
  ADD CONSTRAINT `fk_detallemed_medicamento` FOREIGN KEY (`idMedicamento`) REFERENCES `medicamentos` (`idMedicamento`),
  ADD CONSTRAINT `fk_detallemed_usuario` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`);

--
-- Filtros para la tabla `entrenamientos`
--
ALTER TABLE `entrenamientos`
  ADD CONSTRAINT `fk_entrenamiento_equino` FOREIGN KEY (`idEquino`) REFERENCES `equinos` (`idEquino`);

--
-- Filtros para la tabla `equinos`
--
ALTER TABLE `equinos`
  ADD CONSTRAINT `fk_equino_estado_monta` FOREIGN KEY (`idEstadoMonta`) REFERENCES `estadomonta` (`idEstado`),
  ADD CONSTRAINT `fk_equino_propietario` FOREIGN KEY (`idPropietario`) REFERENCES `propietarios` (`idPropietario`),
  ADD CONSTRAINT `fk_equino_tipoequino` FOREIGN KEY (`idTipoEquino`) REFERENCES `tipoequinos` (`idTipoEquino`);

--
-- Filtros para la tabla `historialherrero`
--
ALTER TABLE `historialherrero`
  ADD CONSTRAINT `fk_historialherrero_equino` FOREIGN KEY (`idEquino`) REFERENCES `equinos` (`idEquino`),
  ADD CONSTRAINT `fk_historialherrero_usuario` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`);

--
-- Filtros para la tabla `historialmedico`
--
ALTER TABLE `historialmedico`
  ADD CONSTRAINT `fk_historial_equino` FOREIGN KEY (`idEquino`) REFERENCES `equinos` (`idEquino`),
  ADD CONSTRAINT `fk_historial_usuario` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`);

--
-- Filtros para la tabla `historialmovimientos`
--
ALTER TABLE `historialmovimientos`
  ADD CONSTRAINT `historialmovimientos_ibfk_1` FOREIGN KEY (`idAlimento`) REFERENCES `alimentos` (`idAlimento`),
  ADD CONSTRAINT `historialmovimientos_ibfk_2` FOREIGN KEY (`idTipoEquino`) REFERENCES `tipoequinos` (`idTipoEquino`),
  ADD CONSTRAINT `historialmovimientos_ibfk_3` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`);

--
-- Filtros para la tabla `historialmovimientosmedicamentos`
--
ALTER TABLE `historialmovimientosmedicamentos`
  ADD CONSTRAINT `historialmovimientosmedicamentos_ibfk_1` FOREIGN KEY (`idMedicamento`) REFERENCES `medicamentos` (`idMedicamento`),
  ADD CONSTRAINT `historialmovimientosmedicamentos_ibfk_2` FOREIGN KEY (`idTipoEquino`) REFERENCES `tipoequinos` (`idTipoEquino`),
  ADD CONSTRAINT `historialmovimientosmedicamentos_ibfk_3` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`);

--
-- Filtros para la tabla `implementos`
--
ALTER TABLE `implementos`
  ADD CONSTRAINT `fk_implemento_inventario` FOREIGN KEY (`idTipoinventario`) REFERENCES `tipoinventarios` (`idTipoinventario`),
  ADD CONSTRAINT `fk_implemento_movimiento` FOREIGN KEY (`idTipomovimiento`) REFERENCES `tipomovimientos` (`idTipomovimiento`);

--
-- Filtros para la tabla `medicamentos`
--
ALTER TABLE `medicamentos`
  ADD CONSTRAINT `fk_medicamento_combinacion` FOREIGN KEY (`idCombinacion`) REFERENCES `combinacionesmedicamentos` (`idCombinacion`),
  ADD CONSTRAINT `fk_medicamento_lote` FOREIGN KEY (`idLoteMedicamento`) REFERENCES `lotesmedicamento` (`idLoteMedicamento`),
  ADD CONSTRAINT `fk_medicamento_tipoequino` FOREIGN KEY (`idTipoEquino`) REFERENCES `tipoequinos` (`idTipoEquino`),
  ADD CONSTRAINT `fk_medicamento_usuario` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`);

--
-- Filtros para la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD CONSTRAINT `fk_idRol_per` FOREIGN KEY (`idRol`) REFERENCES `roles` (`idRol`),
  ADD CONSTRAINT `fk_idvisita_per` FOREIGN KEY (`idvista`) REFERENCES `vistas` (`idvista`);

--
-- Filtros para la tabla `rotacioncampos`
--
ALTER TABLE `rotacioncampos`
  ADD CONSTRAINT `fk_rotacioncampo_campo` FOREIGN KEY (`idCampo`) REFERENCES `campos` (`idCampo`),
  ADD CONSTRAINT `fk_rotacioncampo_tiporotacion` FOREIGN KEY (`idTipoRotacion`) REFERENCES `tiporotaciones` (`idTipoRotacion`);

--
-- Filtros para la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD CONSTRAINT `fk_servicio_equino_hembra` FOREIGN KEY (`idEquinoHembra`) REFERENCES `equinos` (`idEquino`),
  ADD CONSTRAINT `fk_servicio_equino_macho` FOREIGN KEY (`idEquinoMacho`) REFERENCES `equinos` (`idEquino`),
  ADD CONSTRAINT `fk_servicio_medicamento` FOREIGN KEY (`idMedicamento`) REFERENCES `medicamentos` (`idMedicamento`),
  ADD CONSTRAINT `fk_servicio_propietario` FOREIGN KEY (`idPropietario`) REFERENCES `propietarios` (`idPropietario`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuario_personal` FOREIGN KEY (`idPersonal`) REFERENCES `personal` (`idPersonal`),
  ADD CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`idRol`) REFERENCES `roles` (`idRol`);

--
-- Filtros para la tabla `vistas`
--
ALTER TABLE `vistas`
  ADD CONSTRAINT `fk_idmodulo_vis` FOREIGN KEY (`idmodulo`) REFERENCES `modulos` (`idmodulo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
