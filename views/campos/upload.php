<?php
header('Content-Type: application/json'); // Asegúrate de que la respuesta es JSON

error_reporting(E_ALL);
ini_set('display_errors', 1); // Mostrar errores de PHP para depurar

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['cadFile'])) {
        $file = $_FILES['cadFile'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['message' => 'Error al subir el archivo.']);
            exit;
        }

        // Validar el tipo de archivo
        $allowedExtensions = ['dxf', 'dwg'];
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($fileExtension, $allowedExtensions)) {
            echo json_encode(['message' => 'Tipo de archivo no permitido. Solo se aceptan .dxf y .dwg.']);
            exit;
        }

        $uploadDir = 'uploads/';
        
        // Crear el directorio si no existe
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Crear un nombre único para el archivo
        $uploadFile = $uploadDir . uniqid() . '.' . $fileExtension;

        if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
            echo json_encode(['message' => 'Archivo subido con éxito!', 'filePath' => $uploadFile]);
        } else {
            echo json_encode(['message' => 'Error al guardar el archivo.']);
        }
    } else {
        echo json_encode(['message' => 'No se subió ningún archivo.']);
    }
} else {
    echo json_encode(['message' => 'Método no permitido.']);
}
?>
