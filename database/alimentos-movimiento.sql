-- -------------------------------------------------------------------------------------------------------------------------------------
-- Procedimiento para registrar los alimentos  
DELIMITER $$

CREATE PROCEDURE spu_alimentos_nuevo(
    IN _idUsuario INT,              -- Usuario que realiza la operación
    IN _nombreAlimento VARCHAR(100), -- Nombre del alimento
    IN _tipoAlimento VARCHAR(50),    -- Tipo de alimento (ej. Grano, Heno, Suplemento)
    IN _unidadMedida VARCHAR(10),    -- Unidad de medida del alimento (Kilos, Litros, etc.)
    IN _lote VARCHAR(50),            -- Número de lote del alimento
    IN _costo DECIMAL(10,2),         -- Precio unitario del alimento
    IN _fechaCaducidad DATE,         -- Fecha de caducidad (puede ser NULL)
    IN _cantidad DECIMAL(10,2)       -- Cantidad inicial del alimento
)
BEGIN
    DECLARE _exists INT DEFAULT 0;

    -- Manejador de errores
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error en el registro del nuevo alimento.';
    END;

    -- Convertir el nombre del alimento a minúsculas para consistencia
    SET _nombreAlimento = LOWER(_nombreAlimento);

    -- Iniciar transacción
    START TRANSACTION;

    -- Verificar si el alimento y lote ya están registrados
    SELECT COUNT(*) INTO _exists 
    FROM Alimentos
    WHERE nombreAlimento = _nombreAlimento AND lote = _lote;

    -- Si el alimento y lote no existen, registrar el nuevo alimento
    IF _exists = 0 THEN
        INSERT INTO Alimentos (
            idUsuario, nombreAlimento, tipoAlimento, unidadMedida, lote, costo, fechaCaducidad, cantidad, stockFinal, fechaIngreso, fechaMovimiento, compra
        ) 
        VALUES (
            _idUsuario, _nombreAlimento, _tipoAlimento, _unidadMedida, _lote, _costo, IFNULL(_fechaCaducidad, NULL), _cantidad, _cantidad, NOW(), NOW(), _costo * _cantidad
        );
        COMMIT; -- Confirmar la transacción
    ELSE
        -- Si el alimento y lote ya existen, generar un error
        ROLLBACK;  -- Deshacer la transacción
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El alimento con este lote ya está registrado.';
    END IF;

END $$

DELIMITER ;



-- ------------------------------------------------------------------------------------------------------------------------
-- Procedimiento Entrada de Alimentos -----------------------------------------------------------------------------------
DELIMITER $$

CREATE PROCEDURE spu_alimentos_entrada(
    IN _idUsuario INT,              -- Usuario que realiza la operación
    IN _nombreAlimento VARCHAR(100), -- Nombre del alimento
    IN _cantidad DECIMAL(10,2),      -- Cantidad de alimento a ingresar
    IN _unidadMedida VARCHAR(10),    -- Unidad de medida del alimento
    IN _lote VARCHAR(50),            -- Número de lote del alimento
    IN _fechaCaducidad DATE,         -- Fecha de caducidad del lote (puede ser NULL)
    IN _nuevoPrecio DECIMAL(10,2)    -- Nuevo precio opcional (puede ser NULL)
)
BEGIN
    DECLARE _exists INT DEFAULT 0;
    DECLARE _idAlimento INT;
    DECLARE _currentStock DECIMAL(10,2);
    DECLARE _currentPrice DECIMAL(10,2);
    DECLARE _currentFechaCaducidad DATE;
    DECLARE _nuevoPrecioPonderado DECIMAL(10,2); -- Precio ponderado si el precio es diferente
    DECLARE _cantidadTotal DECIMAL(10,2);        -- Nueva cantidad total para el lote

    -- Manejador de errores
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error en el proceso de entrada de alimentos.';
    END;

    -- Iniciar transacción para asegurar la consistencia de la operación
    START TRANSACTION;

    -- Validaciones estrictas
    IF _cantidad <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La cantidad debe ser mayor que cero.';
    END IF;

    IF _nuevoPrecio IS NOT NULL AND (_nuevoPrecio < 0 OR _nuevoPrecio > 999999.99) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El precio no es válido.';
    END IF;

    IF _lote IS NULL OR _lote = '' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El lote no puede ser vacío o nulo.';
    END IF;

    -- Convertir el nombre del alimento a minúsculas para consistencia
    SET _nombreAlimento = LOWER(_nombreAlimento);

    -- Verificar si ya existe un lote con el mismo nombre de alimento y número de lote
    SELECT COUNT(*), idAlimento, stockFinal, costo, fechaCaducidad 
    INTO _exists, _idAlimento, _currentStock, _currentPrice, _currentFechaCaducidad
    FROM Alimentos
    WHERE nombreAlimento = _nombreAlimento AND lote = _lote;

    -- Caso 1: Si el lote ya existe y la fecha de caducidad es la misma (o NULL en ambos), actualizar el stock
    IF _exists > 0 AND (_currentFechaCaducidad = _fechaCaducidad OR (_currentFechaCaducidad IS NULL AND _fechaCaducidad IS NULL)) THEN
        SET _cantidadTotal = _currentStock + _cantidad;
        UPDATE Alimentos
        SET stockFinal = _cantidadTotal,
            fechaMovimiento = NOW()
        WHERE idAlimento = _idAlimento;

        -- Calcular el precio ponderado si el precio nuevo es diferente
        IF _nuevoPrecio IS NOT NULL AND _nuevoPrecio != _currentPrice THEN
            SET _nuevoPrecioPonderado = (
                (_currentPrice * _currentStock) + (_nuevoPrecio * _cantidad)
            ) / _cantidadTotal;

            -- Actualizar el nuevo precio ponderado
            UPDATE Alimentos
            SET costo = _nuevoPrecioPonderado
            WHERE idAlimento = _idAlimento;
        END IF;

    -- Caso 2: Si el lote ya existe pero la fecha de caducidad es diferente, crear un nuevo lote
    ELSEIF _exists > 0 AND _currentFechaCaducidad != _fechaCaducidad THEN
        INSERT INTO Alimentos (
            idUsuario, nombreAlimento, cantidad, unidadMedida, lote, fechaCaducidad, 
            idTipomovimiento, stockFinal, fechaIngreso, fechaMovimiento, costo
        ) 
        VALUES (
            _idUsuario, _nombreAlimento, _cantidad, _unidadMedida, _lote, _fechaCaducidad, 
            1, _cantidad, NOW(), NOW(), _nuevoPrecio
        );

    -- Caso 3: Si el lote no existe, crear uno nuevo
    ELSE
        INSERT INTO Alimentos (
            idUsuario, nombreAlimento, cantidad, unidadMedida, lote, fechaCaducidad, 
            idTipomovimiento, stockFinal, fechaIngreso, fechaMovimiento, costo
        ) 
        VALUES (
            _idUsuario, _nombreAlimento, _cantidad, _unidadMedida, _lote, _fechaCaducidad, 
            1, _cantidad, NOW(), NOW(), _nuevoPrecio
        );
    END IF;

    -- Registrar la entrada en el historial de movimientos
    INSERT INTO HistorialMovimientos (idAlimento, tipoMovimiento, cantidad, idUsuario, fechaMovimiento)
    VALUES (IFNULL(_idAlimento, LAST_INSERT_ID()), 'Entrada', _cantidad, _idUsuario, NOW());

    -- Confirmar la transacción
    COMMIT;

END $$

DELIMITER ;




-- ------------------------------------------------------------------------------------------------------------------------
-- Procedimiento Salida de Alimentos -----------------------------------------------------------------------------------
DELIMITER $$

CREATE PROCEDURE spu_alimentos_salida(
    IN _idUsuario INT,              -- Usuario que realiza la operación
    IN _nombreAlimento VARCHAR(100), -- Nombre del alimento
    IN _cantidad DECIMAL(10,2),      -- Cantidad que se retira
    IN _idTipoEquino INT,            -- Tipo de animal al que va la salida
    IN _merma DECIMAL(10,2)          -- Cantidad de merma (pérdida) en la salida
)
BEGIN
    DECLARE _cantidadNecesaria DECIMAL(10,2);    -- Cantidad total necesaria (cantidad + merma)
    DECLARE _idAlimento INT;                     -- ID del alimento (lote) que se está procesando
    DECLARE _currentStock DECIMAL(10,2);         -- Stock actual del lote que se está procesando
    DECLARE _totalStock DECIMAL(10,2);           -- Stock total disponible de todos los lotes del alimento

    -- Manejador de errores: marcar error y hacer ROLLBACK
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
    END;

    -- Iniciar la transacción para garantizar la consistencia
    START TRANSACTION;

    -- Validar que la cantidad a retirar sea mayor que cero
    IF _cantidad <= 0 THEN
        -- No se puede continuar, ROLLBACK y terminar
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error: La cantidad a retirar debe ser mayor que cero.';
        -- No usar LEAVE o RETURN, simplemente deja que el procedimiento termine aquí con el error
    END IF;

    -- Calcular el stock total disponible del alimento
    SELECT SUM(stockFinal) INTO _totalStock
    FROM Alimentos
    WHERE nombreAlimento = LOWER(_nombreAlimento);

    -- Verificar si hay stock suficiente
    IF _totalStock IS NULL THEN
        -- No se encontró stock disponible, hacer ROLLBACK y terminar
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error: No se encontró stock para el alimento solicitado.';
    END IF;

    -- Verificar si hay suficiente stock para cubrir la cantidad solicitada más la merma
    IF _totalStock < (_cantidad + _merma) THEN
        -- No hay suficiente stock, hacer ROLLBACK y terminar
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error: No hay suficiente stock disponible para esta cantidad.';
    END IF;

    -- Asignar la cantidad total necesaria (cantidad + merma)
    SET _cantidadNecesaria = _cantidad + _merma;

    -- Mientras la cantidad necesaria sea mayor que cero, procesar por lotes (FIFO)
    WHILE _cantidadNecesaria > 0 DO
        -- Seleccionar el lote más antiguo con stock disponible
        SELECT idAlimento, stockFinal INTO _idAlimento, _currentStock
        FROM Alimentos
        WHERE nombreAlimento = LOWER(_nombreAlimento)
        ORDER BY fechaCaducidad ASC
        LIMIT 1 FOR UPDATE;

        -- Verificar si hay un lote disponible
        IF _idAlimento IS NULL THEN
            -- No hay lotes disponibles, hacer ROLLBACK y terminar
            ROLLBACK;
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error: No se encontraron lotes disponibles para este alimento.';
        END IF;

        -- Si el lote tiene suficiente stock para cubrir la cantidad necesaria
        IF _currentStock >= _cantidadNecesaria THEN
            -- Restar la cantidad necesaria del stock del lote
            UPDATE Alimentos
            SET stockFinal = stockFinal - _cantidadNecesaria,
                idTipoEquino = _idTipoEquino,
                fechaMovimiento = NOW()
            WHERE idAlimento = _idAlimento;

            -- Registrar la salida y la merma en el historial
            INSERT INTO HistorialMovimientos (idAlimento, tipoMovimiento, cantidad, merma, idUsuario, fechaMovimiento)
            VALUES (_idAlimento, 'Salida', _cantidad, _merma, _idUsuario, NOW());

            -- Finalizar el ciclo, ya que toda la cantidad necesaria ha sido cubierta
            SET _cantidadNecesaria = 0;

        -- Si el lote no tiene suficiente stock para cubrir toda la cantidad necesaria
        ELSE
            -- Usar todo el stock del lote y continuar con el siguiente lote
            UPDATE Alimentos
            SET stockFinal = 0,
                idTipoEquino = _idTipoEquino,
                fechaMovimiento = NOW()
            WHERE idAlimento = _idAlimento;

            -- Registrar la salida parcial y la merma en el historial
            INSERT INTO HistorialMovimientos (idAlimento, tipoMovimiento, cantidad, merma, idUsuario, fechaMovimiento)
            VALUES (_idAlimento, 'Salida', _currentStock, _merma, _idUsuario, NOW());

            -- Reducir la cantidad necesaria en la cantidad usada del lote actual
            SET _cantidadNecesaria = _cantidadNecesaria - _currentStock;

            -- Eliminar el lote si se ha agotado completamente
            DELETE FROM Alimentos WHERE idAlimento = _idAlimento;

            -- Registrar la eliminación del lote en el historial
            INSERT INTO HistorialMovimientos (idAlimento, tipoMovimiento, cantidad, idUsuario, fechaMovimiento)
            VALUES (_idAlimento, 'Lote Eliminado', 0, _idUsuario, NOW());
        END IF;
    END WHILE;

    -- Confirmar la transacción
    COMMIT;

END $$

DELIMITER ;


-- Salida sin merma
CALL spu_alimentos_salida(
    3,                 -- idUsuario (El ID del usuario que realiza la operación)
    'pepino',          -- nombreAlimento (El nombre del alimento, en este caso 'pepino')
    2,                 -- cantidad (Cantidad a retirar del inventario, en este caso 2 unidades)
    2,                 -- idTipoEquino (El ID del tipo de equino al que va la salida, en este caso 2 para 'Padrillo')
    0                  -- merma (No hay merma en este caso, por lo que se indica 0)
);

SELECT * FROM Alimentos WHERE nombreAlimento = 'pepino';
SELECT idAlimento, nombreAlimento, stockFinal FROM Alimentos WHERE nombreAlimento = 'pepino';



-- ------------------------------------------------------------------------------------------------------------------------
-- Procedimiento para notificar Stock Bajo-----------------------------------------
DELIMITER $$

CREATE PROCEDURE spu_notificar_stock_bajo(
    IN _minimoStock DECIMAL(10,2)  -- Valor de stock mínimo para generar la alerta
)
BEGIN
    DECLARE _mensaje VARCHAR(255);
    DECLARE _totalLotesBajoStock INT;
    DECLARE _nombreAlimento VARCHAR(100);  -- Declarar la variable para nombreAlimento
    DECLARE _lote VARCHAR(50);  -- Declarar la variable para lote
    DECLARE _stockFinal DECIMAL(10,2);  -- Declarar la variable para stockFinal

    -- Manejador de errores
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error en la notificación de stock bajo.';
    END;

    -- Iniciar transacción para evitar inconsistencias en los cálculos de stock
    START TRANSACTION;

    -- Verificar cuántos lotes tienen un stock por debajo del mínimo
    SELECT COUNT(*) INTO _totalLotesBajoStock
    FROM Alimentos
    WHERE stockFinal < _minimoStock;

    -- Si hay lotes con stock bajo, generar una alerta
    IF _totalLotesBajoStock > 0 THEN
        -- Seleccionar los detalles del alimento, lote y stock
        SELECT nombreAlimento, lote, stockFinal 
        INTO _nombreAlimento, _lote, _stockFinal
        FROM Alimentos
        WHERE stockFinal < _minimoStock
        LIMIT 1;  -- Limitar a un registro si se busca generar una única alerta por vez
        
        -- Generar mensaje de alerta
        SET _mensaje = CONCAT('El lote ', _lote, ' del alimento ', _nombreAlimento, ' tiene stock bajo: ', _stockFinal);
        
        -- Lanzar la alerta
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = _mensaje;
    END IF;

    -- Confirmar la transacción
    COMMIT;
END $$

DELIMITER ;



-- --------------------------------------------------------------------------------------------------------------------
-- Procedimiento para historial Alimentos -----------------------------------------
DELIMITER $$

CREATE PROCEDURE spu_historial_completo(
    IN _tipoMovimiento VARCHAR(50),
    IN _fechaInicio DATE,
    IN _fechaFin DATE,
    IN _idUsuario INT,
    IN _limit INT,
    IN _offset INT
)
BEGIN
    -- Validar los límites de la paginación
    IF _limit <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El límite de registros debe ser mayor que cero.';
    END IF;

    IF _offset < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El desplazamiento no puede ser negativo.';
    END IF;

    -- Consulta del historial con filtros opcionales y paginación
    SELECT idAlimento, tipoMovimiento, cantidad, merma, idUsuario, fechaMovimiento
    FROM HistorialMovimientos
    WHERE (_tipoMovimiento = '' OR tipoMovimiento = _tipoMovimiento)
      AND (_fechaInicio = '1900-01-01' OR fechaMovimiento >= _fechaInicio)
      AND (_fechaFin = CURDATE() OR fechaMovimiento <= _fechaFin)
      AND (_idUsuario = 0 OR idUsuario = _idUsuario)
    ORDER BY fechaMovimiento DESC
    LIMIT _limit OFFSET _offset;
END $$

DELIMITER ;


-- ----------------------------------------------------------------------------------------------------------------------------------------------------------------------
-- Prueba con fecha de caducidad definida
CALL spu_alimentos_nuevo(
    3,                 -- idUsuario
    'pepino',         -- nombreAlimento
    'Vegetal',         -- tipoAlimento
    'Kilos',           -- unidadMedida
    'Lote17',         -- lote
    2.50,              -- costo
    '2024-12-31',      -- fechaCaducidad
    200                -- cantidad
);

-- Prueba con fecha de caducidad como "No definida"
CALL spu_alimentos_nuevo(
    3,                 -- idUsuario
    'manzana',          -- nombreAlimento
    'Fruta',           -- tipoAlimento
    'Kilos',           -- unidadMedida
    'Lote002',         -- lote
    3.00,              -- costo
    NULL,              -- fechaCaducidad "No definida"
    50                 -- cantidad
);

-- -------------------------------------------------------------------------------------------
-- Prueba de entrada con nuevo precio
CALL spu_alimentos_entrada(
    3,                 -- idUsuario
    'Lechugaa',         -- nombreAlimento
    500,                -- cantidad a ingresar
    'Kilos',           -- unidadMedida
    'Lote011',         -- lote
    '2024-12-31',      -- fechaCaducidad
    2.75               -- nuevoPrecio
);

SELECT * FROM Alimentos WHERE nombreAlimento = 'lechugaa' AND lote = 'Lote011';

-- Prueba de entrada sin nuevo precio (usar el precio anterior)
CALL spu_alimentos_entrada(
    3,                 -- idUsuario
    'Manzana',          -- nombreAlimento
    25,                -- cantidad a ingresar
    'Kilos',           -- unidadMedida
    'Lote002',         -- lote
    NULL,              -- fechaCaducidad "No definida"
    NULL               -- Sin nuevoPrecio
);

-- ------------------------------------------------------------------------------------------
-- Prueba de salida con merma
CALL spu_alimentos_salida(
    3,                 -- idUsuario
    'pepino',          -- nombreAlimento
    2,                -- cantidad a retirar
    2,                 -- idTipoEquino (ej. 2 para Padrillo)
    0                  -- Sin merma
);

CALL spu_alimentos_salida(
    3,                 -- idUsuario
    'pepino',          -- nombreAlimento
    3,                -- cantidad a retirar
    2,                 -- idTipoEquino (ej. 2 para Padrillo)
    5                  -- merma (cantidad de pérdida)
);



-- -------------------------------------------------------------------
-- Prueba con mínimo de stock establecido en 30
CALL spu_notificar_stock_bajo(30);

-- Prueba con mínimo de stock establecido en 10
CALL spu_notificar_stock_bajo(10);

-- ---------------------------------------------------------------------------------------
-- Prueba de historial completo sin filtros (todos los movimientos)
CALL spu_historial_completo(
    '',                -- Tipo de movimiento ('' para todos)
    '2024-01-01',      -- Fecha de inicio
    CURDATE(),         -- Fecha de fin
    0,                 -- idUsuario (0 para todos)
    100,               -- Límite
    0                  -- Offset
);

-- Prueba de historial filtrado por tipo "Entrada"
CALL spu_historial_completo(
    'Entrada',         -- Filtrar solo por entradas
    '2024-01-01',      -- Fecha de inicio
    CURDATE(),         -- Fecha de fin
    0,                 -- idUsuario (0 para todos)
    100,               -- Límite
    0                  -- Offset
);


select * from alimentos;
select * from usuarios;
SELECT * FROM TipoMovimientos;

SELECT * FROM Alimentos WHERE nombreAlimento = 'lechugaa' AND lote = 'Lote011';

