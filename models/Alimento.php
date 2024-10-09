<?php
require_once 'Conexion.php';

class Alimento extends Conexion {
    private $pdo;

    public function __CONSTRUCT() {
        $this->pdo = parent::getConexion();
    }

    // Método para registrar un nuevo alimento
    public function registrarNuevoAlimento($params = []) {
        try {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            $idUsuario = $_SESSION['idUsuario'] ?? null;

            if ($idUsuario === null) {
                throw new Exception('Usuario no autenticado.');
            }

            // Validar los datos
            if (empty($params['nombreAlimento']) || $params['cantidad'] <= 0 || $params['costo'] <= 0) {
                throw new Exception('Datos inválidos. Verifique el nombre del alimento, cantidad y costo.');
            }

            // Llamar al procedimiento almacenado para registrar el alimento
            $query = $this->pdo->prepare("CALL spu_alimentos_nuevo(?,?,?,?,?,?,?,?)");
            $query->execute([
                $idUsuario,
                $params['nombreAlimento'],
                $params['tipoAlimento'],
                $params['unidadMedida'],
                $params['lote'],
                $params['costo'],
                $params['fechaCaducidad'],
                $params['cantidad']
            ]);

            return ['status' => 'success', 'message' => 'Alimento registrado exitosamente.'];
        } catch (PDOException $e) {
            if ($e->getCode() == '45000') {
                return ['status' => 'error', 'message' => $e->getMessage()];
            }
            error_log($e->getMessage());
            return ['status' => 'error', 'message' => 'Error en la base de datos.'];
        } catch (Exception $e) {
            error_log($e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // Método para registrar una entrada de alimento
    public function registrarEntradaAlimento($params = []) {
        try {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            $idUsuario = $_SESSION['idUsuario'] ?? null;

            if ($idUsuario === null) {
                throw new Exception('Usuario no autenticado.');
            }

            // Validar los datos
            if (empty($params['nombreAlimento']) || $params['cantidad'] <= 0) {
                throw new Exception('Datos inválidos. Verifique el nombre del alimento y la cantidad.');
            }

            // Llamar al procedimiento almacenado para registrar la entrada de alimento
            $query = $this->pdo->prepare("CALL spu_alimentos_entrada(?,?,?,?,?,?,?,?)");
            $query->execute([
                $idUsuario,
                $params['nombreAlimento'],
                $params['tipoAlimento'],  // Agregar el parámetro 'tipoAlimento'
                $params['unidadMedida'],
                $params['lote'],
                $params['fechaCaducidad'],
                $params['cantidad'],
                $params['nuevoPrecio'] ?? null
            ]);

            return ['status' => 'success', 'message' => 'Entrada registrada exitosamente.'];
        } catch (PDOException $e) {
            if ($e->getCode() == '45000') {
                return ['status' => 'error', 'message' => $e->getMessage()];
            }
            error_log($e->getMessage());
            return ['status' => 'error', 'message' => 'Error en la base de datos.'];
        } catch (Exception $e) {
            error_log($e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // Método para registrar una salida de alimento
    public function registrarSalidaAlimento($params = []) {
        try {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            $idUsuario = $_SESSION['idUsuario'] ?? null;

            if ($idUsuario === null) {
                throw new Exception('Usuario no autenticado.');
            }

            // Validar los datos
            if (empty($params['nombreAlimento']) || $params['cantidad'] <= 0 || empty($params['idTipoEquino'])) {
                throw new Exception('Datos inválidos. Verifique el nombre del alimento, la cantidad y el tipo de equino.');
            }

            // Llamar al procedimiento almacenado para registrar la salida de alimento
            $query = $this->pdo->prepare("CALL spu_alimentos_salida(?,?,?,?,?)");
            $query->execute([
                $idUsuario,
                $params['nombreAlimento'],
                $params['cantidad'],
                $params['idTipoEquino'],
                $params['merma'] ?? 0
            ]);

            return ['status' => 'success', 'message' => 'Salida registrada exitosamente.'];
        } catch (PDOException $e) {
            if ($e->getCode() == '45000') {
                return ['status' => 'error', 'message' => $e->getMessage()];
            }
            error_log($e->getMessage());
            return ['status' => 'error', 'message' => 'Error en la base de datos.'];
        } catch (Exception $e) {
            error_log($e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // Método para notificar stock bajo
    public function notificarStockBajo($minimoStock) {
        try {
            $query = $this->pdo->prepare("CALL spu_notificar_stock_bajo(?)");
            $query->execute([$minimoStock]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            if ($e->getCode() == '45000') {
                return ['status' => 'error', 'message' => $e->getMessage()];
            }
            error_log($e->getMessage());
            return [];
        }
    }

    // Método para obtener historial de movimientos de alimentos
    public function obtenerHistorialMovimientos($params = []) {
        try {
            $query = $this->pdo->prepare("CALL spu_historial_completo(?,?,?,?,?,?)");
            $query->execute([
                $params['tipoMovimiento'] ?? '',
                $params['fechaInicio'] ?? '1900-01-01',
                $params['fechaFin'] ?? date('Y-m-d'),
                $params['idUsuario'] ?? 0,
                $params['limit'] ?? 10,
                $params['offset'] ?? 0
            ]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // Método para obtener todos los alimentos
    public function getAllAlimentos() {
        try {
            $query = $this->pdo->prepare("SELECT * FROM Alimentos");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // Método para obtener información de stock de alimentos
    public function getAlimentosStockInfo() {
        try {
            $query = $this->pdo->prepare("SELECT nombreAlimento, stockFinal, cantidad FROM Alimentos");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // Método para eliminar un alimento
    public function eliminarAlimento($idAlimento) {
        try {
            $query = $this->pdo->prepare("DELETE FROM Alimentos WHERE idAlimento = ?");
            $query->execute([$idAlimento]);
            return ['status' => 'success', 'message' => 'Alimento eliminado exitosamente.'];
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return ['status' => 'error', 'message' => 'Error al eliminar el alimento.'];
        }
    }

    // Método para obtener los tipos de equinos
    public function getTipoEquinos() {
        try {
            $query = $this->pdo->prepare("SELECT idTipoEquino, tipoEquino FROM TipoEquinos");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }
}
