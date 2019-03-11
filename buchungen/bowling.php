<?php

parse_str(file_get_contents("php://input"),$put_vars);
$_REQUEST = array_merge($_REQUEST, $put_vars);

define('MYSQL_HOST',"localhost"); 
define('MYSQL_USER',"root"); 
define('MYSQL_PW',""); 
define('MYSQL_DB',"buchungen");
// Verbindung zur DB herstellen
$con = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PW);
if(!$con->select_db(MYSQL_DB)){
    // DB existiert nicht, also neu erstellen
    $createdb = "CREATE DATABASE IF NOT EXISTS " . MYSQL_DB . " DEFAULT CHARACTER SET utf8";
    $con->query($createdb);

    $con->query("USE " . MYSQL_DB);

    $con->query("CREATE TABLE IF NOT EXISTS bowling (
        bowling_id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
        bowling_date DATE NOT NULL,
        bowling_time TIME NOT NULL,
        bowling_person INTEGER NOT NULL,
        bowling_telephone VARCHAR(255) NOT NULL,
        bowling_name VARCHAR(255) NOT NULL,
        bowling_company VARCHAR(255)
    )");

    $con->query('INSERT INTO `bowling` (`bowling_id`, `bowling_date` `bowling_time`, `bowling_person`, `bowling_telephone`, `bowling_name`, `bowling_company`) 
    VALUES (NULL, "09.03.2019", "11:40", 8, "076 466 3939", "Beispieldaten")');

    $con->query('INSERT INTO `bowling` (`bowling_id`, `bowling_date` `bowling_time`, `bowling_person`, `bowling_telephone`, `bowling_name`, `bowling_company`) 
    VALUES (NULL, "09.03.2019", "11:40", 8, "076 466 3939", "Beispieldaten")');

    $con->query('INSERT INTO `bowling` (`bowling_id`, `bowling_date` `bowling_time`, `bowling_person`, `bowling_telephone`, `bowling_name`, `bowling_company`) 
    VALUES (NULL, "09.03.2019", "11:40", 8, "076 466 3939", "Beispieldaten")');
}

$con->select_db(MYSQL_DB) or die('Datenbankverbindung nicht möglich');

$action = $_REQUEST['action'];

switch ($action) {
    case 'getdata':
        echo alledaten();
        break;
    case 'deletedata':
        $errors = array();

        $bowling_id = validateNumber($_REQUEST['bowling_id']);

        if(!$bowling_id){
            $errors[] = 'bowling_id';
        }

        if(count($errors) == 0){
            echo deleteEntry($bowling_id);
            http_response_code(200);
        }else{
            echo json_encode($errors);
            http_response_code(500);
        }
        break;
    case 'postdata':
        $errors = array();

        $bowling_date = validateDate($_REQUEST['bowling_date']);
        $bowling_time = validateString($_REQUEST['bowling_time']);
        $bowling_person = validateNumber($_REQUEST['bowling_person']);
        $bowling_telephone = validateString($_REQUEST['bowling_telephone']);
        $bowling_name = validateString($_REQUEST['bowling_name']);
        $bowling_company = validateString($_REQUEST['bowling_company']);

        if(!$bowling_date){
            $errors[] = 'bowling_date';
        }
        if(!$bowling_time){
            $errors[] = 'bowling_time';
        }
        if(!$bowling_person){
            $errors[] = 'bowling_person';
        }
        if(!$bowling_telephone){
            $errors[] = 'bowling_telephone';
        }
        if(!$bowling_name){
            $errors[] = 'bowling_name';
        }
        if(!$bowling_company){
            $bowling_company = null;
        }

        if(count($errors) == 0){
            echo newEntry($bowling_date, $bowling_time, $bowling_person, $bowling_telephone, $bowling_name, $bowling_company);
            http_response_code(200);
        }
        else{
            echo json_encode($errors);
            http_response_code(500);
        }
        break;
    case 'putdata':
        $errors = array();

        $bowling_date = validateDate($_REQUEST['bowling_date']);
        $bowling_time = validateString($_REQUEST['bowling_time']);
        $bowling_person = validateNumber($_REQUEST['bowling_person']);
        $bowling_telephone = validateString($_REQUEST['bowling_telephone']);
        $bowling_name = validateString($_REQUEST['bowling_name']);
        $bowling_company = validateString($_REQUEST['bowling_company']);
        $bowling_id = validateNumber($_REQUEST['bowling_id']);

        if(!$bowling_date){
            $errors[] = 'bowling_date';
        }
        if(!$bowling_time){
            $errors[] = 'bowling_time';
        }
        if(!$bowling_person){
            $errors[] = 'bowling_person';
        }
        if(!$bowling_telephone){
            $errors[] = 'bowling_telephone';
        }
        if(!$bowling_name){
            $errors[] = 'bowling_name';
        }
        if(!$bowling_company){
            $bowling_company = null;
        }
        if(!$bowling_id){
            $errors[] = 'bowling_id';
        }

        if(count($errors) == 0){
            echo editEntry($bowling_date, $bowling_id, $bowling_time, $bowling_person, $bowling_telephone, $bowling_name, $bowling_company);
            http_response_code(200);
        }
        else{
            echo json_encode($errors);
            http_response_code(500);
        }
        break;
    case 'getdatabyid':
        $errors = array();

        $bowling_id = validateNumber($_REQUEST['bowling_id']);

        if(!$bowling_id){
            $errors[] = 'bowling_id';
        }

        if(count($errors) == 0){
            echo getEntryById($bowling_id);
            http_response_code(200);
        }else{
            echo json_encode($errors);
            http_response_code(500);
        }
        break;
    default: 
        break;
}

$con->close();


function alledaten(){
    global $con;
    $result = $con->query( 'SELECT * FROM bowling ORDER BY bowling_date, bowling_time');
    $dataarray = array();
    $dataarray = $result->fetch_all(MYSQLI_ASSOC);

    return json_encode($dataarray);
}

function getEntryById($bowling_id){
    global $con;
    $result = $con->query( 'SELECT * FROM bowling WHERE `bowling_id` = "'.$bowling_id .'"');
    $dataarray = array();
    $dataarray = $result->fetch_all(MYSQLI_ASSOC);

    return json_encode($dataarray);
}


function newEntry($bowling_date, $bowling_time, $bowling_person, $bowling_telephone, $bowling_name, $bowling_company){
    global $con;

    $query = 'INSERT INTO `bowling` (`bowling_date`, `bowling_time`, `bowling_person`, `bowling_telephone`, `bowling_name`, `bowling_company`) 
    VALUES ("'.$bowling_date.'", "'.$bowling_time.'", "'.$bowling_person.'", "'.$bowling_telephone.'", "'.$bowling_name.'", "'.$bowling_company.'")';
    
    $con->query($query);

    return alledaten();
}

function editEntry($bowling_date, $bowling_id, $bowling_time, $bowling_person, $bowling_telephone, $bowling_name, $bowling_company){
    global $con;

    $query = 'UPDATE `bowling` SET `bowling_date` = "'.$bowling_date.'", `bowling_time` = "'.$bowling_time.'", `bowling_person` = "' . $bowling_person . '", `bowling_telephone` = "' . $bowling_telephone. '", `bowling_name` = "'.$bowling_name.'", `bowling_company` = "'.$bowling_company.'" WHERE `bowling`.`bowling_id` = "'.$bowling_id.'"';

    $con->query($query);

    return alledaten();
}

function deleteEntry($id){
    global $con;
    $con->query('DELETE FROM `bowling` WHERE `bowling`.`bowling_id` = ' . $id);

    return alledaten();
}

function validateMail($mail){
    $check = true;
    if(isset($mail)){
        $mail = htmlspecialchars($mail);

        if(!filter_var($mail, FILTER_VALIDATE_EMAIL)){
            $check = false;
        }

        if(!filter_var($mail, '/[,]/')){
            $check = false;
        }

        if(strlen($mail)>255){
            $check = false;
        }
    }
    else{
        $check = false;
    }

    if($check === true){
        return $mail;
    }
    else{
        return $check;
    }
}

function validateString($string){
    $check = true;
    if(isset($string)){
        $string = htmlspecialchars($string);

        if(strlen($string)>255){
            $check = false;
        }
    }
    else{
        $check = false;
    }

    if($check == true){
        return $string;
    }
    else{
        return $check;
    }
}

function validateNumber($number){
    $check = true;
    if(isset($number)){
        $number = htmlspecialchars($number);

        if(!is_numeric($number)){
            $check = false;
        }

        if(strlen($number)>255){
            $check = false;
        }
    }
    else{
        $check = false;
    }

    if($check == true){
        return $number;
    }
    else{
        return $check;
    }
}

function validateDate($date){
    $check = true;
    if(isset($date)){
        $date = htmlspecialchars($date);

        $date_var = DateTime::createFromFormat('Y-m-d', $date);
        $date_errors = DateTime::getLastErrors();
        if ($date_errors['warning_count'] + $date_errors['error_count'] > 0) {
            $check = false;
        }
    }
    else{
        $check = false;
    }

    if($check == true){
        return $date;
    }
    else{
        return $check;
    }
}

function validateBoolean($boolean){
    $check = true;
    if(isset($boolean)){
        $boolean = htmlspecialchars($mail);

        if(!filter_var($boolean, FILTER_VALIDATE_BOOLEAN)){
            $check = false;
        }
    }
    else{
        $check = false;
    }

    if($check == true){
        return $boolean;
    }
    else{
        return $check;
    }
}
