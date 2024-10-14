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
            if (empty($params['nombreAlimento']) || $params['stockActual'] <= 0 || $params['costo'] <= 0 || $params['stockMinimo'] < 0) {
                throw new Exception('Datos inválidos. Verifique el nombre del alimento, stock actual, stock mínimo y costo.');
            }

            // Llamar al procedimiento almacenado para registrar el alimento
            $query = $this->pdo->prepare("CALL spu_alimentos_nuevo(?,?,?,?,?,?,?,?,?)");
            $query->execute([
                $idUsuario,
                $params['nombreAlimento'],
                $params['tipoAlimento'],
                $params['unidadMedida'],
                $params['lote'],
                $params['costo'],
                $params['fechaCaducidad'],
                $params['stockActual'],
                $params['stockMinimo']
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
            // Iniciar sesión si no está iniciada
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            // Obtener el id del usuario autenticado
            $idUsuario = $_SESSION['idUsuario'] ?? null;

            // Validar si el usuario está autenticado
            if ($idUsuario === null) {
                throw new Exception('Usuario no autenticado.');
            }

            // Validar los datos, incluyendo el stock actual y stock mínimo
            if (empty($params['nombreAlimento']) || $params['stockActual'] <= 0 || $params['stockMinimo'] < 0) {
                throw new Exception('Datos inválidos. Verifique el nombre del alimento, el stock actual y el stock mínimo.');
            }

            // Validar que el stock mínimo no sea mayor al stock actual
            if ($params['stockMinimo'] > $params['stockActual']) {
                throw new Exception('El stock mínimo no puede ser mayor que el stock actual.');
            }

            // Llamar al procedimiento almacenado para registrar la entrada de alimento
            $query = $this->pdo->prepare("CALL spu_alimentos_entrada(?,?,?,?,?,?,?,?,?)");
            $query->execute([
                $idUsuario,
                $params['nombreAlimento'],
                $params['tipoAlimento'],   // Tipo de alimento
                $params['unidadMedida'],
                $params['lote'],
                $params['fechaCaducidad'],
                $params['stockActual'],    // Stock actual
                $params['nuevoPrecio'] ?? null  // Nuevo precio (opcional)
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
            if (empty($params['nombreAlimento']) || $params['cantidad'] <= 0 || empty($params['idTipoEquino']) || empty($params['unidadMedida'])) {
                throw new Exception('Datos inválidos. Verifique el nombre del alimento, la cantidad, la unidad de medida y el tipo de equino.');
            }

            // Llamar al procedimiento almacenado para registrar la salida de alimento
            $query = $this->pdo->prepare("CALL spu_alimentos_salida(?,?,?,?,?,?,?)");
            $query->execute([
                $idUsuario,
                $params['nombreAlimento'],
                $params['unidadMedida'],
                $params['cantidad'],
                $params['idTipoEquino'],
                $params['lote'] ?? null,  // Lote opcional
                $params['merma'] ?? 0     // Merma opcional, por defecto es 0
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
            // Preparar la llamada al procedimiento almacenado con los 6 parámetros
            $query = $this->pdo->prepare("CALL spu_historial_completo(?, ?, ?, ?, ?, ?)");

            // Ejecutar el procedimiento almacenado pasando los parámetros necesarios
            $query->execute([
                $params['tipoMovimiento'] ?? 'Entrada',          // Tipo de movimiento (Entrada/Salida)
                $params['fechaInicio'] ?? '1900-01-01',         // Fecha de inicio (default: muy anterior)
                $params['fechaFin'] ?? date('Y-m-d'),           // Fecha de fin (default: hoy)
                $params['idUsuario'] ?? 0,                      // ID del usuario (0 para todos los usuarios)
                $params['limit'] ?? 10,                         // Límite de resultados
                $params['offset'] ?? 0                          // Desplazamiento para paginación
            ]);

            // Devolver los resultados en forma de array asociativo
            return $query->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            // Registrar el error en el log en caso de que algo falle
            error_log($e->getMessage());

            // Devolver un array vacío en caso de error
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
            $query = $this->pdo->prepare("CALL spu_obtener_tipo_equino_alimento()");
             $query->execute();
            
            return $query->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function getUnidadesMedida($nombreAlimento) {
        $query = $this->pdo->prepare("SELECT unidadMedida FROM Alimentos WHERE nombreAlimento = :nombreAlimento");
        $query->execute(['nombreAlimento' => $nombreAlimento]);
    
        // Asumimos que se puede devolver más de una unidad de medida
        return $query->fetchAll(PDO::FETCH_COLUMN);
    }
    

}
