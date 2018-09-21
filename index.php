<?php

require 'Slim/Slim.php';
require 'CorsSlim/CorsSlim.php';

require 'Models/AccessLevels.php';
require 'Models/User.php';
require 'Models/Organization.php';
require 'Models/Log.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

$corsOptions = array("origin" => "*", "allowMethods" => array("POST, GET, OPTIONS, PUT, DELETE"));

$cors = new \CorsSlim\CorsSlim($corsOptions);

$app->add($cors);


//public routes
$app->hook('slim.before.dispatch', function () use ($app) {
    
    $publicRoutes = array('/login');
    
    if(!in_array($app->router()->getCurrentRoute()->getPattern(), $publicRoutes)){ 
        
        $token = validateToken();
        //print_r($token['type']);
        if($token['type'] == FALSE){
            //não autoriza
            $app->halt(401);
        }
    }
});
  

$app->post('/login', 'login');
//endpoints
//Access Levels
$app->get('/accesslevels/:id', array('AccessLevels','getOneAccessLevels'));
$app->get('/accesslevels',     array('AccessLevels','getAllAccessLevels'));
$app->get('/accesslevelsnotroot',     array('AccessLevels','getAllAccessLevelsNotRoot'));

//USER
$app->post('/user', array('User','newUser'));
$app->put('/user/:id', array('User','updateUser'));
$app->delete('/user/:id', array('User','deleteUser'));
$app->get('/user/:id', array('User','getOneUser'));
$app->get('/user', array('User','getAllUser'));
$app->get('/user/organization/:organization', array('User','getAllUserOfOrganization'));


//Log
$app->post('/log', array('Log','newLog'));
$app->get('/log/user/:id/module/:module', array('Log','getAllLogOfUserByModule'));



//Organization
$app->post('/organization', array('Organization','newOrganization'));
$app->put('/organization/:id', array('Organization','updateOrganization'));
$app->delete('/organization/:id', array('Organization','deleteOrganization'));
$app->get('/organization/:id', array('Organization','getOneOrganization'));
$app->get('/organization', array('Organization','getAllOrganization'));





function login(){
    
    $request = \Slim\Slim::getInstance()->request();
	$login = json_decode($request->getBody());
     //logar na API por acesso   
        $sql = "SELECT u.id, u.username, u.token, al.code AS access_levels FROM user u INNER JOIN access_levels al ON al.idaccess_levels =u.access_levels WHERE u.login = :login AND u.password = :password";	
        
        if((isset($login->password)) && (isset($login->login))){
            
         $login->password = md5($login->password);
          
            
            try {

                $db = getConnection();
                $stmt = $db->prepare($sql); 
                $stmt->bindParam(':login', $login->login, PDO::PARAM_STR);
                $stmt->bindParam(':password', $login->password, PDO::PARAM_STR);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_OBJ);
                $db = null;

                if($user){
                    $token = bin2hex(openssl_random_pseudo_bytes(16)).$user->access_levels; 
                    $db = getConnection();
                    $sql = "UPDATE user SET token = :token WHERE id = :id";
                    $stmt = $db->prepare($sql); 
                    $stmt->bindParam(':token', $token, PDO::PARAM_STR);
                    $stmt->bindParam(':id', $user->id, PDO::PARAM_INT);
                    $stmt->execute();
                    $db = null;
                    print('{"type":true,"data":"'.$user->username.'","id":"'.$user->id.'","token":"'.$token.'","access_levels":"'.$user->access_levels.'"}'); 
                } else {
                    print('{"type":false,"data":"Incorrect email/password"}');    
                }

            } catch(PDOException $e) {
                echo '{"type":false,"data":"'.$e->getMessage().'"}'; 
            }
        }else{
            
            $app = \Slim\Slim::getInstance();
            //request não aceita
            $app->halt(406);
        }
        
}

function validateToken(){
    //validação
    $isset = apache_request_headers();
    if(isset($isset["Authorization"])){

        $head = $isset["Authorization"];//apache_request_headers()["Authorization"];
            
        $token = explode(" ", $head);
        $token = $token[1];
        
      
        $sql = "SELECT username, token FROM user WHERE token = :token;";    

        $db = getConnection();
        $stmt = $db->prepare($sql); 
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_OBJ);
        $db = null;

        if($user) {
            $return = array("type" => TRUE, "data" => $user->username, "token" => $user->token);
        } else {
            $return = array("type" => FALSE, "data" => "Incorrect Token", "token"=> NULL);
        }
    }else{
        $return = array("type" => FALSE, "data" => "No Token", "token"=> NULL);
    }
    
    return $return;
}

function getConnection() {
	$dbhost="localhost";
	$dbuser="test";
	$dbpass="test";
        $dbname="test";

	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));	
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $dbh;
}
//roda API
$app->run();

?>
