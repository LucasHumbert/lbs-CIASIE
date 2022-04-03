<?php

namespace lbs\command\app\controller;

require_once __DIR__ . '/../../vendor/autoload.php';

use Illuminate\Database\Eloquent\ModelNotFoundException;
use lbs\command\app\models\Item;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use lbs\command\app\models\Commande;

class ItemController
{
    function getItems(Request $req, Response $res, $args)
    {
        $resp = $res->withHeader('Content-Type', "application/json;charset=utf-8");
        $cmd = Item::where('command_id', '=', $args['id'])->get();
        $cmd->makeHidden(['uri', 'command_id']);

        $datas = ["type" => "collection",
            "count" => count($cmd),
            "items" => $cmd];

        $resp->getBody()->write(json_encode($datas));
        return $resp;
    }
}