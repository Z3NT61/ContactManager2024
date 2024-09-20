<?php

#trying to update the fields we need
#   firstname
#   lastnmae
#   email
#   phonenumber
#see if we can update the fields separately rather than just redoing the whole row
#we only need the relationship so, ??
#because we need only have 1 database we if we update in one user, the other users will experience the same thing
#not intended?
#create new db? table in a userdb?

$inData = getRequestInfo();

$id = 0;
$FirstName = "";
$LastName = "";
$Email = "";
#phone number?

$db = new mysqli("localhost", "root", "b+YXZI98+xeB", "SPROJECTDB"); #connects with the DB using the users login n password
if($db->connect_error){
    returnWithError($db->connect_error);
}
else{
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

