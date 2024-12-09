<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Configuración de conexión a la base de datos
$sql_details = array(
    'user' => 'root',
    'pass' => '',
    'db'   => 'HarasDB',
    'host' => 'localhost',
    'charset' => 'utf8'
);

try {
    // Conexión a la base de datos
    $pdo = new PDO(
        "mysql:host={$sql_details['host']};dbname={$sql_details['db']};charset={$sql_details['charset']}",
        $sql_details['user'],
        $sql_details['pass'],
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );

    // Parámetros enviados por DataTables
    $draw = isset($_GET['draw']) ? intval($_GET['draw']) : 1;
    $start = isset($_GET['start']) ? intval($_GET['start']) : 0;
    $length = isset($_GET['length']) ? intval($_GET['length']) : 10;
    $searchValue = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';
    $orderColumn = isset($_GET['order'][0]['column']) ? intval($_GET['order'][0]['column']) : 0;
    $orderDir = isset($_GET['order'][0]['dir']) && in_array($_GET['order'][0]['dir'], ['asc', 'desc']) ? $_GET['order'][0]['dir'] : 'asc';

    // Mapear columnas para ordenar
    $columns = ['id', 'nombre', 'tipo'];
    $orderBy = $columns[$orderColumn] ?? 'nombre';

    // Ejecutar el procedimiento almacenado
    $stmt = $pdo->prepare("CALL spu_ListarTiposYHerramientas()");
    $stmt->execute();

    // Obtener todos los datos
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    // Filtrar los datos si hay un valor de búsqueda
    if (!empty($searchValue)) {
        $data = array_filter($data, function ($row) use ($searchValue) {
            return stripos($row['id'], $searchValue) !== false ||
                stripos($row['nombre'], $searchValue) !== false ||
                stripos($row['tipo'], $searchValue) !== false;
        });
    }

    // Ordenar los datos
    usort($data, function ($a, $b) use ($orderBy, $orderDir) {
        if ($orderDir === 'asc') {
            return strcmp($a[$orderBy], $b[$orderBy]);
        } else {
            return strcmp($b[$orderBy], $a[$orderBy]);
        }
    });

    // Total de registros después del filtrado
    $recordsFiltered = count($data);

    // Aplicar paginación
    $pagedData = array_slice($data, $start, $length);

    // Total de registros sin filtrar
    $recordsTotal = count($data);

    // Respuesta para DataTables
    echo json_encode(array(
        "draw" => $draw,
        "recordsTotal" => $recordsTotal,
        "recordsFiltered" => $recordsFiltered,
        "data" => $pagedData
    ));
} catch (PDOException $e) {
    // Manejo de errores
    echo json_encode(array(
        "error" => "Error en la conexión a la base de datos: " . $e->getMessage()
    ));
}