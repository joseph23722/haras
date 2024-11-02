DROP PROCEDURE IF EXISTS `spu_buscar_equino_por_nombre`;
DELIMITER //
CREATE PROCEDURE spu_buscar_equino_por_nombre(IN p_nombreEquino VARCHAR(100))
BEGIN
    SELECT 
        e.idEquino,
        e.nombreEquino,
        e.fechaNacimiento,
        e.sexo,
        te.tipoEquino,
        e.idEstadoMonta,
        e.nacionalidad,
        e.idPropietario,
        e.fotografia
    FROM 
        Equinos e
    JOIN 
        TipoEquinos te ON e.idTipoEquino = te.idTipoEquino
    WHERE 
        e.nombreEquino LIKE CONCAT('%', p_nombreEquino, '%');
END //
DELIMITER ;