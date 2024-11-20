DROP PROCEDURE IF EXISTS `spu_nuevas_fotografias_equinos`;
DELIMITER $$
CREATE PROCEDURE spu_nuevas_fotografias_equinos(
    IN p_idEquino INT,
    IN p_public_id VARCHAR(255)
)
BEGIN
    -- Inserta una nueva fotografía en la tabla fotografiaequinos
    INSERT INTO fotografiaequinos (idEquino, public_id, created_at)
    VALUES (p_idEquino, p_public_id, NOW());
END$$
DELIMITER ;