<?php
require_once 'Conexion.php';

class Registrarequino extends Conexion
{
    private $pdo;

    public function __CONSTRUCT()
    {
        $this->pdo = parent::getConexion();
    }

    // Registra un nuevo equino
    public function registrarEquino($data)
    {
        try {
            $sql = "CALL spu_equino_registrar(?,?,?,?,?,?,?,?,?,?,?)";
            $stmt = $this->pdo->prepare($sql);

            $nombreEquino = ($data['nombreEquino']);
            $idPropietario = !empty($data['idPropietario']) ? $data['idPropietario'] : null;
            $fechaNacimiento = !empty($data['fechaNacimiento']) ? $data['fechaNacimiento'] : null;
            $sexo = ($data['sexo']);
            $idTipoEquino = ($data['idTipoEquino']);
            $idNacionalidad = !empty($data['idNacionalidad']) ? $data['idNacionalidad'] : null;
            $detalles = !empty($data['detalles']) ? $data['detalles'] : null;
            $pesokg = !empty($data['pesokg']) ? $data['pesokg'] : null;
            $fotografia = ($data['fotografia']); // Recibe el public_id de la imagen guardada en CLOUDINARY
            $fechaentrada = !empty($data['fechaentrada']) ? $data['fechaentrada'] : null;
            $fechasalida = !empty($data['fechasalida']) ? $data['fechasalida'] : null;

            // Validación adicional: la fecha de entrada no puede ser mayor a la de salida
            if (!empty($fechaentrada) && !empty($fechasalida) && $fechaentrada > $fechasalida) {
                return ["status" => "error", "message" => "La fecha de entrada no puede ser mayor que la fecha de salida."];
            }

            if (!empty($idPropietario)) {
                $fechaNacimiento = null;
                $pesokg = null;
            }

            $stmt->bindParam(1, $nombreEquino);
            $stmt->bindParam(2, $fechaNacimiento);
            $stmt->bindParam(3, $sexo);
            $stmt->bindParam(4, $detalles);
            $stmt->bindParam(5, $idTipoEquino);
            $stmt->bindParam(6, $idPropietario);
            $stmt->bindParam(7, $pesokg);
            $stmt->bindParam(8, $idNacionalidad);
            $stmt->bindParam(9, $fotografia);
            $stmt->bindParam(10, $fechaentrada);
            $stmt->bindParam(11, $fechasalida);

            $stmt->execute();

            return ["status" => "success", "message" => "Equino registrado exitosamente."];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Error al registrar el equino: " . $e->getMessage()];
        }
    }

    public function listarPropietarios(): array
    {
        return parent::getData("spu_listar_haras");
    }

    public function listarTipoEquinos(): array
    {
        return parent::getData("spu_listar_tipoequinos");
    }

    public function listadoEquinos(): array
    {
        return parent::getData("spu_equinos_listar");
    }

    public function buscarEquinoPorNombre($nombreEquino): array
    {
        try {
            $sql = "CALL spu_buscar_equino_por_nombre(:nombreEquino)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nombreEquino', $nombreEquino, PDO::PARAM_STR);

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Error al buscar el equino: " . $e->getMessage()];
        }
    }

    public function buscarEquinosGenerales($nombreEquino): array
    {
        try {
            $sql = "CALL spu_buscar_equino_por_nombre_general(:nombreEquino)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nombreEquino', $nombreEquino, PDO::PARAM_STR);

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Error al buscar el equino: " . $e->getMessage()];
        }
    }

    public function buscarNacionalidad($nacionalidad)
    {
        try {
            $sql = "CALL spu_buscar_nacionalidad(:nacionalidad)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nacionalidad', $nacionalidad, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Error al buscar la nacionalidad: " . $e->getMessage()];
        }
    }

    // Obtener historial de un equino
    public function obtenerHistorialEquino($idEquino)
    {
        try {
            $sql = "CALL spu_obtener_historial_equino(:idEquino)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':idEquino', $idEquino, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Error al obtener el historial: " . $e->getMessage()];
        }
    }
}
