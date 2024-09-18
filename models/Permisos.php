<?php
require_once 'Conexion.php';

class Permisos extends Conexion {
    private $pdo;

    public function __CONSTRUCT() {
        $this->pdo = parent::getConexion();
    }

    // Procedimiento para insertar un nuevo rol
    public function insertarRol($nombreRol) {
        try {
            $query = $this->pdo->prepare("CALL insertarRol(?)");
            $query->execute([$nombreRol]);
            return $query->fetch(PDO::FETCH_ASSOC)['idRol'];  // Retornar el ID del rol recién insertado
        } catch (Exception $e) {
            throw new Exception("Error al insertar rol: " . $e->getMessage());
        }
    }

    // Procedimiento para insertar un nuevo permiso
    public function insertarPermiso($nombrePermiso, $descripcionPermiso) {
        try {
            $query = $this->pdo->prepare("CALL insertarPermiso(?, ?)");
            $query->execute([$nombrePermiso, $descripcionPermiso]);
            return $query->fetch(PDO::FETCH_ASSOC)['idPermiso'];  // Retornar el ID del permiso recién insertado
        } catch (Exception $e) {
            throw new Exception("Error al insertar permiso: " . $e->getMessage());
        }
    }

    // Procedimiento para asignar permisos a un rol
    public function asignarPermisosRol($idRol, $permisosSeleccionados) {
        try {
            $permisos = implode(',', $permisosSeleccionados);  // Convertir el array de permisos en una cadena separada por comas
            $query = $this->pdo->prepare("CALL asignarPermisosRol(?, ?)");
            $query->execute([$idRol, $permisos]);
        } catch (Exception $e) {
            throw new Exception("Error al asignar permisos: " . $e->getMessage());
        }
    }

    // Listar roles
    public function listarRoles() {
        try {
            $query = $this->pdo->prepare("SELECT idRol, nombreRol FROM Roles");
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);

            // Depuración: Verificar si se encontraron roles
            if (empty($result)) {
                throw new Exception("No se encontraron roles en la base de datos.");
            }

            return $result;
        } catch (Exception $e) {
            throw new Exception("Error al listar roles: " . $e->getMessage());
        }
    }

    // Listar todos los permisos
    public function listarPermisos() {
        try {
            $query = $this->pdo->prepare("SELECT * FROM Permisos");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al listar permisos: " . $e->getMessage());
        }
    }

    // Obtener los permisos de un rol específico
    public function obtenerPermisosRol($idRol) {
        try {
            $query = $this->pdo->prepare("SELECT idPermiso FROM RolPermisos WHERE idRol = ?");
            $query->execute([$idRol]);
            return $query->fetchAll(PDO::FETCH_COLUMN);
        } catch (Exception $e) {
            throw new Exception("Error al obtener permisos del rol: " . $e->getMessage());
        }
    }
}
