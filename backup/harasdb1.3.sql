-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-01-2025 a las 06:50:36
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
CREATE DEFINER=`root`@`localhost` PROCEDURE `ConsultarHistorialEquino` ()   BEGIN
    SELECT 
        HH.idHistorialHerrero, 
        HH.fecha, 
        TT.nombreTrabajo AS TrabajoRealizado, 
        GROUP_CONCAT(H.nombreHerramienta SEPARATOR ', ') AS HerramientasUsadas, 
        HH.observaciones,
        E.nombreEquino,              
        TE.tipoEquino                 
    FROM 
        HistorialHerrero HH
    INNER JOIN 
        Equinos E ON HH.idEquino = E.idEquino
    INNER JOIN 
        TipoEquinos TE ON E.idTipoEquino = TE.idTipoEquino
    INNER JOIN 
        TiposTrabajos TT ON HH.idTrabajo = TT.idTipoTrabajo
    LEFT JOIN 
        HerramientasUsadasHistorial HUH ON HH.idHistorialHerrero = HUH.idHistorialHerrero
    LEFT JOIN 
        Herramientas H ON HUH.idHerramienta = H.idHerramienta
    GROUP BY 
        HH.idHistorialHerrero, 
        HH.fecha, 
        TT.nombreTrabajo, 
        HH.observaciones, 
        E.nombreEquino, 
        TE.tipoEquino
    ORDER BY 
        HH.fecha DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `EditarCombinacionAlimento` (IN `p_IdTipoActual` INT, IN `p_IdUnidadActual` INT, IN `p_NuevoTipo` VARCHAR(50), IN `p_NuevaUnidad` VARCHAR(10))   BEGIN
    DECLARE v_IdNuevoTipo INT;
    DECLARE v_IdNuevaUnidad INT;

    -- Paso 1: Verificar o insertar el nuevo tipo de alimento
    SELECT idTipoAlimento INTO v_IdNuevoTipo
    FROM TipoAlimentos
    WHERE tipoAlimento = p_NuevoTipo;

    IF v_IdNuevoTipo IS NULL THEN
        INSERT INTO TipoAlimentos (tipoAlimento)
        VALUES (p_NuevoTipo);
        SET v_IdNuevoTipo = LAST_INSERT_ID();
    END IF;

    -- Paso 2: Verificar o insertar la nueva unidad de medida
    SELECT idUnidadMedida INTO v_IdNuevaUnidad
    FROM UnidadesMedidaAlimento
    WHERE nombreUnidad = p_NuevaUnidad;

    IF v_IdNuevaUnidad IS NULL THEN
        INSERT INTO UnidadesMedidaAlimento (nombreUnidad)
        VALUES (p_NuevaUnidad);
        SET v_IdNuevaUnidad = LAST_INSERT_ID();
    END IF;

    -- Paso 3: Actualizar la combinación específica en TipoAlimento_UnidadMedida
    UPDATE TipoAlimento_UnidadMedida
    SET idTipoAlimento = v_IdNuevoTipo, idUnidadMedida = v_IdNuevaUnidad
    WHERE idTipoAlimento = p_IdTipoActual AND idUnidadMedida = p_IdUnidadActual;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `FiltrarHistorialHerreroPorTipoEquino` (IN `_tipoEquino` VARCHAR(50))   BEGIN
    SELECT 
        HH.idHistorialHerrero, 
        HH.fecha, 
        TT.nombreTrabajo AS TrabajoRealizado, 
        GROUP_CONCAT(H.nombreHerramienta SEPARATOR ', ') AS HerramientasUsadas, 
        HH.observaciones,
        E.nombreEquino,              
        TE.tipoEquino                 
    FROM 
        HistorialHerrero HH
    INNER JOIN 
        Equinos E ON HH.idEquino = E.idEquino
    INNER JOIN 
        TipoEquinos TE ON E.idTipoEquino = TE.idTipoEquino
    INNER JOIN 
        TiposTrabajos TT ON HH.idTrabajo = TT.idTipoTrabajo
    LEFT JOIN 
        HerramientasUsadasHistorial HUH ON HH.idHistorialHerrero = HUH.idHistorialHerrero
    LEFT JOIN 
        Herramientas H ON HUH.idHerramienta = H.idHerramienta
    WHERE 
        TE.tipoEquino = _tipoEquino
        AND TE.tipoEquino IN ('Padrillo', 'Yegua', 'Potrillo', 'Potranca')
    GROUP BY 
        HH.idHistorialHerrero, 
        HH.fecha, 
        TT.nombreTrabajo, 
        HH.observaciones, 
        E.nombreEquino, 
        TE.tipoEquino
    ORDER BY 
        HH.fecha DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertarHistorialHerrero` (IN `p_idEquino` INT, IN `p_idUsuario` INT, IN `p_fecha` DATE, IN `p_idTrabajo` INT, IN `p_idHerramienta` INT, IN `p_observaciones` TEXT)   BEGIN
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error al insertar en HistorialHerrero';
    END;

    -- Validación de entrada
    IF p_idEquino IS NULL OR p_idUsuario IS NULL OR p_fecha IS NULL OR p_idTrabajo IS NULL OR p_idHerramienta IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Campos obligatorios faltantes para la inserción.';
    END IF;

    -- Iniciar una transacción
    START TRANSACTION;

    -- Inserción en la tabla HistorialHerrero
    INSERT INTO HistorialHerrero (
        idEquino, idUsuario, fecha, idTrabajo, observaciones
    ) VALUES (
        p_idEquino, p_idUsuario, p_fecha, p_idTrabajo, p_observaciones
    );

    -- Obtener el ID generado para HistorialHerrero
    SET @idHistorialHerrero = LAST_INSERT_ID();

    -- Inserción en la tabla HerramientasUsadasHistorial
    INSERT INTO HerramientasUsadasHistorial (
        idHistorialHerrero, idHerramienta
    ) VALUES (
        @idHistorialHerrero, p_idHerramienta
    );

    -- Confirmar la transacción
    COMMIT;
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
    SELECT 
        SUM(cantidad_stock) AS stock_total,
        COUNT(*) AS cantidad_medicamentos,
        GROUP_CONCAT(CASE WHEN cantidad_stock <= stockMinimo THEN CONCAT(nombreMedicamento, ' (', cantidad_stock, ')') ELSE NULL END) AS criticos,
        GROUP_CONCAT(CASE WHEN cantidad_stock > stockMinimo THEN CONCAT(nombreMedicamento, ' (', cantidad_stock, ')') ELSE NULL END) AS en_stock,
        COUNT(CASE WHEN cantidad_stock <= stockMinimo THEN 1 ELSE NULL END) AS criticos_count,
        COUNT(CASE WHEN cantidad_stock > stockMinimo THEN 1 ELSE NULL END) AS en_stock_count
    FROM 
        Medicamentos;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ObtenerServiciosRealizadosMensual` (IN `p_meta` INT)   BEGIN
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `ObtenerSugerenciasAlimentos` ()   BEGIN
    SELECT 
        tal.idTipoAlimento AS IdTipoAlimento,
        tal.tipoAlimento AS TipoAlimento,
        uma.idUnidadMedida AS IdUnidadMedida,
        uma.nombreUnidad AS UnidadMedida
    FROM 
        TipoAlimento_UnidadMedida taum
    INNER JOIN 
        TipoAlimentos tal ON taum.idTipoAlimento = tal.idTipoAlimento
    INNER JOIN 
        UnidadesMedidaAlimento uma ON taum.idUnidadMedida = uma.idUnidadMedida
    ORDER BY 
        tal.tipoAlimento ASC, 
        uma.nombreUnidad ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ObtenerTotalEquinosRegistrados` ()   BEGIN
    SELECT COUNT(*) AS total_equinos 
    FROM Equinos 
    WHERE estado = 1
    AND idPropietario IS NULL;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `registrarServicio` (IN `p_idEquinoMacho` INT, IN `p_idEquinoHembra` INT, IN `p_idPropietario` INT, IN `p_idEquinoExterno` INT, IN `p_fechaServicio` DATE, IN `p_tipoServicio` ENUM('propio','mixto'), IN `p_detalles` TEXT, IN `p_idMedicamento` INT, IN `p_horaEntrada` TIME, IN `p_horaSalida` TIME, IN `p_costoServicio` DECIMAL(10,2))   BEGIN
    DECLARE v_mensajeError VARCHAR(255);
    DECLARE v_idEstadoServida INT;
    DECLARE v_idEstadoActivo INT;
    DECLARE v_idEstadoSS INT;
    DECLARE v_horaActual TIME;

    -- Obtener los ID de estados correspondientes
    SELECT idEstadoMonta INTO v_idEstadoServida FROM EstadoMonta WHERE genero = 'Hembra' AND nombreEstado = 'Servida' LIMIT 1;
    SELECT idEstadoMonta INTO v_idEstadoActivo FROM EstadoMonta WHERE genero = 'Macho' AND nombreEstado = 'Activo' LIMIT 1;
    SELECT idEstadoMonta INTO v_idEstadoSS FROM EstadoMonta WHERE genero = 'Hembra' AND nombreEstado = 'S/S' LIMIT 1;

    -- Verificar si la fecha de servicio es válida
    IF p_fechaServicio > CURDATE() THEN
        SET v_mensajeError = 'Error: La fecha de servicio no puede ser mayor que la fecha actual.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
    END IF;

    -- Si la fecha de servicio es hoy, verificar que la hora de entrada y salida no sean mayores a la hora actual
    IF p_fechaServicio = CURDATE() THEN
        SET v_horaActual = CURTIME();

        -- Verificar que la hora de entrada no sea mayor que la hora de salida
        IF p_horaEntrada > p_horaSalida THEN
            SET v_mensajeError = 'Error: La hora de entrada no puede ser mayor que la hora de salida.';
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
        END IF;

        -- Verificar que la hora de entrada y la hora de salida no sean mayores que la hora actual
        IF p_horaEntrada > v_horaActual OR p_horaSalida > v_horaActual THEN
            SET v_mensajeError = 'Error: La hora de entrada y la hora de salida no pueden ser mayores a la hora actual.';
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
        END IF;
    END IF;

    -- Validar duplicados para yeguas propias
    IF p_idEquinoHembra IS NOT NULL THEN
        IF EXISTS (
            SELECT 1
            FROM Servicios
            WHERE idEquinoHembra = p_idEquinoHembra
              AND fechaServicio = p_fechaServicio
        ) THEN
            SET v_mensajeError = 'Error: La yegua propia ya recibió un servicio en esta fecha.';
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
        END IF;
    END IF;

    -- Validar duplicados para yeguas externas
    IF p_idEquinoExterno IS NOT NULL THEN
        IF EXISTS (
            SELECT 1
            FROM Servicios
            WHERE idEquinoExterno = p_idEquinoExterno
              AND fechaServicio = p_fechaServicio
        ) THEN
            SET v_mensajeError = 'Error: La yegua externa ya recibió un servicio en esta fecha.';
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
        END IF;
    END IF;

    -- Registrar el servicio
    IF p_tipoServicio = 'propio' THEN
        INSERT INTO Servicios (
            idEquinoMacho, idEquinoHembra, fechaServicio, tipoServicio, detalles, idMedicamento, horaEntrada, horaSalida, idPropietario, costoServicio
        ) VALUES (
            p_idEquinoMacho, p_idEquinoHembra, p_fechaServicio, p_tipoServicio, p_detalles, p_idMedicamento, p_horaEntrada, p_horaSalida, NULL, p_costoServicio
        );

        -- Cambiar estado del macho a "Activo"
        UPDATE Equinos
        SET idEstadoMonta = v_idEstadoActivo
        WHERE idEquino = p_idEquinoMacho;

        -- Cambiar estado de la yegua propia a "Servida"
        UPDATE Equinos
        SET idEstadoMonta = v_idEstadoServida
        WHERE idEquino = p_idEquinoHembra;

    ELSEIF p_tipoServicio = 'mixto' THEN
        INSERT INTO Servicios (
            idEquinoMacho, idEquinoHembra, idEquinoExterno, fechaServicio, tipoServicio, detalles, idMedicamento, horaEntrada, horaSalida, idPropietario, costoServicio
        ) VALUES (
            p_idEquinoMacho, p_idEquinoHembra, p_idEquinoExterno, p_fechaServicio, p_tipoServicio, p_detalles, p_idMedicamento, p_horaEntrada, p_horaSalida, p_idPropietario, p_costoServicio
        );

        -- Cambiar estado del macho a "Activo"
        UPDATE Equinos
        SET idEstadoMonta = v_idEstadoActivo
        WHERE idEquino = p_idEquinoMacho;

        -- Cambiar estado de la yegua propia a "Servida" si aplica
        IF p_idEquinoHembra IS NOT NULL THEN
            UPDATE Equinos
            SET idEstadoMonta = v_idEstadoServida
            WHERE idEquino = p_idEquinoHembra;
        END IF;

        -- Cambiar estado de la yegua externa a "Servida" si aplica
        IF p_idEquinoExterno IS NOT NULL THEN
            UPDATE Equinos
            SET idEstadoMonta = v_idEstadoServida
            WHERE idEquino = p_idEquinoExterno;
        END IF;
    END IF;

    -- Actualizar el estado de todas las yeguas no servidas recientemente a "S/S"
    UPDATE Equinos
    SET idEstadoMonta = v_idEstadoSS
    WHERE sexo = 'Hembra'
      AND idEquino NOT IN (
          SELECT idEquinoHembra
          FROM Servicios
          WHERE fechaServicio = p_fechaServicio
      );

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_actualizar_contraseña` (IN `_correo` VARCHAR(120), IN `p_clave` VARCHAR(120))   BEGIN 
    UPDATE usuarios
    SET clave = p_clave
    WHERE correo = _correo;
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_agregar_herramienta` (IN `_nombreHerramienta` VARCHAR(100))   BEGIN
    -- Verificar que no exista una herramienta con el mismo nombre
    IF EXISTS (SELECT 1 FROM Herramientas WHERE nombreHerramienta = _nombreHerramienta) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La herramienta ya existe.';
    ELSE
        -- Insertar la nueva herramienta
        INSERT INTO Herramientas (nombreHerramienta)
        VALUES (_nombreHerramienta);
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_agregar_tipo_trabajo` (IN `_nombreTrabajo` VARCHAR(100))   BEGIN
    -- Verificar que no exista un tipo de trabajo con el mismo nombre
    IF EXISTS (SELECT 1 FROM TiposTrabajos WHERE nombreTrabajo = _nombreTrabajo) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El tipo de trabajo ya existe.';
    ELSE
        -- Insertar el nuevo tipo de trabajo
        INSERT INTO TiposTrabajos (nombreTrabajo)
        VALUES (_nombreTrabajo);
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_Agregar_Via_Administracion` (IN `p_nombreVia` VARCHAR(50), IN `p_descripcion` TEXT)   BEGIN
    -- Verificar si ya existe una vía con el mismo nombre
    IF EXISTS (SELECT 1 FROM ViasAdministracion WHERE nombreVia = p_nombreVia) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Ya existe una vía de administración con este nombre.';
    ELSE
        -- Insertar la nueva vía en la tabla
        INSERT INTO ViasAdministracion (nombreVia, descripcion)
        VALUES (p_nombreVia, p_descripcion);
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
    DECLARE _fechaCaducidadLote DATE;
    DECLARE _estado ENUM('Disponible', 'Por agotarse', 'Agotado');
    DECLARE _estadoLote ENUM('Vencido', 'No vencido', 'Agotado');
    DECLARE _mensajeLote VARCHAR(255); -- Variable para el mensaje

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

    -- Verificar si el lote ya está registrado
    SELECT idLote, fechaCaducidad INTO _idLote, _fechaCaducidadLote 
    FROM LotesAlimento
    WHERE lote = _lote
    LIMIT 1;

    -- Si el lote ya existe y está vencido o agotado, actualizar la fecha de caducidad y el estado
    IF _idLote IS NOT NULL THEN
        IF _fechaCaducidadLote IS NOT NULL AND _fechaCaducidadLote < CURDATE() THEN
            -- El lote está vencido, actualizar la fecha de caducidad y el estado a 'No vencido'
            UPDATE LotesAlimento
            SET fechaCaducidad = _fechaCaducidad, estadoLote = 'No vencido'
            WHERE idLote = _idLote;
        END IF;
    ELSE
        -- Si el lote no existe, registrarlo en la tabla LotesAlimento
        INSERT INTO LotesAlimento (lote, fechaCaducidad, fechaIngreso) 
        VALUES (_lote, _fechaCaducidad, NOW());
        SET _idLote = LAST_INSERT_ID();
    END IF;

    -- Registrar el alimento
    INSERT INTO Alimentos (
        idUsuario, nombreAlimento, idTipoAlimento, idUnidadMedida, idLote, costo, 
        stockActual, stockMinimo, estado, fechaMovimiento, compra
    ) 
    VALUES (
        _idUsuario, _nombreAlimento, _idTipoAlimento, _idUnidadMedida, _idLote, _costo, 
        _stockActual, _stockMinimo, _estado, NOW(), _costo * _stockActual
    );

    -- Confirmar la transacción
    COMMIT;

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

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_buscar_equino_por_nombre_general` (IN `p_nombreEquino` VARCHAR(100))   BEGIN
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
        e.nombreEquino = p_nombreEquino;
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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_contar_equinos_por_categoria` ()   BEGIN
    -- Consulta directa para obtener el representante único y la cantidad de equinos por categoría
    SELECT 
        CASE 
            WHEN te.tipoEquino = 'Yegua' AND em.nombreEstado = 'S/S' THEN 'Yegua Vacía'
            WHEN te.tipoEquino = 'Yegua' AND em.nombreEstado = 'Preñada' THEN 'Yegua Preñada'
            WHEN te.tipoEquino = 'Yegua' AND em.nombreEstado = 'Con Cria' THEN 'Yegua Con Cria'
            WHEN te.tipoEquino = 'Padrillo' AND em.nombreEstado = 'Activo' THEN 'Padrillo Activo'
            WHEN te.tipoEquino = 'Padrillo' AND em.nombreEstado = 'Inactivo' THEN 'Padrillo Inactivo'
            WHEN te.tipoEquino = 'Potranca' THEN 'Potranca'
            WHEN te.tipoEquino = 'Potrillo' THEN 'Potrillo'
        END AS Categoria,
        COUNT(DISTINCT e.idEquino) AS Cantidad,
        -- Selección de un representante único para la categoría (primer equino encontrado)
        MIN(e.idEquino) AS idEquino
    FROM 
        Equinos e
    JOIN 
        TipoEquinos te ON e.idTipoEquino = te.idTipoEquino
    LEFT JOIN 
        EstadoMonta em ON e.idEstadoMonta = em.idEstadoMonta
    WHERE 
        e.estado = 1  -- Solo los equinos vivos
    AND (
        (te.tipoEquino = 'Yegua' AND em.nombreEstado IN ('S/S', 'Preñada', 'Con Cria'))
        OR (te.tipoEquino = 'Padrillo' AND em.nombreEstado IN ('Activo', 'Inactivo'))
        OR te.tipoEquino IN ('Potranca', 'Potrillo')
    )
    GROUP BY 
        Categoria
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_equinos_listar` (IN `p_estadoMonta` INT)   BEGIN
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
        E.fotografia,
        CASE 
            WHEN E.estado = 1 THEN 'Vivo'
            WHEN E.estado = 0 THEN 'Muerto'
            ELSE 'Desconocido'
        END AS estadoDescriptivo,
        HE.descripcion AS descripcion
        
    FROM
        Equinos E
    LEFT JOIN TipoEquinos TE ON E.idTipoEquino = TE.idTipoEquino
    LEFT JOIN EstadoMonta EM ON E.idEstadoMonta = EM.idEstadoMonta
    LEFT JOIN nacionalidades N ON E.idNacionalidad = N.idNacionalidad
    LEFT JOIN HistorialEquinos HE ON E.idEquino = HE.idEquino
    WHERE
        E.idPropietario IS NULL
        AND (
            p_estadoMonta IS NULL 
            OR E.idEstadoMonta = p_estadoMonta
        )
    ORDER BY 
        E.estado DESC,
        E.idEquino DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_equino_editar` (IN `_idEquino` INT, IN `_idPropietario` INT, IN `_pesokg` DECIMAL(5,1), IN `_idEstadoMonta` VARCHAR(50), IN `_estado` ENUM('Vivo','Muerto'), IN `_fechaEntrada` DATE, IN `_fechaSalida` DATE)   BEGIN
    DECLARE _errorMsg VARCHAR(255);

    -- Verificar si el equino existe
    IF NOT EXISTS (SELECT 1 FROM Equinos WHERE idEquino = _idEquino) THEN
        SET _errorMsg = 'Error: No existe un equino con el ID proporcionado.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
    END IF;

    -- Iniciar una transacción
    START TRANSACTION;

    -- Actualizar solo los campos que no sean NULL o vacíos
    UPDATE Equinos
    SET 
        idPropietario = COALESCE(NULLIF(_idPropietario, 0), idPropietario),
        pesokg = COALESCE(NULLIF(_pesokg, 0), pesokg),
        idEstadoMonta = COALESCE(NULLIF(_idEstadoMonta, ''), idEstadoMonta),
        estado = COALESCE(NULLIF(_estado, ''), estado),
        -- Actualizar las fechas solo si no son NULL
        fechaentrada = COALESCE(NULLIF(_fechaEntrada, '0000-00-00'), fechaentrada),
        fechasalida = COALESCE(NULLIF(_fechaSalida, '0000-00-00'), fechasalida)
    WHERE idEquino = _idEquino;

    -- Validar si se actualizó correctamente
    IF ROW_COUNT() = 0 THEN
        SET _errorMsg = 'Error: No se realizaron cambios en el registro.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
    END IF;

    -- Confirmar los cambios
    COMMIT;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_equino_registrar` (IN `_nombreEquino` VARCHAR(100), IN `_fechaNacimiento` DATE, IN `_sexo` ENUM('Macho','Hembra'), IN `_detalles` TEXT, IN `_idTipoEquino` INT, IN `_idPropietario` INT, IN `_pesokg` DECIMAL(5,1), IN `_idNacionalidad` INT, IN `_public_id` VARCHAR(255), IN `_fechaentrada` DATE, IN `_fechasalida` DATE)   BEGIN
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
    
    -- Validar fechas de entrada y salida si no son NULL
    IF _fechaEntrada IS NOT NULL AND _fechaSalida IS NOT NULL THEN
        IF _fechaEntrada > _fechaSalida THEN
            SET _errorMsg = 'Error: La fecha de entrada no puede ser mayor a la fecha de salida.';
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
        END IF;
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
        estado,
        fechaentrada,
        fechasalida
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
        1,  -- Estado "Vivo" (1)
        _fechaentrada,
        _fechasalida
    );

    -- Obtener el ID del equino recién insertado
    SET _idEquino = LAST_INSERT_ID();
    -- Retornar el ID del equino registrado
    SELECT _idEquino AS idEquino;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_filtrarAlimentos` (IN `_fechaCaducidadInicio` DATE, IN `_fechaCaducidadFin` DATE, IN `_fechaRegistroInicio` DATETIME, IN `_fechaRegistroFin` DATETIME)   BEGIN
    -- Realizar la consulta de los alimentos con los filtros especificados
    SELECT 
        A.idAlimento,
        A.idUsuario,
        A.nombreAlimento,
        TA.tipoAlimento AS nombreTipoAlimento,
        A.stockActual,
        A.stockMinimo,
        A.estado,
        U.nombreUnidad AS unidadMedidaNombre,
        A.costo,
        A.idLote,
        A.idEquino,
        A.compra,
        A.fechaMovimiento,
        L.idLote AS loteId,
        L.lote,
        L.fechaCaducidad,
        L.fechaIngreso,
        L.estadoLote
    FROM 
        Alimentos A
    INNER JOIN 
        LotesAlimento L ON A.idLote = L.idLote
    INNER JOIN 
        TipoAlimentos TA ON A.idTipoAlimento = TA.idTipoAlimento
    INNER JOIN 
        UnidadesMedidaAlimento U ON A.idUnidadMedida = U.idUnidadMedida
    WHERE 
        (_fechaCaducidadInicio IS NULL OR L.fechaCaducidad >= _fechaCaducidadInicio)
        AND (_fechaCaducidadFin IS NULL OR L.fechaCaducidad <= _fechaCaducidadFin)
        AND (_fechaRegistroInicio IS NULL OR L.fechaIngreso >= _fechaRegistroInicio)
        AND (_fechaRegistroFin IS NULL OR L.fechaIngreso <= _fechaRegistroFin);

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_filtrar_historial_medicoMedi` (IN `_nombreEquino` VARCHAR(255), IN `_nombreMedicamento` VARCHAR(255), IN `_estadoTratamiento` VARCHAR(50))   BEGIN
    -- Desactivar el modo seguro temporalmente
    SET SQL_SAFE_UPDATES = 0;

    -- Actualizar el estado de los tratamientos en la tabla DetalleMedicamentos
    -- Cambiar a 'Finalizado' los tratamientos cuya fecha de fin ha pasado y que están en estado 'Activo'
    UPDATE DetalleMedicamentos
    SET estadoTratamiento = 'Finalizado'
    WHERE fechaFin < CURDATE() AND estadoTratamiento = 'Activo';

    -- Seleccionar la información detallada de los registros de historial médico con filtros aplicados
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
    WHERE 
        (E.nombreEquino LIKE CONCAT('%', _nombreEquino, '%') OR _nombreEquino IS NULL OR _nombreEquino = '')
        AND (M.nombreMedicamento LIKE CONCAT('%', _nombreMedicamento, '%') OR _nombreMedicamento IS NULL OR _nombreMedicamento = '')
        AND (DM.estadoTratamiento = _estadoTratamiento OR _estadoTratamiento IS NULL OR _estadoTratamiento = '')
    ORDER BY 
        DM.fechaInicio DESC;

    -- Reactivar el modo seguro
    SET SQL_SAFE_UPDATES = 1;
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_historial_completo` (IN `tipoMovimiento` VARCHAR(50), IN `filtroFecha` VARCHAR(20), IN `idUsuario` INT, IN `limite` INT, IN `desplazamiento` INT)   BEGIN
    DECLARE fechaInicio DATE;
    DECLARE fechaFin DATE;

    -- Establecer las fechas según el filtro seleccionado
    IF filtroFecha = 'hoy' THEN
        SET fechaInicio = CURDATE();
        SET fechaFin = CURDATE();
    ELSEIF filtroFecha = 'ultimaSemana' THEN
        SET fechaInicio = CURDATE() - INTERVAL 7 DAY;
        SET fechaFin = CURDATE();
    ELSEIF filtroFecha = 'ultimoMes' THEN
        SET fechaInicio = CURDATE() - INTERVAL 1 MONTH;
        SET fechaFin = CURDATE();
    ELSEIF filtroFecha = 'todos' THEN
        SET fechaInicio = '1900-01-01'; -- Fecha muy antigua para incluir todos los registros
        SET fechaFin = CURDATE();
    ELSE
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Filtro de fecha no válido.';
    END IF;

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
            h.idMovimiento AS ID,
            a.nombreAlimento AS Alimento,
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
            um.nombreUnidad AS Unidad,
            h.merma AS Merma,
            l.lote AS Lote,
            h.fechaMovimiento AS FechaSalida
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
            h.idMovimiento, Alimento, TipoEquino, Unidad, Lote, FechaSalida
        ORDER BY 
            h.fechaMovimiento DESC
        LIMIT 
            limite OFFSET desplazamiento;
    ELSE
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Tipo de movimiento no válido.';
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_historial_completo_medicamentos` (IN `tipoMovimiento` VARCHAR(50), IN `filtroFecha` VARCHAR(20), IN `idUsuario` INT, IN `limite` INT, IN `desplazamiento` INT)   BEGIN
    DECLARE fechaInicio DATE;
    DECLARE fechaFin DATE;

    -- Establecer las fechas según el filtro seleccionado
    IF filtroFecha = 'hoy' THEN
        SET fechaInicio = CURDATE();
        SET fechaFin = CURDATE();
    ELSEIF filtroFecha = 'ultimaSemana' THEN
        SET fechaInicio = CURDATE() - INTERVAL 7 DAY;
        SET fechaFin = CURDATE();
    ELSEIF filtroFecha = 'ultimoMes' THEN
        SET fechaInicio = CURDATE() - INTERVAL 1 MONTH;
        SET fechaFin = CURDATE();
    ELSEIF filtroFecha = 'todos' THEN
        SET fechaInicio = '1900-01-01'; -- Fecha muy antigua para incluir todos los registros
        SET fechaFin = CURDATE();
    ELSE
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Filtro de fecha no válido.';
    END IF;

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
            AND h.fechaMovimiento BETWEEN fechaInicio AND fechaFin
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
            AND h.fechaMovimiento BETWEEN fechaInicio AND fechaFin
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_ListarTiposYHerramientas` ()   BEGIN
    -- Combinar TiposTrabajos y Herramientas en un solo resultado con IDs únicos
    SELECT 
        CONCAT('T-', idTipoTrabajo) AS id, -- Prefijo 'T-' para TiposTrabajos
        nombreTrabajo AS nombre,
        'Tipo de Trabajo' AS tipo
    FROM 
        TiposTrabajos
    UNION ALL
    SELECT 
        CONCAT('H-', idHerramienta) AS id, -- Prefijo 'H-' para Herramientas
        nombreHerramienta AS nombre,
        'Herramienta' AS tipo
    FROM 
        Herramientas
    ORDER BY 
        nombre ASC; -- Ordena por nombre de forma ascendente
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_listar_equinos_externos` ()   BEGIN
	SELECT 
        e.idEquino,
        e.nombreEquino,
        e.sexo,
        t.TipoEquino,
        e.detalles,
        em.nombreEstado,
        n.nacionalidad,
        p.nombreHaras,
        e.fechaentrada,
        e.fechasalida
    FROM Equinos e
    LEFT JOIN TipoEquinos t ON e.idTipoEquino = t.idTipoEquino
    LEFT JOIN EstadoMonta em ON e.idEstadoMonta = em.idEstadoMonta
    LEFT JOIN Nacionalidades n ON e.idNacionalidad = n.idNacionalidad
    LEFT JOIN Propietarios p ON e.idPropietario = p.idPropietario
    WHERE e.idPropietario IS NOT NULL;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_listar_equinos_para_revision` (IN `p_idPropietario` INT)   BEGIN
    -- Si se pasa un idPropietario, listar las yeguas de ese propietario específico
    IF p_idPropietario IS NOT NULL THEN
        SELECT 
            idEquino, 
            nombreEquino
        FROM 
            Equinos
        WHERE 
            sexo = 'Hembra'
            AND idPropietario = p_idPropietario
            AND estado = 1;

    -- Si no se pasa un idPropietario (NULL), listar las yeguas sin propietario
    ELSE
        SELECT 
            idEquino, 
            nombreEquino
        FROM 
            Equinos
        WHERE 
            sexo = 'Hembra'
            AND idPropietario IS NULL
            AND estado = 1;
    END IF;
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
		idPropietario IS NULL
		AND idTipoEquino IN (1, 2, 3, 4)
		AND estado = 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_listar_fotografias_equinos` (IN `p_idEquino` INT)   BEGIN
    -- Selecciona las fotografías asociadas a un idEquino específico
    SELECT idEquino, public_id, created_at
    FROM fotografiaequinos
    WHERE idEquino = p_idEquino
    ORDER BY created_at DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_listar_fotografia_dashboard` ()   BEGIN
    SELECT nombreEquino, fotografia FROM Equinos
    WHERE idPropietario IS NULL;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_listar_haras` ()   BEGIN
		SELECT DISTINCT 
        idPropietario,
        nombreHaras
    FROM Propietarios;
 END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_listar_herramientas` ()   BEGIN
    SELECT idHerramienta, nombreHerramienta
    FROM Herramientas;
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
        idTipoinventario = p_idTipoinventario
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_listar_lotes_medicamentos_por_nombre` (IN `nombreMedicamento` VARCHAR(255))   BEGIN
    -- Seleccionar solo los lotes de medicamentos asociados al nombre especificado
    SELECT 
        lm.lote AS loteMedicamento       -- Lote del medicamento
    FROM 
        Medicamentos m
    JOIN 
        LotesMedicamento lm ON m.idLoteMedicamento = lm.idLoteMedicamento
    WHERE 
        m.nombreMedicamento = nombreMedicamento; -- Filtrar por el nombre del medicamento
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_listar_lotes_por_nombre` (IN `nombreAlimento` VARCHAR(100))   BEGIN
    SELECT 
        l.lote
    FROM 
        Alimentos a
    JOIN 
        LotesAlimento l ON a.idLote = l.idLote
    WHERE 
        a.nombreAlimento = nombreAlimento;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_listar_medicamentos` ()   BEGIN                                       
    SELECT 
        idMedicamento,
        nombreMedicamento
    FROM 
        Medicamentos
    ORDER BY 
        nombreMedicamento ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_listar_medicamentosMedi` ()   BEGIN
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_listar_revision_basica` ()   BEGIN
    -- Seleccionamos todos los registros de la tabla revisionequinos con el nombre del equino
    SELECT 
        r.idRevision,
        e.nombreEquino,  -- Obtenemos el nombre del equino desde la tabla Equinos
        p.nombreHaras,    -- Nombre del propietario (nombreHaras)
        r.tiporevision,
        r.fecharevision,
        r.observaciones,
        r.costorevision
    FROM 
        revisionequinos r
    JOIN 
        Equinos e ON r.idEquino = e.idEquino  -- Hacemos JOIN con la tabla Equinos
	LEFT JOIN 
		Propietarios p ON r.idPropietario = p.idPropietario
    ORDER BY 
        r.fecharevision DESC; -- Ordenamos por la fecha de la revisión de forma descendente
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
    SELECT idTipoEquino, tipoEquino
    FROM TipoEquinos;
 END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_listar_tipos_presentaciones_dosis` ()   BEGIN
    -- Selecciona los tipos de medicamentos junto con la presentación y la dosis (cantidad y unidad), agrupados
    SELECT 
        c.idCombinacion,
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_listar_tipos_trabajos` ()   BEGIN
    SELECT idTipoTrabajo, nombreTrabajo
    FROM TiposTrabajos;
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_Listar_ViasAdministracion` ()   BEGIN
    SELECT idViaAdministracion, nombreVia, descripcion
    FROM ViasAdministracion;
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
    DECLARE _currentStock INT;  -- Usar INT ya que la columna cantidad_stock es de tipo INT
    DECLARE _finalMotivo TEXT;

    -- Iniciar transacción
    START TRANSACTION;

    -- Validar que la cantidad a retirar sea mayor que cero
    IF _cantidad <= 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La cantidad a retirar debe ser mayor que cero.';
    END IF;

    -- Buscar el ID del medicamento y la cantidad en stock
    SELECT M.idMedicamento, M.cantidad_stock INTO _idMedicamento, _currentStock
    FROM Medicamentos M
    JOIN LotesMedicamento L ON M.idLoteMedicamento = L.idLoteMedicamento
    WHERE M.nombreMedicamento = _nombreMedicamento AND L.lote = _lote;

    -- Verificar que el medicamento exista
    IF _idMedicamento IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El medicamento especificado no existe.';
    END IF;

    -- Verificar si hay suficiente stock
    IF _currentStock < _cantidad THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No hay suficiente stock de este medicamento.';
    END IF;

    -- Actualizar el stock del medicamento
    UPDATE Medicamentos
    SET cantidad_stock = cantidad_stock - _cantidad
    WHERE idMedicamento = _idMedicamento;

    -- Establecer el motivo en función de si es por equino o por otros motivos
    IF _idEquino IS NOT NULL THEN
        SET _finalMotivo = _motivo;
    ELSE
        SET _finalMotivo = 'No especificado';
    END IF;

    -- Registrar el movimiento en la tabla HistorialMovimientosMedicamentos
    INSERT INTO HistorialMovimientosMedicamentos (idMedicamento, tipoMovimiento, cantidad, motivo, idEquino, idUsuario)
    VALUES (_idMedicamento, 'Salida', _cantidad, _finalMotivo, _idEquino, _idUsuario);

    -- Confirmar transacción
    COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_modificar_estado_user` (IN `p_idUsuario` INT)   BEGIN
	IF EXISTS (SELECT 1 FROM Usuarios WHERE idUsuario = p_idUsuario) THEN
    UPDATE Usuarios
    SET estado = CASE
		WHEN estado = 1 THEN 0
        ELSE 1
        END
	WHERE idUsuario = p_idUsuario;
    
    SELECT 'Estado cambiado correctamente' AS mensaje;
    ELSE
		SELECT 'Usuario no encontrado' AS mensaje;
	end if;
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
    -- Seleccionamos directamente las columnas necesarias, incluyendo un mensaje personalizado
    SELECT 
        a.nombreAlimento AS nombreAlimento,       -- Nombre del alimento
        l.lote AS loteAlimento,                  -- Lote del alimento
        a.stockActual AS stockActual,            -- Stock actual del alimento
        a.stockMinimo AS stockMinimo,            -- Stock mínimo permitido
        ta.tipoAlimento AS tipoAlimento,         -- Tipo de alimento
        um.nombreUnidad AS unidadMedida,         -- Unidad de medida
        CASE 
            WHEN a.stockActual = 0 THEN 'Agotado'   -- Mensaje si el stock es 0
            WHEN a.stockActual < a.stockMinimo THEN 'Stock bajo' -- Mensaje si está por debajo del mínimo
            ELSE 'En stock'                         -- Por si acaso, un valor genérico
        END AS mensaje                            -- Mensaje personalizado basado en la condición
    FROM Alimentos a
    JOIN LotesAlimento l ON a.idLote = l.idLote
    JOIN TipoAlimentos ta ON a.idTipoAlimento = ta.idTipoAlimento
    JOIN UnidadesMedidaAlimento um ON a.idUnidadMedida = um.idUnidadMedida
    WHERE a.stockActual <= a.stockMinimo          -- Filtro para stock bajo o agotado
    ORDER BY a.stockActual ASC                   -- Orden por stock más bajo
    LIMIT 5;                                     -- Limitamos los resultados a 5
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_notificar_stock_bajo_medicamentos` ()   BEGIN
    -- Seleccionamos directamente las columnas necesarias, incluyendo un mensaje personalizado
    SELECT 
        m.nombreMedicamento AS nombreMedicamento,
        lm.lote AS loteMedicamento,
        m.cantidad_stock AS stockActual,
        m.stockMinimo AS stockMinimo,
        CASE 
            WHEN m.cantidad_stock = 0 THEN 'Agotado'
            WHEN m.cantidad_stock > 0 AND m.cantidad_stock < m.stockMinimo THEN 'Stock bajo'
        END AS mensaje
    FROM 
        Medicamentos m
    JOIN 
        LotesMedicamento lm ON m.idLoteMedicamento = lm.idLoteMedicamento
    WHERE 
        m.cantidad_stock <= m.stockMinimo
    ORDER BY 
        m.cantidad_stock ASC
    LIMIT 10; -- Limitar a las primeras 10 notificaciones
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_notificar_tratamientos_veterinarios` ()   BEGIN
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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_nuevas_fotografias_equinos` (IN `p_idEquino` INT, IN `p_public_id` VARCHAR(255))   BEGIN
    -- Inserta una nueva fotografía en la tabla fotografiaequinos
    INSERT INTO fotografiaequinos (idEquino, public_id, created_at)
    VALUES (p_idEquino, p_public_id, NOW());
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_obtenerAlimentosConLote` (IN `_idAlimento` INT)   BEGIN
    DECLARE _idLote INT;
    DECLARE _fechaCaducidadLote DATE;

    -- Actualizar el estado de los alimentos según su stock
    UPDATE Alimentos 
    SET estado = 'Agotado'
    WHERE stockActual = 0;

    UPDATE Alimentos 
    SET estado = 'Por agotarse'
    WHERE stockActual > 0 AND stockActual <= stockMinimo;

    UPDATE Alimentos 
    SET estado = 'Disponible'
    WHERE stockActual > stockMinimo;

    -- Obtener el idLote y fechaCaducidad del lote asociado al alimento
    SELECT idLote, fechaCaducidad INTO _idLote, _fechaCaducidadLote 
    FROM LotesAlimento
    WHERE lote = (SELECT lote FROM Alimentos WHERE idAlimento = _idAlimento LIMIT 1);

    -- Verificar si el lote está vencido (si la fecha de caducidad es menor a la fecha actual)
    IF _fechaCaducidadLote IS NOT NULL AND _fechaCaducidadLote < CURDATE() THEN
        -- El lote está vencido, actualizar el estado del lote y de los alimentos a 'Vencido'
        UPDATE LotesAlimento
        SET estadoLote = 'Vencido'
        WHERE idLote = _idLote;

        UPDATE Alimentos
        SET estado = 'Vencido'
        WHERE idLote = _idLote;
    ELSE
        -- Si el lote no está vencido, asegurarse que su estado sea 'No Vencido'
        UPDATE LotesAlimento
        SET estadoLote = 'No Vencido'
        WHERE idLote = _idLote;
    END IF;

    -- Realizar la consulta de los alimentos (excluyendo los vencidos)
    SELECT 
        A.idAlimento,
        A.idUsuario,
        A.nombreAlimento,
        TA.tipoAlimento AS nombreTipoAlimento,
        A.stockActual,
        A.stockMinimo,
        A.estado,
        U.nombreUnidad AS unidadMedidaNombre,
        A.costo,
        A.idLote,
        A.idEquino,
        A.compra,
        A.fechaMovimiento,
        L.idLote AS loteId,
        L.lote,
        L.fechaCaducidad,
        L.fechaIngreso,
        L.estadoLote
    FROM 
        Alimentos A
    INNER JOIN 
        LotesAlimento L ON A.idLote = L.idLote
    INNER JOIN 
        TipoAlimentos TA ON A.idTipoAlimento = TA.idTipoAlimento
    INNER JOIN 
        UnidadesMedidaAlimento U ON A.idUnidadMedida = U.idUnidadMedida
    WHERE 
        (_idAlimento IS NULL OR A.idAlimento = _idAlimento)
        AND A.estado != 'Vencido'; -- Excluir alimentos con estado 'Vencido'

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_ObtenerHistorialDosisAplicadas` ()   BEGIN
    SELECT 
        m.nombreMedicamento AS Medicamento,                     -- Nombre del medicamento
        CONCAT(h.cantidadAplicada, ' ', u.unidad) AS DosisAplicada, -- Dosis aplicada con unidad desde la base
        CONCAT(h.cantidadRestante, ' ', u.unidad) AS StockRestante, -- Stock restante después de la aplicación con unidad
        CONCAT((h.cantidadRestante + h.cantidadAplicada), ' ', u.unidad) AS StockAntes, -- Stock antes de la aplicación con unidad
        m.cantidad_stock AS StockActual,                        -- Stock actual disponible en unidades completas
        m.estado AS EstadoMedicamento,                         -- Estado del medicamento (Disponible, Agotado, etc.)
        h.fechaAplicacion AS FechaAplicación,                  -- Fecha en la que se aplicó la dosis
        e.nombreEquino AS NombreDelEquino,                     -- Nombre del equino al que se aplicó la dosis
        CONCAT(p.nombres, ' ', p.apellidos) AS NombreUsuario    -- Nombre completo del usuario que realizó la aplicación
    FROM 
        Medicamentos m
    JOIN 
        CombinacionesMedicamentos c ON m.idCombinacion = c.idCombinacion
    JOIN 
        TiposMedicamentos t ON c.idTipo = t.idTipo
    JOIN
        UnidadesMedida u ON c.idUnidad = u.idUnidad            -- Relación con unidades de medida
    JOIN
        LotesMedicamento lm ON m.idLoteMedicamento = lm.idLoteMedicamento -- Relación con lotes
    JOIN
        HistorialDosisAplicadas h ON m.idMedicamento = h.idMedicamento    -- Relación con historial de dosis
    JOIN
        Equinos e ON h.idEquino = e.idEquino                              -- Relación con equinos
    JOIN
        Usuarios usr ON h.idUsuario = usr.idUsuario                       -- Relación con usuarios
    JOIN
        Personal p ON usr.idPersonal = p.idPersonal                       -- Relación con personal
    ORDER BY 
        h.fechaAplicacion DESC, m.nombreMedicamento ASC;
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
        END AS tieneUsuario,
        u.idUsuario,
        u.correo
    FROM 
        Personal p
    LEFT JOIN 
        Usuarios u ON p.idPersonal = u.idPersonal;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_personal_registrar` (OUT `_idPersonal` INT, IN `_nombres` VARCHAR(100), IN `_apellidos` VARCHAR(100), IN `_direccion` VARCHAR(255), IN `_tipodoc` VARCHAR(20), IN `_nrodocumento` VARCHAR(50), IN `_fechaIngreso` DATE, IN `_tipoContrato` ENUM('Parcial','Completo','Por Prácticas','Otro'))   BEGIN
    -- Declaración de variables
    DECLARE existe_error INT DEFAULT 0;
    
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
    BEGIN
        SET existe_error = 1;
    END;
    
    INSERT INTO Personal (nombres, apellidos, direccion, tipodoc, nrodocumento, fechaIngreso, tipoContrato)
    VALUES (_nombres, _apellidos, _direccion, _tipodoc, _nrodocumento, _fechaIngreso, _tipoContrato);
    
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_registrar_dosis_aplicada` (IN `_idMedicamento` INT, IN `_idEquino` INT, IN `_cantidadAplicada` DECIMAL(10,2), IN `_idUsuario` INT, IN `_unidadAplicada` VARCHAR(50), IN `_fechaAplicacion` DATE)   BEGIN
    DECLARE _stockActual DECIMAL(10, 2); -- Stock actual del medicamento
    DECLARE _unidadCompleta DECIMAL(10, 2); -- Cantidad por unidad en stock (en mg)
    DECLARE _unidadBase VARCHAR(50); -- Unidad real del medicamento desde la base
    DECLARE _cantidadRestanteAcumulada DECIMAL(10, 2); -- Cantidad restante acumulada de dosis anteriores
    DECLARE _cantidadTotal DECIMAL(10, 2); -- Total acumulado con la nueva dosis
    DECLARE _nuevasUnidades INT; -- Número de unidades completas que se pueden descontar
    DECLARE _errorMessage VARCHAR(255); -- Mensaje de error

    -- Obtener la cantidad por unidad, stock actual y unidad base del medicamento
    SELECT c.dosis, m.cantidad_stock, u.unidad
    INTO _unidadCompleta, _stockActual, _unidadBase
    FROM Medicamentos m
    JOIN CombinacionesMedicamentos c ON m.idCombinacion = c.idCombinacion
    JOIN UnidadesMedida u ON c.idUnidad = u.idUnidad
    WHERE m.idMedicamento = _idMedicamento
    FOR UPDATE;

    -- Verificar que la unidad proporcionada coincide con la unidad base
    IF _unidadAplicada != _unidadBase THEN
        SET _errorMessage = CONCAT('Unidad no válida. Se esperaba: ', _unidadBase);
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMessage;
    END IF;

    -- Calcular la cantidad restante acumulada de dosis anteriores
    SELECT COALESCE(cantidadRestante, 0)
    INTO _cantidadRestanteAcumulada
    FROM HistorialDosisAplicadas
    WHERE idMedicamento = _idMedicamento
    ORDER BY idDosis DESC
    LIMIT 1;

    -- Sumar la nueva cantidad aplicada al acumulado
    SET _cantidadTotal = _cantidadRestanteAcumulada + _cantidadAplicada;

    -- Calcular cuántas unidades completas se pueden descontar
    SET _nuevasUnidades = FLOOR(_cantidadTotal / _unidadCompleta);

    -- Verificar que el stock es suficiente para descontar las unidades completas
    IF _nuevasUnidades > _stockActual THEN
        SET _errorMessage = 'Stock insuficiente para completar la operación.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMessage;
    END IF;

    -- Calcular la cantidad restante después de completar las unidades
    SET _cantidadRestanteAcumulada = MOD(_cantidadTotal, _unidadCompleta);

    -- Registrar la dosis aplicada en el historial, usando la fecha de aplicación proporcionada
    INSERT INTO HistorialDosisAplicadas (idMedicamento, idEquino, cantidadAplicada, cantidadRestante, fechaAplicacion, idUsuario)
    VALUES (_idMedicamento, _idEquino, _cantidadAplicada, _cantidadRestanteAcumulada, _fechaAplicacion, _idUsuario);

    -- Actualizar el stock general si se completaron unidades completas
    IF _nuevasUnidades > 0 THEN
        UPDATE Medicamentos
        SET cantidad_stock = cantidad_stock - _nuevasUnidades,
            ultima_modificacion = NOW()
        WHERE idMedicamento = _idMedicamento;
    END IF;

    -- Confirmar la transacción
    COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_registrar_historial_equinos` (IN `p_idEquino` INT, IN `p_descripcion` TEXT)   BEGIN
    DECLARE historial_existe INT;

    -- Verificamos si ya existe un historial para el equino
    SELECT COUNT(*) 
    INTO historial_existe
    FROM HistorialEquinos
    WHERE idEquino = p_idEquino;

    -- Si ya existe un historial, enviamos un mensaje de error
    IF historial_existe > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Ya existe un historial para este equino';
    ELSE
        -- Si no existe un historial, procedemos con la inserción
        INSERT INTO HistorialEquinos (idEquino, descripcion)
        VALUES (p_idEquino, p_descripcion);
    END IF;
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_registrar_propietario` (OUT `_idPropietario` INT, IN `_nombreHaras` VARCHAR(100))   BEGIN
    -- Declaración de una variable para capturar errores
    DECLARE existe_error INT DEFAULT 0;
    DECLARE nombre_existente INT;

    -- Manejo de errores de SQL
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
    BEGIN
        SET existe_error = 1;
    END;

    -- Verificar si el nombre ya existe en la base de datos
    SELECT COUNT(*) INTO nombre_existente
    FROM Propietarios
    WHERE nombreHaras = _nombreHaras;

    -- Si el nombre ya existe, asignar un valor de error y salir del procedimiento
    IF nombre_existente > 0 THEN
        SET _idPropietario = -2; -- Error: El nombre ya existe
    ELSE
        -- Si el nombre no existe, realizar la inserción
        INSERT INTO Propietarios (nombreHaras) 
        VALUES (_nombreHaras);

        -- Verificar si ocurrió un error en la inserción
        IF existe_error = 1 THEN
            SET _idPropietario = -1; -- Error en la inserción
        ELSE
            SET _idPropietario = LAST_INSERT_ID(); -- Devuelve el id del nuevo propietario
        END IF;
    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_registrar_revision_equino` (IN `p_idEquino` INT, IN `p_idPropietario` INT, IN `p_tiporevision` ENUM('Ecografía','Examen ginecológico','Citología','Cultivo bacteriológico','Biopsia endometrial'), IN `p_fecharevision` DATE, IN `p_observaciones` TEXT, IN `p_costorevision` DECIMAL(10,2))   BEGIN
    -- Verificar si el equino es una Yegua
    DECLARE v_tipoEquino INT;
    DECLARE v_serviciosCount INT;

    -- Obtener el tipo de equino (1 = Yegua, 2 = Padrillo, 3 = Potranca, 4 = Potrillo)
    SELECT idTipoEquino INTO v_tipoEquino
    FROM Equinos
    WHERE idEquino = p_idEquino;

    -- Verificar si el equino es una Yegua (tipo 1)
    IF v_tipoEquino != 1 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El equino no es una yegua';
    END IF;

    -- Verificar si el equino ha tenido al menos un servicio
    -- Verificar para yeguas propias (idEquinoHembra)
    SELECT COUNT(*) INTO v_serviciosCount
    FROM Servicios
    WHERE idEquinoHembra = p_idEquino;

    -- Verificar para yeguas externas (idEquinoExterno)
    IF v_serviciosCount = 0 THEN
        SELECT COUNT(*) INTO v_serviciosCount
        FROM Servicios
        WHERE idEquinoExterno = p_idEquino;
    END IF;

    IF v_serviciosCount = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La yegua no tiene servicios registrados';
    END IF;
    
    -- Verificar si la fecha de la revisión no es posterior a la fecha actual
    IF p_fecharevision > CURDATE() THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se puede registrar una revisión con una fecha futura';
    END IF;

    -- Insertar la nueva revisión en la tabla revisionequinos
    INSERT INTO revisionequinos (
        idEquino, 
        idPropietario, 
        tiporevision, 
        fecharevision, 
        observaciones, 
        costorevision
    )
    VALUES (
        p_idEquino, 
        p_idPropietario, 
        p_tiporevision, 
        p_fecharevision, 
        p_observaciones, 
        p_costorevision
    );

    -- Mensaje de confirmación
    SELECT 'Revisión registrada correctamente' AS mensaje;
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
        USU.correo = _correo
        AND USU.estado = 1; -- Filtro para solo usuarios activos
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `spu_usuarios_registrar` (OUT `_idUsuario` INT, IN `_idPersonal` INT, IN `_correo` VARCHAR(50), IN `_clave` VARCHAR(100), IN `_idRol` INT)   BEGIN
    DECLARE existe_error INT DEFAULT 0;
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
  `estado` enum('Disponible','Por agotarse','Agotado','Vencido') DEFAULT 'Disponible',
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
(1, 3, 'Afrecho', 2, 412.50, 100.00, 'Disponible', 1, 65.00, 1, 5, 32500.00, '2025-01-28 22:03:33'),
(2, 3, 'Afrecho', 2, 10000.00, 1000.00, 'Disponible', 1, 70.00, 2, NULL, 700000.00, '2025-01-28 21:50:39'),
(3, 3, 'Cebada', 2, 8000.00, 1000.00, 'Disponible', 1, 65.00, 2, NULL, 520000.00, '2025-01-28 21:51:50'),
(4, 3, 'Afrecho', 2, 10000.00, 1000.00, 'Disponible', 1, 69.00, 3, NULL, 690000.00, '2025-01-28 21:52:45');

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

--
-- Volcado de datos para la tabla `bostas`
--

INSERT INTO `bostas` (`idbosta`, `fecha`, `cantidadsacos`, `pesoaprox`, `peso_diario`, `peso_semanal`, `peso_mensual`, `numero_semana`) VALUES
(1, '2024-12-31', 34, 27.00, 918.00, 918.00, 918.00, 53),
(2, '2025-01-10', 36, 25.00, 900.00, 900.00, 900.00, 2),
(3, '2025-01-20', 40, 24.00, 960.00, 960.00, 1860.00, 4);

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
(1, 1, 998.83, 1, 'Activo'),
(2, 2, 1023.89, 1, 'Activo'),
(3, 3, 1015.33, 1, 'Activo'),
(4, 4, 1008.52, 1, 'Activo'),
(5, 5, 1004.52, 2, 'Activo'),
(6, 6, 999.65, 2, 'Activo'),
(7, 7, 997.77, 2, 'Activo'),
(8, 8, 989.45, 2, 'Activo');

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
(16, 1, 15, 250.00, 1),
(5, 2, 1, 50.00, 1),
(2, 2, 2, 10.00, 2),
(3, 3, 3, 200.00, 1),
(6, 3, 5, 5.00, 1),
(15, 3, 14, 15.00, 1),
(17, 3, 16, 100.00, 4),
(21, 3, 18, 10.00, 1),
(22, 3, 18, 50.00, 1),
(7, 4, 6, 300.00, 1),
(8, 5, 7, 100.00, 3),
(14, 5, 13, 50.00, 1),
(18, 6, 1, 10.00, 5),
(9, 6, 8, 1.00, 2),
(10, 7, 9, 0.50, 2),
(11, 8, 10, 20.00, 1),
(12, 9, 11, 5.00, 1),
(13, 10, 12, 1.00, 3),
(19, 11, 4, 200.00, 3),
(24, 11, 19, 10.00, 1),
(23, 11, 19, 10.00, 2);

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
  `idViaAdministracion` int(11) NOT NULL,
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
  `fechaentrada` date DEFAULT NULL,
  `fechasalida` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `equinos`
--

INSERT INTO `equinos` (`idEquino`, `nombreEquino`, `fechaNacimiento`, `sexo`, `idTipoEquino`, `detalles`, `idEstadoMonta`, `idNacionalidad`, `idPropietario`, `pesokg`, `fotografia`, `estado`, `fechaentrada`, `fechasalida`, `created_at`, `updated_at`) VALUES
(1, 'Southdale', '2006-04-30', 'Macho', 2, NULL, 1, 35, NULL, 750.0, 'wamevh4drqeq6014yabu', b'1', NULL, NULL, '2025-01-28 04:04:36', '2025-01-28 04:57:13'),
(2, 'Umbridled Command', '2009-03-22', 'Macho', 2, NULL, 1, 57, NULL, 730.0, 'fdox90by0ixfpqmqnwml', b'1', NULL, NULL, '2025-01-28 04:07:28', '2025-01-29 05:25:19'),
(3, 'Floriform', '2018-04-16', 'Macho', 2, NULL, 1, 57, NULL, 700.0, 'mbutbvtf7srkrulyrqwv', b'1', NULL, NULL, '2025-01-28 04:09:48', '2025-01-29 05:25:30'),
(4, 'La Lomada', '2012-08-05', 'Hembra', 1, NULL, 5, 8, NULL, 620.0, 'agzlw5eefgmhfyw8he5s', b'1', NULL, NULL, '2025-01-28 04:38:13', '2025-01-29 05:25:19'),
(5, 'Gong Zhu', '2013-01-24', 'Hembra', 1, NULL, 5, 57, NULL, 630.0, 'siyzv6nrrxb1zecq4lu4', b'1', NULL, NULL, '2025-01-28 04:39:03', NULL),
(6, 'Rocio de Lima', '2011-05-13', 'Hembra', 1, NULL, 5, 57, NULL, 650.0, 'snrw85wx03ygxhouhz7q', b'1', NULL, NULL, '2025-01-28 04:39:56', NULL),
(7, 'Alena', '2016-03-03', 'Hembra', 1, NULL, 5, 137, NULL, 680.0, 'z88s36zxckkryce0pteb', b'1', NULL, NULL, '2025-01-28 04:40:43', NULL),
(8, 'La Elegida', '2006-07-08', 'Hembra', 1, NULL, 5, 137, NULL, 640.0, 'g5e4cujkuzryiajuj0a3', b'1', NULL, NULL, '2025-01-28 04:41:26', '2025-01-29 05:25:30'),
(9, 'Nairobi', '2008-06-02', 'Hembra', 1, NULL, 5, 137, NULL, 600.0, 'amq0nqrlbcr9cbe9kesa', b'1', NULL, NULL, '2025-01-28 04:42:09', NULL),
(10, 'Galaxia', '2016-05-05', 'Hembra', 1, NULL, 5, 137, NULL, 630.0, 'pcyx90wsaoq4fgrag58u', b'1', NULL, NULL, '2025-01-28 04:43:59', NULL),
(11, 'Gwendoline', '2008-04-25', 'Hembra', 1, NULL, 5, 137, NULL, 620.0, 'qzuldgsu0vovijsynoti', b'1', NULL, NULL, '2025-01-28 04:44:49', NULL),
(12, 'Moon Pass', '2009-03-24', 'Hembra', 1, NULL, 5, 57, NULL, 610.0, 'qziseur0xemjkbwanbgn', b'1', NULL, NULL, '2025-01-28 04:45:52', NULL),
(13, 'Mosquetera', '2018-05-06', 'Hembra', 1, NULL, 4, 57, NULL, 620.0, 'qnznzzd93imue7ct2w15', b'1', NULL, NULL, '2025-01-28 04:46:29', '2025-01-29 05:25:30'),
(14, 'Q\'Orianka', '2017-04-16', 'Hembra', 1, NULL, 5, 8, NULL, 630.0, 'x97hi5dbkvs9kdvwpbbo', b'1', NULL, NULL, '2025-01-28 04:47:31', NULL),
(15, 'Hechicero', '2023-10-11', 'Macho', 4, NULL, NULL, 137, NULL, 280.0, 'eplldnef8xjozjw2gttz', b'1', NULL, NULL, '2025-01-28 04:50:09', NULL),
(16, 'Via Regina', '2022-09-17', 'Hembra', 3, NULL, 5, 137, NULL, 350.0, 'zakh9ur42g5mrijkh1et', b'1', NULL, NULL, '2025-01-28 04:51:21', '2025-01-28 04:57:13'),
(17, 'Curare', '2023-10-06', 'Macho', 4, NULL, NULL, 137, NULL, 290.0, 'yncuf9afn7ysqecuyrwr', b'1', NULL, NULL, '2025-01-28 04:53:05', NULL),
(18, 'La Candy', NULL, 'Hembra', 1, NULL, 4, 57, 1, NULL, '', b'1', NULL, NULL, '2025-01-28 04:54:02', '2025-01-29 05:40:50'),
(19, 'La Negra', NULL, 'Hembra', 1, NULL, 5, 115, 2, NULL, '', b'1', '2025-01-27', '2025-01-31', '2025-01-28 04:54:52', '2025-01-29 05:25:19'),
(20, 'Poética', NULL, 'Hembra', 1, NULL, 5, 137, 1, NULL, '', b'1', NULL, NULL, '2025-01-28 04:56:16', NULL),
(21, 'Rayo Veloz', '2025-01-15', 'Macho', 5, NULL, NULL, 137, NULL, 40.0, 'ie2fgreqsrhkrpjsk5op', b'1', NULL, NULL, '2025-01-29 02:58:51', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estadomonta`
--

CREATE TABLE `estadomonta` (
  `idEstadoMonta` int(11) NOT NULL,
  `genero` enum('Macho','Hembra') NOT NULL,
  `nombreEstado` enum('S/S','Servida','Por Servir','Preñada','Vacia','Con Cria','Activo','Inactivo') NOT NULL
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
(8, 'Hembra', 'Con Cria');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fotografiaequinos`
--

CREATE TABLE `fotografiaequinos` (
  `idfotografia` int(11) NOT NULL,
  `idEquino` int(11) NOT NULL,
  `public_id` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `herramientas`
--

CREATE TABLE `herramientas` (
  `idHerramienta` int(11) NOT NULL,
  `nombreHerramienta` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `herramientas`
--

INSERT INTO `herramientas` (`idHerramienta`, `nombreHerramienta`) VALUES
(2, 'Corta-casco'),
(3, 'Cortador de herraduras'),
(4, 'Cuñas y espuelas'),
(1, 'Tenazas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `herramientasusadashistorial`
--

CREATE TABLE `herramientasusadashistorial` (
  `idHerramientasUsadas` int(11) NOT NULL,
  `idHistorialHerrero` int(11) NOT NULL,
  `idHerramienta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `herramientasusadashistorial`
--

INSERT INTO `herramientasusadashistorial` (`idHerramientasUsadas`, `idHistorialHerrero`, `idHerramienta`) VALUES
(1, 1, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historialdosisaplicadas`
--

CREATE TABLE `historialdosisaplicadas` (
  `idDosis` int(11) NOT NULL,
  `idMedicamento` int(11) NOT NULL,
  `idEquino` int(11) NOT NULL,
  `cantidadAplicada` decimal(10,2) NOT NULL,
  `cantidadRestante` decimal(10,2) DEFAULT NULL,
  `fechaAplicacion` date NOT NULL,
  `idUsuario` int(11) NOT NULL
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

--
-- Volcado de datos para la tabla `historialequinos`
--

INSERT INTO `historialequinos` (`idHistorial`, `idEquino`, `descripcion`) VALUES
(1, 1, '<p>Linajudo zaino hijo de Street Cry con compaña en Canada donde logró ganar 4 carreras en 9 presentaciones incluso Eclipse S.<strong>(G3).</strong></p>');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historialherrero`
--

CREATE TABLE `historialherrero` (
  `idHistorialHerrero` int(11) NOT NULL,
  `idEquino` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `idTrabajo` int(11) NOT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historialherrero`
--

INSERT INTO `historialherrero` (`idHistorialHerrero`, `idEquino`, `idUsuario`, `fecha`, `idTrabajo`, `observaciones`) VALUES
(1, 1, 3, '2025-01-31', 2, 'Se requiere realizar el recorte de los cascos del equino Southdale para poder salvaguardar el estado y salubridad del mismo.');

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

--
-- Volcado de datos para la tabla `historialimplemento`
--

INSERT INTO `historialimplemento` (`idHistorial`, `idInventario`, `idTipoinventario`, `idTipomovimiento`, `cantidad`, `precioUnitario`, `precioTotal`, `descripcion`, `fechaMovimiento`) VALUES
(1, 6, 2, 2, 1, NULL, NULL, 'Mal estado', '2025-01-29 05:39:42');

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
(1, 1, 'Entrada', 200.00, NULL, 3, '1', '2025-01-28', NULL),
(2, 1, 'Salida', 98.00, 15, 3, 'kg', '2025-01-28', 2.00),
(3, 1, 'Salida', 7.00, 1, 3, 'kg', '2025-01-28', 0.00),
(4, 1, 'Salida', 12.00, 2, 3, 'kg', '2025-01-28', 0.00),
(5, 1, 'Salida', 5.00, 16, 3, 'kg', '2025-01-28', 0.00),
(6, 1, 'Salida', 10.00, 15, 3, 'kg', '2025-01-28', 0.50),
(7, 1, 'Salida', 72.00, 5, 3, 'kg', '2025-01-28', 1.00),
(8, 1, 'Salida', 75.00, 5, 3, 'kg', '2025-01-28', 5.00);

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

--
-- Volcado de datos para la tabla `historialmovimientosmedicamentos`
--

INSERT INTO `historialmovimientosmedicamentos` (`idMovimiento`, `idMedicamento`, `tipoMovimiento`, `cantidad`, `motivo`, `idEquino`, `idUsuario`, `fechaMovimiento`) VALUES
(1, 1, 'Entrada', 20, '', NULL, 3, '2025-01-28');

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

--
-- Volcado de datos para la tabla `implementos`
--

INSERT INTO `implementos` (`idInventario`, `idTipoinventario`, `nombreProducto`, `descripcion`, `precioUnitario`, `precioTotal`, `idTipomovimiento`, `cantidad`, `stockFinal`, `estado`, `create_at`) VALUES
(1, 1, 'Soga', 'Soga de 3.5 metros de largo.', 12.00, 240.00, 1, 20, 20, b'1', '2025-01-28 23:56:18'),
(2, 1, 'Jáquima', 'Jáquima para diferenciar equinos', 100.00, 1500.00, 1, 15, 15, b'1', '2025-01-28 23:57:35'),
(3, 1, 'Cepillo de cuerpo', 'Cepillo de cerdas suaves, de cerdas duras.', 12.00, 60.00, 1, 5, 5, b'1', '2025-01-28 23:58:49'),
(4, 1, 'Cepillo de crin', 'Mantener las crines y colas desenredadas.', 8.00, 16.00, 1, 2, 2, b'1', '2025-01-28 23:59:56'),
(5, 1, 'Refuerzo para casco', 'Mantener la salud del pie del caballo.', 150.00, 450.00, 1, 3, 3, b'1', '2025-01-29 00:00:33'),
(6, 2, 'Lampa', 'Beneficiar campos', 26.00, 156.00, 1, 6, 5, b'1', '2025-01-29 00:05:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lotesalimento`
--

CREATE TABLE `lotesalimento` (
  `idLote` int(11) NOT NULL,
  `lote` varchar(50) NOT NULL,
  `fechaCaducidad` date DEFAULT NULL,
  `fechaIngreso` datetime DEFAULT current_timestamp(),
  `estadoLote` enum('No Vencido','Vencido','Agotado') DEFAULT 'No Vencido'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `lotesalimento`
--

INSERT INTO `lotesalimento` (`idLote`, `lote`, `fechaCaducidad`, `fechaIngreso`, `estadoLote`) VALUES
(1, '0101', '2025-03-18', '2025-01-28 00:02:50', 'No Vencido'),
(2, '1114', '2025-03-20', '2025-01-28 21:50:39', 'No Vencido'),
(3, '1142', '2025-05-07', '2025-01-28 21:52:45', 'No Vencido');

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
(1, '0101', '2025-06-27', '2025-01-28'),
(2, '0552', '2027-05-31', '2025-01-28');

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
(1, 3, 'Flunixin Meglumine', 'Antiinflamatorio no esteroideo (AINE).', 22, 30, 3, 'Disponible', NULL, 1, 110.00, NULL, '2025-01-28', '2025-01-29 04:51:34'),
(2, 3, 'Ivermectina', '', 24, 15, 5, 'Disponible', NULL, 2, 35.00, NULL, '2025-01-28', '2025-01-29 04:50:41');

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
(1, 1, 2.00, '2025-01-28 00:04:55', 'Merma registrada en salida de inventario'),
(2, 1, 0.50, '2025-01-28 22:02:05', 'Merma registrada en salida de inventario'),
(3, 1, 1.00, '2025-01-28 22:02:35', 'Merma registrada en salida de inventario'),
(4, 1, 5.00, '2025-01-28 22:03:33', 'Merma registrada en salida de inventario');

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
(1, 'campos', '2025-01-27 22:51:30'),
(2, 'equinos', '2025-01-27 22:51:30'),
(3, 'historialMedico', '2025-01-27 22:51:30'),
(4, 'inventarios', '2025-01-27 22:51:30'),
(5, 'reportes', '2025-01-27 22:51:30'),
(6, 'servicios', '2025-01-27 22:51:30'),
(7, 'usuarios', '2025-01-27 22:51:30');

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
(1, 1, 1, '2025-01-27 22:51:30'),
(2, 1, 5, '2025-01-27 22:51:30'),
(3, 1, 6, '2025-01-27 22:51:30'),
(4, 1, 7, '2025-01-27 22:51:30'),
(5, 1, 10, '2025-01-27 22:51:30'),
(6, 1, 12, '2025-01-27 22:51:30'),
(7, 1, 15, '2025-01-27 22:51:30'),
(8, 1, 19, '2025-01-27 22:51:30'),
(9, 1, 20, '2025-01-27 22:51:30'),
(10, 1, 21, '2025-01-27 22:51:30'),
(11, 1, 22, '2025-01-27 22:51:30'),
(12, 1, 23, '2025-01-27 22:51:30'),
(13, 1, 30, '2025-01-27 22:51:30'),
(14, 1, 31, '2025-01-27 22:51:30'),
(15, 1, 32, '2025-01-27 22:51:30'),
(16, 1, 36, '2025-01-27 22:51:30'),
(17, 2, 1, '2025-01-27 22:51:30'),
(18, 2, 5, '2025-01-27 22:51:30'),
(19, 2, 6, '2025-01-27 22:51:30'),
(20, 2, 7, '2025-01-27 22:51:30'),
(21, 2, 10, '2025-01-27 22:51:30'),
(22, 2, 12, '2025-01-27 22:51:30'),
(23, 2, 15, '2025-01-27 22:51:30'),
(24, 2, 19, '2025-01-27 22:51:30'),
(25, 2, 20, '2025-01-27 22:51:30'),
(26, 2, 21, '2025-01-27 22:51:30'),
(27, 2, 22, '2025-01-27 22:51:30'),
(28, 2, 23, '2025-01-27 22:51:30'),
(29, 2, 30, '2025-01-27 22:51:30'),
(30, 2, 31, '2025-01-27 22:51:30'),
(31, 2, 32, '2025-01-27 22:51:30'),
(32, 2, 35, '2025-01-27 22:51:30'),
(33, 2, 36, '2025-01-27 22:51:30'),
(34, 3, 1, '2025-01-27 22:51:30'),
(35, 3, 4, '2025-01-27 22:51:30'),
(36, 3, 6, '2025-01-27 22:51:30'),
(37, 3, 7, '2025-01-27 22:51:30'),
(38, 3, 9, '2025-01-27 22:51:30'),
(39, 3, 10, '2025-01-27 22:51:30'),
(40, 3, 11, '2025-01-27 22:51:30'),
(41, 3, 12, '2025-01-27 22:51:30'),
(42, 3, 13, '2025-01-27 22:51:30'),
(43, 3, 14, '2025-01-27 22:51:30'),
(44, 3, 15, '2025-01-27 22:51:30'),
(45, 3, 16, '2025-01-27 22:51:30'),
(46, 3, 17, '2025-01-27 22:51:30'),
(47, 3, 18, '2025-01-27 22:51:30'),
(48, 3, 19, '2025-01-27 22:51:30'),
(49, 3, 21, '2025-01-27 22:51:30'),
(50, 3, 23, '2025-01-27 22:51:30'),
(51, 3, 24, '2025-01-27 22:51:30'),
(52, 3, 26, '2025-01-27 22:51:30'),
(53, 3, 27, '2025-01-27 22:51:30'),
(54, 3, 28, '2025-01-27 22:51:30'),
(55, 3, 31, '2025-01-27 22:51:30'),
(56, 3, 32, '2025-01-27 22:51:30'),
(57, 3, 33, '2025-01-27 22:51:30'),
(58, 3, 34, '2025-01-27 22:51:30'),
(59, 3, 36, '2025-01-27 22:51:30'),
(60, 4, 1, '2025-01-27 22:51:30'),
(61, 4, 2, '2025-01-27 22:51:30'),
(62, 4, 3, '2025-01-27 22:51:30'),
(63, 4, 5, '2025-01-27 22:51:30'),
(64, 4, 8, '2025-01-27 22:51:30'),
(65, 4, 22, '2025-01-27 22:51:30'),
(66, 4, 25, '2025-01-27 22:51:30'),
(67, 4, 29, '2025-01-27 22:51:30'),
(68, 4, 36, '2025-01-27 22:51:30'),
(69, 5, 1, '2025-01-27 22:51:30'),
(70, 5, 6, '2025-01-27 22:51:30'),
(71, 5, 10, '2025-01-27 22:51:30'),
(72, 5, 11, '2025-01-27 22:51:30'),
(73, 5, 12, '2025-01-27 22:51:30'),
(74, 5, 13, '2025-01-27 22:51:30'),
(75, 5, 14, '2025-01-27 22:51:30'),
(76, 5, 15, '2025-01-27 22:51:30'),
(77, 5, 36, '2025-01-27 22:51:30'),
(78, 6, 1, '2025-01-27 22:51:30'),
(79, 6, 13, '2025-01-27 22:51:30'),
(80, 6, 17, '2025-01-27 22:51:30'),
(81, 6, 19, '2025-01-27 22:51:30'),
(82, 6, 36, '2025-01-27 22:51:30'),
(83, 3, 20, '2025-01-28 00:03:45');

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
  `fechaIngreso` date NOT NULL,
  `fechaSalida` date DEFAULT NULL,
  `tipoContrato` enum('Parcial','Completo','Por Prácticas','Otro') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `personal`
--

INSERT INTO `personal` (`idPersonal`, `nombres`, `apellidos`, `direccion`, `tipodoc`, `nrodocumento`, `fechaIngreso`, `fechaSalida`, `tipoContrato`) VALUES
(1, 'Gerente', 'Mateo', 'San Agustin ', 'DNI', '11111111', '2024-08-27', NULL, 'Completo'),
(2, 'Administrador', 'Marcos', 'Calle Fatima', 'DNI', '22222222', '2024-08-27', NULL, 'Completo'),
(3, 'SupervisorE', 'Gereda', 'AV. Los Angeles', 'DNI', '33333333', '2024-08-27', NULL, 'Completo'),
(4, 'SupervisorC', 'Mamani', 'Calle Fatima', 'DNI', '44444444', '2024-08-27', NULL, 'Completo'),
(5, 'Medico', 'Paullac', 'Calle Fatima', 'DNI', '55555555', '2024-08-27', NULL, 'Completo'),
(6, 'Herrero', 'Nuñez', 'Calle Fatima', 'DNI', '66666666', '2024-08-27', NULL, 'Parcial'),
(9, 'José Aurelio', 'Quispe Yupanqui', 'Av. Brasil', 'DNI', '77777777', '2024-12-30', NULL, 'Parcial');

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
(18, 'inyectables'),
(2, 'jarabes'),
(11, 'píldoras'),
(15, 'polvos medicinales'),
(7, 'pomadas'),
(19, 'solución oral'),
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
(1, 'Los Eucaliptos'),
(2, 'Haras Hasmide');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `revisionequinos`
--

CREATE TABLE `revisionequinos` (
  `idRevision` int(11) NOT NULL,
  `idEquino` int(11) NOT NULL,
  `idPropietario` int(11) DEFAULT NULL,
  `tiporevision` enum('Ecografía','Examen ginecológico','Citología','Cultivo bacteriológico','Biopsia endometrial') DEFAULT NULL,
  `fecharevision` date NOT NULL,
  `observaciones` text NOT NULL,
  `costorevision` decimal(10,2) DEFAULT NULL,
  `create_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `revisionequinos`
--

INSERT INTO `revisionequinos` (`idRevision`, `idEquino`, `idPropietario`, `tiporevision`, `fecharevision`, `observaciones`, `costorevision`, `create_at`) VALUES
(1, 18, 1, 'Ecografía', '2025-01-06', 'Abortó', NULL, '2025-01-28 22:34:03'),
(7, 18, 1, 'Biopsia endometrial', '2025-01-23', 'Ninguna, solo rutinaria', 1100.00, '2025-01-28 23:37:52');

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
  `fechaRotacion` date DEFAULT NULL,
  `estadoRotacion` varchar(50) NOT NULL,
  `detalleRotacion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rotacioncampos`
--

INSERT INTO `rotacioncampos` (`idRotacion`, `idCampo`, `idTipoRotacion`, `fechaRotacion`, `estadoRotacion`, `detalleRotacion`) VALUES
(1, 8, 3, '2025-01-01', '', ''),
(2, 3, 2, '2025-01-10', '', ''),
(3, 8, 12, '2025-01-04', '', '');

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
  `detalles` text DEFAULT NULL,
  `idMedicamento` int(11) DEFAULT NULL,
  `horaEntrada` time DEFAULT NULL,
  `horaSalida` time DEFAULT NULL,
  `idPropietario` int(11) DEFAULT NULL,
  `idEstadoMonta` int(11) DEFAULT NULL,
  `costoServicio` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`idServicio`, `idEquinoMacho`, `idEquinoHembra`, `idEquinoExterno`, `fechaServicio`, `tipoServicio`, `detalles`, `idMedicamento`, `horaEntrada`, `horaSalida`, `idPropietario`, `idEstadoMonta`, `costoServicio`) VALUES
(1, 1, 4, NULL, '2025-01-27', 'Propio', '', NULL, NULL, NULL, NULL, NULL, NULL),
(2, 1, NULL, 18, '2025-01-01', 'Mixto', '', NULL, '06:00:00', '06:15:00', 1, NULL, 3500.00),
(3, 1, NULL, 19, '2025-01-01', 'Mixto', '', NULL, '10:44:00', '10:49:00', 2, NULL, 2000.00),
(4, 1, 4, NULL, '2025-01-01', 'Propio', '', NULL, NULL, NULL, NULL, NULL, NULL),
(5, 2, 8, NULL, '2024-12-29', 'Propio', '', NULL, NULL, NULL, NULL, NULL, NULL),
(6, 3, 13, NULL, '2025-01-16', 'Propio', '', NULL, NULL, NULL, NULL, NULL, NULL),
(7, 1, NULL, 18, '2025-01-17', 'Mixto', '', NULL, '12:40:00', '12:50:00', 1, NULL, 2500.00);

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
(11, 'Destetados', NULL),
(12, 'Sembrío', NULL);

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
-- Estructura de tabla para la tabla `tipostrabajos`
--

CREATE TABLE `tipostrabajos` (
  `idTipoTrabajo` int(11) NOT NULL,
  `nombreTrabajo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipostrabajos`
--

INSERT INTO `tipostrabajos` (`idTipoTrabajo`, `nombreTrabajo`) VALUES
(1, 'Colocación de herraduras'),
(2, 'Recorte de los cascos');

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
(9, 'sacos'),
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
  `estado` bit(1) NOT NULL DEFAULT b'1',
  `create_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `inactive_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`idUsuario`, `idPersonal`, `correo`, `clave`, `idRol`, `estado`, `create_at`, `inactive_at`) VALUES
(1, 1, 'gerente', '$2y$10$RaoPTBz9oVETRVocodEaWuwxQPjshzARRmDnGZcWcDY43YxNF/sIa', 1, b'1', '2025-01-28 03:51:30', NULL),
(2, 2, 'admin', '$2y$10$RaoPTBz9oVETRVocodEaWuwxQPjshzARRmDnGZcWcDY43YxNF/sIa', 2, b'1', '2025-01-28 03:51:30', NULL),
(3, 3, 'superE', '$2y$10$RaoPTBz9oVETRVocodEaWuwxQPjshzARRmDnGZcWcDY43YxNF/sIa', 3, b'1', '2025-01-28 03:51:30', NULL),
(4, 4, 'superC', '$2y$10$RaoPTBz9oVETRVocodEaWuwxQPjshzARRmDnGZcWcDY43YxNF/sIa', 4, b'1', '2025-01-28 03:51:30', NULL),
(5, 5, 'medico', '$2y$10$RaoPTBz9oVETRVocodEaWuwxQPjshzARRmDnGZcWcDY43YxNF/sIa', 5, b'1', '2025-01-28 03:51:30', NULL),
(6, 6, 'herrero', '$2y$10$RaoPTBz9oVETRVocodEaWuwxQPjshzARRmDnGZcWcDY43YxNF/sIa', 6, b'1', '2025-01-28 03:51:30', NULL),
(7, 9, 'jose', '$2y$10$8UuHdbYIkL036ecao7Y69.yCV9GHZhMbMDulc95h/dSDTbR6shCU.', 2, b'0', '2025-01-29 05:47:51', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `viasadministracion`
--

CREATE TABLE `viasadministracion` (
  `idViaAdministracion` int(11) NOT NULL,
  `nombreVia` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `viasadministracion`
--

INSERT INTO `viasadministracion` (`idViaAdministracion`, `nombreVia`, `descripcion`) VALUES
(1, 'Oral', 'Por la boca.'),
(2, 'Intravenosa', 'En una vena.'),
(3, 'Intramuscular', 'En un músculo.'),
(4, 'Sublingual', 'Bajo la lengua.'),
(5, 'Tópica', 'Sobre la piel.'),
(6, 'Rectal', 'Por el recto.'),
(7, 'Inhalatoria', 'Por las vías respiratorias.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vistas`
--

CREATE TABLE `vistas` (
  `idvista` int(11) NOT NULL,
  `idmodulo` int(11) DEFAULT NULL,
  `ruta` varchar(50) NOT NULL,
  `sidebaroption` char(1) NOT NULL,
  `texto` varchar(40) DEFAULT NULL,
  `icono` varchar(35) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vistas`
--

INSERT INTO `vistas` (`idvista`, `idmodulo`, `ruta`, `sidebaroption`, `texto`, `icono`) VALUES
(1, NULL, 'home', 'S', 'Inicio', 'fas fa-home'),
(2, 1, 'rotar-campo', 'S', 'Campos', 'fa-solid fa-group-arrows-rotate'),
(3, 1, 'programar-rotacion', 'S', 'Rotacion Campos', 'fa-solid fa-calendar-days'),
(4, 2, 'historial-equino', 'S', 'Historial Equinos', 'fas fa-history'),
(5, 2, 'listar-bostas', 'S', 'Listado Bostas', 'fa-solid fa-list'),
(6, 2, 'listar-equino', 'S', 'Listado Equinos', 'fa-solid fa-list'),
(7, 2, 'mostrar-foto', 'S', 'Colección de Fotos', 'fa-solid fa-image'),
(8, 2, 'registrar-bostas', 'S', 'Registro Bostas', 'fas fa-poop'),
(9, 2, 'registrar-equino', 'S', 'Registro Equinos', 'fa-solid fa-horse'),
(10, 2, 'listar-equino-externo', 'S', 'Listado Equinos Ajenos', 'fas fa-file-alt'),
(11, 3, 'diagnosticar-equino', 'N', NULL, NULL),
(12, 3, 'listar-diagnostico-avanzado', 'N', NULL, NULL),
(13, 3, 'revisar-equino', 'N', NULL, NULL),
(14, 3, 'seleccionar-diagnostico', 'S', 'Diagnóstico', 'fa-solid fa-notes-medical'),
(15, 3, 'listar-diagnostico-basico', 'N', NULL, NULL),
(16, 4, 'administrar-alimento', 'S', 'Alimentos', 'fas fa-apple-alt'),
(17, 4, 'administrar-herramienta', 'S', 'Herrero', 'fas fa-wrench'),
(18, 4, 'administrar-medicamento', 'S', 'Medicamentos', 'fas fa-pills'),
(19, 4, 'listar-accion-herrero', 'N', NULL, NULL),
(20, 4, 'listar-alimento', 'N', NULL, NULL),
(21, 4, 'listar-implemento-caballo', 'N', NULL, NULL),
(22, 4, 'listar-implemento-campo', 'N', NULL, NULL),
(23, 4, 'listar-medicamento', 'N', NULL, NULL),
(24, 4, 'registrar-implemento-caballo', 'S', 'Implementos Caballos', 'fa-solid fa-scissors'),
(25, 4, 'registrar-implemento-campo', 'S', 'Implementos Campos', 'fa-solid fa-wrench'),
(26, 4, 'listar-historial-medicamento', 'N', NULL, NULL),
(27, 4, 'listar-historial-alimento', 'N', NULL, NULL),
(28, 4, 'listar-historial-I-caballo', 'N', NULL, NULL),
(29, 4, 'listar-historial-I-campo', 'N', NULL, NULL),
(30, 5, 'presionar-boton-reporte', 'S', 'Reportes', 'fa-solid fa-file-circle-plus'),
(31, 6, 'listar-medicamento-usado', 'N', NULL, NULL),
(32, 6, 'listar-servicio', 'S', 'Listado Servicios', 'fa-solid fa-list'),
(33, 6, 'servir-mixto', 'S', 'Servicio Mixto', 'fas fa-exchange-alt'),
(34, 6, 'servir-propio', 'S', 'Servicio Propio', 'fas fa-tools'),
(35, 7, 'registrar-personal', 'S', 'Registrar Personal', 'fa-solid fa-wallet'),
(36, 7, 'actualizar-contrasenia', 'S', 'Actualizar Contraseña', 'fas fa-key');

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
  ADD KEY `fk_detallemed_usuario` (`idUsuario`),
  ADD KEY `fk_detallemed_via` (`idViaAdministracion`);

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
-- Indices de la tabla `estadomonta`
--
ALTER TABLE `estadomonta`
  ADD PRIMARY KEY (`idEstadoMonta`);

--
-- Indices de la tabla `fotografiaequinos`
--
ALTER TABLE `fotografiaequinos`
  ADD PRIMARY KEY (`idfotografia`),
  ADD KEY `fk_public_id_ft` (`idEquino`);

--
-- Indices de la tabla `herramientas`
--
ALTER TABLE `herramientas`
  ADD PRIMARY KEY (`idHerramienta`),
  ADD UNIQUE KEY `nombreHerramienta` (`nombreHerramienta`);

--
-- Indices de la tabla `herramientasusadashistorial`
--
ALTER TABLE `herramientasusadashistorial`
  ADD PRIMARY KEY (`idHerramientasUsadas`),
  ADD KEY `fk_herramienta_historial` (`idHistorialHerrero`),
  ADD KEY `fk_herramienta` (`idHerramienta`);

--
-- Indices de la tabla `historialdosisaplicadas`
--
ALTER TABLE `historialdosisaplicadas`
  ADD PRIMARY KEY (`idDosis`),
  ADD KEY `fk_idMedicamento` (`idMedicamento`),
  ADD KEY `fk_idEquino_dosis` (`idEquino`),
  ADD KEY `fk_idUsuario_dosis` (`idUsuario`);

--
-- Indices de la tabla `historialequinos`
--
ALTER TABLE `historialequinos`
  ADD PRIMARY KEY (`idHistorial`),
  ADD KEY `fk_idEquino_historial` (`idEquino`);

--
-- Indices de la tabla `historialherrero`
--
ALTER TABLE `historialherrero`
  ADD PRIMARY KEY (`idHistorialHerrero`),
  ADD KEY `fk_historialherrero_equino` (`idEquino`),
  ADD KEY `fk_historialherrero_usuario` (`idUsuario`),
  ADD KEY `fk_historialherrero_trabajo` (`idTrabajo`);

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
-- Indices de la tabla `revisionequinos`
--
ALTER TABLE `revisionequinos`
  ADD PRIMARY KEY (`idRevision`),
  ADD KEY `fk_idEquino_revision` (`idEquino`),
  ADD KEY `fk_idPropietario_revision` (`idPropietario`);

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
-- Indices de la tabla `tipostrabajos`
--
ALTER TABLE `tipostrabajos`
  ADD PRIMARY KEY (`idTipoTrabajo`),
  ADD UNIQUE KEY `nombreTrabajo` (`nombreTrabajo`);

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
-- Indices de la tabla `viasadministracion`
--
ALTER TABLE `viasadministracion`
  ADD PRIMARY KEY (`idViaAdministracion`),
  ADD UNIQUE KEY `nombreVia` (`nombreVia`);

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
  MODIFY `idAlimento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `asistenciapersonal`
--
ALTER TABLE `asistenciapersonal`
  MODIFY `idAsistencia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `bostas`
--
ALTER TABLE `bostas`
  MODIFY `idbosta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `campos`
--
ALTER TABLE `campos`
  MODIFY `idCampo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `combinacionesmedicamentos`
--
ALTER TABLE `combinacionesmedicamentos`
  MODIFY `idCombinacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

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
  MODIFY `idEquino` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `estadomonta`
--
ALTER TABLE `estadomonta`
  MODIFY `idEstadoMonta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `fotografiaequinos`
--
ALTER TABLE `fotografiaequinos`
  MODIFY `idfotografia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `herramientas`
--
ALTER TABLE `herramientas`
  MODIFY `idHerramienta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `herramientasusadashistorial`
--
ALTER TABLE `herramientasusadashistorial`
  MODIFY `idHerramientasUsadas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `historialdosisaplicadas`
--
ALTER TABLE `historialdosisaplicadas`
  MODIFY `idDosis` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historialequinos`
--
ALTER TABLE `historialequinos`
  MODIFY `idHistorial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `historialherrero`
--
ALTER TABLE `historialherrero`
  MODIFY `idHistorialHerrero` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `historialimplemento`
--
ALTER TABLE `historialimplemento`
  MODIFY `idHistorial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `historialmovimientos`
--
ALTER TABLE `historialmovimientos`
  MODIFY `idMovimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `historialmovimientosmedicamentos`
--
ALTER TABLE `historialmovimientosmedicamentos`
  MODIFY `idMovimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `implementos`
--
ALTER TABLE `implementos`
  MODIFY `idInventario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `lotesalimento`
--
ALTER TABLE `lotesalimento`
  MODIFY `idLote` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `lotesmedicamento`
--
ALTER TABLE `lotesmedicamento`
  MODIFY `idLoteMedicamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `medicamentos`
--
ALTER TABLE `medicamentos`
  MODIFY `idMedicamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `mermasalimento`
--
ALTER TABLE `mermasalimento`
  MODIFY `idMerma` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `idmodulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `nacionalidades`
--
ALTER TABLE `nacionalidades`
  MODIFY `idNacionalidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=183;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `idpermiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT de la tabla `personal`
--
ALTER TABLE `personal`
  MODIFY `idPersonal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `presentacionesmedicamentos`
--
ALTER TABLE `presentacionesmedicamentos`
  MODIFY `idPresentacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `propietarios`
--
ALTER TABLE `propietarios`
  MODIFY `idPropietario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `revisionequinos`
--
ALTER TABLE `revisionequinos`
  MODIFY `idRevision` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `idRol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `rotacioncampos`
--
ALTER TABLE `rotacioncampos`
  MODIFY `idRotacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `idServicio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
  MODIFY `idTipoRotacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `tiposmedicamentos`
--
ALTER TABLE `tiposmedicamentos`
  MODIFY `idTipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `tipostrabajos`
--
ALTER TABLE `tipostrabajos`
  MODIFY `idTipoTrabajo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `idUsuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `viasadministracion`
--
ALTER TABLE `viasadministracion`
  MODIFY `idViaAdministracion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `vistas`
--
ALTER TABLE `vistas`
  MODIFY `idvista` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

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
  ADD CONSTRAINT `fk_detallemed_usuario` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`),
  ADD CONSTRAINT `fk_detallemed_via` FOREIGN KEY (`idViaAdministracion`) REFERENCES `viasadministracion` (`idViaAdministracion`);

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
-- Filtros para la tabla `fotografiaequinos`
--
ALTER TABLE `fotografiaequinos`
  ADD CONSTRAINT `fk_public_id_ft` FOREIGN KEY (`idEquino`) REFERENCES `equinos` (`idEquino`) ON DELETE CASCADE;

--
-- Filtros para la tabla `herramientasusadashistorial`
--
ALTER TABLE `herramientasusadashistorial`
  ADD CONSTRAINT `fk_herramienta` FOREIGN KEY (`idHerramienta`) REFERENCES `herramientas` (`idHerramienta`),
  ADD CONSTRAINT `fk_herramienta_historial` FOREIGN KEY (`idHistorialHerrero`) REFERENCES `historialherrero` (`idHistorialHerrero`);

--
-- Filtros para la tabla `historialdosisaplicadas`
--
ALTER TABLE `historialdosisaplicadas`
  ADD CONSTRAINT `fk_idEquino_dosis` FOREIGN KEY (`idEquino`) REFERENCES `equinos` (`idEquino`),
  ADD CONSTRAINT `fk_idMedicamento` FOREIGN KEY (`idMedicamento`) REFERENCES `medicamentos` (`idMedicamento`),
  ADD CONSTRAINT `fk_idUsuario_dosis` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`);

--
-- Filtros para la tabla `historialequinos`
--
ALTER TABLE `historialequinos`
  ADD CONSTRAINT `fk_idEquino_historial` FOREIGN KEY (`idEquino`) REFERENCES `equinos` (`idEquino`);

--
-- Filtros para la tabla `historialherrero`
--
ALTER TABLE `historialherrero`
  ADD CONSTRAINT `fk_historialherrero_equino` FOREIGN KEY (`idEquino`) REFERENCES `equinos` (`idEquino`),
  ADD CONSTRAINT `fk_historialherrero_trabajo` FOREIGN KEY (`idTrabajo`) REFERENCES `tipostrabajos` (`idTipoTrabajo`),
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
-- Filtros para la tabla `revisionequinos`
--
ALTER TABLE `revisionequinos`
  ADD CONSTRAINT `fk_idEquino_revision` FOREIGN KEY (`idEquino`) REFERENCES `equinos` (`idEquino`),
  ADD CONSTRAINT `fk_idPropietario_revision` FOREIGN KEY (`idPropietario`) REFERENCES `propietarios` (`idPropietario`);

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
