DROP PROCEDURE IF EXISTS `spu_listarServiciosPorTipo`;

CREATE PROCEDURE spu_listarServiciosPorTipo(
    IN p_tipoServicio ENUM('Propio', 'Mixto', 'General')
)
BEGIN
    SELECT 
        s.idServicio,
        em.nombreEquino AS nombrePadrillo,
        eh.nombreEquino AS nombreYegua,
        ee.nombreEquino AS nombreEquinoExterno,
        s.fechaServicio,
        s.detalles,
        s.horaEntrada,
        s.horaSalida,
        s.costoServicio,
        p.nombreHaras
    FROM 
        Servicios s
    LEFT JOIN Equinos em ON s.idEquinoMacho = em.idEquino
    LEFT JOIN Equinos eh ON s.idEquinoHembra = eh.idEquino
    LEFT JOIN Equinos ee ON s.idEquinoExterno = ee.idEquino
    LEFT JOIN Propietarios p ON s.idPropietario = p.idPropietario
    WHERE 
        (
            (p_tipoServicio = 'General' AND s.tipoServicio IN ('General', 'Mixto', 'Propio'))
            OR s.tipoServicio = p_tipoServicio
        )
    ORDER BY 
        s.fechaServicio DESC;
 END; 