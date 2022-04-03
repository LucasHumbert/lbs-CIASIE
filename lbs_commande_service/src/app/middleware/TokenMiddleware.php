<?php
namespace lbs\command\app\middleware;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use lbs\command\app\models\Commande;
use Slim\Http\Request;
use Slim\Http\Response;

require_once  __DIR__ . '/../../vendor/autoload.php';

class TokenMiddleware {
    function checkToken(Request $rq, Response $rs, callable $next) {
        $queryparam = $rq->getQueryParams();
        $token = null;
        if (!empty($queryparam)) {
            if (isset($queryparam['token'])) {
                $token = $queryparam['token'];
            } else {
                $rs = $rs->withStatus(404)->withHeader('Content-Type', 'application/json');
                $rs->getBody()->write(json_encode([
                    "error" => 404,
                    "message" => 'Paramètre invalide',
                ]));
                return $rs;
            }
        } else {
            $data = $rq->getHeader('token')[0];
            if (isset($data)) {
                $token = $data;
            } else {
                $rs = $rs->withStatus(404)->withHeader('Content-Type', 'application/json');
                $rs->getBody()->write(json_encode([
                    "error" => 404,
                    "message" => 'Aucun parametre',
                ]));
                return $rs;
            }
        }

        $id = $rq->getAttribute('route')->getArgument( 'id');


        $commande = Commande::where('id', '=', $id)
            ->firstOrFail();

        if ($commande->token == $token){
            return $next($rq, $rs);
        } else {
            $rs = $rs->withStatus(404)->withHeader('Content-Type', 'application/json');
            $rs->getBody()->write(json_encode([
                "error" => 404,
                "message" => 'Token invalide',
            ]));
            return $rs;
        }
    }
}