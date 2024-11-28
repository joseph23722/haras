<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../models/EquinoExterno.php';

header('Content-Type: application/json');

$equino = new EquinoExterno();

try {
  $method = $_SERVER['REQUEST_METHOD'];

  if ($method === 'GET') {
    $operation = $_GET['operation'] ?? '';

    switch ($operation) {
      case 'listarEquinosExternos':
        try {
          $result = $equino->listarEquinosExternos();
          if ($result) {
            echo json_encode(['status' => 'success', 'data' => $result]);
          } else {
            echo json_encode(['status' => 'error', 'message' => 'No se encontraron equinos externos.']);
          }
        } catch (PDOException $e) {
          echo json_encode(['status' => 'error', 'message' => 'Error al obtener los equinos externos: ' . $e->getMessage()]);
        }
        break;
      default:
        echo json_encode(['status' => 'error', 'message' => 'Operación no válida']);
        break;
    }
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Método HTTP no permitido.']);
  }
} catch (Exception $e) {
  echo json_encode(['status' => 'error', 'message' => 'Error general: ' . $e->getMessage()]);
}