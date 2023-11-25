<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
require_once './models/Ajuste.php';

class AjusteController
{
    public static function InsertarAjuste(Request $request, Response $response, $args)
    {
        $parametros = $request->getParsedBody();

        $idOperacion = $parametros['idOperacion'];
        $motivo = $parametros['motivo'];
        $monto = $parametros['monto'];

        if ($motivo == "deposito") {
            if ($deposito = Movimientos::BuscarDepositoPorId($idOperacion)) {
                Cuenta::AjustarCuentaDeposito($deposito[0]->numeroCuenta, $monto);
                
                $ajuste = new Ajuste();

                $ajuste->idOperacion = $idOperacion;
                $ajuste->motivo = $motivo;
                $ajuste->monto = $monto;

                $ajuste->CrearAjuste();
            }
        } else if ($motivo == "retiro") {
            if ($retiro = Movimientos::BuscarRetiroPorId($idOperacion)) {
                Cuenta::AjustarCuentaRetiro($retiro[0]->numeroCuenta, $monto);

                $ajuste = new Ajuste();

                $ajuste->idOperacion = $idOperacion;
                $ajuste->motivo = $motivo;
                $ajuste->monto = $monto;

                $ajuste->CrearAjuste();
            }
        }

        // Respuesta JSON
        $payload = json_encode(['mensaje' => 'Ajuste realizado con éxito']);

        // Establece el encabezado Content-Type
        $response->getBody()->write($payload);
        $response = $response->withHeader('Content-Type', 'application/json');

        // Devuelve la respuesta
        return $response;
    }
}

?>
