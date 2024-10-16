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