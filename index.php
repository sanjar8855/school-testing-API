<?php 

error_reporting(false);
//ini_set("error_reporting", E_ALL);

require "config.php";
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

use Ahc\Jwt\JWT;
use functions\function_app; 

$Httpstatus = new Lukasoppermann\Httpstatus\Httpstatus();

$Httpstatus->setLanguage('en');

$jwt = new JWT('secret', 'HS256', time() + 3600, 10);

$function = new function_app(); 

$mysql = new MysqliDb(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);

$table = $_GET['q'];
$explode = explode("/", $table);
$method = $explode[0];
$submethod = $explode[1];
$subtosub = $explode[2];

if ($method == "login")
{
    $ex = explode("/", $table);
    $mysql->where('login', $_POST['login']);
    $mysql->where('password', $_POST['password']);
    $data = $mysql->get('users');
    if (!$data)
    {
        header('HTTP/1.0 400 Bad Request');
        echo json_encode(['error' => 400, 'message' => 'Bad Request'], JSON_UNESCAPED_UNICODE);
    }
    else {
        header("HTTP/1.1 200 OK");
        $token = $jwt->encode([
            'user_id' => $data[0]['id'],
            'role' => $data[0]['role'],
            'maktabId' => $data[0]['maktab_id'],
            'adress' => 'http://localhost',
        ]);

        $mysql->where('id', $data[0]['id']);
        $mysql->update('users', [
            'token' => $token
        ]);

        echo json_encode(['error' => false, 'message' => "OK!", 'token'=>$token, 'userRole'=>$data[0]['role'], 'maktabId'=>$data[0]['maktab_id']], JSON_UNESCAPED_UNICODE);
    }
}

elseif ($method == "register")
{

    $name = $_POST['name'];
    $login = $_POST['login'];
    $password = $_POST['password'];
    $phone_number = $_POST['phone_number'];
    $maktab_id = $_POST['maktab_id'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    if (empty($login) and empty($password) and empty($name) and empty($phone_number) and empty($maktab_id) and empty($role) and empty($status)){

        header("HTTP/1.1 302 Found");
        echo json_encode(['error' => true, 'message' => '(302 Found) Ma\'lumotlar to\'liq shaklda to\'ldirilmagan']);

    }else{

        $mysql->where('password', $_POST['password']) ;
        $data = $mysql->get('users');

        if (empty($data)) {
            $mysql->insert("users", [
                'name' => $name,
                'login' => $login,
                'password' => $password,
                'phone_number' => $phone_number,
                'maktab_id' => $maktab_id,
                'role' => $role,
                'status' => $status
            ]);
            header("HTTP/1.1 201 Created");
            echo json_encode(['error' => false, 'message' => 'OK!']);
        } else {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(['error' => true,'message' => "Bunday foydalanuvchi mavjud",], JSON_UNESCAPED_UNICODE);
        }
    
    }
}


if ($function->Auth()){

    if($method == "viloyatlar" && $submethod == "add"){
        $data = Array (
            'name' => $_POST['name'],
            'status' => $_POST['status']
        );
        $mysql->insert("viloyatlar", $data);
        if($mysql)
            echo json_encode(['error' => false, 'message' => 'Bazaga malumot qoshildi. OK!']);
        else
            echo json_encode(['error' => true, 'message' => 'Malumotlar kelishda xato!']);
    }

    elseif($method == "viloyatlar" && $submethod == "update"){
        $data = Array (
            'name' => $_POST['name'],
            'status' => $_POST['status']
        );
        $mysql->where ('id', $_POST['id']);
        if ($mysql->update ('viloyatlar', $data))
            echo json_encode(['error' => false, 'message' => 'Bazadagi malumot ozgardi. OK!']);
        else
            echo json_encode(['error' => true, 'message' => 'Malumotlar kelishda xato!']);
    }

    elseif($method == "viloyatlar" && $submethod == "read"){
        $mysql->where ('id', $_POST['id']);
        $data = $mysql->get('viloyatlar');
        if ($data)
            echo json_encode(['error' => false, 'message' => 'OK!', 'data' => $data]);
        else
            echo json_encode(['error' => true, 'message' => 'Malumotlar kelishda xato!']);
    }

    elseif($method == "viloyatlar" && $submethod == "delete"){
        $mysql->where ('id', $_POST['id']);
        if ($mysql->delete('viloyatlar'))
            echo json_encode(['error' => false, 'message' => 'Malumot ochirildi. OK!']);
        else
            echo json_encode(['error' => true, 'message' => 'Malumotlar kelishda xato!']);
    }

    elseif($method == "viloyatlar"){


        $data = $mysql->get('viloyatlar');
        echo json_encode(['error' => false, 'message' => 'OK!', 'data' => $data]);


    }

    if($method == "shaharlar" && $submethod == "add"){
        $data = Array (
            'viloyat_id' => $_POST['viloyat_id'],
            'name' => $_POST['name'],
            'status' => $_POST['status']
        );
        $mysql->insert("shaharlar", $data);
        if($mysql)
            echo json_encode(['error' => false, 'message' => 'Bazaga malumot qoshildi. OK!']);
        else
            echo json_encode(['error' => true, 'message' => 'Malumotlar kelishda xato!']);
    }

    elseif($method == "shaharlar" && $submethod == "update"){
        $data = Array (
            'viloyat_id' => $_POST['viloyat_id'],
            'name' => $_POST['name'],
            'status' => $_POST['status']
        );
        $mysql->where ('id', $_POST['id']);
        if ($mysql->update ('shaharlar', $data))
            echo json_encode(['error' => false, 'message' => 'Bazadagi malumot ozgardi. OK!']);
        else
            echo json_encode(['error' => true, 'message' => 'Malumotlar kelishda xato!']);
    }

    elseif($method == "shaharlar" && $submethod == "read"){
        $mysql->where ('id', $_POST['id']);
        $data = $mysql->get('shaharlar');
        if ($data)
            echo json_encode(['error' => false, 'message' => 'OK!', 'data' => $data]);
        else
            echo json_encode(['error' => true, 'message' => 'Malumotlar kelishda xato!']);
    }

    elseif($method == "shaharlar" && $submethod == "delete"){
        $mysql->where ('id', $_POST['id']);
        if ($mysql->delete('shaharlar'))
            echo json_encode(['error' => false, 'message' => 'Malumot ochirildi. OK!']);
        else
            echo json_encode(['error' => true, 'message' => 'Malumotlar kelishda xato!']);
    }

    elseif($method == "shaharlar"){
        $data = $mysql->get('shaharlar');
        echo json_encode(['error' => false, 'message' => 'OK!', 'data' => $data]);
    }

    if($method == "maktablar" && $submethod == "add"){
        $data = Array (
            'shahar_id' => $_POST['shahar_id'],
            'name' => $_POST['name'],
            'status' => $_POST['status']
        );
        $mysql->insert("maktablar", $data);
        if($mysql)
            echo json_encode(['error' => false, 'message' => 'Bazaga malumot qoshildi. OK!']);
        else
            echo json_encode(['error' => true, 'message' => 'Malumotlar kelishda xato!']);
    }

    elseif($method == "maktablar" && $submethod == "update"){
        $data = Array (
            'shahar_id' => $_POST['shahar_id'],
            'name' => $_POST['name'],
            'status' => $_POST['status']
        );
        $mysql->where ('id', $_POST['id']);
        if ($mysql->update ('maktablar', $data))
            echo json_encode(['error' => false, 'message' => 'Bazadagi malumot ozgardi. OK!']);
        else
            echo json_encode(['error' => true, 'message' => 'Malumotlar kelishda xato!']);
    }

    elseif($method == "maktablar" && $submethod == "read"){
        $mysql->where ('id', $_POST['id']);
        $data = $mysql->get('maktablar');
        if ($data)
            echo json_encode(['error' => false, 'message' => 'OK!', 'data' => $data]);
        else
            echo json_encode(['error' => true, 'message' => 'Malumotlar kelishda xato!']);
    }

    elseif($method == "maktablar" && $submethod == "delete"){
        $mysql->where ('id', $_POST['id']);
        if ($mysql->delete('maktablar'))
            echo json_encode(['error' => false, 'message' => 'Malumot ochirildi. OK!']);
        else
            echo json_encode(['error' => true, 'message' => 'Malumotlar kelishda xato!']);
    }

    elseif($method == "maktablar"){
        $data = $mysql->get('maktablar');
        echo json_encode(['error' => false, 'message' => 'OK!', 'data' => $data]);
    }

    elseif($method == "users" && $submethod == "update"){
        $data = Array (
            'name' => $_POST['name'],
            'login' => $_POST['login'],
            'password' => $_POST['password'],
            'phone_number' => $_POST['phone_number'],
            'maktab_id' => $_POST['maktab_id'],
            'role' => $_POST['role'],
            'status' => $_POST['status']
        );
        $mysql->where ('id', $_POST['id']);
        if ($mysql->update ('users', $data))
            echo json_encode(['error' => false, 'message' => 'Bazadagi malumot ozgardi. OK!']);
        else
            echo json_encode(['error' => true, 'message' => 'Malumotlar kelishda xato!']);
    }

    elseif($method == "users" && $submethod == "read"){
        $mysql->where ('id', $_POST['id']);
        $data = $mysql->get('users');
        if ($data)
            echo json_encode(['error' => false, 'message' => 'OK!', 'data' => $data]);
        else
            echo json_encode(['error' => true, 'message' => 'Malumotlar kelishda xato!']);
    }

    elseif($method == "users" && $submethod == "delete"){
        $mysql->where ('id', $_POST['id']);
        if ($mysql->delete('users'))
            echo json_encode(['error' => false, 'message' => 'Malumot ochirildi. OK!']);
        else
            echo json_encode(['error' => true, 'message' => 'Malumotlar kelishda xato!']);
    }

    elseif($method == "users"){
        $data = $mysql->get('users');
        echo json_encode(['error' => false, 'message' => 'OK!', 'data' => $data]);
    }

    if($method == "questions" && $submethod == "add"){


        $masterplan = move_uploaded_file($_FILES['image1']['tmp_name'], "images/questions/".$_FILES["image1"]["name"]);
        $floorplan = move_uploaded_file($_FILES['image2']['tmp_name'], "images/questions/".$_FILES["image2"]["name"]);

        $data = Array (
            'catalog' => $_POST['catalog'],
            'text' => $_POST['text'],
            'image'=>$_FILES["image1"]["name"].",".$_FILES["image2"]["name"],
            'answer' => $_POST['answer'],
            'status' => $_POST['status']
        );
        $mysql->insert("questions", $data);
        if($mysql)
            echo json_encode(['error' => false, 'message' => 'Bazaga malumot qoshildi. OK!']);
        else
            echo json_encode(['error' => true, 'message' => 'Malumotlar kelishda xato!']);
    }

    elseif($method == "questions" && $submethod == "update"){
        $data = Array (
            'catalog' => $_POST['catalog'],
            'text' => $_POST['text'],
            'answer' => $_POST['answer'],
            'status' => $_POST['status']
        );
        $mysql->where ('id', $_POST['id']);
        if ($mysql->update ('questions', $data))
            echo json_encode(['error' => false, 'message' => 'Bazadagi malumot ozgardi. OK!']);
        else
            echo json_encode(['error' => true, 'message' => 'Malumotlar kelishda xato!']);
    }

    elseif($method == "questions" && $submethod == "read"){
        $mysql->where ('id', $_POST['id']);
        $data = $mysql->get('questions');
        if ($data)
            echo json_encode(['error' => false, 'message' => 'OK!', 'data' => $data]);
        else
            echo json_encode(['error' => true, 'message' => 'Malumotlar kelishda xato!']);
    }

    elseif($method == "questions" && $submethod == "delete"){
        $mysql->where ('id', $_POST['id']);
        if ($mysql->delete('questions'))
            echo json_encode(['error' => false, 'message' => 'Malumot ochirildi. OK!']);
        else
            echo json_encode(['error' => true, 'message' => 'Malumotlar kelishda xato!']);
    }

    elseif($method == "questions"){
        $data = $mysql->get('questions');
        echo json_encode(['error' => false, 'message' => 'OK!', 'data' => $data]);
    }

    if($method == "answers" && $submethod == "add"){
        $data = Array (
            'user_id' => $_POST['user_id'],
            'togri' => $_POST['togri'],
            'notogri' => $_POST['notogri'],
            'ball' => $_POST['ball'],
            'status' => $_POST['status']
        );
        $mysql->insert("answers", $data);
        if($mysql)
            echo json_encode(['error' => false, 'message' => 'Bazaga malumot qoshildi. OK!']);
        else
            echo json_encode(['error' => true, 'message' => 'Malumotlar kelishda xato!']);
    }

    elseif($method == "answers" && $submethod == "update"){
        $data = Array (
            'user_id' => $_POST['user_id'],
            'togri' => $_POST['togri'],
            'notogri' => $_POST['notogri'],
            'ball' => $_POST['ball'],
            'status' => $_POST['status']
        );
        $mysql->where ('id', $_POST['id']);
        if ($mysql->update ('answers', $data))
            echo json_encode(['error' => false, 'message' => 'Bazadagi malumot ozgardi. OK!']);
        else
            echo json_encode(['error' => true, 'message' => 'Malumotlar kelishda xato!']);
    }

    elseif($method == "answers" && $submethod == "read"){
        $mysql->where ('id', $_POST['id']);
        $data = $mysql->get('answers');
        if ($data)
            echo json_encode(['error' => false, 'message' => 'OK!', 'data' => $data]);
        else
            echo json_encode(['error' => true, 'message' => 'Malumotlar kelishda xato!']);
    }

    elseif($method == "answers" && $submethod == "delete"){
        $mysql->where ('id', $_POST['id']);
        if ($mysql->delete('answers'))
            echo json_encode(['error' => false, 'message' => 'Malumot ochirildi. OK!']);
        else
            echo json_encode(['error' => true, 'message' => 'Malumotlar kelishda xato!']);
    }

    elseif($method == "answers"){
        $data = $mysql->get('answers');
        echo json_encode(['error' => false, 'message' => 'OK!', 'data' => $data]);
    }

    if ($method == "profile") {
        echo json_encode(['error' => false, 'message' => 'OK!', 'data' => $function->Auth()]);
    }

    if ($method == "answers") {
        $user_id = $function->Auth()[0]['id'];
        $mysql->where('user_id', $user_id);
        $data = $mysql->get('answers');

        echo json_encode(['error' => false, 'message' => 'OK!', 'data' =>$data]);

    }
}

if ($method == "test") {
//        $user_id = $function->Auth()[0]['id'];
    $mysql->where('id', 6);
    $data = $mysql->get('questions');
    $image = $data[0]['image'];
    $ex = explode(",", $image);
    $image1 = $ex[0];
    echo "<img src=\"http://schooltesting.uz/images/questions/$image1\"> ";

//        print_r($data);
//        echo json_encode(['error' => false, 'message' => 'OK!', 'data' =>$data]);

}