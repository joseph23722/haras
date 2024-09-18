<?php

require_once 'Conexion.php';

class Personal extends Conexion {
    private $pdo;

    public function __CONSTRUCT() {
        $this->pdo = parent::getConexion();
    }

    // Método para registrar una persona y un usuario combinados
    public function registrarPersonaUsuario($params = []): int {
        $idgenerado = null;
        try {
            $query = $this->pdo->prepare("CALL spu_registrar_persona_usuario(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $query->execute([
                $params['apellidos'],
                $params['nombres'],
                $params['nrodocumento'],
                $params['direccion'],
                $params['tipodoc'],
                $params['numeroHijos'],
                !empty($params['fechaIngreso']) ? $params['fechaIngreso'] : null,
                $params['correo'],
                $params['clave'],  // La contraseña ya debe estar encriptada
                $params['idRol']
            ]);

            $row = $query->fetch(PDO::FETCH_ASSOC);
            $idgenerado = $row['idPersonal'];
        } catch (Exception $e) {
            $idgenerado = -1;
        }
        return $idgenerado;
    }

    // Método para buscar una persona por DNI
    public function searchByDoc($nrodocumento): array {
        try {
            $query = $this->pdo->prepare("CALL spu_personal_buscar_dni(?)");
            $query->execute([$nrodocumento]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
}
