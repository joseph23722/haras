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
function ejecutarProcedimientoDataTablesConFiltro($procedure, $sql_details, $params)
{
    try {
        $pdo = new PDO(
            "mysql:host={$sql_details['host']};dbname={$sql_details['db']};charset={$sql_details['charset']}",
            $sql_details['user'],
            $sql_details['pass'],
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );

        // Ejecutar el procedimiento almacenado con los parámetros
        $stmt = $pdo->prepare("CALL $procedure(?, ?, ?, ?, ?)");
        $stmt->execute([
            $params['tipoMovimiento'], // Tipo de movimiento (Entrada o Salida)
            $params['filtroFecha'],    // Filtro de fecha (hoy, ultimaSemana, ultimoMes, todos)
            $params['idUsuario'],      // ID del usuario
            $params['limit'],          // Límite de resultados
            $params['offset']          // Desplazamiento para la paginación
        ]);

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Si hay un término de búsqueda, aplicar el filtro
        if (!empty($params['search']['value'])) {
            $busqueda = strtolower($params['search']['value']);
            $data = array_filter($data, function ($row) use ($busqueda) {
                // Concatenar los valores de las columnas en una sola cadena para buscar
                $searchable = strtolower(implode(' ', array_values($row)));
                return strpos($searchable, $busqueda) !== false;
            });
        }

        // Total de registros después de aplicar el filtro de búsqueda
        $recordsFiltered = count($data);

        // Aplicar paginación después del filtrado (si es necesario)
        $data = array_slice($data, $params['offset'], $params['limit']);

        // Formatear el resultado para DataTables
        $output = array(
            "draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 0,
            "recordsTotal" => $recordsFiltered,
            "recordsFiltered" => $recordsFiltered,
            "data" => array_values($data) // Reindexar el array
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
    'tipoMovimiento' => $_GET['tipoMovimiento'] ?? 'Entrada', // Entrada o Salida
    'filtroFecha' => $_GET['filtroFecha'] ?? 'hoy',          // hoy, ultimaSemana, ultimoMes, todos
    'idUsuario' => $_GET['idUsuario'] ?? 0,                  // ID del usuario
    'limit' => $_GET['length'] ?? 10,                        // Límite de resultados para la paginación
    'offset' => $_GET['start'] ?? 0,                         // Desplazamiento para la paginación
    'search' => $_GET['search'] ?? array('value' => '')      // Parámetro de búsqueda
];

// Llamar a la función para ejecutar el procedimiento con los parámetros dados
ejecutarProcedimientoDataTablesConFiltro('spu_historial_completo_medicamentos', $sql_details, $params);