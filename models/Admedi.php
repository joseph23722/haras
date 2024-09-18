<?php
require_once 'Conexion.php';

class Admi extends Conexion {
    private $pdo;

    public function __CONSTRUCT() {
        $this->pdo = parent::getConexion();
    }

    // Método para registrar medicamentos
    public function registrarMedicamento($params = []) {
        try {
            $query = $this->pdo->prepare("CALL spu_medicamentos_registrar(?,?,?,?,?,?,?,?)");
            $query->execute([
                $params['nombreMedicamento'],
                $params['cantidad'],
                $params['caducidad'],
                $params['precioUnitario'],
                $params['idTipomovimiento'],
                $params['idUsuario'],
                $params['visita'],
                $params['tratamiento']
            ]);

            return $query->rowCount() > 0; // Verifica si se insertó correctamente
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
