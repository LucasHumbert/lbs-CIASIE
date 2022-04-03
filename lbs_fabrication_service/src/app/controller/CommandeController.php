<?php
namespace lbs\fabrication\app\controller;

require_once  __DIR__ . '/../../vendor/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Request ;
use \Psr\Http\Message\ResponseInterface as Response ;
use lbs\fabrication\app\models\Commande;

class CommandeController {

    function getCommandes(Request $req, Response $resp, $args) {
        $queryparam = $req->getQueryParams();
        $resp = $resp->withHeader( 'Content-Type', "application/json;charset=utf-8");
        isset($queryparam['size']) ? $size = $queryparam['size'] : $size = 10;
        $cmd = Commande::get()->toArray();
        $count = count($cmd);

        if (isset($queryparam['s'])) {
            $cmd = Commande::where('status', '=', $queryparam['s'])->get()->toArray();
        }

        $dernierePage = ceil(count($cmd) / $size);

        if (!isset($queryparam['page'])) {
            $cmd = array_slice($cmd, 0, $size);
        } else {
            if ($queryparam['page'] < 1) {
                $cmd = array_slice($cmd, 0, $size);
            } elseif ($queryparam['page'] > $dernierePage) {
                $cmd = array_slice($cmd, -$size , $size);
            } else {
                $cmd = array_slice($cmd, ($queryparam['page'] - 1) * $size, $size);
            }
        }

        $result = ["type" => "collection", "count" => $count, "size" => $size, "links" => [
            "next" => ["href" => "/commandes/?page=" . ($queryparam['page'] + 1) . "&size=" . $size],
            "prev" => ["href" => "/commandes/?page=" . ($queryparam['page'] - 1) . "&size=" . $size],
            "last" => ["href" => "/commandes/?page=" . $dernierePage . "&size=" . $size],
            "first" => ["href" => "/commandes/?page=1&size=" . $size]
        ]];
        foreach ($cmd as $item) {
            $result['commands'][] = ['command' => $item, 'links' => ['self' => ['href' => $req->getUri() . '/' . $item['id']]]];
        }
        $resp->getBody()->write(json_encode($result));
        return $resp;
    }

}