-- 1. Insertar Datos en TipoInventarios
INSERT INTO TipoInventarios (nombreInventario) 
VALUES 
('Herramientas'),         -- Tipo de inventario: Herramientas
('Equipo de Protección');  -- Tipo de inventario: Equipo de Protección

-- 2. Insertar Datos en TipoMovimientos
INSERT INTO TipoMovimientos (movimiento) 
VALUES 
('entrada'),   -- Tipo de movimiento: Entrada
('salida');    -- Tipo de movimiento: Salida

-- 3. Insertar Datos en TipoEquinos
INSERT INTO TipoEquinos (tipoEquino) VALUES 
('yegua'), 
('padrillo'), 
('potranca'), 
('potrillo');

-- 4. Insertar Datos en EstadoMonta
INSERT INTO EstadoMonta (genero, nombreEstado) 
VALUES 
('macho', 'activo'),   -- Estado de monta: Macho Activo
('hembra', 'preñada'); -- Estado de monta: Hembra Preñada

-- 5. Insertar Datos en Personal
INSERT INTO Personal (nombres, apellidos, direccion, tipodoc, nrodocumento, numeroHijos, fechaIngreso) 
VALUES 
('Juan', 'Pérez', 'Calle Fatima', 'DNI', '12345678', 2, '2024-08-27'),   -- Personal: Juan Pérez
('Ana', 'García', 'Avenida San Vicente 456', 'DNI', '87654321', 1, '2024-08-26');  -- Personal: Ana García


-- Insertar datos en la tabla Roles
INSERT INTO Roles (nombreRol) 
VALUES 
('Administrador'),
('Empleado');

-- 6. Insertar Datos en Usuarios
INSERT INTO Usuarios (idPersonal, correo, clave, idRol) 
VALUES 
(1, 'juanperez@gmail.com', '$2y$10$RaoPTBz9oVETRVocodEaWuwxQPjshzARRmDnGZcWcDY43YxNF/sIa', 1),  -- Usuario: Juan Pérez, Rol: Administrador
(2, 'anagarcia@gmail.com', '$2y$10$MRJu1.8gZKUVLvIaU6EeseekrcojOrG3KMEFmx/o5qAuNAyb/zfPy', 2);   -- Usuario: Ana García, Rol: Empleado




-- 7. Insertar Datos en Implementos
INSERT INTO Implementos (idTipoinventario, nombreProducto, descripcion, precioUnitario, idTipomovimiento, cantidad, stockFinal) 
VALUES 
(1, 'Martillo', 'Martillo de acero', 15.00, 1, 10, 10),   -- Implemento: Martillo
(2, 'Guantes', 'Guantes de seguridad', 5.00, 2, 20, 20);  -- Implemento: Guantes de seguridad

-- 8. Insertar Datos en Alimentos
INSERT INTO Alimentos (idUsuario, nombreAlimento, cantidad, costo, idTipoEquino, idTipomovimiento, stockFinal, fechaIngreso, compra) 
VALUES 
(3, 'Heno', 100.00, 50.00, 1, 1, 100, '2024-08-27', 50.00),  -- Alimento: Heno para Padrillo
(4, 'Avena', 200.00, 75.00, 2, 2, 200, '2024-08-26', 75.00);  -- Alimento: Avena para Yegua

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

-- 11. Insertar Datos en Propietarios
INSERT INTO Propietarios (nombreHaras, nombreequino, genero, costoServicio) 
VALUES ('Haras Sunrise', 'Caballo Fuego', 'macho', 1500.00),
       ('Haras Luna', 'Yegua Brillante', 'hembra', 1300.00);
       
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



USE HarasDB;
SELECT * FROM TipoEquinos;
SELECT * FROM Servicios;
SELECT * FROM Usuarios;
SELECT * FROM Personal;
SELECT * FROM detallemedicamentos;
SELECT * FROM propietarios;
SELECT * FROM medicamentos;
SELECT * FROM equinos;
select * from alimentos;

CALL spu_listar_equinos_por_tipo('padrillo');
CALL spu_listar_equinos_por_tipo('yegua');


SELECT * FROM TipoEquinos;
SELECT * FROM Equinos;


CALL spu_listar_medicamentos_con_detalles();
CALL spu_listar_haras();