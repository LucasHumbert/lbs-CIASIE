<?php

use lbs\auth\api\controller\LBSAuthController;

require_once __DIR__ . '/../src/vendor/autoload.php';

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
        'dbconf' => __DIR__ . '/../src/app/conf/auth.db.conf.ini']
];
$c = new \Slim\Container($configuration);

$db = new \Illuminate\Database\Capsule\Manager();
$db->addConnection(parse_ini_file($configuration['settings']['dbconf']));
$db->setAsGlobal();
$db->bootEloquent();

$app = new \Slim\App($c);

$app->get('/chingchong[/]',
    LBSAuthController::class . ':chingchong'
);


$app->run();
