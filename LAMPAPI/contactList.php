<?php

#contact list, doesn't want a the full list displayed, should still return the
#full table and cache server side until user logouts most likely. sigh.
#check if logged in. could either be in js or here, learn.
#select statement that takes in the whole table for a UID,
#
#table is build like
# ID | ID connected to
#therefore only need to check one column, most likely the first column,
#scaling wise, need to think about how this will be in future, could take a long
#time search thru unneccessary table entries. Maybe a separaete database per user?
#but have to think about the space complexity of this system.
#a list for each user and worse case, every user is connected to each other
#O(n * n) space.

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
    $stmt = $db->prepare("select id from USERCONTACTS where id=?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result= $stmt->get_result();
    sendInfoAsJson($result->fetch_all());
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
