-- Procedimiento para registrar los alimentos  
DELIMITER $$
CREATE PROCEDURE spu_alimentos_nuevo(
    IN _idUsuario INT,              
    IN _nombreAlimento VARCHAR(100), 
    IN _tipoAlimento VARCHAR(50),    
    IN _unidadMedida VARCHAR(10),    
    IN _lote VARCHAR(50),            -- Número de lote enviado desde el formulario
    IN _costo DECIMAL(10,2),         
    IN _fechaCaducidad DATE,         
    IN _stockActual DECIMAL(10,2),   
    IN _stockMinimo DECIMAL(10,2)    
)
BEGIN
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

END $$
DELIMITER ;


-- -------
DELIMITER $$
CREATE PROCEDURE spu_obtenerAlimentosConLote()
BEGIN
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
END $$
DELIMITER ;


-- Procedimiento Entrada de Alimentos -----------------------------------------------------------------------------------
DELIMITER $$

CREATE PROCEDURE spu_alimentos_entrada(
    IN _idUsuario INT,
    IN _nombreAlimento VARCHAR(100),
    IN _unidadMedida VARCHAR(10),
    IN _lote VARCHAR(50),
    IN _cantidad DECIMAL(10,2)
)
BEGIN
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

END $$
DELIMITER ;


-- Procedimiento Salida de Alimentos 
DELIMITER $$
CREATE PROCEDURE spu_alimentos_salida(
    IN _idUsuario INT,
    IN _nombreAlimento VARCHAR(100),
    IN _unidadMedida VARCHAR(10),
    IN _cantidad DECIMAL(10,2),
    IN _idTipoEquino INT,
    IN _lote VARCHAR(50),
    IN _merma DECIMAL(10,2)
)
BEGIN
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

END $$
DELIMITER ;



-- -----------
DELIMITER $$
CREATE PROCEDURE spu_listar_lotes_alimentos()
BEGIN
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
END $$
DELIMITER ;




-- Procedimiento para notificar Stock Bajo-----------------------------------------
DELIMITER $$
CREATE PROCEDURE spu_notificar_stock_bajo_alimentos()
BEGIN
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
END $$
DELIMITER ;


-- Procedimiento para historial Alimentos -----------------------------------------
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
END $$
DELIMITER ;



-- tipo de equino - alimento ------ 
DELIMITER $$
CREATE PROCEDURE spu_obtener_tipo_equino_alimento()
BEGIN
    SELECT idTipoEquino, tipoEquino
    FROM TipoEquinos
    WHERE tipoEquino IN ('Yegua', 'Padrillo', 'Potranca', 'Potrillo');
END $$
DELIMITER ;

-- -------------------------------------------------------------------------------




