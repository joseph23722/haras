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

-- reporte historial medico (veterinario)
DROP PROCEDURE IF EXISTS `spu_filtrar_historial_medicoMedi`;
DELIMITER $$
CREATE PROCEDURE spu_filtrar_historial_medicoMedi(
    IN _nombreEquino VARCHAR(255),
    IN _nombreMedicamento VARCHAR(255),
    IN _estadoTratamiento VARCHAR(50)
)
BEGIN
    -- Desactivar el modo seguro temporalmente
    SET SQL_SAFE_UPDATES = 0;

    -- Actualizar el estado de los tratamientos en la tabla DetalleMedicamentos
    -- Cambiar a 'Finalizado' los tratamientos cuya fecha de fin ha pasado y que están en estado 'Activo'
    UPDATE DetalleMedicamentos
    SET estadoTratamiento = 'Finalizado'
    WHERE fechaFin < CURDATE() AND estadoTratamiento = 'Activo';

    -- Seleccionar la información detallada de los registros de historial médico con filtros aplicados
    SELECT 
        DM.idDetalleMed AS idRegistro,
        DM.idEquino,
        E.nombreEquino,
        DM.idMedicamento,
        M.nombreMedicamento,
        DM.dosis,
        DM.frecuenciaAdministracion,
        VA.nombreVia AS viaAdministracion,  -- Obtener solo el nombre de la vía desde la tabla ViasAdministracion
        E.pesokg,
        DM.fechaInicio,
        DM.fechaFin,
        DM.observaciones,
        DM.reaccionesAdversas,
        DM.idUsuario AS responsable,
        DM.tipoTratamiento,
        DM.estadoTratamiento,       -- Incluir el estado del tratamiento
        DM.fechaInicio AS fechaRegistro
    FROM 
        DetalleMedicamentos DM
    INNER JOIN 
        Medicamentos M ON DM.idMedicamento = M.idMedicamento
    INNER JOIN 
        Equinos E ON DM.idEquino = E.idEquino
    INNER JOIN 
        Usuarios U ON DM.idUsuario = U.idUsuario
    LEFT JOIN 
        ViasAdministracion VA ON DM.idViaAdministracion = VA.idViaAdministracion  -- Vincular con la tabla ViasAdministracion
    WHERE 
        (E.nombreEquino LIKE CONCAT('%', _nombreEquino, '%') OR _nombreEquino IS NULL OR _nombreEquino = '')
        AND (M.nombreMedicamento LIKE CONCAT('%', _nombreMedicamento, '%') OR _nombreMedicamento IS NULL OR _nombreMedicamento = '')
        AND (DM.estadoTratamiento = _estadoTratamiento OR _estadoTratamiento IS NULL OR _estadoTratamiento = '')
    ORDER BY 
        DM.fechaInicio DESC;

    -- Reactivar el modo seguro
    SET SQL_SAFE_UPDATES = 1;
END $$
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_listar_medicamentos`;
DELIMITER $$
CREATE PROCEDURE spu_listar_medicamentos()
BEGIN
    SELECT 
        idMedicamento,
        nombreMedicamento
    FROM 
        Medicamentos
    ORDER BY 
        nombreMedicamento ASC;
END $$
DELIMITER ;


-- reprote medicamento 