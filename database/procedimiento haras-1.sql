
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


-- prueba de servicio---------------------------------------------------------------------------------
-- Procedimiento para registrar un nuevo servicio equino - propio
DELIMITER $$

CREATE PROCEDURE spu_registrar_servicio_propio(
    IN _idEquino1 INT, 
    IN _idEquino2 INT, 
    IN _fechaServicio DATE, 
    IN _detalles TEXT, 
    IN _horaEntrada TIME, 
    IN _horaSalida TIME
)
BEGIN
    -- Iniciar la transacción
    START TRANSACTION;

    -- Insertar en la tabla Servicios para el primer equino
    INSERT INTO Servicios (
        idEquino, 
        fechaServicio, 
        tipoServicio, 
        detalles, 
        horaEntrada, 
        horaSalida
    )
    VALUES (
        _idEquino1, 
        _fechaServicio, 
        'propio', 
        _detalles, 
        _horaEntrada, 
        _horaSalida
    );

    -- Insertar en la tabla Servicios para el segundo equino
    INSERT INTO Servicios (
        idEquino, 
        fechaServicio, 
        tipoServicio, 
        detalles, 
        horaEntrada, 
        horaSalida
    )
    VALUES (
        _idEquino2, 
        _fechaServicio, 
        'propio', 
        _detalles, 
        _horaEntrada, 
        _horaSalida
    );

    -- Finalizar la transacción
    COMMIT;
END$$

DELIMITER ;


-- Procedimiento para registrar un nuevo servicio equino - mixto----------------------------------------------------------------------
DELIMITER $$

CREATE PROCEDURE spu_registrar_servicio_mixto(
    IN _idEquinoSeleccionado INT,  -- Equino seleccionado desde el formulario
    IN _nombreNuevoEquino VARCHAR(100),  -- Nombre del nuevo equino
    IN _idTipoEquino INT,  -- Tipo del nuevo equino (1 = Yegua, 2 = Padrillo)
    IN _nombreHaras VARCHAR(100),  -- Nombre del Haras del nuevo equino (solo si es nuevo)
    IN _idHaras INT,  -- ID del Haras existente (si se seleccionó uno)
    IN _fechaServicio DATE,  -- Fecha del servicio
    IN _detalles TEXT,  -- Detalles del servicio
    IN _horaEntrada TIME,  -- Hora de entrada
    IN _horaSalida TIME  -- Hora de salida
)
BEGIN
    DECLARE _nuevoPropietarioId INT;

    -- Iniciar la transacción
    START TRANSACTION;

    -- Si no se seleccionó un Haras existente, insertar un nuevo haras
    IF _idHaras IS NULL THEN
        -- Insertar el nuevo haras en la tabla Propietarios
        INSERT INTO Propietarios (nombreHaras) 
        VALUES (_nombreHaras);

        -- Obtener el id del nuevo propietario creado
        SET _nuevoPropietarioId = LAST_INSERT_ID();
    ELSE
        -- Si se seleccionó un haras existente, usar su ID
        SET _nuevoPropietarioId = _idHaras;
    END IF;

    -- Insertar el nuevo equino en la tabla Equinos
    INSERT INTO Equinos (
        nombreEquino,
        sexo,
        idTipoEquino,
        detalles,
        idPropietario
    )
    VALUES (
        _nombreNuevoEquino,
        CASE WHEN _idTipoEquino = 1 THEN 'hembra' ELSE 'macho' END,
        _idTipoEquino,
        'Detalles del nuevo equino',
        _nuevoPropietarioId
    );

    -- Obtener el id del nuevo equino creado
    SET @nuevoEquinoId = LAST_INSERT_ID();

    -- Insertar en la tabla Servicios para el equino seleccionado
    INSERT INTO Servicios (
        idEquino, 
        fechaServicio, 
        tipoServicio, 
        detalles, 
        horaEntrada, 
        horaSalida
    )
    VALUES (
        _idEquinoSeleccionado, 
        _fechaServicio, 
        'mixto', 
        _detalles, 
        _horaEntrada, 
        _horaSalida
    );

    -- Insertar en la tabla Servicios para el nuevo equino creado
    INSERT INTO Servicios (
        idEquino, 
        fechaServicio, 
        tipoServicio, 
        detalles, 
        horaEntrada, 
        horaSalida
    )
    VALUES (
        @nuevoEquinoId, 
        _fechaServicio, 
        'mixto', 
        _detalles, 
        _horaEntrada, 
        _horaSalida
    );

    -- Finalizar la transacción
    COMMIT;
END$$

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

-- Procedimiento para listar haras (propietarios) con nombres únicos-------------------------------------------------------------------------
DELIMITER $$

CREATE PROCEDURE spu_listar_haras()
BEGIN
    -- Seleccionamos el ID y el nombre de los haras de forma única
    SELECT DISTINCT 
        idPropietario,            -- ID del propietario
        nombreHaras               -- Nombre del haras
    FROM Propietarios;
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
-- ---------------------------------------------------------------------------------------------
-- Procedimiento para registrar los alimentos  entrada y salida 
DELIMITER $$

CREATE PROCEDURE spu_alimentos_nuevo(
    IN _idUsuario INT,
    IN _nombreAlimento VARCHAR(100),
    IN _cantidad DECIMAL(10,2),
    IN _costo DECIMAL(10,2),
    IN _idTipoEquino INT,
    IN _fechaIngreso DATETIME
)
BEGIN
    -- Insertar un nuevo alimento en el inventario
    INSERT INTO Alimentos (
        idUsuario, 
        nombreAlimento, 
        cantidad, 
        costo, 
        idTipoEquino, 
        idTipomovimiento, 
        stockFinal, 
        fechaIngreso
    ) 
    VALUES (
        _idUsuario, 
        _nombreAlimento, 
        _cantidad, 
        _costo, 
        _idTipoEquino, 
        1,  -- Se asume que es una entrada inicial
        _cantidad,  -- El stock inicial es igual a la cantidad ingresada
        _fechaIngreso
    );
END $$

DELIMITER ;
-- ----------------------------------------------------------
DELIMITER $$

CREATE PROCEDURE spu_alimentos_actualizar_stock(
    IN _idUsuario INT,
    IN _nombreAlimento VARCHAR(100),
    IN _cantidad DECIMAL(10,2),
    IN _idTipomovimiento INT,  -- 1: Entrada, 2: Salida
    IN _fechaIngreso DATETIME
)
BEGIN
    DECLARE _stockActual INT DEFAULT 0;
    DECLARE _errorStock INT DEFAULT 0;
    DECLARE _alimentoID INT;

    -- Obtener el stock actual del alimento
    SELECT idAlimento, stockFinal INTO _alimentoID, _stockActual
    FROM Alimentos
    WHERE nombreAlimento = _nombreAlimento
    ORDER BY fechaIngreso DESC
    LIMIT 1;

    -- Si no hay registros anteriores, inicializar el stock actual en 0
    IF _stockActual IS NULL THEN
        SET _stockActual = 0;
    END IF;

    -- Manejar el movimiento de entrada o salida
    IF _idTipomovimiento = 1 THEN
        SET _stockActual = _stockActual + _cantidad;

    ELSEIF _idTipomovimiento = 2 THEN
        IF _stockActual >= _cantidad THEN
            SET _stockActual = _stockActual - _cantidad;
        ELSE
            SET _errorStock = 1;  -- Marcar que no hay suficiente stock
        END IF;
    END IF;

    -- Actualizar el stock si no hay error
    IF _errorStock = 0 THEN
        -- Actualizar el registro del alimento
        UPDATE Alimentos
        SET 
            cantidad = _cantidad,
            stockFinal = _stockActual,
            fechaIngreso = _fechaIngreso,
            idTipomovimiento = _idTipomovimiento
        WHERE idAlimento = _alimentoID;

    ELSE
        -- Enviar mensaje de error en caso de stock insuficiente
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stock insuficiente para realizar la salida';
    END IF;
END $$

DELIMITER ;

-- ----------------------------------------------------------------------------------------------------------------------------------
-- Procedimiento para registrar un nuevo equino en la base de datos
DELIMITER $$

CREATE PROCEDURE spu_equino_registrar(
    IN _nombreEquino VARCHAR(100),         -- Nombre del equino
    IN _fechaNacimiento DATE,              -- Fecha de nacimiento del equino (tipo DATE)
    IN _sexo ENUM('macho', 'hembra'),      -- Sexo del equino
    IN _detalles TEXT,                     -- Detalles adicionales del equino
    IN _idPropietario INT,                 -- ID del propietario del equino (puede ser NULL si es propio)
    IN _generacion VARCHAR(50),            -- Generación del equino
    IN _nacionalidad VARCHAR(50)           -- Nacionalidad del equino
)
BEGIN
    DECLARE _idTipoEquino INT;  -- Variable para almacenar el ID del tipo de equino
    DECLARE _edadMeses INT;     -- Variable para calcular la edad en meses
    DECLARE _idEstadoMonta INT; -- Variable para almacenar el ID del estado de monta (solo para adultos)
    DECLARE _currentTime DATE;  -- Variable para almacenar la fecha actual
    DECLARE _errorMsg VARCHAR(255);
    
    -- Obtener la fecha actual
    SET _currentTime = CURDATE();

    -- Validar si la fecha de nacimiento es válida (no puede ser futura)
    IF _fechaNacimiento > _currentTime THEN
        SET _errorMsg = 'Error: La fecha de nacimiento no puede ser futura.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
    END IF;

    -- Calcular la edad del equino en meses
    SET _edadMeses = TIMESTAMPDIFF(MONTH, _fechaNacimiento, _currentTime);

    -- Determinar el tipo de equino en función de la edad y el sexo
    IF _edadMeses <= 12 THEN
        IF _sexo = 'macho' THEN
            SET _idTipoEquino = (SELECT idTipoEquino FROM TipoEquinos WHERE tipoEquino = 'potrillo' LIMIT 1);
        ELSE
            SET _idTipoEquino = (SELECT idTipoEquino FROM TipoEquinos WHERE tipoEquino = 'potranca' LIMIT 1);
        END IF;
        -- No asignar estado de monta para potrillos/potrancas
        SET _idEstadoMonta = NULL;
    ELSE
        IF _sexo = 'macho' THEN
            SET _idTipoEquino = (SELECT idTipoEquino FROM TipoEquinos WHERE tipoEquino = 'padrillo' LIMIT 1);
        ELSE
            SET _idTipoEquino = (SELECT idTipoEquino FROM TipoEquinos WHERE tipoEquino = 'yegua' LIMIT 1);
        END IF;

        -- Asignar un estado de monta válido solo para yeguas y padrillos
        SET _idEstadoMonta = (SELECT idEstado FROM EstadoMonta WHERE genero = _sexo LIMIT 1);
    END IF;

    -- Validar si el propietario proporcionado existe (si se proporcionó uno)
    IF _idPropietario IS NOT NULL AND NOT EXISTS (SELECT 1 FROM Propietarios WHERE idPropietario = _idPropietario) THEN
        SET _errorMsg = 'Error: El propietario no existe.';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = _errorMsg;
    END IF;

    -- Insertar un nuevo registro en la tabla 'Equinos'
    INSERT INTO Equinos (
        nombreEquino, 
        fechaNacimiento, 
        sexo, 
        idTipoEquino, 
        detalles, 
        idEstadoMonta, 
        idPropietario, 
        generacion, 
        nacionalidad
    ) 
    VALUES (
        _nombreEquino, 
        _fechaNacimiento, 
        _sexo, 
        _idTipoEquino, 
        _detalles, 
        _idEstadoMonta,  -- Este valor será NULL para potrillos/potrancas
        _idPropietario, 
        _generacion, 
        _nacionalidad
    );
    
    -- Devolver el ID del equino recién insertado
    SELECT LAST_INSERT_ID() AS idEquino;
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
    IN _cantidad DECIMAL(10,2),
    IN _caducidad DATE,
    IN _precioUnitario DECIMAL(10,2),
    IN _idTipomovimiento INT,
    IN _idUsuario INT,
    IN _visita TEXT,
    IN _tratamiento TEXT
)
BEGIN
    INSERT INTO Medicamentos (
        nombreMedicamento, 
        cantidad, 
        caducidad, 
        precioUnitario, 
        idTipomovimiento, 
        idUsuario, 
        visita, 
        tratamiento
    ) 
    VALUES (
        _nombreMedicamento, 
        _cantidad, 
        _caducidad, 
        _precioUnitario, 
        _idTipomovimiento, 
        _idUsuario, 
        _visita, 
        _tratamiento
    );
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