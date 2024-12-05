<?php
require_once '../models/ssp.class.php'; // Asegúrate de tener la clase SSP

// Configuración de la conexión a la base de datos
$sql_details = array(
    'user' => 'root',
    'pass' => '',
    'db'   => 'HarasDB',
    'host' => 'localhost',
    'charset' => 'utf8'
);

// Parámetros de DataTables
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
$length = isset($_GET['length']) ? intval($_GET['length']) : 10;
$search = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';

// Llamada al procedimiento almacenado `spu_listar_medicamentosMedi`
// NOTA: Asegúrate de que este procedimiento acepte los parámetros de paginación y búsqueda
function ejecutarProcedimientoDataTables($sql_details, $start, $length, $search)
{
    try {
        // Conectar a la base de datos
        $pdo = new PDO(
            "mysql:host={$sql_details['host']};dbname={$sql_details['db']};charset={$sql_details['charset']}",
            $sql_details['user'],
            $sql_details['pass'],
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );

        // Llamar al procedimiento almacenado
        $stmt = $pdo->prepare("CALL spu_listar_medicamentosMedi(:start, :length, :search)");
        $stmt->bindParam(':start', $start, PDO::PARAM_INT);
        $stmt->bindParam(':length', $length, PDO::PARAM_INT);
        $stmt->bindParam(':search', $search, PDO::PARAM_STR);
        $stmt->execute();

        // Obtener los datos
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Obtener el total de registros sin filtrar
        $stmtTotal = $pdo->query("SELECT COUNT(*) AS total FROM Medicamentos");
        $recordsTotal = $stmtTotal->fetchColumn();

        // Salida en el formato de DataTables
        return array(
            "draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 0,
            "recordsTotal" => intval($recordsTotal),
            "recordsFiltered" => intval($recordsTotal), // Puedes ajustar esto si haces una búsqueda avanzada
            "data" => $data
        );
    } catch (Exception $e) {
        return array(
            "error" => "Error en la conexión a la base de datos: " . $e->getMessage()
        );
    }
}

// Ejecutar la función y devolver la respuesta en JSON
echo json_encode(ejecutarProcedimientoDataTables($sql_details, $start, $length, $search));
