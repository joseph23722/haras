<?php
ini_set('display_errors', 0);  // Desactivar la visualización de errores en producción
ini_set('log_errors', 1);  // Activar el registro de errores
ini_set('error_log', '/var/log/php_errors.log');  // Definir archivo de log

header('Content-Type: application/json');

// Detalles de la conexión a la base de datos
$sql_details = array(
    'user' => 'root',
    'pass' => '',
    'db'   => 'HarasDB',
    'host' => 'localhost',
    'charset' => 'utf8'
);

try {
    // Conectar a la base de datos
    $pdo = new PDO(
        "mysql:host={$sql_details['host']};dbname={$sql_details['db']};charset={$sql_details['charset']}",
        $sql_details['user'],
        $sql_details['pass'],
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );

    // Obtener el tipo de equino del parámetro de entrada (si existe)
    $tipoEquino = isset($_GET['tipoEquino']) ? $_GET['tipoEquino'] : null;

    if ($tipoEquino) {
        // Llamar al procedimiento almacenado de filtrado
        $stmt = $pdo->prepare("CALL FiltrarHistorialHerreroPorTipoEquino(:tipoEquino)");
        $stmt->bindParam(':tipoEquino', $tipoEquino, PDO::PARAM_STR);
    } else {
        // Llamar al procedimiento almacenado general
        $stmt = $pdo->prepare("CALL ConsultarHistorialEquino()");
    }

    $stmt->execute();

    // Obtener los datos
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $recordsTotal = count($data);
    $recordsFiltered = $recordsTotal; // Si no hay filtros, los totales son iguales

    // Crear el array de salida para el DataTable
    $output = array(
        "draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 0,
        "recordsTotal" => $recordsTotal,
        "recordsFiltered" => $recordsFiltered,
        "data" => array_map(function ($row) {
            return [
                "nombreEquino" => $row['nombreEquino'],
                "tipoEquino" => $row['tipoEquino'],
                "fecha" => $row['fecha'],
                "trabajoRealizado" => $row['TrabajoRealizado'],  // Correcto con el alias del procedimiento
                "herramientasUsadas" => $row['HerramientasUsadas'],  // Correcto con el alias del procedimiento
                "observaciones" => $row['observaciones']
            ];
        }, $data)
    );

    // Enviar el resultado como JSON
    echo json_encode($output);

} catch (PDOException $e) {
    // Manejar errores de conexión a la base de datos
    echo json_encode(array("error" => "Error en la conexión a la base de datos: " . $e->getMessage()));
} catch (Exception $e) {
    // Manejar otros errores
    echo json_encode(array("error" => "Error: " . $e->getMessage()));
}
