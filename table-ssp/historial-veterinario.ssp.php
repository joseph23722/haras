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

function ejecutarProcedimientoDataTables($procedure, $sql_details, $params = []) {
    try {
        $pdo = new PDO(
            "mysql:host={$sql_details['host']};dbname={$sql_details['db']};charset={$sql_details['charset']}",
            $sql_details['user'],
            $sql_details['pass'],
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );

        // Preparar la consulta para el procedimiento almacenado con múltiples parámetros
        if (count($params) > 0) {
            $stmt = $pdo->prepare("CALL $procedure(" . str_repeat('?,', count($params) - 1) . "?)");
            $stmt->execute($params);
        } else {
            $stmt = $pdo->prepare("CALL $procedure()");
            $stmt->execute();
        }

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
        error_log("Error en ejecutarProcedimientoDataTables: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
    }
}

// Obtener los parámetros de filtro de la solicitud
$nombreEquino = isset($_GET['nombreEquino']) ? $_GET['nombreEquino'] : null;
$nombreMedicamento = isset($_GET['nombreMedicamento']) ? $_GET['nombreMedicamento'] : null;
$estadoTratamiento = isset($_GET['estadoTratamiento']) ? $_GET['estadoTratamiento'] : null;
$listarMedicamentos = isset($_GET['listarMedicamentos']) ? $_GET['listarMedicamentos'] : null;

// Determinar qué procedimiento llamar
if ($listarMedicamentos) {
    // Llamar al procedimiento para listar medicamentos
    ejecutarProcedimientoDataTables('spu_listar_medicamentos', $sql_details);
} elseif ($nombreEquino || $nombreMedicamento || $estadoTratamiento) {
    // Llamar al procedimiento de filtrado
    ejecutarProcedimientoDataTables('spu_filtrar_historial_medicoMedi', $sql_details, [$nombreEquino, $nombreMedicamento, $estadoTratamiento]);
} else {
    // Llamar al procedimiento general
    ejecutarProcedimientoDataTables('spu_consultar_historial_medicoMedi', $sql_details);
}
