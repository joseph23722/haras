-- -------------------------------------------------------------------------------------------------------------------------------------
-- Procedimiento para registrar los alimentos  y manejar los movimintos entrada y salida 
DELIMITER $$

CREATE PROCEDURE spu_alimentos_nuevo(
    IN _idUsuario INT,
    IN _nombreAlimento VARCHAR(100),
    IN _tipoAlimento VARCHAR(50),
    IN _cantidad DECIMAL(10,2),
    IN _unidadMedida VARCHAR(10),
    IN _costo DECIMAL(10,2),
    IN _lote VARCHAR(50),
    IN _fechaCaducidad DATE,
    IN _fechaIngreso DATETIME
)
BEGIN
    DECLARE _exists INT DEFAULT 0;
    DECLARE _compra DECIMAL(10,2);

    -- Verificar que la cantidad y el costo sean válidos
    IF _cantidad <= 0 OR _costo <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La cantidad y el costo deben ser mayores que cero.';
    END IF;

    -- Convertir el nombre del alimento a minúsculas antes de la verificación
    SET _nombreAlimento = LOWER(_nombreAlimento);

    -- Calcular el costo total de la compra
    SET _compra = _cantidad * _costo;

    -- Verificar si el alimento ya existe para ese lote
    SELECT COUNT(*) INTO _exists 
    FROM Alimentos
    WHERE nombreAlimento = _nombreAlimento
      AND lote = _lote;

    -- Si el alimento ya existe, actualiza el stock
    IF _exists > 0 THEN
        UPDATE Alimentos
        SET cantidad = cantidad + _cantidad,
            stockFinal = stockFinal + _cantidad,
            fechaMovimiento = NOW()
        WHERE nombreAlimento = _nombreAlimento
          AND lote = _lote;
    ELSE
        -- Insertar un nuevo registro si es un alimento/lote nuevo
        INSERT INTO Alimentos (
            idUsuario, 
            nombreAlimento, 
            tipoAlimento, 
            cantidad, 
            unidadMedida, 
            costo, 
            lote, 
            fechaCaducidad, 
            idTipomovimiento, 
            stockFinal, 
            fechaIngreso, 
            fechaMovimiento, 
            compra
        ) 
        VALUES (
            _idUsuario, 
            _nombreAlimento, 
            _tipoAlimento,
            _cantidad, 
            _unidadMedida,
            _costo, 
            _lote, 
            _fechaCaducidad,
            1,  -- '1' indica una entrada
            _cantidad,  -- El stock inicial es igual a la cantidad ingresada
            _fechaIngreso,
            NOW(),  -- Registrar el momento del movimiento
            _compra  -- Costo total de la compra
        );
    END IF;
END $$

DELIMITER ;

-- ------------------------------------------------------------------------------------------------------------------------
-- Procedimiento Entrada y Salida de Alimentos -----------------------------------------------------------------------------------
DELIMITER $$

CREATE PROCEDURE spu_alimentos_movimiento(
    IN _idUsuario INT,
    IN _nombreAlimento VARCHAR(100),
    IN _tipoAlimento VARCHAR(50),
    IN _cantidad DECIMAL(10,2),
    IN _unidadMedida VARCHAR(10),
    IN _costo DECIMAL(10,2),
    IN _lote VARCHAR(50),
    IN _fechaCaducidad DATE,
    IN _idTipomovimiento INT,  -- '1' para entrada, '2' para salida
    IN _idTipoEquino INT,      -- Solo se usa en salidas
    IN _merma DECIMAL(10,2)    -- Registro de la merma en salida
)
BEGIN
    DECLARE _exists INT DEFAULT 0;
    DECLARE _currentStock DECIMAL(10,2);
    DECLARE _newStock DECIMAL(10,2);
    DECLARE _compra DECIMAL(10,2);
    DECLARE _idAlimento INT;
    DECLARE _cantidadNecesaria DECIMAL(10,2);
    DECLARE _totalMerma DECIMAL(10,2);

    -- Manejo de errores y verificaciones
    IF _cantidad <= 0 OR _costo <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La cantidad y el costo deben ser mayores que cero.';
    END IF;

    -- Convertir el nombre del alimento a minúsculas para consistencia
    SET _nombreAlimento = LOWER(_nombreAlimento);

    -- Manejamos las entradas
    IF _idTipomovimiento = 1 THEN
        -- Verificar si el lote ya existe para el mismo alimento
        SELECT COUNT(*) INTO _exists 
        FROM Alimentos
        WHERE nombreAlimento = _nombreAlimento 
        AND lote = _lote
        AND fechaCaducidad = _fechaCaducidad;  -- Asegurarse de comparar también la fecha de caducidad

        -- Si el lote y la fecha de caducidad ya existen, actualiza el stock
        IF _exists > 0 THEN
            SELECT idAlimento, stockFinal INTO _idAlimento, _currentStock
            FROM Alimentos
            WHERE nombreAlimento = _nombreAlimento 
            AND lote = _lote 
            AND fechaCaducidad = _fechaCaducidad  -- Asegurarse de que coincida la fecha
            FOR UPDATE;

            SET _newStock = _currentStock + _cantidad;

            -- Actualizar el stock sin cambiar la fecha de caducidad ni otros detalles
            UPDATE Alimentos
            SET stockFinal = _newStock, 
                fechaMovimiento = NOW(),  -- Actualizar solo la fecha de movimiento
                costo = _costo  -- Actualizar el costo si cambia
            WHERE idAlimento = _idAlimento;

        ELSE
            -- Si es un nuevo lote o nueva fecha de caducidad, insertar un nuevo registro
            INSERT INTO Alimentos (
                idUsuario, 
                nombreAlimento, 
                tipoAlimento, 
                cantidad, 
                unidadMedida,
                costo, 
                lote, 
                fechaCaducidad,
                idTipomovimiento, 
                stockFinal, 
                fechaIngreso, 
                compra, 
                fechaMovimiento
            ) 
            VALUES (
                _idUsuario, 
                _nombreAlimento, 
                _tipoAlimento, 
                _cantidad, 
                _unidadMedida,
                _costo, 
                _lote, 
                _fechaCaducidad, -- La nueva fecha de caducidad para este lote
                1,  -- Entrada
                _cantidad,  -- Stock inicial
                NOW(), 
                _cantidad * _costo, 
                NOW()
            );
        END IF;

    -- Manejamos las salidas
    ELSEIF _idTipomovimiento = 2 THEN
        SET _cantidadNecesaria = _cantidad;
        SET _totalMerma = _merma;

        -- Loop para gestionar múltiples lotes si es necesario (FIFO)
        WHILE _cantidadNecesaria > 0 DO
            -- Seleccionar el lote más antiguo (por fecha de caducidad)
            SELECT idAlimento, stockFinal INTO _idAlimento, _currentStock
            FROM Alimentos
            WHERE nombreAlimento = _nombreAlimento
            ORDER BY fechaCaducidad ASC
            LIMIT 1
            FOR UPDATE;

            -- Verificar si hay suficiente stock en este lote
            IF _currentStock >= (_cantidadNecesaria + _totalMerma) THEN
                -- Restar la cantidad y actualizar el stock
                SET _newStock = _currentStock - (_cantidadNecesaria + _totalMerma);

                UPDATE Alimentos
                SET stockFinal = _newStock, 
                    idTipoEquino = _idTipoEquino,
                    merma = _totalMerma,
                    fechaMovimiento = NOW()
                WHERE idAlimento = _idAlimento;

                -- Registrar la salida en el historial
                INSERT INTO HistorialMovimientos (idAlimento, tipoMovimiento, cantidad, idUsuario, fechaMovimiento)
                VALUES (_idAlimento, 'Salida', _cantidadNecesaria, _idUsuario, NOW());

                -- Salida completada
                SET _cantidadNecesaria = 0;

            ELSE
                -- Si el stock no es suficiente, usar todo el lote y continuar con el siguiente
                SET _newStock = 0;
                SET _cantidadNecesaria = _cantidadNecesaria - _currentStock;

                -- Actualizar el stock y registrar la salida parcial
                UPDATE Alimentos
                SET stockFinal = 0,
                    idTipoEquino = _idTipoEquino,
                    merma = _totalMerma,
                    fechaMovimiento = NOW()
                WHERE idAlimento = _idAlimento;

                -- Registrar la salida parcial en el historial
                INSERT INTO HistorialMovimientos (idAlimento, tipoMovimiento, cantidad, idUsuario, fechaMovimiento)
                VALUES (_idAlimento, 'Salida', _currentStock, _idUsuario, NOW());
            END IF;
        END WHILE;

    ELSE
        -- Si el tipo de movimiento no es válido
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Tipo de movimiento no válido.';
    END IF;
END $$

DELIMITER ;

-- ------------------------------------------------------------------------------------------------------------------------
-- Procedimiento para Reporte de Alimentos Próximos a Caducar o con Stock Bajo-----------------------------------------
DELIMITER $$

CREATE PROCEDURE spu_reporte_inventario(
    IN _dias INT -- Días para revisar los alimentos que caducarán en los próximos _dias
)
BEGIN
    -- Alimentos que están por caducar en los próximos días
    SELECT nombreAlimento, lote, stockFinal, fechaCaducidad
    FROM Alimentos
    WHERE fechaCaducidad BETWEEN CURDATE() AND CURDATE() + INTERVAL _dias DAY;

    -- Alimentos cuyo stock está por debajo del mínimo
    SELECT nombreAlimento, lote, stockFinal, stockMinimo
    FROM Alimentos
    WHERE stockFinal < stockMinimo;
END $$

DELIMITER ;


-- ------------------------------------------------------------------------------------------------------------------------
-- Procedimiento para Verificar Alimentos Caducados-----------------------------------------

DELIMITER $$

CREATE PROCEDURE spu_verificar_caducidad()
BEGIN
    DECLARE _caducados INT;
    DECLARE _mensaje VARCHAR(255);

    -- Seleccionar todos los alimentos caducados
    SELECT COUNT(*) INTO _caducados 
    FROM Alimentos
    WHERE fechaCaducidad < CURDATE();
    
    IF _caducados > 0 THEN
        -- Crear el mensaje concatenado
        SET _mensaje = CONCAT('Hay ', _caducados, ' alimentos que han caducado.');
        
        -- Lanzar la señal con el mensaje concatenado
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = _mensaje;
    END IF;
END $$

DELIMITER ;