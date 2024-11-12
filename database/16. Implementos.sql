DROP PROCEDURE IF EXISTS `spu_registrar_implemento`;
DELIMITER //
CREATE PROCEDURE spu_registrar_implemento(
    IN p_idTipoinventario INT,
    IN p_nombreProducto VARCHAR(100),
    IN p_descripcion TEXT,
    IN p_precioUnitario DECIMAL(10,2),
    IN p_cantidad INT
)
BEGIN
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
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_movimiento_implemento`;
DELIMITER //
CREATE PROCEDURE spu_movimiento_implemento(
    IN p_idTipomovimiento INT,  -- ID del tipo de movimiento (1 para Entrada, 2 para Salida)
    IN p_idTipoinventario INT,  -- Tipo de inventario (Ejemplo: 1 para implementos de caballo)
    IN p_idInventario INT,  -- Nombre del producto
    IN p_cantidad INT,  -- Cantidad de la entrada o salida
    IN p_precioUnitario DECIMAL(10,2),  -- Precio unitario del producto (solo en entrada)
    IN p_descripcion TEXT  -- Descripción del movimiento (puede ser NULL)
)
BEGIN
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

END //
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_cambiar_estado_implemento`;
DELIMITER //
CREATE PROCEDURE spu_cambiar_estado_implemento(
    IN p_idInventario INT,
    IN p_nuevoEstado BIT
)
BEGIN
    -- Cambiar el estado del implemento
    UPDATE Implementos
    SET estado = p_nuevoEstado
    WHERE idInventario = p_idInventario;

    -- Verificar si la actualización fue exitosa
    IF ROW_COUNT() = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Implemento no encontrado.';
    END IF;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_listar_implementos_por_tipo`;
DELIMITER //
CREATE PROCEDURE spu_listar_implementos_por_tipo(
    IN p_idTipoinventario INT
)
BEGIN
    -- Listar implementos según el tipo de inventario, con estado 0 al final
    SELECT *
    FROM Implementos
    WHERE idTipoinventario = p_idTipoinventario
    ORDER BY estado DESC;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_listar_implementos_con_cantidad`;
DELIMITER $$
CREATE PROCEDURE spu_listar_implementos_con_cantidad(IN p_idTipoinventario INT)
BEGIN
    -- Consulta para obtener la lista de implementos filtrados por idTipoinventario
    SELECT 
        idInventario,
        nombreProducto,
        stockFinal
    FROM 
        Implementos
    WHERE
        idTipoinventario = p_idTipoinventario  -- Filtra por idTipoinventario
    ORDER BY 
        nombreProducto;  -- Ordena por nombre, puedes cambiar el orden si lo necesitas
END $$
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_listar_tipo_movimiento`;
DELIMITER //
CREATE PROCEDURE `spu_listar_tipo_movimiento`()
BEGIN
    SELECT * FROM tipomovimientos;
END //
DELIMITER ;