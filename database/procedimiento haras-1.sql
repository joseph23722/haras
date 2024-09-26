-- procedimientos 

-- login -------------------------------------------------------------------------------------------
DELIMITER $$
CREATE PROCEDURE spu_usuarios_login(IN _correo VARCHAR(100))
BEGIN
    SELECT 
        USU.idUsuario,
        PER.apellidos,
        PER.nombres,
        USU.clave,
        USU.idRol  -- Asegúrate de incluir idRol en la selección
    FROM 
        Usuarios USU
    INNER JOIN 
        Personal PER ON PER.idPersonal = USU.idPersonal
    WHERE 
        USU.correo = _correo;
END $$
DELIMITER ;

-- registrar personal - no uso - prueba---------------------------------------------------------------------------------
DELIMITER $$
CREATE PROCEDURE spu_personal_registrar(
    IN _apellidos VARCHAR(100),       -- Apellidos del personal
    IN _nombres VARCHAR(100),         -- Nombres del personal
    IN _nrodocumento VARCHAR(50),     -- Número de documento del personal
    IN _direccion VARCHAR(255),       -- Dirección del personal
    IN _tipodoc VARCHAR(20),          -- Tipo de documento (DNI, pasaporte, etc.)
    IN _numeroHijos INT,              -- Número de hijos del personal
    IN _fechaIngreso DATETIME         -- Fecha de ingreso del personal (puede ser NULL)
)
BEGIN
    -- Insertar un nuevo registro en la tabla 'Personal'
    INSERT INTO Personal (apellidos, nombres, nrodocumento, direccion, tipodoc, numeroHijos, fechaIngreso)
    VALUES (_apellidos, _nombres, _nrodocumento, _direccion, _tipodoc, _numeroHijos, IFNULL(_fechaIngreso, NULL));
    
    -- Devolver el ID del personal recién insertado
    SELECT LAST_INSERT_ID() AS idPersonal;
END $$
DELIMITER ;

CALL spu_personal_registrar('Perez', 'Juan', '12345678', 'Calle Falsa 123', 'DNI', 2, NULL);

-- --------------------------------------------------------------------------------------------------------------------------


-- Procedimiento combinado para registrar persona y usuario (en uso)-------------------------------------------------------------------------------------------------------------------
DELIMITER $$
CREATE PROCEDURE spu_registrar_persona_usuario(
    IN _apellidos VARCHAR(100),       -- Apellidos del personal
    IN _nombres VARCHAR(100),         -- Nombres del personal
    IN _nrodocumento VARCHAR(50),     -- Número de documento del personal
    IN _direccion VARCHAR(255),       -- Dirección del personal
    IN _tipodoc VARCHAR(20),          -- Tipo de documento (DNI, pasaporte, etc.)
    IN _numeroHijos INT,              -- Número de hijos del personal
    IN _fechaIngreso DATETIME,        -- Fecha de ingreso del personal (puede ser NULL)
    IN _correo VARCHAR(50),           -- Correo electrónico del usuario
    IN _clave VARCHAR(100),           -- Clave del usuario (encriptada)
    IN _idRol INT                     -- ID del rol del usuario
)
BEGIN
    -- Iniciar una transacción
    START TRANSACTION;
    
    -- Insertar en la tabla 'Personal'
    INSERT INTO Personal (apellidos, nombres, nrodocumento, direccion, tipodoc, numeroHijos, fechaIngreso)
    VALUES (_apellidos, _nombres, _nrodocumento, _direccion, _tipodoc, _numeroHijos, IFNULL(_fechaIngreso, NULL));
    
    -- Obtener el ID del personal recién insertado
    SET @idPersonal = LAST_INSERT_ID();
    
    -- Insertar en la tabla 'Usuarios' con el ID de la persona
    INSERT INTO Usuarios (idPersonal, idRol, correo, clave)
    VALUES (@idPersonal, _idRol, _correo, _clave);
    
    -- Confirmar la transacción
    COMMIT;
    
    -- Devolver el ID del usuario recién insertado
    SELECT @idPersonal AS idPersonal;
END $$
DELIMITER ;

-- procedimiento para buscar por Dni ---------------------------------------------------------------------------
DELIMITER $$
CREATE PROCEDURE spu_personal_buscar_dni(
    IN _nrodocumento VARCHAR(50) -- Número de documento a buscar
)
BEGIN
    -- Seleccionar los datos de la persona y del usuario asociado según el número de documento proporcionado
    SELECT 
        p.idPersonal,
        p.apellidos,
        p.nombres,
        p.nrodocumento,
        p.direccion,
        p.tipodoc,
        p.numeroHijos,
        p.fechaIngreso,
        u.correo,          -- Incluir correo del usuario
        u.clave            -- Incluir clave del usuario
    FROM 
        Personal p
    LEFT JOIN 
        Usuarios u ON p.idPersonal = u.idPersonal
    WHERE 
        p.nrodocumento = _nrodocumento;
END $$
DELIMITER ;


-- Procedimiento para registrar usuarios en la tabla 'Usuarios'--------------------------------------------------------------
DELIMITER $$
CREATE PROCEDURE spu_usuarios_registrar(
    IN _idPersonal INT,             -- ID del personal asociado al usuario
    IN _idRol INT,                  -- ID del rol del usuario
    IN _correo VARCHAR(50),         -- Correo electrónico del usuario
    IN _clave VARCHAR(100)          -- Clave del usuario
)
BEGIN
    -- Insertar un nuevo registro en la tabla 'Usuarios'
    INSERT INTO Usuarios (idPersonal, idRol, correo, clave)
    VALUES (_idPersonal, _idRol, _correo, _clave);

    -- Devolver el ID del usuario recién insertado
    SELECT LAST_INSERT_ID() AS idUsuario;
END $$
DELIMITER ;

-- Procedimiento para listar equinos por tipo (yegua o padrillo)----------------------------------------------------------------------------------------------
DELIMITER $$
CREATE PROCEDURE spu_listar_equinos_por_tipo (
    IN _tipoEquino ENUM('yegua', 'padrillo')  -- Tipo de equino: yegua o padrillo
)
BEGIN
    -- Seleccionamos el ID y el nombre del equino según su tipo
    SELECT 
        e.idEquino,              -- ID del equino
        e.nombreEquino           -- Nombre del equino
    FROM 
        Equinos e
    INNER JOIN 
        TipoEquinos te ON e.idTipoEquino = te.idTipoEquino
    WHERE 
        te.tipoEquino = _tipoEquino;  -- Filtramos por el tipo de equino (yegua o padrillo)
END $$
DELIMITER ;

-- Procedimiento para obtener la lista de medicamentos con sus detalles (si existen)--------------------------------------------------------------------------------------------
DELIMITER $$
CREATE PROCEDURE spu_listar_medicamentos_con_detalles()
BEGIN
    -- Seleccionamos los medicamentos junto con los detalles si existen
    SELECT 
        m.idMedicamento,          -- ID del medicamento
        m.nombreMedicamento,      -- Nombre del medicamento
        dm.dosis,                 -- Dosis del medicamento (si está disponible)
        dm.fechaInicio,           -- Fecha de inicio del tratamiento (si está disponible)
        dm.fechaFin               -- Fecha de fin del tratamiento (si está disponible)
    FROM 
        Medicamentos m
    LEFT JOIN 
        DetalleMedicamentos dm ON m.idMedicamento = dm.idMedicamento;  -- Incluimos detalles si existen
END $$
DELIMITER ;



-- -------------------------------------------------------------------------------------------------------------------------------------
-- Procedimiento para registrar los alimentos  y manejar los movimintos entrada y salida 
DELIMITER $$

CREATE PROCEDURE spu_alimentos_nuevo(
    IN _idUsuario INT,
    IN _nombreAlimento VARCHAR(100),
    IN _tipoAlimento VARCHAR(50),
    IN _cantidad DECIMAL(10,2),
    IN _unidadMedida VARCHAR(10),
    IN _costo DECIMAL(10,2),
    IN _lote VARCHAR(50),
    IN _fechaCaducidad DATE,
    IN _fechaIngreso DATETIME
)
BEGIN
    DECLARE _exists INT DEFAULT 0;
    DECLARE _compra DECIMAL(10,2);

    -- Verificar que la cantidad y el costo sean válidos
    IF _cantidad <= 0 OR _costo <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La cantidad y el costo deben ser mayores que cero.';
    END IF;

    -- Convertir el nombre del alimento a minúsculas antes de la verificación
    SET _nombreAlimento = LOWER(_nombreAlimento);

    -- Calcular el costo total de la compra
    SET _compra = _cantidad * _costo;

    -- Verificar si el alimento ya existe para ese lote
    SELECT COUNT(*) INTO _exists 
    FROM Alimentos
    WHERE nombreAlimento = _nombreAlimento
      AND lote = _lote;

    -- Si el alimento ya existe, actualiza el stock
    IF _exists > 0 THEN
        UPDATE Alimentos
        SET cantidad = cantidad + _cantidad,
            stockFinal = stockFinal + _cantidad,
            fechaMovimiento = NOW()
        WHERE nombreAlimento = _nombreAlimento
          AND lote = _lote;
    ELSE
        -- Insertar un nuevo registro si es un alimento/lote nuevo
        INSERT INTO Alimentos (
            idUsuario, 
            nombreAlimento, 
            tipoAlimento, 
            cantidad, 
            unidadMedida, 
            costo, 
            lote, 
            fechaCaducidad, 
            idTipomovimiento, 
            stockFinal, 
            fechaIngreso, 
            fechaMovimiento, 
            compra
        ) 
        VALUES (
            _idUsuario, 
            _nombreAlimento, 
            _tipoAlimento,
            _cantidad, 
            _unidadMedida,
            _costo, 
            _lote, 
            _fechaCaducidad,
            1,  -- '1' indica una entrada
            _cantidad,  -- El stock inicial es igual a la cantidad ingresada
            _fechaIngreso,
            NOW(),  -- Registrar el momento del movimiento
            _compra  -- Costo total de la compra
        );
    END IF;
END $$

DELIMITER ;

-- ------------------------------------------------------------------------------------------------------------------------
-- Procedimiento Entrada y Salida de Alimentos -----------------------------------------------------------------------------------
DELIMITER $$

CREATE PROCEDURE spu_alimentos_movimiento(
    IN _idUsuario INT,
    IN _nombreAlimento VARCHAR(100),
    IN _tipoAlimento VARCHAR(50),
    IN _cantidad DECIMAL(10,2),
    IN _unidadMedida VARCHAR(10),
    IN _costo DECIMAL(10,2),
    IN _lote VARCHAR(50),
    IN _fechaCaducidad DATE,
    IN _idTipomovimiento INT,
    IN _idTipoEquino INT, -- Solo se usa en salidas
    IN _merma DECIMAL(10,2) -- Registro de la merma en salida
)
BEGIN
    -- Declaraciones de variables
    DECLARE _exists INT DEFAULT 0;
    DECLARE _currentStock DECIMAL(10,2);
    DECLARE _newStock DECIMAL(10,2);
    DECLARE _compra DECIMAL(10,2);
    DECLARE _idAlimento INT;
    DECLARE _stockMinimo DECIMAL(10,2);

    -- Verificar que la cantidad y el costo sean válidos
    IF _cantidad <= 0 OR _costo <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La cantidad y el costo deben ser mayores que cero.';
    END IF;

    -- Calcular el costo total de la compra
    SET _compra = _cantidad * _costo;

    -- Convertir el nombre del alimento a minúsculas
    SET _nombreAlimento = LOWER(_nombreAlimento);

    -- Verificar si el alimento ya existe con el mismo lote
    SELECT COUNT(*) INTO _exists 
    FROM Alimentos
    WHERE nombreAlimento = _nombreAlimento
    AND lote = _lote;

    -- Manejar el movimiento según sea entrada o salida
    IF _idTipomovimiento = 1 THEN -- Entrada
        IF _exists > 0 THEN
            -- Si el alimento con el lote ya existe, actualizar el stock
            SELECT idAlimento, stockFinal INTO _idAlimento, _currentStock
            FROM Alimentos
            WHERE nombreAlimento = _nombreAlimento AND lote = _lote;

            SET _newStock = _currentStock + _cantidad;

            -- Actualizar el stock final y fecha de ingreso
            UPDATE Alimentos
            SET stockFinal = _newStock, 
                fechaMovimiento = NOW(),
                fechaIngreso = NOW(),
                costo = _costo
            WHERE idAlimento = _idAlimento;
        ELSE
            -- Insertar un nuevo alimento en el inventario
            INSERT INTO Alimentos (
                idUsuario, 
                nombreAlimento, 
                tipoAlimento, 
                cantidad, 
                unidadMedida,
                costo, 
                lote, 
                fechaCaducidad,
                idTipomovimiento, 
                stockFinal, 
                fechaIngreso, 
                compra, 
                fechaMovimiento
            ) 
            VALUES (
                _idUsuario, 
                _nombreAlimento, 
                _tipoAlimento, 
                _cantidad, 
                _unidadMedida,
                _costo, 
                _lote, 
                _fechaCaducidad,
                _idTipomovimiento, 
                _cantidad, 
                NOW(), 
                _compra, 
                NOW()
            );
        END IF;

    ELSEIF _idTipomovimiento = 2 THEN -- Salida
        -- Verificar si el alimento y lote existen
        SELECT idAlimento, stockFinal, stockMinimo INTO _idAlimento, _currentStock, _stockMinimo
        FROM Alimentos
        WHERE nombreAlimento = _nombreAlimento
        AND lote = _lote
        ORDER BY fechaCaducidad ASC -- Priorizar el lote con caducidad más cercana
        LIMIT 1;

        IF _currentStock IS NULL THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'El alimento o el lote no existen.';
        ELSE
            -- Verificar si hay suficiente stock
            IF _currentStock >= (_cantidad + _merma) THEN
                SET _newStock = _currentStock - (_cantidad + _merma);

                -- Verificar si el stock cae por debajo del stock mínimo
                IF _newStock < _stockMinimo THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Alerta: El stock está por debajo del mínimo.';
                END IF;

                -- Validar que se proporcione idTipoEquino para las salidas
                IF _idTipoEquino IS NULL THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'idTipoEquino es obligatorio para las salidas.';
                END IF;

                -- Actualizar el stock y registrar la salida
                UPDATE Alimentos
                SET stockFinal = _newStock, 
                    idTipomovimiento = 2, 
                    idTipoEquino = _idTipoEquino, 
                    merma = _merma, 
                    fechaMovimiento = NOW()
                WHERE idAlimento = _idAlimento;

                -- Insertar en Historial de Movimientos
                INSERT INTO HistorialMovimientos (idAlimento, tipoMovimiento, cantidad, idUsuario, fechaMovimiento)
                VALUES (_idAlimento, 'Salida', _cantidad, _idUsuario, NOW());
            ELSE
                SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'Stock insuficiente para realizar la salida.';
            END IF;
        END IF;
    ELSE
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Tipo de movimiento no válido.';
    END IF;
END $$

DELIMITER ;


-- ------------------------------------------------------------------------------------------------------------------------
-- Procedimiento para Reporte de Alimentos Próximos a Caducar o con Stock Bajo-----------------------------------------
DELIMITER $$

CREATE PROCEDURE spu_reporte_inventario(
    IN _dias INT -- Días para revisar los alimentos que caducarán en los próximos _dias
)
BEGIN
    -- Alimentos que están por caducar en los próximos días
    SELECT nombreAlimento, lote, stockFinal, fechaCaducidad
    FROM Alimentos
    WHERE fechaCaducidad BETWEEN CURDATE() AND CURDATE() + INTERVAL _dias DAY;

    -- Alimentos cuyo stock está por debajo del mínimo
    SELECT nombreAlimento, lote, stockFinal, stockMinimo
    FROM Alimentos
    WHERE stockFinal < stockMinimo;
END $$

DELIMITER ;


-- ------------------------------------------------------------------------------------------------------------------------
-- Procedimiento para Verificar Alimentos Caducados-----------------------------------------

DELIMITER $$

CREATE PROCEDURE spu_verificar_caducidad()
BEGIN
    DECLARE _caducados INT;
    DECLARE _mensaje VARCHAR(255);

    -- Seleccionar todos los alimentos caducados
    SELECT COUNT(*) INTO _caducados 
    FROM Alimentos
    WHERE fechaCaducidad < CURDATE();
    
    IF _caducados > 0 THEN
        -- Crear el mensaje concatenado
        SET _mensaje = CONCAT('Hay ', _caducados, ' alimentos que han caducado.');
        
        -- Lanzar la señal con el mensaje concatenado
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = _mensaje;
    END IF;
END $$

DELIMITER ;


















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
    IN _tipoEquino ENUM('yegua', 'padrillo', 'potrillo', 'potranca')  -- Tipo de equino: yegua, padrillo, potrillo o potranca
)
BEGIN
    -- Seleccionamos el ID y el nombre del equino según su tipo
    SELECT 
        e.idEquino,              -- ID del equino
        e.nombreEquino           -- Nombre del equino
    FROM 
        Equinos e
    INNER JOIN 
        TipoEquinos te ON e.idTipoEquino = te.idTipoEquino
    WHERE 
        te.tipoEquino = _tipoEquino;  -- Filtramos por el tipo de equino
END $$
DELIMITER ;

CALL spu_listar_equinos_para_medicamento('yegua');
CALL spu_listar_equinos_para_medicamento('potranca');


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

    -- Verificar si el medicamento ya existe (independiente de mayúsculas/minúsculas)
    SELECT COUNT(*) INTO _exists 
    FROM Medicamentos
    WHERE LOWER(nombreMedicamento) = LOWER(_nombreMedicamento);

    IF _exists > 0 THEN
        -- Si el medicamento ya existe, lanzar un error
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El medicamento ya existe en el inventario.';
    ELSE
        -- Insertar un nuevo medicamento en el inventario
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

-- procedimientos faltantes---------------------------------------------------------------------------------------------------------------------------------
-- Procedimiento para registrar un nuevo entrenamiento realizado a un equino------------------------------------------
DELIMITER $$
CREATE PROCEDURE spu_entrenamientos_registrar(
    IN _idEquino INT,
    IN _fecha DATETIME,
    IN _tipoEntrenamiento VARCHAR(100),
    IN _duracion DECIMAL(5,2),
    IN _intensidad ENUM('baja', 'media', 'alta'),
    IN _comentarios TEXT
)
BEGIN
    INSERT INTO Entrenamientos (
        idEquino, 
        fecha, 
        tipoEntrenamiento, 
        duracion, 
        intensidad, 
        comentarios
    ) 
    VALUES (
        _idEquino, 
        _fecha, 
        _tipoEntrenamiento, 
        _duracion, 
        _intensidad, 
        _comentarios
    );
END $$
DELIMITER ;

-- Procedimiento para registrar la asistencia del personal------------------------------------------------------------------------------------------------------------------------------------------------
DELIMITER $$
CREATE PROCEDURE spu_asistencia_personal_registrar(
    IN _idPersonal INT,
    IN _fecha DATETIME,
    IN _horaEntrada TIME,
    IN _horaSalida TIME,
    IN _horasTrabajadas DECIMAL(5,2),
    IN _tipoJornada ENUM('completa', 'parcial'),
    IN _comentarios TEXT
)
BEGIN
    INSERT INTO AsistenciaPersonal (
        idPersonal, 
        fecha, 
        horaEntrada, 
        horaSalida, 
        horasTrabajadas, 
        tipoJornada, 
        comentarios
    ) 
    VALUES (
        _idPersonal, 
        _fecha, 
        _horaEntrada, 
        _horaSalida, 
        _horasTrabajadas, 
        _tipoJornada, 
        _comentarios
    );
END $$
DELIMITER ;


-- Procedimiento para registrar la rotación de campos---------------------------------------------------------------------------------------------------------------------------
DELIMITER $$
CREATE PROCEDURE spu_rotacion_campos_registrar(
    IN _idCampo INT,
    IN _idTipoRotacion INT,
    IN _fechaRotacion DATETIME,
    IN _estadoRotacion VARCHAR(50),
    IN _detalleRotacion TEXT
)
BEGIN
    INSERT INTO RotacionCampos (
        idCampo, 
        idTipoRotacion, 
        fechaRotacion, 
        estadoRotacion, 
        detalleRotacion
    ) 
    VALUES (
        _idCampo, 
        _idTipoRotacion, 
        _fechaRotacion, 
        _estadoRotacion, 
        _detalleRotacion
    );
END $$
DELIMITER ;

-- Agregados:
DELIMITER $$
CREATE PROCEDURE spu_listar_equinos_propios()
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
        AND idTipoEquino IN (1, 2);  -- Filtrar solo yeguas (1) y padrillos (2)
END $$
DELIMITER ;

-- Listar Medicamentos
DELIMITER $$
CREATE PROCEDURE listarMedicamentos()
BEGIN
    SELECT idMedicamento, nombreMedicamento
    FROM Medicamentos;
END $$
DELIMITER ;

-- Listar Haras
DELIMITER $$
CREATE PROCEDURE spu_listar_haras()
BEGIN
		SELECT DISTINCT 
        idPropietario,            -- ID del propietario
        nombreHaras               -- Nombre del haras
    FROM Propietarios;
END $$
DELIMITER ;

-- Listar por propietarios
DELIMITER $$
CREATE PROCEDURE spu_listar_equinos_por_propietario (
    IN _idPropietario INT,    -- ID del propietario (Haras)
    IN _genero INT            -- Género: 1 para hembra, 2 para macho
)
BEGIN
    SELECT 
        e.idEquino,           
        e.nombreEquino,         
        p.nombreHaras            
    FROM 
        Equinos e
    JOIN 
        Propietarios p ON e.idPropietario = p.idPropietario 
    WHERE 
        e.idPropietario = _idPropietario AND  
        e.sexo = _genero;                      
END $$
DELIMITER ;

-- Registrar Equino
DELIMITER $$
CREATE PROCEDURE spu_equino_registrar(
    IN _nombreEquino VARCHAR(100),         -- Nombre del equino
    IN _fechaNacimiento DATE,               -- Fecha de nacimiento del equino (puede ser NULL)
    IN _sexo ENUM('macho', 'hembra'),      -- Sexo del equino
    IN _detalles TEXT,                     -- Detalles adicionales del equino
    IN _idTipoEquino INT,                  -- ID del tipo de equino seleccionado
    IN _idPropietario INT,                  -- ID del propietario del equino (puede ser NULL si es propio)
    IN _nacionalidad VARCHAR(50)            -- Nacionalidad del equino (puede ser NULL)
    -- IN _fotografia LONGBLOB
)
BEGIN
    DECLARE _errorMsg VARCHAR(255);
    DECLARE _tipoEquinoNombre VARCHAR(50);

    -- Validar si la fecha de nacimiento no es futura
    IF _fechaNacimiento > CURDATE() THEN
        SET _errorMsg = 'Error: La fecha de nacimiento no puede ser posterior a la fecha actual.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
    END IF;

    -- Validar si el propietario proporcionado existe (si se proporcionó uno)
    IF _idPropietario IS NOT NULL AND NOT EXISTS (SELECT * FROM Propietarios WHERE idPropietario = _idPropietario) THEN
        SET _errorMsg = 'Error: El propietario no existe.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
    END IF;

    -- Obtener el nombre del tipo de equino para validación
    SELECT tipoequino INTO _tipoEquinoNombre FROM TipoEquinos WHERE idTipoEquino = _idTipoEquino;

    -- Validar tipo de equino según el sexo
    IF _sexo = 'macho' AND _tipoEquinoNombre NOT IN ('padrillo', 'potrillo') THEN
        SET _errorMsg = 'Error: Un macho debe ser un padrillo o un potrillo.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
    END IF;

    IF _sexo = 'hembra' AND _tipoEquinoNombre NOT IN ('yegua', 'potranca') THEN
        SET _errorMsg = 'Error: Una hembra debe ser una yegua o una potranca.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
    END IF;

    -- Insertar un nuevo registro en la tabla 'Equinos'
    INSERT INTO Equinos (
        nombreEquino, 
        fechaNacimiento, 
        sexo, 
        idTipoEquino, 
        detalles, 
        idPropietario,
        nacionalidad
        -- fotografia
    ) 
    VALUES (
        _nombreEquino, 
        _fechaNacimiento, 
        _sexo, 
        _idTipoEquino, 
        _detalles, 
        _idPropietario, 
        _nacionalidad
        -- _fotografia
    );
    
    -- Devolver el ID del equino recién insertado
    SELECT LAST_INSERT_ID() AS idEquino;
END $$
DELIMITER ;

-- Listar tipo equino
DELIMITER $$
CREATE PROCEDURE spu_listar_tipoequinos()
BEGIN
    -- Listar todos los tipos de equinos disponibles
    SELECT idTipoEquino, tipoEquino
    FROM TipoEquinos;
END $$
DELIMITER ;

-- Registrar Servicio
DELIMITER $$
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
    IN p_costoServicio INT
)
BEGIN
    DECLARE v_sexoMacho ENUM('macho', 'hembra');
    DECLARE v_sexoHembra ENUM('macho', 'hembra');
    DECLARE v_sexoExterno ENUM('macho', 'hembra');
    DECLARE v_mensajeError VARCHAR(255);
    DECLARE v_count INT;

    -- Validación para la fecha de servicio
    IF p_fechaServicio > CURDATE() THEN
        SET v_mensajeError = 'Error: La fecha de servicio no puede ser mayor que la fecha actual.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
    END IF;

    -- Validación para evitar que un padrillo y una yegua realicen más de un servicio en el mismo día
    IF p_tipoServicio = 'propio' THEN
        SELECT COUNT(*) INTO v_count
        FROM Servicios
        WHERE idEquinoHembra = p_idEquinoHembra
          AND DATE(fechaServicio) = p_fechaServicio;

        IF v_count > 0 THEN
            SET v_mensajeError = 'Error: La yegua ya tiene un servicio registrado en esta fecha.';
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
        END IF;

    ELSEIF p_tipoServicio = 'mixto' THEN
        SELECT COUNT(*) INTO v_count
        FROM Servicios
        WHERE (idEquinoHembra = p_idEquinoHembra OR idEquinoHembra IS NULL)
          AND DATE(fechaServicio) = p_fechaServicio;

        IF v_count > 0 THEN
            SET v_mensajeError = 'Error: La yegua ya tiene un servicio registrado en esta fecha.';
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
        END IF;
    END IF;

    -- Validación para la hora de entrada solo si la fecha es hoy
    IF p_fechaServicio = CURDATE() THEN
        IF p_horaEntrada >= CURRENT_TIME THEN
            SET v_mensajeError = 'Error: La hora de entrada no puede ser mayor o igual a la hora actual.';
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
        END IF;
    END IF;

    -- Validación para la hora de salida
    IF p_horaSalida <= p_horaEntrada THEN
        SET v_mensajeError = 'Error: La hora de salida debe ser mayor que la hora de entrada.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
    END IF;

    -- Validación para la hora de salida solo si la fecha es hoy
    IF p_fechaServicio = CURDATE() THEN
        IF p_horaSalida > CURRENT_TIME THEN
            SET v_mensajeError = 'Error: La hora de salida no puede ser mayor que la hora actual si la fecha es hoy.';
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
        END IF;
    END IF;

    -- Validaciones y lógica para los servicios propios y mixtos
    IF p_tipoServicio = 'propio' THEN
        -- Validaciones de género para servicio propio
        SELECT sexo INTO v_sexoMacho
        FROM Equinos
        WHERE idEquino = p_idEquinoMacho;

        SELECT sexo INTO v_sexoHembra
        FROM Equinos
        WHERE idEquino = p_idEquinoHembra;

        IF v_sexoMacho IS NULL OR v_sexoHembra IS NULL THEN
            SET v_mensajeError = 'Uno o ambos equinos no existen.';
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
        END IF;

        IF v_sexoMacho = v_sexoHembra THEN
            SET v_mensajeError = 'Los equinos deben ser de géneros opuestos.';
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
        END IF;

        -- Registro del servicio propio
        INSERT INTO Servicios (
            idEquinoMacho,
            idEquinoHembra,
            fechaServicio,
            tipoServicio,
            detalles,
            idMedicamento,
            horaEntrada,
            horaSalida,
            idPropietario
        ) VALUES (
            p_idEquinoMacho,
            p_idEquinoHembra,
            p_fechaServicio,
            p_tipoServicio,
            p_detalles,
            p_idMedicamento,
            p_horaEntrada,
            p_horaSalida,
            NULL  -- No hay propietario externo para servicios propios
        );

    ELSEIF p_tipoServicio = 'mixto' THEN
        -- Validaciones para servicio mixto
        IF p_idPropietario IS NULL THEN
            SET v_mensajeError = 'Debe seleccionar el ID del propietario para servicios mixtos.';
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
        END IF;

        -- Obtener el sexo del equino externo
        SELECT sexo INTO v_sexoExterno
        FROM Equinos
        WHERE idEquino = p_idEquinoExterno;

        IF v_sexoExterno IS NULL THEN
            SET v_mensajeError = 'No se encontró un equino externo con el ID proporcionado.';
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
        END IF;

        -- Validaciones para asegurar géneros opuestos
        IF p_idEquinoMacho IS NOT NULL THEN
            SELECT sexo INTO v_sexoMacho
            FROM Equinos
            WHERE idEquino = p_idEquinoMacho;

            IF v_sexoMacho = v_sexoExterno THEN
                SET v_mensajeError = 'El equino externo debe tener el género opuesto al equino propio.';
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensajeError;
            END IF;

            -- Registro del servicio mixto
            INSERT INTO Servicios (
                idEquinoMacho,
                idEquinoHembra,
                fechaServicio,
                tipoServicio,
                detalles,
                idMedicamento,
                horaEntrada,
                horaSalida,
                idPropietario,
                costoServicio
            ) VALUES (
                p_idEquinoMacho,
                p_idEquinoExterno,
                p_fechaServicio,
                p_tipoServicio,
                p_detalles,
                p_idMedicamento,
                p_horaEntrada,
                p_horaSalida,
                p_idPropietario,
                p_costoServicio
            );
        ELSE
            INSERT INTO Servicios (
                idEquinoMacho,
                idEquinoHembra,
                fechaServicio,
                tipoServicio,
                detalles,
                idMedicamento,
                horaEntrada,
                horaSalida,
                idPropietario,
                costoServicio
            ) VALUES (
                p_idEquinoExterno,
                p_idEquinoHembra,
                p_fechaServicio,
                p_tipoServicio,
                p_detalles,
                p_idMedicamento,
                p_horaEntrada,
                p_horaSalida,
                p_idPropietario,
                p_costoServicio
            );
        END IF;
    END IF;

END $$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE listarServiciosPorFechaYTipo(
    IN p_fechaInicio DATE,
    IN p_fechaFin DATE,
    IN p_tipoServicio ENUM('Propio', 'Mixto')
)
BEGIN
    SELECT 
        s.idServicio,
        em.nombreEquino AS nombrePadrillo,
        eh.nombreEquino AS nombreYegua,
        s.fechaServicio,
        s.detalles,
        s.horaEntrada,
        s.horaSalida,
        s.costoServicio,
        CASE 
            WHEN s.tipoServicio = 'Mixto' THEN p.nombreHaras 
            ELSE NULL 
        END AS nombreHaras
    FROM 
        Servicios s
    LEFT JOIN Equinos em ON s.idEquinoMacho = em.idEquino
    LEFT JOIN Equinos eh ON s.idEquinoHembra = eh.idEquino
    LEFT JOIN Propietarios p ON s.idPropietario = p.idPropietario
    WHERE 
        s.fechaServicio BETWEEN p_fechaInicio AND p_fechaFin AND
        s.tipoServicio = p_tipoServicio
    ORDER BY 
        s.fechaServicio DESC;
END $$
DELIMITER ;