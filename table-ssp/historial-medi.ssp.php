<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

$sql_details = array(
    'user' => 'root',
    'pass' => '',
    'db'   => 'HarasDB',
    'host' => 'localhost',
    'charset' => 'utf8'
);

// Función para ejecutar el procedimiento almacenado con parámetros
function ejecutarProcedimientoDataTablesConParametros($procedure, $sql_details, $params) {
    try {
        $pdo = new PDO(
            "mysql:host={$sql_details['host']};dbname={$sql_details['db']};charset={$sql_details['charset']}",
            $sql_details['user'],
            $sql_details['pass'],
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );

        // Ejecutar el procedimiento almacenado con los 6 parámetros
        $stmt = $pdo->prepare("CALL $procedure(?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $params['tipoMovimiento'],
            $params['fechaInicio'],
            $params['fechaFin'],
            $params['idUsuario'],
            $params['limit'],
            $params['offset']
        ]);

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

// Obtener parámetros del request
$params = [
    'tipoMovimiento' => $_GET['tipoMovimiento'] ?? 'Entrada',
    'fechaInicio' => $_GET['fechaInicio'] ?? '1900-01-01',
    'fechaFin' => $_GET['fechaFin'] ?? date('Y-m-d'),
    'idUsuario' => $_GET['idUsuario'] ?? 0,     // ID del usuario
    'limit' => $_GET['length'] ?? 10,           // Límite de resultados para la paginación
    'offset' => $_GET['start'] ?? 0             // Desplazamiento para la paginación
];

// Llamar a la función para ejecutar el procedimiento con los parámetros dados
ejecutarProcedimientoDataTablesConParametros('spu_historial_completo_medicamentos', $sql_details, $params);
