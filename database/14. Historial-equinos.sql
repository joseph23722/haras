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
        n.nacionalidad,  -- Mostrar el nombre de la nacionalidad
        e.pesokg,
        e.idPropietario,
        e.fotografia
    FROM 
        Equinos e
    JOIN 
        TipoEquinos te ON e.idTipoEquino = te.idTipoEquino
    LEFT JOIN 
        EstadoMonta em ON e.idEstadoMonta = em.idEstadoMonta 
    LEFT JOIN 
        Nacionalidades n ON e.idNacionalidad = n.idNacionalidad
    WHERE 
        e.nombreEquino = p_nombreEquino
        AND e.idPropietario IS NULL; 
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_registrar_historial_equinos`;
DELIMITER $$
CREATE PROCEDURE spu_registrar_historial_equinos(
    IN p_idEquino INT,
    IN p_descripcion TEXT
)
BEGIN
    INSERT INTO HistorialEquinos (idEquino, descripcion)
    VALUES (p_idEquino, p_descripcion);
END $$
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_obtener_historial_equino`;
DELIMITER //
CREATE PROCEDURE `spu_obtener_historial_equino`(IN p_idEquino INT)
BEGIN
    SELECT
        HE.descripcion,
        E.fotografia
    FROM
        HistorialEquinos HE
    JOIN
        Equinos E ON HE.idEquino = E.idEquino
    WHERE
        HE.idEquino = p_idEquino;
END //
DELIMITER ;