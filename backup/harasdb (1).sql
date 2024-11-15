-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-11-2024 a las 16:03:34
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
CREATE DEFINER=`root`@`localhost` PROCEDURE `ConsultarEstadoActualHerramientas` ()   BEGIN
    SELECT H.idHerramienta, E.descripcionEstado AS estadoActual
    FROM HerramientasUsadasHistorial H
    JOIN EstadoHerramienta E ON H.idHerramienta = E.idEstado
    ORDER BY H.idHistorialHerrero DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ConsultarHistorialEquino` (IN `p_idEquino` INT)   BEGIN
    SELECT 
        H.idHistorialHerrero, 
        H.fecha, 
        H.trabajoRealizado, 
        H.herramientasUsadas, 
        H.observaciones,
        E.nombreEquino,              -- Agrega el nombre del equino
        T.tipoEquino                 -- Agrega el tipo de equino
    FROM 
        HistorialHerrero H
    INNER JOIN 
        Equinos E ON H.idEquino = E.idEquino
    INNER JOIN 
        TipoEquinos T ON E.idTipoEquino = T.idTipoEquino
    WHERE 
        H.idEquino = p_idEquino
    ORDER BY 
        H.fecha DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertarEstadoHerramienta` (IN `p_descripcionEstado` VARCHAR(50))   BEGIN
    INSERT INTO EstadoHerramienta (descripcionEstado)
    VALUES (p_descripcionEstado);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertarHerramientaUsada` (IN `p_idHistorialHerrero` INT, IN `p_idHerramienta` INT)   BEGIN
    INSERT INTO HerramientasUsadasHistorial (
        idHistorialHerrero, idHerramienta
    ) VALUES (
        p_idHistorialHerrero, p_idHerramienta
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertarHistorialHerrero` (IN `p_idEquino` INT, IN `p_idUsuario` INT, IN `p_fecha` DATE, IN `p_trabajoRealizado` TEXT, IN `p_herramientasUsadas` TEXT, IN `p_observaciones` TEXT)   BEGIN
    INSERT INTO HistorialHerrero (
        idEquino, idUsuario, fecha, trabajoRealizado, herramientasUsadas, observaciones
    ) VALUES (
        p_idEquino, p_idUsuario, p_fecha, p_trabajoRealizado, p_herramientasUsadas, p_observaciones
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `listarMedicamentos` ()   BEGIN
    SELECT idMedicamento, nombreMedicamento
    FROM Medicamentos;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ObtenerResumenServicios` ()   BEGIN
    SELECT 
        COUNT(*) AS totalServicios, 
        SUM(CASE WHEN tipoServicio = 'Propio' THEN 1 ELSE 0 END) AS totalServiciosPropios,
        SUM(CASE WHEN tipoServicio = 'Mixto' THEN 1 ELSE 0 END) AS totalServiciosMixtos
    FROM Servicios;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ObtenerResumenStockAlimentos` ()   BEGIN
    SELECT 
        SUM(stockActual) AS stock_total,
        COUNT(*) AS cantidad_alimentos,
        GROUP_CONCAT(CASE WHEN stockActual <= stockMinimo THEN CONCAT(nombreAlimento, ' (', stockActual, ')') ELSE NULL END) AS baja_cantidad,
        GROUP_CONCAT(CASE WHEN stockActual > stockMinimo THEN CONCAT(nombreAlimento, ' (', stockActual, ')') ELSE NULL END) AS en_stock,
        COUNT(CASE WHEN stockActual <= stockMinimo THEN 1 ELSE NULL END) AS baja_cantidad_count,
        COUNT(CASE WHEN stockActual > stockMinimo THEN 1 ELSE NULL END) AS en_stock_count
    FROM 
        Alimentos;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ObtenerResumenStockMedicamentos` ()   BEGIN
    -- Resumen de stock de medicamentos
    SELECT 
        SUM(cantidad_stock) AS stock_total,                              -- Total de cantidad de stock
        COUNT(*) AS cantidad_medicamentos,                                 -- Total de registros de medicamentos
        GROUP_CONCAT(CASE WHEN cantidad_stock <= stockMinimo THEN CONCAT(nombreMedicamento, ' (', cantidad_stock, ')') ELSE NULL END) AS criticos,  -- Lista de medicamentos con stock bajo
        GROUP_CONCAT(CASE WHEN cantidad_stock > stockMinimo THEN CONCAT(nombreMedicamento, ' (', cantidad_stock, ')') ELSE NULL END) AS en_stock, -- Lista de medicamentos en stock suficiente
        COUNT(CASE WHEN cantidad_stock <= stockMinimo THEN 1 ELSE NULL END) AS criticos_count,  -- Contador de medicamentos con stock bajo
        COUNT(CASE WHEN cantidad_stock > stockMinimo THEN 1 ELSE NULL END) AS en_stock_count  -- Contador de medicamentos en stock suficiente
    FROM 
        Medicamentos;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ObtenerServiciosRealizadosMensual` (IN `p_meta` INT)   BEGIN
    -- Contar servicios realizados en el mes actual
    SELECT COUNT(*) AS totalServiciosRealizados,
           ROUND((COUNT(*) / p_meta) * 100, 2) AS porcentajeProgreso
    FROM Servicios
    WHERE MONTH(fechaServicio) = MONTH(CURDATE()) 
      AND YEAR(fechaServicio) = YEAR(CURDATE());
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ObtenerServiciosSemanaActual` ()   BEGIN
    SELECT COUNT(*) AS total_servicios
    FROM Servicios
    WHERE WEEK(fechaServicio) = WEEK(CURDATE()) AND YEAR(fechaServicio) = YEAR(CURDATE());
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ObtenerTotalEquinosRegistrados` ()   BEGIN
    SELECT COUNT(*) AS total_equinos 
    FROM Equinos 
    WHERE estado = 1  -- Estado 1 representa "Vivo" o "Activo"
    AND idPropietario IS NULL;  -- Filtrar solo los equinos sin propietario
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `registrarServicio` (IN `p_idEquinoMacho` INT, IN `p_idEquinoHembra` INT, IN `p_idPropietario` INT, IN `p_idEquinoExterno` INT, IN `p_fechaServicio` DATE, IN `p_tipoServicio` ENUM('propio','mixto'), IN `p_detalles` TEXT, IN `p_idMedicamento` INT, IN `p_horaEntrada` TIME, IN `p_horaSalida` TIME, IN `p_costoServicio` DECIMAL(10,2))   BEGIN
    DECLARE v_mensajeError VARCHAR(255);
    DECLARE v_idEstadoServida INT;
    DECLARE v_idEstadoActivo INT;
    DECLARE v_idEstadoSS INT;
    DECLARE v_idPropietarioEquinoExterno INT;
    DECLARE v_idPropietarioEquinoMacho INT;
    DECLARE v_idPropietarioEquinoHembra INT;
    DECLARE v_sexoEquinoExterno CHAR(1);  -- Se declara la variable para el sexo del equino externo

    -- Obtener los ID de estados correspondientes
    SELECT idEstadoMonta INTO v_idEstadoServida FROM EstadoMonta WHERE genero = 'Hembra' AND nombreEstado = 'Servida' LIMIT 1;
    SELECT idEstadoMonta INTO v_idEstadoActivo FROM EstadoMonta WHERE genero = 'Macho' AND nombreEstado = 'Activo' LIMIT 1;
    SELECT idEstadoMonta INTO v_idEstadoSS FROM EstadoMonta WHERE genero = 'Hembra' AND nombreEstado = 'S/S' LIMIT 1;

    -- Validación para la fecha de servicio
    IF p_fechaServicio > CURDATE() THEN
        SET v_mensajeError = 'Error: La fecha de servicio no puede ser mayor que la fecha actual.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
    END IF;

    -- Si la fecha de servicio es hoy, validar que la hora de entrada y salida no sean mayores que la hora actual
    IF p_fechaServicio = CURDATE() THEN
        IF p_horaEntrada > CURTIME() THEN
            SET v_mensajeError = 'Error: La hora de entrada no puede ser mayor que la hora actual.';
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
        END IF;
        IF p_horaSalida > CURTIME() THEN
            SET v_mensajeError = 'Error: La hora de salida no puede ser mayor que la hora actual.';
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
        END IF;
    END IF;

    -- Verificar que la yegua no haya recibido ningún servicio en el mismo día, tanto propio como mixto
    IF EXISTS (
        SELECT 1
        FROM Servicios
        WHERE idEquinoHembra = p_idEquinoHembra
        AND fechaServicio = p_fechaServicio
    ) THEN
        SET v_mensajeError = 'Error: La yegua ya recibió un servicio (propio o mixto) en esta fecha.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
    END IF;

    -- Si el servicio es mixto, verificar el tipo de equino externo
    IF p_tipoServicio = 'mixto' THEN
        -- Verificar que la hora de entrada no sea mayor que la hora de salida
        IF p_horaEntrada >= p_horaSalida THEN
            SET v_mensajeError = 'Error: La hora de entrada no puede ser mayor o igual que la hora de salida.';
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
        END IF;

        -- Obtener el propietario y sexo del equino externo
        SELECT idPropietario, sexo INTO v_idPropietarioEquinoExterno, v_sexoEquinoExterno
        FROM Equinos WHERE idEquino = p_idEquinoExterno LIMIT 1;

        -- Verificar que el equino externo sea hembra
        IF v_sexoEquinoExterno = 'H' THEN
            -- Si el equino externo es hembra, verificar que no haya un servicio registrado para esa hembra en la misma fecha
            IF EXISTS (
                SELECT 1
                FROM Servicios
                WHERE idEquinoExterno = p_idEquinoExterno
                AND fechaServicio = p_fechaServicio
            ) THEN
                SET v_mensajeError = 'Error: La yegua externa ya tiene un servicio registrado en esta fecha.';
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
            END IF;
        ELSE
            -- Si el equino externo no es hembra, no hay restricciones de servicio por fecha
            -- Verificar que el propietario del equino macho coincida con el propietario del equino externo
            SELECT idPropietario INTO v_idPropietarioEquinoMacho 
            FROM Equinos WHERE idEquino = p_idEquinoMacho LIMIT 1;

            IF v_idPropietarioEquinoExterno != v_idPropietarioEquinoMacho THEN
                SET v_mensajeError = 'Error: El propietario del equino macho debe ser el mismo que el del equino externo.';
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
            END IF;

            -- Verificar que el propietario del equino hembra coincida con el propietario del equino externo
            SELECT idPropietario INTO v_idPropietarioEquinoHembra 
            FROM Equinos WHERE idEquino = p_idEquinoHembra LIMIT 1;

            IF v_idPropietarioEquinoExterno != v_idPropietarioEquinoHembra THEN
                SET v_mensajeError = 'Error: El propietario del equino hembra debe ser el mismo que el del equino externo.';
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
            END IF;
        END IF;
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
            idEquinoMacho, idEquinoHembra, idEquinoExterno, fechaServicio, tipoServicio, detalles, idMedicamento, horaEntrada, horaSalida, idPropietario, costoServicio
        ) VALUES (
            p_idEquinoMacho, p_idEquinoHembra, p_idEquinoExterno, p_fechaServicio, p_tipoServicio, p_detalles, p_idMedicamento, p_horaEntrada, p_horaSalida, p_idPropietario, p_costoServicio
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

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_agregarTipoUnidadMedidaNuevo` (IN `p_tipoAlimento` VARCHAR(50), IN `p_nombreUnidad` VARCHAR(10))   BEGIN
    DECLARE tipoID INT;
    DECLARE unidadID INT;

    -- 1. Verificar si el tipo de alimento existe, si no, agregarlo
    SET tipoID = (SELECT idTipoAlimento FROM TipoAlimentos WHERE tipoAlimento = p_tipoAlimento);
    IF tipoID IS NULL THEN
        INSERT INTO TipoAlimentos (tipoAlimento) VALUES (p_tipoAlimento);
        SET tipoID = LAST_INSERT_ID();  -- Obtener el ID del tipo recién insertado
    END IF;

    -- 2. Verificar si la unidad de medida existe, si no, agregarla
    SET unidadID = (SELECT idUnidadMedida FROM UnidadesMedidaAlimento WHERE nombreUnidad = p_nombreUnidad);
    IF unidadID IS NULL THEN
        INSERT INTO UnidadesMedidaAlimento (nombreUnidad) VALUES (p_nombreUnidad);
        SET unidadID = LAST_INSERT_ID();  -- Obtener el ID de la unidad recién insertada
    END IF;

    -- 3. Verificar si la relación ya existe, si no, agregarla
    IF NOT EXISTS (
        SELECT 1 
        FROM TipoAlimento_UnidadMedida 
        WHERE idTipoAlimento = tipoID AND idUnidadMedida = unidadID
    ) THEN
        INSERT INTO TipoAlimento_UnidadMedida (idTipoAlimento, idUnidadMedida)
        VALUES (tipoID, unidadID);
    ELSE
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La combinación de tipo de alimento y unidad de medida ya existe.';
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_agregar_nueva_combinacion_medicamento` (IN `_tipo` VARCHAR(100), IN `_presentacion` VARCHAR(100), IN `_unidad` VARCHAR(50), IN `_dosis` DECIMAL(10,2), OUT `mensaje` VARCHAR(255))   BEGIN
    DECLARE _idTipo INT;
    DECLARE _idPresentacion INT;
    DECLARE _idUnidad INT;
    -- Paso 1: Verificar si el tipo ya existe en TiposMedicamentos
    SELECT idTipo INTO _idTipo
    FROM TiposMedicamentos
    WHERE LOWER(tipo) = LOWER(_tipo);
    
    -- Si el tipo no existe, lo insertamos
    IF _idTipo IS NULL THEN
        INSERT INTO TiposMedicamentos (tipo) VALUES (_tipo);
        SET _idTipo = LAST_INSERT_ID();
        SET mensaje = CONCAT('Nuevo tipo de medicamento agregado: ', _tipo);
    ELSE
        SET mensaje = CONCAT('Tipo de medicamento ya existente: ', _tipo);
    END IF;
    -- Paso 2: Verificar si la presentación ya existe en PresentacionesMedicamentos
    SELECT idPresentacion INTO _idPresentacion
    FROM PresentacionesMedicamentos
    WHERE LOWER(presentacion) = LOWER(_presentacion);
    
    -- Si la presentación no existe, la insertamos
    IF _idPresentacion IS NULL THEN
        INSERT INTO PresentacionesMedicamentos (presentacion) VALUES (_presentacion);
        SET _idPresentacion = LAST_INSERT_ID();
        SET mensaje = CONCAT(mensaje, '; Nueva presentación agregada: ', _presentacion);
    ELSE
        SET mensaje = CONCAT(mensaje, '; Presentación ya existente: ', _presentacion);
    END IF;
    -- Paso 3: Verificar si la unidad ya existe en UnidadesMedida
    SELECT idUnidad INTO _idUnidad
    FROM UnidadesMedida
    WHERE LOWER(unidad) = LOWER(_unidad);
    
    -- Si la unidad no existe, la insertamos
    IF _idUnidad IS NULL THEN
        INSERT INTO UnidadesMedida (unidad) VALUES (_unidad);
        SET _idUnidad = LAST_INSERT_ID();
        SET mensaje = CONCAT(mensaje, '; Nueva unidad de medida agregada: ', _unidad);
    ELSE
        SET mensaje = CONCAT(mensaje, '; Unidad de medida ya existente: ', _unidad);
    END IF;
    -- Paso 4: Verificar si la combinación ya existe en CombinacionesMedicamentos
    IF EXISTS (
        SELECT 1 FROM CombinacionesMedicamentos 
        WHERE idTipo = _idTipo 
          AND idPresentacion = _idPresentacion 
          AND idUnidad = _idUnidad
          AND dosis = _dosis
    ) THEN
        -- Mensaje de advertencia amigable en lugar de error
        SET mensaje = CONCAT(mensaje, '; La combinación de tipo, presentación, unidad y dosis ya existe.');
    ELSE
        -- Insertar la combinación si no existe
        INSERT INTO CombinacionesMedicamentos (idTipo, idPresentacion, idUnidad, dosis) 
        VALUES (_idTipo, _idPresentacion, _idUnidad, _dosis);
        SET mensaje = 'Combinación agregada exitosamente.';
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_alimentos_entrada` (IN `_idUsuario` INT, IN `_nombreAlimento` VARCHAR(100), IN `_idUnidadMedida` INT, IN `_lote` VARCHAR(50), IN `_cantidad` DECIMAL(10,2))   BEGIN
    DECLARE _idAlimento INT;
    DECLARE _idLote INT;
    DECLARE _currentStock DECIMAL(10,2);

    -- Iniciar transacción
    START TRANSACTION;

    -- Verificar si el ID de la unidad de medida existe
    IF _idUnidadMedida IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La unidad de medida especificada no existe.';
    END IF;

    -- Verificar si el lote existe y obtener su ID
    SELECT idLote INTO _idLote
    FROM LotesAlimento
    WHERE LOWER(lote) = LOWER(_lote)
    LIMIT 1; -- Asegúrate de obtener solo un registro

    IF _idLote IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El lote especificado no existe.';
    END IF;

    -- Buscar el `idAlimento` correspondiente al nombre, lote y unidad de medida
    -- Se agrega LIMIT 1 para asegurar que solo se obtenga un registro
    SELECT idAlimento, stockActual INTO _idAlimento, _currentStock
    FROM Alimentos
    WHERE LOWER(nombreAlimento) = LOWER(_nombreAlimento)
      AND idLote = _idLote
      AND idUnidadMedida = _idUnidadMedida
    ORDER BY idAlimento ASC -- Opcional: especifica el orden para seleccionar el primer registro consistente
    LIMIT 1 FOR UPDATE;

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
    VALUES (_idAlimento, 'Entrada', _cantidad, _idUsuario, NOW(), _idUnidadMedida);

    COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_alimentos_nuevo` (IN `_idUsuario` INT, IN `_nombreAlimento` VARCHAR(100), IN `_idTipoAlimento` INT, IN `_idUnidadMedida` INT, IN `_lote` VARCHAR(50), IN `_costo` DECIMAL(10,2), IN `_fechaCaducidad` DATE, IN `_stockActual` DECIMAL(10,2), IN `_stockMinimo` DECIMAL(10,2))   BEGIN
    DECLARE _exists INT DEFAULT 0;
    DECLARE _idLote INT;
    DECLARE _estado ENUM('Disponible', 'Por agotarse', 'Agotado');

    -- Determinar el estado inicial del alimento
    IF _stockActual = 0 THEN
        SET _estado = 'Agotado';
    ELSEIF _stockActual <= _stockMinimo THEN
        SET _estado = 'Por agotarse';
    ELSE
        SET _estado = 'Disponible';
    END IF;

    -- Iniciar transacción
    START TRANSACTION;

    -- Verificar si el tipo de alimento existe y obtener `idTipoAlimento`
    IF NOT EXISTS (SELECT 1 FROM TipoAlimentos WHERE idTipoAlimento = _idTipoAlimento) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Tipo de alimento no encontrado. Verifique el ID proporcionado.';
    END IF;

    -- Verificar si la unidad de medida existe y obtener `idUnidadMedida`
    IF NOT EXISTS (SELECT 1 FROM UnidadesMedidaAlimento WHERE idUnidadMedida = _idUnidadMedida) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Unidad de medida no encontrada. Verifique el ID proporcionado.';
    END IF;

    -- Verificar si el lote ya está registrado en la tabla LotesAlimento
    SELECT idLote INTO _idLote 
    FROM LotesAlimento
    WHERE lote = _lote
    LIMIT 1;

    -- Si el lote no existe, registrarlo en la tabla LotesAlimento
    IF _idLote IS NULL THEN
        INSERT INTO LotesAlimento (lote, fechaCaducidad, fechaIngreso) 
        VALUES (_lote, IFNULL(_fechaCaducidad, NULL), NOW());
        SET _idLote = LAST_INSERT_ID();
    END IF;

    -- Verificar si el alimento ya está registrado con ese nombre, lote, tipo y unidad de medida
    SELECT COUNT(*) INTO _exists 
    FROM Alimentos
    WHERE nombreAlimento = _nombreAlimento 
      AND idLote = _idLote 
      AND idTipoAlimento = _idTipoAlimento 
      AND idUnidadMedida = _idUnidadMedida;

    -- Si el alimento no existe, registrarlo en la tabla Alimentos
    IF _exists = 0 THEN
        INSERT INTO Alimentos (
            idUsuario, nombreAlimento, idTipoAlimento, idUnidadMedida, idLote, costo, 
            stockActual, stockMinimo, estado, fechaMovimiento, compra
        ) 
        VALUES (
            _idUsuario, _nombreAlimento, _idTipoAlimento, _idUnidadMedida, _idLote, _costo, 
            _stockActual, _stockMinimo, _estado, NOW(), _costo * _stockActual
        );
        COMMIT;
    ELSE
        ROLLBACK;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_alimentos_salida` (IN `_idUsuario` INT, IN `_nombreAlimento` VARCHAR(100), IN `_idUnidadMedida` INT, IN `_cantidad` DECIMAL(10,2), IN `_uso` DECIMAL(10,2), IN `_merma` DECIMAL(10,2), IN `_idEquino` INT, IN `_lote` VARCHAR(50))   BEGIN
    DECLARE _idAlimento INT;
    DECLARE _idLote INT;
    DECLARE _currentStock DECIMAL(10,2);

    -- Iniciar transacción
    START TRANSACTION;

    -- Validar que la cantidad de uso y merma sumen la cantidad total de salida
    IF _cantidad != (_uso + _merma) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La cantidad de uso y merma deben sumar el total de la salida.';
        ROLLBACK;
    ELSEIF _cantidad <= 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La cantidad a retirar debe ser mayor que cero.';
        ROLLBACK;
    ELSE
        -- Verificar si el lote existe y obtener su ID
        SELECT idLote INTO _idLote
        FROM LotesAlimento
        WHERE LOWER(lote) = LOWER(_lote)
        LIMIT 1;

        -- Si el lote no existe, generar un error
        IF _idLote IS NULL THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El lote especificado no existe.';
            ROLLBACK;
        ELSE
            -- Buscar el alimento usando el lote, nombre de alimento, y idUnidadMedida
            SELECT a.idAlimento, a.stockActual INTO _idAlimento, _currentStock
            FROM Alimentos a
            WHERE LOWER(a.nombreAlimento) = LOWER(_nombreAlimento)
              AND a.idLote = _idLote
              AND a.idUnidadMedida = _idUnidadMedida
            LIMIT 1 FOR UPDATE;

            -- Verificar si el alimento existe y que el stock sea suficiente
            IF _idAlimento IS NULL THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El alimento con este lote y unidad de medida no está registrado.';
                ROLLBACK;
            ELSEIF _currentStock < _cantidad THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No hay suficiente stock disponible.';
                ROLLBACK;
            ELSE
                -- Actualizar el stock del alimento
                UPDATE Alimentos
                SET stockActual = stockActual - _cantidad,
                    idEquino = _idEquino,
                    fechaMovimiento = NOW()
                WHERE idAlimento = _idAlimento;

                -- Insertar en el historial de movimientos
                INSERT INTO HistorialMovimientos (idAlimento, tipoMovimiento, cantidad, merma, idUsuario, fechaMovimiento, idEquino, unidadMedida)
                VALUES (_idAlimento, 'Salida', _uso, _merma, _idUsuario, NOW(), _idEquino, (SELECT nombreUnidad FROM UnidadesMedidaAlimento WHERE idUnidadMedida = _idUnidadMedida));

                -- Insertar en la tabla de Mermas
                IF _merma > 0 THEN
                    INSERT INTO MermasAlimento (idAlimento, cantidadMerma, fechaMerma, motivo)
                    VALUES (_idAlimento, _merma, NOW(), 'Merma registrada en salida de inventario');
                END IF;

                -- Confirmación de éxito
                COMMIT;
                SIGNAL SQLSTATE '01000' SET MESSAGE_TEXT = 'Salida registrada exitosamente con desglose de uso y merma.';
            END IF;
        END IF;
    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_buscar_equino_por_nombre` (IN `p_nombreEquino` VARCHAR(100))   BEGIN
    SELECT 
        e.idEquino,
        e.nombreEquino,
        e.fechaNacimiento,
        e.sexo,
        te.tipoEquino,
        em.nombreEstado AS estadoMonta,
        n.nacionalidad,
        e.pesokg,
        e.idPropietario,
        e.fotografia,
        IF(e.estado = 1, 'Vivo', IF(e.estado = 2, 'Muerto', 'Desconocido')) AS estado
    FROM 
        Equinos e
    JOIN 
        TipoEquinos te ON e.idTipoEquino = te.idTipoEquino
    LEFT JOIN 
        EstadoMonta em ON e.idEstadoMonta = em.idEstadoMonta 
    LEFT JOIN 
        Nacionalidades n ON e.idNacionalidad = n.idNacionalidad
    WHERE 
        e.nombreEquino = p_nombreEquino
        AND e.idPropietario IS NULL; 
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_buscar_nacionalidad` (IN `_nacionalidad` VARCHAR(255))   BEGIN
    SELECT idNacionalidad, nacionalidad
    FROM nacionalidades
    WHERE nacionalidad LIKE CONCAT('%', _nacionalidad, '%');
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_cambiar_estado_implemento` (IN `p_idInventario` INT, IN `p_nuevoEstado` BIT)   BEGIN
    -- Cambiar el estado del implemento
    UPDATE Implementos
    SET estado = p_nuevoEstado
    WHERE idInventario = p_idInventario;

    -- Verificar si la actualización fue exitosa
    IF ROW_COUNT() = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Implemento no encontrado.';
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_consultar_historial_medicoMedi` ()   BEGIN
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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_contar_equinos_por_categoria` ()   BEGIN
    SELECT 
        e.idEquino,  -- Incluir idEquino en el SELECT
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
        e.idEquino, Categoria  -- Asegurarse de agrupar por idEquino para obtener el detalle
    ORDER BY 
        Categoria;
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_eliminarAlimento` (IN `_idAlimento` INT)   BEGIN
    -- Verificar si el alimento existe antes de intentar eliminarlo
    IF EXISTS (SELECT 1 FROM Alimentos WHERE idAlimento = _idAlimento) THEN
        -- Eliminar el alimento
        DELETE FROM Alimentos WHERE idAlimento = _idAlimento;
    ELSE
        -- Si no existe, generar un error de control
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El alimento no existe.';
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
        E.pesokg,
        N.nacionalidad AS nacionalidad,
        E.estado,
        E.fotografia,  -- Aquí seleccionamos la columna 'fotografia'
        CASE 
            WHEN E.estado = 1 THEN 'Vivo'
            WHEN E.estado = 0 THEN 'Muerto'
            ELSE 'Desconocido'
        END AS estadoDescriptivo,
        
        -- Relacionamos el historial completo del equino
        HE.descripcion AS descripcion
        
    FROM
        Equinos E
    LEFT JOIN TipoEquinos TE ON E.idTipoEquino = TE.idTipoEquino
    LEFT JOIN EstadoMonta EM ON E.idEstadoMonta = EM.idEstadoMonta
    LEFT JOIN nacionalidades N ON E.idNacionalidad = N.idNacionalidad
    LEFT JOIN HistorialEquinos HE ON E.idEquino = HE.idEquino  -- Relación con historial
    WHERE
        E.idPropietario IS NULL
    ORDER BY 
        E.estado DESC,
        E.idEquino DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_equino_editar` (IN `_idEquino` INT, IN `_nombreEquino` VARCHAR(100), IN `_fechaNacimiento` DATE, IN `_sexo` ENUM('Macho','Hembra'), IN `_detalles` TEXT, IN `_idTipoEquino` INT, IN `_idPropietario` INT, IN `_pesokg` DECIMAL(5,1), IN `_idNacionalidad` INT, IN `_idEstadoMonta` INT)   BEGIN
    DECLARE _errorMsg VARCHAR(255);
    DECLARE _edadMeses INT;
    DECLARE _edadAnios INT;
    DECLARE _sexoEquino CHAR(1);
    
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

    -- Verificar si el equino existe
    IF NOT EXISTS (SELECT 1 FROM Equinos WHERE idEquino = _idEquino) THEN
        SET _errorMsg = 'Error: No existe un equino con el ID proporcionado.';
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

    -- Validar que la nacionalidad exista
    IF NOT EXISTS (SELECT 1 FROM nacionalidades WHERE idNacionalidad = _idNacionalidad) THEN
        SET _errorMsg = 'Error: La nacionalidad seleccionada no existe.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
    END IF;

    -- Validación para evitar que un macho esté preñado
    IF _sexo = 'Macho' AND _idEstadoMonta = (SELECT idEstadoMonta FROM EstadoMonta WHERE nombreEstado = 'Preñada') THEN
        SET _errorMsg = 'Error: Un macho no puede estar preñado.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
    END IF;

    -- Actualización de los datos del equino
    UPDATE Equinos
    SET 
        nombreEquino = _nombreEquino,
        fechaNacimiento = _fechaNacimiento,
        sexo = _sexo,
        idTipoEquino = _idTipoEquino,
        detalles = _detalles,
        idPropietario = _idPropietario,
        pesokg = _pesokg,
        idNacionalidad = _idNacionalidad,
        idEstadoMonta = _idEstadoMonta
    WHERE idEquino = _idEquino;

    -- Verificar si la actualización fue exitosa
    IF ROW_COUNT() = 0 THEN
        SET _errorMsg = 'Error: No se encontró el equino con el ID proporcionado.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
    END IF;
    
    -- Retornar el ID del equino editado
    SELECT _idEquino AS idEquino;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_equino_registrar` (IN `_nombreEquino` VARCHAR(100), IN `_fechaNacimiento` DATE, IN `_sexo` ENUM('Macho','Hembra'), IN `_detalles` TEXT, IN `_idTipoEquino` INT, IN `_idPropietario` INT, IN `_pesokg` DECIMAL(5,1), IN `_idNacionalidad` INT, IN `_public_id` VARCHAR(255))   BEGIN
    DECLARE _errorMsg VARCHAR(255);
    DECLARE _edadDias INT;
    DECLARE _idEquino INT;
    DECLARE _idEstadoMonta INT;

    -- Calcular la edad en días
    SET _edadDias = TIMESTAMPDIFF(DAY, _fechaNacimiento, CURDATE());

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
        -- Recién nacido (<= 180 días)
        IF _edadDias <= 180 THEN
            IF _idTipoEquino NOT IN (5) THEN
                SET _errorMsg = 'Error: Un equino recién nacido debe ser registrado como tipo "Recién nacido".';
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
            END IF;
        -- Potrillo o potranca (<= 730 días)
        ELSEIF _edadDias > 180 AND _edadDias <= 730 THEN
            IF _sexo = 'Macho' AND _idTipoEquino != 4 THEN
                SET _errorMsg = 'Error: Un macho de esta edad debe ser registrado como potrillo.';
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
            END IF;
            IF _sexo = 'Hembra' AND _idTipoEquino != 3 THEN
                SET _errorMsg = 'Error: Una hembra de esta edad debe ser registrada como potranca.';
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
            END IF;
        -- Equinos mayores de 730 días
        ELSEIF _edadDias > 730 THEN
            IF _sexo = 'Macho' AND _idTipoEquino NOT IN (2, 4) THEN
                SET _errorMsg = 'Error: Un macho mayor de 730 días debe ser registrado como padrillo o potrillo.';
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
            END IF;
            IF _sexo = 'Hembra' AND _idTipoEquino NOT IN (1, 3) THEN
                SET _errorMsg = 'Error: Una hembra mayor de 730 días debe ser registrada como yegua o potranca.';
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

    -- INSERT con estado "Vivo"
    INSERT INTO Equinos (
        nombreEquino, 
        fechaNacimiento, 
        sexo, 
        idTipoEquino, 
        detalles, 
        idPropietario,
        pesokg,
        idNacionalidad,
        idEstadoMonta,
        fotografia,       -- Aquí guardaremos el public_id
        estado
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
        _idEstadoMonta,
        _public_id,       -- Guardar el public_id en la columna fotografia
        1  -- Estado "Vivo" (1)
    );

    -- Obtener el ID del equino recién insertado
    SET _idEquino = LAST_INSERT_ID();
    -- Retornar el ID del equino registrado
    SELECT _idEquino AS idEquino;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_gestionar_tratamiento` (IN `_idDetalleMed` INT, IN `_accion` VARCHAR(10))   BEGIN
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
            a.nombreAlimento,
            ta.tipoAlimento AS nombreTipoAlimento,
            um.nombreUnidad AS nombreUnidadMedida,
            l.lote,
            l.fechaCaducidad,
            a.stockActual,
            h.cantidad,
            h.unidadMedida,
            h.fechaMovimiento
        FROM 
            HistorialMovimientos h
        JOIN 
            Alimentos a ON h.idAlimento = a.idAlimento
        JOIN
            LotesAlimento l ON a.idLote = l.idLote
        JOIN
            TipoAlimentos ta ON a.idTipoAlimento = ta.idTipoAlimento
        JOIN
            UnidadesMedidaAlimento um ON a.idUnidadMedida = um.idUnidadMedida
        WHERE 
            h.tipoMovimiento = 'Entrada'  
            AND h.fechaMovimiento >= fechaInicio
            AND h.fechaMovimiento <= fechaFin
            AND (idUsuario = 0 OR h.idUsuario = idUsuario)
        ORDER BY 
            h.fechaMovimiento DESC
        LIMIT 
            limite OFFSET desplazamiento;

    -- Si el tipo de movimiento es 'Salida', mostrar campos específicos incluyendo el tipo de equino, cantidad de equinos por categoría, y otros detalles
    ELSEIF tipoMovimiento = 'Salida' THEN
        SELECT 
            h.idMovimiento AS ID,  -- ID del movimiento
            a.nombreAlimento AS Alimento,  -- Nombre del alimento
            CASE 
                WHEN te.tipoEquino = 'Yegua' AND em.nombreEstado = 'S/S' THEN 'Yegua Vacía'
                WHEN te.tipoEquino = 'Yegua' AND em.nombreEstado = 'Preñada' THEN 'Yegua Preñada'
                WHEN te.tipoEquino = 'Yegua' AND em.nombreEstado = 'Con Cria' THEN 'Yegua Con Cria'
                WHEN te.tipoEquino = 'Padrillo' AND em.nombreEstado = 'Activo' THEN 'Padrillo Activo'
                WHEN te.tipoEquino = 'Padrillo' AND em.nombreEstado = 'Inactivo' THEN 'Padrillo Inactivo'
                WHEN te.tipoEquino = 'Potranca' THEN 'Potranca'
                WHEN te.tipoEquino = 'Potrillo' THEN 'Potrillo'
                ELSE 'Desconocido'
            END AS TipoEquino,  -- Tipo de equino según el estado
            COUNT(h.idEquino) AS CantidadEquino,  -- Cantidad de equinos por categoría
            h.cantidad AS Cantidad,  -- Cantidad de salida
            um.nombreUnidad AS Unidad,  -- Unidad de medida
            h.merma AS Merma,  -- Merma (si aplica)
            l.lote AS Lote,  -- Lote del alimento
            h.fechaMovimiento AS FechaSalida  -- Fecha del movimiento
        FROM 
            HistorialMovimientos h
        JOIN 
            Alimentos a ON h.idAlimento = a.idAlimento
        LEFT JOIN
            Equinos eq ON h.idEquino = eq.idEquino
        LEFT JOIN
            TipoEquinos te ON eq.idTipoEquino = te.idTipoEquino
        LEFT JOIN
            EstadoMonta em ON eq.idEstadoMonta = em.idEstadoMonta
        JOIN
            UnidadesMedidaAlimento um ON a.idUnidadMedida = um.idUnidadMedida
        JOIN 
            LotesAlimento l ON a.idLote = l.idLote
        WHERE 
            h.tipoMovimiento = 'Salida'
            AND h.fechaMovimiento >= fechaInicio
            AND h.fechaMovimiento <= fechaFin
            AND (idUsuario = 0 OR h.idUsuario = idUsuario)
        GROUP BY 
            h.idMovimiento, Alimento, TipoEquino, Unidad, Lote, FechaSalida  -- Agrupar para evitar duplicados y calcular cantidad de equinos por categoría
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
            m.nombreMedicamento AS Medicamento,
            m.descripcion AS Descripcion,
            lm.lote AS Lote,
            m.cantidad_stock AS StockActual,
            h.cantidad AS Cantidad,
            h.fechaMovimiento AS FechaMovimiento
        FROM 
            HistorialMovimientosMedicamentos h
        JOIN 
            Medicamentos m ON h.idMedicamento = m.idMedicamento
        JOIN
            LotesMedicamento lm ON m.idLoteMedicamento = lm.idLoteMedicamento
        WHERE 
            h.tipoMovimiento = 'Entrada'
            AND h.fechaMovimiento >= fechaInicio
            AND h.fechaMovimiento <= fechaFin
            AND (idUsuario = 0 OR h.idUsuario = idUsuario)
        ORDER BY 
            h.fechaMovimiento DESC
        LIMIT 
            limite OFFSET desplazamiento;

    -- Si el tipo de movimiento es 'Salida', mostrar campos específicos incluyendo tipo de equino y cantidad por categoría
    ELSEIF tipoMovimiento = 'Salida' THEN
        SELECT 
            h.idMovimiento AS ID,
            m.nombreMedicamento AS Medicamento,
            m.descripcion AS Descripcion,
            lm.lote AS Lote,
            CASE 
                WHEN te.tipoEquino = 'Yegua' AND em.nombreEstado = 'S/S' THEN 'Yegua Vacía'
                WHEN te.tipoEquino = 'Yegua' AND em.nombreEstado = 'Preñada' THEN 'Yegua Preñada'
                WHEN te.tipoEquino = 'Yegua' AND em.nombreEstado = 'Con Cria' THEN 'Yegua Con Cria'
                WHEN te.tipoEquino = 'Padrillo' AND em.nombreEstado = 'Activo' THEN 'Padrillo Activo'
                WHEN te.tipoEquino = 'Padrillo' AND em.nombreEstado = 'Inactivo' THEN 'Padrillo Inactivo'
                WHEN te.tipoEquino = 'Potranca' THEN 'Potranca'
                WHEN te.tipoEquino = 'Potrillo' THEN 'Potrillo'
                ELSE 'Desconocido'
            END AS TipoEquino, 
            COUNT(h.idEquino) AS CantidadEquino, 
            h.cantidad AS Cantidad, 
            h.motivo AS Motivo, 
            h.fechaMovimiento AS FechaSalida
        FROM 
            HistorialMovimientosMedicamentos h
        JOIN 
            Medicamentos m ON h.idMedicamento = m.idMedicamento
        JOIN
            LotesMedicamento lm ON m.idLoteMedicamento = lm.idLoteMedicamento
        LEFT JOIN
            Equinos eq ON h.idEquino = eq.idEquino
        LEFT JOIN
            TipoEquinos te ON eq.idTipoEquino = te.idTipoEquino
        LEFT JOIN
            EstadoMonta em ON eq.idEstadoMonta = em.idEstadoMonta
        WHERE 
            h.tipoMovimiento = 'Salida'
            AND h.fechaMovimiento >= fechaInicio
            AND h.fechaMovimiento <= fechaFin
            AND (idUsuario = 0 OR h.idUsuario = idUsuario)
        GROUP BY 
            h.idMovimiento, Medicamento, TipoEquino, Motivo, FechaSalida
        ORDER BY 
            h.fechaMovimiento DESC
        LIMIT 
            limite OFFSET desplazamiento;
    ELSE
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Tipo de movimiento no válido.';
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_historial_medico_registrarMedi` (IN `_idEquino` INT, IN `_idUsuario` INT, IN `_idMedicamento` INT, IN `_dosis` VARCHAR(50), IN `_frecuenciaAdministracion` VARCHAR(50), IN `_viaAdministracion` VARCHAR(50), IN `_fechaFin` DATE, IN `_observaciones` TEXT, IN `_reaccionesAdversas` TEXT, IN `_tipoTratamiento` VARCHAR(20))   BEGIN
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

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_listarServiciosPorTipo` (IN `p_tipoServicio` ENUM('Propio','Mixto','General'))   BEGIN
    SELECT 
        s.idServicio,
        em.nombreEquino AS nombrePadrillo,
        eh.nombreEquino AS nombreYegua,
        ee.nombreEquino AS nombreEquinoExterno,
        s.fechaServicio,
        s.detalles,
        s.horaEntrada,
        s.horaSalida,
        s.costoServicio,
        p.nombreHaras
    FROM 
        Servicios s
    LEFT JOIN Equinos em ON s.idEquinoMacho = em.idEquino
    LEFT JOIN Equinos eh ON s.idEquinoHembra = eh.idEquino
    LEFT JOIN Equinos ee ON s.idEquinoExterno = ee.idEquino
    LEFT JOIN Propietarios p ON s.idPropietario = p.idPropietario
    WHERE 
        (
            (p_tipoServicio = 'General' AND s.tipoServicio IN ('General', 'Mixto', 'Propio'))
            OR s.tipoServicio = p_tipoServicio
        )
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
        AND idTipoEquino IN (1, 2)
        AND estado = 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_listar_equinos_propiosMedi` ()   BEGIN
	SELECT 
		idEquino,
		nombreEquino,
		sexo,
        pesokg,
		idTipoEquino
	FROM 
		Equinos
	WHERE 
		idPropietario IS NULL  -- Filtrar solo los equinos que no tienen propietario
		AND idTipoEquino IN (1, 2, 3, 4);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_listar_fotografia_dashboard` ()   BEGIN
    SELECT nombreEquino, fotografia FROM Equinos;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_listar_haras` ()   BEGIN
		SELECT DISTINCT 
        idPropietario,
        nombreHaras
    FROM Propietarios;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_listar_historial_movimiento` (IN `p_idTipoinventario` INT, IN `p_idTipomovimiento` INT)   BEGIN
    SELECT 
        h.idHistorial, 
        i.nombreProducto, 
        h.idTipomovimiento, 
        h.cantidad, 
        h.precioUnitario, 
        h.descripcion, 
        h.fechaMovimiento,
        ti.nombreInventario
    FROM 
        HistorialImplemento h
    INNER JOIN 
        TipoInventarios ti ON h.idTipoinventario = ti.idTipoinventario
	INNER JOIN
        Implementos i ON h.idInventario = i.idInventario 
    INNER JOIN 
        TipoMovimientos tm ON h.idTipomovimiento = tm.idTipomovimiento
	WHERE 
        h.idTipoinventario = p_idTipoinventario
		AND h.idTipomovimiento = p_idTipomovimiento;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_listar_implementos_con_cantidad` (IN `p_idTipoinventario` INT)   BEGIN
    -- Consulta para obtener la lista de implementos filtrados por idTipoinventario
    SELECT 
        idInventario, nombreProducto, stockFinal,
        cantidad, precioUnitario, precioTotal,
        estado
    FROM 
        Implementos
    WHERE
        idTipoinventario = p_idTipoinventario  -- Filtra por idTipoinventario
    ORDER BY 
        nombreProducto;  -- Ordena por nombre, puedes cambiar el orden si lo necesitas
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_listar_implementos_por_tipo` (IN `p_idTipoinventario` INT)   BEGIN
    -- Listar implementos según el tipo de inventario, con estado 0 al final
    SELECT *
    FROM Implementos
    WHERE idTipoinventario = p_idTipoinventario
    ORDER BY estado DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_listar_lotes_alimentos` ()   BEGIN
    -- Seleccionar todos los lotes registrados junto con la información de los alimentos asociados
    SELECT 
        l.idLote,                               -- ID único del lote
        l.lote,                                 -- Número del lote
        l.fechaCaducidad,                       -- Fecha de caducidad del lote
        l.fechaIngreso,                         -- Fecha de ingreso del lote
        a.nombreAlimento,                       -- Nombre del alimento
        ta.tipoAlimento AS nombreTipoAlimento,  -- Nombre del tipo de alimento
        a.stockActual,                          -- Stock actual del alimento
        a.stockMinimo,                          -- Stock mínimo para alerta
        a.estado,                               -- Estado del alimento
        um.nombreUnidad AS nombreUnidadMedida,  -- Nombre de la unidad de medida
        a.costo                                 -- Costo unitario del alimento
    FROM 
        Alimentos a
    JOIN 
        LotesAlimento l ON a.idLote = l.idLote                -- Relación entre alimentos y lotes
    JOIN 
        TipoAlimentos ta ON a.idTipoAlimento = ta.idTipoAlimento -- Relación para obtener el tipo de alimento
    JOIN 
        UnidadesMedidaAlimento um ON a.idUnidadMedida = um.idUnidadMedida -- Relación para obtener la unidad de medida
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
    -- Actualizar el estado de los registros en la tabla Medicamentos
    UPDATE Medicamentos 
    SET estado = 'Agotado'
    WHERE cantidad_stock = 0;

    UPDATE Medicamentos 
    SET estado = 'Por agotarse'
    WHERE cantidad_stock > 0 AND cantidad_stock <= stockMinimo;

    UPDATE Medicamentos 
    SET estado = 'Disponible'
    WHERE cantidad_stock > stockMinimo;

    -- Mostrar la información detallada de todos los medicamentos registrados
    SELECT 
        m.idMedicamento,
        m.nombreMedicamento,
        m.descripcion,
        lm.lote,                         -- Lote del medicamento (desde LotesMedicamento)
        p.presentacion,
        CONCAT(c.dosis, ' ', u.unidad) AS dosis,  -- Concatenar la cantidad y la unidad de medida
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
        UnidadesMedida u ON c.idUnidad = u.idUnidad  -- Relación con UnidadesMedida para obtener la unidad
    JOIN
        LotesMedicamento lm ON m.idLoteMedicamento = lm.idLoteMedicamento -- Relación con LotesMedicamento
    ORDER BY 
        m.nombreMedicamento ASC; -- Ordenar alfabéticamente por nombre de medicamento
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_listar_presentaciones_por_tipo` (IN `_idTipo` INT)   BEGIN
    SELECT DISTINCT p.idPresentacion, p.presentacion
    FROM CombinacionesMedicamentos c
    JOIN PresentacionesMedicamentos p ON c.idPresentacion = p.idPresentacion
    WHERE c.idTipo = _idTipo
    ORDER BY p.presentacion ASC;
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
    -- Selecciona los tipos de medicamentos junto con la presentación y la dosis (cantidad y unidad), agrupados
    SELECT 
        c.idCombinacion,  -- Incluye el ID de combinación para poder identificar cada sugerencia de combinación de manera única
        t.tipo, 
        GROUP_CONCAT(DISTINCT p.presentacion ORDER BY p.presentacion ASC SEPARATOR ', ') AS presentaciones,
        GROUP_CONCAT(DISTINCT u.unidad ORDER BY c.dosis ASC SEPARATOR ', ') AS dosis
    FROM 
        CombinacionesMedicamentos c
    JOIN 
        TiposMedicamentos t ON c.idTipo = t.idTipo
    JOIN 
        PresentacionesMedicamentos p ON c.idPresentacion = p.idPresentacion
    JOIN 
        UnidadesMedida u ON c.idUnidad = u.idUnidad
    GROUP BY 
        c.idCombinacion, t.tipo  -- Asegúrate de agrupar por idCombinacion para evitar resultados ambiguos
    ORDER BY 
        t.tipo ASC;  -- Ordena por tipo de medicamento
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_listar_tipos_unicos` ()   BEGIN
    SELECT DISTINCT t.idTipo, t.tipo
    FROM TiposMedicamentos t
    JOIN CombinacionesMedicamentos c ON t.idTipo = c.idTipo
    ORDER BY t.tipo ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_listar_tipo_movimiento` ()   BEGIN
    SELECT * FROM tipomovimientos;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_medicamentos_entrada` (IN `_idUsuario` INT, IN `_nombreMedicamento` VARCHAR(255), IN `_lote` VARCHAR(100), IN `_cantidad` INT)   BEGIN
    DECLARE _idMedicamento INT;
    DECLARE _idLoteMedicamento INT;
    DECLARE _currentStock INT;
    DECLARE _debugInfo VARCHAR(255) DEFAULT '';

    -- Verificar si la cantidad es mayor que cero
    IF _cantidad <= 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La cantidad debe ser mayor a 0 para registrar una entrada de medicamento.';
    ELSE
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
    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_medicamentos_registrar` (IN `_nombreMedicamento` VARCHAR(255), IN `_descripcion` TEXT, IN `_lote` VARCHAR(100), IN `_idPresentacion` INT, IN `_dosisCompleta` VARCHAR(50), IN `_idTipo` INT, IN `_cantidad_stock` INT, IN `_stockMinimo` INT, IN `_fechaCaducidad` DATE, IN `_precioUnitario` DECIMAL(10,2), IN `_idUsuario` INT)   BEGIN
    DECLARE _idCombinacion INT DEFAULT NULL;
    DECLARE _idLoteMedicamento INT DEFAULT NULL;
    DECLARE _dosis DECIMAL(10,2);
    DECLARE _unidad VARCHAR(50);

    -- Separar dosis en cantidad y unidad
    SET _dosis = CAST(SUBSTRING_INDEX(_dosisCompleta, ' ', 1) AS DECIMAL(10,2));
    SET _unidad = TRIM(SUBSTRING_INDEX(_dosisCompleta, ' ', -1));

    -- Validar y registrar la combinación de dosis con IDs de tipo y presentación
    CALL spu_validar_registrar_combinacion(_idTipo, _idPresentacion, _dosis, _unidad);

    -- Recuperar el idCombinacion ya validado o registrado en `spu_validar_registrar_combinacion`
    SELECT idCombinacion INTO _idCombinacion
    FROM CombinacionesMedicamentos
    WHERE idTipo = _idTipo
      AND idPresentacion = _idPresentacion
      AND dosis = _dosis
      AND idUnidad = (SELECT idUnidad FROM UnidadesMedida WHERE unidad = _unidad LIMIT 1);

    -- Verificar si el idCombinacion es NULL y lanzar un error
    IF _idCombinacion IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error: idCombinacion es NULL.';
    END IF;

    -- Verificar si el lote ya existe
    SELECT idLoteMedicamento INTO _idLoteMedicamento 
    FROM LotesMedicamento
    WHERE lote = _lote;

    -- Crear el lote si no existe
    IF _idLoteMedicamento IS NULL THEN
        INSERT INTO LotesMedicamento (lote, fechaCaducidad) 
        VALUES (_lote, _fechaCaducidad);
        SET _idLoteMedicamento = LAST_INSERT_ID();
    END IF;

    -- Verificar si el idLoteMedicamento es NULL y lanzar un error
    IF _idLoteMedicamento IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error: idLoteMedicamento es NULL.';
    END IF;

    -- Insertar en Medicamentos
    INSERT INTO Medicamentos (
        nombreMedicamento, 
        descripcion, 
        idLoteMedicamento, 
        idCombinacion,
        cantidad_stock,
        stockMinimo, 
        estado,
        fecha_registro,
        precioUnitario, 
        idUsuario
    ) 
    VALUES (
        _nombreMedicamento, 
        _descripcion, 
        _idLoteMedicamento, 
        _idCombinacion,
        _cantidad_stock, 
        _stockMinimo, 
        'Disponible',
        CURDATE(), 
        _precioUnitario, 
        _idUsuario
    );

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_medicamentos_salida` (IN `_idUsuario` INT, IN `_nombreMedicamento` VARCHAR(255), IN `_cantidad` DECIMAL(10,2), IN `_idEquino` INT, IN `_lote` VARCHAR(100), IN `_motivo` TEXT)   BEGIN
    DECLARE _idMedicamento INT;
    DECLARE _idLoteMedicamento INT;
    DECLARE _currentStock DECIMAL(10,2);
    DECLARE _debugInfo VARCHAR(255) DEFAULT '';  -- Variable para depuración

    -- Iniciar transacción para asegurar consistencia de la operación
    START TRANSACTION;

    -- Validar que la cantidad a retirar sea mayor que cero
    IF _cantidad <= 0 THEN
        SET _debugInfo = 'La cantidad a retirar debe ser mayor que cero.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _debugInfo;
    END IF;

    -- Verificar si el lote fue proporcionado
    IF _lote IS NOT NULL AND _lote != '' THEN
        -- Si se proporciona el lote, obtener su ID
        SELECT idLoteMedicamento INTO _idLoteMedicamento
        FROM LotesMedicamento
        WHERE lote = _lote;

        -- Si el lote no existe, generar un error
        IF _idLoteMedicamento IS NULL THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El lote especificado no existe en LotesMedicamento.';
        END IF;
    ELSE
        -- Si el lote no es proporcionado, buscar el lote más antiguo con stock disponible
        SELECT idLoteMedicamento INTO _idLoteMedicamento
        FROM LotesMedicamento
        WHERE idLoteMedicamento IN (
            SELECT idLoteMedicamento
            FROM Medicamentos
            WHERE LOWER(nombreMedicamento) = LOWER(_nombreMedicamento)
              AND cantidad_stock > 0
        )
        ORDER BY fechaCaducidad ASC
        LIMIT 1;

        -- Si no se encuentra un lote con stock disponible, generar un error
        IF _idLoteMedicamento IS NULL THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No hay lotes disponibles para este medicamento con stock suficiente.';
        END IF;
    END IF;

    -- Buscar el medicamento usando el nombre y el idLoteMedicamento obtenido
    SELECT idMedicamento, cantidad_stock INTO _idMedicamento, _currentStock
    FROM Medicamentos
    WHERE LOWER(nombreMedicamento) = LOWER(_nombreMedicamento)
      AND idLoteMedicamento = _idLoteMedicamento
    LIMIT 1 FOR UPDATE;

    -- Si el medicamento no existe o no tiene suficiente stock, generar un error o advertencia
    IF _idMedicamento IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El medicamento con este lote no está registrado.';
    ELSEIF _currentStock < _cantidad THEN
        -- Advertencia de que el stock es insuficiente para la cantidad solicitada
        SET _debugInfo = CONCAT('Stock insuficiente. Solo hay ', _currentStock, 
                                ' disponible en este lote. Retire esa cantidad o elija otro lote.');
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _debugInfo;
    ELSE
        -- Realizar la salida del stock en el lote seleccionado
        UPDATE Medicamentos
        SET cantidad_stock = cantidad_stock - _cantidad,
            ultima_modificacion = NOW()
        WHERE idMedicamento = _idMedicamento;

        -- Registrar la salida en el historial de movimientos con motivo
        INSERT INTO HistorialMovimientosMedicamentos (idMedicamento, tipoMovimiento, cantidad, idUsuario, fechaMovimiento, idEquino, motivo)
        VALUES (_idMedicamento, 'Salida', _cantidad, _idUsuario, NOW(), _idEquino, _motivo);

        -- Confirmar la transacción
        COMMIT;

        -- Confirmación de éxito
        SET _debugInfo = 'Transacción completada exitosamente.';
        SIGNAL SQLSTATE '01000' SET MESSAGE_TEXT = _debugInfo;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_movimiento_implemento` (IN `p_idTipomovimiento` INT, IN `p_idTipoinventario` INT, IN `p_idInventario` INT, IN `p_cantidad` INT, IN `p_precioUnitario` DECIMAL(10,2), IN `p_descripcion` TEXT)   BEGIN
    DECLARE v_idInventario INT;
    DECLARE v_stockFinal INT;
    DECLARE v_precioTotal DECIMAL(10,2);

    -- Validar que la cantidad sea un número positivo
    IF p_cantidad <= 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La cantidad debe ser un número positivo.';
    END IF;

    -- Obtener el idInventario y stockFinal a partir del nombreProducto
    SELECT idInventario, stockFinal INTO v_idInventario, v_stockFinal
    FROM Implementos
    WHERE idInventario = p_idInventario AND idTipoinventario = p_idTipoinventario
    LIMIT 1;

    -- Si el producto no existe y el tipo de movimiento es 'Entrada', se crea un nuevo registro
    IF v_idInventario IS NULL AND p_idTipomovimiento = 1 THEN  -- 1 = Entrada
        -- Insertar un nuevo implemento en la tabla Implementos con el precio unitario
        INSERT INTO Implementos (
            idTipoinventario, idInventario, descripcion, precioUnitario, stockFinal, estado
        )
        VALUES (
            p_idTipoinventario, p_idInventario, p_descripcion, p_precioUnitario, p_cantidad, 1  -- Estado por defecto 1 (activo)
        );

        -- Obtener el ID del nuevo implemento
        SET v_idInventario = LAST_INSERT_ID();
        SET v_stockFinal = p_cantidad;  -- La cantidad total que entra

    ELSEIF v_idInventario IS NOT NULL THEN
        -- Si el producto ya existe, actualizamos el stock y el precio según el tipo de movimiento
        IF p_idTipomovimiento = 1 THEN  -- 1 = Entrada
            -- Sumar la cantidad en caso de entrada
            SET v_stockFinal = v_stockFinal + p_cantidad;
            -- Actualizar el precio unitario si es diferente al actual
            UPDATE Implementos
            SET stockFinal = v_stockFinal, precioUnitario = p_precioUnitario
            WHERE idInventario = v_idInventario;
        ELSEIF p_idTipomovimiento = 2 THEN  -- 2 = Salida
            -- Validar que haya suficiente stock para la salida
            IF v_stockFinal < p_cantidad THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No hay suficiente stock para la salida solicitada.';
            END IF;
            -- Restar la cantidad en caso de salida
            SET v_stockFinal = v_stockFinal - p_cantidad;
            -- Actualizar el stock en la tabla Implementos
            UPDATE Implementos
            SET stockFinal = v_stockFinal
            WHERE idInventario = v_idInventario;
        END IF;
    ELSE
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El producto no existe.';
    END IF;

    -- Calcular el precio total para el movimiento
    SET v_precioTotal = p_precioUnitario * p_cantidad;

    -- Registrar el movimiento en el historial de implementos
    INSERT INTO HistorialImplemento (
        idInventario, 
        idTipoinventario, 
        idTipomovimiento, 
        cantidad, 
        precioUnitario, 
        precioTotal,  -- Este es el precio total que ahora se está calculando y guardando
        descripcion
    )
    VALUES (
        v_idInventario, 
        p_idTipoinventario, 
        p_idTipomovimiento, 
        p_cantidad, 
        p_precioUnitario,
        v_precioTotal,  -- Precio total (precio unitario * cantidad)
        p_descripcion  -- La descripción puede ser NULL
    );

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_notificar_stock_bajo_alimentos` ()   BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE alimentoNombre VARCHAR(100);
    DECLARE loteAlimento VARCHAR(50);
    DECLARE stockActual DECIMAL(10,2);
    DECLARE stockMinimo DECIMAL(10,2);
    DECLARE tipoAlimento VARCHAR(50);
    DECLARE unidadMedida VARCHAR(10);

    -- Cursor para seleccionar los alimentos con stock bajo o agotados, limitando a 5
    DECLARE cur CURSOR FOR
        SELECT a.nombreAlimento, l.lote, a.stockActual, a.stockMinimo, ta.tipoAlimento, um.nombreUnidad 
        FROM Alimentos a
        JOIN LotesAlimento l ON a.idLote = l.idLote
        JOIN TipoAlimentos ta ON a.idTipoAlimento = ta.idTipoAlimento
        JOIN UnidadesMedidaAlimento um ON a.idUnidadMedida = um.idUnidadMedida
        WHERE a.stockActual <= a.stockMinimo
        ORDER BY a.stockActual ASC
        LIMIT 5;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    -- Abrir el cursor
    OPEN cur;

    -- Bucle para recorrer los resultados
    read_loop: LOOP
        FETCH cur INTO alimentoNombre, loteAlimento, stockActual, stockMinimo, tipoAlimento, unidadMedida;
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Imprimir el mensaje de notificación
        IF stockActual = 0 THEN
            -- Notificación de alimentos agotados
            SELECT CONCAT('Alimento agotado: ', alimentoNombre, ' (Tipo: ', tipoAlimento, '), Lote: ', loteAlimento, ', Unidad: ', unidadMedida, ', Stock: ', stockActual) AS Notificacion;
        ELSE
            -- Notificación de alimentos con stock bajo
            SELECT CONCAT('Alimento con stock bajo: ', alimentoNombre, ' (Tipo: ', tipoAlimento, '), Lote: ', loteAlimento, ', Unidad: ', unidadMedida, ', Stock: ', stockActual, ' (Stock mínimo: ', stockMinimo, ')') AS Notificacion;
        END IF;
    END LOOP;

    -- Cerrar cursor
    CLOSE cur;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_notificar_stock_bajo_medicamentos` ()   BEGIN
    -- Notificaciones para medicamentos agotados
    SELECT 
        CONCAT('Medicamento agotado: ', m.nombreMedicamento, ', Lote: ', lm.lote, ', Stock: ', m.cantidad_stock) AS Notificacion
    FROM 
        Medicamentos m
    JOIN 
        LotesMedicamento lm ON m.idLoteMedicamento = lm.idLoteMedicamento
    WHERE 
        m.cantidad_stock = 0
    LIMIT 5;

    -- Notificaciones para medicamentos con stock bajo
    SELECT 
        CONCAT('Medicamento con stock bajo: ', m.nombreMedicamento, ', Lote: ', lm.lote, ', Stock: ', m.cantidad_stock, ' (Stock mínimo: ', m.stockMinimo, ')') AS Notificacion
    FROM 
        Medicamentos m
    JOIN 
        LotesMedicamento lm ON m.idLoteMedicamento = lm.idLoteMedicamento
    WHERE 
        m.cantidad_stock > 0 AND m.cantidad_stock < m.stockMinimo
    LIMIT 5;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_obtenerAlimentosConLote` (IN `_idAlimento` INT)   BEGIN
	-- Actualizar el estado de los registros en la tabla Alimentos
		UPDATE Alimentos 
		SET estado = 'Agotado'
		WHERE stockActual = 0;

		UPDATE Alimentos 
		SET estado = 'Por agotarse'
		WHERE stockActual > 0 AND stockActual <= stockMinimo;

		UPDATE Alimentos 
		SET estado = 'Disponible'
		WHERE stockActual > stockMinimo;
        
		SELECT 
        A.idAlimento,
        A.idUsuario,
        A.nombreAlimento,
        TA.tipoAlimento AS nombreTipoAlimento,       -- Obtener el nombre del tipo de alimento
        A.stockActual,
        A.stockMinimo,
        A.estado,
        U.nombreUnidad AS unidadMedidaNombre,        -- Obtener el nombre de la unidad de medida
        A.costo,
        A.idLote,
        A.idEquino,                                  -- Uso de idEquino en lugar de idTipoEquino
        A.compra,
        A.fechaMovimiento,
        L.idLote AS loteId,
        L.lote,
        L.fechaCaducidad,
        L.fechaIngreso
    FROM 
        Alimentos A
    INNER JOIN 
        LotesAlimento L ON A.idLote = L.idLote
    INNER JOIN 
        TipoAlimentos TA ON A.idTipoAlimento = TA.idTipoAlimento       -- Relación con TipoAlimentos
    INNER JOIN 
        UnidadesMedidaAlimento U ON A.idUnidadMedida = U.idUnidadMedida -- Relación con UnidadesMedidaAlimento
    WHERE 
        (_idAlimento IS NULL OR A.idAlimento = _idAlimento);           -- Filtro por idAlimento si se proporciona
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_obtenerTiposAlimento` ()   BEGIN
    SELECT idTipoAlimento, tipoAlimento 
    FROM TipoAlimentos 
    ORDER BY tipoAlimento;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_obtenerUnidadesPorTipoAlimento` (IN `_idTipoAlimento` INT)   BEGIN
    -- Verificar si el tipo de alimento existe
    IF EXISTS (SELECT 1 FROM TipoAlimentos WHERE idTipoAlimento = _idTipoAlimento) THEN
        -- Seleccionar las unidades de medida asociadas al tipo de alimento
        SELECT um.idUnidadMedida, um.nombreUnidad 
        FROM TipoAlimento_UnidadMedida tum
        JOIN UnidadesMedidaAlimento um ON tum.idUnidadMedida = um.idUnidadMedida
        WHERE tum.idTipoAlimento = _idTipoAlimento;
    ELSE
        -- Enviar mensaje de error si el tipo de alimento no existe
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El tipo de alimento especificado no existe.';
    END IF;
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_obtener_historial_equino` (IN `p_idEquino` INT)   BEGIN
    -- Verificar si existen registros en el historial
    IF EXISTS (SELECT 1 FROM HistorialEquinos WHERE idEquino = p_idEquino) THEN
        -- Selección del historial y la fotografía
        SELECT
            HE.descripcion,
            E.fotografia
        FROM
            HistorialEquinos HE
        JOIN
            Equinos E ON HE.idEquino = E.idEquino
        WHERE
            HE.idEquino = p_idEquino;
    ELSE
        -- Mensaje en caso de no haber registros
        SELECT 'No se encontró historial para el equino con ID ' AS mensaje;
    END IF;
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_registrar_historial_equinos` (IN `p_idEquino` INT, IN `p_descripcion` TEXT)   BEGIN
    INSERT INTO HistorialEquinos (idEquino, descripcion)
    VALUES (p_idEquino, p_descripcion);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_registrar_implemento` (IN `p_idTipoinventario` INT, IN `p_nombreProducto` VARCHAR(100), IN `p_descripcion` TEXT, IN `p_precioUnitario` DECIMAL(10,2), IN `p_cantidad` INT)   BEGIN
    DECLARE v_idInventario INT;
    DECLARE v_stockFinal INT;
    DECLARE v_precioTotal DECIMAL(10,2);

    -- Validar que la cantidad y precio sean números positivos mayores a 0
    IF p_cantidad <= 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La cantidad debe ser un número positivo mayor a 0.';
    END IF;

    IF p_precioUnitario <= 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El precio unitario debe ser un número positivo mayor a 0.';
    END IF;

    -- Calcular el precio total (precio unitario * cantidad)
    SET v_precioTotal = p_precioUnitario * p_cantidad;

    -- Verificar si el producto con el mismo nombre ya existe en el tipo de inventario
    SELECT idInventario INTO v_idInventario
    FROM Implementos
    WHERE nombreProducto = p_nombreProducto AND idTipoinventario = p_idTipoinventario
    LIMIT 1;

    -- Si el producto ya existe, lanzar error
    IF v_idInventario IS NOT NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Ya existe un producto con el mismo nombre en este tipo de inventario.';
    ELSE
        -- Registrar nuevo producto en el inventario, incluyendo el precio total
        INSERT INTO Implementos (
            idTipoinventario, nombreProducto, descripcion, precioUnitario,
            idTipomovimiento, cantidad, stockFinal, estado, precioTotal
        )
        VALUES (
            p_idTipoinventario, p_nombreProducto, p_descripcion, p_precioUnitario,
            1, p_cantidad, p_cantidad, 1, v_precioTotal
        );
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_validar_registrar_combinacion` (IN `_idTipo` INT, IN `_idPresentacion` INT, IN `_dosisMedicamento` DECIMAL(10,2), IN `_unidadMedida` VARCHAR(50))   BEGIN
    DECLARE _idUnidad INT;
    DECLARE _idCombinacion INT;
    DECLARE _errorMensaje VARCHAR(255);
    -- Manejador de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        IF _errorMensaje IS NULL THEN
            SET _errorMensaje = 'Lo sentimos, ha ocurrido un error inesperado. Por favor, inténtalo de nuevo o contacta al administrador.';
        END IF;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMensaje;
    END;
    -- Iniciar la transacción
    START TRANSACTION;
    -- Validar unidad de medida y agregar depuración
    SELECT idUnidad INTO _idUnidad
    FROM UnidadesMedida
    WHERE LOWER(unidad) = LOWER(_unidadMedida)
    LIMIT 1;
    -- Verificar si la unidad de medida existe
    IF _idUnidad IS NULL THEN
        SET _errorMensaje = CONCAT('La unidad de medida "', _unidadMedida, '" no está registrada. Verifica que sea correcta.');
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMensaje;
    END IF;
    -- Agregar mensaje de depuración para _idTipo, _idPresentacion, _dosisMedicamento y _idUnidad
    SELECT _idTipo AS "ID Tipo", _idPresentacion AS "ID Presentacion", _dosisMedicamento AS "Dosis", _idUnidad AS "ID Unidad";
    -- Buscar combinación exacta
    SELECT idCombinacion INTO _idCombinacion
    FROM CombinacionesMedicamentos
    WHERE idTipo = _idTipo
      AND idPresentacion = _idPresentacion
      AND dosis = _dosisMedicamento
      AND idUnidad = _idUnidad
    LIMIT 1;
    -- Verificar si existe la combinación y agregar mensaje de depuración
    IF _idCombinacion IS NOT NULL THEN
        COMMIT;
        SELECT 'Combinación exacta encontrada.' AS mensaje, _idCombinacion AS idCombinacion;
    ELSE
        -- Registrar nueva combinación
        INSERT INTO CombinacionesMedicamentos (idTipo, idPresentacion, dosis, idUnidad)
        VALUES (_idTipo, _idPresentacion, _dosisMedicamento, _idUnidad);
        SET _idCombinacion = LAST_INSERT_ID();
        COMMIT;
        SELECT 'Nueva combinación registrada.' AS mensaje, _idCombinacion AS idCombinacion;
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
  `idTipoAlimento` int(11) NOT NULL,
  `stockActual` decimal(10,2) NOT NULL,
  `stockMinimo` decimal(10,2) DEFAULT 0.00,
  `estado` enum('Disponible','Por agotarse','Agotado') DEFAULT 'Disponible',
  `idUnidadMedida` int(11) NOT NULL,
  `costo` decimal(10,2) NOT NULL,
  `idLote` int(11) NOT NULL,
  `idEquino` int(11) DEFAULT NULL,
  `compra` decimal(10,2) NOT NULL,
  `fechaMovimiento` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alimentos`
--

INSERT INTO `alimentos` (`idAlimento`, `idUsuario`, `nombreAlimento`, `idTipoAlimento`, `stockActual`, `stockMinimo`, `estado`, `idUnidadMedida`, `costo`, `idLote`, `idEquino`, `compra`, `fechaMovimiento`) VALUES
(1, 3, 'Afrecho', 2, 45.00, 10.00, 'Disponible', 1, 50.00, 1, NULL, 2250.00, '2024-11-15 09:54:38'),
(2, 3, 'Cebada', 2, 4.00, 10.00, 'Por agotarse', 1, 40.00, 1, 3, 2000.00, '2024-11-15 10:00:29');

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
-- Estructura de tabla para la tabla `campos`
--

CREATE TABLE `campos` (
  `idCampo` int(11) NOT NULL,
  `numeroCampo` int(11) NOT NULL,
  `tamanoCampo` decimal(10,2) NOT NULL,
  `idTipoSuelo` int(11) NOT NULL,
  `estado` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `combinacionesmedicamentos`
--

CREATE TABLE `combinacionesmedicamentos` (
  `idCombinacion` int(11) NOT NULL,
  `idTipo` int(11) NOT NULL,
  `idPresentacion` int(11) NOT NULL,
  `dosis` decimal(10,2) NOT NULL,
  `idUnidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `combinacionesmedicamentos`
--

INSERT INTO `combinacionesmedicamentos` (`idCombinacion`, `idTipo`, `idPresentacion`, `dosis`, `idUnidad`) VALUES
(1, 1, 1, 500.00, 1),
(4, 1, 4, 1.00, 3),
(20, 1, 4, 5.00, 3),
(21, 1, 4, 500.00, 1),
(16, 1, 15, 250.00, 1),
(5, 2, 1, 50.00, 1),
(2, 2, 2, 10.00, 2),
(3, 3, 3, 200.00, 1),
(6, 3, 5, 5.00, 1),
(15, 3, 14, 15.00, 1),
(17, 3, 16, 100.00, 4),
(7, 4, 6, 300.00, 1),
(8, 5, 7, 100.00, 3),
(14, 5, 13, 50.00, 1),
(18, 6, 1, 10.00, 5),
(9, 6, 8, 1.00, 2),
(10, 7, 9, 0.50, 2),
(11, 8, 10, 20.00, 1),
(12, 9, 11, 5.00, 1),
(13, 10, 12, 1.00, 3),
(19, 11, 4, 200.00, 3);

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
  `fechaInicio` date NOT NULL,
  `fechaFin` date NOT NULL,
  `observaciones` text DEFAULT NULL,
  `reaccionesAdversas` text DEFAULT NULL,
  `idUsuario` int(11) NOT NULL,
  `tipoTratamiento` enum('Primario','Complementario') DEFAULT 'Primario',
  `estadoTratamiento` enum('Activo','Finalizado','En pausa') DEFAULT 'Activo'
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
  `idNacionalidad` int(11) DEFAULT NULL,
  `idPropietario` int(11) DEFAULT NULL,
  `pesokg` decimal(5,1) DEFAULT NULL,
  `fotografia` varchar(255) DEFAULT NULL,
  `estado` bit(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `equinos`
--

INSERT INTO `equinos` (`idEquino`, `nombreEquino`, `fechaNacimiento`, `sexo`, `idTipoEquino`, `detalles`, `idEstadoMonta`, `idNacionalidad`, `idPropietario`, `pesokg`, `fotografia`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'Southdale', '2006-04-03', 'Macho', 2, NULL, 1, 35, NULL, 650.0, 'hfruikigi3rdrjonbwbu', b'1', '2024-11-15 14:45:50', '2024-11-15 14:47:05'),
(2, 'Caleta', '2015-05-05', 'Hembra', 1, NULL, 4, 137, NULL, 550.0, 'xzvfcgtpgglpfxd2yrvv', b'1', '2024-11-15 14:46:28', '2024-11-15 14:47:05'),
(3, 'La candy', NULL, 'Hembra', 1, NULL, 5, 137, 1, NULL, 'xzvfcgtpgglpfxd2yrvv', b'1', '2024-11-15 14:46:43', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estadoherramienta`
--

CREATE TABLE `estadoherramienta` (
  `idEstado` int(11) NOT NULL,
  `descripcionEstado` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estadomonta`
--

CREATE TABLE `estadomonta` (
  `idEstadoMonta` int(11) NOT NULL,
  `genero` enum('Macho','Hembra') NOT NULL,
  `nombreEstado` enum('S/S','Servida','Por Servir','Preñada','Vacia','Activo','Inactivo') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estadomonta`
--

INSERT INTO `estadomonta` (`idEstadoMonta`, `genero`, `nombreEstado`) VALUES
(1, 'Macho', 'Activo'),
(2, 'Macho', 'Inactivo'),
(3, 'Hembra', 'Preñada'),
(4, 'Hembra', 'Servida'),
(5, 'Hembra', 'S/S'),
(6, 'Hembra', 'Por Servir'),
(7, 'Hembra', 'Vacia'),
(8, 'Hembra', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `herramientasusadashistorial`
--

CREATE TABLE `herramientasusadashistorial` (
  `idHerramientasUsadas` int(11) NOT NULL,
  `idHistorialHerrero` int(11) NOT NULL,
  `idHerramienta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historialequinos`
--

CREATE TABLE `historialequinos` (
  `idHistorial` int(11) NOT NULL,
  `idEquino` int(11) NOT NULL,
  `descripcion` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Estructura de tabla para la tabla `historialimplemento`
--

CREATE TABLE `historialimplemento` (
  `idHistorial` int(11) NOT NULL,
  `idInventario` int(11) NOT NULL,
  `idTipoinventario` int(11) NOT NULL,
  `idTipomovimiento` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precioUnitario` decimal(10,2) DEFAULT NULL,
  `precioTotal` decimal(10,2) DEFAULT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  `fechaMovimiento` timestamp NOT NULL DEFAULT current_timestamp()
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
  `idEquino` int(11) DEFAULT NULL,
  `idUsuario` int(11) NOT NULL,
  `unidadMedida` varchar(50) NOT NULL,
  `fechaMovimiento` date DEFAULT current_timestamp(),
  `merma` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historialmovimientos`
--

INSERT INTO `historialmovimientos` (`idMovimiento`, `idAlimento`, `tipoMovimiento`, `cantidad`, `idEquino`, `idUsuario`, `unidadMedida`, `fechaMovimiento`, `merma`) VALUES
(1, 2, 'Salida', 30.00, 1, 3, 'kg', '2024-11-15', 1.00),
(2, 2, 'Salida', 10.00, 3, 3, 'kg', '2024-11-15', 5.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historialmovimientosmedicamentos`
--

CREATE TABLE `historialmovimientosmedicamentos` (
  `idMovimiento` int(11) NOT NULL,
  `idMedicamento` int(11) NOT NULL,
  `tipoMovimiento` varchar(50) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `motivo` text NOT NULL,
  `idEquino` int(11) DEFAULT NULL,
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
  `precioUnitario` decimal(10,2) DEFAULT NULL,
  `precioTotal` decimal(10,2) DEFAULT NULL,
  `idTipomovimiento` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `stockFinal` int(11) NOT NULL,
  `estado` bit(1) NOT NULL,
  `create_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lotesalimento`
--

CREATE TABLE `lotesalimento` (
  `idLote` int(11) NOT NULL,
  `lote` varchar(50) NOT NULL,
  `fechaCaducidad` date DEFAULT NULL,
  `fechaIngreso` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `lotesalimento`
--

INSERT INTO `lotesalimento` (`idLote`, `lote`, `fechaCaducidad`, `fechaIngreso`) VALUES
(1, '1511', '2024-11-30', '2024-11-15 09:54:38');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lotesmedicamento`
--

CREATE TABLE `lotesmedicamento` (
  `idLoteMedicamento` int(11) NOT NULL,
  `lote` varchar(100) NOT NULL,
  `fechaCaducidad` date NOT NULL,
  `fechaIngreso` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `lotesmedicamento`
--

INSERT INTO `lotesmedicamento` (`idLoteMedicamento`, `lote`, `fechaCaducidad`, `fechaIngreso`) VALUES
(1, '1511', '2024-11-30', '2024-11-15');

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
  `idEquino` int(11) DEFAULT NULL,
  `idLoteMedicamento` int(11) NOT NULL,
  `precioUnitario` decimal(10,2) NOT NULL,
  `motivo` text DEFAULT NULL,
  `fecha_registro` date NOT NULL,
  `ultima_modificacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `medicamentos`
--

INSERT INTO `medicamentos` (`idMedicamento`, `idUsuario`, `nombreMedicamento`, `descripcion`, `idCombinacion`, `cantidad_stock`, `stockMinimo`, `estado`, `idEquino`, `idLoteMedicamento`, `precioUnitario`, `motivo`, `fecha_registro`, `ultima_modificacion`) VALUES
(1, 3, 'Ibuprofeno', '', 21, 55, 10, 'Disponible', NULL, 1, 20.00, NULL, '2024-11-15', '2024-11-15 14:50:47'),
(2, 3, 'Paracetamol', '', 1, 15, 10, 'Disponible', NULL, 1, 25.00, NULL, '2024-11-15', '2024-11-15 14:55:25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mermasalimento`
--

CREATE TABLE `mermasalimento` (
  `idMerma` int(11) NOT NULL,
  `idAlimento` int(11) NOT NULL,
  `cantidadMerma` decimal(10,2) NOT NULL,
  `fechaMerma` datetime DEFAULT current_timestamp(),
  `motivo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mermasalimento`
--

INSERT INTO `mermasalimento` (`idMerma`, `idAlimento`, `cantidadMerma`, `fechaMerma`, `motivo`) VALUES
(1, 2, 1.00, '2024-11-15 09:56:58', 'Merma registrada en salida de inventario'),
(2, 2, 5.00, '2024-11-15 10:00:29', 'Merma registrada en salida de inventario');

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
(1, 'campos', '2024-11-15 09:42:21'),
(2, 'equinos', '2024-11-15 09:42:21'),
(3, 'historialMedico', '2024-11-15 09:42:21'),
(4, 'inventarios', '2024-11-15 09:42:21'),
(5, 'servicios', '2024-11-15 09:42:21'),
(6, 'usuarios', '2024-11-15 09:42:21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nacionalidades`
--

CREATE TABLE `nacionalidades` (
  `idNacionalidad` int(11) NOT NULL,
  `nacionalidad` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `nacionalidades`
--

INSERT INTO `nacionalidades` (`idNacionalidad`, `nacionalidad`) VALUES
(1, 'Afgana'),
(2, 'Alemana'),
(3, 'Andorrana'),
(4, 'Angoleña'),
(5, 'Antiguana'),
(6, 'Árabe'),
(7, 'Argelina'),
(8, 'Argentina'),
(9, 'Armenia'),
(10, 'Arubeña'),
(11, 'Australiana'),
(12, 'Austriaca'),
(13, 'Azerbaiyana'),
(14, 'Bahameña'),
(15, 'Bahreiní'),
(16, 'Bangladesí'),
(17, 'Barbadense'),
(18, 'Belga'),
(19, 'Beliceña'),
(20, 'Beninesa'),
(21, 'Bermudeña'),
(22, 'Bielorrusa'),
(23, 'Boliviana'),
(24, 'Bosnia'),
(25, 'Botsuana'),
(26, 'Brasileña'),
(27, 'Bruneana'),
(28, 'Búlgara'),
(29, 'Burkinesa'),
(30, 'Burundesa'),
(31, 'Butanesa'),
(32, 'Caboverdiana'),
(33, 'Camboyana'),
(34, 'Camerunesa'),
(35, 'Canadiense'),
(36, 'Centroafricana'),
(37, 'Chadiana'),
(38, 'Checa'),
(39, 'Chilena'),
(40, 'China'),
(41, 'Chipriota'),
(42, 'Colombiana'),
(43, 'Comorense'),
(44, 'Congoleña'),
(45, 'Costarricense'),
(46, 'Croata'),
(47, 'Cubana'),
(48, 'Danesa'),
(49, 'Dominicana'),
(50, 'Ecuatoriana'),
(51, 'Egipcia'),
(52, 'Emiratí'),
(53, 'Eritrea'),
(54, 'Eslovaca'),
(55, 'Eslovena'),
(56, 'Española'),
(57, 'Estadounidense'),
(58, 'Estonia'),
(59, 'Etíope'),
(60, 'Filipina'),
(61, 'Finlandesa'),
(62, 'Fiyiana'),
(63, 'Francesa'),
(64, 'Gabonesa'),
(65, 'Galesa'),
(66, 'Gambiana'),
(67, 'Georgiana'),
(68, 'Ghanesa'),
(69, 'Granadina'),
(70, 'Griega'),
(71, 'Guatemalteca'),
(72, 'Guineana'),
(73, 'Guyanesa'),
(74, 'Haitiana'),
(75, 'Hondureña'),
(76, 'Húngara'),
(77, 'India'),
(78, 'Indonesia'),
(79, 'Iraquí'),
(80, 'Iraní'),
(81, 'Irlandesa'),
(82, 'Islandesa'),
(83, 'Israelí'),
(84, 'Italiana'),
(85, 'Jamaiquina'),
(86, 'Japonesa'),
(87, 'Jordana'),
(88, 'Kazaja'),
(89, 'Keniana'),
(90, 'Kirguisa'),
(91, 'Kiribatiana'),
(92, 'Kosovar'),
(93, 'Kuwaití'),
(94, 'Laosiana'),
(95, 'Lesotense'),
(96, 'Letona'),
(97, 'Libanesa'),
(98, 'Liberiana'),
(99, 'Libia'),
(100, 'Liechtensteiniana'),
(101, 'Lituana'),
(102, 'Luxemburguesa'),
(103, 'Macedonia'),
(104, 'Malaya'),
(105, 'Malauí'),
(106, 'Maldiva'),
(107, 'Malgache'),
(108, 'Maliense'),
(109, 'Maltesa'),
(110, 'Marfileña'),
(111, 'Marroquí'),
(112, 'Marshallina'),
(113, 'Mauritana'),
(114, 'Mauriciana'),
(115, 'Mexicana'),
(116, 'Micronesia'),
(117, 'Moldava'),
(118, 'Monegasca'),
(119, 'Mongola'),
(120, 'Montenegrina'),
(121, 'Mozambiqueña'),
(122, 'Namibia'),
(123, 'Neerlandesa'),
(124, 'Nepalí'),
(125, 'Nicaragüense'),
(126, 'Nigeriana'),
(127, 'Nigerina'),
(128, 'Norcoreana'),
(129, 'Noruega'),
(130, 'Nueva Zelandesa'),
(131, 'Omaní'),
(132, 'Pakistaní'),
(133, 'Palauana'),
(134, 'Panameña'),
(135, 'Papú'),
(136, 'Paraguaya'),
(137, 'Peruana'),
(138, 'Polaca'),
(139, 'Portuguesa'),
(140, 'Puertorriqueña'),
(141, 'Qatarí'),
(142, 'Reino Unido'),
(143, 'Rumana'),
(144, 'Rusa'),
(145, 'Ruandesa'),
(146, 'Salvadoreña'),
(147, 'Samoana'),
(148, 'Sanmarinense'),
(149, 'Santa Lucía'),
(150, 'Saudí'),
(151, 'Senegalesa'),
(152, 'Serbia'),
(153, 'Seychellense'),
(154, 'Sierraleonesa'),
(155, 'Singapurense'),
(156, 'Somalí'),
(157, 'Sri Lanka'),
(158, 'Sudafricana'),
(159, 'Sudanesa'),
(160, 'Sueca'),
(161, 'Suiza'),
(162, 'Surcoreana'),
(163, 'Surinamesa'),
(164, 'Tailandesa'),
(165, 'Tanzana'),
(166, 'Togolesa'),
(167, 'Tongana'),
(168, 'Trinitaria'),
(169, 'Tunecina'),
(170, 'Turca'),
(171, 'Tuvaluana'),
(172, 'Ucraniana'),
(173, 'Ugandesa'),
(174, 'Uruguaya'),
(175, 'Uzbeca'),
(176, 'Vanuatuense'),
(177, 'Venezolana'),
(178, 'Vietnamita'),
(179, 'Yemení'),
(180, 'Yibutiana'),
(181, 'Zambiana'),
(182, 'Zimbabuense');

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
(1, 1, 1, '2024-11-15 09:42:21'),
(2, 1, 4, '2024-11-15 09:42:21'),
(3, 1, 17, '2024-11-15 09:42:21'),
(4, 2, 1, '2024-11-15 09:42:21'),
(5, 2, 4, '2024-11-15 09:42:21'),
(6, 2, 17, '2024-11-15 09:42:21'),
(7, 3, 1, '2024-11-15 09:42:21'),
(8, 3, 4, '2024-11-15 09:42:21'),
(9, 3, 5, '2024-11-15 09:42:21'),
(10, 3, 8, '2024-11-15 09:42:21'),
(11, 3, 9, '2024-11-15 09:42:21'),
(12, 3, 10, '2024-11-15 09:42:21'),
(13, 3, 11, '2024-11-15 09:42:21'),
(14, 3, 12, '2024-11-15 09:42:21'),
(15, 3, 14, '2024-11-15 09:42:21'),
(16, 3, 15, '2024-11-15 09:42:21'),
(17, 3, 16, '2024-11-15 09:42:21'),
(18, 3, 17, '2024-11-15 09:42:21'),
(19, 3, 18, '2024-11-15 09:42:21'),
(20, 4, 1, '2024-11-15 09:42:21'),
(21, 4, 2, '2024-11-15 09:42:21'),
(22, 4, 3, '2024-11-15 09:42:21'),
(23, 4, 6, '2024-11-15 09:42:21'),
(24, 4, 7, '2024-11-15 09:42:21'),
(25, 4, 13, '2024-11-15 09:42:21'),
(26, 5, 1, '2024-11-15 09:42:21'),
(27, 5, 9, '2024-11-15 09:42:21'),
(28, 6, 1, '2024-11-15 09:42:21'),
(29, 6, 4, '2024-11-15 09:42:21'),
(30, 6, 14, '2024-11-15 09:42:21');

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `idServicio` int(11) NOT NULL,
  `idEquinoMacho` int(11) DEFAULT NULL,
  `idEquinoHembra` int(11) DEFAULT NULL,
  `idEquinoExterno` int(11) DEFAULT NULL,
  `fechaServicio` date NOT NULL,
  `tipoServicio` enum('Propio','Mixto') NOT NULL,
  `detalles` text NOT NULL,
  `idMedicamento` int(11) DEFAULT NULL,
  `horaEntrada` time DEFAULT NULL,
  `horaSalida` time DEFAULT NULL,
  `idPropietario` int(11) DEFAULT NULL,
  `idEstadoMonta` int(11) NOT NULL,
  `costoServicio` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`idServicio`, `idEquinoMacho`, `idEquinoHembra`, `idEquinoExterno`, `fechaServicio`, `tipoServicio`, `detalles`, `idMedicamento`, `horaEntrada`, `horaSalida`, `idPropietario`, `idEstadoMonta`, `costoServicio`) VALUES
(1, 1, 2, NULL, '2024-11-15', 'Propio', '', NULL, NULL, NULL, NULL, 0, NULL),
(2, 1, NULL, 3, '2024-11-15', 'Mixto', '', NULL, '06:05:00', '06:15:00', 1, 0, 3500.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipoalimentos`
--

CREATE TABLE `tipoalimentos` (
  `idTipoAlimento` int(11) NOT NULL,
  `tipoAlimento` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipoalimentos`
--

INSERT INTO `tipoalimentos` (`idTipoAlimento`, `tipoAlimento`) VALUES
(10, 'Alimentos Especializados para Caballos Deportivos'),
(8, 'Complementos Nutricionales'),
(7, 'Fibras'),
(1, 'Forrajes'),
(2, 'Granos y Cereales'),
(6, 'Heno y Pasto Preservado'),
(9, 'Hierbas Medicinales'),
(5, 'Proteínas y Energéticos'),
(4, 'Subproductos de la Agricultura'),
(3, 'Suplementos y Concentrados');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipoalimento_unidadmedida`
--

CREATE TABLE `tipoalimento_unidadmedida` (
  `idTipoAlimento` int(11) NOT NULL,
  `idUnidadMedida` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipoalimento_unidadmedida`
--

INSERT INTO `tipoalimento_unidadmedida` (`idTipoAlimento`, `idUnidadMedida`) VALUES
(1, 1),
(1, 3),
(1, 6),
(1, 8),
(2, 1),
(2, 2),
(2, 3),
(2, 9),
(3, 1),
(3, 2),
(3, 10),
(3, 13),
(4, 1),
(4, 2),
(4, 9),
(5, 1),
(5, 4),
(5, 5),
(6, 1),
(6, 6),
(6, 8),
(7, 1),
(7, 2),
(7, 8),
(8, 1),
(8, 2),
(8, 15),
(9, 2),
(9, 11),
(10, 1),
(10, 4),
(10, 14),
(10, 16);

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
-- Estructura de tabla para la tabla `unidadesmedida`
--

CREATE TABLE `unidadesmedida` (
  `idUnidad` int(11) NOT NULL,
  `unidad` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `unidadesmedida`
--

INSERT INTO `unidadesmedida` (`idUnidad`, `unidad`) VALUES
(6, 'dL'),
(5, 'fL'),
(3, 'g'),
(7, 'L'),
(4, 'mcg'),
(8, 'mcl'),
(9, 'mcmol'),
(10, 'mEq'),
(1, 'mg'),
(2, 'ml'),
(11, 'mm'),
(12, 'mm Hg'),
(13, 'mmol'),
(14, 'mOsm'),
(16, 'mU'),
(15, 'mUI'),
(17, 'ng'),
(18, 'nmol'),
(19, 'pg'),
(20, 'pmol'),
(22, 'U'),
(21, 'UI');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidadesmedidaalimento`
--

CREATE TABLE `unidadesmedidaalimento` (
  `idUnidadMedida` int(11) NOT NULL,
  `nombreUnidad` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `unidadesmedidaalimento`
--

INSERT INTO `unidadesmedidaalimento` (`idUnidadMedida`, `nombreUnidad`) VALUES
(10, 'bloque'),
(14, 'cápsula'),
(12, 'cc'),
(7, 'cubeta'),
(16, 'dosificado'),
(8, 'fardo'),
(2, 'g'),
(1, 'kg'),
(4, 'L'),
(11, 'mg'),
(5, 'ml'),
(6, 'paca'),
(15, 'ración'),
(9, 'saco'),
(3, 't'),
(13, 'tableta');

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
(8, 2, 'historial-equino', 'S', 'Historial Equinos', 'fas fa-history'),
(9, 3, 'diagnosticar-equino', 'S', 'Diagnóstico', 'fa-solid fa-file-wav'),
(10, 4, 'administrar-alimento', 'S', 'Alimentos', 'fas fa-apple-alt'),
(11, 4, 'administrar-medicamento', 'S', 'Medicamentos', 'fas fa-pills'),
(12, 4, 'registrar-implementos-caballos', 'S', 'Implementos Caballos', 'fa-solid fa-scissors'),
(13, 4, 'registrar-implementos-campos', 'S', 'Implementos Campos', 'fa-solid fa-wrench'),
(14, 4, 'administrar-herramienta', 'S', 'Herrero', 'fas fa-wrench'),
(15, 5, 'servir-propio', 'S', 'Servicio Propio', 'fas fa-tools'),
(16, 5, 'servir-mixto', 'S', 'Servicio Mixto', 'fas fa-exchange-alt'),
(17, 5, 'listar-servicio', 'S', 'Listado Servicios', 'fa-solid fa-list'),
(18, 6, 'registrar-personal', 'S', 'Registrar Personal', 'fa-solid fa-wallet');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alimentos`
--
ALTER TABLE `alimentos`
  ADD PRIMARY KEY (`idAlimento`),
  ADD KEY `fk_alimento_usuario` (`idUsuario`),
  ADD KEY `fk_alimento_tipoalimento` (`idTipoAlimento`),
  ADD KEY `fk_alimento_unidadmedida` (`idUnidadMedida`),
  ADD KEY `fk_alimento_lote` (`idLote`),
  ADD KEY `fk_alimento_equino` (`idEquino`);

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
-- Indices de la tabla `campos`
--
ALTER TABLE `campos`
  ADD PRIMARY KEY (`idCampo`);

--
-- Indices de la tabla `combinacionesmedicamentos`
--
ALTER TABLE `combinacionesmedicamentos`
  ADD PRIMARY KEY (`idCombinacion`),
  ADD UNIQUE KEY `idTipo` (`idTipo`,`idPresentacion`,`dosis`,`idUnidad`),
  ADD KEY `idPresentacion` (`idPresentacion`),
  ADD KEY `idUnidad` (`idUnidad`);

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
  ADD KEY `fk_equino_estado_monta` (`idEstadoMonta`),
  ADD KEY `fk_equino_nacionalidad` (`idNacionalidad`);

--
-- Indices de la tabla `estadoherramienta`
--
ALTER TABLE `estadoherramienta`
  ADD PRIMARY KEY (`idEstado`);

--
-- Indices de la tabla `estadomonta`
--
ALTER TABLE `estadomonta`
  ADD PRIMARY KEY (`idEstadoMonta`);

--
-- Indices de la tabla `herramientasusadashistorial`
--
ALTER TABLE `herramientasusadashistorial`
  ADD PRIMARY KEY (`idHerramientasUsadas`),
  ADD KEY `fk_herramienta_historial` (`idHistorialHerrero`);

--
-- Indices de la tabla `historialequinos`
--
ALTER TABLE `historialequinos`
  ADD PRIMARY KEY (`idHistorial`),
  ADD KEY `fk_campanapotrillos_equino` (`idEquino`);

--
-- Indices de la tabla `historialherrero`
--
ALTER TABLE `historialherrero`
  ADD PRIMARY KEY (`idHistorialHerrero`),
  ADD KEY `fk_historialherrero_equino` (`idEquino`),
  ADD KEY `fk_historialherrero_usuario` (`idUsuario`);

--
-- Indices de la tabla `historialimplemento`
--
ALTER TABLE `historialimplemento`
  ADD PRIMARY KEY (`idHistorial`),
  ADD KEY `fk_historial_inventario` (`idInventario`),
  ADD KEY `fk_historial_tipoinventario` (`idTipoinventario`);

--
-- Indices de la tabla `historialmovimientos`
--
ALTER TABLE `historialmovimientos`
  ADD PRIMARY KEY (`idMovimiento`),
  ADD KEY `idAlimento` (`idAlimento`),
  ADD KEY `idEquino` (`idEquino`),
  ADD KEY `idUsuario` (`idUsuario`);

--
-- Indices de la tabla `historialmovimientosmedicamentos`
--
ALTER TABLE `historialmovimientosmedicamentos`
  ADD PRIMARY KEY (`idMovimiento`),
  ADD KEY `idMedicamento` (`idMedicamento`),
  ADD KEY `fk_historialmedicamentos_equino` (`idEquino`),
  ADD KEY `idUsuario` (`idUsuario`);

--
-- Indices de la tabla `implementos`
--
ALTER TABLE `implementos`
  ADD PRIMARY KEY (`idInventario`),
  ADD UNIQUE KEY `fk_implemento_nombreProducto` (`nombreProducto`),
  ADD KEY `fk_implemento_inventario` (`idTipoinventario`),
  ADD KEY `fk_implemento_movimiento` (`idTipomovimiento`);

--
-- Indices de la tabla `lotesalimento`
--
ALTER TABLE `lotesalimento`
  ADD PRIMARY KEY (`idLote`);

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
  ADD KEY `fk_medicamento_equino` (`idEquino`);

--
-- Indices de la tabla `mermasalimento`
--
ALTER TABLE `mermasalimento`
  ADD PRIMARY KEY (`idMerma`),
  ADD KEY `fk_merma_alimento` (`idAlimento`);

--
-- Indices de la tabla `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`idmodulo`),
  ADD UNIQUE KEY `uk_modulo_mod` (`modulo`);

--
-- Indices de la tabla `nacionalidades`
--
ALTER TABLE `nacionalidades`
  ADD PRIMARY KEY (`idNacionalidad`);

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
  ADD KEY `fk_servicio_equino_externo` (`idEquinoExterno`),
  ADD KEY `fk_servicio_medicamento` (`idMedicamento`),
  ADD KEY `fk_servicio_propietario` (`idPropietario`);

--
-- Indices de la tabla `tipoalimentos`
--
ALTER TABLE `tipoalimentos`
  ADD PRIMARY KEY (`idTipoAlimento`),
  ADD UNIQUE KEY `tipoAlimento` (`tipoAlimento`);

--
-- Indices de la tabla `tipoalimento_unidadmedida`
--
ALTER TABLE `tipoalimento_unidadmedida`
  ADD PRIMARY KEY (`idTipoAlimento`,`idUnidadMedida`),
  ADD UNIQUE KEY `uq_tipo_unidad` (`idTipoAlimento`,`idUnidadMedida`),
  ADD KEY `fk_unidadmedida` (`idUnidadMedida`);

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
-- Indices de la tabla `unidadesmedida`
--
ALTER TABLE `unidadesmedida`
  ADD PRIMARY KEY (`idUnidad`),
  ADD UNIQUE KEY `unidad` (`unidad`);

--
-- Indices de la tabla `unidadesmedidaalimento`
--
ALTER TABLE `unidadesmedidaalimento`
  ADD PRIMARY KEY (`idUnidadMedida`),
  ADD UNIQUE KEY `nombreUnidad` (`nombreUnidad`);

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
  MODIFY `idAlimento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- AUTO_INCREMENT de la tabla `campos`
--
ALTER TABLE `campos`
  MODIFY `idCampo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `combinacionesmedicamentos`
--
ALTER TABLE `combinacionesmedicamentos`
  MODIFY `idCombinacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

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
  MODIFY `idEquino` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `estadoherramienta`
--
ALTER TABLE `estadoherramienta`
  MODIFY `idEstado` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estadomonta`
--
ALTER TABLE `estadomonta`
  MODIFY `idEstadoMonta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `herramientasusadashistorial`
--
ALTER TABLE `herramientasusadashistorial`
  MODIFY `idHerramientasUsadas` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historialequinos`
--
ALTER TABLE `historialequinos`
  MODIFY `idHistorial` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historialherrero`
--
ALTER TABLE `historialherrero`
  MODIFY `idHistorialHerrero` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historialimplemento`
--
ALTER TABLE `historialimplemento`
  MODIFY `idHistorial` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historialmovimientos`
--
ALTER TABLE `historialmovimientos`
  MODIFY `idMovimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `idLote` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `lotesmedicamento`
--
ALTER TABLE `lotesmedicamento`
  MODIFY `idLoteMedicamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `medicamentos`
--
ALTER TABLE `medicamentos`
  MODIFY `idMedicamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `mermasalimento`
--
ALTER TABLE `mermasalimento`
  MODIFY `idMerma` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `idmodulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `nacionalidades`
--
ALTER TABLE `nacionalidades`
  MODIFY `idNacionalidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=183;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `idpermiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

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
  MODIFY `idRotacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `idServicio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tipoalimentos`
--
ALTER TABLE `tipoalimentos`
  MODIFY `idTipoAlimento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
-- AUTO_INCREMENT de la tabla `unidadesmedida`
--
ALTER TABLE `unidadesmedida`
  MODIFY `idUnidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `unidadesmedidaalimento`
--
ALTER TABLE `unidadesmedidaalimento`
  MODIFY `idUnidadMedida` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

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
  ADD CONSTRAINT `fk_alimento_equino` FOREIGN KEY (`idEquino`) REFERENCES `equinos` (`idEquino`),
  ADD CONSTRAINT `fk_alimento_lote` FOREIGN KEY (`idLote`) REFERENCES `lotesalimento` (`idLote`),
  ADD CONSTRAINT `fk_alimento_tipoalimento` FOREIGN KEY (`idTipoAlimento`) REFERENCES `tipoalimentos` (`idTipoAlimento`),
  ADD CONSTRAINT `fk_alimento_unidadmedida` FOREIGN KEY (`idUnidadMedida`) REFERENCES `unidadesmedidaalimento` (`idUnidadMedida`),
  ADD CONSTRAINT `fk_alimento_usuario` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`);

--
-- Filtros para la tabla `asistenciapersonal`
--
ALTER TABLE `asistenciapersonal`
  ADD CONSTRAINT `fk_asistencia_personal` FOREIGN KEY (`idPersonal`) REFERENCES `personal` (`idPersonal`);

--
-- Filtros para la tabla `combinacionesmedicamentos`
--
ALTER TABLE `combinacionesmedicamentos`
  ADD CONSTRAINT `combinacionesmedicamentos_ibfk_1` FOREIGN KEY (`idTipo`) REFERENCES `tiposmedicamentos` (`idTipo`),
  ADD CONSTRAINT `combinacionesmedicamentos_ibfk_2` FOREIGN KEY (`idPresentacion`) REFERENCES `presentacionesmedicamentos` (`idPresentacion`),
  ADD CONSTRAINT `combinacionesmedicamentos_ibfk_3` FOREIGN KEY (`idUnidad`) REFERENCES `unidadesmedida` (`idUnidad`);

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
  ADD CONSTRAINT `fk_equino_estado_monta` FOREIGN KEY (`idEstadoMonta`) REFERENCES `estadomonta` (`idEstadoMonta`),
  ADD CONSTRAINT `fk_equino_nacionalidad` FOREIGN KEY (`idNacionalidad`) REFERENCES `nacionalidades` (`idNacionalidad`),
  ADD CONSTRAINT `fk_equino_propietario` FOREIGN KEY (`idPropietario`) REFERENCES `propietarios` (`idPropietario`),
  ADD CONSTRAINT `fk_equino_tipoequino` FOREIGN KEY (`idTipoEquino`) REFERENCES `tipoequinos` (`idTipoEquino`);

--
-- Filtros para la tabla `herramientasusadashistorial`
--
ALTER TABLE `herramientasusadashistorial`
  ADD CONSTRAINT `fk_herramienta_historial` FOREIGN KEY (`idHistorialHerrero`) REFERENCES `historialherrero` (`idHistorialHerrero`);

--
-- Filtros para la tabla `historialequinos`
--
ALTER TABLE `historialequinos`
  ADD CONSTRAINT `fk_campanapotrillos_equino` FOREIGN KEY (`idEquino`) REFERENCES `equinos` (`idEquino`);

--
-- Filtros para la tabla `historialherrero`
--
ALTER TABLE `historialherrero`
  ADD CONSTRAINT `fk_historialherrero_equino` FOREIGN KEY (`idEquino`) REFERENCES `equinos` (`idEquino`),
  ADD CONSTRAINT `fk_historialherrero_usuario` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`);

--
-- Filtros para la tabla `historialimplemento`
--
ALTER TABLE `historialimplemento`
  ADD CONSTRAINT `fk_historial_inventario` FOREIGN KEY (`idInventario`) REFERENCES `implementos` (`idInventario`),
  ADD CONSTRAINT `fk_historial_tipoinventario` FOREIGN KEY (`idTipoinventario`) REFERENCES `tipoinventarios` (`idTipoinventario`);

--
-- Filtros para la tabla `historialmovimientos`
--
ALTER TABLE `historialmovimientos`
  ADD CONSTRAINT `historialmovimientos_ibfk_1` FOREIGN KEY (`idAlimento`) REFERENCES `alimentos` (`idAlimento`),
  ADD CONSTRAINT `historialmovimientos_ibfk_2` FOREIGN KEY (`idEquino`) REFERENCES `equinos` (`idEquino`),
  ADD CONSTRAINT `historialmovimientos_ibfk_3` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`);

--
-- Filtros para la tabla `historialmovimientosmedicamentos`
--
ALTER TABLE `historialmovimientosmedicamentos`
  ADD CONSTRAINT `fk_historialmedicamentos_equino` FOREIGN KEY (`idEquino`) REFERENCES `equinos` (`idEquino`),
  ADD CONSTRAINT `historialmovimientosmedicamentos_ibfk_1` FOREIGN KEY (`idMedicamento`) REFERENCES `medicamentos` (`idMedicamento`),
  ADD CONSTRAINT `historialmovimientosmedicamentos_ibfk_2` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`);

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
  ADD CONSTRAINT `fk_medicamento_equino` FOREIGN KEY (`idEquino`) REFERENCES `equinos` (`idEquino`),
  ADD CONSTRAINT `fk_medicamento_lote` FOREIGN KEY (`idLoteMedicamento`) REFERENCES `lotesmedicamento` (`idLoteMedicamento`),
  ADD CONSTRAINT `fk_medicamento_usuario` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`);

--
-- Filtros para la tabla `mermasalimento`
--
ALTER TABLE `mermasalimento`
  ADD CONSTRAINT `fk_merma_alimento` FOREIGN KEY (`idAlimento`) REFERENCES `alimentos` (`idAlimento`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `fk_servicio_equino_externo` FOREIGN KEY (`idEquinoExterno`) REFERENCES `equinos` (`idEquino`),
  ADD CONSTRAINT `fk_servicio_equino_hembra` FOREIGN KEY (`idEquinoHembra`) REFERENCES `equinos` (`idEquino`),
  ADD CONSTRAINT `fk_servicio_equino_macho` FOREIGN KEY (`idEquinoMacho`) REFERENCES `equinos` (`idEquino`),
  ADD CONSTRAINT `fk_servicio_medicamento` FOREIGN KEY (`idMedicamento`) REFERENCES `medicamentos` (`idMedicamento`),
  ADD CONSTRAINT `fk_servicio_propietario` FOREIGN KEY (`idPropietario`) REFERENCES `propietarios` (`idPropietario`);

--
-- Filtros para la tabla `tipoalimento_unidadmedida`
--
ALTER TABLE `tipoalimento_unidadmedida`
  ADD CONSTRAINT `fk_tipoalimento` FOREIGN KEY (`idTipoAlimento`) REFERENCES `tipoalimentos` (`idTipoAlimento`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_unidadmedida` FOREIGN KEY (`idUnidadMedida`) REFERENCES `unidadesmedidaalimento` (`idUnidadMedida`) ON DELETE CASCADE;

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
