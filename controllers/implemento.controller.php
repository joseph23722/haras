<?php
require_once '../models/Implemento.php';

$controller = new Implemento();

header("Content-type: application/json; charset=utf-8");

// Verifica si la solicitud es GET o POST
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Caso de operación GET
    if (isset($_GET['operation'])) {
        switch ($_GET['operation']) {
            case 'tipoMovimiento':
                $response = $controller->listadoTipoMovimiento();
                echo json_encode($response); // Verifica si realmente llega algo aquí
                break;

            case 'implementosPorInventario':
                if (isset($_GET['idTipoinventario'])) {
                    $idTipoinventario = $_GET['idTipoinventario'];
                    error_log("idTipoinventario recibido: " . $idTipoinventario); // Agrega esto para depurar
                    $response = $controller->listarProductosporInventario($idTipoinventario);
                    echo json_encode($response);
                } else {
                    echo json_encode(["status" => "error", "message" => "idTipoinventario no especificado."]);
                }
                break;

            default:
                echo json_encode(["status" => "error", "message" => "Operación no válida."]);
                break;
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Operación no especificada."]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Caso de operación POST con cuerpo JSON
    $requestBody = json_decode(file_get_contents("php://input"), true);

    // Verifica si la operación es registrarImplemento
    if (isset($requestBody['operation']) && $requestBody['operation'] === 'registrarImplemento') {
        if (isset($requestBody['idTipoinventario'], $requestBody['nombreProducto'], $requestBody['descripcion'], $requestBody['precioUnitario'], $requestBody['cantidad'])) {
            $params = [
                'idTipoinventario' => $requestBody['idTipoinventario'],
                'nombreProducto' => $requestBody['nombreProducto'],
                'descripcion' => $requestBody['descripcion'],
                'precioUnitario' => $requestBody['precioUnitario'],
                'cantidad' => $requestBody['cantidad']
                
            ];

            // Llamada al modelo para registrar el implemento
            $response = $controller->registroImplemento($params);
            
            // Devolver la respuesta del modelo
            echo json_encode($response); // Devuelve la respuesta del modelo
        } else {
            echo json_encode(["status" => -1, "message" => "Faltan parámetros en la solicitud."]);
        }
    } else {
        echo json_encode(["status" => -1, "message" => "Operación no especificada o inválida."]);
    }
} else {
    echo json_encode(["status" => -1, "message" => "Método no permitido."]);
}
