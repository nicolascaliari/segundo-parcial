<?php
require __DIR__ . '/../models/Cuenta.php';
require __DIR__ . '/../models/Movimientos.php';
require __DIR__ . '/./MovimientosController.php';
class CuentaController
{

    public static function InsertarCuenta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        $tipoDoc = $parametros['tipoDoc'];
        $numeroDoc = $parametros['numeroDoc'];
        $mail = $parametros['mail'];
        $tipoCuenta = $parametros['tipoCuenta'];
        $saldo = $parametros['saldo'];
        $imagen = $parametros['imagen'];
        $numeroCuenta = $parametros['numeroCuenta'];

        $cuenta = new Cuenta();
        $cuenta->nombre = $nombre;
        $cuenta->tipoDoc = $tipoDoc;
        $cuenta->numeroDoc = $numeroDoc;
        $cuenta->mail = $mail;
        $cuenta->tipoCuenta = $tipoCuenta;
        $cuenta->saldo = $saldo;
        $cuenta->numeroCuenta = $numeroCuenta;

        if (isset($_FILES["imagen"])) {

            $rutaImagen = __DIR__ . '/../images/imagenesCuentas/';


            var_dump($rutaImagen);

            $imagen = $_FILES['imagen'];
            $destino = $cuenta->DefinirDestinoImagen($rutaImagen);
            move_uploaded_file($imagen['tmp_name'], $destino);

            $cuenta->imagen = $destino;
        } else {
            $imagen = "";
        }

        $cuenta->CraerCuenta();

        $payload = json_encode(array("mensaje" => "Cuenta creado con exito"));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }



    public static function DepositarCuenta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $numeroCuenta = $parametros['numeroCuenta'];
        $tipoCuenta = $parametros['tipoCuenta'];
        $deposito = $parametros['deposito'];
        $imagen = $_FILES['imagen'];
        $tipoMovimiento = "deposito";

        if ($cuenta = Cuenta::CuentaExiste($numeroCuenta, $tipoCuenta)) {
            $nuevoSaldo = $cuenta->saldo + $deposito;
            Cuenta::ActualizarSaldo($nuevoSaldo, $numeroCuenta);

            MovimientosController::InsertarMovimientoDeposito($deposito, $tipoCuenta, $numeroCuenta, $cuenta, $imagen, $tipoMovimiento);


            $payload = json_encode(array("mensaje" => "Deposito realizado con exito"));
        } else {
            $payload = json_encode(array("mensaje" => "Cuenta no encontrada"));
        }

        // Devuelve la respuesta como JSON
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }



    public static function RetirarCuenta($require, $response, $args)
    {
        $parametros = $require->getParsedBody();

        $tipoCuenta = $parametros['tipoCuenta'];
        $monto = $parametros['monto'];
        $numeroCuenta = $parametros['numeroCuenta'];
        $tipoMovimiento = "retiro";

        if ($cuenta = Cuenta::CuentaExiste($numeroCuenta, $tipoCuenta)) {

            if (Cuenta::VerificarSaldo($monto, $numeroCuenta)) {

                Cuenta::RetirarDinero($monto, $numeroCuenta);
                MovimientosController::InsertarMovimientoRetiro($monto, $tipoCuenta, $numeroCuenta, $cuenta, $tipoMovimiento);

                $payload = json_encode(array("mensaje" => "Retiro realizado con éxito"));

            } else {
                $payload = json_encode(array("mensaje" => "Saldo insuficiente"));
            }

        } else {
            $payload = json_encode(array("mensaje" => "Cuenta no encontrada"));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }





    public static function ConsultarCuenta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $numeroCuenta = $parametros['numeroCuenta'];
        $tipoCuenta = $parametros['tipoCuenta'];

        if ($cuenta = Cuenta::CuentaExiste($numeroCuenta, $tipoCuenta)) {
            $mensaje = 'La moneda de la cuenta consultada es ' . $cuenta->tipoCuenta . ' y su saldo es de ' . $cuenta->saldo;
            $response->getBody()->write($mensaje);
        } else {
            $payload = json_encode(array("mensaje" => "Cuenta no encontrada"));
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function EliminarCuentaController($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if (isset($parametros['numeroCuenta'])) {
            $numeroCuenta = $parametros['numeroCuenta'];

            $cuenta = Cuenta::TraerCuentaPorNumero($numeroCuenta);

            if ($cuenta) {
                if (Cuenta::EliminarCuenta($numeroCuenta)) {
                    $payload = json_encode(array("mensaje" => "Cuenta eliminado con exito"));
                } else {
                    $payload = json_encode(array("mensaje" => "Error al eliminar Cuenta"));
                }
            } else {
                $payload = json_encode(array("mensaje" => "Cuenta no encontrado"));
            }
        } else {
            $payload = json_encode(array("mensaje" => "ID de Cuenta no proporcionado"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader("Content-Type", "application/json");
    }


    public static function ModificarCuentaController($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $numeroCuenta = $parametros['numeroCuenta'];
        $tipoCuenta = $parametros['tipoCuenta'];

        $cuenta = Cuenta::TraerCuentaPorNumero($numeroCuenta);

        if ($cuenta !== false) {

            if (isset($parametros['nombre'])) {
                $cuenta->nombre = $parametros['nombre'];
            } elseif (isset($parametros['tipoDoc'])) {
                $cuenta->tipoDoc = $parametros['tipoDoc'];
            } elseif (isset($parametros['mail'])) {
                $cuenta->mail = $parametros['mail'];
            }elseif (isset($parametros['numeroDoc'])) {
                $cuenta->numeroDoc = $parametros['numeroDoc'];
            }elseif (isset($parametros['estado'])) {
                $cuenta->estado = $parametros['estado'];
            }

            Cuenta::ModificarCuenta($numeroCuenta, $cuenta->nombre, $cuenta->tipoDoc, $cuenta->mail,$cuenta->numeroDoc,$cuenta->estado);

            $payload = json_encode(array("mensaje" => "Cuenta modificado con éxito"));
        } else {
            $payload = json_encode(array("mensaje" => "Error en modificar Cuenta. Cuenta no encontrado"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

}

?>