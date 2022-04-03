<?php
namespace lbs\command\app\controller;

require_once  __DIR__ . '/../../vendor/autoload.php';

use Illuminate\Database\Eloquent\ModelNotFoundException;
use lbs\command\app\models\Item;
use \Psr\Http\Message\ServerRequestInterface as Request ;
use \Psr\Http\Message\ResponseInterface as Response ;
use lbs\command\app\models\Commande;
use Ramsey\Uuid\Uuid;

class CommandeController {

    function getCommandes(Request $req, Response $resp, $args) {
        $resp = $resp->withHeader( 'Content-Type', "application/json;charset=utf-8" ) ;
        $cmd = Commande::get();
        $resp->getBody()->write($cmd);
        return $resp;
    }

    public function getCommande(Request $request, Response $response, $args): Response
    {
        try {
            $commande = Commande::findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            $response = $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode([
                "error" => 404,
                "message" => 'Model innexistant',
            ]));
            return $response;
        }

        $queryparam = $request->getQueryParams();
        if (!empty($queryparam)) {
            if (isset($queryparam['embed']) && $queryparam['embed'] == 'items') {
                $items = Item::where('command_id', '=', $args['id'])->get();
                $items = $items->makeHidden(['uri', 'command_id']);


                $data = ["type" => "ressource",
                    "commande" => $commande,
                    "items" => $items,
                    "links" => [
                        'items' => ['href' => $request->getUri() . '/items'],
                        'self' => ['href' => $request->getUri() . '']
                    ]];
                $response = $response->withHeader('Content-Type', 'application/json;charset=utf-8');
                $response->getBody()->write(json_encode($data));
                return $response;
            }
        }

        $data = ["type" => "ressource",
            "commande" => $commande,
            "links" => [
                'items' => ['href' => $request->getUri() . '/items'],
                'self' => ['href' => $request->getUri() . '']
            ]];
        $response = $response->withHeader('Content-Type', 'application/json;charset=utf-8');
        $response->getBody()->write(json_encode($data));
        return $response;
    }

    function putCommande(Request $req, Response $res, $args){
        $nom = $req->getParsedBody()['nom'];
        $mail = $req->getParsedBody()['mail'];
        $livraison = $req->getParsedBody()['livraison'];

        $nom = filter_var($nom, FILTER_SANITIZE_STRING);
        $mail = filter_var($mail, FILTER_SANITIZE_EMAIL);


        try {
            $commande = Commande::findOrFail($args['id']);
        }
        catch(ModelNotFoundException $e) {
            $res = $res
                ->withStatus(404)
                ->withHeader("Content-type", "application/json");
            $res->getBody()
                ->write(json_encode([
                    "type" => "error",
                    "error"=> 404,
                    "message" => "Command not found"
                ]));
            return $res;
        }

        $commande->nom = $nom;
        $commande->mail = $mail;
        $commande->livraison = $livraison;
        $commande->save();
    }

    function postCommande (Request $req, Response $res, $args){
        try {
            $data = $req->getParsedBody();
            $commande = new Commande();
            $commande->nom = $data['nom'];
            $commande->mail = $data['mail'];
            $date = strtotime($data['livraison']['date'] . ' ' . $data['livraison']['heure']);
            $commande->livraison = date('Y-m-d h:i:s', $date);
            $commande->id = Uuid::uuid4();
            $commande->token = bin2hex(random_bytes(32));
            $commande->created_at = date('y-m-d h:i:s');

            $montant = 0;
            foreach ($data['items'] as $unItem) {
                $item = new Item();
                $item->uri = $unItem['uri'];
                $item->libelle = $unItem['libelle'];
                $item->tarif = $unItem['tarif'];
                $item->quantite = $unItem['q'];
                $item->command_id = $commande->id;
                //$item->save();

                $montant += $unItem['q'] * $unItem['tarif'];
            }
            $commande->montant = $montant;
            //$commande->save();

        } catch(ModelNotFoundException $e) {
            $res = $res
                ->withStatus(404)
                ->withHeader("Content-type", "application/json");
            $res->getBody()
                ->write(json_encode([
                    "type" => "error",
                    "error"=> 404,
                    "message" => "Command not found"
                ]));
            return $res;
        }
        $res = $res->withHeader('Content-Type', 'application/json;charset=utf-8');
        $res->getBody()->write(json_encode(['commande' => $commande]));
        return $res;
    }
}