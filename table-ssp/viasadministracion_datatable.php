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

function ejecutarProcedimientoDataTables($procedure, $sql_details) {
    try {
        // Conexión a la base de datos
        $pdo = new PDO(
            "mysql:host={$sql_details['host']};dbname={$sql_details['db']};charset={$sql_details['charset']}",
            $sql_details['user'],
            $sql_details['pass'],
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );

        error_log("Conexión establecida con la base de datos.\n");

        // Ejecutar procedimiento almacenado
        $stmt = $pdo->prepare("CALL $procedure()");
        error_log("Ejecutando procedimiento almacenado: CALL $procedure()\n");
        $stmt->execute();

        // Obtener resultados
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Datos obtenidos del procedimiento: " . json_encode($data) . "\n");

        // Validar si se obtuvieron datos
        if (empty($data)) {
            error_log("El procedimiento no devolvió datos.\n");
        }

        $recordsTotal = count($data);
        $recordsFiltered = $recordsTotal;

        // Respuesta para DataTables
        $response = array(
            "draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 1,
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data
        );

        error_log("Enviando respuesta JSON: " . json_encode($response) . "\n");

        echo json_encode($response);

    } catch (PDOException $e) {
        error_log("Error en conexión o ejecución: " . $e->getMessage() . "\n");
        echo json_encode(array(
            "error" => "Error en la conexión a la base de datos: " . $e->getMessage()
        ));
    }
}

// Ejecutar procedimiento almacenado para listar vías de administración
ejecutarProcedimientoDataTables('spu_Listar_ViasAdministracion', $sql_details);
