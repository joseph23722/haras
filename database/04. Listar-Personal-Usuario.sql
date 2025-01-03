DROP PROCEDURE IF EXISTS `spu_usuarios_listar`;
CREATE PROCEDURE spu_usuarios_listar()
BEGIN
    SELECT 
        USU.idUsuario,
        PER.nombres,
        PER.apellidos,
        PER.tipodoc,
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
END;

DROP PROCEDURE IF EXISTS `spu_modificar_estado_user`;
CREATE PROCEDURE spu_modificar_estado_user(IN p_idUsuario INT)
BEGIN
	IF EXISTS (SELECT 1 FROM Usuarios WHERE idUsuario = p_idUsuario) THEN
    UPDATE Usuarios
    SET estado = CASE
		WHEN estado = 1 THEN 0
        ELSE 1
        END
	WHERE idUsuario = p_idUsuario;
    
    SELECT 'Estado cambiado correctamente' AS mensaje;
    ELSE
		SELECT 'Usuario no encontrado' AS mensaje;
	end if;
END;

DROP PROCEDURE IF EXISTS `spu_personal_listar`;

CREATE PROCEDURE `spu_personal_listar`()
BEGIN
    SELECT 
        p.idPersonal, 
        p.nombres, 
        p.apellidos, 
        p.direccion,
        p.tipodoc,
        p.nrodocumento,
        CASE 
            WHEN u.idUsuario IS NOT NULL THEN 1 
            ELSE 0 
        END AS tieneUsuario,
        u.idUsuario,
        u.correo
    FROM 
        Personal p
    LEFT JOIN 
        Usuarios u ON p.idPersonal = u.idPersonal;
END; 