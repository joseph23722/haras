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
            // Verificar y manejar la sesión
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
    
            $idUsuario = $_SESSION['idUsuario'] ?? null;
            if ($idUsuario === null) {
                error_log("Usuario no autenticado: idUsuario no definido en la sesión.");
                return ['status' => 'error', 'message' => 'Usuario no autenticado.'];
            }
    
            // Validar los campos obligatorios
            $obligatorios = [
                'idEquino' => $params['idEquino'] ?? null,
                'fecha' => $params['fecha'] ?? null,
                'trabajoRealizado' => $params['trabajoRealizado'] ?? null,
                'herramientasUsadas' => $params['herramientasUsadas'] ?? null,
                'observaciones' => $params['observaciones'] ?? null
            ];
    
            foreach ($obligatorios as $campo => $valor) {
                if (empty($valor)) {
                    error_log("Campo obligatorio faltante: $campo.");
                    throw new Exception("Falta el campo obligatorio: $campo.");
                }
            }
    
            // Ejecutar el procedimiento almacenado
            $stmt = $this->pdo->prepare("CALL InsertarHistorialHerrero(?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $params['idEquino'],
                $idUsuario,
                $params['fecha'],
                $params['trabajoRealizado'],
                $params['herramientasUsadas'],
                $params['observaciones']
            ]);
    
            // Verificar si se insertó correctamente
            if ($stmt->rowCount() > 0) {
                return ['status' => 'success', 'message' => 'Trabajo registrado en el historial correctamente.'];
            } else {
                error_log("No se afectaron filas al insertar historial del herrero.");
                return ['status' => 'error', 'message' => 'No se pudo registrar el trabajo en el historial.'];
            }
        } catch (PDOException $e) {
            // Manejar errores de base de datos
            error_log("Error PDO al insertar historial del herrero: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error en la base de datos al registrar el historial.'];
        } catch (Exception $e) {
            // Manejar otros errores
            error_log("Error en insertarHistorialHerrero: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
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


    // Método para obtener los tipos de equinos
    // Método para listar equinos propios (sin propietario) para medicamentos
    public function listarEquinosPorTipo()
    {
        try {
            $query = $this->pdo->prepare("CALL spu_listar_equinos_propiosMedi()");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    /*
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
        */
}
