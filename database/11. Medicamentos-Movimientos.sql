-- -------------------------------------------------------------------------------------------
DELIMITER $$
CREATE PROCEDURE spu_listar_medicamentosMedi()
BEGIN
    -- Mostrar la información de todos los medicamentos registrados
    SELECT 
        m.idMedicamento,
        m.nombreMedicamento,
        m.descripcion,
        m.lote,
        p.presentacion,
        c.dosis,
        t.tipo AS nombreTipo, -- Mostrar el nombre del tipo
        m.cantidad_stock,
        m.stockMinimo,
        m.fecha_registro,
        m.fecha_caducidad,
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
    ORDER BY 
        m.nombreMedicamento ASC; -- Ordenar alfabéticamente por nombre de medicamento
END $$
DELIMITER ;


-- Procedimiento para registrar medicamentos---------------------------------------------------------------------------------------------------------
DELIMITER $$

CREATE PROCEDURE spu_medicamentos_registrar(
    IN _nombreMedicamento VARCHAR(255),
    IN _descripcion TEXT, 
    IN _lote VARCHAR(100),
    IN _presentacion VARCHAR(100),
    IN _dosis VARCHAR(50),
    IN _tipo VARCHAR(100),
    IN _cantidad_stock INT,
    IN _stockMinimo INT,
    IN _fecha_caducidad DATE,
    IN _precioUnitario DECIMAL(10,2),
    IN _idUsuario INT
)
BEGIN
    DECLARE _idTipo INT;
    DECLARE _idPresentacion INT;
    DECLARE _idCombinacion INT;
    DECLARE _exists INT DEFAULT 0;
    
    -- Manejador de errores: si ocurre un error, se revierte la transacción
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error en la transacción. Registro cancelado.';
    END;

    -- Iniciar la transacción
    START TRANSACTION;

    -- Validar si ya existe un medicamento con el mismo nombre y lote
    SELECT COUNT(*) INTO _exists
    FROM Medicamentos
    WHERE LOWER(nombreMedicamento) = LOWER(_nombreMedicamento)
      AND LOWER(lote) = LOWER(_lote);

    IF _exists > 0 THEN
        -- Si ya existe un medicamento con el mismo nombre y lote, devolver un error
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error: Ya existe un medicamento con el mismo nombre y lote.';
    END IF;

    -- Validar la combinación de tipo, presentación y dosis usando el procedimiento de validación
    CALL spu_validar_registrar_combinacion(_tipo, _presentacion, _dosis);

    -- Buscar el ID de la combinación validada
    SELECT idCombinacion INTO _idCombinacion
    FROM CombinacionesMedicamentos
    WHERE idTipo = (SELECT idTipo FROM TiposMedicamentos WHERE LOWER(tipo) = LOWER(_tipo))
      AND idPresentacion = (SELECT idPresentacion FROM PresentacionesMedicamentos WHERE LOWER(presentacion) = LOWER(_presentacion))
      AND LOWER(dosis) = LOWER(_dosis)
    LIMIT 1;

    -- Insertar el medicamento en el inventario
    INSERT INTO Medicamentos (
        nombreMedicamento, 
        descripcion, 
        lote,
        idCombinacion,
        cantidad_stock,
        stockMinimo, 
        fecha_caducidad, 
        precioUnitario, 
        estado, 
        idUsuario,
        fecha_registro
    ) 
    VALUES (
        _nombreMedicamento, 
        _descripcion, 
        _lote, 
        _idCombinacion,
        _cantidad_stock, 
        _stockMinimo, 
        _fecha_caducidad, 
        _precioUnitario, 
        'Disponible', 
        _idUsuario,
        CURDATE()  
    );

    -- Confirmar la transacción
    COMMIT;

    -- Mostrar la información del medicamento registrado con JOIN a otras tablas
    SELECT 
        m.idMedicamento,
        m.nombreMedicamento,
        m.descripcion,
        m.lote,
        p.presentacion,
        c.dosis,
        t.tipo AS nombreTipo,
        m.cantidad_stock,
        m.stockMinimo,
        m.fecha_registro,
        m.fecha_caducidad,
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
    WHERE 
        m.lote = _lote AND m.nombreMedicamento = _nombreMedicamento;

    -- Mensaje de confirmación
    SELECT 'Datos confirmados' AS mensaje;

END $$
DELIMITER ;


-- Procedimiento Entrada de Medicamentos -----------------------------------------------------------------------------------
DELIMITER $$
CREATE PROCEDURE spu_medicamentos_entrada(
    IN _idUsuario INT,                -- Usuario que realiza la operación
    IN _nombreMedicamento VARCHAR(255), -- Nombre del medicamento
    IN _lote VARCHAR(100),              -- Número de lote del medicamento
    IN _cantidad_stock INT              -- Cantidad de medicamento a ingresar
)
BEGIN
    DECLARE _exists INT DEFAULT 0;
    DECLARE _idMedicamento INT;
    DECLARE _currentStock INT;
    DECLARE _cantidadTotal INT;

    -- Iniciar transacción para asegurar la consistencia de la operación
    START TRANSACTION;

    -- Validar la cantidad
    IF _cantidad_stock <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La cantidad debe ser mayor que cero.';
    END IF;

    -- Validar el lote
    IF _lote IS NULL OR _lote = '' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El lote no puede ser vacío o nulo.';
    END IF;

    -- Verificar si ya existe un medicamento con el mismo nombre y lote
    SELECT COUNT(*), idMedicamento, cantidad_stock
    INTO _exists, _idMedicamento, _currentStock
    FROM Medicamentos
    WHERE LOWER(nombreMedicamento) = LOWER(_nombreMedicamento)
      AND LOWER(lote) = LOWER(_lote);

    -- Si el lote ya existe, actualizar la cantidad
    IF _exists > 0 THEN
        SET _cantidadTotal = _currentStock + _cantidad_stock;
        UPDATE Medicamentos
        SET cantidad_stock = _cantidadTotal, ultima_modificacion = NOW()
        WHERE idMedicamento = _idMedicamento;

    -- Si el lote no existe, crear un nuevo registro
    ELSE
        INSERT INTO Medicamentos (
            nombreMedicamento, cantidad_stock, lote, estado, idUsuario, fecha_registro
        ) 
        VALUES (
            _nombreMedicamento, _cantidad_stock, _lote, 'Disponible', _idUsuario, CURDATE()
        );
        
        -- Obtener el ID del nuevo medicamento insertado
        SET _idMedicamento = LAST_INSERT_ID();
    END IF;

    -- Actualizar el estado del medicamento según el stock actual
    IF _cantidadTotal IS NOT NULL THEN
        IF _cantidadTotal > 0 THEN
            UPDATE Medicamentos
            SET estado = 'Disponible'
            WHERE idMedicamento = _idMedicamento;
        ELSE
            UPDATE Medicamentos
            SET estado = 'Agotado'
            WHERE idMedicamento = _idMedicamento;
        END IF;
    END IF;

    -- Registrar la entrada en el historial de movimientos
    INSERT INTO HistorialMovimientosMedicamentos (idMedicamento, tipoMovimiento, cantidad, idUsuario, fechaMovimiento)
    VALUES (_idMedicamento, 'Entrada', _cantidad_stock, _idUsuario, NOW());

    -- Confirmar la transacción
    COMMIT;

END $$
DELIMITER ;



-- Procedimiento Salida de Medicamentos-----------------------------------------------------------------------------------
DELIMITER $$

CREATE PROCEDURE spu_medicamentos_salida(
    IN _idUsuario INT,               -- Usuario que realiza la operación
    IN _nombreMedicamento VARCHAR(255), -- Nombre del medicamento
    IN _cantidad DECIMAL(10,2)       -- Cantidad de medicamento a retirar
)
BEGIN
    DECLARE _idMedicamento INT;                  -- ID del medicamento (lote) que se está procesando
    DECLARE _currentStock DECIMAL(10,2);         -- Stock actual del lote que se está procesando
    DECLARE _totalStock DECIMAL(10,2);           -- Stock total disponible del medicamento
    DECLARE _stockMinimo INT;                    -- Stock mínimo del medicamento
    DECLARE _estadoActual VARCHAR(20);           -- Nuevo estado calculado
    DECLARE _cantidadOriginal DECIMAL(10,2);     -- Cantidad original solicitada
    DECLARE done INT DEFAULT 0;                  -- Control de finalización del cursor

    -- Cursor para manejar los lotes de medicamentos, ordenados por fecha de caducidad
    DECLARE curLote CURSOR FOR 
        SELECT idMedicamento, cantidad_stock, stockMinimo
        FROM Medicamentos 
        WHERE LOWER(nombreMedicamento) = LOWER(_nombreMedicamento) AND cantidad_stock > 0 
        ORDER BY fecha_caducidad ASC;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    -- Asignar la cantidad original solicitada a una variable separada
    SET _cantidadOriginal = _cantidad;

    -- Iniciar la transacción para garantizar la consistencia
    START TRANSACTION;

    -- Validar que la cantidad a retirar sea mayor que cero
    IF _cantidad <= 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error: La cantidad a retirar debe ser mayor que cero.';
    END IF;

    -- Calcular el stock total disponible del medicamento
    SELECT SUM(cantidad_stock), stockMinimo
    INTO _totalStock, _stockMinimo
    FROM Medicamentos
    WHERE LOWER(nombreMedicamento) = LOWER(_nombreMedicamento);

    -- Verificar si se encontró el medicamento
    IF _totalStock IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error: El medicamento no fue encontrado.';
    END IF;

    -- Verificar si hay suficiente stock para cubrir la cantidad solicitada
    IF _totalStock < _cantidad THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error: No hay suficiente stock disponible para esta cantidad.';
    END IF;

    -- Procesar la salida del medicamento por lotes usando un cursor
    OPEN curLote;

    salida_loop: LOOP
        FETCH curLote INTO _idMedicamento, _currentStock, _stockMinimo;

        IF done THEN
            LEAVE salida_loop;
        END IF;

        IF _cantidad <= _currentStock THEN
            -- Si la cantidad es menor o igual que el stock actual del lote, restar solo la cantidad necesaria
            UPDATE Medicamentos
            SET cantidad_stock = cantidad_stock - _cantidad
            WHERE idMedicamento = _idMedicamento;
            SET _cantidad = 0;
        ELSE
            -- Si la cantidad es mayor que el stock del lote actual, restar todo el stock del lote y continuar con el siguiente lote
            SET _cantidad = _cantidad - _currentStock;
            UPDATE Medicamentos
            SET cantidad_stock = 0
            WHERE idMedicamento = _idMedicamento;
        END IF;

        -- Calcular el stock restante de este lote y actualizar su estado individualmente
        SELECT cantidad_stock
        INTO _currentStock
        FROM Medicamentos
        WHERE idMedicamento = _idMedicamento;

        -- Actualizar el estado de cada lote individualmente
        IF _currentStock > _stockMinimo THEN
            SET _estadoActual = 'Disponible';
        ELSEIF _currentStock > 0 AND _currentStock <= _stockMinimo THEN
            SET _estadoActual = 'Por agotarse';
        ELSE
            SET _estadoActual = 'Agotado';
        END IF;

        UPDATE Medicamentos
        SET estado = _estadoActual
        WHERE idMedicamento = _idMedicamento;

    END LOOP;

    CLOSE curLote;

    -- Registrar la salida en el historial de movimientos
    INSERT INTO HistorialMovimientosMedicamentos (idMedicamento, tipoMovimiento, cantidad, idUsuario, fechaMovimiento)
    VALUES (_idMedicamento, 'Salida', _cantidadOriginal, _idUsuario, NOW());

    -- Confirmar la transacción
    COMMIT;

END $$
DELIMITER ;




-- 1. Notificación de Stock Bajo
DELIMITER $$
CREATE PROCEDURE spu_notificar_stock_bajo_medicamentos()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE medicamentoNombre VARCHAR(100);
    DECLARE medicamentoLote VARCHAR(50);
    DECLARE medicamentoStock INT;

    -- Cursor para seleccionar tanto medicamentos con stock bajo como agotados, limitando a 5
    DECLARE cur CURSOR FOR
        SELECT nombreMedicamento, lote, cantidad_stock 
        FROM Medicamentos
        WHERE cantidad_stock < stockMinimo OR cantidad_stock = 0
        LIMIT 5;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    -- Abrir cursor
    OPEN cur;

    -- Bucle para recorrer los resultados
    read_loop: LOOP
        FETCH cur INTO medicamentoNombre, medicamentoLote, medicamentoStock;
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Imprimir el mensaje de notificación
        IF medicamentoStock = 0 THEN
            -- Notificación de medicamentos agotados
            SELECT CONCAT('Medicamento agotado: ', medicamentoNombre, ', Lote: ', medicamentoLote, ', Stock: ', medicamentoStock) AS Notificacion; 
        ELSE
            -- Notificación de medicamentos con stock bajo
            SELECT CONCAT('Medicamento con stock bajo: ', medicamentoNombre, ', Lote: ', medicamentoLote, ', Stock: ', medicamentoStock) AS Notificacion;
        END IF;
    END LOOP;

    -- Cerrar cursor
    CLOSE cur;
END $$
DELIMITER ;


-- 2. Procedimiento para registrar historial de medicamentos y movimientos
DELIMITER $$
CREATE PROCEDURE spu_historial_medicamentos_movimientosMedi(
    IN _idMedicamento INT,
    IN _accion VARCHAR(50),       -- Ejemplo: 'Agregar', 'Eliminar', 'Actualizar', 'Entrada', 'Salida'
    IN _tipoMovimiento VARCHAR(50),-- Solo para movimientos, puede ser NULL
    IN _cantidad INT              -- Solo para movimientos, puede ser NULL
)
BEGIN
    DECLARE _fecha DATETIME DEFAULT NOW();

    -- Registrar acción en el historial de medicamentos
    INSERT INTO HistorialMedicamentosMedi (idMedicamento, accion, fecha)
    VALUES (_idMedicamento, _accion, _fecha);

    -- Si es un movimiento, registrar en el historial de movimientos
    IF _tipoMovimiento IS NOT NULL AND _cantidad IS NOT NULL THEN
        INSERT INTO HistorialMovimientosMedi (idMedicamento, tipoMovimiento, cantidad, fechaMovimiento)
        VALUES (_idMedicamento, _tipoMovimiento, _cantidad, _fecha);
    END IF;
END $$
DELIMITER ;


--  Mejortar los otros  ----------------------------------------------------------------------------

-- 1.Procedimiento para agregar un nuevo tipo
DELIMITER $$
CREATE PROCEDURE spu_agregar_tipo_medicamento(
    IN _tipo VARCHAR(100)
)
BEGIN
    DECLARE _exists INT DEFAULT 0;
    
    -- Verificar si el tipo ya existe
    SELECT COUNT(*) INTO _exists 
    FROM TiposMedicamentos
    WHERE LOWER(tipo) = LOWER(_tipo);
    
    IF _exists = 0 THEN
        -- Insertar nuevo tipo si no existe
        INSERT INTO TiposMedicamentos (tipo) VALUES (_tipo);
    ELSE
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El tipo ya existe.';
    END IF;
END $$
DELIMITER ;



DELIMITER $$
CREATE PROCEDURE spu_agregar_presentacion_medicamento(
    IN _presentacion VARCHAR(100)
)
BEGIN
    DECLARE _exists INT DEFAULT 0;
    
    -- Verificar si la presentación ya existe
    SELECT COUNT(*) INTO _exists 
    FROM PresentacionesMedicamentos
    WHERE LOWER(presentacion) = LOWER(_presentacion);
    
    IF _exists = 0 THEN
        -- Insertar nueva presentación si no existe
        INSERT INTO PresentacionesMedicamentos (presentacion) VALUES (_presentacion);
    ELSE
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La presentación ya existe.';
    END IF;
END $$
DELIMITER ;


-- 2. Procedimiento para validar presentación tipo y dosis:
DELIMITER $$
CREATE PROCEDURE spu_validar_registrar_combinacion(
    IN _tipoMedicamento VARCHAR(100),      -- Tipo de medicamento (ej. Antibiótico)
    IN _presentacionMedicamento VARCHAR(100), -- Presentación del medicamento (ej. Inyectable)
    IN _dosisMedicamento VARCHAR(50)       -- Dosis del medicamento (ej. 500 mg)
)
BEGIN
    DECLARE _idTipo INT;
    DECLARE _idPresentacion INT;
    DECLARE _idCombinacion INT;
    DECLARE _validDosisFormat VARCHAR(50);   -- Para verificar si la dosis es coherente con la combinación
    DECLARE _errorMensaje VARCHAR(255);      -- Variable para construir mensajes de error paso a paso

    -- Manejador de errores: Si ocurre un error, se revierte la transacción
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        -- Mostrar el mensaje específico construido
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMensaje;
    END;

    -- Iniciar la transacción
    START TRANSACTION;

    -- VALIDACIÓN 1: Verificar si el tipo de medicamento existe
    SELECT idTipo INTO _idTipo
    FROM TiposMedicamentos
    WHERE LOWER(tipo) = LOWER(_tipoMedicamento)
    LIMIT 1;

    IF _idTipo IS NULL THEN
        SET _errorMensaje = CONCAT('Error: El tipo de medicamento "', _tipoMedicamento, '" no es válido o no existe.');
        -- Salir del procedimiento con el error
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMensaje;
    END IF;

    -- VALIDACIÓN 2: Verificar si la presentación de medicamento existe
    SELECT idPresentacion INTO _idPresentacion
    FROM PresentacionesMedicamentos
    WHERE LOWER(presentacion) = LOWER(_presentacionMedicamento)
    LIMIT 1;

    IF _idPresentacion IS NULL THEN
        SET _errorMensaje = CONCAT('Error: La presentación "', _presentacionMedicamento, '" no es válida o no existe.');
        -- Salir del procedimiento con el error
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMensaje;
    END IF;

    -- VALIDACIÓN 3: Verificar si ya existe la combinación de tipo, presentación y dosis
    SELECT idCombinacion INTO _idCombinacion
    FROM CombinacionesMedicamentos
    WHERE idTipo = _idTipo
      AND idPresentacion = _idPresentacion
      AND LOWER(dosis) = LOWER(_dosisMedicamento)
    LIMIT 1;

    IF _idCombinacion IS NOT NULL THEN
        COMMIT;
        SELECT 'Combinación válida, reutilizada' AS mensaje, _idCombinacion AS idCombinacion;
    ELSE
        -- VALIDACIÓN 4: Verificar combinaciones previas y coherencia de la unidad (no la cantidad)
        SELECT dosis INTO _validDosisFormat
        FROM CombinacionesMedicamentos
        WHERE idTipo = _idTipo
          AND idPresentacion = _idPresentacion
        LIMIT 1;

        IF _validDosisFormat IS NOT NULL THEN
            -- Validar que la unidad de la nueva dosis sea coherente con la combinación existente (ignoramos la cantidad)
            IF NOT (LOWER(_dosisMedicamento) REGEXP LOWER(REPLACE(_validDosisFormat, '[0-9]+', ''))) THEN
                SET _errorMensaje = CONCAT('Error: La unidad de dosis "', _dosisMedicamento, '" no es coherente con la combinación de tipo "', _tipoMedicamento, '" y presentación "', _presentacionMedicamento, '".');
                ROLLBACK;
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMensaje;
            END IF;
        END IF;

        -- Insertar la nueva combinación si es válida y no existe previamente
        INSERT INTO CombinacionesMedicamentos (idTipo, idPresentacion, dosis)
        VALUES (_idTipo, _idPresentacion, _dosisMedicamento);

        SET _idCombinacion = LAST_INSERT_ID();  -- Obtenemos el ID de la nueva combinación

        -- Confirmar la transacción
        COMMIT;

        -- Devolver el ID de la nueva combinación registrada
        SELECT 'Nueva combinación registrada y válida' AS mensaje, _idCombinacion AS idCombinacion;
    END IF;
END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE spu_listar_tipos_presentaciones_dosis()
BEGIN
    -- Selecciona los tipos de medicamentos junto con la presentación y la dosis, agrupados
    SELECT 
        t.tipo, 
        GROUP_CONCAT(DISTINCT p.presentacion ORDER BY p.presentacion ASC SEPARATOR ', ') AS presentaciones,
        GROUP_CONCAT(DISTINCT c.dosis ORDER BY c.dosis ASC SEPARATOR ', ') AS dosis
    FROM 
        CombinacionesMedicamentos c
    JOIN 
        TiposMedicamentos t ON c.idTipo = t.idTipo
    JOIN 
        PresentacionesMedicamentos p ON c.idPresentacion = p.idPresentacion
    GROUP BY 
        t.tipo
    ORDER BY 
        t.tipo ASC;  -- Ordena por tipo de medicamento
END $$
DELIMITER ;


-- ----
DELIMITER $$
CREATE PROCEDURE spu_listar_presentaciones_medicamentos()
BEGIN
    -- Selecciona todas las presentaciones de medicamentos
    SELECT 
        idPresentacion, 
        presentacion 
    FROM 
        PresentacionesMedicamentos
    ORDER BY 
        presentacion ASC;  -- Ordena por el nombre de la presentación
END $$
DELIMITER ;

-- ---------------------
DELIMITER $$
CREATE PROCEDURE spu_listar_lotes()
BEGIN
    -- Seleccionar todos los lotes registrados en la tabla Medicamentos
    SELECT lote, nombreMedicamento, descripcion, cantidad_stock, stockMinimo, fecha_caducidad, estado
    FROM Medicamentos;
END $$
DELIMITER ;


-- insert importante --------------------------------------------------------------------

-- Insertar Tipos de Medicamentos
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


-- Insertar Presentaciones de Medicamentos
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


-- Insertar Combinaciones de Medicamentos
INSERT INTO CombinacionesMedicamentos (idTipo, idPresentacion, dosis) VALUES
(1, 1, '500 mg'),  -- Antibiótico, tabletas
(2, 2, '10 ml'),   -- Analgésico, jarabes
(3, 3, '200 mg'),  -- Antiinflamatorio, cápsulas
(1, 4, '1 g'),     -- Antibiótico, inyectable
(2, 1, '50 mg'),   -- Analgésico, tabletas
(3, 5, '5 mg/ml'), -- Antiinflamatorio, suspensión
(4, 6, '300 mg'),  -- Gastroprotector, grageas
(5, 7, '100 g'),   -- Desparasitante, pomadas
(6, 8, '1 ml'),    -- Suplemento, ampollas
(7, 9, '0.5 ml'),  -- Broncodilatador, colirios
(8, 10, '20 mg/ml'),-- Antifúngico, gotas nasales
(9, 11, '5 mg'),   -- Sedante, píldoras
(10, 12, '1 g'),   -- Vacuna, comprimidos
(5, 13, '50 mg'),  -- Desparasitante, enemas
(3, 14, '15 mg/ml'),-- Antiinflamatorio, goteros
(1, 15, '250 mg'),  -- Antibiótico, polvos medicinales
(3, 16, '100 mcg'), -- Antiinflamatorio, aerosoles
(6, 1, '10 fL'),   -- Suplemento, tabletas
(11, 4, '200 g/dL'),-- Antiparasitario, suspensión
(1, 4, '5 g/L'),   -- Antibiótico, inyectable
(2, 7, '50 mcg'),  -- Analgésico, pomadas
(3, 2, '1 mcg/dL'),-- Antiinflamatorio, jarabes
(5, 1, '10 mL'),   -- Desparasitante, tabletas
(8, 14, '200 mcl'),-- Antifúngico, spray
(12, 4, '300 mcmol/L'),-- Vitaminas, inyectable
(9, 1, '15 mEq'),  -- Sedante, tabletas
(3, 10, '5 mEq/L'),-- Antiinflamatorio, gotas nasales
(1, 6, '100 mg'),  -- Antibiótico, grageas
(2, 5, '50 mg/dL'),-- Analgésico, suspensión
(5, 15, '5 mm'),   -- Desparasitante, polvos
(3, 1, '10 mm Hg'),-- Antiinflamatorio, tabletas
(6, 8, '1 mmol'),  -- Suplemento, ampollas
(11, 2, '250 mmol/L'),-- Antiparasitario, jarabes
(1, 4, '5 mOsm/kg'),-- Antibiótico, inyectable
(3, 14, '20 mUI/L'),-- Antiinflamatorio, goteros
(12, 11, '50 mU/g'),-- Vitaminas, comprimidos
(8, 16, '100 mU/L'),-- Antifúngico, spray
(2, 10, '10 ng/dL'),-- Analgésico, gotas nasales
(5, 1, '5 ng/L'),  -- Desparasitante, tabletas
(6, 5, '200 ng/mL'),-- Suplemento, suspensión
(1, 2, '1 ng/mL/h'),-- Antibiótico, píldoras
(3, 4, '5 nmol'),  -- Antiinflamatorio, inyectable
(12, 1, '10 nmol/L'),-- Vitaminas, tabletas
(8, 15, '20 pg'),  -- Antifúngico, polvos
(3, 10, '5 pg/mL'), -- Antiinflamatorio, gotas nasales
(1, 4, '1 pmol/L'), -- Antibiótico, inyectable
(2, 1, '100 UI/L'), -- Analgésico, tabletas
(5, 5, '50 UI/mL'), -- Desparasitante, suspensión
(6, 8, '10 U/L'),  -- Suplemento, ampollas
(3, 15, '200 U/mL');-- Antiinflamatorio, polvos