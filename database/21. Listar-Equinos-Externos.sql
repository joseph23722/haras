DROP PROCEDURE IF EXISTS `spu_listar_equinos_externos`;
DELIMITER $$
CREATE PROCEDURE spu_listar_equinos_externos()
BEGIN
	SELECT 
        e.idEquino,
        e.nombreEquino,
        e.sexo,
        t.TipoEquino,
        e.detalles,
        em.nombreEstado,
        n.nacionalidad,
        p.nombreHaras,
        e.fechaentrada,
        e.fechasalida
    FROM Equinos e
    LEFT JOIN TipoEquinos t ON e.idTipoEquino = t.idTipoEquino
    LEFT JOIN EstadoMonta em ON e.idEstadoMonta = em.idEstadoMonta
    LEFT JOIN Nacionalidades n ON e.idNacionalidad = n.idNacionalidad
    LEFT JOIN Propietarios p ON e.idPropietario = p.idPropietario
    WHERE e.idPropietario IS NOT NULL;
END$$
DELIMITER ;