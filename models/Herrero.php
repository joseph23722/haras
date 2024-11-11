<?php
require_once 'Conexion.php';

class Herrero extends Conexion {
    private $pdo;

    // Constructor para establecer la conexión con la base de datos
    public function __CONSTRUCT() {
        $this->pdo = parent::getConexion();
    }

    // Método para insertar un nuevo trabajo en el historial del herrero
    public function insertarHistorialHerrero($params = []) {
        try {
            $stmt = $this->pdo->prepare("CALL InsertarHistorialHerrero(?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $params['idEquino'],
                $params['idUsuario'],
                $params['fecha'],
                $params['trabajoRealizado'],
                $params['herramientasUsadas'],
                $params['estadoInicio'],
                $params['estadoFin'],
                $params['observaciones']
            ]);
            return ['status' => 'success', 'message' => 'Trabajo registrado en el historial correctamente.'];
        } catch (PDOException $e) {
            error_log("Error al insertar historial de herrero: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error al registrar el trabajo en el historial.'];
        }
    }

    // Método para insertar una herramienta usada en el historial
    public function insertarHerramientaUsada($params = []) {
        try {
            $stmt = $this->pdo->prepare("CALL InsertarHerramientaUsada(?, ?, ?, ?)");
            $stmt->execute([
                $params['idHistorialHerrero'],
                $params['idHerramienta'],
                $params['estadoInicio'],
                $params['estadoFin']
            ]);
            return ['status' => 'success', 'message' => 'Herramienta registrada correctamente en el historial.'];
        } catch (PDOException $e) {
            error_log("Error al insertar herramienta usada: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error al registrar la herramienta en el historial.'];
        }
    }

    // Método para consultar el historial completo de un equino
    public function consultarHistorialEquino($idEquino) {
        try {
            $stmt = $this->pdo->prepare("CALL ConsultarHistorialEquino(?)");
            $stmt->execute([$idEquino]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al consultar historial de equino: " . $e->getMessage());
            return false;
        }
    }

    // Método para actualizar el estado final de una herramienta usada
    public function actualizarEstadoFinalHerramientaUsada($params = []) {
        try {
            $stmt = $this->pdo->prepare("CALL ActualizarEstadoFinalHerramientaUsada(?, ?)");
            $stmt->execute([$params['idHerramientasUsadas'], $params['estadoFin']]);
            return ['status' => 'success', 'message' => 'Estado de herramienta actualizado correctamente.'];
        } catch (PDOException $e) {
            error_log("Error al actualizar estado de herramienta usada: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error al actualizar el estado de la herramienta.'];
        }
    }

    // Método para consultar el estado actual de todas las herramientas
    public function consultarEstadoActualHerramientas() {
        try {
            $stmt = $this->pdo->prepare("CALL ConsultarEstadoActualHerramientas()");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al consultar estado actual de herramientas: " . $e->getMessage());
            return false;
        }
    }

    // Método para insertar un nuevo estado de herramienta en EstadoHerramienta
    public function insertarEstadoHerramienta($descripcionEstado) {
        try {
            $stmt = $this->pdo->prepare("CALL InsertarEstadoHerramienta(?)");
            $stmt->execute([$descripcionEstado]);
            return ['status' => 'success', 'message' => 'Nuevo estado de herramienta insertado correctamente.'];
        } catch (PDOException $e) {
            error_log("Error al insertar estado de herramienta: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error al insertar el estado de la herramienta.'];
        }
    }

    // Método para obtener los tipos de equinos
    public function getTipoEquinos() {
        try {
            // Cambiamos la llamada al nuevo procedimiento almacenado
            $query = $this->pdo->prepare("CALL spu_obtener_tipo_equino_alimento()");
            $query->execute();
            
            // Devolvemos los resultados como un arreglo asociativo
            return $query->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            // Registramos el error en el log
            error_log("Error en getTipoEquinos: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerEquinosPorTipo($idTipoEquino) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM equinos WHERE idTipoEquino = ?");
            $stmt->execute([$idTipoEquino]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener equinos por tipo: " . $e->getMessage());
            return [];
        }
    }
    
    
    
}

