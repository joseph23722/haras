-- procedimiento almacenado que soporte el dashboard de "Stock de Medicamentos"
DROP PROCEDURE IF EXISTS `ObtenerResumenStockMedicamentos`;
DELIMITER //
CREATE PROCEDURE ObtenerResumenStockMedicamentos()
BEGIN
    -- Resumen de stock de medicamentos
    SELECT 
        SUM(cantidad_stock) AS stock_total,                              -- Total de cantidad de stock
        COUNT(*) AS cantidad_medicamentos,                                 -- Total de registros de medicamentos
        GROUP_CONCAT(CASE WHEN cantidad_stock <= stockMinimo THEN CONCAT(nombreMedicamento, ' (', cantidad_stock, ')') ELSE NULL END) AS criticos,  -- Lista de medicamentos con stock bajo
        GROUP_CONCAT(CASE WHEN cantidad_stock > stockMinimo THEN CONCAT(nombreMedicamento, ' (', cantidad_stock, ')') ELSE NULL END) AS en_stock, -- Lista de medicamentos en stock suficiente
        COUNT(CASE WHEN cantidad_stock <= stockMinimo THEN 1 ELSE NULL END) AS criticos_count,  -- Contador de medicamentos con stock bajo
        COUNT(CASE WHEN cantidad_stock > stockMinimo THEN 1 ELSE NULL END) AS en_stock_count  -- Contador de medicamentos en stock suficiente
    FROM 
        Medicamentos;
END //
DELIMITER ;

-- procedimiento calcula el stock total de alimentos, la cantidad de alimentos en stock y el número de alimentos con baja cantidad
DROP PROCEDURE IF EXISTS `ObtenerResumenStockAlimentos`;
DELIMITER //
CREATE PROCEDURE ObtenerResumenStockAlimentos()
BEGIN
    SELECT 
        SUM(stockActual) AS stock_total,
        COUNT(*) AS cantidad_alimentos,
        GROUP_CONCAT(CASE WHEN stockActual <= stockMinimo THEN CONCAT(nombreAlimento, ' (', stockActual, ')') ELSE NULL END) AS baja_cantidad,
        GROUP_CONCAT(CASE WHEN stockActual > stockMinimo THEN CONCAT(nombreAlimento, ' (', stockActual, ')') ELSE NULL END) AS en_stock,
        COUNT(CASE WHEN stockActual <= stockMinimo THEN 1 ELSE NULL END) AS baja_cantidad_count,
        COUNT(CASE WHEN stockActual > stockMinimo THEN 1 ELSE NULL END) AS en_stock_count
    FROM 
        Alimentos;
END //
DELIMITER ;

-- Procedimiento para Equinos Registrados
DROP PROCEDURE IF EXISTS `ObtenerTotalEquinosRegistrados`;
DELIMITER //
CREATE PROCEDURE ObtenerTotalEquinosRegistrados()
BEGIN
    SELECT COUNT(*) AS total_equinos 
    FROM Equinos 
    WHERE estado = 1  -- Estado 1 representa "Vivo" o "Activo"
    AND idPropietario IS NULL;  -- Filtrar solo los equinos sin propietario
END //
DELIMITER ;

-- Procedimiento para Servicios Realizados (en la semana actual)
DROP PROCEDURE IF EXISTS `ObtenerServiciosSemanaActual`;
DELIMITER //
CREATE PROCEDURE ObtenerServiciosSemanaActual()
BEGIN
    SELECT COUNT(*) AS total_servicios
    FROM Servicios
    WHERE WEEK(fechaServicio) = WEEK(CURDATE()) AND YEAR(fechaServicio) = YEAR(CURDATE());
END //
DELIMITER ;

-- Procedimiento Almacenado para Calcular Porcentajes de Servicios
DROP PROCEDURE IF EXISTS `ObtenerResumenServicios`;
DELIMITER $$
CREATE PROCEDURE ObtenerResumenServicios()
BEGIN
    SELECT 
        COUNT(*) AS totalServicios, 
        SUM(CASE WHEN tipoServicio = 'Propio' THEN 1 ELSE 0 END) AS totalServiciosPropios,
        SUM(CASE WHEN tipoServicio = 'Mixto' THEN 1 ELSE 0 END) AS totalServiciosMixtos
    FROM Servicios;
END $$
DELIMITER ;

--  Procedimiento Almacenado para Obtener los Servicios Realizados y el Progreso Mensual
DROP PROCEDURE IF EXISTS `ObtenerServiciosRealizadosMensual`;
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

call ObtenerServiciosRealizadosMensual(2);

DROP PROCEDURE IF EXISTS `spu_listar_fotografia_dashboard`;
DELIMITER //
CREATE PROCEDURE spu_listar_fotografia_dashboard()
BEGIN
    SELECT nombreEquino, fotografia FROM Equinos;
END //
DELIMITER ;