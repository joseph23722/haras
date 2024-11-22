<?php
require_once '../models/Editarequino.php';

$controller = new Editarequino();
header('Content-Type: application/json; charset=utf-8');

// Leer el cuerpo de la solicitud JSON
$input = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($input['operation'])) {
    switch ($input['operation']) {
      case 'editarEquino':
        // Validación de parámetros
        $params = [
          'idEquino' => $input['idEquino'] ?? null,
          'nombreEquino' => $input['nombreEquino'] ?? null,
          'idPropietario' => $input['idPropietario'] ?? null,
          'pesokg' => $input['pesokg'] ?? null,
          'idEstadoMonta' => $input['idEstadoMonta'] ?? null,
          'estado' => $input['estado'] ?? null,
        ];

        // Comprobación de campos obligatorios
        if (empty($params['idEquino'])) {
          echo json_encode(["status" => "error", "message" => "El ID del equino es obligatorio."]);
          break;
        }

        if (empty($params['nombreEquino'])) {
          echo json_encode(["status" => "error", "message" => "El nombre del equino es obligatorio."]);
          break;
        }

        if (!in_array($params['estado'], ['Vivo', 'Muerto'])) {
          echo json_encode(["status" => "error", "message" => "El estado debe ser 'Vivo' o 'Muerto'."]);
          break;
        }

        // Llamar al método para editar el equino
        try {
          $resultado = $controller->editarEquino($params);
          if ($resultado == 1) {
            echo json_encode(["status" => "success", "message" => "Equino actualizado correctamente."]);
          } else {
            echo json_encode(["status" => "error", "message" => "Error al actualizar el equino."]);
          }
        } catch (Exception $e) {
          echo json_encode(["status" => "error", "message" => "Error al editar el equino: " . $e->getMessage()]);
        }
        break;

      default:
        echo json_encode(["status" => "error", "message" => "Operación no válida."]);
        break;
    }
  } else {
    echo json_encode(["status" => "error", "message" => "Operación no especificada."]);
  }
} else {
  echo json_encode(["status" => "error", "message" => "Método HTTP no permitido."]);
}
