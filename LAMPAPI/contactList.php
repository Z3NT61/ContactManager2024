<?php
session_start(); 
if (!isset($_SESSION['UserID'])) {
    returnWithError("No UserID Found");
    exit();
}

$inData = getRequestInfo();
$userId = $_SESSION['UserID'];
$searchItem = $inData["searchContactItem"];

// It's better to keep credentials out of the code
require 'config.php'; // Assumes your DB credentials are stored in config.php


// Database connection
$conn = new mysqli("localhost", "root", "b+YXZI98+xeB", "SPROJECTDB");
if ($conn->connect_error) 
{
    returnWithError("Database connection failed: " . $conn->connect_error);
    exit();
} else {
    $stmt = $db->prepare("SELECT FirstName, LastName, Email, ID FROM CONTACTS WHERE UserID=? AND (FirstName LIKE ? OR LastName LIKE ?)");
    if (!$stmt) {
        returnWithError("Failed to prepare the statement");
        exit();
    }

    $likeSearch = "%{$searchItem}%";
    $stmt->bind_param("sss", $userId, $likeSearch, $likeSearch);
    if (!$stmt->execute()) {
        returnWithError($stmt->error);
        exit();
    }
    $result = $stmt->get_result();
    $contacts = [];
    while ($row = $result->fetch_assoc()) {
        $contacts[] = $row;
    }
    if (count($contacts) > 0) {
        sendInfoAsJson($contacts);
    } else {
        returnWithError("No Contacts Found");
    }
}

function getRequestInfo(){
    return json_decode(file_get_contents("php://input"), true);
}

function sendInfoAsJson($obj){
    header('Content-type: application/json');
    echo json_encode($obj);
}

function returnWithError($err){
    sendInfoAsJson(["id" => 0, "FirstName" => "", "LastName" => "", "Email" => "", "error" => $err]);
}
?>
