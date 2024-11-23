DROP PROCEDURE IF EXISTS `spu_registrar_revision_equino`;
DELIMITER $$
CREATE PROCEDURE spu_registrar_revision_equino (
    IN p_idEquino INT,
    IN p_idPropietario INT,
    IN p_tiporevision ENUM('Ecografía', 'Examen ginecológico', 'Citología', 'Cultivo bacteriológico', 'Biopsia endometrial'),
    IN p_fecharevision DATE,
    IN p_observaciones TEXT,
    IN p_costorevision DECIMAL(10,2)
)
BEGIN
    -- Verificar si el equino es una Yegua y ha tenido al menos un servicio
    DECLARE v_tipoEquino INT;
    DECLARE v_serviciosCount INT;

    -- Obtener el tipo de equino (1 = Yegua, 2 = Padrillo, 3 = Potranca, 4 = Potrillo)
    SELECT idTipoEquino INTO v_tipoEquino
    FROM Equinos
    WHERE idEquino = p_idEquino;

    -- Verificar si el equino es una Yegua (tipo 1)
    IF v_tipoEquino != 1 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El equino no es una yegua';
    END IF;

    -- Verificar si el equino (como yegua) ha tenido al menos un servicio
    SELECT COUNT(*) INTO v_serviciosCount
    FROM Servicios
    WHERE idEquinoHembra = p_idEquino;

    IF v_serviciosCount = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La yegua no tiene servicios registrados';
    END IF;
    
     -- Verificar si la fecha de la revisión no es posterior a la fecha actual
    IF p_fecharevision > CURDATE() THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se puede registrar una revisión con una fecha futura';
    END IF;

    -- Insertar la nueva revisión en la tabla revisionequinos
    INSERT INTO revisionequinos (
        idEquino, 
        idPropietario, 
        tiporevision, 
        fecharevision, 
        observaciones, 
        costorevision
    )
    VALUES (
        p_idEquino, 
        p_idPropietario, 
        p_tiporevision, 
        p_fecharevision, 
        p_observaciones, 
        p_costorevision
    );

    -- Mensaje de confirmación
    SELECT 'Revisión registrada correctamente' AS mensaje;
END$$
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_listar_equinos_para_revision`;
DELIMITER $$
CREATE PROCEDURE spu_listar_equinos_para_revision(
    IN p_idPropietario INT
)
BEGIN
    -- Si se pasa un idPropietario, listar las yeguas de ese propietario específico
    IF p_idPropietario IS NOT NULL THEN
        SELECT 
            idEquino, 
            nombreEquino
        FROM 
            Equinos
        WHERE 
            sexo = 'Hembra'
            AND idPropietario = p_idPropietario
            AND estado = 1;

    -- Si no se pasa un idPropietario (NULL), listar las yeguas sin propietario
    ELSE
        SELECT 
            idEquino, 
            nombreEquino
        FROM 
            Equinos
        WHERE 
            sexo = 'Hembra'
            AND idPropietario IS NULL
            AND estado = 1;
    END IF;
END $$
DELIMITER ;