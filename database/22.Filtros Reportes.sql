-- reportes alimentos 
DROP PROCEDURE IF EXISTS `spu_filtrarAlimentos`;
DELIMITER $$
CREATE PROCEDURE spu_filtrarAlimentos(
    IN _fechaCaducidadInicio DATE,
    IN _fechaCaducidadFin DATE,
    IN _fechaRegistroInicio DATETIME,
    IN _fechaRegistroFin DATETIME
)
BEGIN
    -- Realizar la consulta de los alimentos con los filtros especificados
    SELECT 
        A.idAlimento,
        A.idUsuario,
        A.nombreAlimento,
        TA.tipoAlimento AS nombreTipoAlimento,
        A.stockActual,
        A.stockMinimo,
        A.estado,
        U.nombreUnidad AS unidadMedidaNombre,
        A.costo,
        A.idLote,
        A.idEquino,
        A.compra,
        A.fechaMovimiento,
        L.idLote AS loteId,
        L.lote,
        L.fechaCaducidad,
        L.fechaIngreso,
        L.estadoLote
    FROM 
        Alimentos A
    INNER JOIN 
        LotesAlimento L ON A.idLote = L.idLote
    INNER JOIN 
        TipoAlimentos TA ON A.idTipoAlimento = TA.idTipoAlimento
    INNER JOIN 
        UnidadesMedidaAlimento U ON A.idUnidadMedida = U.idUnidadMedida
    WHERE 
        (_fechaCaducidadInicio IS NULL OR L.fechaCaducidad >= _fechaCaducidadInicio)
        AND (_fechaCaducidadFin IS NULL OR L.fechaCaducidad <= _fechaCaducidadFin)
        AND (_fechaRegistroInicio IS NULL OR L.fechaIngreso >= _fechaRegistroInicio)
        AND (_fechaRegistroFin IS NULL OR L.fechaIngreso <= _fechaRegistroFin);

END $$
DELIMITER ;

CALL spu_filtrarAlimentos('2023-02-28', '2028-10-30', NULL, NULL);

CALL spu_filtrarAlimentos('2024-11-05', '2025-01-16', NULL, NULL);
