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
        em.nombreEstado AS estadoMonta,  -- Obtener el nombre del estado
        e.nacionalidad,
        e.pesokg,
        e.idPropietario,
        e.fotografia
    FROM 
        Equinos e
    JOIN 
        TipoEquinos te ON e.idTipoEquino = te.idTipoEquino
    JOIN 
        EstadoMonta em ON e.idEstadoMonta = em.idEstadoMonta
    WHERE 
        e.nombreEquino = p_nombreEquino;
END //
DELIMITER ;