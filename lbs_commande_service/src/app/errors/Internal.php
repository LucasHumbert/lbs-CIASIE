<?php
namespace lbs\command\app\errors;

class Internal {
    static function showError($c){
        return function ($c) {
            return function($req, $res, $e){
                $res = $res->withHeader( 'Content-Type', "application/json;charset=utf-8" ) ;
                $res = $res->withStatus(500);
                $res->getBody()->write(json_encode(["type" => "error", "error" => 500, "message" => $e->getMessage()]));
                return $res;
            };
        };
    }
}