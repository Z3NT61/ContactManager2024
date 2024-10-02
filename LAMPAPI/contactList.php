<?php
session_start(); 
if (!isset($_SESSION['UserID'])) {
    returnWithError("No UserID Found");
    exit();
}

$inData = getRequestInfo();
$userId = $_SESSION['UserID'];
$searchItem = $inData["searchContactItem"];  // Adjust this based on actual input name

$db = new mysqli("localhost", "root", "b+YXZI98+xeB", "SPROJECTDB");
if($db->connect_error) {
    returnWithError($db->connect_error);
} else {
    $stmt = $db->prepare("SELECT FirstName, LastName, Email, ID FROM CONTACTS WHERE UserID=? AND (FirstName LIKE ? OR LastName LIKE ?)");
    $likeSearch = "%{$searchItem}%";
    $stmt->bind_param("sss", $userId, $likeSearch, $likeSearch);
    $stmt->execute();
    $result = $stmt->get_result();
    $contacts = [];
    while($row = $result->fetch_assoc()) {
        $contacts[] = $row;
    }
    if(count($contacts) > 0){
        sendInfoAsJson(json_encode($contacts));
    } else {
        returnWithError("No Contacts Found");
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
    $retValue = '{"id":0, "FirstName":"", "LastName":"", "Email":"", "error": "'. $err . '"}';
    sendInfoAsJson($retValue);
}
?>
