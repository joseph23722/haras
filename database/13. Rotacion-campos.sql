DROP PROCEDURE IF EXISTS `spu_campos_listar`;
DELIMITER //
CREATE PROCEDURE `spu_campos_listar`()
BEGIN
    SELECT 
        C.idCampo,
        C.numeroCampo,
        C.tamanoCampo,
        TS.nombreTipoSuelo,
        C.estado,
        (SELECT TR.nombreRotacion
         FROM RotacionCampos RC
         JOIN TipoRotaciones TR ON RC.idTipoRotacion = TR.idTipoRotacion
         WHERE RC.idCampo = C.idCampo
         ORDER BY RC.fechaRotacion DESC
         LIMIT 1) AS ultimaAccionRealizada,
        MAX(RC.fechaRotacion) AS fechaUltimaAccion
    FROM 
        Campos C
    LEFT JOIN 
        RotacionCampos RC ON C.idCampo = RC.idCampo
    LEFT JOIN 
        TipoSuelo TS ON C.idTipoSuelo = TS.idTipoSuelo
    GROUP BY 
        C.idCampo, C.numeroCampo, C.tamanoCampo, TS.nombreTipoSuelo, C.estado
    ORDER BY 
        C.numeroCampo DESC;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_tiposuelo_listar`;
DELIMITER //
CREATE PROCEDURE `spu_tiposuelo_listar`()
BEGIN
    SELECT 
        C.idTipoSuelo,
        C.nombreTipoSuelo
    FROM 
        tipoSuelo C;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_obtener_ultima_accion`;
DELIMITER //
CREATE PROCEDURE spu_obtener_ultima_accion(IN idCampo INT)
BEGIN
    DECLARE nombreRotacion VARCHAR(100);
    
    SELECT tr.nombreRotacion INTO nombreRotacion
    FROM RotacionCampos rc
    JOIN TipoRotaciones tr ON rc.idTipoRotacion = tr.idTipoRotacion
    WHERE rc.idCampo = idCampo
    ORDER BY rc.fechaRotacion DESC
    LIMIT 1;

    IF nombreRotacion IS NULL THEN
        SELECT 'No hay acciones registradas' AS mensaje;
    ELSE
        SELECT nombreRotacion;
    END IF;
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
    IN p_detalleRotacion TEXT
)
BEGIN
    DECLARE v_count INT;

    -- Verificar si ya existe una rotación del mismo tipo en la misma fecha
    SELECT COUNT(*) INTO v_count
    FROM RotacionCampos
    WHERE idCampo = p_idCampo
      AND idTipoRotacion = p_idTipoRotacion
      AND DATE(fechaRotacion) = DATE(p_fechaRotacion);

    IF v_count > 0 THEN
        -- Si existe, se puede lanzar un error o manejarlo como desees
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Ya existe una rotación del mismo tipo en la misma fecha.';
    ELSE
        -- Si no existe, proceder a insertar
        INSERT INTO RotacionCampos (idCampo, idTipoRotacion, fechaRotacion, detalleRotacion)
        VALUES (p_idCampo, p_idTipoRotacion, p_fechaRotacion, p_detalleRotacion);
    END IF;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_registrar_campo`;
DELIMITER //
CREATE PROCEDURE `spu_registrar_campo`(
    IN p_numeroCampo INT,
    IN p_tamanoCampo DECIMAL(10,2),
    IN p_idTipoSuelo INT,  -- Cambiado a idTipoSuelo
    IN p_estado VARCHAR(50)
)
BEGIN
    DECLARE campoExistente INT;

    SELECT COUNT(*) INTO campoExistente
    FROM Campos
    WHERE numeroCampo = p_numeroCampo;

    IF campoExistente > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error: Ya existe un campo con el mismo número.';
    ELSE
        INSERT INTO Campos (numeroCampo, tamanoCampo, idTipoSuelo, estado)  -- Cambiado a idTipoSuelo
        VALUES (p_numeroCampo, p_tamanoCampo, p_idTipoSuelo, p_estado);
    END IF;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_listar_rotaciones`;
DELIMITER //
CREATE PROCEDURE `spu_listar_rotaciones`()
BEGIN
    SELECT 
    c.numeroCampo,
    tr.nombreRotacion, 
    rc.fechaRotacion 
	FROM 
		RotacionCampos rc
	JOIN 
		TipoRotaciones tr ON rc.idTipoRotacion = tr.idTipoRotacion
	JOIN
        Campos c ON rc.idCampo = c.idCampo;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_eliminar_campo`;
DELIMITER //
CREATE PROCEDURE `spu_eliminar_campo`(
    IN `p_idCampo` INT
)
BEGIN
    -- Eliminar las rotaciones asociadas
    DELETE FROM rotacioncampos WHERE idCampo = p_idCampo;

    -- Ahora eliminar el campo
    DELETE FROM campos WHERE idCampo = p_idCampo;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_editar_campo`;
DELIMITER //
CREATE PROCEDURE `spu_editar_campo`(
    IN `p_idCampo` INT,
    IN `p_numeroCampo` INT,
    IN `p_tamanoCampo` DECIMAL(10,2),
    IN `p_idTipoSuelo` INT,
    IN `p_estado` ENUM('Activo', 'Inactivo')
)
BEGIN
    DECLARE campoExistente INT;

    SELECT COUNT(*) INTO campoExistente
    FROM Campos
    WHERE idCampo = p_idCampo;

    IF campoExistente = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error: El campo no existe.';
    ELSE
        UPDATE Campos
        SET 
            numeroCampo = p_numeroCampo,
            tamanoCampo = p_tamanoCampo,
            idTipoSuelo = p_idTipoSuelo,  -- Cambiado a idTipoSuelo
            estado = p_estado
        WHERE idCampo = p_idCampo;
    END IF;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_obtener_campoID`;
DELIMITER //
CREATE PROCEDURE `spu_obtener_campoID`(
    IN `p_idCampo` INT
)
BEGIN
    SELECT 
        c.idCampo,
        c.numeroCampo,
        c.tamanoCampo,
        c.idTipoSuelo,  -- Asegúrate de incluir idTipoSuelo aquí
        ts.nombreTipoSuelo,
        c.estado
    FROM Campos c
    LEFT JOIN tipoSuelo ts ON c.idTipoSuelo = ts.idTipoSuelo
    WHERE c.idCampo = p_idCampo;
END //
DELIMITER ;