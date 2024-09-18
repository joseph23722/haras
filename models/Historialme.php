<?php
require_once 'Conexion.php';

class Historialme extends Conexion {
    private $pdo;

    public function __CONSTRUCT() {
        $this->pdo = parent::getConexion();
    }

    // Método para registrar el historial médico
    public function registrarHistorial($params = []) {
        try {
            // Iniciar la sesión si aún no se ha iniciado
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            // Obtener el idUsuario desde la sesión
            $idUsuario = $_SESSION['idUsuario'] ?? null;

            if ($idUsuario === null) {
                throw new Exception('Usuario no autenticado.');
            }

            // Ejecutar el procedimiento almacenado
            $query = $this->pdo->prepare("CALL spu_historial_medico_registrar(?,?,?,?,?,?,?)");
            $query->execute([
                $params['idEquino'],
                $idUsuario, // Usar el idUsuario de la sesión
                $params['fecha'],
                $params['diagnostico'],
                $params['tratamiento'],
                $params['observaciones'],
                $params['recomendaciones']
            ]);

            return $query->rowCount() > 0;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // Método para listar equinos por tipo
    public function listarEquinosPorTipo($tipoEquino) {
        try {
            $query = $this->pdo->prepare("CALL spu_listar_equinos_para_medicamento(?)");
            $query->execute([$tipoEquino]);
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            error_log(print_r($result, true)); // Agrega este mensaje de depuración
            return $result;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return [];
        }
    }
}