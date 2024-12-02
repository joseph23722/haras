DROP PROCEDURE IF EXISTS `spu_equinos_listar`;
DELIMITER //
CREATE PROCEDURE `spu_equinos_listar`()
BEGIN
    SELECT
        E.idEquino,
        E.nombreEquino,
        E.fechaNacimiento,
        E.sexo,
        TE.tipoEquino,
        E.detalles,
        EM.nombreEstado,
        E.pesokg,
        N.nacionalidad AS nacionalidad,
        E.estado,
        E.fotografia,
        CASE 
            WHEN E.estado = 1 THEN 'Vivo'
            WHEN E.estado = 0 THEN 'Muerto'
            ELSE 'Desconocido'
        END AS estadoDescriptivo,
        
        -- Relacionamos el historial completo del equino
        HE.descripcion AS descripcion
        
    FROM
        Equinos E
    LEFT JOIN TipoEquinos TE ON E.idTipoEquino = TE.idTipoEquino
    LEFT JOIN EstadoMonta EM ON E.idEstadoMonta = EM.idEstadoMonta
    LEFT JOIN nacionalidades N ON E.idNacionalidad = N.idNacionalidad
    LEFT JOIN HistorialEquinos HE ON E.idEquino = HE.idEquino
    WHERE
        E.idPropietario IS NULL
    ORDER BY 
        E.estado DESC,
        E.idEquino DESC;
END //
DELIMITER ;