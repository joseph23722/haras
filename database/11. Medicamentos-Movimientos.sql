-- -------------------------------------------------------------------------------------------
DELIMITER $$

CREATE PROCEDURE spu_listar_medicamentosMedi()
BEGIN
    -- Actualizar el estado de los registros en la tabla Medicamentos
    UPDATE Medicamentos 
    SET estado = 'Agotado'
    WHERE cantidad_stock = 0;

    UPDATE Medicamentos 
    SET estado = 'Por agotarse'
    WHERE cantidad_stock > 0 AND cantidad_stock <= stockMinimo;

    UPDATE Medicamentos 
    SET estado = 'Disponible'
    WHERE cantidad_stock > stockMinimo;

    -- Mostrar la información detallada de todos los medicamentos registrados
    SELECT 
        m.idMedicamento,
        m.nombreMedicamento,
        m.descripcion,
        lm.lote,                         -- Lote del medicamento (desde LotesMedicamento)
        p.presentacion,
        CONCAT(c.dosis, ' ', u.unidad) AS dosis,  -- Concatenar la cantidad y la unidad de medida
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
        UnidadesMedida u ON c.idUnidad = u.idUnidad  -- Relación con UnidadesMedida para obtener la unidad
    JOIN
        LotesMedicamento lm ON m.idLoteMedicamento = lm.idLoteMedicamento -- Relación con LotesMedicamento
    ORDER BY 
        m.nombreMedicamento ASC; -- Ordenar alfabéticamente por nombre de medicamento
END $$
DELIMITER ;


-- Procedimiento para registrar medicamentos---------------------------------------------------------------------------------------------------------
-- Procedimiento para registrar medicamentos con verificación de lote en LotesMedicamento
DROP PROCEDURE IF EXISTS `spu_medicamentos_registrar`;
DELIMITER $$

CREATE PROCEDURE spu_medicamentos_registrar(
    IN _nombreMedicamento VARCHAR(255),
    IN _descripcion TEXT, 
    IN _lote VARCHAR(100),
    IN _presentacion VARCHAR(100),
    IN _dosisCompleta VARCHAR(50), -- Ej. "56 mg"
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
    DECLARE _idTipo INT;
    DECLARE _idPresentacion INT;
    DECLARE _idUnidad INT;
    DECLARE _dosis DECIMAL(10,2);
    DECLARE _unidad VARCHAR(50);

    -- Manejador de errores: si ocurre un error, se revierte la transacción
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    -- Iniciar la transacción
    START TRANSACTION;

    -- Separar la dosis en cantidad y unidad
    SET _dosis = CAST(SUBSTRING_INDEX(_dosisCompleta, ' ', 1) AS DECIMAL(10,2)); -- Extrae la parte numérica
    SET _unidad = TRIM(SUBSTRING_INDEX(_dosisCompleta, ' ', -1)); -- Extrae la parte de la unidad

    -- Obtener o insertar idTipo
    SELECT idTipo INTO _idTipo FROM TiposMedicamentos WHERE LOWER(tipo) = LOWER(_tipo);
    IF _idTipo IS NULL THEN
        INSERT INTO TiposMedicamentos (tipo) VALUES (_tipo);
        SET _idTipo = LAST_INSERT_ID();
    END IF;

    -- Obtener o insertar idPresentacion
    SELECT idPresentacion INTO _idPresentacion FROM PresentacionesMedicamentos WHERE LOWER(presentacion) = LOWER(_presentacion);
    IF _idPresentacion IS NULL THEN
        INSERT INTO PresentacionesMedicamentos (presentacion) VALUES (_presentacion);
        SET _idPresentacion = LAST_INSERT_ID();
    END IF;

    -- Obtener o insertar idUnidad
    SELECT idUnidad INTO _idUnidad FROM UnidadesMedida WHERE LOWER(unidad) = LOWER(_unidad);
    IF _idUnidad IS NULL THEN
        INSERT INTO UnidadesMedida (unidad) VALUES (_unidad);
        SET _idUnidad = LAST_INSERT_ID();
    END IF;

    -- Obtener o insertar idCombinacion
    SELECT idCombinacion INTO _idCombinacion
    FROM CombinacionesMedicamentos
    WHERE idTipo = _idTipo AND idPresentacion = _idPresentacion AND dosis = _dosis AND idUnidad = _idUnidad;

    IF _idCombinacion IS NULL THEN
        INSERT INTO CombinacionesMedicamentos (idTipo, idPresentacion, dosis, idUnidad)
        VALUES (_idTipo, _idPresentacion, _dosis, _idUnidad);
        SET _idCombinacion = LAST_INSERT_ID();
    END IF;

    -- Obtener o insertar idLoteMedicamento
    SELECT idLoteMedicamento INTO _idLoteMedicamento 
    FROM LotesMedicamento
    WHERE lote = _lote;

    IF _idLoteMedicamento IS NULL THEN
        INSERT INTO LotesMedicamento (lote, fechaCaducidad) 
        VALUES (_lote, _fechaCaducidad);
        SET _idLoteMedicamento = LAST_INSERT_ID();
    END IF;

    -- Insertar el medicamento en la tabla Medicamentos
    INSERT INTO Medicamentos (
        nombreMedicamento, 
        descripcion, 
        idLoteMedicamento, 
        idCombinacion,
        cantidad_stock,
        stockMinimo, 
        estado,
        fecha_registro,
        precioUnitario, 
        idUsuario
    ) 
    VALUES (
        _nombreMedicamento, 
        _descripcion, 
        _idLoteMedicamento, 
        _idCombinacion,
        _cantidad_stock, 
        _stockMinimo, 
        'Disponible',
        CURDATE(), 
        _precioUnitario, 
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
    IN _lote VARCHAR(100),                 -- Número de lote del medicamento (puede ser NULL)
    IN _motivo TEXT                        -- Motivo de la salida del medicamento
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

    -- Verificar si el lote fue proporcionado
    IF _lote IS NOT NULL AND _lote != '' THEN
        -- Si se proporciona el lote, obtener su ID
        SELECT idLoteMedicamento INTO _idLoteMedicamento
        FROM LotesMedicamento
        WHERE lote = _lote;

        -- Si el lote no existe, generar un error
        IF _idLoteMedicamento IS NULL THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El lote especificado no existe en LotesMedicamento.';
        END IF;
    ELSE
        -- Si el lote no es proporcionado, buscar el lote más antiguo con la fecha de caducidad más próxima
        SELECT idLoteMedicamento INTO _idLoteMedicamento
        FROM LotesMedicamento
        WHERE idLoteMedicamento IN (
            SELECT idLoteMedicamento
            FROM Medicamentos
            WHERE LOWER(nombreMedicamento) = LOWER(_nombreMedicamento)
              AND cantidad_stock > 0
        )
        ORDER BY fechaCaducidad ASC
        LIMIT 1;

        -- Si no se encuentra un lote con stock disponible, generar un error
        IF _idLoteMedicamento IS NULL THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No hay lotes disponibles para este medicamento con stock suficiente.';
        END IF;
    END IF;

    -- Buscar el medicamento usando el nombre y el idLoteMedicamento obtenido
    SELECT idMedicamento, cantidad_stock INTO _idMedicamento, _currentStock
    FROM Medicamentos
    WHERE LOWER(nombreMedicamento) = LOWER(_nombreMedicamento)
      AND idLoteMedicamento = _idLoteMedicamento
    LIMIT 1 FOR UPDATE;

    -- Si el medicamento no existe o no tiene suficiente stock, generar un error
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

        -- Registrar la salida en el historial de movimientos con motivo
        INSERT INTO HistorialMovimientosMedicamentos (idMedicamento, tipoMovimiento, cantidad, idUsuario, fechaMovimiento, idTipoEquino, motivo)
        VALUES (_idMedicamento, 'Salida', _cantidad, _idUsuario, NOW(), _idTipoEquino, _motivo);

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
    -- Notificaciones para medicamentos agotados
    SELECT 
        CONCAT('Medicamento agotado: ', m.nombreMedicamento, ', Lote: ', lm.lote, ', Stock: ', m.cantidad_stock) AS Notificacion
    FROM 
        Medicamentos m
    JOIN 
        LotesMedicamento lm ON m.idLoteMedicamento = lm.idLoteMedicamento
    WHERE 
        m.cantidad_stock = 0
    LIMIT 5;

    -- Notificaciones para medicamentos con stock bajo
    SELECT 
        CONCAT('Medicamento con stock bajo: ', m.nombreMedicamento, ', Lote: ', lm.lote, ', Stock: ', m.cantidad_stock, ' (Stock mínimo: ', m.stockMinimo, ')') AS Notificacion
    FROM 
        Medicamentos m
    JOIN 
        LotesMedicamento lm ON m.idLoteMedicamento = lm.idLoteMedicamento
    WHERE 
        m.cantidad_stock > 0 AND m.cantidad_stock < m.stockMinimo
    LIMIT 5;
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
            h.motivo,                      -- Motivo de la salida
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


-- 2.Procedimiento para agregar una nueva presentacion
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


-- 3.Procedimiento para agregar una nueva dosis (composicion)
DELIMITER $$
CREATE PROCEDURE spu_agregar_unidad_medida(
    IN _unidad VARCHAR(50)
)
BEGIN
    DECLARE _exists INT DEFAULT 0;
    
    -- Verificar si la unidad ya existe
    SELECT COUNT(*) INTO _exists 
    FROM UnidadesMedida
    WHERE LOWER(unidad) = LOWER(_unidad);
    
    IF _exists = 0 THEN
        -- Insertar nueva unidad si no existe
        INSERT INTO UnidadesMedida (unidad) VALUES (_unidad);
    ELSE
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La unidad de medida ya existe.';
    END IF;
END $$
DELIMITER ;


-- 1. Procedimiento para validar presentación tipo y dosis:

DELIMITER $$

CREATE PROCEDURE spu_validar_registrar_combinacion(
    IN _tipoMedicamento VARCHAR(100),
    IN _presentacionMedicamento VARCHAR(100),
    IN _dosisMedicamento DECIMAL(10, 2),
    IN _unidadMedida VARCHAR(50)
)
BEGIN
    DECLARE _idTipo INT;
    DECLARE _idPresentacion INT;
    DECLARE _idUnidad INT;
    DECLARE _idCombinacion INT;
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

    -- Validar unidad de medida
    SELECT idUnidad INTO _idUnidad
    FROM UnidadesMedida
    WHERE LOWER(unidad) = LOWER(_unidadMedida)
    LIMIT 1;

    IF _idUnidad IS NULL THEN
        SET _errorMensaje = CONCAT('Error: La unidad de medida "', _unidadMedida, '" no es válida o no existe.');
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMensaje;
    END IF;

    -- Primera Función: Buscar una combinación exacta
    SELECT idCombinacion INTO _idCombinacion
    FROM CombinacionesMedicamentos
    WHERE idTipo = _idTipo
      AND idPresentacion = _idPresentacion
      AND dosis = _dosisMedicamento
      AND idUnidad = _idUnidad
    LIMIT 1;

    IF _idCombinacion IS NOT NULL THEN
        -- Mensaje específico para combinación exacta
        COMMIT;
        SELECT 'Combinación exacta encontrada y confirmada' AS mensaje, _idCombinacion AS idCombinacion;
    ELSE
        -- Segunda Función: Si la unidad y presentación coinciden, registrar nueva combinación
        INSERT INTO CombinacionesMedicamentos (idTipo, idPresentacion, dosis, idUnidad)
        VALUES (_idTipo, _idPresentacion, _dosisMedicamento, _idUnidad);

        SET _idCombinacion = LAST_INSERT_ID();

        COMMIT;
        SELECT 'Nueva combinación registrada y válida.' AS mensaje, _idCombinacion AS idCombinacion;
    END IF;
END $$
DELIMITER ;




-- sugerencias
DELIMITER $$
CREATE PROCEDURE spu_listar_tipos_presentaciones_dosis()
BEGIN
    -- Selecciona los tipos de medicamentos junto con la presentación y la dosis (cantidad y unidad), agrupados
    SELECT 
        t.tipo, 
        GROUP_CONCAT(DISTINCT p.presentacion ORDER BY p.presentacion ASC SEPARATOR ', ') AS presentaciones,
        GROUP_CONCAT(DISTINCT CONCAT( u.unidad) ORDER BY c.dosis ASC SEPARATOR ', ') AS dosis
    FROM 
        CombinacionesMedicamentos c
    JOIN 
        TiposMedicamentos t ON c.idTipo = t.idTipo
    JOIN 
        PresentacionesMedicamentos p ON c.idPresentacion = p.idPresentacion
    JOIN 
        UnidadesMedida u ON c.idUnidad = u.idUnidad
    GROUP BY 
        t.tipo
    ORDER BY 
        t.tipo ASC;  -- Ordena por tipo de medicamento
END $$
DELIMITER ;



-- ---- listar tipos
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

-- --------------------- listar lotes
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

SELECT * FROM UnidadesMedida;

-- Insertar Unidades de Medida
INSERT INTO UnidadesMedida (unidad) VALUES
('mg'),
('ml'),
('g'),
('mcg'),
('fL'),
('dL'),
('L'),
('mcl'),
('mcmol'),
('mEq'),
('mm'),
('mm Hg'),
('mmol'),
('mOsm'),
('mUI'),
('mU'),
('ng'),
('nmol'),
('pg'),
('pmol'),
('UI'),
('U');



-- Insertar Combinaciones de Medicamentos
INSERT INTO CombinacionesMedicamentos (idTipo, idPresentacion, dosis, idUnidad) VALUES
(1, 1, 500, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'mg')),       -- Antibiótico, tabletas, 500 mg
(2, 2, 10, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'ml')),        -- Analgésico, jarabes, 10 ml
(3, 3, 200, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'mg')),       -- Antiinflamatorio, cápsulas, 200 mg
(1, 4, 1, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'g')),          -- Antibiótico, inyectable, 1 g
(2, 1, 50, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'mg')),        -- Analgésico, tabletas, 50 mg
(3, 5, 5, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'mg')),         -- Antiinflamatorio, suspensión, 5 mg (para mg/ml usar lógica extra si es necesario)
(4, 6, 300, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'mg')),       -- Gastroprotector, grageas, 300 mg
(5, 7, 100, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'g')),        -- Desparasitante, pomadas, 100 g
(6, 8, 1, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'ml')),         -- Suplemento, ampollas, 1 ml
(7, 9, 0.5, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'ml')),       -- Broncodilatador, colirios, 0.5 ml
(8, 10, 20, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'mg')),       -- Antifúngico, gotas nasales, 20 mg (complejo mg/ml manejado aparte si necesario)
(9, 11, 5, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'mg')),        -- Sedante, píldoras, 5 mg
(10, 12, 1, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'g')),        -- Vacuna, comprimidos, 1 g
(5, 13, 50, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'mg')),       -- Desparasitante, enemas, 50 mg
(3, 14, 15, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'mg')),       -- Antiinflamatorio, goteros, 15 mg (si es mg/ml manejar aparte)
(1, 15, 250, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'mg')),      -- Antibiótico, polvos medicinales, 250 mg
(3, 16, 100, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'mcg')),     -- Antiinflamatorio, aerosoles, 100 mcg
(6, 1, 10, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'fL')),        -- Suplemento, tabletas, 10 fL
(11, 4, 200, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'g')),       -- Antiparasitario, suspensión, 200 g (asume g/dL según lógica de negocio)
(1, 4, 5, (SELECT idUnidad FROM UnidadesMedida WHERE unidad = 'g'));          -- Antibiótico, inyectable, 5 g (para g/L manejar como necesario)

