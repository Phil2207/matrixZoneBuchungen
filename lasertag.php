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

    $con->query("CREATE TABLE IF NOT EXISTS lasertag (
        lasertag_id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
        lasertag_date DATE NOT NULL,
        lasertag_time TIME NOT NULL,
        lasertag_person INTEGER NOT NULL,
        lasertag_telephone VARCHAR(255) NOT NULL,
        lasertag_name VARCHAR(255) NOT NULL,
        lasertag_company VARCHAR(255)
    )");

    $con->query('INSERT INTO `lasertag` (`lasertag_id`, `lasertag_date` `lasertag_time`, `lasertag_person`, `lasertag_telephone`, `lasertag_name`, `lasertag_company`) 
    VALUES (NULL, "09.03.2019", "11:40", 8, "076 466 3939", "Beispieldaten")');

    $con->query('INSERT INTO `lasertag` (`lasertag_id`, `lasertag_date` `lasertag_time`, `lasertag_person`, `lasertag_telephone`, `lasertag_name`, `lasertag_company`) 
    VALUES (NULL, "09.03.2019", "11:40", 8, "076 466 3939", "Beispieldaten")');

    $con->query('INSERT INTO `lasertag` (`lasertag_id`, `lasertag_date` `lasertag_time`, `lasertag_person`, `lasertag_telephone`, `lasertag_name`, `lasertag_company`) 
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

        $lasertag_id = validateNumber($_REQUEST['lasertag_id']);

        if(!$lasertag_id){
            $errors[] = 'lasertag_id';
        }

        if(count($errors) == 0){
            echo deleteEntry($lasertag_id);
            http_response_code(200);
        }else{
            echo json_encode($errors);
            http_response_code(500);
        }
        break;
    case 'postdata':
        $errors = array();

        $lasertag_date = validateDate($_REQUEST['lasertag_date']);
        $lasertag_time = validateString($_REQUEST['lasertag_time']);
        $lasertag_person = validateNumber($_REQUEST['lasertag_person']);
        $lasertag_telephone = validateString($_REQUEST['lasertag_telephone']);
        $lasertag_name = validateString($_REQUEST['lasertag_name']);
        $lasertag_company = validateString($_REQUEST['lasertag_company']);

        if(!$lasertag_date){
            $errors[] = 'lasertag_date';
        }
        if(!$lasertag_time){
            $errors[] = 'lasertag_time';
        }
        if(!$lasertag_person){
            $errors[] = 'lasertag_person';
        }
        if(!$lasertag_telephone){
            $errors[] = 'lasertag_telephone';
        }
        if(!$lasertag_name){
            $errors[] = 'lasertag_name';
        }
        if(!$lasertag_company){
            $lasertag_company = null;
        }

        if(count($errors) == 0){
            echo newEntry($lasertag_date, $lasertag_time, $lasertag_person, $lasertag_telephone, $lasertag_name, $lasertag_company);
            http_response_code(200);
        }
        else{
            echo json_encode($errors);
            http_response_code(500);
        }
        break;
    case 'putdata':
        $errors = array();

        $lasertag_date = validateDate($_REQUEST['lasertag_date']);
        $lasertag_time = validateString($_REQUEST['lasertag_time']);
        $lasertag_person = validateNumber($_REQUEST['lasertag_person']);
        $lasertag_telephone = validateString($_REQUEST['lasertag_telephone']);
        $lasertag_name = validateString($_REQUEST['lasertag_name']);
        $lasertag_company = validateString($_REQUEST['lasertag_company']);
        $lasertag_id = validateNumber($_REQUEST['lasertag_id']);

        if(!$lasertag_date){
            $errors[] = 'lasertag_date';
        }
        if(!$lasertag_time){
            $errors[] = 'lasertag_time';
        }
        if(!$lasertag_person){
            $errors[] = 'lasertag_person';
        }
        if(!$lasertag_telephone){
            $errors[] = 'lasertag_telephone';
        }
        if(!$lasertag_name){
            $errors[] = 'lasertag_name';
        }
        if(!$lasertag_company){
            $lasertag_company = null;
        }
        if(!$lasertag_id){
            $errors[] = 'lasertag_id';
        }

        if(count($errors) == 0){
            echo editEntry($lasertag_date, $lasertag_id, $lasertag_time, $lasertag_person, $lasertag_telephone, $lasertag_name, $lasertag_company);
            http_response_code(200);
        }
        else{
            echo json_encode($errors);
            http_response_code(500);
        }
        break;
    case 'getdatabyid':
        $errors = array();

        $lasertag_id = validateNumber($_REQUEST['lasertag_id']);

        if(!$lasertag_id){
            $errors[] = 'lasertag_id';
        }

        if(count($errors) == 0){
            echo getEntryById($lasertag_id);
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
    $result = $con->query( 'SELECT * FROM lasertag ORDER BY lasertag_date, lasertag_time');
    $dataarray = array();
    $dataarray = $result->fetch_all(MYSQLI_ASSOC);

    return json_encode($dataarray);
}

function getEntryById($lasertag_id){
    global $con;
    $result = $con->query( 'SELECT * FROM lasertag WHERE `lasertag_id` = "'.$lasertag_id .'"');
    $dataarray = array();
    $dataarray = $result->fetch_all(MYSQLI_ASSOC);

    return json_encode($dataarray);
}


function newEntry($lasertag_date, $lasertag_time, $lasertag_person, $lasertag_telephone, $lasertag_name, $lasertag_company){
    global $con;

    $query = 'INSERT INTO `lasertag` (`lasertag_date`, `lasertag_time`, `lasertag_person`, `lasertag_telephone`, `lasertag_name`, `lasertag_company`) 
    VALUES ("'.$lasertag_date.'", "'.$lasertag_time.'", "'.$lasertag_person.'", "'.$lasertag_telephone.'", "'.$lasertag_name.'", "'.$lasertag_company.'")';
    
    $con->query($query);

    return alledaten();
}

function editEntry($lasertag_date, $lasertag_id, $lasertag_time, $lasertag_person, $lasertag_telephone, $lasertag_name, $lasertag_company){
    global $con;

    $query = 'UPDATE `lasertag` SET `lasertag_date` = "'.$lasertag_date.'", `lasertag_time` = "'.$lasertag_time.'", `lasertag_person` = "' . $lasertag_person . '", `lasertag_telephone` = "' . $lasertag_telephone. '", `lasertag_name` = "'.$lasertag_name.'", `lasertag_company` = "'.$lasertag_company.'" WHERE `lasertag`.`lasertag_id` = "'.$lasertag_id.'"';

    $con->query($query);

    return alledaten();
}

function deleteEntry($id){
    global $con;
    $con->query('DELETE FROM `lasertag` WHERE `lasertag`.`lasertag_id` = ' . $id);

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
