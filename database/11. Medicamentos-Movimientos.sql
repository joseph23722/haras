-- Procedimiento para registrar un nuevo historial médico de un equino-------------------------------------------------------------------------------------------------
DELIMITER $$
CREATE PROCEDURE spu_historial_medico_registrar(
    IN _idEquino INT,
    IN _idUsuario INT,
    IN _fecha DATE,
    IN _diagnostico TEXT,
    IN _tratamiento TEXT,
    IN _observaciones TEXT,
    IN _recomendaciones TEXT
)
BEGIN
    INSERT INTO HistorialMedico (
        idEquino, 
        idUsuario, 
        fecha, 
        diagnostico, 
        tratamiento, 
        observaciones, 
        recomendaciones
    ) 
    VALUES (
        _idEquino, 
        _idUsuario, 
        _fecha, 
        _diagnostico, 
        _tratamiento, 
        _observaciones, 
        _recomendaciones
    );
END $$
DELIMITER ;

-- lista equinos por tipo en medicamento ----------------------------------------------------------------------------------
DELIMITER $$
CREATE PROCEDURE spu_listar_equinos_para_medicamento (
    IN _tipoEquino ENUM('Yegua', 'Padrillo', 'Potrillo', 'Potranca', 'Recién nacido', 'Destete')
)
BEGIN
    SELECT 
        e.idEquino,
        e.nombreEquino
    FROM 
        Equinos e
    INNER JOIN 
        TipoEquinos te ON e.idTipoEquino = te.idTipoEquino
    WHERE 
        te.tipoEquino = _tipoEquino;
END $$
DELIMITER ;

-- Procedimiento para registrar la entrada y administración de medicamentos---------------------------------------------------------------------------------------------------------
DELIMITER $$
CREATE PROCEDURE spu_medicamentos_registrar(
    IN _nombreMedicamento VARCHAR(100),
    IN _cantidad INT, -- Cambiado a INT
    IN _caducidad DATE,
    IN _precioUnitario DECIMAL(10,2),
    IN _idTipomovimiento INT,
    IN _idUsuario INT
)
BEGIN
    DECLARE _exists INT DEFAULT 0;

    SELECT COUNT(*) INTO _exists 
    FROM Medicamentos
    WHERE LOWER(nombreMedicamento) = LOWER(_nombreMedicamento);

    IF _exists > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El medicamento ya existe en el inventario.';
    ELSE
        INSERT INTO Medicamentos (
            nombreMedicamento, 
            cantidad, 
            caducidad, 
            precioUnitario, 
            idTipomovimiento, 
            idUsuario
        ) 
        VALUES (
            _nombreMedicamento, 
            _cantidad, 
            _caducidad, 
            _precioUnitario, 
            _idTipomovimiento, 
            _idUsuario
        );
    END IF;
END $$
DELIMITER ;

-- Procedimiento Entrada y Salida de Medicamentos-----------------------------------------------------------------------------------
DELIMITER $$
CREATE PROCEDURE spu_medicamentos_movimiento(
    IN _nombreMedicamento VARCHAR(100),
    IN _cantidad INT, -- Cambiado a INT
    IN _idTipomovimiento INT
)
BEGIN
    DECLARE _currentCantidad INT; -- Cambiado a INT
    DECLARE _newCantidad INT; -- Cambiado a INT
    
    -- Obtener la cantidad actual del medicamento
    SELECT cantidad INTO _currentCantidad
    FROM Medicamentos
    WHERE nombreMedicamento = _nombreMedicamento
    LIMIT 1;
    
    -- Verificar si el medicamento existe
    IF _currentCantidad IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El medicamento no existe.';
    ELSE
        -- Calcular la nueva cantidad basada en el tipo de movimiento
        IF _idTipomovimiento = 1 THEN -- Entrada
            SET _newCantidad = _currentCantidad + _cantidad;
        ELSEIF _idTipomovimiento = 2 THEN -- Salida
            IF _currentCantidad >= _cantidad THEN
                SET _newCantidad = _currentCantidad - _cantidad;
            ELSE
                SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'Stock insuficiente para realizar la salida.';
            END IF;
        ELSE
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Tipo de movimiento no válido.';
        END IF;

        -- Actualizar la cantidad del medicamento
        UPDATE Medicamentos
        SET cantidad = _newCantidad
        WHERE nombreMedicamento = _nombreMedicamento;
    END IF;
END $$
DELIMITER ;