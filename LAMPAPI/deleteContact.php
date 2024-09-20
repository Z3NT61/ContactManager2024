<?php
$inData = getRequestInfo();

$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331"); #make sure to change this or cache the user variables somewhere else, maybe so no one can reach them.

#select from list? maybe store in JS so that we don't have to access the db multiple times.

if($conn->connect_error){
    returnWithError($conn->connect_error);
}else{
    $email = $inData["email"] . "%";
    $stmt = $conn->prepare("delete from CONTACTS where Email like ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
}

function getRequestInfo(){
    return json_decode(file_get_contents('php://input'), true);
}

function sendResultInfoAsJson($obj){
    header('Content-type: application/json');
	echo $obj;
}

function returnWithError($err){
    $ret = '{"error":"' . $err . '"}';
    sendresultInfoAsJson($ret);
}

#check to make SURE this works
function returnWithInfo(){
    sendresultInfoAsJson("success");
};
