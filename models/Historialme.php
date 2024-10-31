<?php
require_once 'Conexion.php';

class Historialme extends Conexion {
    private $pdo;

    public function __CONSTRUCT() {
        $this->pdo = parent::getConexion();
    }

    // Método para registrar el historial médico
    // Método para registrar el historial médico
    // Método para registrar el historial médico
    public function registrarHistorial($params = []) {
        try {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
    
            $idUsuario = $_SESSION['idUsuario'] ?? null;
    
            if ($idUsuario === null) {
                throw new Exception('Usuario no autenticado.');
            }
    
            // Crear un array de campos obligatorios para verificar si alguno está vacío
            $obligatorios = [
                'idEquino' => $params['idEquino'] ?? null,
                'idMedicamento' => $params['idMedicamento'] ?? null,
                'dosis' => $params['dosis'] ?? null,
                'frecuenciaAdministracion' => $params['frecuenciaAdministracion'] ?? null,
                'viaAdministracion' => $params['viaAdministracion'] ?? null,
                'fechaFin' => $params['fechaFin'] ?? null
            ];
    
            // Verificar si falta algún campo obligatorio
            foreach ($obligatorios as $campo => $valor) {
                if (empty($valor)) {
                    throw new Exception("Falta el campo obligatorio: $campo.");
                }
            }
    
            // Ejecutar el procedimiento almacenado sin el parámetro fechaInicio
            $query = $this->pdo->prepare("CALL spu_historial_medico_registrarMedi(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $query->execute([
                $params['idEquino'],
                $idUsuario,
                $params['idMedicamento'],
                $params['dosis'],
                $params['frecuenciaAdministracion'],
                $params['viaAdministracion'],
                $params['pesoEquino'] ?? null,
                $params['fechaFin'],
                $params['observaciones'],
                $params['reaccionesAdversas'] ?? null
            ]);
    
            if ($query->rowCount() > 0) {
                return ['status' => 'success', 'message' => 'Historial médico registrado correctamente'];
            } else {
                return ['status' => 'error', 'message' => 'No se pudo registrar el historial médico'];
            }
        } catch (Exception $e) {
            error_log("Error en registrarHistorial: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
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
    public function consultarHistorialMedico() {
        $query = $this->pdo->prepare("CALL spu_consultar_historial_medicoMedi()");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
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

    // Eliminar un medicamento por ID directamente en la tabla
    public function deleteMedicamentoDirect($idMedicamento) {
        try {
            // Consulta para eliminar el medicamento directamente de la tabla
            $query = "DELETE FROM Medicamentos WHERE idMedicamento = :idMedicamento";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':idMedicamento', $idMedicamento, PDO::PARAM_INT); // Asignar el ID del medicamento
            $stmt->execute();
            
            // Verificar si se afectó alguna fila
            if ($stmt->rowCount() > 0) {
                return true; // Eliminación exitosa
            } else {
                return false; // No se encontró el medicamento para eliminar
            }
        } catch (Exception $e) {
            error_log("Error al eliminar medicamento: " . $e->getMessage());
            return false;
        }
    }

}
