<?php

require_once 'Conexion.php';

class Usuario extends Conexion {

    private $pdo;

    public function __CONSTRUCT() {
        $this->pdo = parent::getConexion();
    }

    // Función para el login
    public function login($params = []): array {
        try {
            // Preparar la consulta para el procedimiento almacenado
            $query = $this->pdo->prepare("CALL spu_usuarios_login(?)");
            $query->execute([$params['correo']]);
            return $query->fetchAll(PDO::FETCH_ASSOC); // Retornar los resultados
        } catch (Exception $e) {
            error_log("Error en login: " . $e->getMessage());
            return [];
        }
    }

    // Función para agregar un usuario
    public function add($params = []): array {
        $resultado = [
            'status' => 'error',
            'message' => '',
            'idUsuario' => -1
        ];

        try {
            // Log detallado de los parámetros que se envían al procedimiento almacenado
            error_log("Parámetros recibidos en add: " . json_encode($params));

            // Ejecución del procedimiento almacenado
            $cmd = $this->pdo->prepare("CALL spu_usuarios_registrar(@idUsuario, ?, ?, ?, ?)");

            // Ejecutar el procedimiento almacenado con los parámetros
            $cmd->execute([
                $params['idPersonal'],
                $params['correo'],
                password_hash($params['clave'], PASSWORD_BCRYPT), // Encriptación de la clave
                $params['idRol']
            ]);

            // Obtener el ID del usuario generado
            $response = $this->pdo->query("SELECT @idUsuario AS idUsuario")->fetch(PDO::FETCH_ASSOC);

            // Verificar el resultado del procedimiento almacenado
            if ($response && isset($response['idUsuario'])) {
                if ($response['idUsuario'] == -1) {
                    // Usuario ya existente o error en la inserción
                    $resultado['message'] = 'El personal ya cuenta con un usuario registrado.';
                } else {
                    // Usuario registrado exitosamente
                    $resultado['status'] = 'success';
                    $resultado['idUsuario'] = (int) $response['idUsuario'];
                    $resultado['message'] = 'Usuario registrado exitosamente.';
                }
            } else {
                $resultado['message'] = 'Error: No se pudo obtener el ID del usuario.';
            }

        } catch (Exception $e) {
            // Log detallado para capturar cualquier error
            error_log("Error en la inserción de usuario: " . $e->getMessage());
            $resultado['message'] = 'Ocurrió un error al intentar registrar el usuario.';
        }

        return $resultado;
    }

    // Función para obtener lista de usuarios
    public function getAll(): array {
        return parent::getLisu("spu_usuarios_listar");
    }
}
