DROP PROCEDURE IF EXISTS `spu_listar_tipo_movimiento`;
DELIMITER //
CREATE PROCEDURE `spu_listar_tipo_movimiento`()
BEGIN
    SELECT * FROM tipomovimientos;
END //
DELIMITER ;