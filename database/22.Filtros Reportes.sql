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

-- reporte herrero
DROP PROCEDURE IF EXISTS `FiltrarHistorialHerreroPorTipoEquino`;
DELIMITER $$
CREATE PROCEDURE FiltrarHistorialHerreroPorTipoEquino(
    IN _tipoEquino VARCHAR(50)
)
BEGIN
    SELECT 
        HH.idHistorialHerrero, 
        HH.fecha, 
        TT.nombreTrabajo AS TrabajoRealizado, 
        GROUP_CONCAT(H.nombreHerramienta SEPARATOR ', ') AS HerramientasUsadas, 
        HH.observaciones,
        E.nombreEquino,              
        TE.tipoEquino                 
    FROM 
        HistorialHerrero HH
    INNER JOIN 
        Equinos E ON HH.idEquino = E.idEquino
    INNER JOIN 
        TipoEquinos TE ON E.idTipoEquino = TE.idTipoEquino
    INNER JOIN 
        TiposTrabajos TT ON HH.idTrabajo = TT.idTipoTrabajo
    LEFT JOIN 
        HerramientasUsadasHistorial HUH ON HH.idHistorialHerrero = HUH.idHistorialHerrero
    LEFT JOIN 
        Herramientas H ON HUH.idHerramienta = H.idHerramienta
    WHERE 
        TE.tipoEquino = _tipoEquino
        AND TE.tipoEquino IN ('Padrillo', 'Yegua', 'Potrillo', 'Potranca')
    GROUP BY 
        HH.idHistorialHerrero, 
        HH.fecha, 
        TT.nombreTrabajo, 
        HH.observaciones, 
        E.nombreEquino, 
        TE.tipoEquino
    ORDER BY 
        HH.fecha DESC;
END $$
DELIMITER ;

CALL FiltrarHistorialHerreroPorTipoEquino('Padrillo');