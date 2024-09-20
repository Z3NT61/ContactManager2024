<?php

#assuming that dp.php is true and doesn't stop the script,
#also look into return error info for require, maybe include?
#see if require can also return data, it can return status.


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
    $stmt = $db->prepare("select FirstName, LastName, Email,  count(*) c from CONTACTS group by name having c > 1"); #how do we want to filter contacts when added?? Check all fields or just check relationship ie. usercontacts?
    $stmt->execute();
    $result = $stmt->get_result();
    $row = result->fetch_assoc();
    $stmt->close();
    if(!$row["c"] > 1){
        $start = $db->prepare("insert ID, FirstName, LastName, Email in CONTACTS where FirstName=? and LastName=? and Email=?");
        $start->bind_param("sss",$inData["login"], $inData["password"], $inData["email"]);
        $start->execute();
        $start->close();
        $start->prepare("insert UserID, ContactID, DataRecordCreated in USERCONTACTS where UserID=? and ContactID=? and DataRecordCreated=?");
        $start->bind_param("sss", ?, ?); #make sure to get the return ID from $stmt
        $db->close();
        #also need to add the stmt that adds the uid relationship in USERCONTACTS
    }
    else{
        returnWithError("This user already exists");
    }
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
