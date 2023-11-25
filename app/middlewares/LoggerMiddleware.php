<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once(__DIR__ . '/../utils/autenticadorJWT.php');
class LoggerMidleware
{


    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);

        try {
            AutentificadorJWT::VerificarToken($token);
            $response = $handler->handle($request);
        } catch (Exception $e) {
            $response = new Response();
            $payload = json_encode(array('mensaje' => 'ERROR: Hubo un error con el TOKEN'));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }



    // public function __invoke(Request $request, RequestHandler $handler): Response{
    //     $parametros = $request->getQueryParams();
    //     $permiso = $parametros['permiso'];
    //     if ($permiso === 'admin') {
    //         $response = $handler->handle($request);
    //     } else {
    //         $response = new Response();
    //         $payload = json_encode(array('mensaje' => 'Acceso autorizado solo para admins'));
    //         $response->getBody()->write($payload);
    //     }

    //     return $response->withHeader('Content-Type', 'application/json');
    // }
}

?>