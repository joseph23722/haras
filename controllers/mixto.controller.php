<?php

require_once '../models/Mixto.php';

$servicioMixto = new ServicioMixto();

try {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Content-Type: application/json');

    // Manejar solicitudes GET para listar padrillos, yeguas o haras
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['tipoEquino'])) {
            // Listar equinos por tipo (yegua o padrillo)
            $tipoEquino = $_GET['tipoEquino'];
            $equinos = $servicioMixto->listarEquinosPorTipo($tipoEquino);
            echo json_encode($equinos);
        } elseif (isset($_GET['listarHaras'])) {
            // Listar haras
            $haras = $servicioMixto->listarHaras();
            echo json_encode($haras);
        } else {
            throw new Exception("Faltan parámetros para listar datos.");
        }
    }
    // Manejar solicitudes POST para registrar un nuevo servicio mixto
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Obtener los datos enviados desde el cliente (Postman o frontend)
        $data = json_decode(file_get_contents('php://input'), true);

        // Registrar en el log el contenido de $data para verificar qué se está enviando
        error_log("Datos recibidos en el controlador: " . print_r($data, true));

        // Verificar si viene idPadrillo o idYegua y establecerlo como idEquinoSeleccionado
        if (isset($data['idPadrillo'])) {
            $data['idEquinoSeleccionado'] = $data['idPadrillo'];
        } elseif (isset($data['idYegua'])) {
            $data['idEquinoSeleccionado'] = $data['idYegua'];
        } else {
            throw new Exception("Faltan parámetros necesarios: ni Padrillo ni Yegua seleccionados.");
        }

        // Verificar si el usuario seleccionó un haras existente o está agregando uno nuevo
        if (!empty($data['idHaras'])) {
            // Si se selecciona un haras existente, usar su ID
            $data['nombreHaras'] = null;
        } elseif (!empty($data['nombreHaras'])) {
            // Si se está agregando un nuevo haras
            $data['idHaras'] = null;
        } else {
            throw new Exception("Faltan parámetros: debe seleccionar o registrar un Haras.");
        }

        // Verificar que todos los demás parámetros necesarios estén presentes
        if (empty($data['nombreNuevoEquino']) || empty($data['idTipoEquino']) || empty($data['fechaServicio']) || empty($data['detalles']) || empty($data['horaEntrada']) || empty($data['horaSalida'])) {
            throw new Exception("Faltan parámetros necesarios en la solicitud.");
        }

        // Llamar al método para registrar el servicio mixto
        $result = $servicioMixto->registrarServicioMixto($data);

        // Verificar si hubo un error en la ejecución de la consulta
        if ($result['status'] === 'success') {
            echo json_encode(["status" => "success", "message" => $result['message']]);
        } else {
            throw new Exception($result['message']);
        }
    } else {
        throw new Exception("Método de solicitud no permitido.");
    }
} catch (Exception $e) {
    // Registrar el error en los logs del servidor
    error_log("Error en el controlador: " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
