DROP PROCEDURE IF EXISTS `spu_registrar_propietario`;
DELIMITER $$
CREATE PROCEDURE spu_registrar_propietario(
    OUT _idPropietario INT,          -- Salida: id del propietario insertado
    IN _nombreHaras VARCHAR(100)     -- Entrada: nombre del propietario
)
BEGIN
    -- Declaración de una variable para capturar errores
    DECLARE existe_error INT DEFAULT 0;
    DECLARE nombre_existente INT;

    -- Manejo de errores de SQL
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
    BEGIN
        SET existe_error = 1;
    END;

    -- Verificar si el nombre ya existe en la base de datos
    SELECT COUNT(*) INTO nombre_existente
    FROM Propietarios
    WHERE nombreHaras = _nombreHaras;

    -- Si el nombre ya existe, asignar un valor de error y salir del procedimiento
    IF nombre_existente > 0 THEN
        SET _idPropietario = -2; -- Error: El nombre ya existe
    ELSE
        -- Si el nombre no existe, realizar la inserción
        INSERT INTO Propietarios (nombreHaras) 
        VALUES (_nombreHaras);

        -- Verificar si ocurrió un error en la inserción
        IF existe_error = 1 THEN
            SET _idPropietario = -1; -- Error en la inserción
        ELSE
            SET _idPropietario = LAST_INSERT_ID(); -- Devuelve el id del nuevo propietario
        END IF;
    END IF;

END $$
DELIMITER ;

CALL spu_registrar_propietario(@id, 'Haras Hasmide');
SELECT @id AS idPropietario;