<?php

namespace functions;

use Ahc\Jwt\JWT;

use Masterminds\HTML5\Exception;
use MysqliDb;

class function_app 
{
    public $data;


    public function getAuthorizationHeader(){
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }


    public function Auth(){
        $mysql = new MysqliDb('localhost', 'user5973_schooltesting', 'cT6gJ2cI3n', 'user5973_schooltesting');
        // $mysql = new MysqliDb('localhost', 'root', '', 'online_testing');
        $jwt = new JWT('secret', 'HS256', 3600, 10);
        $token = $this->getAuthorizationHeader();
        $explode = explode(" ", $token);
        if ($explode[0] == "Bearer"){
            $token = $explode[1];
            if ($token){
                $data = $jwt->decode($token);
                if(!$data){
                    return false;
                }else{
                    $mysql->where('id', $data['user_id']);
                    $userData = $mysql->get('users');
                    if ($userData){
                        return $userData;
                    }else{
                        return  "User Yoq";
                    }
                }
            }else{

            }

        }
    }


    public function getAuthorizationToken(){
        $token = $this->getAuthorizationHeader();
        $explode = explode(" ", $token);
        if ($explode[0] == "Bearer") {
            $token = $explode[1];
            return $token;
        }
    }

    
}