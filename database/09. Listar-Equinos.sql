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
        E.fotografia
    FROM
        Equinos E
    LEFT JOIN TipoEquinos TE ON E.idTipoEquino = TE.idTipoEquino
    LEFT JOIN EstadoMonta EM ON E.idEstadoMonta = EM.idEstadoMonta
    LEFT JOIN nacionalidades N ON E.idNacionalidad = N.idNacionalidad
    WHERE
        E.idPropietario IS NULL
    ORDER BY E.idEquino DESC;
END //
DELIMITER ;