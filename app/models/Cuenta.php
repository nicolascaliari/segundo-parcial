<?php
class Cuenta
{
    public $numeroCuenta;
    public $nombre;
    public $tipoDoc;
    public $numeroDoc;
    public $mail;
    public $tipoCuenta;
    public $saldo;
    public $imagen;
    public $estado;



    public function CraerCuenta()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO cuenta (numeroCuenta, tipoDoc, numeroDoc,mail,tipoCuenta,saldo,imagen,nombre,estado) VALUES (:numeroCuenta, :tipoDoc, :numeroDoc, :mail, :tipoCuenta, :saldo, :imagen, :nombre,:estado)");
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':numeroCuenta', $this->numeroCuenta, PDO::PARAM_INT);
        $consulta->bindValue(':tipoDoc', $this->tipoDoc, PDO::PARAM_STR);
        $consulta->bindValue(':numeroDoc', $this->numeroDoc, PDO::PARAM_STR);
        $consulta->bindValue(':mail', $this->mail, PDO::PARAM_STR);
        $consulta->bindValue(':tipoCuenta', $this->tipoCuenta, PDO::PARAM_STR);
        $consulta->bindValue(':saldo', $this->saldo, PDO::PARAM_STR);
        $consulta->bindValue(':imagen', $this->imagen, PDO::PARAM_STR);
        $consulta->bindValue(':estado', 1, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }



    public static function ActualizarSaldo($saldo, $numeroCuenta)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE cuenta SET saldo = :saldo WHERE numeroCuenta = :numeroCuenta");
        $consulta->bindValue(':saldo', $saldo, PDO::PARAM_STR);
        $consulta->bindValue(':numeroCuenta', $numeroCuenta, PDO::PARAM_INT);
        $consulta->execute();
    }


    public static function TraerCuentaPorNumero($numeroCuenta)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM cuenta WHERE numeroCuenta = ?");
        $consulta->bindValue(1, $numeroCuenta, PDO::PARAM_INT);
        $consulta->execute();
        $cuentas = $consulta->fetchAll(PDO::FETCH_OBJ);
        return $cuentas;
    }
    




    public static function CuentaYaExiste($numeroDeCuenta)
    {

        $arrayCuentas = Cuenta::TraerCuentaPorNumero($numeroDeCuenta);

        $index = -1;
        if (count($arrayCuentas) > 0) {
            foreach ($arrayCuentas as $indice => $cuenta) {
                if ($cuenta->numeroCuenta == $numeroDeCuenta) {
                    $index = $indice;
                    break;
                }
            }
        }
        return $index;
    }




    public static function CuentaExiste($numeroCuenta, $tipoCuenta)
    {
        $cuentas = Cuenta::TraerCuentaPorNumero($numeroCuenta);
        if (count($cuentas) > 0) {
            foreach ($cuentas as $cuenta) {
                if ($cuenta->numeroCuenta == $numeroCuenta && $cuenta->tipoCuenta == $tipoCuenta) {
                    return $cuenta;
                }
            }
        }
        return false;
    }




    public static function VerificarSaldo($monto, $numeroCuenta)
    {
        $cuentas = Cuenta::TraerCuentaPorNumero($numeroCuenta);
        foreach ($cuentas as &$cuenta) {
            if ($cuenta->numeroCuenta == $numeroCuenta) {
                if ($cuenta->saldo > $monto) {
                    return true;
                } else {
                    return false;
                }
            }
        }
        return false;
    }




    public static function RetirarDinero($monto, $numeroCuenta)
    {
        $cuenta = Cuenta::TraerCuentaPorNumero($numeroCuenta);

        if (count($cuenta) > 0) {
            foreach ($cuenta as &$cuenta) {
                if ($cuenta->numeroCuenta == $numeroCuenta) {
                    $cuenta->saldo = $cuenta->saldo - $monto;
                    Cuenta::ActualizarSaldo($cuenta->saldo, $numeroCuenta);
                    return true;
                }
            }
        }
    }



    public static function AjustarCuentaDeposito($numeroCuenta, $ajuste)
    {
        $cuenta = Cuenta::TraerCuentaPorNumero($numeroCuenta);

        if (count($cuenta) > 0) {
            foreach ($cuenta as &$cuenta) {
                if ($cuenta->numeroCuenta == $numeroCuenta) {
                    $cuenta->saldo = $cuenta->saldo + $ajuste;
                    Cuenta::ActualizarSaldo($cuenta->saldo, $numeroCuenta);
                    return true;
                }
            }
        }
    }

    public static function AjustarCuentaRetiro($numeroCuenta, $ajuste)
    {
        $cuenta = Cuenta::TraerCuentaPorNumero($numeroCuenta);

        if (count($cuenta) > 0) {
            foreach ($cuenta as &$cuenta) {
                if ($cuenta->numeroCuenta == $numeroCuenta) {
                    $cuenta->saldo = $cuenta->saldo - $ajuste;
                    Cuenta::ActualizarSaldo($cuenta->saldo, $numeroCuenta);
                    return true;
                }
            }
        }
    }



    public static function ModificarCuenta($numeroCuenta, $nombre, $tipoDoc, $mail,$numeroDoc,$estado){
        $objetoAccesoDato = AccesoDatos::obtenerInstancia(); 
        $consulta = $objetoAccesoDato->prepararConsulta("UPDATE cuenta SET nombre = :nombre, tipoDoc = :tipoDoc, mail = :mail, numeroDoc = :numeroDoc, estado = :estado WHERE numeroCuenta = :numeroCuenta");
        $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->bindValue(':tipoDoc', $tipoDoc, PDO::PARAM_STR);
        $consulta->bindValue(':mail', $mail, PDO::PARAM_STR);
        $consulta->bindValue(':numeroCuenta', $numeroCuenta, PDO::PARAM_INT);
        $consulta->bindValue(':numeroDoc', $numeroDoc, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_INT);
        return $consulta->execute();
    }


    public static function EliminarCuenta($numeroCuenta)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE cuenta SET estado = 0 WHERE numeroCuenta = ?");
        $consulta->bindValue(1, $numeroCuenta, PDO::PARAM_INT);
        return $consulta->execute();
    }





    public function DefinirDestinoImagen($ruta)
    {
        $destino = str_replace('\\', '/', $ruta) . $this->numeroCuenta . '.png';
        return $destino;
    }

}
?>