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

    $con->query("CREATE TABLE IF NOT EXISTS virtualReality (
        virtualReality_id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
        virtualReality_date DATE NOT NULL,
        virtualReality_time TIME NOT NULL,
        virtualReality_person INTEGER NOT NULL,
        virtualReality_telephone VARCHAR(255) NOT NULL,
        virtualReality_name VARCHAR(255) NOT NULL,
        virtualReality_company VARCHAR(255)
    )");

    $con->query('INSERT INTO `virtualReality` (`virtualReality_id`, `virtualReality_date` `virtualReality_time`, `virtualReality_person`, `virtualReality_telephone`, `virtualReality_name`, `virtualReality_company`) 
    VALUES (NULL, "09.03.2019", "11:40", 8, "076 466 3939", "Beispieldaten")');

    $con->query('INSERT INTO `virtualReality` (`virtualReality_id`, `virtualReality_date` `virtualReality_time`, `virtualReality_person`, `virtualReality_telephone`, `virtualReality_name`, `virtualReality_company`) 
    VALUES (NULL, "09.03.2019", "11:40", 8, "076 466 3939", "Beispieldaten")');

    $con->query('INSERT INTO `virtualReality` (`virtualReality_id`, `virtualReality_date` `virtualReality_time`, `virtualReality_person`, `virtualReality_telephone`, `virtualReality_name`, `virtualReality_company`) 
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

        $virtualReality_id = validateNumber($_REQUEST['virtualReality_id']);

        if(!$virtualReality_id){
            $errors[] = 'virtualReality_id';
        }

        if(count($errors) == 0){
            echo deleteEntry($virtualReality_id);
            http_response_code(200);
        }else{
            echo json_encode($errors);
            http_response_code(500);
        }
        break;
    case 'postdata':
        $errors = array();

        $virtualReality_date = validateDate($_REQUEST['virtualReality_date']);
        $virtualReality_time = validateString($_REQUEST['virtualReality_time']);
        $virtualReality_person = validateNumber($_REQUEST['virtualReality_person']);
        $virtualReality_telephone = validateString($_REQUEST['virtualReality_telephone']);
        $virtualReality_name = validateString($_REQUEST['virtualReality_name']);
        $virtualReality_company = validateString($_REQUEST['virtualReality_company']);

        if(!$virtualReality_date){
            $errors[] = 'virtualReality_date';
        }
        if(!$virtualReality_time){
            $errors[] = 'virtualReality_time';
        }
        if(!$virtualReality_person){
            $errors[] = 'virtualReality_person';
        }
        if(!$virtualReality_telephone){
            $errors[] = 'virtualReality_telephone';
        }
        if(!$virtualReality_name){
            $errors[] = 'virtualReality_name';
        }
        if(!$virtualReality_company){
            $virtualReality_company = null;
        }

        if(count($errors) == 0){
            echo newEntry($virtualReality_date, $virtualReality_time, $virtualReality_person, $virtualReality_telephone, $virtualReality_name, $virtualReality_company);
            http_response_code(200);
        }
        else{
            echo json_encode($errors);
            http_response_code(500);
        }
        break;
    case 'putdata':
        $errors = array();

        $virtualReality_date = validateDate($_REQUEST['virtualReality_date']);
        $virtualReality_time = validateString($_REQUEST['virtualReality_time']);
        $virtualReality_person = validateNumber($_REQUEST['virtualReality_person']);
        $virtualReality_telephone = validateString($_REQUEST['virtualReality_telephone']);
        $virtualReality_name = validateString($_REQUEST['virtualReality_name']);
        $virtualReality_company = validateString($_REQUEST['virtualReality_company']);
        $virtualReality_id = validateNumber($_REQUEST['virtualReality_id']);

        if(!$virtualReality_date){
            $errors[] = 'virtualReality_date';
        }
        if(!$virtualReality_time){
            $errors[] = 'virtualReality_time';
        }
        if(!$virtualReality_person){
            $errors[] = 'virtualReality_person';
        }
        if(!$virtualReality_telephone){
            $errors[] = 'virtualReality_telephone';
        }
        if(!$virtualReality_name){
            $errors[] = 'virtualReality_name';
        }
        if(!$virtualReality_company){
            $virtualReality_company = null;
        }
        if(!$virtualReality_id){
            $errors[] = 'virtualReality_id';
        }

        if(count($errors) == 0){
            echo editEntry($virtualReality_date, $virtualReality_id, $virtualReality_time, $virtualReality_person, $virtualReality_telephone, $virtualReality_name, $virtualReality_company);
            http_response_code(200);
        }
        else{
            echo json_encode($errors);
            http_response_code(500);
        }
        break;
    case 'getdatabyid':
        $errors = array();

        $virtualReality_id = validateNumber($_REQUEST['virtualReality_id']);

        if(!$virtualReality_id){
            $errors[] = 'virtualReality_id';
        }

        if(count($errors) == 0){
            echo getEntryById($virtualReality_id);
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
    $result = $con->query( 'SELECT * FROM virtualReality ORDER BY virtualReality_date, virtualReality_time');
    $dataarray = array();
    $dataarray = $result->fetch_all(MYSQLI_ASSOC);

    return json_encode($dataarray);
}

function getEntryById($virtualReality_id){
    global $con;
    $result = $con->query( 'SELECT * FROM virtualReality WHERE `virtualReality_id` = "'.$virtualReality_id .'"');
    $dataarray = array();
    $dataarray = $result->fetch_all(MYSQLI_ASSOC);

    return json_encode($dataarray);
}


function newEntry($virtualReality_date, $virtualReality_time, $virtualReality_person, $virtualReality_telephone, $virtualReality_name, $virtualReality_company){
    global $con;

    $query = 'INSERT INTO `virtualReality` (`virtualReality_date`, `virtualReality_time`, `virtualReality_person`, `virtualReality_telephone`, `virtualReality_name`, `virtualReality_company`) 
    VALUES ("'.$virtualReality_date.'", "'.$virtualReality_time.'", "'.$virtualReality_person.'", "'.$virtualReality_telephone.'", "'.$virtualReality_name.'", "'.$virtualReality_company.'")';
    
    $con->query($query);

    return alledaten();
}

function editEntry($virtualReality_date, $virtualReality_id, $virtualReality_time, $virtualReality_person, $virtualReality_telephone, $virtualReality_name, $virtualReality_company){
    global $con;

    $query = 'UPDATE `virtualReality` SET `virtualReality_date` = "'.$virtualReality_date.'", `virtualReality_time` = "'.$virtualReality_time.'", `virtualReality_person` = "' . $virtualReality_person . '", `virtualReality_telephone` = "' . $virtualReality_telephone. '", `virtualReality_name` = "'.$virtualReality_name.'", `virtualReality_company` = "'.$virtualReality_company.'" WHERE `virtualReality`.`virtualReality_id` = "'.$virtualReality_id.'"';

    $con->query($query);

    return alledaten();
}

function deleteEntry($id){
    global $con;
    $con->query('DELETE FROM `virtualReality` WHERE `virtualReality`.`virtualReality_id` = ' . $id);

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
