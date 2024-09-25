-- 1. Insertar Datos en TipoInventarios
INSERT INTO TipoInventarios (nombreInventario) 
VALUES 
('Herramientas'),         -- Tipo de inventario: Herramientas
('Equipo de Protección');  -- Tipo de inventario: Equipo de Protección

-- 5. Insertar Datos en Personal
INSERT INTO Personal (nombres, apellidos, direccion, tipodoc, nrodocumento, numeroHijos, fechaIngreso) 
VALUES 
('Ruben', 'Marcos', 'Calle Fatima', 'DNI', '12345678', 2, '2024-08-27');   -- Personal: Juan Pérez


-- Insertar datos en la tabla Roles
INSERT INTO Roles (nombreRol) 
VALUES
('Gerente'), 
('Administrador'),
('Supervisor Equino'),
('Supervisor Campo'),
('Médico'),
('Herrero');

-- 6. Insertar Datos en Usuarios
INSERT INTO Usuarios (idPersonal, correo, clave, idRol) 
VALUES 
(1, 'ruben@gmail.com', '$2y$10$RaoPTBz9oVETRVocodEaWuwxQPjshzARRmDnGZcWcDY43YxNF/sIa', 2),  -- Usuario: Juan Pérez, Rol: Administrador
(2, 'anagarcia@gmail.com', '$2y$10$MRJu1.8gZKUVLvIaU6EeseekrcojOrG3KMEFmx/o5qAuNAyb/zfPy', 2);   -- Usuario: Ana García, Rol: Empleado

-- 7. Insertar Datos en Implementos
INSERT INTO Implementos (idTipoinventario, nombreProducto, descripcion, precioUnitario, idTipomovimiento, cantidad, stockFinal) 
VALUES 
(1, 'Martillo', 'Martillo de acero', 15.00, 1, 10, 10),   -- Implemento: Martillo
(2, 'Guantes', 'Guantes de seguridad', 5.00, 2, 20, 20);  -- Implemento: Guantes de seguridad

-- 8. Insertar Datos en Alimentos
INSERT INTO Alimentos (idUsuario, nombreAlimento, cantidad, costo, idTipomovimiento, idTipoEquino, stockFinal, fechaIngreso, compra, fechaMovimiento) 
VALUES 
(3, 'Avena', 100, 15.50, 1, 1, 100, '2024-09-20', 1550.00, NOW());
CALL spu_alimentos_nuevo(3, 'Manzana', 150, 18.75, '2024-09-22');
 CALL spu_alimentos_movimiento('Manzana', 50, 1, NULL);
CALL spu_alimentos_movimiento('Manzana', 30, 2, 1);
--
CALL spu_alimentos_movimiento('Manzana', 200, 2, 1);

select * from alimentos;





-- 9. Insertar Datos en Medicamentos
INSERT INTO Medicamentos (nombreMedicamento, cantidad, caducidad, precioUnitario, idTipomovimiento, idUsuario, visita, tratamiento) 
VALUES 
('Antibiótico', 50.00, '2025-08-27', 30.00, 1, 3, 'Visita rutinaria', 'Tratamiento para infecciones'),  -- Medicamento: Antibiótico
('Desinfectante', 30.00, '2024-12-31', 20.00, 2, 4, 'Tratamiento general', 'Desinfección');  -- Medicamento: Desinfectante

-- 10. Insertar Datos en DetalleMedicamentos
INSERT INTO DetalleMedicamentos (idMedicamento, dosis, fechaInicio, fechaFin) 
VALUES 
(3, 5.00, '2024-08-27', '2024-09-27'),  -- Detalle de Medicamento: Antibiótico, dosis 5.00
(4, 10.00, '2024-08-26', '2024-09-26'); -- Detalle de Medicamento: Desinfectante, dosis 10.00

-- 12. Insertar Datos en Equinos
INSERT INTO Equinos (nombreEquino, sexo, idTipoEquino, detalles, idPropietario, nacionalidad) 
VALUES 
('Relámpago', 'macho', 2, 'Caballo pura sangre', 1, 'Argentino'),        
('Estrella', 'hembra', 1, 'Yegua campeona', 2, 'Peruano');


-- 13. Insertar Datos en Servicios
INSERT INTO Servicios (idEquino, fechaServicio, tipoServicio, detalles, idDetalleMed, horaEntrada, horaSalida)
VALUES (1, '2024-09-10', 'propio', 'Servicio de entrenamiento intensivo', 3, '08:00:00', '12:00:00');

-- 14. Insertar Datos en Entrenamientos
INSERT INTO Entrenamientos (idEquino, fecha, tipoEntrenamiento, duracion, intensidad, comentarios) 
VALUES 
(1, '2024-08-27 10:00:00', 'Entrenamiento general', 1.50, 'media', 'Entrenamiento sin incidencias'),  -- Entrenamiento para Rayo
(2, '2024-08-26 11:00:00', 'Entrenamiento específico', 2.00, 'alta', 'Entrenamiento intensivo');      -- Entrenamiento para Luna

-- 15. Insertar Datos en HistorialMedico
INSERT INTO HistorialMedico (idEquino, idUsuario, fecha, diagnostico, tratamiento, observaciones, recomendaciones) 
VALUES 
(1, 3, '2024-08-27', 'Diagnóstico general', 'Tratamiento específico', 'Sin observaciones', 'Revisión en 1 mes'),  -- Historial Médico: Rayo
(2, 4, '2024-08-26', 'Diagnóstico rutinario', 'Tratamiento preventivo', 'Observaciones menores', 'Revisión en 2 meses');  -- Historial Médico: Luna

-- 16. Insertar Datos en HistorialHerrero
INSERT INTO HistorialHerrero (idEquino, idUsuario, fecha, trabajoRealizado, herramientasUsadas, observaciones) 
VALUES 
(1, 3, '2024-08-27', 'Trabajo de herrado', 'Martillo, tenazas', 'Trabajo satisfactorio'),  -- Herrado: Rayo
(2, 4, '2024-08-26', 'Reparación de herraduras', 'Martillo, limas', 'Reparación completada');  -- Reparación: Luna

-- 17. Insertar Datos en Campos
INSERT INTO Campos (numeroCampo, tamanoCampo, tipoSuelo, estado, riego) 
VALUES 
(1, 5000.00, 'arcilloso', 'bueno', 'manual'),    -- Campo 1: Arcilloso, riego manual
(2, 6000.00, 'arenoso', 'excelente', 'automático');  -- Campo 2: Arenoso, riego automático

-- 18. Insertar Datos en TipoRotaciones
INSERT INTO TipoRotaciones (nombreRotacion, detalles) 
VALUES 
('Rotación Primaveral', 'Rotación para mejorar la fertilidad del suelo'),  -- Rotación Primaveral
('Rotación Otoñal', 'Rotación para preparar el campo para el invierno');   -- Rotación Otoñal

-- 19. Insertar Datos en RotacionCampos
INSERT INTO RotacionCampos (idCampo, idTipoRotacion, fechaRotacion, estadoRotacion, detalleRotacion) 
VALUES 
(1, 1, '2024-08-27', 'completado', 'Rotación realizada con éxito'),  -- Rotación en Campo 1
(2, 2, '2024-08-26', 'en proceso', 'Rotación en curso');             -- Rotación en Campo 2

-- 20. Insertar Datos en CampañaPotrillos
INSERT INTO CampanaPotrillos (idPotrillo, registroPrecio, precioSubasta) 
VALUES 
(1, 2000.00, 3000.00),  -- Potrillo 1: Registro 2000, Subasta 3000
(2, 1500.00, 2500.00);  -- Potrillo 2: Registro 1500, Subasta 2500

-- 21. Insertar Datos en AsistenciaPersonal
INSERT INTO AsistenciaPersonal (idPersonal, fecha, horaEntrada, horaSalida, observaciones) 
VALUES 
(1, '2024-08-27', '08:00:00', '17:00:00', 'Asistencia completa'),  -- Asistencia de Juan Pérez
(2, '2024-08-26', '09:00:00', '16:00:00', 'Asistencia parcial');   -- Asistencia de Ana García

-- AGREGADOS:

INSERT INTO Propietarios (nombreHaras) 
    VALUES  ('Los Eucaliptos');

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

-- 2. Insertar Datos en TipoMovimientos
INSERT INTO TipoMovimientos (movimiento) 
    VALUES 
        ('Entrada'),   -- Tipo de movimiento: Entrada
        ('Salida');    -- Tipo de movimiento: Salida

-- 3. Insertar Datos en TipoEquinos
INSERT INTO TipoEquinos (tipoEquino) VALUES 
    ('Yegua'), 
    ('Padrillo'), 
    ('Potranca'), 
    ('Potrillo');

-- 4. Insertar Datos en EstadoMonta
INSERT INTO EstadoMonta (genero, nombreEstado) 
    VALUES 
        ('Macho', 'Activo'),   -- Estado de monta: Macho Activo
        ('Hembra', 'Preñada'); -- Estado de monta: Hembra Preñada
        