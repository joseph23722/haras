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
            // Iniciar la sesión si aún no se ha iniciado
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            // Obtener el idUsuario desde la sesión
            $idUsuario = $_SESSION['idUsuario'] ?? null;

            if ($idUsuario === null) {
                throw new Exception('Usuario no autenticado.');
            }

            // Validar los parámetros
            if (empty($params['nombreAlimento']) || $params['cantidad'] <= 0 || $params['costo'] <= 0) {
                throw new Exception('Datos inválidos. Verifique el nombre del alimento, cantidad y costo.');
            }

            // Llamar al procedimiento almacenado para registrar el alimento
            $query = $this->pdo->prepare("CALL spu_alimentos_nuevo(?,?,?,?,?,?,?,?,?)");
            $query->execute([
                $idUsuario,
                $params['nombreAlimento'],
                $params['tipoAlimento'],
                $params['cantidad'],
                $params['unidadMedida'],
                $params['costo'],
                $params['lote'],
                $params['fechaCaducidad'],
                $params['fechaIngreso']
            ]);

            return ['status' => 'success', 'message' => 'Alimento registrado exitosamente.'];
        } catch (PDOException $e) {
            if ($e->getCode() == '45000') { // Código SQLSTATE personalizado para errores del procedimiento
                return ['status' => 'error', 'message' => $e->getMessage()];
            }
            error_log($e->getMessage());
            return ['status' => 'error', 'message' => 'Error en la base de datos.'];
        } catch (Exception $e) {
            error_log($e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // Método para actualizar el stock de un alimento (entrada/salida)
    public function actualizarStockAlimento($params = []) {
        try {
            // Validar que idTipoEquino sea proporcionado en caso de salida
            if ($params['idTipomovimiento'] == 2 && empty($params['idTipoEquino'])) {
                throw new Exception('idTipoEquino es obligatorio para las salidas.');
            }

            // Llamar al procedimiento almacenado para gestionar la entrada/salida
            $query = $this->pdo->prepare("CALL spu_alimentos_movimiento(?,?,?,?,?,?,?,?,?,?)");
            $query->execute([
                $_SESSION['idUsuario'], // Usar idUsuario de la sesión
                $params['nombreAlimento'],
                $params['tipoAlimento'],
                $params['cantidad'],
                $params['unidadMedida'],
                $params['costo'],
                $params['lote'],
                $params['fechaCaducidad'],
                $params['idTipomovimiento'],
                $params['idTipoEquino'] ?? null,
                $params['merma'] ?? 0
            ]);

            return ['status' => 'success', 'message' => 'Stock actualizado exitosamente.'];
        } catch (PDOException $e) {
            if ($e->getCode() == '45000') { // Código SQLSTATE personalizado para errores del procedimiento
                return ['status' => 'error', 'message' => $e->getMessage()];
            }
            error_log($e->getMessage());
            return ['status' => 'error', 'message' => 'Error en la base de datos.'];
        } catch (Exception $e) {
            error_log($e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // Método para eliminar un registro de alimento
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

    // Método específico para el Dashboard - Obtener información de stock de alimentos
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

    // Método para generar el reporte de inventario (próximos a caducar o con stock bajo)
    public function reporteInventario($dias) {
        try {
            $query = $this->pdo->prepare("CALL spu_reporte_inventario(?)");
            $query->execute([$dias]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // Método para verificar si hay alimentos caducados
    public function verificarCaducidad() {
        try {
            $query = $this->pdo->prepare("CALL spu_verificar_caducidad()");
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC); // Esperar un solo registro
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }
}
