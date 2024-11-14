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

$idEquino = isset($_GET['idEquino']) ? $_GET['idEquino'] : 0;

if ($idEquino > 0) {
    try {
        $pdo = new PDO(
            "mysql:host={$sql_details['host']};dbname={$sql_details['db']};charset={$sql_details['charset']}",
            $sql_details['user'],
            $sql_details['pass'],
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );

        // Preparar y ejecutar el procedimiento almacenado
        $stmt = $pdo->prepare("CALL ConsultarHistorialEquino(?)");
        $stmt->execute([$idEquino]);

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $recordsTotal = count($data);
        $recordsFiltered = $recordsTotal;

        $output = array(
            "draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 0,
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => array_map(function ($row) {
                return [
                    "nombreEquino" => $row['nombreEquino'],
                    "tipoEquino" => $row['tipoEquino'],
                    "fecha" => $row['fecha'],
                    "trabajoRealizado" => $row['trabajoRealizado'],
                    "herramientasUsadas" => $row['herramientasUsadas'],
                    "observaciones" => $row['observaciones'],
                    "acciones" => "<button class='btn btn-warning btn-sm' onclick='actualizarEstadoFinal({$row['idHistorialHerrero']})'>Actualizar Estado</button>"
                ];
            }, $data)
        );
        

        echo json_encode($output);
    } catch (PDOException $e) {
        echo json_encode(array("error" => "Error en la conexión a la base de datos: " . $e->getMessage()));
    }
} else {
    echo json_encode(array("error" => "ID del equino no proporcionado."));
}

