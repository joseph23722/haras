-- procedimientos 



-- Procedimiento para listar equinos por tipo (yegua o padrillo)----------------------------------------------------------------------------------------------
DELIMITER $$
CREATE PROCEDURE spu_listar_equinos_por_tipo (
    IN _tipoEquino ENUM('yegua', 'padrillo')  -- Tipo de equino: yegua o padrillo
)
BEGIN
    -- Seleccionamos el ID y el nombre del equino según su tipo
    SELECT 
        e.idEquino,              -- ID del equino
        e.nombreEquino           -- Nombre del equino
    FROM 
        Equinos e
    INNER JOIN 
        TipoEquinos te ON e.idTipoEquino = te.idTipoEquino
    WHERE 
        te.tipoEquino = _tipoEquino;  -- Filtramos por el tipo de equino (yegua o padrillo)
END $$
DELIMITER ;

-- Procedimiento para obtener la lista de medicamentos con sus detalles (si existen)--------------------------------------------------------------------------------------------
DELIMITER $$
CREATE PROCEDURE spu_listar_medicamentos_con_detalles()
BEGIN
    -- Seleccionamos los medicamentos junto con los detalles si existen
    SELECT 
        m.idMedicamento,          -- ID del medicamento
        m.nombreMedicamento,      -- Nombre del medicamento
        dm.dosis,                 -- Dosis del medicamento (si está disponible)
        dm.fechaInicio,           -- Fecha de inicio del tratamiento (si está disponible)
        dm.fechaFin               -- Fecha de fin del tratamiento (si está disponible)
    FROM 
        Medicamentos m
    LEFT JOIN 
        DetalleMedicamentos dm ON m.idMedicamento = dm.idMedicamento;  -- Incluimos detalles si existen
END $$
DELIMITER ;






