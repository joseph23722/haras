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

    -- Iniciar la transacción
    START TRANSACTION;

    -- Buscar el idTipo en la tabla TiposMedicamentos
    SELECT idTipo INTO _idTipo
    FROM TiposMedicamentos
    WHERE LOWER(tipo) = LOWER(_tipo)
    LIMIT 1;

    -- Si el tipo de medicamento no existe, devolver un error
    IF _idTipo IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: El tipo de medicamento no es válido.';
    END IF;

    -- Buscar el idPresentacion en la tabla PresentacionesMedicamentos
    SELECT idPresentacion INTO _idPresentacion
    FROM PresentacionesMedicamentos
    WHERE LOWER(presentacion) = LOWER(_presentacion)
    LIMIT 1;

    -- Si la presentación no existe, devolver un error
    IF _idPresentacion IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: La presentación no es válida.';
    END IF;

    -- Verificar si la combinación ya existe en CombinacionesMedicamentos
    SELECT idCombinacion INTO _idCombinacion
    FROM CombinacionesMedicamentos
    WHERE idTipo = _idTipo
      AND idPresentacion = _idPresentacion
      AND LOWER(dosis) = LOWER(_dosis)
    LIMIT 1;

    -- Si la combinación no existe, insertarla
    IF _idCombinacion IS NULL THEN
        INSERT INTO CombinacionesMedicamentos (idTipo, idPresentacion, dosis)
        VALUES (_idTipo, _idPresentacion, _dosis);
        SET _idCombinacion = LAST_INSERT_ID();
    END IF;

    -- Verificar si el medicamento ya existe en ese lote
    SELECT COUNT(*) INTO _exists
    FROM Medicamentos
    WHERE LOWER(nombreMedicamento) = LOWER(_nombreMedicamento) 
      AND LOWER(lote) = LOWER(_lote)
    LIMIT 1;

    IF _exists > 0 THEN
        -- Si el medicamento ya existe, actualizar el stock del lote
        UPDATE Medicamentos
        SET cantidad_stock = cantidad_stock + _cantidad_stock,
            ultima_modificacion = NOW()
        WHERE LOWER(nombreMedicamento) = LOWER(_nombreMedicamento)
          AND LOWER(lote) = LOWER(_lote);
    ELSE
        -- Si el medicamento no existe, insertarlo en el inventario
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
    END IF;

    -- Confirmar la transacción
	COMMIT;

	-- Verificación de la transacción
	SELECT 'Datos confirmados' AS mensaje;


    -- Mostrar la información del medicamento registrado
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
    WHERE 
        m.lote = _lote AND m.nombreMedicamento = _nombreMedicamento;

END $$
DELIMITER ;



-- Procedimiento Entrada de Medicamentos -----------------------------------------------------------------------------------
DELIMITER $$
CREATE PROCEDURE spu_medicamentos_entrada(
    IN _idUsuario INT,              -- Usuario que realiza la operación
    IN _nombreMedicamento VARCHAR(255), -- Nombre del medicamento
    IN _lote VARCHAR(100),            -- Número de lote del medicamento
    IN _presentacion VARCHAR(100),     -- Presentación del medicamento
    IN _dosis VARCHAR(50),             -- Dosis del medicamento
    IN _tipo VARCHAR(100),             -- Tipo del medicamento
    IN _cantidad_stock INT,            -- Cantidad de medicamento a ingresar
    IN _stockMinimo INT,               -- Stock mínimo
    IN _fecha_caducidad DATE,          -- Fecha de caducidad del lote
    IN _precioUnitario DECIMAL(10,2)   -- Nuevo precio unitario (opcional)
)
BEGIN
    DECLARE _exists INT DEFAULT 0;
    DECLARE _idMedicamento INT;
    DECLARE _idTipo INT;
    DECLARE _idPresentacion INT;
    DECLARE _idCombinacion INT;
    DECLARE _currentStock DECIMAL(10,2);
    DECLARE _currentPrice DECIMAL(10,2);
    DECLARE _currentFechaCaducidad DATE;
    DECLARE _nuevoPrecioPonderado DECIMAL(10,2); -- Precio ponderado si el precio es diferente
    DECLARE _cantidadTotal DECIMAL(10,2);        -- Nueva cantidad total para el lote
    DECLARE _estadoActual VARCHAR(20);
    DECLARE _existeCombinacion INT DEFAULT 0;

    -- Iniciar transacción para asegurar la consistencia de la operación
    START TRANSACTION;

    -- Validar la cantidad
    IF _cantidad_stock <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La cantidad debe ser mayor que cero.';
    END IF;

    -- Validar el precio unitario
    IF _precioUnitario IS NOT NULL AND (_precioUnitario < 0 OR _precioUnitario > 999999.99) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El precio no es válido.';
    END IF;

    -- Validar el lote
    IF _lote IS NULL OR _lote = '' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El lote no puede ser vacío o nulo.';
    END IF;

    -- Validación de tipo de medicamento
    SELECT idTipo INTO _idTipo
    FROM TiposMedicamentos
    WHERE LOWER(tipo) = LOWER(_tipo)
    LIMIT 1;

    IF _idTipo IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: El tipo de medicamento no es válido.';
    END IF;

    -- Validación de presentación de medicamento
    SELECT idPresentacion INTO _idPresentacion
    FROM PresentacionesMedicamentos
    WHERE LOWER(presentacion) = LOWER(_presentacion)
    LIMIT 1;

    IF _idPresentacion IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: La presentación no es válida.';
    END IF;

    -- Validación de combinación de tipo, presentación y dosis
    SELECT idCombinacion INTO _idCombinacion
    FROM CombinacionesMedicamentos
    WHERE idTipo = _idTipo
      AND idPresentacion = _idPresentacion
      AND LOWER(dosis) = LOWER(_dosis)
    LIMIT 1;

    IF _idCombinacion IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: La combinación de presentación, dosis y tipo no es válida para este medicamento.';
    END IF;

    -- Verificar si ya existe un lote con el mismo nombre de medicamento, número de lote y combinación válida
    SELECT COUNT(*), idMedicamento, cantidad_stock, precioUnitario, fecha_caducidad
    INTO _exists, _idMedicamento, _currentStock, _currentPrice, _currentFechaCaducidad
    FROM Medicamentos
    WHERE LOWER(nombreMedicamento) = LOWER(_nombreMedicamento)
      AND LOWER(lote) = LOWER(_lote)
      AND idCombinacion = _idCombinacion;

    -- Caso 1: Si el lote ya existe y la fecha de caducidad es la misma (o NULL en ambos), actualizar el stock
    IF _exists > 0 AND (_currentFechaCaducidad = _fecha_caducidad OR (_currentFechaCaducidad IS NULL AND _fecha_caducidad IS NULL)) THEN
        SET _cantidadTotal = _currentStock + _cantidad_stock;
        UPDATE Medicamentos
        SET cantidad_stock = _cantidadTotal, ultima_modificacion = NOW()
        WHERE idMedicamento = _idMedicamento;

        -- Calcular el precio ponderado si el precio nuevo es diferente
        IF _precioUnitario IS NOT NULL AND _precioUnitario != _currentPrice THEN
            SET _nuevoPrecioPonderado = (
                (_currentPrice * _currentStock) + (_precioUnitario * _cantidad_stock)
            ) / _cantidadTotal;

            -- Actualizar el nuevo precio ponderado
            UPDATE Medicamentos
            SET precioUnitario = _nuevoPrecioPonderado
            WHERE idMedicamento = _idMedicamento;
        END IF;

    -- Caso 2: Si el lote ya existe pero la fecha de caducidad es diferente, crear un nuevo lote
    ELSEIF _exists > 0 AND _currentFechaCaducidad != _fecha_caducidad THEN
        INSERT INTO Medicamentos (
            idUsuario, nombreMedicamento, idCombinacion, cantidad_stock, lote, fecha_caducidad, 
            precioUnitario, stockMinimo, estado, fecha_registro
        ) 
        VALUES (
            _idUsuario, _nombreMedicamento, _idCombinacion, _cantidad_stock, _lote, _fecha_caducidad, 
            _precioUnitario, _stockMinimo, 'Disponible', CURDATE()
        );

    -- Caso 3: Si el lote no existe, crear uno nuevo
    ELSE
        INSERT INTO Medicamentos (
            idUsuario, nombreMedicamento, idCombinacion, cantidad_stock, lote, fecha_caducidad, 
            precioUnitario, stockMinimo, estado, fecha_registro
        ) 
        VALUES (
            _idUsuario, _nombreMedicamento, _idCombinacion, _cantidad_stock, _lote, _fecha_caducidad, 
            _precioUnitario, _stockMinimo, 'Disponible', CURDATE()
        );
    END IF;

    -- Actualizar el estado del medicamento según el stock actual
    IF _cantidadTotal > _stockMinimo THEN
        SET _estadoActual = 'Disponible';
    ELSEIF _cantidadTotal > 0 AND _cantidadTotal <= _stockMinimo THEN
        SET _estadoActual = 'Por agotarse';
    ELSE
        SET _estadoActual = 'Agotado';
    END IF;

    -- Actualizar el estado del medicamento
    UPDATE Medicamentos
    SET estado = _estadoActual
    WHERE idMedicamento = _idMedicamento;

    -- Registrar la entrada en el historial de movimientos
    INSERT INTO HistorialMovimientosMedicamentos (idMedicamento, tipoMovimiento, cantidad, idUsuario, fechaMovimiento)
    VALUES (IFNULL(_idMedicamento, LAST_INSERT_ID()), 'Entrada', _cantidad_stock, _idUsuario, NOW());

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
    DECLARE _cantidadNecesaria DECIMAL(10,2);    -- Cantidad total necesaria a retirar
    DECLARE _idMedicamento INT;                  -- ID del medicamento (lote) que se está procesando
    DECLARE _currentStock DECIMAL(10,2);         -- Stock actual del lote que se está procesando
    DECLARE _lote VARCHAR(50);                   -- Lote del medicamento que se está procesando
    DECLARE _totalStock DECIMAL(10,2);           -- Stock total disponible del medicamento
    DECLARE _stockMinimo INT;                    -- Stock mínimo del medicamento
    DECLARE _estadoActual VARCHAR(20);           -- Nuevo estado calculado
    DECLARE _cantidadRestante DECIMAL(10,2);     -- Cantidad restante de medicamento tras la salida
    DECLARE _fecha_caducidad DATE;               -- Fecha de caducidad del lote
    DECLARE _cantidadOriginal DECIMAL(10,2);     -- Cantidad original solicitada
    DECLARE done INT DEFAULT 0;                  -- Control de finalización del cursor

    -- Cursor para manejar los lotes de medicamentos, ahora ordenados por fecha de caducidad ascendente y luego por ID descendente
    DECLARE curLote CURSOR FOR 
        SELECT idMedicamento, lote, cantidad_stock, fecha_caducidad 
        FROM Medicamentos 
        WHERE LOWER(nombreMedicamento) = LOWER(_nombreMedicamento) AND cantidad_stock > 0 
        ORDER BY fecha_caducidad ASC, idMedicamento DESC       -- Prioriza lotes más próximos a vencer y luego lotes más recientes
        FOR UPDATE;  -- Bloquear filas seleccionadas para actualizar

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
    SELECT SUM(cantidad_stock), idMedicamento, stockMinimo
    INTO _totalStock, _idMedicamento, _stockMinimo
    FROM Medicamentos
    WHERE LOWER(nombreMedicamento) = LOWER(_nombreMedicamento);

    -- Verificar si se encontró el medicamento
    IF _idMedicamento IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error: El medicamento no fue encontrado.';
    END IF;

    -- Verificar si hay suficiente stock para cubrir la cantidad solicitada
    IF _totalStock < _cantidad THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error: No hay suficiente stock disponible para esta cantidad.';
    END IF;

    -- Procesar la salida del medicamento por lotes usando un cursor (priorizando lotes más próximos a caducar)
    OPEN curLote;

    salida_loop: LOOP
        FETCH curLote INTO _idMedicamento, _lote, _currentStock, _fecha_caducidad;

        IF done THEN
            LEAVE salida_loop;
        END IF;

        IF _cantidad <= _currentStock THEN
            -- Si la cantidad es menor o igual que el stock actual del lote, restar solo la cantidad necesaria
            UPDATE Medicamentos
            SET cantidad_stock = cantidad_stock - _cantidad
            WHERE idMedicamento = _idMedicamento;
            SET _cantidad = 0;
            LEAVE salida_loop;  -- Salir del bucle una vez que la cantidad se ha cubierto
        ELSE
            -- Si la cantidad es mayor que el stock del lote actual, restar todo el stock del lote y continuar con el siguiente lote
            SET _cantidad = _cantidad - _currentStock;
            UPDATE Medicamentos
            SET cantidad_stock = 0
            WHERE idMedicamento = _idMedicamento;
        END IF;

    END LOOP;

    CLOSE curLote;

    -- Calcular el stock restante del medicamento
    SELECT SUM(cantidad_stock)
    INTO _cantidadRestante
    FROM Medicamentos
    WHERE LOWER(nombreMedicamento) = LOWER(_nombreMedicamento);

    -- Verificar el estado del medicamento después de la salida
    IF _cantidadRestante > _stockMinimo THEN
        SET _estadoActual = 'Disponible';
    ELSEIF _cantidadRestante > 0 AND _cantidadRestante <= _stockMinimo THEN
        SET _estadoActual = 'Por agotarse';
    ELSE
        SET _estadoActual = 'Agotado';
    END IF;

    -- Actualizar el estado en la tabla de medicamentos
    UPDATE Medicamentos
    SET estado = _estadoActual
    WHERE LOWER(nombreMedicamento) = LOWER(_nombreMedicamento);

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

    DECLARE cur CURSOR FOR
        SELECT nombreMedicamento, lote, cantidad_stock 
        FROM Medicamentos
        WHERE cantidad_stock < stockMinimo;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN cur;

    read_loop: LOOP
        FETCH cur INTO medicamentoNombre, medicamentoLote, medicamentoStock;
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Aquí puedes agregar el código para enviar una notificación
        -- Ejemplo: CALL sp_send_notification(medicamentoNombre, medicamentoLote, medicamentoStock);
    END LOOP;

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


-- 2. Procedimiento para validar presentación y dosis:
DELIMITER $$
CREATE PROCEDURE spu_validar_presentacion_dosis(
    IN _nombreMedicamento VARCHAR(255),
    IN _presentacion VARCHAR(100),
    IN _dosis VARCHAR(50),
    IN _tipo VARCHAR(100)
)
BEGIN
    -- Validaciones específicas por tipo de medicamento
    IF _tipo = 'Antibiótico' AND _presentacion != 'Inyectable' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Los antibióticos deben ser inyectables.';
    END IF;

    IF _tipo = 'Gastroprotector' AND _dosis NOT LIKE '%mg%' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La dosis para gastroprotectores debe estar en mg.';
    END IF;

    -- Si todo es válido, proceder
END $$
DELIMITER ;


-- 3. Procedimiento para auditoría:
DELIMITER $$
CREATE PROCEDURE spu_registrar_actividad(
    IN _idUsuario INT,
    IN _accion VARCHAR(50),
    IN _detalles TEXT
)
BEGIN
    INSERT INTO AuditoriaActividades (idUsuario, accion, detalles, fecha)
    VALUES (_idUsuario, _accion, _detalles, NOW());
END $$
DELIMITER ;


-- 4. Procedimiento para bloqueo de modificación:
DELIMITER $$
CREATE PROCEDURE spu_bloquear_campos_criticos(
    IN _idMedicamento INT
)
BEGIN
    -- Bloquear modificaciones a los campos críticos
    IF EXISTS (SELECT 1 FROM Medicamentos WHERE idMedicamento = _idMedicamento AND bloqueado = 1) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se pueden modificar campos críticos.';
    END IF;
END $$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE spu_sugerir_combinaciones(
    IN _nombreMedicamento VARCHAR(255)
)
BEGIN
    -- Mostrar todas las combinaciones válidas para el medicamento ingresado
    SELECT presentacion, dosis, tipoMedicamento
    FROM CombinacionesValidas
    WHERE LOWER(nombreMedicamento) = LOWER(_nombreMedicamento);
END $$
DELIMITER ;



DELIMITER $$
CREATE PROCEDURE spu_validar_combinacion(
    IN _nombreMedicamento VARCHAR(255),
    IN _presentacion VARCHAR(100),
    IN _dosis VARCHAR(50),
    IN _tipoMedicamento VARCHAR(100)
)
BEGIN
    DECLARE _existeCombinacion INT DEFAULT 0;

    -- Verificar si la combinación es válida
    SELECT COUNT(*) INTO _existeCombinacion
    FROM CombinacionesValidas
    WHERE LOWER(nombreMedicamento) = LOWER(_nombreMedicamento)
    AND LOWER(presentacion) = LOWER(_presentacion)
    AND LOWER(dosis) = LOWER(_dosis)
    AND LOWER(tipoMedicamento) = LOWER(_tipoMedicamento);

    -- Si no existe, devolver un error
    IF _existeCombinacion = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: La combinación de presentación, dosis y tipo de medicamento no es válida.';
    END IF;
END $$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE spu_listar_tipos_medicamentos()
BEGIN
    -- Selecciona todos los tipos de medicamentos
    SELECT idTipo, tipo 
    FROM TiposMedicamentos
    ORDER BY tipo ASC;  -- Ordena alfabéticamente los tipos de medicamentos
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



