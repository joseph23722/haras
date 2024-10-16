DROP PROCEDURE IF EXISTS `spu_campos_listar`;
DELIMITER //
CREATE PROCEDURE `spu_campos_listar`()
BEGIN
    SELECT 
        C.idCampo,
        C.numeroCampo,
        C.tamanoCampo,
        C.tipoSuelo,
        C.estado,
        RC.estadoRotacion AS ultimaAccion,
        MAX(RC.fechaRotacion) AS fechaUltimaAccion
    FROM 
        Campos C
    LEFT JOIN 
        RotacionCampos RC ON C.idCampo = RC.idCampo
    GROUP BY 
        C.idCampo, C.numeroCampo, C.tamanoCampo, C.tipoSuelo, C.estado
    ORDER BY 
        C.numeroCampo DESC;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_tipos_rotaciones_listar`;
DELIMITER //
CREATE PROCEDURE `spu_tipos_rotaciones_listar`()
BEGIN
    SELECT 
        TR.idTipoRotacion,
        TR.nombreRotacion,
        TR.detalles
    FROM 
        TipoRotaciones TR
    ORDER BY 
        TR.nombreRotacion;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_registrar_rotacion_campos`;
DELIMITER //
CREATE PROCEDURE `spu_registrar_rotacion_campos`(
    IN p_idCampo INT,
    IN p_idTipoRotacion INT,
    IN p_fechaRotacion DATETIME,
    IN p_estadoRotacion VARCHAR(50),
    IN p_detalleRotacion TEXT
)
BEGIN
    INSERT INTO RotacionCampos (idCampo, idTipoRotacion, fechaRotacion, estadoRotacion, detalleRotacion)
    VALUES (p_idCampo, p_idTipoRotacion, p_fechaRotacion, p_estadoRotacion, p_detalleRotacion);
END //
DELIMITER ;