-- Agregados:
DELIMITER $$
CREATE PROCEDURE spu_listar_equinos_propios()
BEGIN
    SELECT 
        idEquino,
        nombreEquino,
        sexo,
        idTipoEquino
    FROM 
        Equinos
    WHERE 
        idPropietario IS NULL
        AND idTipoEquino IN (1, 2);
END $$
DELIMITER ;

-- Listar Medicamentos
DELIMITER $$
CREATE PROCEDURE listarMedicamentos()
BEGIN
    SELECT idMedicamento, nombreMedicamento
    FROM Medicamentos;
END $$
DELIMITER ;

-- Listar Haras
DELIMITER $$
CREATE PROCEDURE spu_listar_haras()
BEGIN
		SELECT DISTINCT 
        idPropietario,
        nombreHaras
    FROM Propietarios;
END $$
DELIMITER ;

-- Listar por propietarios
DELIMITER $$
CREATE PROCEDURE spu_listar_equinos_por_propietario (
    IN _idPropietario INT,
    IN _genero INT
)
BEGIN
    SELECT 
        e.idEquino,           
        e.nombreEquino,         
        p.nombreHaras            
    FROM 
        Equinos e
    JOIN 
        Propietarios p ON e.idPropietario = p.idPropietario 
    WHERE 
        e.idPropietario = _idPropietario AND  
        e.sexo = _genero;                      
END $$
DELIMITER ;

-- Listar tipo equino
DELIMITER $$
CREATE PROCEDURE spu_listar_tipoequinos()
BEGIN
    -- Listar todos los tipos de equinos disponibles
    SELECT idTipoEquino, tipoEquino
    FROM TipoEquinos;
END $$
DELIMITER ;