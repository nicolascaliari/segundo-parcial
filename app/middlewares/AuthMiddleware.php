<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
//require_once /*'./utils/autenticadorJWT.php';*/'C:\xampp\htdocs\slim-php-deployment\utils\autenticadorJWT.php';
require_once(__DIR__ . '/../utils/autenticadorJWT.php');
    class AuthMiddleware{
        public $tipo;
        public function __construct($tipo){$this->tipo = $tipo;}
        public function __invoke(Request $request, RequestHandler $handler): Response
        {   
            $header = $request->getHeaderLine('Authorization');
            $token = trim(explode("Bearer", $header)[1]);
    
            //$data = AutentificadorJWT::ObtenerData($token);
            if(AutentificadorJWT::VerificarTipo($token, $this->tipo)) {
                $response = $handler->handle($request);
            }else{
                $response = new Response();
                $payload = json_encode(array('mensaje' => 'ERROR: solo socios autorizados'));
                $response->getBody()->write($payload);
            }
                
            return $response->withHeader('Content-Type', 'application/json');
        
        }
    }

?>