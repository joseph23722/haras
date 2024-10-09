-- tablas para la adminsitrar   |°|

-- HistorialMovimientosMedicamentos
DROP TABLE IF EXISTS HistorialMovimientosMedicamentos;
CREATE TABLE HistorialMovimientosMedicamentos (
    idMovimiento INT PRIMARY KEY AUTO_INCREMENT,     -- ID único del movimiento
    idMedicamento INT NOT NULL,                      -- Relación con el medicamento
    tipoMovimiento ENUM('Entrada', 'Salida', 'Lote Eliminado') NOT NULL, -- Tipo de movimiento
    cantidad DECIMAL(10,2) NOT NULL,                 -- Cantidad que entra o sale
    idUsuario INT NOT NULL,                          -- Usuario que realiza el movimiento
    fechaMovimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Fecha y hora del movimiento
    CONSTRAINT fk_movimiento_medicamento FOREIGN KEY (idMedicamento) REFERENCES Medicamentos(idMedicamento),
    CONSTRAINT fk_movimiento_usuario FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario)
) ENGINE = INNODB;



-- Crear Tabla de Tipos Medicamentos
DROP TABLE IF EXISTS TiposMedicamentos;
CREATE TABLE TiposMedicamentos (
    idTipo INT AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(100) NOT NULL UNIQUE  -- Tipo de medicamento, debe ser único
);

-- tabla para veterinario 

-- Crear tabla HistorialMedicamentosMedi
DROP TABLE IF EXISTS HistorialMedicamentosMedi;
CREATE TABLE HistorialMedicamentosMedi (
    idHistorial INT PRIMARY KEY AUTO_INCREMENT,
    idMedicamento INT NOT NULL, -- Relación con la tabla Medicamentos
    accion VARCHAR(50) NOT NULL, -- Acción registrada (Agregar, Eliminar, Actualizar, etc.)
    fecha DATETIME DEFAULT NOW(), -- Fecha de la acción
    CONSTRAINT fk_historial_medicamento FOREIGN KEY (idMedicamento) REFERENCES Medicamentos(idMedicamento)
) ENGINE = INNODB;

-- Crear tabla HistorialMovimientosMedi
DROP TABLE IF EXISTS HistorialMovimientosMedi;
CREATE TABLE HistorialMovimientosMedi (
    idMovimiento INT PRIMARY KEY AUTO_INCREMENT,
    idMedicamento INT NOT NULL, -- Relación con la tabla Medicamentos
    tipoMovimiento VARCHAR(50) NOT NULL, -- Tipo de movimiento (Entrada o Salida)
    cantidad INT NOT NULL, -- Cantidad involucrada en el movimiento
    fechaMovimiento DATETIME DEFAULT NOW(), -- Fecha del movimiento
    CONSTRAINT fk_historial_movimiento_medicamento FOREIGN KEY (idMedicamento) REFERENCES Medicamentos(idMedicamento)
) ENGINE = INNODB;


-- Procedimiento para registrar un nuevo historial médico de un equino-------------------------------------------------------------------------------------------------
DELIMITER $$

CREATE PROCEDURE spu_historial_medico_registrarMedi(
    IN _idEquino INT,
    IN _idUsuario INT,
    IN _idMedicamento INT,
    IN _dosis VARCHAR(50), -- Cambiado a VARCHAR(50)
    IN _frecuenciaAdministracion VARCHAR(50),
    IN _viaAdministracion VARCHAR(50),
    IN _pesoEquino DECIMAL(10,2), -- Se manejará NULL dentro del procedimiento
    IN _fechaInicio DATE,
    IN _fechaFin DATE,
    IN _observaciones TEXT,
    IN _reaccionesAdversas TEXT -- Se manejará NULL dentro del procedimiento
)
BEGIN
    DECLARE _nombreMedicamento VARCHAR(255);
    DECLARE _presentacion VARCHAR(100);
    DECLARE _tipoMedicamento VARCHAR(100);

    -- Verificar que el equino exista
    IF NOT EXISTS (SELECT 1 FROM Equinos WHERE idEquino = _idEquino) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El equino no existe.';
    END IF;
    
    -- Verificar que el usuario exista
    IF NOT EXISTS (SELECT 1 FROM Usuarios WHERE idUsuario = _idUsuario) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El usuario no existe.';
    END IF;
    
    -- Verificar que el medicamento exista
    IF NOT EXISTS (SELECT 1 FROM Medicamentos WHERE idMedicamento = _idMedicamento) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El medicamento no existe.';
    END IF;

    -- Obtener detalles del medicamento
    SELECT nombreMedicamento, presentacion, tipo 
    INTO _nombreMedicamento, _presentacion, _tipoMedicamento
    FROM Medicamentos M
    JOIN TiposMedicamentos T ON M.idTipo = T.idTipo
    WHERE M.idMedicamento = _idMedicamento;

    -- Validar la combinación de presentación, dosis y tipo de medicamento
    CALL spu_validar_combinacion(_nombreMedicamento, _presentacion, _dosis, _tipoMedicamento);

    -- Validar que la dosis no esté vacía
    IF _dosis = '' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La dosis no puede estar vacía.';
    END IF;

    -- Validar que la fecha de inicio y la fecha de fin sean correctas
    IF _fechaInicio > _fechaFin THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La fecha de inicio no puede ser posterior a la fecha de fin.';
    END IF;

    -- Validar que el peso del equino no sea negativo (si se proporciona)
    IF _pesoEquino IS NOT NULL AND _pesoEquino < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El peso del equino no puede ser un valor negativo.';
    END IF;

    -- Validar que la frecuencia de administración no sea vacía
    IF _frecuenciaAdministracion = '' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La frecuencia de administración no puede estar vacía.';
    END IF;

    -- Verificar que la vía de administración no sea vacía
    IF _viaAdministracion = '' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La vía de administración no puede estar vacía.';
    END IF;

    -- Insertar el detalle del medicamento administrado al equino
    INSERT INTO DetalleMedicamentos (
        idMedicamento, 
        idEquino, 
        dosis, 
        frecuenciaAdministracion, 
        viaAdministracion, 
        pesoEquino, 
        fechaInicio, 
        fechaFin, 
        observaciones, 
        reaccionesAdversas, 
        idUsuario
    ) 
    VALUES (
        _idMedicamento, 
        _idEquino, 
        _dosis, 
        _frecuenciaAdministracion, 
        _viaAdministracion, 
        IFNULL(_pesoEquino, NULL), -- Manejar NULL para pesoEquino
        _fechaInicio, 
        _fechaFin, 
        _observaciones, 
        IFNULL(_reaccionesAdversas, NULL), -- Manejar NULL para reaccionesAdversas
        _idUsuario
    );
END $$

DELIMITER ;


-- listar equinos propios para medicamentos;
DELIMITER $$
CREATE PROCEDURE spu_listar_equinos_propiosMedi()
BEGIN
    SELECT 
        idEquino,
        nombreEquino,
        sexo,
        idTipoEquino
    FROM 
        Equinos
    WHERE 
        idPropietario IS NULL  -- Filtrar solo los equinos que no tienen propietario
        AND idTipoEquino IN (1, 2, 3, 4);  -- Filtrar solo Yeguas (1), Padrillos (2), Potrancas (3), y Potrillos (4)
END $$
DELIMITER ;

-- 
DELIMITER $$

CREATE PROCEDURE spu_consultar_historial_medicoMedi(
    IN _idEquino INT
)
BEGIN
    SELECT 
        DM.idDetalleMed AS idRegistro,
        DM.idEquino,
        E.nombreEquino,
        DM.idMedicamento,
        M.nombreMedicamento,
        DM.dosis,
        DM.frecuenciaAdministracion,
        DM.viaAdministracion,
        DM.pesoEquino,
        DM.fechaInicio,
        DM.fechaFin,
        DM.observaciones,
        DM.reaccionesAdversas,
        -- No hay columna nombreUsuario, mostrar solo idUsuario como responsable
        DM.idUsuario AS responsable,
        DM.fechaInicio AS fechaRegistro
    FROM 
        DetalleMedicamentos DM
    JOIN 
        Medicamentos M ON DM.idMedicamento = M.idMedicamento
    JOIN 
        Equinos E ON DM.idEquino = E.idEquino
    JOIN 
        Usuarios U ON DM.idUsuario = U.idUsuario -- Aquí mostramos el idUsuario como responsable
    WHERE 
        DM.idEquino = _idEquino
    ORDER BY 
        DM.fechaInicio DESC;
END $$
DELIMITER ;


DELIMITER $$

CREATE PROCEDURE spu_listar_medicamentosMedi()
BEGIN
    SELECT 
        M.idMedicamento, 
        M.nombreMedicamento, 
        M.descripcion, 
        M.lote, 
        M.presentacion, 
        M.dosis, 
        T.tipo AS tipoMedicamento, 
        M.cantidad_stock, 
        M.stockMinimo, 
        M.fecha_registro, 
        M.fecha_caducidad, 
        M.precioUnitario, 
        M.estado 
    FROM 
        Medicamentos M
    JOIN 
        TiposMedicamentos T ON M.idTipo = T.idTipo;
END $$

DELIMITER ;


CALL spu_listar_medicamentosMedi();


CALL spu_historial_medico_registrarMedi(
    5,                  -- _idEquino (ID del equino, asegúrate de que este equino exista en la tabla Equinos)
    1,                  -- _idUsuario (ID del usuario que está registrando, asegúrate de que este usuario exista en la tabla Usuarios)
    127,                  -- _idMedicamento (ID del medicamento, asegúrate de que este medicamento exista en la tabla Medicamentos)
    5.00,               -- _dosis (dosis administrada en mg, ml, etc.)
    'Cada 8 horas',     -- _frecuenciaAdministracion (frecuencia con la que se administra el medicamento)
    'Oral',             -- _viaAdministracion (vía de administración, por ejemplo, 'Oral')
    450.00,             -- _pesoEquino (peso del equino, puede ser NULL si no se proporciona)
    '2024-10-01',       -- _fechaInicio (fecha en que se inicia el tratamiento)
    '2024-10-07',       -- _fechaFin (fecha en que termina el tratamiento)
    'El equino ha mostrado mejoría.', -- _observaciones (observaciones adicionales, puede ser NULL)
    'Ninguna'           -- _reaccionesAdversas (si no hubo reacciones adversas, puede ser NULL)
);

select * from Medicamentos;
CALL spu_consultar_historial_medicoMedi(5); 

















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
    DECLARE _exists INT DEFAULT 0;
    DECLARE _existeCombinacion INT DEFAULT 0;

    -- Iniciar la transacción
    START TRANSACTION;

    -- Buscar el idTipo a partir del nombre del tipo
    SELECT idTipo INTO _idTipo
    FROM TiposMedicamentos
    WHERE LOWER(tipo) = LOWER(_tipo)
    LIMIT 1;

    -- Si el tipo de medicamento no existe, devolver un error
    IF _idTipo IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: El tipo de medicamento no es válido.';
    END IF;

    -- Verificar si la combinación ya existe en CombinacionesValidas
    SELECT COUNT(*) INTO _existeCombinacion
    FROM CombinacionesValidas
    WHERE LOWER(nombreMedicamento) = LOWER(_nombreMedicamento)
      AND LOWER(presentacion) = LOWER(_presentacion)
      AND LOWER(dosis) = LOWER(_dosis)
      AND LOWER(tipoMedicamento) = LOWER(_tipo);

    -- Si la combinación es válida, registrar el medicamento; si no existe la combinación, se inserta en CombinacionesValidas.
    IF _existeCombinacion = 0 THEN
        -- Validar la presentación, dosis y tipo según la lógica
        IF LOWER(_presentacion) NOT IN ('tabletas', 'cápsulas', 'inyección') THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Error: La presentación no es válida.';
        END IF;

        IF LOWER(_dosis) NOT LIKE '%mg%' THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Error: La dosis no es válida.';
        END IF;

        -- Si pasa todas las validaciones, registrar la combinación en CombinacionesValidas
        INSERT INTO CombinacionesValidas (nombreMedicamento, presentacion, dosis, tipoMedicamento)
        VALUES (_nombreMedicamento, _presentacion, _dosis, _tipo);
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
            presentacion,
            dosis,
            idTipo,
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
            _presentacion,
            _dosis, 
            _idTipo,
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

    -- Mostrar la información del medicamento registrado
    SELECT 
        m.idMedicamento,
        m.nombreMedicamento,
        m.descripcion,
        m.lote,
        m.presentacion,
        m.dosis,
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
        TiposMedicamentos t ON m.idTipo = t.idTipo
    WHERE 
        m.lote = _lote AND m.nombreMedicamento = _nombreMedicamento;

END $$
DELIMITER ;

CALL spu_medicamentos_entrada(
    1,                           -- idUsuario
    'Amoxicilina',               -- nombreMedicamento
    'LOTE17A',                   -- lote
    'Cápsulas',                  -- presentacion
    '500 mg',                    -- dosis
    'Antibiótico',               -- tipo
    200,                         -- cantidad_stock
    20,                          -- stockMinimo
    '2026-09-01',                -- fecha_caducidad
    5.00                         -- precioUnitario
);
select * from Medicamentos;
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
    DECLARE _currentStock DECIMAL(10,2);
    DECLARE _currentPrice DECIMAL(10,2);
    DECLARE _currentFechaCaducidad DATE;
    DECLARE _nuevoPrecioPonderado DECIMAL(10,2); -- Precio ponderado si el precio es diferente
    DECLARE _cantidadTotal DECIMAL(10,2);        -- Nueva cantidad total para el lote
    DECLARE _estadoActual VARCHAR(20);
    DECLARE _existeCombinacion INT DEFAULT 0;

    -- Iniciar transacción para asegurar la consistencia de la operación
    START TRANSACTION;

    -- Validaciones estrictas
    IF _cantidad_stock <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La cantidad debe ser mayor que cero.';
    END IF;

    IF _precioUnitario IS NOT NULL AND (_precioUnitario < 0 OR _precioUnitario > 999999.99) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El precio no es válido.';
    END IF;

    IF _lote IS NULL OR _lote = '' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El lote no puede ser vacío o nulo.';
    END IF;

    -- Validación de combinaciones de nombre, presentación, dosis y tipo de medicamento
    SELECT COUNT(*) INTO _existeCombinacion
    FROM CombinacionesValidas
    WHERE LOWER(nombreMedicamento) = LOWER(_nombreMedicamento)
      AND LOWER(presentacion) = LOWER(_presentacion)
      AND LOWER(dosis) = LOWER(_dosis)
      AND LOWER(tipoMedicamento) = LOWER(_tipo);

    -- Si la combinación no es válida, devolver un error
    IF _existeCombinacion = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: La combinación de presentación, dosis y tipo no es válida para este medicamento.';
    END IF;

    -- Verificar si ya existe un lote con el mismo nombre de medicamento, número de lote y combinación válida
    SELECT COUNT(*), idMedicamento, cantidad_stock, precioUnitario, fecha_caducidad
    INTO _exists, _idMedicamento, _currentStock, _currentPrice, _currentFechaCaducidad
    FROM Medicamentos
    WHERE LOWER(nombreMedicamento) = LOWER(_nombreMedicamento)
      AND LOWER(lote) = LOWER(_lote);

    -- Caso 1: Si el lote ya existe y la fecha de caducidad es la misma (o NULL en ambos), actualizar el stock
    IF _exists > 0 AND (_currentFechaCaducidad = _fecha_caducidad OR (_currentFechaCaducidad IS NULL AND _fecha_caducidad IS NULL)) THEN
        SET _cantidadTotal = _currentStock + _cantidad_stock;
        UPDATE Medicamentos
        SET cantidad_stock = _cantidadTotal
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
            idUsuario, nombreMedicamento, presentacion, dosis, tipo, cantidad_stock, lote, fecha_caducidad, 
            precioUnitario, stockMinimo
        ) 
        VALUES (
            _idUsuario, _nombreMedicamento, _presentacion, _dosis, _tipo, _cantidad_stock, _lote, _fecha_caducidad, 
            _precioUnitario, _stockMinimo
        );

    -- Caso 3: Si el lote no existe, crear uno nuevo
    ELSE
        INSERT INTO Medicamentos (
            idUsuario, nombreMedicamento, presentacion, dosis, tipo, cantidad_stock, lote, fecha_caducidad, 
            precioUnitario, stockMinimo
        ) 
        VALUES (
            _idUsuario, _nombreMedicamento, _presentacion, _dosis, _tipo, _cantidad_stock, _lote, _fecha_caducidad, 
            _precioUnitario, _stockMinimo
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



CALL spu_medicamentos_salida(1, 'Ibuprofeno', 10);
SET SQL_SAFE_UPDATES = 0;

select * from medicamentos;
SELECT * 
FROM Medicamentos 
WHERE LOWER(nombreMedicamento) = LOWER('Ibuprofeno');

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


--  nuevas mejoras para aprender - no borrar 
CREATE TABLE SugerenciasMedicamentos (
    idSugerencia INT AUTO_INCREMENT PRIMARY KEY,
    idMedicamento INT,
    sugerencia TEXT,
    fechaRegistro DATETIME DEFAULT NOW(),
    FOREIGN KEY (idMedicamento) REFERENCES Medicamentos(idMedicamento)
);

CREATE TABLE CombinacionesValidas (
    idCombinacion INT AUTO_INCREMENT PRIMARY KEY,
    nombreMedicamento VARCHAR(255),
    presentacion VARCHAR(100),
    dosis VARCHAR(50),
    tipoMedicamento VARCHAR(100),
    fechaRegistro DATETIME DEFAULT NOW(),
    UNIQUE (nombreMedicamento, presentacion, dosis, tipoMedicamento)
);



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

call spu_listar_tipos_medicamentos();

-- --------------------------------------------------------------------
-- Insertar múltiples tipos de medicamentos en una sola instrucción
INSERT INTO TiposMedicamentos (tipo)
VALUES
    ('Antibiótico'),
    ('Antiinflamatorio'),
    ('Gastroprotector'),
    ('Desparasitante'),
    ('Suplemento'),
    ('Analgésico'),
    ('Broncodilatador'),
    ('Vacuna'),
    ('Sedante'), 
    ('Antifúngico');

-- -------------------------------------------------------------------------------

-- Medicamentos para Yeguas
CALL spu_medicamentos_registrar('Fenilbutazona', 'Antiinflamatorio para yeguas', 'LOTE-001', 'Tabletas', '500 mg', 'Antiinflamatorio', 100, 10, '2024-12-31', 12.50, 1);
CALL spu_medicamentos_registrar('Ivermectina', 'Antiparasitario para yeguas', 'LOTE-002', 'Inyectable', '50 mg/ml', 'Antiparasitario', 200, 20, '2025-05-30', 25.00, 1);
CALL spu_medicamentos_registrar('Omeprazol', 'Protector gástrico para yeguas', 'LOTE-003', 'Tabletas', '20 mg', 'Gastroprotector', 500, 50, '2025-03-01', 5.00, 1);
CALL spu_medicamentos_registrar('Acepromacina', 'Sedante para yeguas', 'LOTE-004', 'Inyectable', '10 mg/ml', 'Sedante', 100, 10, '2025-02-15', 22.00, 1);
CALL spu_medicamentos_registrar('Oxibendazol', 'Antiparasitario para yeguas', 'LOTE-005', 'Suspensión', '100 mg/ml', 'Antiparasitario', 250, 25, '2025-05-05', 12.00, 1);
CALL spu_medicamentos_registrar('Furosemida', 'Diurético para yeguas', 'LOTE-006', 'Inyectable', '10 mg/ml', 'Diurético', 60, 6, '2025-10-01', 25.00, 1);

-- Medicamentos para Padrillos
CALL spu_medicamentos_registrar('Dexametasona', 'Corticosteroide para padrillos', 'LOTE-007', 'Inyectable', '2 mg/ml', 'Corticosteroide', 100, 10, '2024-09-30', 18.50, 1);
CALL spu_medicamentos_registrar('Meloxicam', 'Antiinflamatorio no esteroideo para padrillos', 'LOTE-008', 'Tabletas', '15 mg', 'Antiinflamatorio', 120, 12, '2025-09-12', 9.00, 1);
CALL spu_medicamentos_registrar('Penicilina', 'Antibiótico para padrillos', 'LOTE-009', 'Inyectable', '300,000 UI', 'Antibiótico', 150, 15, '2024-11-15', 30.00, 1);
CALL spu_medicamentos_registrar('Trimetoprima-sulfadiazina', 'Antibiótico de amplio espectro para padrillos', 'LOTE-010', 'Tabletas', '480 mg', 'Antibiótico', 120, 12, '2025-01-01', 14.50, 1);

-- Medicamentos para Potrillos
CALL spu_medicamentos_registrar('Flunixina meglumina', 'Analgésico para potrillos', 'LOTE-011', 'Inyectable', '50 mg/ml', 'Analgésico', 80, 8, '2025-07-10', 20.00, 1);
CALL spu_medicamentos_registrar('Clorhexidina', 'Antiséptico para potrillos', 'LOTE-012', 'Solución', '2%', 'Antiséptico', 500, 50, '2025-06-20', 7.50, 1);
CALL spu_medicamentos_registrar('Ketamina', 'Anestésico para potrillos', 'LOTE-013', 'Inyectable', '50 mg/ml', 'Anestésico', 90, 9, '2024-11-01', 40.00, 1);
CALL spu_medicamentos_registrar('Povidona yodada', 'Antiséptico para potrillos', 'LOTE-014', 'Solución', '10%', 'Antiséptico', 400, 40, '2025-08-15', 5.00, 1);

-- Medicamentos para Potrancas
CALL spu_medicamentos_registrar('Ácido fólico', 'Suplemento vitamínico para potrancas', 'LOTE-015', 'Tabletas', '5 mg', 'Suplemento', 200, 20, '2024-12-15', 3.50, 1);
CALL spu_medicamentos_registrar('Sulfato de cobre', 'Suplemento para potrancas', 'LOTE-016', 'Polvo', '1%', 'Suplemento', 300, 30, '2026-01-01', 2.50, 1);

-- Medicamentos Generales para Equinos
CALL spu_medicamentos_registrar('Sulfato de condroitina', 'Suplemento articular para equinos', 'LOTE-017', 'Polvo', '500 mg', 'Suplemento', 180, 18, '2025-03-20', 15.00, 1);
CALL spu_medicamentos_registrar('Glucosamina', 'Suplemento articular para equinos', 'LOTE-018', 'Tabletas', '1500 mg', 'Suplemento', 150, 15, '2026-04-05', 10.00, 1);
CALL spu_medicamentos_registrar('Sulfato de condroitina', 'Suplemento articular para equinos', 'LOTE-019', 'Polvo', '500 mg', 'Suplemento', 200, 20, '2025-03-20', 15.00, 1);

-- Antiparasitarios para Equinos
CALL spu_medicamentos_registrar('Fenbendazol', 'Antiparasitario de amplio espectro', 'LOTE-020', 'Suspensión', '100 mg/ml', 'Antiparasitario', 100, 10, '2026-03-25', 9.00, 1);
CALL spu_medicamentos_registrar('Moxidectina', 'Antiparasitario para equinos', 'LOTE-021', 'Inyectable', '10 mg/ml', 'Antiparasitario', 120, 12, '2025-05-15', 12.50, 1);
CALL spu_medicamentos_registrar('Pirantel', 'Antiparasitario para equinos', 'LOTE-022', 'Tabletas', '250 mg', 'Antiparasitario', 300, 30, '2026-02-10', 3.50, 1);

-- Otros Medicamentos para Equinos
CALL spu_medicamentos_registrar('Betametasona', 'Corticosteroide para equinos', 'LOTE-023', 'Inyectable', '4 mg/ml', 'Corticosteroide', 80, 8, '2025-08-25', 19.00, 1);
CALL spu_medicamentos_registrar('Sulfadiazina de plata', 'Antibacteriano para heridas de equinos', 'LOTE-024', 'Crema', '1%', 'Antibacteriano', 150, 15, '2024-12-01', 10.00, 1);
CALL spu_medicamentos_registrar('Rifampicina', 'Antibiótico para infecciones severas en equinos', 'LOTE-025', 'Cápsulas', '300 mg', 'Antibiótico', 120, 12, '2026-03-01', 20.00, 1);
CALL spu_medicamentos_registrar('Colchicina', 'Tratamiento de laminitis', 'LOTE-026', 'Tabletas', '500 µg', 'Antiinflamatorio', 50, 5, '2026-01-15', 35.00, 1);
CALL spu_medicamentos_registrar('Vitamina E', 'Suplemento antioxidante para equinos', 'LOTE-027', 'Cápsulas', '500 IU', 'Suplemento', 300, 30, '2025-05-30', 12.00, 1);
CALL spu_medicamentos_registrar('Vitamina A', 'Suplemento para el crecimiento óseo', 'LOTE-028', 'Tabletas', '25,000 IU', 'Suplemento', 200, 20, '2026-02-01', 8.50, 1);
CALL spu_medicamentos_registrar('Biotina', 'Suplemento para el crecimiento del casco', 'LOTE-029', 'Polvo', '15 mg', 'Suplemento', 400, 40, '2025-04-10', 7.00, 1);
CALL spu_medicamentos_registrar('Lisina', 'Suplemento de aminoácidos para equinos', 'LOTE-030', 'Polvo', '50 mg', 'Suplemento', 350, 35, '2026-01-20', 9.50, 1);
CALL spu_medicamentos_registrar('Tiamina', 'Vitamina del complejo B para equinos', 'LOTE-031', 'Inyectable', '100 mg/ml', 'Vitamina', 200, 20, '2025-07-15', 5.00, 1);

-- Insertar medicamentos relacionados con la reproducción y mejora del rendimiento reproductivo en equinos.

CALL spu_medicamentos_registrar('Sildenafil', 'Mejora el flujo sanguíneo en equinos', 'LOTE-001', 'Tabletas', '50mg', 'Reproductivo', 100, 10, '2025-12-31', 35.50, 1);

CALL spu_medicamentos_registrar('Deslorelina', 'Inductor de ovulación en yeguas', 'LOTE-002', 'Inyectable', '1mg', 'Reproductivo', 50, 5, '2024-09-30', 75.00, 1);

CALL spu_medicamentos_registrar('Altrenogest', 'Supresión del celo en yeguas', 'LOTE-003', 'Solución oral', '50ml', 'Reproductivo', 60, 10, '2026-01-15', 45.00, 1);

CALL spu_medicamentos_registrar('Oxitocina', 'Inductor de contracciones uterinas en yeguas', 'LOTE-004', 'Inyectable', '10UI', 'Reproductivo', 200, 20, '2024-06-25', 12.00, 1);

CALL spu_medicamentos_registrar('GnRH', 'Mejora la fertilidad en yeguas y padrillos', 'LOTE-005', 'Inyectable', '10mg', 'Reproductivo', 75, 15, '2025-03-10', 85.00, 1);

CALL spu_medicamentos_registrar('HCG', 'Inductor de ovulación y mejora de fertilidad', 'LOTE-006', 'Inyectable', '5000UI', 'Reproductivo', 40, 5, '2024-08-22', 90.00, 1);

CALL spu_medicamentos_registrar('Dinoprost', 'Sincronización del celo en yeguas', 'LOTE-007', 'Inyectable', '25mg', 'Reproductivo', 30, 5, '2025-11-02', 40.50, 1);

CALL spu_medicamentos_registrar('Suplemento Vitamínico Reproductivo', 'Suplemento para mejorar fertilidad', 'LOTE-008', 'Polvo', '200g', 'Suplemento', 100, 20, '2025-05-15', 30.00, 1);

CALL spu_medicamentos_registrar('Cloprostenol', 'Prostaglandina para inducción del parto', 'LOTE-009', 'Inyectable', '250mcg', 'Reproductivo', 80, 10, '2024-07-01', 60.00, 1);

CALL spu_medicamentos_registrar('Progesterona', 'Regulación hormonal en yeguas', 'LOTE-010', 'Inyectable', '200mg', 'Reproductivo', 50, 10, '2024-12-15', 70.00, 1);



DELETE FROM HistorialMovimientosMedicamentos WHERE idMedicamento IN (SELECT idMedicamento FROM Medicamentos);
DELETE FROM Medicamentos;


