-- Salida sin merma
CALL spu_alimentos_salida(
    3,                 -- idUsuario (El ID del usuario que realiza la operación)
    'pepino',          -- nombreAlimento (El nombre del alimento, en este caso 'pepino')
    2,                 -- cantidad (Cantidad a retirar del inventario, en este caso 2 unidades)
    2,                 -- idTipoEquino (El ID del tipo de equino al que va la salida, en este caso 2 para 'Padrillo')
    0                  -- merma (No hay merma en este caso, por lo que se indica 0)
);

SELECT * FROM Alimentos WHERE nombreAlimento = 'pepino';
SELECT idAlimento, nombreAlimento, stockFinal FROM Alimentos WHERE nombreAlimento = 'pepino';

-- Prueba con fecha de caducidad definida
CALL spu_alimentos_nuevo(
    3,                 -- idUsuario
    'pepino',         -- nombreAlimento
    'Vegetal',         -- tipoAlimento
    'Kilos',           -- unidadMedida
    'Lote17',         -- lote
    2.50,              -- costo
    '2024-12-31',      -- fechaCaducidad
    200                -- cantidad
);

-- Prueba con fecha de caducidad como "No definida"
CALL spu_alimentos_nuevo(
    3,                 -- idUsuario
    'manzana',          -- nombreAlimento
    'Fruta',           -- tipoAlimento
    'Kilos',           -- unidadMedida
    'Lote002',         -- lote
    3.00,              -- costo
    NULL,              -- fechaCaducidad "No definida"
    50                 -- cantidad
);

-- Prueba de entrada con nuevo precio
CALL spu_alimentos_entrada(
    3,                 -- idUsuario
    'Lechugaa',         -- nombreAlimento
    500,                -- cantidad a ingresar
    'Kilos',           -- unidadMedida
    'Lote011',         -- lote
    '2024-12-31',      -- fechaCaducidad
    2.75               -- nuevoPrecio
);

SELECT * FROM Alimentos WHERE nombreAlimento = 'lechugaa' AND lote = 'Lote011';

-- Prueba de entrada sin nuevo precio (usar el precio anterior)
CALL spu_alimentos_entrada(
    3,                 -- idUsuario
    'Manzana',          -- nombreAlimento
    25,                -- cantidad a ingresar
    'Kilos',           -- unidadMedida
    'Lote002',         -- lote
    NULL,              -- fechaCaducidad "No definida"
    NULL               -- Sin nuevoPrecio
);

-- ------------------------------------------------------------------------------------------
-- Prueba de salida con merma
CALL spu_alimentos_salida(
    3,                 -- idUsuario
    'pepino',          -- nombreAlimento
    2,                -- cantidad a retirar
    2,                 -- idTipoEquino (ej. 2 para Padrillo)
    0                  -- Sin merma
);

CALL spu_alimentos_salida(
    3,                 -- idUsuario
    'pepino',          -- nombreAlimento
    3,                -- cantidad a retirar
    2,                 -- idTipoEquino (ej. 2 para Padrillo)
    5                  -- merma (cantidad de pérdida)
);

-- -------------------------------------------------------------------
-- Prueba con mínimo de stock establecido en 30
CALL spu_notificar_stock_bajo(30);

-- Prueba con mínimo de stock establecido en 10
CALL spu_notificar_stock_bajo(10);


-- ---------------------------------------------------------------------------------------
-- Prueba de historial completo sin filtros (todos los movimientos)
CALL spu_historial_completo(
    '',                -- Tipo de movimiento ('' para todos)
    '2024-01-01',      -- Fecha de inicio
    CURDATE(),         -- Fecha de fin
    0,                 -- idUsuario (0 para todos)
    100,               -- Límite
    0                  -- Offset
);

-- Prueba de historial filtrado por tipo "Entrada"
CALL spu_historial_completo(
    'Entrada',         -- Filtrar solo por entradas
    '2024-01-01',      -- Fecha de inicio
    CURDATE(),         -- Fecha de fin
    0,                 -- idUsuario (0 para todos)
    100,               -- Límite
    0                  -- Offset
);


select * from alimentos;
select * from usuarios;
SELECT * FROM TipoMovimientos;

SELECT * FROM Alimentos WHERE nombreAlimento = 'lechugaa' AND lote = 'Lote011';