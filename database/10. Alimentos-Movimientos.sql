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


-- Procedimiento Entrada de Alimentos -----------------------------------------------------------------------------------
DELIMITER $$
CREATE PROCEDURE spu_alimentos_entrada(
    IN _idUsuario INT,              -- Usuario que realiza la operación
    IN _nombreAlimento VARCHAR(100), -- Nombre del alimento
    IN _tipoAlimento VARCHAR(50),    -- Tipo de alimento (ej. Grano, Heno, Suplemento)
    IN _unidadMedida VARCHAR(10),    -- Unidad de medida del alimento
    IN _lote VARCHAR(50),            -- Número de lote del alimento
    IN _fechaCaducidad DATE,         -- Fecha de caducidad del lote (puede ser NULL)
    IN _cantidad DECIMAL(10,2),      -- Cantidad de alimento a ingresar
    IN _nuevoPrecio DECIMAL(10,2)    -- Nuevo precio opcional (puede ser NULL)
)
BEGIN
    DECLARE _existsLote INT DEFAULT 0;
    DECLARE _idAlimento INT;
    DECLARE _precioAnterior DECIMAL(10,2); -- Precio del lote anterior si existe

    -- Manejador de errores
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
    END;

    -- Convertir el nombre del alimento a minúsculas para consistencia
    SET _nombreAlimento = LOWER(_nombreAlimento);

    -- Iniciar transacción
    START TRANSACTION;

    -- Verificar si el lote ya está registrado para este alimento y obtener el precio
    SELECT COUNT(*), idAlimento, costo
    INTO _existsLote, _idAlimento, _precioAnterior
    FROM Alimentos
    WHERE nombreAlimento = _nombreAlimento AND lote = _lote;

    -- Si el lote ya existe, revertimos la transacción (no se permite lote duplicado)
    IF _existsLote > 0 THEN
        ROLLBACK; -- Si el lote ya existe, no registramos otro lote igual
    ELSE
        -- Si el lote no existe, registrar un nuevo lote para este alimento
        -- Si no se proporciona un nuevo precio, usar el precio del lote anterior
        INSERT INTO Alimentos (
            idUsuario, nombreAlimento, tipoAlimento, unidadMedida, lote, costo, fechaCaducidad, cantidad, stockFinal, fechaIngreso, fechaMovimiento, compra
        ) 
        VALUES (
            _idUsuario, _nombreAlimento, _tipoAlimento, _unidadMedida, _lote, 
            IFNULL(_nuevoPrecio, _precioAnterior), -- Usar el nuevo precio si es proporcionado, o el anterior si es NULL
            _fechaCaducidad, _cantidad, _cantidad, NOW(), NOW(), 
            IFNULL(_nuevoPrecio, _precioAnterior) * _cantidad -- Calcular el total de compra
        );

        -- Registrar la entrada en el historial de movimientos
        INSERT INTO HistorialMovimientos (idAlimento, tipoMovimiento, cantidad, idUsuario, fechaMovimiento)
        VALUES (LAST_INSERT_ID(), 'Entrada', _cantidad, _idUsuario, NOW());

        -- Confirmar la transacción
        COMMIT;
    END IF;

END $$
DELIMITER ;


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
    COMMIT;
END $$
DELIMITER ;



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
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error en la notificación de stock bajo.';
    END;

    START TRANSACTION;

    SELECT COUNT(*) INTO _totalLotesBajoStock
    FROM Alimentos
    WHERE stockFinal < _minimoStock;

    IF _totalLotesBajoStock > 0 THEN
        SELECT nombreAlimento, lote, stockFinal 
        INTO _nombreAlimento, _lote, _stockFinal
        FROM Alimentos
        WHERE stockFinal < _minimoStock
        LIMIT 1;        
        SET _mensaje = CONCAT('El lote ', _lote, ' del alimento ', _nombreAlimento, ' tiene stock bajo: ', _stockFinal);
        
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = _mensaje;
    END IF;

    COMMIT;
END $$
DELIMITER ;



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

    -- Consulta para obtener el historial completo, uniendo la tabla de movimientos y alimentos
    SELECT 
        h.idAlimento,
        a.nombreAlimento,               -- Nombre del alimento
        a.tipoAlimento,                 -- Tipo de alimento (Grano, Heno, etc.)
        a.unidadMedida,                 -- Unidad de medida del alimento
        a.lote,                         -- Lote del alimento
        a.fechaCaducidad,               -- Fecha de caducidad del lote
        h.tipoMovimiento,               -- Tipo de movimiento (Entrada/Salida)
        h.cantidad,                     -- Cantidad del movimiento
        h.merma,                        -- Merma (si aplica)
        h.idUsuario,                    -- Usuario que hizo el movimiento
        h.fechaMovimiento,              -- Fecha del movimiento
        a.costo                         -- Costo del alimento en el momento del movimiento
    FROM 
        HistorialMovimientos h
    JOIN 
        Alimentos a ON h.idAlimento = a.idAlimento  -- Unimos ambas tablas por idAlimento
    WHERE 
        (_tipoMovimiento = '' OR h.tipoMovimiento = _tipoMovimiento)
        AND (_fechaInicio = '1900-01-01' OR h.fechaMovimiento >= _fechaInicio)
        AND (_fechaFin = CURDATE() OR h.fechaMovimiento <= _fechaFin)
        AND (_idUsuario = 0 OR h.idUsuario = _idUsuario)
    ORDER BY 
        h.fechaMovimiento DESC
    LIMIT 
        _limit OFFSET _offset;
END $$
DELIMITER ;
