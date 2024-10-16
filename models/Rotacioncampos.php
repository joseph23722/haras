<?php

require_once 'Conexion.php';

class RotacionCampos extends Conexion
{
    private $pdo;

    public function __CONSTRUCT()
    {
        $this->pdo = parent::getConexion();
    }

    public function listarTipoRotaciones(): array
    {
        return parent::getData("spu_tipos_rotaciones_listar");
    }
}
