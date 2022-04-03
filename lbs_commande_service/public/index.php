<?php
/**
 * File:  index.php
 *
 */

require_once  __DIR__ . '/../src/vendor/autoload.php';

use lbs\command\app\controller\CommandeController;
use lbs\command\app\controller\ItemController;
use lbs\command\app\errors\BadUrl;
use lbs\command\app\errors\Internal;
use lbs\command\app\middleware\TokenMiddleware;
use DavidePastore\Slim\Validation\Validation;
use Respect\Validation\Validator as v;

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
        'dbconf' => __DIR__.'/../src/app/conf/commande.db.conf.ini' ]
];
$c = new \Slim\Container($configuration);

$c['notAllowedHandler'] = Internal::showError($c);
$c['notFoundHandler'] = BadUrl::showError($c);

$db = new \Illuminate\Database\Capsule\Manager();
$db->addConnection(parse_ini_file($configuration['settings']['dbconf']));
$db->setAsGlobal();
$db->bootEloquent();

$validators = [
    'nom' => v::StringType()->alpha() ,
    'mail' => v::email() ,
    'livraison' => [
        'date' => v::date('d-m-Y')->min( 'now' ),
        'heure' => v::date('h-m')
    ],
    'items' => [
        'uri'=> v::url(),
        'q' => v::intType()->min(0),
        'libelle' => v::StringType()->alpha(),
        'tarif' => v::floatType()
    ] ];

$app = new \Slim\App($c);

$app->get('/commandes[/]',
    CommandeController::class . ':getCommandes'
);

$app->get('/commandes/{id}[/]',
    CommandeController::class . ':getCommande'
)->add(TokenMiddleware::class . ':checkToken');

$app->put('/commandes/{id}[/]',
    CommandeController::class . ':putCommande'
);

$app->get('/commandes/{id}/items[/]',
    ItemController::class . ':getItems'
);

$app->post('/commandes[/]',
    CommandeController::class . ':postCommande'
)->add(new Validation($validators));

$app->run();