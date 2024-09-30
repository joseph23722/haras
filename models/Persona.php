<?php

require_once 'Conexion.php';

class Personal extends Conexion {
    private $pdo;

    public function __CONSTRUCT() {
        $this->pdo = parent::getConexion();
    }

    // Método para registrar una persona
    public function add($params = []): int {
        try {
            $cmd = $this->pdo->prepare("CALL spu_personal_registrar(@idPersonal, ?, ?, ?, ?, ?, ?, ?)");
            $cmd->execute([
                $params['nombres'],
                $params['apellidos'],
                $params['direccion'],
                $params['tipodoc'],
                $params['nrodocumento'],
                $params['numeroHijos'],
                !empty($params['fechaIngreso']) ? $params['fechaIngreso'] : null
            ]);

            // Obtenemos el ID del registro insertado
            $response = $this->pdo->query("SELECT @idPersonal AS idPersonal")->fetch(PDO::FETCH_ASSOC);
            return (int) $response['idPersonal'];

        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
            return -1;
        }
    }

    // Método para buscar una persona por su documento de identidad
    public function searchByDoc($nrodocumento): array {
        try {
            $cmd = $this->pdo->prepare("CALL spu_personal_buscar_dni(?)");
            $cmd->execute([$nrodocumento]);
            return $cmd->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    
    /**
     * Retorna una lista de personal
     */
    public function getAll(): array {
        return parent::getPer("spu_personal_listar");
    }
}



/*

// Ejemplo de cómo registrar personal
$personal = new Personal();
$datos = [
    "nombres" => "Carlos",
    "apellidos" => "Ramirez",
    "direccion" => "Calle Falsa 123",
    "tipodoc" => "DNI",
    "nrodocumento" => "12345678",
    "numeroHijos" => 2,
    "fechaIngreso" => "2024-01-01"
];

$idPersonal = $personal->add($datos);
echo "ID Generado: " . $idPersonal;

// Ejemplo de cómo buscar por documento
$result = $personal->searchByDoc("12345678");
print_r($result);


*/

/* 
$personal = new Personal();
var_dump($personal->getAll()); 
*/