-- login ------------------------------------------------------------------------------------------------------------------------------------------------------
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

-- procedimiento para buscar por Dni -----------------------------------------------------------------------------------------------------------------------------
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

-- Procedimiento para registrar persdonal -------------------------------------------------------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `spu_personal_registrar`;
DELIMITER $$
CREATE PROCEDURE spu_personal_registrar
(
    OUT _idPersonal       INT,      -- Negativo cuando ocurre un error (ej. restricción)
    IN _nombres           VARCHAR(100),
    IN _apellidos         VARCHAR(100),
    IN _direccion         VARCHAR(255),
    IN _tipodoc           VARCHAR(20),
    IN _nrodocumento      VARCHAR(50),
    IN _numeroHijos       INT,
    IN _fechaIngreso      DATE
)
BEGIN
    -- Declaración de variables
    DECLARE existe_error INT DEFAULT 0;
    
    -- Manejador de errores
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
    BEGIN
        SET existe_error = 1;
    END;
    
    -- Intentar insertar los datos en la tabla Personal
    INSERT INTO Personal (nombres, apellidos, direccion, tipodoc, nrodocumento, numeroHijos, fechaIngreso)
    VALUES (_nombres, _apellidos, _direccion, _tipodoc, _nrodocumento, _numeroHijos, _fechaIngreso);
    
    -- Verificar si ocurrió un error
    IF existe_error = 1 THEN
        SET _idPersonal = -1;  -- Devuelve -1 si hay error
    ELSE
        SET _idPersonal = LAST_INSERT_ID();  -- Devuelve el ID del nuevo registro
    END IF;
END $$
DELIMITER ;


CALL spu_personal_registrar(@idPersonal, 'Joseph', ' Mateo Paullac', 'San Agustin', 'DNI', '72183871', 2, '2024-01-01');
SELECT @idPersonal AS 'idPersonal';

-- Procedimiento para registrar usuarios ------------------------------------------------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `spu_usuarios_registrar`;
DELIMITER $$
CREATE PROCEDURE spu_usuarios_registrar
(
    OUT _idUsuario      INT,      -- Negativo cuando ocurre un error (ej. restricción)
    IN _idPersonal      INT,      -- ID del Personal que ya debe existir
    IN _correo          VARCHAR(50),
    IN _clave           VARCHAR(100),
    IN _idRol           INT       -- ID del Rol asignado al usuario
)
BEGIN
    -- Declaración de variables
    DECLARE existe_error INT DEFAULT 0;
    
    -- Manejador de errores
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
    BEGIN
        SET existe_error = 1;
    END;
    
    -- Intentar insertar los datos en la tabla Usuarios
    INSERT INTO Usuarios (idPersonal, correo, clave, idRol)
    VALUES (_idPersonal, _correo, _clave, _idRol);
    
    -- Verificar si ocurrió un error
    IF existe_error = 1 THEN
        SET _idUsuario = -1;  -- Devuelve -1 si hay error
    ELSE
        SET _idUsuario = LAST_INSERT_ID();  -- Devuelve el ID del nuevo registro
    END IF;
END $$
DELIMITER ;


CALL spu_usuarios_registrar(@idUsuario, @idPersonal, 'lcontreras', 'claveSegura', 1);
SELECT @idUsuario AS 'idUsuario';

-- Procedimiento para listar 'Usuarios'------------------------------------------------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `spu_usuarios_listar`;
DELIMITER $$
CREATE PROCEDURE spu_usuarios_listar()
BEGIN
    SELECT 
        USU.idUsuario,
        PER.nombres,
        PER.apellidos,
        PER.nrodocumento,
        PER.direccion,
        USU.correo,
        USU.idRol
    FROM 
        Usuarios USU
    INNER JOIN 
        Personal PER ON USU.idPersonal = PER.idPersonal
    ORDER BY 
        USU.idUsuario DESC;
END $$
DELIMITER ;

CALL spu_usuarios_listar();

