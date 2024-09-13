<?php

#creating a user, as titled

$inData = getRequestInfo();

$FirstName = $inData["firstname"];
$LastName = $inData["lastname"];
$Login = $inData["user"];
$Password = $inData["password"];

$db = new mysqli("localhost", "DBManager", "DesertDesserts45", "SPROJECTDB"); #connects with the DB using the users login n password
if($db->connect_error){
    returnWithError($db->connect_error);
}
else{

    $stmt = $db->prepare("select * from  MAINUSERS (Login) VALUES(?)");
    $stmt->bind_param("s", $Login);
    $stmt->execute();
    if($stmt->num_rows() > 0){
        returnWithError("User already exists");
    }
    else{
        $stmt = $db->prepare("insert into MAINUSERS (FirstName, LastName, Login, Password) VALUES(?,?,?,?)");
        $stmt->bind_param("ssss",$FirstName, $LastName, $Login, $Password);
        $stmt->execute();
        $stmt->close();
        $db->close();
        sendresultInfoAsJson("User created successfully");
    }
}


function getRequestInfo(){
    return json_decode(file_get_contents("php://input"), true);
}

function sendresultInfoAsJson($obj){
    header('Content-type: application/json');
    echo $obj;
}

function returnWithError($err){
    $retValue = '{"error": "' . $err .'"}';
    sendresultInfoAsJson($retValue);
}
