-- 1. Insertar Datos en TipoInventarios
INSERT INTO TipoInventarios (nombreInventario) 
VALUES 
('Implementos Equinos'),
('Implementos Campos');

-- 2. Insertar Datos en Personal
INSERT INTO Personal (nombres, apellidos, direccion, tipodoc, nrodocumento, numeroHijos, fechaIngreso, tipoContrato)
VALUES 
('Ruben', 'Marcos', 'Calle Fatima', 'DNI', '12345678', 2, '2024-08-27', 'Completo');

-- 3. Insertar datos en la tabla Roles
INSERT INTO Roles (nombreRol) 
VALUES
('Gerente'), 
('Administrador'),
('Supervisor Equino'),
('Supervisor Campo'),
('Médico'),
('Herrero');

-- 4. Insertar Datos en Usuarios
INSERT INTO Usuarios (idPersonal, correo, clave, idRol) 
VALUES 
(1, 'ruben@gmail.com', '$2y$10$RaoPTBz9oVETRVocodEaWuwxQPjshzARRmDnGZcWcDY43YxNF/sIa', 2);

-- AGREGADOS:

-- 5. Insertar Datos en Propietarios
INSERT INTO Propietarios (nombreHaras) 
    VALUES  ('Los Eucaliptos');

-- 6. Insertar Datos en TipoMovimientos
INSERT INTO TipoMovimientos (movimiento) 
    VALUES 
        ('Entrada'),
        ('Salida');
        
INSERT INTO Medicamentos (nombreMedicamento, cantidad, caducidad, precioUnitario, idTipomovimiento, idUsuario)
    VALUES
        ('Antibiótico X', 50.00, '2025-12-31', 15.00, 1, 1),
        ('Analgesico Y', 30.00, '2026-06-15', 10.50, 1, 1);
        
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
    ('Recién nacido'), 
    ('Destete');

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