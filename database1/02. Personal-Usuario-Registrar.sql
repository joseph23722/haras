DROP PROCEDURE IF EXISTS `spu_personal_registrar`;
DELIMITER $$
CREATE PROCEDURE spu_personal_registrar
(
    OUT _idPersonal       INT,
    IN _nombres           VARCHAR(100),
    IN _apellidos         VARCHAR(100),
    IN _direccion         VARCHAR(255),
    IN _tipodoc           VARCHAR(20),
    IN _nrodocumento      VARCHAR(50),
    IN _fechaIngreso      DATE,
    IN _tipoContrato	  ENUM('Parcial', 'Completo', 'Por Prácticas', 'Otro')
)
BEGIN
    -- Declaración de variables
    DECLARE existe_error INT DEFAULT 0;
    
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
    BEGIN
        SET existe_error = 1;
    END;
    
    INSERT INTO Personal (nombres, apellidos, direccion, tipodoc, nrodocumento, fechaIngreso, tipoContrato)
    VALUES (_nombres, _apellidos, _direccion, _tipodoc, _nrodocumento, _fechaIngreso, _tipoContrato);
    
    -- Verificar si ocurrió un error
    IF existe_error = 1 THEN
        SET _idPersonal = -1;  -- Devuelve -1 si hay error
    ELSE
        SET _idPersonal = LAST_INSERT_ID();  -- Devuelve el ID del nuevo registro
    END IF;
END $$
DELIMITER ;

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
    DECLARE existe_error INT DEFAULT 0;
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

DROP PROCEDURE IF EXISTS `spu_actualizar_contraseña`;
DELIMITER $$
CREATE PROCEDURE spu_actualizar_contraseña(
    IN _correo VARCHAR(120),
    IN p_clave VARCHAR(120)
)
BEGIN 
    UPDATE usuarios
    SET clave = p_clave
    WHERE correo = _correo;
END $$
DELIMITER ;