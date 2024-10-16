DELIMITER $$
CREATE PROCEDURE listarServiciosPorTipo(
    IN p_tipoServicio ENUM('Propio', 'Mixto', 'General')
)
BEGIN
    SELECT 
        s.idServicio,
        em.nombreEquino AS nombrePadrillo,
        eh.nombreEquino AS nombreYegua,
        s.fechaServicio,
        s.detalles,
        s.horaEntrada,
        s.horaSalida,
        s.costoServicio,
        CASE 
            WHEN s.tipoServicio = 'Mixto' THEN p.nombreHaras 
            ELSE NULL 
        END AS nombreHaras
    FROM 
        Servicios s
    LEFT JOIN Equinos em ON s.idEquinoMacho = em.idEquino
    LEFT JOIN Equinos eh ON s.idEquinoHembra = eh.idEquino
    LEFT JOIN Propietarios p ON s.idPropietario = p.idPropietario
    WHERE 
        (p_tipoServicio = 'General' OR s.tipoServicio = p_tipoServicio)
    ORDER BY 
        s.fechaServicio DESC;
END $$
DELIMITER ;