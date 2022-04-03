<?php
/**
 * File:  index.php
 *
 */

use lbs\fabrication\app\controller\CommandeController;

require_once  __DIR__ . '/../src/vendor/autoload.php';

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
        'dbconf' => __DIR__.'/../src/app/conf/commande.db.conf.ini' ]
];
$c = new \Slim\Container($configuration);

$db = new \Illuminate\Database\Capsule\Manager();
$db->addConnection(parse_ini_file($configuration['settings']['dbconf']));
$db->setAsGlobal();
$db->bootEloquent();

$app = new \Slim\App($c);

$app->get('/commandes[/]',
    CommandeController::class . ':getCommandes'
);


$app->run();