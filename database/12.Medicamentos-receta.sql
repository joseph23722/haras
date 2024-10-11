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
CREATE PROCEDURE spu_listar_medicamentosMedis()
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


