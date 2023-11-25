<?php


class MovimientosController
{
    public static function InsertarMovimientoDeposito($deposito, $tipoCuenta, $numeroCuenta, $cuenta, $imagen, $tipoMovimiento)
    {
        $movimiento = new Movimientos();
        $movimiento->fecha = date("Y-m-d");
        $movimiento->monto = $deposito;
        $movimiento->tipoCuenta = $tipoCuenta;
        $movimiento->numeroCuenta = $numeroCuenta;
        $movimiento->cuenta = $cuenta;
        $movimiento->tipoMovimiento = $tipoMovimiento;
        $movimiento->CrearMovimiento();

        $respuesta = 'Se depositó exitosamente. ';
        $ruta = __DIR__ . '/../images/imagenesDeposito2023/';

        if (move_uploaded_file($imagen['tmp_name'], $movimiento->DestinoImagenDeposito($ruta))) {
            $respuesta = $respuesta . ' ' . 'Se guardó la imagen';
        } else {
            $respuesta = $respuesta . ' ' . 'La imagen no pudo ser guardada';
        }
    }



    public static function InsertarMovimientoRetiro($deposito, $tipoCuenta, $numeroCuenta, $cuenta, $tipoMovimiento)
    {
        $movimiento = new Movimientos();
        $movimiento->fecha = date("Y-m-d");
        $movimiento->monto = $deposito;
        $movimiento->tipoCuenta = $tipoCuenta;
        $movimiento->numeroCuenta = $numeroCuenta;
        $movimiento->cuenta = $cuenta;
        $movimiento->tipoMovimiento = $tipoMovimiento;
        $movimiento->CrearMovimiento();
    }




    //consultas de movimientos a deposito

    public static function ConsultarDepositoTotalPorTipoYMoneda($request, $response, $args)
    {
        $tipoCuenta = $request->getQueryParams()['tipoCuenta'];
        $fecha = $request->getQueryParams()['fecha'] ?? null;

        if ($fecha !== null) {
            $fecha = $_GET["fecha"];
            $total = Movimientos::MovimientosPorTipoYMoneda($tipoCuenta, $fecha);

            $response->getBody()->write(json_encode(['total' => $total]));
        } else {
            $fechaAnterior = date("d-m-Y", strtotime(date("d-m-Y") . "-1 day"));
            $total = Movimientos::MovimientosPorTipoYMoneda($tipoCuenta, $fechaAnterior);

            $response->getBody()->write(json_encode(['total' => $total]));
        }

        $response = $response->withHeader('Content-Type', 'application/json');

        return $response;
    }



    public static function ConsultarDepositoPorUsuario($request, $response, $args)
    {
        $numeroCuenta = $request->getQueryParams()['numeroCuenta'];
        $tipoCuenta = $request->getQueryParams()['tipoCuenta'];
    
        if (Cuenta::CuentaExiste($numeroCuenta, $tipoCuenta)) {
            $movimientos = Movimientos::MovimientosPorUsuario($numeroCuenta);
            $response->getBody()->write(json_encode(['movimientos' => $movimientos]));
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            $response->getBody()->write(json_encode(['mensaje' => 'Cuenta no encontrada']));
            return $response->withHeader('Content-Type', 'application/json');
        }
    }
    
    




}

?>