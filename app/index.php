<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

// require_once './controllers/UsuarioController.php';
// require_once './controllers/ProductoController.php';
// require_once './controllers/MesaController.php';
// require_once './controllers/PedidosController.php';
require_once './controllers/CuentaController.php';
require_once './controllers/AjusteController.php';
require_once './controllers/MovimientosController.php';
require_once './db/AccesoDatos.php';
require_once './middlewares/LoggerMiddleware.php';

require_once(__DIR__ . '/./middlewares/AuthMiddleware.php');
require_once(__DIR__ . '/./middlewares/LoggerMiddleware.php');


require __DIR__ . '/../vendor/autoload.php';

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();


// $app->group('/auth', function (RouteCollectorProxy $group) {
//     $group->post('/login', \UsuarioController::class . ':login');
// });


$app->group('/', function (RouteCollectorProxy $group) {

    $group->get('[/]', function (Request $request, Response $response) {
        $response->getBody()->write("Bienvenido a la api de la comanda");
        return $response;
    });
});



// $app->group('/usuarios', function (RouteCollectorProxy $group) {
//     $group->get('[/]', \UsuarioController::class . ':TraerUsuariosController')
//         ->add(new LoggerMidleware())
//         ->add(new AuthMiddleware('admin'));

//     $group->post('/insertar', \UsuarioController::class . ':InsertarUsuarioController')
//         ->add(new LoggerMidleware())
//         ->add(new AuthMiddleware('admin'));

//     $group->delete('/eliminar', \UsuarioController::class . ':EliminarUsuarioController')
//         ->add(new LoggerMidleware())
//         ->add(new AuthMiddleware('admin'));

//     $group->post('/cambiar', \UsuarioController::class . ':ModificarUsuarioController')
//         ->add(new LoggerMidleware())
//         ->add(new AuthMiddleware('admin'));

//     $group->get('/guardar', \UsuarioController::class . ':GuardarUsuarios')
//         ->add(new LoggerMidleware())
//         ->add(new AuthMiddleware('admin'));

//     $group->get('/cargar', \UsuarioController::class . ':CargarUsuarios')
//         ->add(new LoggerMidleware())
//         ->add(new AuthMiddleware('admin'));
// });




$app->group('/cuentas', function (RouteCollectorProxy $group) {
    $group->post('/insertar', \CuentaController::class . ':InsertarCuenta');
    $group->post('/depositar', \CuentaController::class . ':DepositarCuenta');
    $group->post('/consultar', \CuentaController::class . ':ConsultarCuenta');
    $group->post('/retirar', \CuentaController::class . ':RetirarCuenta');
    $group->delete('/eliminar', \CuentaController::class . ':EliminarCuentaController');
    $group->post('/modificar', \CuentaController::class . ':ModificarCuentaController');
});


$app->group('/ajustes', function (RouteCollectorProxy $group) {
    $group->post('/insertar', \AjusteController::class . ':InsertarAjuste');
});


$app->group('/movimientos', function (RouteCollectorProxy $group) {
    $group->get('/totalDeposito', \MovimientosController::class . ':ConsultarDepositoTotalPorTipoYMoneda');
    $group->get('/depositoPorUsuario', \MovimientosController::class . ':ConsultarDepositoPorUsuario');
});






$app->run();
