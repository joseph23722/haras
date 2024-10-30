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

            // Validar que los parámetros obligatorios están presentes
            if (empty($params['idEquino']) || empty($params['idMedicamento']) || empty($params['dosis']) ||
                empty($params['cantidad']) || empty($params['frecuenciaAdministracion']) ||
                empty($params['viaAdministracion']) || empty($params['fechaInicio']) || empty($params['fechaFin'])) {
                throw new Exception('Faltan parámetros obligatorios.');
            }

            // Ejecutar el procedimiento almacenado con todos los parámetros
            $query = $this->pdo->prepare("CALL spu_historial_medico_registrarMedi(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $query->execute([
                $params['idEquino'],
                $idUsuario, // Usar el idUsuario de la sesión
                $params['idMedicamento'],
                $params['dosis'],
                $params['cantidad'],
                $params['frecuenciaAdministracion'],
                $params['viaAdministracion'],
                $params['pesoEquino'] ?? null, // Permitir NULL
                $params['fechaInicio'],
                $params['fechaFin'],
                $params['observaciones'],
                $params['reaccionesAdversas'] ?? null // Permitir NULL
            ]);

            return $query->rowCount() > 0;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }


    // Método para listar equinos propios (sin propietario) para medicamentos
    public function listarEquinosPorTipo() {
        try {
            $query = $this->pdo->prepare("CALL spu_listar_equinos_propiosMedi()");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // Método para consultar el historial médico de un equino
    public function consultarHistorialMedico($idEquino) {
        try {
            $query = $this->pdo->prepare("CALL spu_consultar_historial_medicoMedi(?)");
            $query->execute([$idEquino]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // Obtener todos los medicamentos
    public function getAllMedicamentos() {
        try {
            // Llamada al procedimiento almacenado para listar los medicamentos
            $query = "CALL spu_listar_medicamentosMedi()"; // Llamada directa al procedimiento almacenado
        
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Devolver todos los registros como un array asociativo
        } catch (Exception $e) {
            error_log("Error al obtener medicamentos: " . $e->getMessage());
            return false;
        }
    }
}
