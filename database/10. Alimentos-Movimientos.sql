DROP PROCEDURE IF EXISTS `spu_alimentos_nuevo`;

CREATE PROCEDURE spu_alimentos_nuevo(
    IN _idUsuario INT,
    IN _nombreAlimento VARCHAR(100),
    IN _idTipoAlimento INT,
    IN _idUnidadMedida INT,
    IN _lote VARCHAR(50),
    IN _costo DECIMAL(10,2),
    IN _fechaCaducidad DATE,
    IN _stockActual DECIMAL(10,2),
    IN _stockMinimo DECIMAL(10,2)
)
BEGIN
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

END ;

DROP PROCEDURE IF EXISTS `spu_obtenerAlimentosConLote`;

CREATE PROCEDURE spu_obtenerAlimentosConLote(IN _idAlimento INT)
BEGIN
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

END ;

DROP PROCEDURE IF EXISTS `spu_alimentos_entrada`;

CREATE PROCEDURE spu_alimentos_entrada(
    IN _idUsuario INT,
    IN _nombreAlimento VARCHAR(100),
    IN _idUnidadMedida INT, -- Cambiado a INT
    IN _lote VARCHAR(50),
    IN _cantidad DECIMAL(10,2)
)
BEGIN
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
END ;

DROP PROCEDURE IF EXISTS `spu_alimentos_salida`;

CREATE PROCEDURE spu_alimentos_salida(
    IN _idUsuario INT,                 -- ID del usuario que realiza la salida
    IN _nombreAlimento VARCHAR(100),   -- Nombre del alimento
    IN _idUnidadMedida INT,            -- ID de la unidad de medida del alimento
    IN _cantidad DECIMAL(10,2),        -- Cantidad total de salida
    IN _uso DECIMAL(10,2),             -- Cantidad que se usará
    IN _merma DECIMAL(10,2),           -- Cantidad que irá a merma
    IN _idEquino INT,                  -- ID del equino al que se destina la salida (si aplica)
    IN _lote VARCHAR(50)               -- Identificación del lote
)
BEGIN
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

END ;

DROP PROCEDURE IF EXISTS `spu_listar_lotes_por_nombre`;

CREATE PROCEDURE spu_listar_lotes_por_nombre(IN nombreAlimento VARCHAR(100))
BEGIN
    SELECT 
        l.lote
    FROM 
        Alimentos a
    JOIN 
        LotesAlimento l ON a.idLote = l.idLote
    WHERE 
        a.nombreAlimento = nombreAlimento;
END ;

DROP PROCEDURE IF EXISTS `spu_notificar_stock_bajo_alimentos`;

CREATE PROCEDURE spu_notificar_stock_bajo_alimentos()
BEGIN
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
END ;


DROP PROCEDURE IF EXISTS `spu_historial_completo`;

CREATE PROCEDURE spu_historial_completo(
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
END ;

DROP PROCEDURE IF EXISTS `spu_eliminarAlimento`;

CREATE PROCEDURE spu_eliminarAlimento(IN _idAlimento INT)
BEGIN
    -- Verificar si el alimento existe antes de intentar eliminarlo
    IF EXISTS (SELECT 1 FROM Alimentos WHERE idAlimento = _idAlimento) THEN
        -- Eliminar el alimento
        DELETE FROM Alimentos WHERE idAlimento = _idAlimento;
    ELSE
        -- Si no existe, generar un error de control
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El alimento no existe.';
    END IF;
END ;


DROP PROCEDURE IF EXISTS `spu_obtenerTiposAlimento`;

CREATE PROCEDURE spu_obtenerTiposAlimento()
BEGIN
    SELECT idTipoAlimento, tipoAlimento 
    FROM TipoAlimentos 
    ORDER BY tipoAlimento;
END ;

DROP PROCEDURE IF EXISTS `spu_obtenerUnidadesPorTipoAlimento`;

CREATE PROCEDURE spu_obtenerUnidadesPorTipoAlimento(IN _idTipoAlimento INT)
BEGIN
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
END ;


DROP PROCEDURE IF EXISTS `spu_agregarTipoUnidadMedidaNuevo`;

CREATE PROCEDURE spu_agregarTipoUnidadMedidaNuevo (
    IN p_tipoAlimento VARCHAR(50),
    IN p_nombreUnidad VARCHAR(10)
)
BEGIN
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
END ;

-- sugerencias 
DROP PROCEDURE IF EXISTS `ObtenerSugerenciasAlimentos`;

CREATE PROCEDURE ObtenerSugerenciasAlimentos()
BEGIN
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
END;

-- editar sugerencias
DROP PROCEDURE IF EXISTS `EditarCombinacionAlimento`;

CREATE PROCEDURE EditarCombinacionAlimento(
    IN p_IdTipoActual INT,           -- ID actual del tipo de alimento
    IN p_IdUnidadActual INT,         -- ID actual de la unidad de medida
    IN p_NuevoTipo VARCHAR(50),      -- Nuevo nombre del tipo de alimento
    IN p_NuevaUnidad VARCHAR(10)     -- Nuevo nombre de la unidad de medida
)
BEGIN
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

END;