<?php
class Ajuste
{
    public $id;
    public $idOperacion;
    public $motivo;
    public $monto;

   
    public function CrearAjuste()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO ajustes (idOperacion, motivo, monto) VALUES (:idOperacion, :motivo, :monto)");
        $consulta->bindValue(':idOperacion', $this->idOperacion, PDO::PARAM_INT);
        $consulta->bindValue(':motivo', $this->motivo, PDO::PARAM_STR);
        $consulta->bindValue(':monto', $this->monto, PDO::PARAM_INT);
        $consulta->execute();
        return $objAccesoDatos->obtenerUltimoId();
    }



    // public static function BuscarIdOperacion($motivo, $idOperacion)
    // {
    //     if ($motivo == 'deposito') {
    //         $depositos = Deposito::LeerJSONDeposito();
    //         foreach ($depositos as $deposito) {
    //             if ($deposito->id == $idOperacion) {
    //                 return $deposito->monto;
    //             }
    //         }
    //     } else if ($motivo == 'retiro') {
    //         $retiros = Retiro::LeerJSONRetiro();
    //         foreach ($retiros as $retiro) {
    //             if ($retiro->id == $idOperacion) {
    //                 return $retiro;
    //             }
    //         }
    //     }
    // }
}


?>