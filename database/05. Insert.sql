-- Unidades de Medida
INSERT INTO UnidadesMedidaAlimento (nombreUnidad) VALUES 
    ('kg'),        -- Kilogramos, ID 1
    ('g'),         -- Gramos, ID 2
    ('t'),         -- Toneladas, ID 3
    ('L'),         -- Litros, ID 4
    ('ml'),        -- Mililitros, ID 5
    ('paca'),      -- Pacas, ID 6
    ('cubeta'),    -- Cubetas, ID 7
    ('fardo'),     -- Fardos, ID 8
    ('sacos'),      -- Sacos, ID 9
    ('bloque'),    -- Bloques, ID 10
    ('mg'),        -- Miligramos, ID 11
    ('cc'),        -- Centímetros cúbicos, ID 12
    ('tableta'),   -- Tabletas, ID 13
    ('cápsula'),   -- Cápsulas, ID 14
    ('ración'),    -- Raciones, ID 15
    ('dosificador'); -- Dosificador, ID 16
    
-- Tipos de Alimentos
INSERT INTO TipoAlimentos (tipoAlimento) VALUES 
    ('Forrajes'),                            -- ID 1
    ('Granos y Cereales'),                   -- ID 2
    ('Suplementos y Concentrados'),          -- ID 3
    ('Subproductos de la Agricultura'),      -- ID 4
    ('Proteínas y Energéticos'),             -- ID 5
    ('Heno y Pasto Preservado'),             -- ID 6
    ('Fibras'),                              -- ID 7
    ('Complementos Nutricionales'),          -- ID 8
    ('Hierbas Medicinales'),                 -- ID 9
    ('Alimentos Especializados para Caballos Deportivos'); -- ID 10


-- Relación entre Tipos de Alimentos y Unidades de Medida
-- Forraje 
INSERT INTO TipoAlimento_UnidadMedida (idTipoAlimento, idUnidadMedida) VALUES 
    (1, 1),  -- kg
    (1, 6),  -- paca
    (1, 8),  -- fardo
    (1, 3);  -- t

-- Granos y Cereales
INSERT INTO TipoAlimento_UnidadMedida (idTipoAlimento, idUnidadMedida) VALUES 
    (2, 1),  -- kg
    (2, 2),  -- g
    (2, 9),  -- saco
    (2, 3);  -- t

-- Suplementos y Concentrados
INSERT INTO TipoAlimento_UnidadMedida (idTipoAlimento, idUnidadMedida) VALUES 
    (3, 1),  -- kg
    (3, 2),  -- g
    (3, 10), -- bloque
    (3, 13); -- tableta

-- Subproductos de la Agricultura
INSERT INTO TipoAlimento_UnidadMedida (idTipoAlimento, idUnidadMedida) VALUES 
    (4, 1),  -- kg
    (4, 2),  -- g
    (4, 9);  -- saco

-- Proteínas y Energéticos
INSERT INTO TipoAlimento_UnidadMedida (idTipoAlimento, idUnidadMedida) VALUES 
    (5, 1),  -- kg
    (5, 4),  -- L
    (5, 5);  -- ml

-- Heno y Pasto Preservado
INSERT INTO TipoAlimento_UnidadMedida (idTipoAlimento, idUnidadMedida) VALUES 
    (6, 1),  -- kg
    (6, 6),  -- paca
    (6, 8);  -- fardo

-- Fibras
INSERT INTO TipoAlimento_UnidadMedida (idTipoAlimento, idUnidadMedida) VALUES 
    (7, 1),  -- kg
    (7, 2),  -- g
    (7, 8);  -- fardo

-- Complementos Nutricionales
INSERT INTO TipoAlimento_UnidadMedida (idTipoAlimento, idUnidadMedida) VALUES 
    (8, 1),  -- kg
    (8, 2),  -- g
    (8, 15); -- ración

-- Hierbas Medicinales
INSERT INTO TipoAlimento_UnidadMedida (idTipoAlimento, idUnidadMedida) VALUES 
    (9, 2),  -- g
    (9, 11); -- mg

-- Alimentos Especializados para Caballos Deportivos
INSERT INTO TipoAlimento_UnidadMedida (idTipoAlimento, idUnidadMedida) VALUES 
    (10, 1),  -- kg
    (10, 4),  -- L
    (10, 14), -- cápsula
    (10, 16); -- dosificador

-- 1. Insertar Datos en TipoInventarios
INSERT INTO TipoInventarios (nombreInventario) 
VALUES 
('Implementos Equinos'),
('Implementos Campos');

-- 2. Insertar Datos en Personal
INSERT INTO Personal (nombres, apellidos, direccion, tipodoc, nrodocumento, fechaIngreso, tipoContrato)
VALUES 
('Gerente', 'Mateo', 'San Agustin ', 'DNI', '11111111', '2024-08-27', 'Completo'),
('Administrador', 'Marcos', 'Calle Fatima', 'DNI', '22222222', '2024-08-27', 'Completo'),
('SupervisorE', 'Gereda', 'AV. Los Angeles', 'DNI', '33333333', '2024-08-27', 'Completo'),
('SupervisorC', 'Mamani', 'Calle Fatima', 'DNI', '44444444', '2024-08-27', 'Completo'),
('Medico', 'Paullac', 'Calle Fatima', 'DNI', '55555555', '2024-08-27', 'Completo'),
('Herrero', 'Nuñez', 'Calle Fatima', 'DNI', '66666666', '2024-08-27', 'Parcial');

-- 3. Insertar datos en la tabla Roles
INSERT INTO Roles (nombreRol) 
VALUES
('Gerente'), 
('Administrador'),
('Supervisor Equino'),
('Supervisor Campo'),
('Médico'),
('Herrero');

-- Las contraseñas de los 6 usuarios es "haras", cabe mencionar que estan ordenadas conforme se encuentran los roles
INSERT INTO Usuarios (idPersonal, correo, clave, idRol) 
VALUES 
(1, 'gerente', '$2y$10$RaoPTBz9oVETRVocodEaWuwxQPjshzARRmDnGZcWcDY43YxNF/sIa', 1),
(2, 'admin', '$2y$10$RaoPTBz9oVETRVocodEaWuwxQPjshzARRmDnGZcWcDY43YxNF/sIa', 2),
(3, 'superE', '$2y$10$RaoPTBz9oVETRVocodEaWuwxQPjshzARRmDnGZcWcDY43YxNF/sIa', 3),
(4, 'superC', '$2y$10$RaoPTBz9oVETRVocodEaWuwxQPjshzARRmDnGZcWcDY43YxNF/sIa', 4),
(5, 'medico', '$2y$10$RaoPTBz9oVETRVocodEaWuwxQPjshzARRmDnGZcWcDY43YxNF/sIa', 5),
(6, 'herrero', '$2y$10$RaoPTBz9oVETRVocodEaWuwxQPjshzARRmDnGZcWcDY43YxNF/sIa', 6);

-- 5. Insertar Datos en Propietarios
INSERT INTO Propietarios (nombreHaras) 
    VALUES  ('Los Eucaliptos');

-- 6. Insertar Datos en TipoMovimientos
INSERT INTO TipoMovimientos (movimiento) 
    VALUES 
        ('Entrada'),
        ('Salida');
        
INSERT INTO TipoRotaciones (nombreRotacion)
 VALUES ('Riego'), ('Deshierve'),
		('Arado'), ('Gradeado'),
        ('Rufiado'), ('Potrillo'),
        ('Potranca'),('Yeguas Preñadas'),
        ('Yeguas con Crías'), ('Yeguas Vacías'),
        ('Destetados');

-- 3. Insertar Datos en TipoEquinos
INSERT INTO TipoEquinos (tipoEquino) VALUES 
    ('Yegua'), 
    ('Padrillo'), 
    ('Potranca'), 
    ('Potrillo'),
    ('Recién nacido');
    
INSERT INTO tipoSuelo (nombreTipoSuelo) VALUES
	('Arcilloso'), ('Arenoso'), ('Calizo'), 
    ('Humiferos'), ('Mixto'), ('Pedregoso'), 
    ('Salino'), ('Urbano');
    
-- 4. Insertar Datos en EstadoMonta
INSERT INTO EstadoMonta (genero, nombreEstado) 
    VALUES 
        ('Macho', 'Activo'),
        ('Macho', 'Inactivo'),
        ('Hembra','Preñada'),
		('Hembra','Servida'),
        ('Hembra','S/S'),
        ('Hembra','Por Servir'),
        ('Hembra','Vacía'),
        ('Hembra','Con Cria');

-- 28/10/2024
INSERT INTO modulos (modulo) VALUES
	('campos'), -- 1
    ('equinos'), -- 2
    ('historialMedico'), -- 3
    ('inventarios'), -- 4
    ('reportes'), -- 5
    ('servicios'), -- 6
    ('usuarios'); -- 7
    
-- HOME
INSERT INTO vistas (idmodulo, ruta, sidebaroption, texto, icono) VALUES
	(NULL, 'home', 'S', 'Inicio', 'fas fa-home');
    
-- Campos
INSERT INTO vistas (idmodulo, ruta, sidebaroption, texto, icono) VALUES
	(1, 'rotar-campo', 'S', 'Campos', 'fa-solid fa-group-arrows-rotate'),
    (1, 'programar-rotacion', 'S', 'Rotacion Campos', 'fa-solid fa-calendar-days');
    
-- Equinos
INSERT INTO vistas (idmodulo, ruta, sidebaroption, texto, icono) VALUES
    (2, 'historial-equino', 'S', 'Historial Equinos', 'fas fa-history'),
	(2, 'listar-bostas', 'S', 'Listado Bostas', 'fa-solid fa-list'),
	(2, 'listar-equino', 'S', 'Listado Equinos', 'fa-solid fa-list'),
    (2, 'mostrar-foto', 'S', 'Colección de Fotos', 'fa-solid fa-image'),
	(2, 'registrar-bostas', 'S', 'Registro Bostas', 'fas fa-poop'),
	(2, 'registrar-equino', 'S', 'Registro Equinos', 'fa-solid fa-horse');
    
-- historialMedico
INSERT INTO vistas (idmodulo, ruta, sidebaroption, texto, icono) VALUES
	(3, 'diagnosticar-equino', 'N', NULL, NULL),
	(3, 'listar-diagnostico-avanzado', 'N', NULL, NULL),
	(3, 'revisar-equino', 'N', NULL, NULL),
	(3, 'seleccionar-diagnostico', 'S', 'Diagnóstico', 'fa-solid fa-notes-medical');

-- Inventarios
INSERT INTO vistas (idmodulo, ruta, sidebaroption, texto, icono) VALUES
	(4, 'administrar-alimento', 'S', 'Alimentos', 'fas fa-apple-alt'),
	(4, 'administrar-herramienta', 'S', 'Herrero', 'fas fa-wrench'),
	(4, 'administrar-medicamento', 'S', 'Medicamentos', 'fas fa-pills'),
	(4, 'listar-accion-herrero', 'N', NULL, NULL),
	(4, 'listar-alimento', 'N', NULL, NULL),
	(4, 'listar-implemento-caballo', 'N', NULL, NULL),
	(4, 'listar-implemento-campo', 'N', NULL, NULL),
	(4, 'listar-medicamento', 'N', NULL, NULL),
    (4, 'registrar-implemento-caballo', 'S', 'Implementos Caballos', 'fa-solid fa-scissors'),
    (4, 'registrar-implemento-campo', 'S', 'Implementos Campos', 'fa-solid fa-wrench');

-- Reportes
INSERT INTO vistas (idmodulo, ruta, sidebaroption, texto, icono) VALUES
	(5, 'presionar-boton-reporte', 'S', 'Reportes', 'fa-solid fa-file-circle-plus');
    
-- Servicios
INSERT INTO vistas (idmodulo, ruta, sidebaroption, texto, icono) VALUES
	(6, 'listar-medicamento-usado', 'N', NULL, NULL),
	(6, 'listar-servicio', 'S', 'Listado Servicios', 'fa-solid fa-list'),
	(6, 'servir-mixto', 'S', 'Servicio Mixto', 'fas fa-exchange-alt'),
    (6, 'servir-propio', 'S', 'Servicio Propio', 'fas fa-tools');
    
-- Usuarios
INSERT INTO vistas (idmodulo, ruta, sidebaroption, texto, icono) VALUES
	(7, 'actualizar-contrasenia', 'S', 'Actualizar Contraseña', 'fas fa-key'),
	(7, 'registrar-personal', 'S', 'Registrar Personal', 'fa-solid fa-wallet');

-- Inventarios
INSERT INTO vistas (idmodulo, ruta, sidebaroption, texto, icono) VALUES
	(4, 'listar-historial-medicamento', 'N', NULL, NULL),
	(4, 'listar-historial-alimento', 'N', NULL, NULL),
	(4, 'listar-historial-I-caballo', 'N', NULL, NULL),
    (4, 'listar-historial-I-campo', 'N', NULL, NULL);

INSERT INTO vistas (idmodulo, ruta, sidebaroption, texto, icono) VALUES
    (2, 'listar-equino-externo', 'S', 'Listado Equinos Ajenos', 'fas fa-file-alt');

-- Gerente
INSERT INTO permisos (idRol, idvista) VALUES
	(1, 1),
    (1, 4),
    (1, 5),
    (1, 6),
    (1, 7),
    (1, 11),
    (1, 17),
    (1, 18),
    (1, 19),
    (1, 20),
    (1, 21),
	(1, 24),
    (1, 25),
    (1, 26),
    (1, 29);

-- Administrador
INSERT INTO permisos (idRol, idvista) VALUES
	(2, 1),
    (2, 4),
    (2, 5),
    (2, 6),
    (2, 7),
    (2, 11),
    (2, 17),
    (2, 18),
    (2, 19),
    (2, 20),
    (2, 21),
    (2, 24),
    (2, 25),
    (2, 26),
    (2, 30),
    (2, 29);

-- Supervisor Equino
INSERT INTO permisos (idRol, idvista) VALUES
	(3, 1),
	(3, 4),
    (3, 6),
    (3, 7),
    (3, 9),
    (3, 10),
    (3, 11),
    (3, 12),
    (3, 13),
    (3, 14),
    (3, 15),
    (3, 16),
    (3, 17),
    (3, 18),
    (3, 19),
    (3, 21),
    (3, 22),
    (3, 25),
    (3, 26),
    (3, 27),
    (3, 28),
    (3, 30),
    (3, 29),
    (3, 31),
    (3, 32),
    (3, 33),
    (3, 34),
    (3, 35);
    
-- Supervisor Campo
INSERT INTO permisos (idRol, idvista) VALUES
	(4, 1),
	(4, 2),
    (4, 3),
    (4, 5),
    (4, 6),
    (4, 8),
    (4, 20),
    (4, 23),
    (4, 29),
    (4, 35); -- modificar al 34 por un error en orden

-- Medico
INSERT INTO permisos (idRol, idvista) VALUES
	(5, 1),
    (5, 6),
    (5, 10),
    (5, 11),
    (5, 12),
	(5, 13),
    (5, 29);
    
-- Herrero
INSERT INTO permisos (idRol, idvista) VALUES
	(6, 1),
	(6, 6),
    (6, 15),
    (6, 17),
    (6, 29);

INSERT INTO Nacionalidades (nacionalidad) VALUES 
('Afgana'), ('Alemana'), ('Andorrana'), ('Angoleña'), ('Antiguana'), ('Árabe'), ('Argelina'), ('Argentina'),
('Armenia'), ('Arubeña'), ('Australiana'), ('Austriaca'), ('Azerbaiyana'), ('Bahameña'), ('Bahreiní'), ('Bangladesí'), 
('Barbadense'), ('Belga'), ('Beliceña'), ('Beninesa'), ('Bermudeña'), ('Bielorrusa'), ('Boliviana'), ('Bosnia'),
('Botsuana'), ('Brasileña'), ('Bruneana'), ('Búlgara'), ('Burkinesa'), ('Burundesa'), ('Butanesa'), ('Caboverdiana'),
('Camboyana'), ('Camerunesa'), ('Canadiense'), ('Centroafricana'), ('Chadiana'), ('Checa'), ('Chilena'), ('China'),
('Chipriota'), ('Colombiana'), ('Comorense'), ('Congoleña'), ('Costarricense'), ('Croata'), ('Cubana'), ('Danesa'),
('Dominicana'), ('Ecuatoriana'), ('Egipcia'), ('Emiratí'), ('Eritrea'), ('Eslovaca'), ('Eslovena'), ('Española'), ('Estadounidense'),
('Estonia'), ('Etíope'), ('Filipina'), ('Finlandesa'), ('Fiyiana'), ('Francesa'), ('Gabonesa'), ('Galesa'), ('Gambiana'),
('Georgiana'), ('Ghanesa'), ('Granadina'), ('Griega'), ('Guatemalteca'), ('Guineana'), ('Guyanesa'), ('Haitiana'), ('Hondureña'),
('Húngara'), ('India'), ('Indonesia'), ('Iraquí'), ('Iraní'), ('Irlandesa'), ('Islandesa'), ('Israelí'), ('Italiana'), ('Jamaiquina'), ('Japonesa'),
('Jordana'), ('Kazaja'), ('Keniana'), ('Kirguisa'), ('Kiribatiana'), ('Kosovar'), ('Kuwaití'), ('Laosiana'), ('Lesotense'), ('Letona'), ('Libanesa'),
('Liberiana'), ('Libia'), ('Liechtensteiniana'), ('Lituana'), ('Luxemburguesa'), ('Macedonia'), ('Malaya'), ('Malauí'), ('Maldiva'),
('Malgache'), ('Maliense'), ('Maltesa'), ('Marfileña'), ('Marroquí'), ('Marshallina'), ('Mauritana'), ('Mauriciana'), ('Mexicana'), ('Micronesia'),
('Moldava'), ('Monegasca'), ('Mongola'), ('Montenegrina'), ('Mozambiqueña'), ('Namibia'), ('Neerlandesa'), ('Nepalí'), ('Nicaragüense'), ('Nigeriana'),
('Nigerina'), ('Norcoreana'), ('Noruega'), ('Nueva Zelandesa'), ('Omaní'), ('Pakistaní'),  ('Palauana'),('Panameña'), ('Papú'),
('Paraguaya'), ('Peruana'), ('Polaca'), ('Portuguesa'), ('Puertorriqueña'), ('Qatarí'), ('Reino Unido'), ('Rumana'), ('Rusa'), ('Ruandesa'),
('Salvadoreña'), ('Samoana'), ('Sanmarinense'), ('Santa Lucía'), ('Saudí'), ('Senegalesa'), ('Serbia'), ('Seychellense'), ('Sierraleonesa'), ('Singapurense'),
('Somalí'), ('Sri Lanka'), ('Sudafricana'), ('Sudanesa'), ('Sueca'), ('Suiza'), ('Surcoreana'), ('Surinamesa'), ('Tailandesa'),
('Tanzana'), ('Togolesa'), ('Tongana'), ('Trinitaria'), ('Tunecina'), ('Turca'), ('Tuvaluana'), ('Ucraniana'), ('Ugandesa'),
('Uruguaya'), ('Uzbeca'), ('Vanuatuense'), ('Venezolana'), ('Vietnamita'), ('Yemení'), ('Yibutiana'), ('Zambiana'), ('Zimbabuense');