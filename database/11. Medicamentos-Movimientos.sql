DROP PROCEDURE IF EXISTS `spu_listar_medicamentosMedi`;
DELIMITER $$
CREATE PROCEDURE spu_listar_medicamentosMedi()
BEGIN
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
END $$
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_medicamentos_registrar`;
DELIMITER $$
CREATE PROCEDURE spu_medicamentos_registrar(
    IN _nombreMedicamento VARCHAR(255),
    IN _descripcion TEXT, 
    IN _lote VARCHAR(100),
    IN _idPresentacion INT,         -- ID de Presentación
    IN _dosisCompleta VARCHAR(50),  -- Ej. "56 mg"
    IN _idTipo INT,                 -- ID del Tipo de Medicamento
    IN _cantidad_stock INT,
    IN _stockMinimo INT,
    IN _fechaCaducidad DATE,
    IN _precioUnitario DECIMAL(10,2),
    IN _idUsuario INT
)
BEGIN
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

END $$
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_medicamentos_entrada`;
DELIMITER $$
CREATE PROCEDURE spu_medicamentos_entrada(
    IN _idUsuario INT,                      -- Usuario que realiza la operación
    IN _nombreMedicamento VARCHAR(255),     -- Nombre del medicamento
    IN _lote VARCHAR(100),                  -- Número de lote del medicamento
    IN _cantidad INT                        -- Cantidad de medicamento a ingresar
)
BEGIN
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

END $$
DELIMITER ;


DROP PROCEDURE IF EXISTS `spu_medicamentos_salida`;
DELIMITER $$

CREATE PROCEDURE spu_medicamentos_salida(
    IN _idUsuario INT,                    -- Usuario que realiza la operación
    IN _nombreMedicamento VARCHAR(255),    -- Nombre del medicamento
    IN _cantidad DECIMAL(10,2),            -- Cantidad de medicamento a retirar
    IN _idEquino INT,                      -- ID del equino relacionado con la salida (NULL si es otro motivo)
    IN _lote VARCHAR(100),                 -- Número de lote del medicamento (puede ser NULL)
    IN _motivo TEXT                        -- Motivo de la salida del medicamento (obligatorio)
)
BEGIN
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
DELIMITER ;


DROP PROCEDURE IF EXISTS `spu_agregar_nueva_combinacion_medicamento`;
DELIMITER $$
CREATE PROCEDURE spu_agregar_nueva_combinacion_medicamento(
    IN _tipo VARCHAR(100),
    IN _presentacion VARCHAR(100),
    IN _unidad VARCHAR(50),
    IN _dosis DECIMAL(10, 2),
    OUT mensaje VARCHAR(255) -- Variable de salida para mensajes amigables
)
BEGIN
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
END $$
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_validar_registrar_combinacion`;
DELIMITER $$
CREATE PROCEDURE spu_validar_registrar_combinacion(
    IN _idTipo INT,
    IN _idPresentacion INT,
    IN _dosisMedicamento DECIMAL(10, 2),
    IN _unidadMedida VARCHAR(50)
)
BEGIN
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
END $$
DELIMITER ;

-- sugerencias
DROP PROCEDURE IF EXISTS `spu_listar_tipos_presentaciones_dosis`;
DELIMITER $$
CREATE PROCEDURE spu_listar_tipos_presentaciones_dosis()
BEGIN
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
END $$
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_listar_tipos_unicos`;
DELIMITER $$
CREATE PROCEDURE spu_listar_tipos_unicos()
BEGIN
    SELECT DISTINCT t.idTipo, t.tipo
    FROM TiposMedicamentos t
    JOIN CombinacionesMedicamentos c ON t.idTipo = c.idTipo
    ORDER BY t.tipo ASC;
END $$
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_listar_presentaciones_por_tipo`;
DELIMITER $$
CREATE PROCEDURE spu_listar_presentaciones_por_tipo(IN _idTipo INT)
BEGIN
    SELECT DISTINCT p.idPresentacion, p.presentacion
    FROM CombinacionesMedicamentos c
    JOIN PresentacionesMedicamentos p ON c.idPresentacion = p.idPresentacion
    WHERE c.idTipo = _idTipo
    ORDER BY p.presentacion ASC;
END $$
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_notificar_stock_bajo_medicamentos`;
DELIMITER $$
CREATE PROCEDURE spu_notificar_stock_bajo_medicamentos()
BEGIN
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
END $$
DELIMITER ;



-- historial 
DROP PROCEDURE IF EXISTS `spu_historial_completo_medicamentos`;
DELIMITER $$
CREATE PROCEDURE spu_historial_completo_medicamentos(
    IN tipoMovimiento VARCHAR(50),
    IN filtroFecha VARCHAR(20),  -- Nuevo parámetro para el filtro de fecha
    IN idUsuario INT,
    IN limite INT,
    IN desplazamiento INT
)
BEGIN
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
END $$
DELIMITER ;


DROP PROCEDURE IF EXISTS `spu_listar_lotes_medicamentos_por_nombre`;
DELIMITER $$
CREATE PROCEDURE spu_listar_lotes_medicamentos_por_nombre(IN nombreMedicamento VARCHAR(255))
BEGIN
    -- Seleccionar solo los lotes de medicamentos asociados al nombre especificado
    SELECT 
        lm.lote AS loteMedicamento       -- Lote del medicamento
    FROM 
        Medicamentos m
    JOIN 
        LotesMedicamento lm ON m.idLoteMedicamento = lm.idLoteMedicamento
    WHERE 
        m.nombreMedicamento = nombreMedicamento; -- Filtrar por el nombre del medicamento
END $$
DELIMITER ;


/*
-- original
DROP PROCEDURE IF EXISTS `spu_medicamentos_salida`;
DELIMITER $$
CREATE PROCEDURE spu_medicamentos_salida(
    IN _idUsuario INT,                    -- Usuario que realiza la operación
    IN _nombreMedicamento VARCHAR(255),    -- Nombre del medicamento
    IN _cantidad DECIMAL(10,2),            -- Cantidad de medicamento a retirar
    IN _idEquino INT,                      -- ID del equino relacionado con la salida
    IN _lote VARCHAR(100),                 -- Número de lote del medicamento (puede ser NULL)
    IN _motivo TEXT                        -- Motivo de la salida del medicamento
)
BEGIN
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
END $$
DELIMITER ;

*/

INSERT INTO TiposMedicamentos (tipo) VALUES
('Antibiótico'),
('Analgésico'),
('Antiinflamatorio'),
('Gastroprotector'),
('Desparasitante'),
('Suplemento'),
('Broncodilatador'),
('Antifúngico'),
('Sedante'),
('Vacuna'),
('Antiparasitario'),
('Vitaminas');

INSERT INTO PresentacionesMedicamentos (presentacion) VALUES
('tabletas'),
('jarabes'),
('cápsulas'),
('inyectable'),
('suspensión'),
('grageas'),
('pomadas'),
('ampollas'),
('colirios'),
('gotas nasales'),
('píldoras'),
('comprimidos'),
('enemas'),
('goteros'),
('polvos medicinales'),
('aerosoles'),
('spray');

INSERT INTO UnidadesMedida (unidad) VALUES
('mg'),
('ml'),
('g'),
('mcg'),
('fL'),
('dL'),
('L'),
('mcl'),
('mcmol'),
('mEq'),
('mm'),
('mm Hg'),
('mmol'),
('mOsm'),
('mUI'),
('mU'),
('ng'),
('nmol'),
('pg'),
('pmol'),
('UI'),
('U');

INSERT INTO CombinacionesMedicamentos (idTipo, idPresentacion, dosis, idUnidad) VALUES
(1, 1, 500, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'mg')),       -- Antibiótico, tabletas, 500 mg
(2, 2, 10, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'ml')),        -- Analgésico, jarabes, 10 ml
(3, 3, 200, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'mg')),       -- Antiinflamatorio, cápsulas, 200 mg
(1, 4, 1, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'g')),          -- Antibiótico, inyectable, 1 g
(2, 1, 50, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'mg')),        -- Analgésico, tabletas, 50 mg
(3, 5, 5, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'mg')),         -- Antiinflamatorio, suspensión, 5 mg (para mg/ml usar lógica extra si es necesario)
(4, 6, 300, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'mg')),       -- Gastroprotector, grageas, 300 mg
(5, 7, 100, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'g')),        -- Desparasitante, pomadas, 100 g
(6, 8, 1, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'ml')),         -- Suplemento, ampollas, 1 ml
(7, 9, 0.5, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'ml')),       -- Broncodilatador, colirios, 0.5 ml
(8, 10, 20, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'mg')),       -- Antifúngico, gotas nasales, 20 mg (complejo mg/ml manejado aparte si necesario)
(9, 11, 5, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'mg')),        -- Sedante, píldoras, 5 mg
(10, 12, 1, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'g')),        -- Vacuna, comprimidos, 1 g
(5, 13, 50, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'mg')),       -- Desparasitante, enemas, 50 mg
(3, 14, 15, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'mg')),       -- Antiinflamatorio, goteros, 15 mg (si es mg/ml manejar aparte)
(1, 15, 250, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'mg')),      -- Antibiótico, polvos medicinales, 250 mg
(3, 16, 100, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'mcg')),     -- Antiinflamatorio, aerosoles, 100 mcg
(6, 1, 10, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'fL')),        -- Suplemento, tabletas, 10 fL
(11, 4, 200, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'g')),       -- Antiparasitario, suspensión, 200 g (asume g/dL según lógica de negocio)
(1, 4, 5, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'g'));          -- Antibiótico, inyectable, 5 g (para g/L manejar como necesario)