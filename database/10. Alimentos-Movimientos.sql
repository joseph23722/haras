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
    IN _stockActual DECIMAL(10,2),   -- Cantidad inicial de stock (ahora stockActual)
    IN _stockMinimo DECIMAL(10,2)    -- Stock mínimo (umbral para cambiar el estado)
)
BEGIN
    DECLARE _exists INT DEFAULT 0;
    DECLARE _estado ENUM('Disponible', 'Por agotarse', 'Agotado');

    -- Manejador de errores
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error en el registro del nuevo alimento.';
    END;

    -- Convertir el nombre del alimento a minúsculas para consistencia
    SET _nombreAlimento = LOWER(_nombreAlimento);

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

    -- Verificar si el alimento y lote ya están registrados
    SELECT COUNT(*) INTO _exists 
    FROM Alimentos
    WHERE nombreAlimento = _nombreAlimento AND lote = _lote;

    -- Si el alimento y lote no existen, registrar el nuevo alimento
    IF _exists = 0 THEN
        INSERT INTO Alimentos (
            idUsuario, nombreAlimento, tipoAlimento, unidadMedida, lote, costo, fechaCaducidad, 
            stockActual, stockMinimo, estado, fechaIngreso, fechaMovimiento, compra
        ) 
        VALUES (
            _idUsuario, _nombreAlimento, _tipoAlimento, _unidadMedida, _lote, _costo, IFNULL(_fechaCaducidad, NULL),
            _stockActual, _stockMinimo, _estado, NOW(), NOW(), _costo * _stockActual
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
    DECLARE _idAlimento INT DEFAULT NULL;
    DECLARE _precioAnterior DECIMAL(10,2) DEFAULT 0; -- Precio del lote anterior si existe

    -- Convertir el nombre del alimento a minúsculas para consistencia
    SET _nombreAlimento = LOWER(_nombreAlimento);

    -- Verificar si el lote ya está registrado para este alimento y obtener el precio
    SELECT COUNT(*), idAlimento, IFNULL(costo, 0)
    INTO _existsLote, _idAlimento, _precioAnterior
    FROM Alimentos
    WHERE nombreAlimento = _nombreAlimento AND lote = _lote;

    -- Si el lote ya existe, actualizamos el stock y el precio si se proporciona uno nuevo
    IF _existsLote > 0 THEN
        -- Actualizar stock y opcionalmente el precio
        UPDATE Alimentos
        SET stockActual = stockActual + _cantidad, 
            costo = IFNULL(_nuevoPrecio, costo),
            compra = IFNULL(_nuevoPrecio, costo) * stockActual,
            fechaMovimiento = NOW()
        WHERE idAlimento = _idAlimento;

        -- Registrar la entrada en el historial de movimientos
        INSERT INTO HistorialMovimientos (idAlimento, tipoMovimiento, cantidad, idUsuario, fechaMovimiento)
        VALUES (_idAlimento, 'Entrada', _cantidad, _idUsuario, NOW());

    ELSE
        -- Si el lote no existe, registrar un nuevo lote para este alimento
        INSERT INTO Alimentos (
            idUsuario, nombreAlimento, tipoAlimento, unidadMedida, lote, 
            costo, fechaCaducidad, stockActual, stockMinimo, estado, 
            fechaIngreso, compra, fechaMovimiento
        ) 
        VALUES (
            _idUsuario, _nombreAlimento, _tipoAlimento, _unidadMedida, _lote, 
            _nuevoPrecio, _fechaCaducidad, _cantidad, 0, 'Disponible', 
            NOW(), _nuevoPrecio * _cantidad, NOW()
        );

        -- Registrar la entrada en el historial de movimientos
		INSERT INTO HistorialMovimientos (idAlimento, tipoMovimiento, cantidad, idUsuario, fechaMovimiento, unidadMedida)
		VALUES (LAST_INSERT_ID(), 'Entrada', _cantidad, _idUsuario, NOW(), _unidadMedida);

    END IF;

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

    -- Manejador de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        IF _debugInfo IS NULL OR _debugInfo = '' THEN
            SET _debugInfo = 'Error desconocido en la transacción';
        END IF;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _debugInfo;
    END;

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

        -- Si no se proporciona lote (NULL o vacío)
        IF _lote IS NULL OR _lote = '' THEN
            -- Buscar el lote con la fecha de caducidad más cercana
            SET _debugInfo = 'Buscando lote con fecha de caducidad más cercana...';
            SELECT idAlimento, stockActual, unidadMedida
            INTO _idAlimento, _currentStock, _unidadMedidaLote
            FROM Alimentos
            WHERE LOWER(nombreAlimento) = LOWER(_nombreAlimento)
              AND LOWER(unidadMedida) = LOWER(_unidadMedida)
              AND fechaCaducidad >= CURDATE()
            ORDER BY fechaCaducidad ASC
            LIMIT 1 FOR UPDATE;

            -- Si no se encuentra un lote con fecha de caducidad cercana, buscar el último lote registrado
            IF _idAlimento IS NULL THEN
                SET _debugInfo = 'No se encontró un lote con fecha cercana, buscando el último lote registrado...';
                SELECT idAlimento, stockActual, unidadMedida
                INTO _idAlimento, _currentStock, _unidadMedidaLote
                FROM Alimentos
                WHERE LOWER(nombreAlimento) = LOWER(_nombreAlimento)
                  AND LOWER(unidadMedida) = LOWER(_unidadMedida)
                ORDER BY fechaIngreso DESC
                LIMIT 1 FOR UPDATE;

                -- Si tampoco hay un último lote, devolver error
                IF _idAlimento IS NULL THEN
                    SET _debugInfo = 'No se encontró ningún lote registrado para este alimento.';
                    ROLLBACK;
                END IF;
            END IF;

        ELSE
            -- Si el usuario proporciona un lote
            SET _debugInfo = 'Buscando el lote proporcionado por el usuario...';
            SELECT idAlimento, stockActual, unidadMedida
            INTO _idAlimento, _currentStock, _unidadMedidaLote
            FROM Alimentos
            WHERE LOWER(nombreAlimento) = LOWER(_nombreAlimento)
              AND lote = _lote
              AND LOWER(unidadMedida) = LOWER(_unidadMedida)
            LIMIT 1 FOR UPDATE;

            -- Verificar si el lote proporcionado existe y la unidad de medida coincide
            IF _idAlimento IS NULL THEN
                SET _debugInfo = 'El lote proporcionado no existe o la unidad de medida no coincide.';
                ROLLBACK;
            END IF;
        END IF;

        -- Verificar si hay suficiente stock en el lote seleccionado
        IF _currentStock < _cantidadNecesaria THEN
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

            -- Si el stock del lote se agota, eliminar el lote
            IF _currentStock - _cantidadNecesaria = 0 THEN
                DELETE FROM Alimentos WHERE idAlimento = _idAlimento;
            END IF;

            -- Confirmar la transacción
            COMMIT;

            -- Confirmación de éxito
            SET _debugInfo = 'Transacción completada exitosamente.';
            SIGNAL SQLSTATE '01000' SET MESSAGE_TEXT = _debugInfo;
        END IF;
    END IF;

END $$
DELIMITER ;



-- Procedimiento para notificar Stock Bajo-----------------------------------------
DELIMITER $$
CREATE PROCEDURE spu_notificar_stock_bajo_alimentos()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE alimentoNombre VARCHAR(100);
    DECLARE alimentoLote VARCHAR(50);
    DECLARE alimentoStock DECIMAL(10,2);

    -- Cursor para seleccionar los alimentos con stock bajo o agotados, limitando a 5
    DECLARE cur CURSOR FOR
        SELECT nombreAlimento, lote, stockFinal 
        FROM Alimentos
        WHERE stockFinal < stockMinimo OR stockFinal = 0
        LIMIT 5;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    -- Abrir el cursor
    OPEN cur;

    -- Bucle para recorrer los resultados
    read_loop: LOOP
        FETCH cur INTO alimentoNombre, alimentoLote, alimentoStock;
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Imprimir el mensaje de notificación
        IF alimentoStock = 0 THEN
            -- Notificación de alimentos agotados
            SELECT CONCAT('Alimento agotado: ', alimentoNombre, ', Lote: ', alimentoLote, ', Stock: ', alimentoStock) AS Notificacion;
        ELSE
            -- Notificación de alimentos con stock bajo
            SELECT CONCAT('Alimento con stock bajo: ', alimentoNombre, ', Lote: ', alimentoLote, ', Stock: ', alimentoStock) AS Notificacion;
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

    -- Si el tipo de movimiento es 'Entrada', mostrar campos específicos para entradas
    IF tipoMovimiento = 'Entrada' THEN
        SELECT 
            h.idAlimento,
            a.nombreAlimento,               -- Nombre del alimento
            a.tipoAlimento,                 -- Tipo de alimento (Grano, Heno, etc.)
            a.unidadMedida,                 -- Unidad de medida del alimento
            a.lote,                         -- Lote del alimento
            a.fechaCaducidad,               -- Fecha de caducidad del lote
            a.stockActual,                  -- Stock actual para entradas
            h.fechaMovimiento               -- Fecha del movimiento
        FROM 
            HistorialMovimientos h
        JOIN 
            Alimentos a ON h.idAlimento = a.idAlimento  -- Unimos ambas tablas por idAlimento
        WHERE 
            h.tipoMovimiento = 'Entrada'  
            AND h.fechaMovimiento >= fechaInicio   -- Usar la variable de entrada
            AND h.fechaMovimiento <= fechaFin      -- Usar la variable de entrada
            AND (idUsuario = 0 OR h.idUsuario = idUsuario)  -- Usar la variable de entrada
        ORDER BY 
            h.fechaMovimiento DESC
        LIMIT 
            limite OFFSET desplazamiento;
        
    -- Si el tipo de movimiento es 'Salida', mostrar campos específicos para salidas
    ELSEIF tipoMovimiento = 'Salida' THEN
        SELECT 
            h.idAlimento,
            a.nombreAlimento,               -- Nombre del alimento
            te.tipoEquino,                  -- Tipo de equino (Yegua, Padrillo, Potranca, Potrillo)
            h.cantidad,                     -- Cantidad de salida
            h.unidadMedida,                 -- Unidad de medida
            h.merma,                        -- Merma (si aplica)
            a.lote,                         -- Lote del alimento
            h.fechaMovimiento               -- Fecha del movimiento
        FROM 
            HistorialMovimientos h
        JOIN 
            Alimentos a ON h.idAlimento = a.idAlimento  -- Unimos ambas tablas por idAlimento
        LEFT JOIN
            TipoEquinos te ON h.idTipoEquino = te.idTipoEquino  -- Unimos con la tabla TipoEquinos (para la salida)
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




