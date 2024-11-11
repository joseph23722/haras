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
}
