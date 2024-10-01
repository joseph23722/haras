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