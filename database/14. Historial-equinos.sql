DROP PROCEDURE IF EXISTS `spu_buscar_equino_por_nombre`;

CREATE PROCEDURE spu_buscar_equino_por_nombre(IN p_nombreEquino VARCHAR(100))
BEGIN
    SELECT 
        e.idEquino,
        e.nombreEquino,
        e.fechaNacimiento,
        e.sexo,
        te.tipoEquino,
        em.nombreEstado AS estadoMonta,
        n.nacionalidad,
        e.pesokg,
        e.idPropietario,
        e.fotografia,
        IF(e.estado = 1, 'Vivo', IF(e.estado = 2, 'Muerto', 'Desconocido')) AS estado
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
END ;

DROP PROCEDURE IF EXISTS `spu_buscar_equino_por_nombre_general`;

CREATE PROCEDURE spu_buscar_equino_por_nombre_general(IN p_nombreEquino VARCHAR(100))
BEGIN
    SELECT 
        e.idEquino,
        e.nombreEquino,
        e.fechaNacimiento,
        e.sexo,
        te.tipoEquino,
        em.nombreEstado AS estadoMonta,
        n.nacionalidad,
        e.pesokg,
        e.idPropietario,
        e.fotografia,
        IF(e.estado = 1, 'Vivo', IF(e.estado = 2, 'Muerto', 'Desconocido')) AS estado
    FROM 
        Equinos e
    JOIN 
        TipoEquinos te ON e.idTipoEquino = te.idTipoEquino
    LEFT JOIN 
        EstadoMonta em ON e.idEstadoMonta = em.idEstadoMonta 
    LEFT JOIN 
        Nacionalidades n ON e.idNacionalidad = n.idNacionalidad
    WHERE 
        e.nombreEquino = p_nombreEquino;
END ;

DROP PROCEDURE IF EXISTS `spu_registrar_historial_equinos`;

CREATE PROCEDURE spu_registrar_historial_equinos(
    IN p_idEquino INT,
    IN p_descripcion TEXT
)
BEGIN
    DECLARE historial_existe INT;

    -- Verificamos si ya existe un historial para el equino
    SELECT COUNT(*) 
    INTO historial_existe
    FROM HistorialEquinos
    WHERE idEquino = p_idEquino;

    -- Si ya existe un historial, enviamos un mensaje de error
    IF historial_existe > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Ya existe un historial para este equino';
    ELSE
        -- Si no existe un historial, procedemos con la inserción
        INSERT INTO HistorialEquinos (idEquino, descripcion)
        VALUES (p_idEquino, p_descripcion);
    END IF;
END ;

DROP PROCEDURE IF EXISTS `spu_obtener_historial_equino`;

CREATE PROCEDURE `spu_obtener_historial_equino`(IN p_idEquino INT)
BEGIN
    -- Verificar si existen registros en el historial
    IF EXISTS (SELECT 1 FROM HistorialEquinos WHERE idEquino = p_idEquino) THEN
        -- Selección del historial y la fotografía
        SELECT
            HE.descripcion,
            E.fotografia
        FROM
            HistorialEquinos HE
        JOIN
            Equinos E ON HE.idEquino = E.idEquino
        WHERE
            HE.idEquino = p_idEquino;
    ELSE
        -- Mensaje en caso de no haber registros
        SELECT 'No se encontró historial para el equino con ID ' AS mensaje;
    END IF;
END ;