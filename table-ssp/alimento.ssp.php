<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Configuración de la base de datos
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
        $stmt->closeCursor();

        // Total de registros sin filtrar
        $recordsTotal = count($data);

        // Filtrar los datos si hay un valor de búsqueda
        if (!empty($_GET['search']['value'])) {
            $searchValue = $_GET['search']['value'];
            $data = array_filter($data, function ($row) use ($searchValue) {
                return stripos($row['nombreAlimento'], $searchValue) !== false ||
                       stripos($row['nombreTipoAlimento'], $searchValue) !== false ||
                       stripos($row['unidadMedidaNombre'], $searchValue) !== false ||
                       stripos($row['lote'], $searchValue) !== false ||
                       stripos($row['estado'], $searchValue) !== false;
            });
        }

        // Total de registros después del filtrado
        $recordsFiltered = count($data);

        // Respuesta para DataTables
        echo json_encode(array(
            "draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 0,
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => array_values($data) // Asegurarse de que los índices sean consecutivos
        ));
    } catch (PDOException $e) {
        error_log("Error en ejecutarProcedimientoDataTables: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
    }
}

// Obtener los parámetros de filtro
$orden = isset($_GET['orden']) ? $_GET['orden'] : null;
$idAlimento = isset($_GET['idAlimento']) ? $_GET['idAlimento'] : null;

// Determinar qué procedimiento llamar en función de los parámetros
if ($orden) {
    // Llamar al procedimiento de filtrado por cantidad en stock
    ejecutarProcedimientoDataTables('spu_filtrarAlimentos', $sql_details, [$orden]);
} else {
    // Llamar al procedimiento para obtener todos los alimentos con el parámetro idAlimento
    ejecutarProcedimientoDataTables('spu_obtenerAlimentosConLote', $sql_details, [$idAlimento]);
}
