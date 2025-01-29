DROP PROCEDURE IF EXISTS `spu_equino_registrar`;

CREATE PROCEDURE `spu_equino_registrar`(
    IN _nombreEquino VARCHAR(100),
    IN _fechaNacimiento DATE,
    IN _sexo ENUM('Macho', 'Hembra'),
    IN _detalles TEXT,
    IN _idTipoEquino INT,
    IN _idPropietario INT,
    IN _pesokg DECIMAL(5,1),
    IN _idNacionalidad INT,
    IN _public_id VARCHAR(255),  -- Añadir el public_id de la imagen
    IN _fechaentrada DATE,      -- Fecha de entrada: Estadía
    IN _fechasalida DATE        -- Fecha de salida: Estadía
)
BEGIN
    DECLARE _errorMsg VARCHAR(255);
    DECLARE _edadDias INT;
    DECLARE _idEquino INT;
    DECLARE _idEstadoMonta INT;

    -- Calcular la edad en días
    SET _edadDias = TIMESTAMPDIFF(DAY, _fechaNacimiento, CURDATE());

    -- Validaciones de fecha y propietario
    IF _fechaNacimiento > CURDATE() THEN
        SET _errorMsg = 'Error: La fecha de nacimiento no puede ser posterior a la fecha actual.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
    END IF;

    IF _idPropietario IS NOT NULL AND NOT EXISTS (SELECT * FROM Propietarios WHERE idPropietario = _idPropietario) THEN
        SET _errorMsg = 'Error: El propietario no existe.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
    END IF;

    IF EXISTS (SELECT * FROM Equinos WHERE nombreEquino = _nombreEquino) THEN
        SET _errorMsg = 'Error: Ya existe un equino con ese nombre.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
    END IF;

    -- Validaciones de edad y tipo de equino
    IF _idPropietario IS NULL THEN
        -- Recién nacido (<= 180 días)
        IF _edadDias <= 180 THEN
            IF _idTipoEquino NOT IN (5) THEN
                SET _errorMsg = 'Error: Un equino recién nacido debe ser registrado como tipo "Recién nacido".';
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
            END IF;
        -- Potrillo o potranca (<= 730 días)
        ELSEIF _edadDias > 180 AND _edadDias <= 730 THEN
            IF _sexo = 'Macho' AND _idTipoEquino != 4 THEN
                SET _errorMsg = 'Error: Un macho de esta edad debe ser registrado como potrillo.';
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
            END IF;
            IF _sexo = 'Hembra' AND _idTipoEquino != 3 THEN
                SET _errorMsg = 'Error: Una hembra de esta edad debe ser registrada como potranca.';
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
            END IF;
        -- Equinos mayores de 730 días
        ELSEIF _edadDias > 730 THEN
            IF _sexo = 'Macho' AND _idTipoEquino NOT IN (2, 4) THEN
                SET _errorMsg = 'Error: Un macho mayor de 730 días debe ser registrado como padrillo o potrillo.';
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
            END IF;
            IF _sexo = 'Hembra' AND _idTipoEquino NOT IN (1, 3) THEN
                SET _errorMsg = 'Error: Una hembra mayor de 730 días debe ser registrada como yegua o potranca.';
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
            END IF;
        END IF;
    END IF;
    
    IF NOT EXISTS (SELECT 1 FROM nacionalidades WHERE idNacionalidad = _idNacionalidad) THEN
        SET _errorMsg = 'Error: La nacionalidad seleccionada no existe.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
    END IF;
    
    -- Validar fechas de entrada y salida si no son NULL
    IF _fechaEntrada IS NOT NULL AND _fechaSalida IS NOT NULL THEN
        IF _fechaEntrada > _fechaSalida THEN
            SET _errorMsg = 'Error: La fecha de entrada no puede ser mayor a la fecha de salida.';
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
        END IF;
    END IF;

    SET _idEstadoMonta = CASE 
        WHEN _sexo = 'Macho' AND _idTipoEquino = 2 THEN (SELECT idEstadoMonta FROM EstadoMonta WHERE genero = 'Macho' AND nombreEstado = 'Inactivo' LIMIT 1)
        WHEN _sexo = 'Hembra' AND _idTipoEquino = 1 THEN (SELECT idEstadoMonta FROM EstadoMonta WHERE genero = 'Hembra' AND nombreEstado = 'S/S' LIMIT 1)
        ELSE NULL
    END;

    -- INSERT con estado "Vivo"
    INSERT INTO Equinos (
        nombreEquino, 
        fechaNacimiento, 
        sexo, 
        idTipoEquino, 
        detalles, 
        idPropietario,
        pesokg,
        idNacionalidad,
        idEstadoMonta,
        fotografia,       -- Aquí guardaremos el public_id
        estado,
        fechaentrada,
        fechasalida
    ) 
    VALUES (
        _nombreEquino, 
        _fechaNacimiento, 
        _sexo, 
        _idTipoEquino, 
        _detalles, 
        _idPropietario,
        _pesokg,
        _idNacionalidad,
        _idEstadoMonta,
        _public_id,       -- Guardar el public_id en la columna fotografia
        1,  -- Estado "Vivo" (1)
        _fechaentrada,
        _fechasalida
    );

    -- Obtener el ID del equino recién insertado
    SET _idEquino = LAST_INSERT_ID();
    -- Retornar el ID del equino registrado
    SELECT _idEquino AS idEquino;
END; 

DROP PROCEDURE IF EXISTS `spu_buscar_nacionalidad`;

CREATE PROCEDURE `spu_buscar_nacionalidad`(IN _nacionalidad VARCHAR(255))
BEGIN
    SELECT idNacionalidad, nacionalidad
    FROM nacionalidades
    WHERE nacionalidad LIKE CONCAT('%', _nacionalidad, '%');
END ;

DROP PROCEDURE IF EXISTS `registrarServicio`;

CREATE PROCEDURE registrarServicio(
    IN p_idEquinoMacho INT,
    IN p_idEquinoHembra INT,
    IN p_idPropietario INT,
    IN p_idEquinoExterno INT,
    IN p_fechaServicio DATE,
    IN p_tipoServicio ENUM('propio', 'mixto'),
    IN p_detalles TEXT,
    IN p_idMedicamento INT,
    IN p_horaEntrada TIME,
    IN p_horaSalida TIME,
    IN p_costoServicio DECIMAL(10, 2)
)
BEGIN
    DECLARE v_mensajeError VARCHAR(255);
    DECLARE v_idEstadoServida INT;
    DECLARE v_idEstadoActivo INT;
    DECLARE v_idEstadoSS INT;
    DECLARE v_horaActual TIME;

    -- Obtener los ID de estados correspondientes
    SELECT idEstadoMonta INTO v_idEstadoServida FROM EstadoMonta WHERE genero = 'Hembra' AND nombreEstado = 'Servida' LIMIT 1;
    SELECT idEstadoMonta INTO v_idEstadoActivo FROM EstadoMonta WHERE genero = 'Macho' AND nombreEstado = 'Activo' LIMIT 1;
    SELECT idEstadoMonta INTO v_idEstadoSS FROM EstadoMonta WHERE genero = 'Hembra' AND nombreEstado = 'S/S' LIMIT 1;

    -- Verificar si la fecha de servicio es válida
    IF p_fechaServicio > CURDATE() THEN
        SET v_mensajeError = 'Error: La fecha de servicio no puede ser mayor que la fecha actual.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
    END IF;

    -- Si la fecha de servicio es hoy, verificar que la hora de entrada y salida no sean mayores a la hora actual
    IF p_fechaServicio = CURDATE() THEN
        SET v_horaActual = CURTIME();

        -- Verificar que la hora de entrada no sea mayor que la hora de salida
        IF p_horaEntrada > p_horaSalida THEN
            SET v_mensajeError = 'Error: La hora de entrada no puede ser mayor que la hora de salida.';
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
        END IF;

        -- Verificar que la hora de entrada y la hora de salida no sean mayores que la hora actual
        IF p_horaEntrada > v_horaActual OR p_horaSalida > v_horaActual THEN
            SET v_mensajeError = 'Error: La hora de entrada y la hora de salida no pueden ser mayores a la hora actual.';
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
        END IF;
    END IF;

    -- Validar duplicados para yeguas propias
    IF p_idEquinoHembra IS NOT NULL THEN
        IF EXISTS (
            SELECT 1
            FROM Servicios
            WHERE idEquinoHembra = p_idEquinoHembra
              AND fechaServicio = p_fechaServicio
        ) THEN
            SET v_mensajeError = 'Error: La yegua propia ya recibió un servicio en esta fecha.';
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
        END IF;
    END IF;

    -- Validar duplicados para yeguas externas
    IF p_idEquinoExterno IS NOT NULL THEN
        IF EXISTS (
            SELECT 1
            FROM Servicios
            WHERE idEquinoExterno = p_idEquinoExterno
              AND fechaServicio = p_fechaServicio
        ) THEN
            SET v_mensajeError = 'Error: La yegua externa ya recibió un servicio en esta fecha.';
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
        END IF;
    END IF;

    -- Registrar el servicio
    IF p_tipoServicio = 'propio' THEN
        INSERT INTO Servicios (
            idEquinoMacho, idEquinoHembra, fechaServicio, tipoServicio, detalles, idMedicamento, horaEntrada, horaSalida, idPropietario, costoServicio
        ) VALUES (
            p_idEquinoMacho, p_idEquinoHembra, p_fechaServicio, p_tipoServicio, p_detalles, p_idMedicamento, p_horaEntrada, p_horaSalida, NULL, p_costoServicio
        );

        -- Cambiar estado del macho a "Activo"
        UPDATE Equinos
        SET idEstadoMonta = v_idEstadoActivo
        WHERE idEquino = p_idEquinoMacho;

        -- Cambiar estado de la yegua propia a "Servida"
        UPDATE Equinos
        SET idEstadoMonta = v_idEstadoServida
        WHERE idEquino = p_idEquinoHembra;

    ELSEIF p_tipoServicio = 'mixto' THEN
        INSERT INTO Servicios (
            idEquinoMacho, idEquinoHembra, idEquinoExterno, fechaServicio, tipoServicio, detalles, idMedicamento, horaEntrada, horaSalida, idPropietario, costoServicio
        ) VALUES (
            p_idEquinoMacho, p_idEquinoHembra, p_idEquinoExterno, p_fechaServicio, p_tipoServicio, p_detalles, p_idMedicamento, p_horaEntrada, p_horaSalida, p_idPropietario, p_costoServicio
        );

        -- Cambiar estado del macho a "Activo"
        UPDATE Equinos
        SET idEstadoMonta = v_idEstadoActivo
        WHERE idEquino = p_idEquinoMacho;

        -- Cambiar estado de la yegua propia a "Servida" si aplica
        IF p_idEquinoHembra IS NOT NULL THEN
            UPDATE Equinos
            SET idEstadoMonta = v_idEstadoServida
            WHERE idEquino = p_idEquinoHembra;
        END IF;

        -- Cambiar estado de la yegua externa a "Servida" si aplica
        IF p_idEquinoExterno IS NOT NULL THEN
            UPDATE Equinos
            SET idEstadoMonta = v_idEstadoServida
            WHERE idEquino = p_idEquinoExterno;
        END IF;
    END IF;

    -- Actualizar el estado de todas las yeguas no servidas recientemente a "S/S"
    UPDATE Equinos
    SET idEstadoMonta = v_idEstadoSS
    WHERE sexo = 'Hembra'
      AND idEquino NOT IN (
          SELECT idEquinoHembra
          FROM Servicios
          WHERE fechaServicio = p_fechaServicio
      );

END ;

-- registrar dosis aplicada
DROP PROCEDURE IF EXISTS `spu_registrar_dosis_aplicada`;

CREATE PROCEDURE spu_registrar_dosis_aplicada(
    IN _idMedicamento INT, -- Medicamento utilizado
    IN _idEquino INT, -- Equino al que se aplica la dosis
    IN _cantidadAplicada DECIMAL(10, 2), -- Dosis utilizada
    IN _idUsuario INT, -- Veterinario que realiza la aplicación
    IN _unidadAplicada VARCHAR(50), -- Unidad utilizada para verificar
    IN _fechaAplicacion DATE -- Fecha de la aplicación (anteriormente fecha de servicio)
)
BEGIN
    DECLARE _stockActual DECIMAL(10, 2); -- Stock actual del medicamento
    DECLARE _unidadCompleta DECIMAL(10, 2); -- Cantidad por unidad en stock (en mg)
    DECLARE _unidadBase VARCHAR(50); -- Unidad real del medicamento desde la base
    DECLARE _cantidadRestanteAcumulada DECIMAL(10, 2); -- Cantidad restante acumulada de dosis anteriores
    DECLARE _cantidadTotal DECIMAL(10, 2); -- Total acumulado con la nueva dosis
    DECLARE _nuevasUnidades INT; -- Número de unidades completas que se pueden descontar
    DECLARE _errorMessage VARCHAR(255); -- Mensaje de error

    -- Obtener la cantidad por unidad, stock actual y unidad base del medicamento
    SELECT c.dosis, m.cantidad_stock, u.unidad
    INTO _unidadCompleta, _stockActual, _unidadBase
    FROM Medicamentos m
    JOIN CombinacionesMedicamentos c ON m.idCombinacion = c.idCombinacion
    JOIN UnidadesMedida u ON c.idUnidad = u.idUnidad
    WHERE m.idMedicamento = _idMedicamento
    FOR UPDATE;

    -- Verificar que la unidad proporcionada coincide con la unidad base
    IF _unidadAplicada != _unidadBase THEN
        SET _errorMessage = CONCAT('Unidad no válida. Se esperaba: ', _unidadBase);
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMessage;
    END IF;

    -- Calcular la cantidad restante acumulada de dosis anteriores
    SELECT COALESCE(cantidadRestante, 0)
    INTO _cantidadRestanteAcumulada
    FROM HistorialDosisAplicadas
    WHERE idMedicamento = _idMedicamento
    ORDER BY idDosis DESC
    LIMIT 1;

    -- Sumar la nueva cantidad aplicada al acumulado
    SET _cantidadTotal = _cantidadRestanteAcumulada + _cantidadAplicada;

    -- Calcular cuántas unidades completas se pueden descontar
    SET _nuevasUnidades = FLOOR(_cantidadTotal / _unidadCompleta);

    -- Verificar que el stock es suficiente para descontar las unidades completas
    IF _nuevasUnidades > _stockActual THEN
        SET _errorMessage = 'Stock insuficiente para completar la operación.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMessage;
    END IF;

    -- Calcular la cantidad restante después de completar las unidades
    SET _cantidadRestanteAcumulada = MOD(_cantidadTotal, _unidadCompleta);

    -- Registrar la dosis aplicada en el historial, usando la fecha de aplicación proporcionada
    INSERT INTO HistorialDosisAplicadas (idMedicamento, idEquino, cantidadAplicada, cantidadRestante, fechaAplicacion, idUsuario)
    VALUES (_idMedicamento, _idEquino, _cantidadAplicada, _cantidadRestanteAcumulada, _fechaAplicacion, _idUsuario);

    -- Actualizar el stock general si se completaron unidades completas
    IF _nuevasUnidades > 0 THEN
        UPDATE Medicamentos
        SET cantidad_stock = cantidad_stock - _nuevasUnidades,
            ultima_modificacion = NOW()
        WHERE idMedicamento = _idMedicamento;
    END IF;

    -- Confirmar la transacción
    COMMIT;
END ;

-- Obtener Historial Dosis Aplicadas  -- crear vista 
DROP PROCEDURE IF EXISTS `spu_ObtenerHistorialDosisAplicadas`;

CREATE PROCEDURE spu_ObtenerHistorialDosisAplicadas()
BEGIN
    SELECT 
        m.nombreMedicamento AS Medicamento,                     -- Nombre del medicamento
        CONCAT(h.cantidadAplicada, ' ', u.unidad) AS DosisAplicada, -- Dosis aplicada con unidad desde la base
        CONCAT(h.cantidadRestante, ' ', u.unidad) AS StockRestante, -- Stock restante después de la aplicación con unidad
        CONCAT((h.cantidadRestante + h.cantidadAplicada), ' ', u.unidad) AS StockAntes, -- Stock antes de la aplicación con unidad
        m.cantidad_stock AS StockActual,                        -- Stock actual disponible en unidades completas
        m.estado AS EstadoMedicamento,                         -- Estado del medicamento (Disponible, Agotado, etc.)
        h.fechaAplicacion AS FechaAplicación,                  -- Fecha en la que se aplicó la dosis
        e.nombreEquino AS NombreDelEquino,                     -- Nombre del equino al que se aplicó la dosis
        CONCAT(p.nombres, ' ', p.apellidos) AS NombreUsuario    -- Nombre completo del usuario que realizó la aplicación
    FROM 
        Medicamentos m
    JOIN 
        CombinacionesMedicamentos c ON m.idCombinacion = c.idCombinacion
    JOIN 
        TiposMedicamentos t ON c.idTipo = t.idTipo
    JOIN
        UnidadesMedida u ON c.idUnidad = u.idUnidad            -- Relación con unidades de medida
    JOIN
        LotesMedicamento lm ON m.idLoteMedicamento = lm.idLoteMedicamento -- Relación con lotes
    JOIN
        HistorialDosisAplicadas h ON m.idMedicamento = h.idMedicamento    -- Relación con historial de dosis
    JOIN
        Equinos e ON h.idEquino = e.idEquino                              -- Relación con equinos
    JOIN
        Usuarios usr ON h.idUsuario = usr.idUsuario                       -- Relación con usuarios
    JOIN
        Personal p ON usr.idPersonal = p.idPersonal                       -- Relación con personal
    ORDER BY 
        h.fechaAplicacion DESC, m.nombreMedicamento ASC;
END ;


DROP PROCEDURE IF EXISTS `spu_contar_equinos_por_categoria`;

CREATE PROCEDURE spu_contar_equinos_por_categoria()
BEGIN
    -- Consulta directa para obtener el representante único y la cantidad de equinos por categoría
    SELECT 
        CASE 
            WHEN te.tipoEquino = 'Yegua' AND em.nombreEstado = 'S/S' THEN 'Yegua Vacía'
            WHEN te.tipoEquino = 'Yegua' AND em.nombreEstado = 'Preñada' THEN 'Yegua Preñada'
            WHEN te.tipoEquino = 'Yegua' AND em.nombreEstado = 'Con Cria' THEN 'Yegua Con Cria'
            WHEN te.tipoEquino = 'Padrillo' AND em.nombreEstado = 'Activo' THEN 'Padrillo Activo'
            WHEN te.tipoEquino = 'Padrillo' AND em.nombreEstado = 'Inactivo' THEN 'Padrillo Inactivo'
            WHEN te.tipoEquino = 'Potranca' THEN 'Potranca'
            WHEN te.tipoEquino = 'Potrillo' THEN 'Potrillo'
        END AS Categoria,
        COUNT(DISTINCT e.idEquino) AS Cantidad,
        -- Selección de un representante único para la categoría (primer equino encontrado)
        MIN(e.idEquino) AS idEquino
    FROM 
        Equinos e
    JOIN 
        TipoEquinos te ON e.idTipoEquino = te.idTipoEquino
    LEFT JOIN 
        EstadoMonta em ON e.idEstadoMonta = em.idEstadoMonta
    WHERE 
        e.estado = 1  -- Solo los equinos vivos
    AND (
        (te.tipoEquino = 'Yegua' AND em.nombreEstado IN ('S/S', 'Preñada', 'Con Cria'))
        OR (te.tipoEquino = 'Padrillo' AND em.nombreEstado IN ('Activo', 'Inactivo'))
        OR te.tipoEquino IN ('Potranca', 'Potrillo')
    )
    GROUP BY 
        Categoria
    ORDER BY 
        Categoria;

END ;

-- editar version 2 - funcionando:
DROP PROCEDURE IF EXISTS spu_equino_editar;

CREATE PROCEDURE spu_equino_editar(
    IN _idEquino INT,
    IN _idPropietario INT,
    IN _pesokg DECIMAL(5,1),
    IN _idEstadoMonta VARCHAR(50),
    IN _estado ENUM('Vivo', 'Muerto'),
    IN _fechaEntrada DATE,
    IN _fechaSalida DATE
)
BEGIN
    DECLARE _errorMsg VARCHAR(255);

    -- Verificar si el equino existe
    IF NOT EXISTS (SELECT 1 FROM Equinos WHERE idEquino = _idEquino) THEN
        SET _errorMsg = 'Error: No existe un equino con el ID proporcionado.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
    END IF;

    -- Iniciar una transacción
    START TRANSACTION;

    -- Actualizar solo los campos que no sean NULL o vacíos
    UPDATE Equinos
    SET 
        idPropietario = COALESCE(NULLIF(_idPropietario, 0), idPropietario),
        pesokg = COALESCE(NULLIF(_pesokg, 0), pesokg),
        idEstadoMonta = COALESCE(NULLIF(_idEstadoMonta, ''), idEstadoMonta),
        estado = COALESCE(NULLIF(_estado, ''), estado),
        -- Actualizar las fechas solo si no son NULL
        fechaentrada = COALESCE(NULLIF(_fechaEntrada, '0000-00-00'), fechaentrada),
        fechasalida = COALESCE(NULLIF(_fechaSalida, '0000-00-00'), fechasalida)
    WHERE idEquino = _idEquino;

    -- Validar si se actualizó correctamente
    IF ROW_COUNT() = 0 THEN
        SET _errorMsg = 'Error: No se realizaron cambios en el registro.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
    END IF;

    -- Confirmar los cambios
    COMMIT;

END;