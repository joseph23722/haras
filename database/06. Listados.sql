DROP PROCEDURE IF EXISTS `spu_listar_equinos_propios`;
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
 END; 

DROP PROCEDURE IF EXISTS `listarMedicamentos`;

CREATE PROCEDURE listarMedicamentos()
BEGIN
    SELECT idMedicamento, nombreMedicamento
    FROM Medicamentos;
 END; 

DROP PROCEDURE IF EXISTS `spu_listar_haras`;

CREATE PROCEDURE spu_listar_haras()
BEGIN
		SELECT DISTINCT 
        idPropietario,
        nombreHaras
    FROM Propietarios;
 END; 

DROP PROCEDURE IF EXISTS `spu_listar_equinos_por_propietario`;

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
 END; 

DROP PROCEDURE IF EXISTS `spu_listar_tipoequinos`;

CREATE PROCEDURE spu_listar_tipoequinos()
BEGIN
    SELECT idTipoEquino, tipoEquino
    FROM TipoEquinos;
 END; 