<?php

class Movimientos
{
    public $id;
    public $fecha;
    public $monto;
    public $tipoCuenta;
    public $numeroCuenta;
    public $cuenta;
    public $tipoMovimiento;


    public function CrearMovimiento()
    {
        $cuentajson = json_encode($this->cuenta);


        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO movimientos (fecha, monto, tipoCuenta, numeroCuenta, cuenta, tipoMovimiento) VALUES (:fecha, :monto, :tipoCuenta, :numeroCuenta, :cuenta, :tipoMovimiento)");
        $consulta->bindValue(':fecha', $this->fecha, PDO::PARAM_STR);
        $consulta->bindValue(':monto', $this->monto, PDO::PARAM_INT);
        $consulta->bindValue(':tipoCuenta', $this->tipoCuenta, PDO::PARAM_STR);
        $consulta->bindValue(':numeroCuenta', $this->numeroCuenta, PDO::PARAM_INT);
        $consulta->bindValue(':cuenta', $cuentajson, PDO::PARAM_STR);
        $consulta->bindValue(':tipoMovimiento', $this->tipoMovimiento, PDO::PARAM_STR);



        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }


    public function DestinoImagenDeposito($ruta)
    {
        $destino = $ruta . "\\" . $this->tipoCuenta . "-" . $this->numeroCuenta . "-" . $this->id . ".png";
        return $destino;
    }


    public static function BuscarDepositoPorId($idOperacion)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM movimientos WHERE id = ? AND tipoMovimiento = 'deposito'");
        $consulta->bindValue(1, $idOperacion, PDO::PARAM_INT);
        $consulta->execute();
        $deposito = $consulta->fetchAll(PDO::FETCH_OBJ);
        return $deposito;
    }

    public static function BuscarRetiroPorId($idOperacion)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM movimientos WHERE id = ? AND tipoMovimiento = 'retiro'");
        $consulta->bindValue(1, $idOperacion, PDO::PARAM_INT);
        $consulta->execute();
        $retiro = $consulta->fetchAll(PDO::FETCH_OBJ);
        return $retiro;
    }



    // movimientos por deposito

    public static function MovimientosPorTipoYMoneda($tipoCuenta, $fecha)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT SUM(monto) AS total FROM movimientos WHERE tipoCuenta = :tipoCuenta AND fecha = :fecha AND tipoMovimiento = 'deposito'");
        $consulta->bindValue(':tipoCuenta', $tipoCuenta, PDO::PARAM_STR);
        $consulta->bindValue(':fecha', $fecha, PDO::PARAM_STR);
        $consulta->execute();
        $total = $consulta->fetchObject();
        return $total->total;
    }


    public static function MovimientosPorUsuario($numeroCuenta)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM movimientos WHERE numeroCuenta = :numeroCuenta AND tipoMovimiento = 'deposito'");
        $consulta->bindValue(':numeroCuenta', $numeroCuenta, PDO::PARAM_INT);
        $consulta->execute();
        $movimientos = $consulta->fetchAll(PDO::FETCH_OBJ);
        return $movimientos;
    }
}

?>