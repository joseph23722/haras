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

function ejecutarProcedimientoHistorial($procedure, $sql_details, $params)
{
    try {
        $pdo = new PDO(
            "mysql:host={$sql_details['host']};dbname={$sql_details['db']};charset={$sql_details['charset']}",
            $sql_details['user'],
            $sql_details['pass'],
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );

        // Preparar la llamada al procedimiento almacenado
        $stmt = $pdo->prepare("CALL $procedure(?, ?, ?, ?, ?)");

        // Ejecutar el procedimiento almacenado pasando los parámetros
        $stmt->execute([
            $params['tipoMovimiento'],  // Tipo de movimiento (Entrada o Salida)
            $params['filtroFecha'],     // Filtro de fecha (hoy, ultimaSemana, ultimoMes, todos)
            $params['idUsuario'],       // ID del usuario (0 para todos)
            $params['limite'],          // Límite de resultados
            $params['desplazamiento']   // Desplazamiento para paginación
        ]);

        // Obtener todos los resultados
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Si hay un término de búsqueda, aplicar el filtro
        if (!empty($params['busqueda'])) {
            $busqueda = strtolower($params['busqueda']);
            $data = array_filter($data, function ($row) use ($busqueda) {
                // Concatenar los valores de las columnas en una sola cadena para buscar
                $searchable = strtolower(implode(' ', array_values($row)));
                return strpos($searchable, $busqueda) !== false;
            });
        }

        // Total de registros después de aplicar el filtro de búsqueda
        $recordsTotal = count($data);

        // Aplicar paginación después del filtrado (si es necesario)
        $data = array_slice($data, 0, $params['limite']);

        // Formatear el resultado para DataTables
        $output = array(
            "draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 0,
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsTotal,
            "data" => array_values($data) // Reindexar el array
        );

        echo json_encode($output);
    } catch (PDOException $e) {
        echo json_encode(array(
            "error" => "Error en la conexión a la base de datos: " . $e->getMessage()
        ));
    }
}

// Obtener los parámetros desde la URL
$params = array(
    'tipoMovimiento' => isset($_GET['tipoMovimiento']) ? $_GET['tipoMovimiento'] : 'Entrada',
    'filtroFecha' => isset($_GET['filtroFecha']) ? $_GET['filtroFecha'] : 'hoy',
    'idUsuario' => isset($_GET['idUsuario']) && is_numeric($_GET['idUsuario']) ? (int)$_GET['idUsuario'] : 0,
    'limite' => isset($_GET['limite']) && is_numeric($_GET['limite']) ? (int)$_GET['limite'] : 10,
    'desplazamiento' => isset($_GET['desplazamiento']) && is_numeric($_GET['desplazamiento']) ? (int)$_GET['desplazamiento'] : 0,
    'busqueda' => isset($_GET['busqueda']) ? $_GET['busqueda'] : ''
);

// Llamar a la función con el procedimiento almacenado y los parámetros obtenidos
ejecutarProcedimientoHistorial('spu_historial_completo', $sql_details, $params);
