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
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
    
            $idUsuario = $_SESSION['idUsuario'] ?? null;
            error_log("ID de usuario en la sesión: " . json_encode($idUsuario));
    
            if ($idUsuario === null) {
                throw new Exception('Usuario no autenticado.');
            }
    
            // Crear un array de campos obligatorios para verificar si alguno está vacío
            $obligatorios = [
                'idEquino' => $params['idEquino'] ?? null,
                'fecha' => $params['fecha'] ?? null,
                'trabajoRealizado' => $params['trabajoRealizado'] ?? null,
                'herramientasUsadas' => $params['herramientasUsadas'] ?? null,
                'observaciones' => $params['observaciones'] ?? null
            ];
    
            // Log de cada campo obligatorio y su valor
            foreach ($obligatorios as $campo => $valor) {
                error_log("Campo obligatorio: $campo, Valor: " . json_encode($valor));
                if (empty($valor)) {
                    throw new Exception("Falta el campo obligatorio: $campo.");
                }
            }
    
            // Log de los datos completos preparados para insertar en la base de datos
            error_log("Datos preparados para InsertarHistorialHerrero: " . json_encode([
                $params['idEquino'],
                $idUsuario,
                $params['fecha'],
                $params['trabajoRealizado'],
                $params['herramientasUsadas'],
                $params['observaciones']
            ]));
    
            $stmt = $this->pdo->prepare("CALL InsertarHistorialHerrero(?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $params['idEquino'],
                $idUsuario,
                $params['fecha'],
                $params['trabajoRealizado'],
                $params['herramientasUsadas'],
                $params['observaciones']
            ]);
    
            error_log("Inserción realizada correctamente en InsertarHistorialHerrero.");
    
            return ['status' => 'success', 'message' => 'Trabajo registrado en el historial correctamente.'];
        } catch (PDOException $e) {
            error_log("Error al insertar historial de herrero: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error al registrar el trabajo en el historial.'];
        } catch (Exception $e) {
            error_log("Error en insertarHistorialHerrero: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    // Método para insertar una herramienta usada en el historial
    public function insertarHerramientaUsada($params = []) {
        try {
            $stmt = $this->pdo->prepare("CALL InsertarHerramientaUsada(?, ?)");
            $stmt->execute([
                $params['idHistorialHerrero'],
                $params['idHerramienta']
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
            $query = $this->pdo->prepare("CALL spu_obtener_tipo_equino_alimento()");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getTipoEquinos: " . $e->getMessage());
            return [];
        }
    }

    // Método para obtener los equinos por tipo
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
