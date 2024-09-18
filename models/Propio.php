<?php

require_once 'Conexion.php';

class ServicioPropio extends Conexion {
    private $pdo;

    public function __CONSTRUCT() {
        $this->pdo = parent::getConexion();
    }

    // Registrar un nuevo servicio propio
    public function registrarServicioPropio($params = []) {
        try {
            // Llamada al procedimiento almacenado para registrar el servicio propio
            $query = $this->pdo->prepare("CALL spu_registrar_servicio_propio(?, ?, ?, ?, ?, ?)");
            $query->execute([
                $params['idEquino1'],
                $params['idEquino2'],
                $params['fechaServicio'],
                $params['detalles'],
                $params['horaEntrada'],
                $params['horaSalida']
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Error al registrar servicio propio: " . $e->getMessage());
            return false;
        }
    }

    // Listar equinos por tipo (yegua o padrillo)
    public function listarEquinosPorTipo($tipoEquino) {
        try {
            $query = $this->pdo->prepare("CALL spu_listar_equinos_por_tipo(?)");
            $query->execute([$tipoEquino]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al listar equinos por tipo: " . $e->getMessage());
            return [];
        }
    }

    // Listar haras (propietarios)
    public function listarHaras() {
        try {
            $query = $this->pdo->prepare("CALL spu_listar_haras()");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al listar haras: " . $e->getMessage());
            return [];
        }
    }

    // Listar medicamentos con sus detalles
    public function listarMedicamentosConDetalles() {
        try {
            $query = $this->pdo->prepare("CALL spu_listar_medicamentos_con_detalles()");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al listar medicamentos con detalles: " . $e->getMessage());
            return [];
        }
    }
}
