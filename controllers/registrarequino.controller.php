<?php
require_once '../models/Registrarequino.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $operation = isset($input['operation']) ? $input['operation'] : '';

    // Instancia de la clase Registrarequino
    $equino = new Registrarequino();

    switch ($operation) {
        case 'add':
            $datosEquino = [
                "nombreEquino" => $input['nombreEquino'],
                "fechaNacimiento" => $input['fechaNacimiento'],
                "sexo" => $input['sexo'],
                "detalles" => $input['detalles'],
                "idPropietario" => $input['idPropietario'],
                "generacion" => $input['generacion'],
                "nacionalidad" => $input['nacionalidad']
            ];

            $result = $equino->registrarEquino($datosEquino);
            if ($result) {
                $response = [
                    "status" => "success",
                    "message" => "El equino ha sido registrado exitosamente.",
                    "idEquino" => $result
                ];
            } else {
                $response = [
                    "status" => "error",
                    "message" => "Hubo un problema al registrar el equino."
                ];
            }
            echo json_encode($response);
            break;

        case 'listarPropietarios':
            $propietarios = $equino->listarPropietarios();
            echo json_encode($propietarios);
            break;

        default:
            echo json_encode(["status" => "error", "message" => "Operación no válida."]);
            break;
    }
}
