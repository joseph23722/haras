<?php
require_once 'Conexion.php';

class RevisionBasica extends Conexion
{
    private $pdo;

    public function __CONSTRUCT()
    {
        $this->pdo = parent::getConexion();
    }
    // Método para registrar una revisión equina
    public function registrarRevisionEquino($params = [])
    {
        try {
            // Verificación de los parámetros enviados al modelo
            error_log("Parámetros enviados al modelo registrarRevisionEquino: " . json_encode($params));

            // Validación de campos obligatorios
            $obligatorios = [
                'idEquino' => $params['idEquino'] ?? null,
                'tiporevision' => $params['tiporevision'] ?? null,
                'fecharevision' => $params['fecharevision'] ?? null,
                'observaciones' => $params['observaciones'] ?? null
            ];

            foreach ($obligatorios as $campo => $valor) {
                if (empty($valor)) {
                    throw new Exception("Falta el campo obligatorio: $campo.");
                }
            }

            // Preparar y ejecutar el procedimiento almacenado
            $query = $this->pdo->prepare("CALL spu_registrar_revision_equino(?, ?, ?, ?, ?, ?)");
            $query->execute([
                $params['idEquino'],
                $params['idPropietario'] ?? null,
                $params['tiporevision'],
                $params['fecharevision'],
                $params['observaciones'], // Describirá el proceso que se realizó
                $params['costorevision'] ?? null
            ]);

            // Verificar el resultado de la ejecución
            if ($query->rowCount() > 0) {
                return ['status' => 'success', 'message' => 'Revisión equina registrada correctamente.'];
            } else {
                return ['status' => 'error', 'message' => 'No se pudo registrar la revisión equina.'];
            }
        } catch (Exception $e) {
            // Manejo de excepciones y registro de errores
            error_log("Error en registrarRevisionEquino: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function listarYeguasPorPropietario($idPropietario)
    {
        try {
            $stmt = $this->pdo->prepare("CALL spu_listaryeguas_porpropietarios(:idPropietario)");
            $stmt->bindParam(':idPropietario', $idPropietario, PDO::PARAM_INT);
            $stmt->execute();
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $resultados;
        } catch (PDOException $e) {
            echo "Error al ejecutar el procedimiento: " . $e->getMessage();
            return [];
        }
    }
}