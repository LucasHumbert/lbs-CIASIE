<?php
namespace lbs\command\app\errors;

class BadUrl{
    static function showError($c){
        return function ($c) {
            return function($req, $res){
                $res = $res->withHeader( 'Content-Type', "application/json;charset=utf-8" ) ;
                $res = $res->withStatus(400);
                $res->getBody()->write(json_encode(["type" => "error", "error" => 400, "message" => "bad request"]));
                return $res;
            };
        };
    }
}