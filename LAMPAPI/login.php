<?php

#this is funcionality assuming you have already logged in, therefore, this is
#is just a request get function, returning
#first name, last name, contacts, and more
#caching may be a good idea when recieving a users' contact information

$inData = getRequestInfo();

$id = 0;
$FirstName = "";
$LastName = "";
#$Email = "";

$db = new mysqli("localhost", "root", "b+YXZI98+xeB", "SPROJECTDB"); #connects with the DB using the users login n password
if($db->connect_error){
    returnWithError($db->connect_error);
}
else{
    $start = $db->prepare("select ID, FirstName, LastName from MAINUSERS where Login=? and Password=?");
    $start->bind_param("ss",$inData["login"], $inData["password"]);
    $start->execute();
    $result = $start->get_result();

    if($row = $result->fetch_assoc()){
        returnWithInfo($row["FirstName"], $row["LastName"], $row["ID"]);
    }
    else{
        returnWithError("No Records Found");
    }
    $start->close();
    $db->close();
}
    function getRequestInfo(){
        return json_decode(file_get_contents("php://input"), true);
    }

    function sendInfoAsJson($obj){
        header('Content-type: application/json');
        echo $obj;
    }

    function returnWithError($err){
    $retValue = '{"id":0, "FirstName":"", "LastName":"error", "error": "'. $err . '"}';
        sendInfoAsJson($retValue);
    }

function returnWithInfo($FirstName, $LastName, $id){
    #to do
    $retValue = '{"id":' . $id . ',"FirstName":"'. $FirstName . '","LastName":"'. $LastName . '"}';
    sendInfoAsJson($retValue);
}





