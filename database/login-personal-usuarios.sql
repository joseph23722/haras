-- login ------------------------------------------------------------------------------------------------------------------------------------------------------
DELIMITER $$
CREATE PROCEDURE spu_usuarios_login(IN _correo VARCHAR(100))
BEGIN
    SELECT 
        USU.idUsuario,
        PER.apellidos,
        PER.nombres,
        USU.clave,
        USU.idRol  -- Asegúrate de incluir idRol en la selección
    FROM 
        Usuarios USU
    INNER JOIN 
        Personal PER ON PER.idPersonal = USU.idPersonal
    WHERE 
        USU.correo = _correo;
END $$
DELIMITER ;

-- procedimiento para buscar por Dni -----------------------------------------------------------------------------------------------------------------------------
DELIMITER $$
CREATE PROCEDURE spu_personal_buscar_dni(
    IN _nrodocumento VARCHAR(50) -- Número de documento a buscar
)
BEGIN
    -- Seleccionar los datos de la persona y del usuario asociado según el número de documento proporcionado
    SELECT 
        p.idPersonal,
        p.apellidos,
        p.nombres,
        p.nrodocumento,
        p.direccion,
        p.tipodoc,
        p.numeroHijos,
        p.fechaIngreso,
        u.correo,          -- Incluir correo del usuario
        u.clave            -- Incluir clave del usuario
    FROM 
        Personal p
    LEFT JOIN 
        Usuarios u ON p.idPersonal = u.idPersonal
    WHERE 
        p.nrodocumento = _nrodocumento;
END $$
DELIMITER ;

-- Procedimiento para registrar usuarios en la tabla 'Usuarios'------------------------------------------------------------------------------------------------------------------
DELIMITER $$
CREATE PROCEDURE spu_usuarios_registrar(
    IN _idPersonal INT,             -- ID del personal asociado al usuario
    IN _idRol INT,                  -- ID del rol del usuario
    IN _correo VARCHAR(50),         -- Correo electrónico del usuario
    IN _clave VARCHAR(100)          -- Clave del usuario
)
BEGIN
    -- Insertar un nuevo registro en la tabla 'Usuarios'
    INSERT INTO Usuarios (idPersonal, idRol, correo, clave)
    VALUES (_idPersonal, _idRol, _correo, _clave);

    -- Devolver el ID del usuario recién insertado
    SELECT LAST_INSERT_ID() AS idUsuario;
END $$
DELIMITER ;
