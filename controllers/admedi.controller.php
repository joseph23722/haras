<?php
require_once '../models/Admedi.php';

// Habilitar la depuración temporalmente
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json'); // Asegura que la respuesta sea JSON

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $operation = $_POST['operation'] ?? '';

        // Crear una instancia del modelo Admi
        $admi = new Admi();

        if ($operation === 'registrar') {
            // Registrar un nuevo medicamento
            $nombreMedicamento = $_POST['nombreMedicamento'] ?? null;
            $cantidad = $_POST['cantidad'] ?? null;
            $caducidad = $_POST['caducidad'] ?? null;
            $precioUnitario = $_POST['precioUnitario'] ?? null;
            $idTipomovimiento = $_POST['idTipomovimiento'] ?? null;

            if (!$nombreMedicamento || !$cantidad || !$caducidad || !$precioUnitario || !$idTipomovimiento) {
                throw new Exception('Datos incompletos para registrar el medicamento.');
            }

            $params = [
                'nombreMedicamento' => $nombreMedicamento,
                'cantidad' => $cantidad,
                'caducidad' => $caducidad,
                'precioUnitario' => $precioUnitario,
                'idTipomovimiento' => $idTipomovimiento
            ];

            $result = $admi->registrarMedicamento($params);

            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Medicamento registrado correctamente.']);
            } else {
                throw new Exception('Error al registrar el medicamento.');
            }

        } elseif ($operation === 'movimiento') {
            // Manejar entrada/salida de medicamento
            $nombreMedicamento = $_POST['nombreMedicamento'] ?? null;
            $cantidad = $_POST['cantidad'] ?? null;
            $idTipomovimiento = $_POST['idTipomovimiento'] ?? null;

            if (!$nombreMedicamento || !$cantidad || !$idTipomovimiento) {
                throw new Exception('Datos incompletos para la operación de movimiento.');
            }

            $params = [
                'nombreMedicamento' => $nombreMedicamento,
                'cantidad' => $cantidad,
                'idTipomovimiento' => $idTipomovimiento
            ];

            $result = $admi->movimientoMedicamento($params);

            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Movimiento realizado correctamente.']);
            } else {
                throw new Exception('Error al realizar el movimiento.');
            }
        } elseif ($operation === 'getAllMedicamentos') {
            // Obtener todos los medicamentos
            $medicamentos = $admi->getAllMedicamentos();

            // Validar que la respuesta es un arreglo
            if (!is_array($medicamentos)) {
                throw new Exception('Error al obtener los medicamentos.');
            }

            echo json_encode($medicamentos);
            exit();
        } else {
            throw new Exception('Operación no válida.');
        }
    }
} catch (Exception $e) {
    // Capturar cualquier error y enviar una respuesta JSON
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
