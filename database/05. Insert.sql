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