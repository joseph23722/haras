<?php
require_once 'Conexion.php';

class Admi extends Conexion {
    private $pdo;

    public function __CONSTRUCT() {
        $this->pdo = parent::getConexion();
    }

    // Método para registrar un nuevo medicamento
    public function registrarMedicamento($params = []) {
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

            // Ejecutar el procedimiento almacenado para registrar un medicamento
            $query = $this->pdo->prepare("CALL spu_medicamentos_registrar(?,?,?,?,?,?)");
            $query->execute([
                $params['nombreMedicamento'],
                $params['cantidad'],
                $params['caducidad'],
                $params['precioUnitario'],
                $params['idTipomovimiento'],
                $idUsuario
            ]);

            return $query->rowCount() > 0; // Verifica si se insertó correctamente
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // Método para manejar la entrada y salida de medicamentos
    public function movimientoMedicamento($params = []) {
        try {
            // Ejecutar el procedimiento almacenado para movimientos de medicamentos
            $query = $this->pdo->prepare("CALL spu_medicamentos_movimiento(?,?,?)");
            $query->execute([
                $params['nombreMedicamento'],
                $params['cantidad'],
                $params['idTipomovimiento']
            ]);

            return $query->rowCount() > 0; // Verifica si se ejecutó correctamente
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // Método para obtener todos los medicamentos
    public function getAllMedicamentos() {
        try {
            $query = $this->pdo->prepare("SELECT * FROM Medicamentos");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC); // Esto debería devolver un array de medicamentos
        } catch (Exception $e) {
            error_log($e->getMessage());
            return []; // Devuelve un array vacío en caso de error
        }
    }

    // Método para eliminar un medicamento
    public function eliminarMedicamento($idMedicamento) {
        try {
            $query = $this->pdo->prepare("DELETE FROM Medicamentos WHERE idMedicamento = ?");
            $query->execute([$idMedicamento]);

            return $query->rowCount() > 0; // Verifica si se eliminó correctamente
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
