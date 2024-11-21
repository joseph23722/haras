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

    // Llamar al procedimiento almacenado con parámetros
    $stmt = $pdo->prepare("CALL spu_Listar_TiposYUnidadesAlimentos(:searchValue, :start, :length)");
    $stmt->bindParam(':searchValue', $searchValue, PDO::PARAM_STR);
    $stmt->bindParam(':start', $start, PDO::PARAM_INT);
    $stmt->bindParam(':length', $length, PDO::PARAM_INT);
    $stmt->execute();

    // Obtener los datos
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor(); // Cierra el cursor después del primer conjunto de resultados

    // Obtener total filtrado
    $stmtFiltered = $pdo->query("SELECT COUNT(*) AS totalFiltered FROM TipoAlimentos ta
                                 LEFT JOIN UnidadesMedidaAlimento uma ON 1 = 1
                                 WHERE '$searchValue' = '' 
                                 OR ta.tipoAlimento LIKE '%$searchValue%' 
                                 OR uma.nombreUnidad LIKE '%$searchValue%'");
    $filteredResult = $stmtFiltered->fetch(PDO::FETCH_ASSOC);
    $recordsFiltered = $filteredResult['totalFiltered'];
    $stmtFiltered->closeCursor();

    // Obtener total general
    $stmtTotal = $pdo->query("SELECT COUNT(*) AS totalRecords FROM TipoAlimentos");
    $totalResult = $stmtTotal->fetch(PDO::FETCH_ASSOC);
    $recordsTotal = $totalResult['totalRecords'];
    $stmtTotal->closeCursor();

    // Respuesta para DataTables
    echo json_encode(array(
        "draw" => $draw,
        "recordsTotal" => $recordsTotal,
        "recordsFiltered" => $recordsFiltered,
        "data" => $data
    ));

} catch (PDOException $e) {
    echo json_encode(array(
        "error" => "Error en la conexión a la base de datos: " . $e->getMessage()
    ));
}

