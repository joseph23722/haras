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
            $query = $this->pdo->prepare("CALL spu_historial_medico_registrar(?,?,?,?,?,?,?)");
            $query->execute([
                $params['idEquino'],
                $params['idUsuario'],
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
