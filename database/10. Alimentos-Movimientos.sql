DROP PROCEDURE IF EXISTS `spu_alimentos_nuevo`;
DELIMITER $$

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
    DECLARE _estado ENUM('Disponible', 'Por agotarse', 'Agotado');
    DECLARE _fechaCaducidadLote DATE;
    DECLARE _estadoLote ENUM('Vencido', 'No vencido');
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

    -- Verificar si el lote ya está registrado en la tabla LotesAlimento
    SELECT idLote, fechaCaducidad INTO _idLote, _fechaCaducidadLote 
    FROM LotesAlimento
    WHERE lote = _lote
    LIMIT 1;

    -- Si el lote no existe, registrarlo en la tabla LotesAlimento
    IF _idLote IS NULL THEN
        INSERT INTO LotesAlimento (lote, fechaCaducidad, fechaIngreso) 
        VALUES (_lote, IFNULL(_fechaCaducidad, NULL), NOW());
        SET _idLote = LAST_INSERT_ID(); -- Reutilizamos el idLote recién generado
    END IF;

    -- Verificar si el lote está vencido
    IF _fechaCaducidadLote IS NOT NULL AND _fechaCaducidadLote < CURDATE() THEN
        SET _estadoLote = 'Vencido';
        SET _mensajeLote = CONCAT('El lote "', _lote, '" está vencido. La fecha de caducidad de este lote puede ser actualizada en el próximo registro.');
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _mensajeLote;
    ELSE
        SET _estadoLote = 'No vencido';
    END IF;

    -- Si el alimento ya está registrado con ese nombre, lote, tipo y unidad de medida
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

    -- Ahora verificamos si todos los alimentos de ese lote están agotados y vencidos,
    -- y si es así, actualizamos el estado del lote a 'Agotado'
    IF EXISTS (
        SELECT 1
        FROM Alimentos
        WHERE idLote = _idLote
          AND estado = 'Agotado'
          AND _fechaCaducidadLote < CURDATE()  -- Verificar si todos los alimentos están agotados y vencidos
    ) THEN
        UPDATE LotesAlimento
        SET estadoLote = 'Agotado', fechaCaducidad = NOW() -- Se actualiza la fecha de caducidad solo cuando el lote está agotado
        WHERE idLote = _idLote;
    END IF;

END $$

DELIMITER ;



DROP PROCEDURE IF EXISTS `spu_obtenerAlimentosConLote`;
DELIMITER $$
CREATE PROCEDURE spu_obtenerAlimentosConLote(IN _idAlimento INT)
BEGIN
    DECLARE _idLote INT;
    DECLARE _fechaCaducidadLote DATE;
    DECLARE _estado ENUM('Disponible', 'Por agotarse', 'Agotado');
    DECLARE _estadoLote ENUM('Vencido', 'No vencido');
    DECLARE _mensajeLote VARCHAR(255); -- Variable para el mensaje

    -- Primero, actualizamos el estado de los alimentos según su stock
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
        -- El lote está vencido, actualizar el estado del lote a 'Vencido'
        UPDATE LotesAlimento
        SET estadoLote = 'Vencido'
        WHERE idLote = _idLote;

        -- Ahora insertamos el alimento vencido en la tabla AlimentosVencidos solo si el lote está vencido
        INSERT INTO AlimentosVencidos (
            idAlimento, idUsuario, nombreAlimento, idTipoAlimento, idUnidadMedida, 
            idLote, costo, stockActual, stockMinimo, estado, fechaMovimiento, 
            fechaCaducidad, motivoVencimiento
        )
        SELECT 
            A.idAlimento, A.idUsuario, A.nombreAlimento, A.idTipoAlimento, A.idUnidadMedida, 
            A.idLote, A.costo, A.stockActual, A.stockMinimo, 'Vencido', NOW(), 
            A.fechaCaducidad, 'Vencido debido a fecha de caducidad pasada'
        FROM Alimentos A
        WHERE A.idLote = _idLote AND A.estado = 'Agotado';

        -- Actualizar el estado del alimento en la tabla Alimentos a 'Vencido'
        UPDATE Alimentos
        SET estado = 'Vencido'
        WHERE idLote = _idLote AND estado = 'Agotado';
    ELSE
        -- Si el lote no está vencido, asegurarse que su estado sea 'No Vencido'
        UPDATE LotesAlimento
        SET estadoLote = 'No Vencido'
        WHERE idLote = _idLote;
    END IF;

    -- Si algún alimento tiene stock 0 (es decir, está agotado), actualizar el estado del lote a 'Agotado'
    IF EXISTS (SELECT 1 FROM Alimentos WHERE idLote = _idLote AND estado = 'Agotado') THEN
        UPDATE LotesAlimento
        SET estadoLote = 'Agotado'
        WHERE idLote = _idLote;
    END IF;

    -- Realizar la consulta de los alimentos (excluyendo los vencidos)
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
        L.fechaIngreso,
        L.estadoLote  -- Mostrar el estado del lote (Vencido, No Vencido, Agotado)
    FROM 
        Alimentos A
    INNER JOIN 
        LotesAlimento L ON A.idLote = L.idLote
    INNER JOIN 
        TipoAlimentos TA ON A.idTipoAlimento = TA.idTipoAlimento       -- Relación con TipoAlimentos
    INNER JOIN 
        UnidadesMedidaAlimento U ON A.idUnidadMedida = U.idUnidadMedida -- Relación con UnidadesMedidaAlimento
    WHERE 
        (_idAlimento IS NULL OR A.idAlimento = _idAlimento)           -- Filtro por idAlimento si se proporciona
        AND A.estado != 'Vencido';  -- Excluir alimentos con estado 'Vencido'

END $$
DELIMITER ;





DROP PROCEDURE IF EXISTS `spu_alimentos_entrada`;
DELIMITER $$
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
END $$
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_alimentos_salida`;
DELIMITER $$
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

END $$
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_listar_lotes_alimentos`;
DELIMITER $$
CREATE PROCEDURE spu_listar_lotes_alimentos()
BEGIN
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
END $$
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_notificar_stock_bajo_alimentos`;
DELIMITER $$
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
END $$
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_historial_completo`;
DELIMITER $$
CREATE PROCEDURE spu_historial_completo(
    IN tipoMovimiento VARCHAR(50),
    IN fechaInicio DATE,
    IN fechaFin DATE,
    IN idUsuario INT,
    IN limite INT,
    IN desplazamiento INT
)
BEGIN
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
END $$
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_eliminarAlimento`;
DELIMITER $$
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
END $$
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_obtenerTiposAlimento`;
DELIMITER $$
CREATE PROCEDURE spu_obtenerTiposAlimento()
BEGIN
    SELECT idTipoAlimento, tipoAlimento 
    FROM TipoAlimentos 
    ORDER BY tipoAlimento;
END $$
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_obtenerUnidadesPorTipoAlimento`;
DELIMITER $$
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
END $$
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_agregarTipoUnidadMedidaNuevo`;
DELIMITER $$
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
END $$
DELIMITER ;

-- sugerencias 
DROP PROCEDURE IF EXISTS spu_Listar_TiposYUnidadesAlimentos;
DELIMITER $$
CREATE PROCEDURE spu_Listar_TiposYUnidadesAlimentos(
    IN searchValue VARCHAR(255),
    IN start INT,
    IN length INT
)
BEGIN
    -- Obtener los registros paginados
    SELECT 
        ta.idTipoAlimento,
        ta.tipoAlimento,
        uma.nombreUnidad AS unidadMedida
    FROM TipoAlimentos ta
    LEFT JOIN UnidadesMedidaAlimento uma
        ON 1 = 1 -- Relación manual, muestra todas las unidades
    WHERE 
        (searchValue IS NULL OR searchValue = '' OR 
         ta.tipoAlimento LIKE CONCAT('%', searchValue, '%') OR
         uma.nombreUnidad LIKE CONCAT('%', searchValue, '%'))
    ORDER BY ta.idTipoAlimento DESC
    LIMIT start, length;

    -- Obtener el total de registros filtrados
    SELECT COUNT(*) AS totalFiltered
    FROM TipoAlimentos ta
    LEFT JOIN UnidadesMedidaAlimento uma
        ON 1 = 1 -- Relación manual
    WHERE 
        (searchValue IS NULL OR searchValue = '' OR 
         ta.tipoAlimento LIKE CONCAT('%', searchValue, '%') OR
         uma.nombreUnidad LIKE CONCAT('%', searchValue, '%'));

    -- Obtener el total general de registros sin filtros
    SELECT COUNT(*) AS totalRecords FROM TipoAlimentos;
END $$
DELIMITER ;






-- esto no va es prueba 
/*
CALL spu_Listar_TiposYUnidadesAlimentos('', 0, 10);
-- tipo de equino - alimento ------ 
DROP PROCEDURE IF EXISTS `spu_obtener_tipo_equino_alimento`;
DELIMITER $$
CREATE PROCEDURE spu_obtener_tipo_equino_alimento()
BEGIN
    SELECT idTipoEquino, tipoEquino
    FROM TipoEquinos
    WHERE tipoEquino IN ('Yegua', 'Padrillo', 'Potranca', 'Potrillo');
END $$
DELIMITER ;
*/