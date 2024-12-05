DROP PROCEDURE IF EXISTS `spu_usuarios_login`;

CREATE PROCEDURE `spu_usuarios_login`(IN _correo VARCHAR(100))
BEGIN
    SELECT 
        USU.idUsuario,
        PER.apellidos,
        PER.nombres,
        USU.correo,
        USU.clave,
        USU.idRol
    FROM 
        Usuarios USU
    INNER JOIN 
        Personal PER ON PER.idPersonal = USU.idPersonal
    WHERE 
        USU.correo = _correo
        AND USU.estado = 1; -- Filtro para solo usuarios activos
END; 

DROP PROCEDURE IF EXISTS `spu_obtener_acceso_usuario`;
 
CREATE PROCEDURE spu_obtener_acceso_usuario(IN _idRol INT)
BEGIN
    SELECT 
       PE.idpermiso,
       MO.modulo,
       VI.ruta,
       VI.sidebaroption,
       VI.texto,
       VI.icono
       FROM permisos PE
       INNER JOIN vistas VI ON VI.idvista = PE.idvista
       LEFT JOIN modulos MO ON MO.idmodulo = VI.idmodulo
       WHERE PE.idRol = _idRol;
END;