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
                    error_log("idTipoinventario recibido: " . $idTipoinventario);
                    $response = $controller->listarProductosporInventario($idTipoinventario);
                    echo json_encode($response);
                } else {
                    echo json_encode(["status" => "error", "message" => "idTipoinventario no especificado."]);
                }
                break;

            case 'listarHistorialMovimiento':
                $idTipoinventario = isset($_GET['idTipoinventario']) ? intval($_GET['idTipoinventario']) : 1;
                $idTipomovimiento = isset($_GET['idTipomovimiento']) ? intval($_GET['idTipomovimiento']) : 1;
                $historial = $controller->listarHistorialMovimiento($idTipoinventario, $idTipomovimiento);
                echo json_encode($historial);
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
    }  // Verifica si la operación es registrarEntrada
    elseif (isset($requestBody['operation']) && $requestBody['operation'] === 'registrarEntrada') {
        if (isset($requestBody['idTipoinventario'], $requestBody['idTipoMovimiento'], $requestBody['idInventario'], $requestBody['cantidad'], $requestBody['descripcion'])) {
            $params = [
                'idTipoinventario' => $requestBody['idTipoinventario'], // Asegúrate de que 'idTipoinventario' esté presente aquí
                'idTipoMovimiento' => $requestBody['idTipoMovimiento'],
                'idInventario' => $requestBody['idInventario'],
                'cantidad' => $requestBody['cantidad'],
                'descripcion' => $requestBody['descripcion'],
                'precioUnitario' => $requestBody['precioUnitario'],
            ];

            // Llamada al modelo para registrar la entrada de implementos
            $response = $controller->registrarEntrada($params);

            echo json_encode($response); // Devuelve la respuesta del modelo
        } else {
            echo json_encode(["status" => -1, "message" => "Faltan parámetros en la solicitud."]);
        }
    } elseif (isset($requestBody['operation']) && $requestBody['operation'] === 'registrarSalida') {
        if (isset($requestBody['idInventario'], $requestBody['descripcion'], $requestBody['cantidad'], $requestBody['idTipoinventario'])) {
            $params = [
                'idTipoinventario' => $requestBody['idTipoinventario'],
                'idInventario' => $requestBody['idInventario'],
                'cantidad' => $requestBody['cantidad'],
                'descripcion' => $requestBody['descripcion'],
            ];

            // Llamada al modelo para registrar la salida de implementos
            $response = $controller->registrarSalida($params); // Método en el modelo

            // Responder con el resultado del modelo
            echo json_encode($response);
        } else {
            echo json_encode(["status" => -1, "message" => "Faltan parámetros en la solicitud."]);
        }
    }
} else {
    echo json_encode(["status" => -1, "message" => "Método no permitido."]);
}
