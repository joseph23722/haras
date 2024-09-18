<?php
require_once 'Conexion.php';

class Registrarequino extends Conexion {
    private $pdo;

    public function __CONSTRUCT() {
        $this->pdo = parent::getConexion();
    }

    public function registrarEquino($params = []) {
        try {
            $query = $this->pdo->prepare("CALL spu_equino_registrar(?,?,?,?,?,?,?)");
            $query->execute([
                $params['nombreEquino'],
                $params['fechaNacimiento'],
                $params['sexo'],
                $params['detalles'],
                $params['idPropietario'],
                $params['generacion'],
                $params['nacionalidad']
            ]);

            return $query->fetch(PDO::FETCH_ASSOC)['idEquino'];
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function listarPropietarios() {
        try {
            $query = $this->pdo->prepare("CALL spu_listar_haras()");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC); // Verifica que aquí se está obteniendo un array
        } catch (Exception $e) {
            error_log($e->getMessage());
            return [];
        }
    }
}
