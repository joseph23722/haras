-- Procedimiento para listar 'Usuarios'------------------------------------------------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `spu_usuarios_listar`;
DELIMITER $$
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
END $$
DELIMITER ;

-- Procedimiento para listar 'Personal'------------------------------------------------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `spu_personal_listar`;
DELIMITER $$
CREATE PROCEDURE `spu_personal_listar`()
BEGIN
    -- Consulta para listar personal y verificar si ya tienen un usuario
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
        END AS tieneUsuario
    FROM 
        Personal p
    LEFT JOIN 
        Usuarios u ON p.idPersonal = u.idPersonal;
END $$
DELIMITER ;