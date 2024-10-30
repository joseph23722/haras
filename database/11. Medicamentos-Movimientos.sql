-- -------------------------------------------------------------------------------------------
DELIMITER $$
CREATE PROCEDURE spu_listar_medicamentosMedi()
BEGIN
    -- Mostrar la información detallada de todos los medicamentos registrados
    SELECT 
        m.idMedicamento,
        m.nombreMedicamento,
        m.descripcion,
        lm.lote,                         -- Lote del medicamento (desde LotesMedicamento)
        p.presentacion,
        c.dosis,
        t.tipo AS nombreTipo,            -- Mostrar el nombre del tipo de medicamento
        m.cantidad_stock,
        m.stockMinimo,
        lm.fechaIngreso,                 -- Fecha de ingreso del lote
        lm.fechaCaducidad,               -- Fecha de caducidad del lote
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
    JOIN
        LotesMedicamento lm ON m.idLoteMedicamento = lm.idLoteMedicamento -- Relación con LotesMedicamento
    ORDER BY 
        m.nombreMedicamento ASC; -- Ordenar alfabéticamente por nombre de medicamento
END $$
DELIMITER ;

SELECT 
        m.idMedicamento,
        m.nombreMedicamento,
        m.descripcion,
        lm.lote,                         -- Lote del medicamento (desde LotesMedicamento)
        p.presentacion,
        c.dosis,
        t.tipo AS nombreTipo,            -- Mostrar el nombre del tipo de medicamento
        m.cantidad_stock,
        m.stockMinimo,
        lm.fechaIngreso,                 -- Fecha de ingreso del lote
        lm.fechaCaducidad,               -- Fecha de caducidad del lote
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
    JOIN
        LotesMedicamento lm ON m.idLoteMedicamento = lm.idLoteMedicamento -- Relación con LotesMedicamento
    ORDER BY 
        m.nombreMedicamento ASC; -- Ordenar alfabéticamente por nombre de medicamento

select * from lotesmedicamento;

-- Procedimiento para registrar medicamentos---------------------------------------------------------------------------------------------------------
-- Procedimiento para registrar medicamentos con verificación de lote en LotesMedicamento
DROP PROCEDURE IF EXISTS `spu_medicamentos_registrar`;
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
    IN _fechaCaducidad DATE,
    IN _precioUnitario DECIMAL(10,2),
    IN _idUsuario INT
)
BEGIN
    DECLARE _idCombinacion INT;
    DECLARE _idLoteMedicamento INT;

    -- Manejador de errores: si ocurre un error, se revierte la transacción
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    -- Iniciar la transacción
    START TRANSACTION;

    -- Verificar si el lote ya está registrado en la tabla LotesMedicamento
    SELECT idLoteMedicamento INTO _idLoteMedicamento 
    FROM LotesMedicamento
    WHERE lote = _lote;

    -- Si el lote no existe, insertarlo en la tabla LotesMedicamento
    IF _idLoteMedicamento IS NULL THEN
        INSERT INTO LotesMedicamento (lote, fechaCaducidad, fechaIngreso) 
        VALUES (_lote, _fechaCaducidad, NOW());
        SET _idLoteMedicamento = LAST_INSERT_ID();
    END IF;

    -- Validar la combinación de tipo, presentación y dosis usando el procedimiento de validación
    CALL spu_validar_registrar_combinacion(_tipo, _presentacion, _dosis);

    -- Obtener el ID de la combinación de tipo, presentación y dosis, ignorando el número en la dosis
    SELECT idCombinacion INTO _idCombinacion
    FROM CombinacionesMedicamentos
    WHERE idTipo = (SELECT idTipo FROM TiposMedicamentos WHERE LOWER(tipo) = LOWER(_tipo))
      AND idPresentacion = (SELECT idPresentacion FROM PresentacionesMedicamentos WHERE LOWER(presentacion) = LOWER(_presentacion))
      AND LOWER(SUBSTRING_INDEX(dosis, ' ', -1)) = LOWER(SUBSTRING_INDEX(_dosis, ' ', -1))
    LIMIT 1;

    -- Insertar el medicamento en la tabla Medicamentos
    INSERT INTO Medicamentos (
        nombreMedicamento, 
        descripcion, 
        idLoteMedicamento, 
        idCombinacion,
        cantidad_stock,
        stockMinimo, 
        fecha_registro,
        precioUnitario, 
        estado, 
        idUsuario
    ) 
    VALUES (
        _nombreMedicamento, 
        _descripcion, 
        _idLoteMedicamento, 
        _idCombinacion,
        _cantidad_stock, 
        _stockMinimo, 
        CURDATE(), 
        _precioUnitario, 
        'Disponible', 
        _idUsuario
    );

    -- Confirmar la transacción
    COMMIT;
END $$

DELIMITER ;


-- Procedimiento Entrada de Medicamentos -----------------------------------------------------------------------------------
-- Procedimiento Entrada de Medicamentos -----------------------------------------------------------------------------------
DELIMITER $$
CREATE PROCEDURE spu_medicamentos_entrada(
    IN _idUsuario INT,                      -- Usuario que realiza la operación
    IN _nombreMedicamento VARCHAR(255),     -- Nombre del medicamento
    IN _lote VARCHAR(100),                  -- Número de lote del medicamento
    IN _cantidad INT                        -- Cantidad de medicamento a ingresar
)
BEGIN
    DECLARE _idMedicamento INT;
    DECLARE _idLoteMedicamento INT;
    DECLARE _currentStock INT;
    DECLARE _debugInfo VARCHAR(255) DEFAULT '';

    -- Iniciar transacción para asegurar la consistencia de la operación
    START TRANSACTION;

    -- Verificar si el lote existe en LotesMedicamento y obtener su ID
    SELECT idLoteMedicamento INTO _idLoteMedicamento
    FROM LotesMedicamento
    WHERE lote = _lote;

    -- Si el lote no existe, generar un error
    IF _idLoteMedicamento IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El lote especificado no existe en LotesMedicamento.';
    END IF;

    -- Buscar el `idMedicamento` correspondiente al nombre y lote
    SELECT idMedicamento, cantidad_stock INTO _idMedicamento, _currentStock
    FROM Medicamentos
    WHERE LOWER(nombreMedicamento) = LOWER(_nombreMedicamento)
      AND idLoteMedicamento = _idLoteMedicamento
    LIMIT 1 FOR UPDATE;

    -- Si el medicamento no existe para el lote, generar un error
    IF _idMedicamento IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El medicamento con este lote no está registrado.';
    END IF;

    -- Actualizar el `cantidad_stock` sumando la cantidad
    UPDATE Medicamentos
    SET cantidad_stock = cantidad_stock + _cantidad,
        ultima_modificacion = NOW()
    WHERE idMedicamento = _idMedicamento;

    -- Registrar la entrada en el historial de movimientos de medicamentos
    INSERT INTO HistorialMovimientosMedicamentos (idMedicamento, tipoMovimiento, cantidad, idUsuario, fechaMovimiento)
    VALUES (_idMedicamento, 'Entrada', _cantidad, _idUsuario, NOW());

    -- Confirmar la transacción
    COMMIT;

    -- Confirmación de éxito
    SET _debugInfo = 'Transacción completada exitosamente.';
    SIGNAL SQLSTATE '01000' SET MESSAGE_TEXT = _debugInfo;

END $$
DELIMITER ;



-- Procedimiento Salida de Medicamentos-----------------------------------------------------------------------------------
DELIMITER $$
CREATE PROCEDURE spu_medicamentos_salida(
    IN _idUsuario INT,                    -- Usuario que realiza la operación
    IN _nombreMedicamento VARCHAR(255),    -- Nombre del medicamento
    IN _cantidad DECIMAL(10,2),            -- Cantidad de medicamento a retirar
    IN _idTipoEquino INT,                  -- Tipo de equino relacionado con la salida
    IN _lote VARCHAR(100)                  -- Número de lote del medicamento
)
BEGIN
    DECLARE _idMedicamento INT;
    DECLARE _idLoteMedicamento INT;
    DECLARE _currentStock DECIMAL(10,2);
    DECLARE _debugInfo VARCHAR(255) DEFAULT '';  -- Variable para depuración

    -- Iniciar transacción para asegurar consistencia de la operación
    START TRANSACTION;

    -- Validar que la cantidad a retirar sea mayor que cero
    IF _cantidad <= 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La cantidad a retirar debe ser mayor que cero.';
    END IF;

    -- Verificar si el lote existe en LotesMedicamento y obtener su ID
    SELECT idLoteMedicamento INTO _idLoteMedicamento
    FROM LotesMedicamento
    WHERE lote = _lote;

    -- Si el lote no existe, generar un error
    IF _idLoteMedicamento IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El lote especificado no existe en LotesMedicamento.';
    END IF;

    -- Buscar el medicamento usando el nombre y lote
    SELECT idMedicamento, cantidad_stock INTO _idMedicamento, _currentStock
    FROM Medicamentos
    WHERE LOWER(nombreMedicamento) = LOWER(_nombreMedicamento)
      AND idLoteMedicamento = _idLoteMedicamento
    LIMIT 1 FOR UPDATE;

    -- Si el medicamento no existe para el lote, generar un error
    IF _idMedicamento IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El medicamento con este lote no está registrado.';
    ELSEIF _currentStock < _cantidad THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No hay suficiente stock disponible en el lote seleccionado.';
    ELSE
        -- Realizar la salida del stock en el lote seleccionado
        UPDATE Medicamentos
        SET cantidad_stock = cantidad_stock - _cantidad,
            ultima_modificacion = NOW()
        WHERE idMedicamento = _idMedicamento;

        -- Registrar la salida en el historial de movimientos
        INSERT INTO HistorialMovimientosMedicamentos (idMedicamento, tipoMovimiento, cantidad, idUsuario, fechaMovimiento, idTipoEquino)
        VALUES (_idMedicamento, 'Salida', _cantidad, _idUsuario, NOW(), _idTipoEquino);

        -- Confirmar la transacción
        COMMIT;

        -- Confirmación de éxito
        SET _debugInfo = 'Transacción completada exitosamente.';
        SIGNAL SQLSTATE '01000' SET MESSAGE_TEXT = _debugInfo;
    END IF;
END $$
DELIMITER ;





-- 1. Notificación de Stock Bajo
DELIMITER $$
CREATE PROCEDURE spu_notificar_stock_bajo_medicamentos()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE medicamentoNombre VARCHAR(100);
    DECLARE lote VARCHAR(50);
    DECLARE cantidadStock INT;

    -- Cursor para seleccionar medicamentos con stock bajo o agotado, limitando a 5 resultados
    DECLARE cur CURSOR FOR
        SELECT m.nombreMedicamento, lm.lote, m.cantidad_stock
        FROM Medicamentos m
        JOIN LotesMedicamento lm ON m.idLoteMedicamento = lm.idLoteMedicamento
        WHERE m.cantidad_stock < m.stockMinimo OR m.cantidad_stock = 0
        LIMIT 5;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    -- Abrir el cursor
    OPEN cur;

    -- Bucle para recorrer los resultados
    read_loop: LOOP
        FETCH cur INTO medicamentoNombre, lote, cantidadStock;
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Imprimir el mensaje de notificación
        IF cantidadStock = 0 THEN
            -- Notificación de medicamentos agotados
            SELECT CONCAT('Medicamento agotado: ', medicamentoNombre, ', Lote: ', lote, ', Stock: ', cantidadStock) AS Notificacion;
        ELSE
            -- Notificación de medicamentos con stock bajo
            SELECT CONCAT('Medicamento con stock bajo: ', medicamentoNombre, ', Lote: ', lote, ', Stock: ', cantidadStock) AS Notificacion;
        END IF;
    END LOOP;

    -- Cerrar el cursor
    CLOSE cur;
END $$
DELIMITER ;



-- 2. Procedimiento para registrar historial de medicamentos 
DELIMITER $$
CREATE PROCEDURE spu_historial_completo_medicamentos(
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
            h.idMedicamento,
            m.nombreMedicamento,           -- Nombre del medicamento
            m.descripcion,                 -- Descripción del medicamento
            m.cantidad_stock AS stockActual, -- Stock actual
            h.cantidad,                    -- Cantidad de entrada
            h.fechaMovimiento              -- Fecha del movimiento
        FROM 
            HistorialMovimientosMedicamentos h
        JOIN 
            Medicamentos m ON h.idMedicamento = m.idMedicamento -- Unimos ambas tablas por idMedicamento
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
            h.idMedicamento,
            m.nombreMedicamento,           -- Nombre del medicamento
            m.descripcion,                 -- Descripción del medicamento
            te.tipoEquino,                 -- Tipo de equino (solo en salidas, si aplica)
            h.cantidad,                    -- Cantidad de salida
            h.fechaMovimiento              -- Fecha del movimiento
        FROM 
            HistorialMovimientosMedicamentos h
        JOIN 
            Medicamentos m ON h.idMedicamento = m.idMedicamento -- Unimos ambas tablas por idMedicamento
        LEFT JOIN
            TipoEquinos te ON h.idTipoEquino = te.idTipoEquino  -- Unimos con la tabla TipoEquinos (solo para salidas)
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
    IN _tipoMedicamento VARCHAR(100),
    IN _presentacionMedicamento VARCHAR(100),
    IN _dosisMedicamento VARCHAR(50)
)
BEGIN
    DECLARE _idTipo INT;
    DECLARE _idPresentacion INT;
    DECLARE _idCombinacion INT;
    DECLARE _unidadDosis VARCHAR(50);
    DECLARE _errorMensaje VARCHAR(255);

    -- Manejador de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMensaje;
    END;

    -- Iniciar la transacción
    START TRANSACTION;

    -- Validar tipo de medicamento
    SELECT idTipo INTO _idTipo
    FROM TiposMedicamentos
    WHERE LOWER(tipo) = LOWER(_tipoMedicamento)
    LIMIT 1;

    IF _idTipo IS NULL THEN
        SET _errorMensaje = CONCAT('Error: El tipo de medicamento "', _tipoMedicamento, '" no es válido o no existe.');
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMensaje;
    END IF;

    -- Validar presentación
    SELECT idPresentacion INTO _idPresentacion
    FROM PresentacionesMedicamentos
    WHERE LOWER(presentacion) = LOWER(_presentacionMedicamento)
    LIMIT 1;

    IF _idPresentacion IS NULL THEN
        SET _errorMensaje = CONCAT('Error: La presentación "', _presentacionMedicamento, '" no es válida o no existe.');
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMensaje;
    END IF;

    -- Obtener solo la unidad de medida de la dosis
    SET _unidadDosis = TRIM(LOWER(REPLACE(_dosisMedicamento, '[0-9]+', '')));

    -- Buscar si ya existe una combinación con el mismo tipo, presentación y unidad de dosis
    SELECT idCombinacion INTO _idCombinacion
    FROM CombinacionesMedicamentos
    WHERE idTipo = _idTipo
      AND idPresentacion = _idPresentacion
      AND LOWER(REPLACE(dosis, '[0-9]+', '')) = _unidadDosis
    LIMIT 1;

    IF _idCombinacion IS NOT NULL THEN
        -- Si la combinación ya existe, confirma la transacción y devuelve el ID de combinación existente
        COMMIT;
        SELECT 'Combinación válida, reutilizada' AS mensaje, _idCombinacion AS idCombinacion;
    ELSE
        -- Insertar una nueva combinación si no existe
        INSERT INTO CombinacionesMedicamentos (idTipo, idPresentacion, dosis)
        VALUES (_idTipo, _idPresentacion, _dosisMedicamento);

        SET _idCombinacion = LAST_INSERT_ID();

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
CREATE PROCEDURE spu_listar_lotes_medicamentos()
BEGIN
    -- Seleccionar todos los lotes registrados en la tabla LotesMedicamento junto con los datos asociados de Medicamentos
    SELECT 
        lm.lote,                         -- Lote del medicamento
        m.nombreMedicamento,             -- Nombre del medicamento
        m.descripcion,                   -- Descripción del medicamento
        m.cantidad_stock,                -- Stock actual del medicamento
        m.stockMinimo,                   -- Stock mínimo del medicamento
        lm.fechaCaducidad,               -- Fecha de caducidad del lote
        m.estado                         -- Estado del medicamento
    FROM 
        LotesMedicamento lm
    JOIN 
        Medicamentos m ON lm.idLoteMedicamento = m.idLoteMedicamento; -- Unir con Medicamentos según el lote
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