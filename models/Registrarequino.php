<?php
require_once 'Conexion.php';

class Registrarequino extends Conexion {
    private $pdo;

    public function __CONSTRUCT() {
        $this->pdo = parent::getConexion();
    }

    // Registra un nuevo equino
    public function registrarEquino($data) {
        try {
            $sql = "CALL spu_equino_registrar(?,?,?,?,?,?,?)";
            $stmt = $this->pdo->prepare($sql);
    
            $nombreEquino = ($data['nombreEquino']);
            $idPropietario = !empty($data['idPropietario']) ? $data['idPropietario'] : null;
            $fechaNacimiento = !empty($data['fechaNacimiento']) ? $data['fechaNacimiento'] : null;
            $sexo = ($data['sexo']);
            $idTipoEquino = ($data['idTipoEquino']);
            $nacionalidad = !empty($data['nacionalidad']) ? $data['nacionalidad'] : null;
            $detalles = !empty($data['detalles']) ? $data['detalles'] : null;

            // Si hay un propietario externo, establece fecha de nacimiento y nacionalidad como null
            if (!empty($idPropietario)) {
                $fechaNacimiento = null;
                $nacionalidad = null;
            }
    
            // Asigna los valores a los parámetros
            $stmt->bindParam(1, $nombreEquino);
            $stmt->bindParam(2, $fechaNacimiento);
            $stmt->bindParam(3, $sexo);
            $stmt->bindParam(4, $detalles);
            $stmt->bindParam(5, $idTipoEquino);
            $stmt->bindParam(6, $idPropietario);
            $stmt->bindParam(7, $nacionalidad);
    
            $stmt->execute();
    
            return ["status" => "success", "message" => "Equino registrado exitosamente."];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Error al registrar el equino: " . $e->getMessage()];
        }
    }
    
    public function listarPropietarios(): array {
        return parent::getData("spu_listar_haras");
    }

    public function listarTipoEquinos(): array {
        return parent::getData("spu_listar_tipoequinos");
    }

    public function listadoEquinos():array{
        return parent::getData("spu_equinos_listar");
      }
}
