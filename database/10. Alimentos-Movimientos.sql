-- Procedimiento para registrar los alimentos  
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
END $$
DELIMITER ;

-- -------
DROP PROCEDURE IF EXISTS `spu_obtenerAlimentosConLote`;
DELIMITER $$
CREATE PROCEDURE spu_obtenerAlimentosConLote(IN _idAlimento INT)
BEGIN
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
END $$
DELIMITER ;

-- Procedimiento Entrada de Alimentos -----------------------------------------------------------------------------------
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

-- Procedimiento Salida de Alimentos 
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

-- -----------
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

-- Procedimiento para notificar Stock Bajo-----------------------------------------
DROP PROCEDURE IF EXISTS `spu_notificar_stock_bajo_alimentos`;
DELIMITER $$
CREATE PROCEDURE spu_notificar_stock_bajo_alimentos()
BEGIN
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
END $$
DELIMITER ;

-- Procedimiento para historial Alimentos -----------------------------------------
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

-- separado  

-- Procedimiento para obtener la lista de tipos de alimentos
DROP PROCEDURE IF EXISTS `spu_obtenerTiposAlimento`;
DELIMITER $$
CREATE PROCEDURE spu_obtenerTiposAlimento()
BEGIN
    SELECT idTipoAlimento, tipoAlimento 
    FROM TipoAlimentos 
    ORDER BY tipoAlimento;
END $$
DELIMITER ;

-- Procedimiento para obtener las unidades de medida asociadas a un tipo de alimento
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

-- agregar tipo y unidad 
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

-- esto no va es prueba 
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

-- -------------------------------------------------------------------------------