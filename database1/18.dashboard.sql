DROP PROCEDURE IF EXISTS `ObtenerResumenStockMedicamentos`;
DELIMITER //
CREATE PROCEDURE ObtenerResumenStockMedicamentos()
BEGIN
    SELECT 
        SUM(cantidad_stock) AS stock_total,
        COUNT(*) AS cantidad_medicamentos,
        GROUP_CONCAT(CASE WHEN cantidad_stock <= stockMinimo THEN CONCAT(nombreMedicamento, ' (', cantidad_stock, ')') ELSE NULL END) AS criticos,
        GROUP_CONCAT(CASE WHEN cantidad_stock > stockMinimo THEN CONCAT(nombreMedicamento, ' (', cantidad_stock, ')') ELSE NULL END) AS en_stock,
        COUNT(CASE WHEN cantidad_stock <= stockMinimo THEN 1 ELSE NULL END) AS criticos_count,
        COUNT(CASE WHEN cantidad_stock > stockMinimo THEN 1 ELSE NULL END) AS en_stock_count
    FROM 
        Medicamentos;
END //
DELIMITER ;

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

DROP PROCEDURE IF EXISTS `ObtenerTotalEquinosRegistrados`;
DELIMITER //
CREATE PROCEDURE ObtenerTotalEquinosRegistrados()
BEGIN
    SELECT COUNT(*) AS total_equinos 
    FROM Equinos 
    WHERE estado = 1
    AND idPropietario IS NULL;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS `ObtenerServiciosSemanaActual`;
DELIMITER //
CREATE PROCEDURE ObtenerServiciosSemanaActual()
BEGIN
    SELECT COUNT(*) AS total_servicios
    FROM Servicios
    WHERE WEEK(fechaServicio) = WEEK(CURDATE()) AND YEAR(fechaServicio) = YEAR(CURDATE());
END //
DELIMITER ;

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

DROP PROCEDURE IF EXISTS `ObtenerServiciosRealizadosMensual`;
DELIMITER $$
CREATE PROCEDURE ObtenerServiciosRealizadosMensual(IN p_meta INT)
BEGIN
    SELECT COUNT(*) AS totalServiciosRealizados,
           ROUND((COUNT(*) / p_meta) * 100, 2) AS porcentajeProgreso
    FROM Servicios
    WHERE MONTH(fechaServicio) = MONTH(CURDATE()) 
      AND YEAR(fechaServicio) = YEAR(CURDATE());
END $$
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_listar_fotografia_dashboard`;
DELIMITER //
CREATE PROCEDURE spu_listar_fotografia_dashboard()
BEGIN
    SELECT nombreEquino, fotografia FROM Equinos
    WHERE idPropietario IS NULL;
END //
DELIMITER ;