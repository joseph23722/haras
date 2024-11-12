<?php
require_once 'Conexion.php';

class Implemento extends Conexion
{
    private $pdo;

    public function __CONSTRUCT()
    {
        $this->pdo = parent::getConexion();
    }

    public function listadoTipoMovimiento(): array
    {
        return parent::getData("spu_listar_tipo_movimiento");
    }

    public function listarProductosporInventario(int $idTipoinventario): array
    {
        try {
            $sql = "CALL spu_listar_implementos_con_cantidad(:idTipoinventario)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':idTipoinventario', $idTipoinventario, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Error al buscar el equino: " . $e->getMessage()];
        }
    }

    public function registroImplemento($params = []): array
    {
        try {
            $cmd = $this->pdo->prepare("CALL spu_registrar_implemento(?, ?, ?, ?, ?)");
            $cmd->execute([
                $params['idTipoinventario'],
                $params['nombreProducto'],
                $params['descripcion'],
                $params['precioUnitario'],
                $params['cantidad'],
            ]);
            // Si no se lanza ningún error dentro del procedimiento, el implemento se registra correctamente
            return [
                'status' => 1,
                'message' => 'Implemento registrado correctamente'
            ];
        } catch (PDOException $e) {
            // Si el procedimiento lanza un error, lo capturamos aquí
            if ($e->getCode() === '45000') {
                return [
                    'status' => -1,
                    'message' => $e->getMessage()
                ];
            }
            return [
                'status' => -1,
                'message' => 'Error al registrar el implemento: ' . $e->getMessage()
            ];
        }
    }

    public function registrarEntrada($params = []): array
    {
        try {
            // Llamada al procedimiento para registrar el movimiento de entrada
            $cmd = $this->pdo->prepare("CALL spu_movimiento_implemento(?, ?, ?, ?, ?, ?)");
            $cmd->execute([
                1,  // Tipo de movimiento 1 = Entrada
                $params['idTipoinventario'],
                $params['idInventario'],
                $params['cantidad'],
                $params['precioUnitario'],
                $params['descripcion'],
            ]);

            // Si el procedimiento se ejecuta sin errores
            return [
                'status' => 1,
                'message' => 'Entrada registrada correctamente'
            ];
        } catch (PDOException $e) {
            // Si el procedimiento lanza un error, lo capturamos aquí
            if ($e->getCode() === '45000') {
                return [
                    'status' => -1,
                    'message' => $e->getMessage()
                ];
            }
            return [
                'status' => -1,
                'message' => 'Error al registrar la entrada del implemento: ' . $e->getMessage()
            ];
        }
    }

    public function registrarSalida($params = []): array
    {
        try {
            // Llamada al procedimiento para registrar el movimiento de salida
            $cmd = $this->pdo->prepare("CALL spu_movimiento_implemento(?, ?, ?, ?, ?, ?)");
            $cmd->execute([
                2,  // Tipo de movimiento 2 = Salida
                $params['idTipoinventario'],
                $params['idInventario'],
                $params['cantidad'],
                null,  // precioUnitario no es necesario para salidas
                $params['descripcion'],
            ]);

            // Si el procedimiento se ejecuta sin errores
            return [
                'status' => 1,
                'message' => 'Salida registrada correctamente'
            ];
        } catch (PDOException $e) {
            // Capturar errores específicos del procedimiento
            if ($e->getCode() === '45000') {
                return [
                    'status' => -1,
                    'message' => $e->getMessage()
                ];
            }
            return [
                'status' => -1,
                'message' => 'Error al registrar la salida del implemento: ' . $e->getMessage()
            ];
        }
    }
}
