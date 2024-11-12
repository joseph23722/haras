<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Detalles de conexión a la base de datos
$sql_details = array(
    'user' => 'root',
    'pass' => '',
    'db'   => 'HarasDB',
    'host' => 'localhost',
    'charset' => 'utf8'
);

function ejecutarProcedimientoHistorial($procedure, $sql_details, $params) {
    try {
        $pdo = new PDO(
            "mysql:host={$sql_details['host']};dbname={$sql_details['db']};charset={$sql_details['charset']}",
            $sql_details['user'],
            $sql_details['pass'],
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );

        // Preparar la llamada al procedimiento almacenado con los parámetros necesarios
        $stmt = $pdo->prepare("CALL $procedure(?, ?, ?, 0, 1000, 0)"); // `0, 1000, 0` son ejemplos de límite y desplazamiento (se pueden ajustar)
        
        // Ejecutar el procedimiento almacenado pasando los parámetros en orden
        $stmt->execute([
            $params['tipoMovimiento'],  // Tipo de movimiento (Entrada o Salida)
            $params['fechaInicio'],     // Fecha de inicio
            $params['fechaFin']         // Fecha de fin
        ]);

        // Obtener los resultados
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $recordsTotal = count($data);
        $recordsFiltered = $recordsTotal;

        $output = array(
            "draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 0,
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data
        );

        echo json_encode($output);

    } catch (PDOException $e) {
        echo json_encode(array(
            "error" => "Error en la conexión a la base de datos: " . $e->getMessage()
        ));
    }
}

// Obtener los parámetros de tipo de movimiento y rango de fechas desde la URL
$params = array(
    'tipoMovimiento' => isset($_GET['tipoMovimiento']) ? $_GET['tipoMovimiento'] : 'Entrada',
    'fechaInicio' => isset($_GET['fechaInicio']) ? $_GET['fechaInicio'] : '1900-01-01',
    'fechaFin' => isset($_GET['fechaFin']) ? $_GET['fechaFin'] : date('Y-m-d')
);

// Llamar a la función con el procedimiento almacenado y los parámetros obtenidos
ejecutarProcedimientoHistorial('spu_historial_completo', $sql_details, $params);

