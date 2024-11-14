-- procedimiento almacenado que soporte el dashboard de "Stock de Medicamentos"
DELIMITER //
CREATE PROCEDURE ObtenerResumenStockMedicamentos()
BEGIN
    -- Total de medicamentos disponibles (que tengan stock > 0)
    SELECT 
        SUM(cantidad_stock) AS stock_total,
        COUNT(*) AS cantidad_medicamentos,
        SUM(CASE WHEN cantidad_stock <= stockMinimo THEN 1 ELSE 0 END) AS criticos,
        SUM(CASE WHEN cantidad_stock > stockMinimo THEN 1 ELSE 0 END) AS en_stock
    FROM 
        Medicamentos;
END //
DELIMITER ;

-- procedimiento calcula el stock total de alimentos, la cantidad de alimentos en stock y el número de alimentos con baja cantidad
DELIMITER //
CREATE PROCEDURE ObtenerResumenStockAlimentos()
BEGIN
    SELECT 
        SUM(stockActual) AS stock_total,                                 -- Stock total de todos los alimentos
        COUNT(*) AS cantidad_alimentos,                                  -- Número total de registros de alimentos
        SUM(CASE WHEN stockActual <= stockMinimo THEN 1 ELSE 0 END) AS baja_cantidad, -- Número de alimentos con stock bajo
        SUM(CASE WHEN stockActual > stockMinimo THEN 1 ELSE 0 END) AS en_stock       -- Número de alimentos en stock suficiente
    FROM 
        Alimentos;
END //
DELIMITER ;


CALL ObtenerResumenStockMedicamentos();
CALL ObtenerResumenStockAlimentos();


-- Procedimiento para Equinos Registrados
DELIMITER //
CREATE PROCEDURE ObtenerTotalEquinosRegistrados()
BEGIN
    SELECT COUNT(*) AS total_equinos FROM Equinos WHERE estado = 1; -- Estado 1 representa "Vivo" o "Activo"
END //
DELIMITER ;

-- Procedimiento para Servicios Realizados (en la semana actual)
DELIMITER //
CREATE PROCEDURE ObtenerServiciosSemanaActual()
BEGIN
    SELECT COUNT(*) AS total_servicios
    FROM Servicios
    WHERE WEEK(fechaServicio) = WEEK(CURDATE()) AND YEAR(fechaServicio) = YEAR(CURDATE());
END //
DELIMITER ;

-- Procedimiento para Medicamentos en Stock
DELIMITER //
CREATE PROCEDURE ObtenerMedicamentosEnStock()
BEGIN
    SELECT SUM(cantidad_stock) AS total_medicamentos FROM Medicamentos WHERE estado = 'Disponible';
END //
DELIMITER ;

-- Procedimiento para Alimentos en Stock
DELIMITER //
CREATE PROCEDURE ObtenerAlimentosEnStock()
BEGIN
    SELECT SUM(stockActual) AS total_alimentos FROM Alimentos WHERE estado = 'Disponible';
END //
DELIMITER ;

-- Procedimiento Almacenado para Calcular Porcentajes de Servicios
DELIMITER $$
CREATE PROCEDURE ObtenerResumenServicios()
BEGIN
    -- Total de Servicios
    SELECT COUNT(*) AS totalServicios FROM Servicios;

    -- Total de Servicios Propios
    SELECT COUNT(*) AS totalServiciosPropios FROM Servicios WHERE tipoServicio = 'Propio';

    -- Total de Servicios Mixtos
    SELECT COUNT(*) AS totalServiciosMixtos FROM Servicios WHERE tipoServicio = 'Mixto';
END $$
DELIMITER ;

--  Procedimiento Almacenado para Obtener los Servicios Realizados y el Progreso Mensual
DELIMITER $$
CREATE PROCEDURE ObtenerServiciosRealizadosMensual(IN p_meta INT)
BEGIN
    -- Contar servicios realizados en el mes actual
    SELECT COUNT(*) AS totalServiciosRealizados,
           ROUND((COUNT(*) / p_meta) * 100, 2) AS porcentajeProgreso
    FROM Servicios
    WHERE MONTH(fechaServicio) = MONTH(CURDATE()) 
      AND YEAR(fechaServicio) = YEAR(CURDATE());
END $$
DELIMITER ;




