DROP PROCEDURE IF EXISTS `spu_registrar_bosta`;

CREATE PROCEDURE `spu_registrar_bosta`(
    IN p_fecha DATE,
    IN p_cantidadsacos INT,
    IN p_pesoaprox DECIMAL(4,2)
)
BEGIN
    DECLARE v_peso_diario DECIMAL(9,2);
    DECLARE v_peso_semanal DECIMAL(9,2);
    DECLARE v_peso_mensual DECIMAL(12,2);
    DECLARE v_numero_semana INT;
    DECLARE v_mensaje_error VARCHAR(255);

    -- Verificar si la fecha es mayor a la fecha actual
    IF p_fecha > CURRENT_DATE THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La fecha no puede ser mayor a la fecha actual.';
    END IF;

    -- Verificar si la fecha es domingo
    IF DAYOFWEEK(p_fecha) = 1 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se permite registrar datos los domingos';
    ELSE
        -- Verificar si ya existe un registro para esta fecha
        IF EXISTS (SELECT 1 FROM bostas WHERE fecha = p_fecha) THEN
            SET v_mensaje_error = CONCAT('Ya existe un registro para esta fecha: ', p_fecha);
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_mensaje_error;
        ELSE
            -- Calcular el número de semana y el peso diario
            SET v_numero_semana = WEEK(p_fecha, 1);
            SET v_peso_diario = p_cantidadsacos * p_pesoaprox;

            -- Calcular el peso semanal (incluyendo el peso diario actual)
            SELECT COALESCE(SUM(peso_diario), 0) + v_peso_diario
            INTO v_peso_semanal
            FROM bostas
            WHERE WEEK(fecha, 1) = v_numero_semana
              AND YEAR(fecha) = YEAR(p_fecha);  -- Coincide con el año

            -- Calcular el peso mensual (incluyendo el peso diario actual)
            SELECT COALESCE(SUM(peso_diario), 0) + v_peso_diario
            INTO v_peso_mensual
            FROM bostas
            WHERE MONTH(fecha) = MONTH(p_fecha)
              AND YEAR(fecha) = YEAR(p_fecha);  -- Coincide con el año

            -- Insertar el registro con el peso calculado
            INSERT INTO bostas (fecha, cantidadsacos, pesoaprox, peso_diario, peso_semanal, peso_mensual, numero_semana)
            VALUES (
                p_fecha,
                p_cantidadsacos,
                p_pesoaprox,
                v_peso_diario,
                v_peso_semanal,
                v_peso_mensual,
                v_numero_semana
            );
        END IF;
    END IF;
END ;

DROP PROCEDURE IF EXISTS `spu_obtener_pesos`;

CREATE PROCEDURE `spu_obtener_pesos`()
BEGIN
    DECLARE v_peso_semanal DECIMAL(9,2);
    DECLARE v_peso_mensual DECIMAL(12,2);
    
    -- Calcular el peso semanal (desde el lunes hasta la fecha actual)
    SELECT COALESCE(SUM(peso_diario), 0)
    INTO v_peso_semanal
    FROM bostas
    WHERE WEEK(fecha, 1) = WEEK(CURDATE(), 1) 
      AND YEAR(fecha) = YEAR(CURDATE())
      AND fecha <= CURDATE(); -- Solo hasta la fecha actual

    -- Calcular el peso mensual (todo el mes actual)
    SELECT COALESCE(SUM(peso_diario), 0)
    INTO v_peso_mensual
    FROM bostas
    WHERE MONTH(fecha) = MONTH(CURDATE())
      AND YEAR(fecha) = YEAR(CURDATE());

    -- Devolver resultados sin el peso diario
    SELECT v_peso_semanal AS peso_semanal,
           v_peso_mensual AS peso_mensual;
END ;

DROP PROCEDURE IF EXISTS `spu_listar_bostas`;

CREATE PROCEDURE `spu_listar_bostas`()
BEGIN
    SELECT 
        b.idbosta,
        b.fecha,
        b.cantidadsacos,
        b.pesoaprox,
        b.peso_diario,
        CASE 
            WHEN ROW_NUMBER() OVER (PARTITION BY b.numero_semana ORDER BY b.fecha) = 1 THEN 
                (SELECT SUM(peso_diario) 
                 FROM bostas 
                 WHERE WEEK(fecha, 1) = b.numero_semana 
                   AND YEAR(fecha) = YEAR(b.fecha)) 
            ELSE NULL 
        END AS peso_semanal,
        b.numero_semana,
        (SELECT SUM(peso_diario) 
         FROM bostas 
         WHERE fecha <= CURDATE()) AS total_acumulado
    FROM 
        bostas b
    ORDER BY 
        b.numero_semana DESC,
        b.fecha ASC;
END ;

DROP PROCEDURE IF EXISTS `spu_eliminar_bosta`;

CREATE PROCEDURE `spu_eliminar_bosta`(
    IN p_idbosta INT
)
BEGIN
    -- Eliminar el registro
    DELETE FROM bostas
    WHERE idbosta = p_idbosta;

    -- Opcional: verificar si la eliminación fue exitosa
    IF ROW_COUNT() = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se encontró un registro con el ID proporcionado.';
    END IF;
END ;

DROP PROCEDURE IF EXISTS `spu_editar_bosta`;

CREATE PROCEDURE `spu_editar_bosta`(
    IN p_idbosta INT,
    IN p_fecha DATE,
    IN p_cantidadsacos INT,
    IN p_pesoaprox DECIMAL(4,2)
)
BEGIN
    -- Verificar si la fecha es mayor a la fecha actual
    IF p_fecha > CURRENT_DATE THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La fecha no puede ser mayor a la fecha actual.';
    END IF;

    -- Verificar si la fecha es domingo
    IF DAYOFWEEK(p_fecha) = 1 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se permite registrar datos los domingos';
    ELSE
        -- Actualizar el registro
        UPDATE bostas
        SET 
            fecha = p_fecha,
            cantidadsacos = p_cantidadsacos,
            pesoaprox = p_pesoaprox,
            peso_diario = p_cantidadsacos * p_pesoaprox,
            numero_semana = WEEK(p_fecha, 1) -- Actualiza el número de semana
        WHERE idbosta = p_idbosta;
    END IF;
END ;