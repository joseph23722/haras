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

-- Procedimiento para registrar usuarios ------------------------------------------------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `spu_usuarios_registrar`;
DELIMITER $$
CREATE PROCEDURE spu_usuarios_registrar
(
    OUT _idUsuario      INT,
    IN _idPersonal      INT,
    IN _correo          VARCHAR(50),
    IN _clave           VARCHAR(100),
    IN _idRol           INT
)
BEGIN
    -- Declaración de variables
    DECLARE existe_error INT DEFAULT 0;
    
    -- Manejador de errores
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
    BEGIN
        SET existe_error = 1;
    END;
    
    INSERT INTO Usuarios (idPersonal, correo, clave, idRol)
    VALUES (_idPersonal, _correo, _clave, _idRol);
    
    IF existe_error = 1 THEN
        SET _idUsuario = -1; 
    ELSE
        SET _idUsuario = LAST_INSERT_ID(); 
    END IF;
END $$
DELIMITER ;

-- INSERCCIONES:
CALL spu_personal_registrar(@idPersonal, 'Jorgue', ' Marron', 'Sanmm', 'DNI', '72183875', 20, '2024-01-01');
SELECT @idPersonal AS 'idPersonal';