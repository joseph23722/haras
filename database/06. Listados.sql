DROP PROCEDURE IF EXISTS `spu_listar_equinos_propios`;
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
        AND idTipoEquino IN (1, 2)
        AND estado = 1;
END $$
DELIMITER ;

DROP PROCEDURE IF EXISTS `listarMedicamentos`;
DELIMITER $$
CREATE PROCEDURE listarMedicamentos()
BEGIN
    SELECT idMedicamento, nombreMedicamento
    FROM Medicamentos;
END $$
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_listar_haras`;
DELIMITER $$
CREATE PROCEDURE spu_listar_haras()
BEGIN
		SELECT DISTINCT 
        idPropietario,
        nombreHaras
    FROM Propietarios;
END $$
DELIMITER ;

DROP PROCEDURE IF EXISTS `spu_listar_equinos_por_propietario`;
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

DROP PROCEDURE IF EXISTS `spu_listar_tipoequinos`;
DELIMITER $$
CREATE PROCEDURE spu_listar_tipoequinos()
BEGIN
    SELECT idTipoEquino, tipoEquino
    FROM TipoEquinos;
END $$
DELIMITER ;