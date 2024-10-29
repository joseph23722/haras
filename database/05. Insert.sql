-- 1. Insertar Datos en TipoInventarios
INSERT INTO TipoInventarios (nombreInventario) 
VALUES 
('Implementos Equinos'),
('Implementos Campos');

-- 2. Insertar Datos en Personal
INSERT INTO Personal (nombres, apellidos, direccion, tipodoc, nrodocumento, numeroHijos, fechaIngreso, tipoContrato)
VALUES 
('Gerente', 'Mateo', 'San Agustin ', 'DNI', '11111111', 1, '2024-08-27', 'Completo'),
('Administrador', 'Marcos', 'Calle Fatima', 'DNI', '22222222', 2, '2024-08-27', 'Completo'),
('SupervisorE', 'Gereda', 'AV. Los Angeles', 'DNI', '33333333', 3, '2024-08-27', 'Completo'),
('SupervisorC', 'Mamani', 'Calle Fatima', 'DNI', '44444444', '', '2024-08-27', 'Completo'),
('Medico', 'Paullac', 'Calle Fatima', 'DNI', '55555555', '', '2024-08-27', 'Completo'),
('Herrero', 'Nuñez', 'Calle Fatima', 'DNI', '66666666', 2, '2024-08-27', 'Parcial');

-- 3. Insertar datos en la tabla Roles
INSERT INTO Roles (nombreRol) 
VALUES
('Gerente'), 
('Administrador'),
('Supervisor Equino'),
('Supervisor Campo'),
('Médico'),
('Herrero');

-- 4. Insertar Datos en Usuario
-- Las contraseñas de los 6 usuarios es "haras", cabe mencionar que estan ordenadas conforme se encuentran los roles
INSERT INTO Usuarios (idPersonal, correo, clave, idRol) 
VALUES 
(1, 'gerente', '$2y$10$RaoPTBz9oVETRVocodEaWuwxQPjshzARRmDnGZcWcDY43YxNF/sIa', 1),
(2, 'admin', '$2y$10$RaoPTBz9oVETRVocodEaWuwxQPjshzARRmDnGZcWcDY43YxNF/sIa', 2),
(3, 'superE', '$2y$10$RaoPTBz9oVETRVocodEaWuwxQPjshzARRmDnGZcWcDY43YxNF/sIa', 3),
(4, 'superC', '$2y$10$RaoPTBz9oVETRVocodEaWuwxQPjshzARRmDnGZcWcDY43YxNF/sIa', 4),
(5, 'medico', '$2y$10$RaoPTBz9oVETRVocodEaWuwxQPjshzARRmDnGZcWcDY43YxNF/sIa', 5),
(6, 'herrero', '$2y$10$RaoPTBz9oVETRVocodEaWuwxQPjshzARRmDnGZcWcDY43YxNF/sIa', 6);

-- AGREGADOS:

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
-- Riego, Deshierve, Arado, Gradeado, Rufiado, Potrillo, Potranca, Yeguas Preñadas, Yeguas con Crías, Yeguas vacías, Destetados

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
        ('Macho', 'Activo'),   -- Estado de monta: Macho Activo
        ('Macho', 'Inactivo'),
        ('Hembra','Preñada'),
		('Hembra','Servida'),
        ('Hembra','S/S'),
        ('Hembra','Por Servir'),
        ('Hembra','Vacía');
        
-- 28/10/2024
INSERT INTO modulos (modulo) VALUES
	('campos'), -- 1
    ('equinos'), -- 2
    ('historialMedico'), -- 3
    ('inventarios'), -- 4
    ('servicios'), -- 5
    ('usuarios'); -- 6


-- HOME
INSERT INTO vistas (idmodulo, ruta, sidebaroption, texto, icono) VALUES
	(NULL, 'home', 'S', 'Inicio', 'fas fa-home');
-- Campos
INSERT INTO vistas (idmodulo, ruta, sidebaroption, texto, icono) VALUES
	(1, 'rotar-campo', 'S', 'Campos', 'fa-solid fa-group-arrows-rotate'),
    (1, 'programar-rotacion', 'S', 'Rotacion Campos', 'fa-solid fa-calendar-days');
-- Equinos
INSERT INTO vistas (idmodulo, ruta, sidebaroption, texto, icono) VALUES
	(2, 'listar-equino', 'S', 'Listado Equinos', 'fa-solid fa-list'),
	(2, 'registrar-equino', 'S', 'Registro Equinos', 'fa-solid fa-horse'),
   	(2, 'registrar-bostas', 'S', 'Registro Bostas', 'fas fa-poop');

-- historialMedico
INSERT INTO vistas (idmodulo, ruta, sidebaroption, texto, icono) VALUES
	(3, 'diagnosticar-equino', 'S', 'Diagnóstico', 'fa-solid fa-file-waveform');
-- Inventarios
INSERT INTO vistas (idmodulo, ruta, sidebaroption, texto, icono) VALUES
	(4, 'administrar-alimento', 'S', 'Alimentos', 'fas fa-apple-alt'),
	(4, 'administrar-medicamento', 'S', 'Medicamentos', 'fas fa-pills');
-- Servicios
INSERT INTO vistas (idmodulo, ruta, sidebaroption, texto, icono) VALUES
    (5, 'servir-propio', 'S', 'Servicio Propio', 'fas fa-tools'),
    (5, 'servir-mixto', 'S', 'Servicio Mixto', 'fas fa-exchange-alt'),
	(5, 'listar-servicio', 'S', 'Listado Servicios', 'fa-solid fa-list');
-- Usuarios
INSERT INTO vistas (idmodulo, ruta, sidebaroption, texto, icono) VALUES
	(6, 'registrar-personal', 'S', 'Registrar Personal', 'fa-solid fa-wallet'),
    (6, 'registrar-usuario', 'N', NULL, NULL);
   
   

-- Gerente
INSERT INTO permisos (idRol, idvista) VALUES
	(1, 1),
    (1, 4),
    (1, 11);
-- Administrador
INSERT INTO permisos (idRol, idvista) VALUES
	(2, 1),
    (2, 4),
    (2, 11);
-- Supervisor Equino
INSERT INTO permisos (idRol, idvista) VALUES
	(3, 1),
	(3, 5),
    (3, 4),
    (3, 6),
    (3, 7),
    (3, 8),
    (3, 9),
    (3, 10),
    (3, 11),
    (3, 12),
    (3, 13);
-- Supervisor Campo
INSERT INTO permisos (idRol, idvista) VALUES
	(4, 1),
	(4, 2),
    (4, 3),
    (4, 14);

-- Medico
INSERT INTO permisos (idRol, idvista) VALUES
	(5, 1),
	(5, 6);
-- Herrero
INSERT INTO permisos (idRol, idvista) VALUES
	(6, 1),
	(6, 4);