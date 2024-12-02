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

DROP PROCEDURE IF EXISTS `spu_listar_fotografias_equinos`;
DELIMITER $$
CREATE PROCEDURE spu_listar_fotografias_equinos(
    IN p_idEquino INT
)
BEGIN
    -- Selecciona las fotografías asociadas a un idEquino específico
    SELECT idEquino, public_id, created_at
    FROM fotografiaequinos
    WHERE idEquino = p_idEquino
    ORDER BY created_at DESC;
END$$
DELIMITER ;