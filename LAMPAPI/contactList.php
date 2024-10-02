<?php
session_start();
require_once 'db.php'; // Include the database configuration

if (!isset($_SESSION['UserID']) || empty($_SESSION['UserID'])) {
    returnWithError("No UserID Found or Session is Invalid");
    exit();
}

$inData = getRequestInfo();
$userId = $_SESSION['UserID'];
$searchItem = $inData["searchContactItem"];

// Use the function from db.php if it exists
$db = getDatabaseConnection(); // Assuming you have this function in db.php

$stmt = $db->prepare("SELECT FirstName, LastName, Email, ID FROM CONTACTS WHERE UserID=? AND (FirstName LIKE ? OR LastName LIKE ?)");
if (!$stmt) {
    returnWithError("Statement preparation failed: " . $db->error);
    $db->close();
    exit();
}

$likeSearch = "%{$searchItem}%";
$stmt->bind_param("sss", $userId, $likeSearch, $likeSearch);
if (!$stmt->execute()) {
    returnWithError("Execute failed: " . $stmt->error);
    $stmt->close();
    $db->close();
    exit();
}

$result = $stmt->get_result();
$contacts = [];
while ($row = $result->fetch_assoc()) {
    $contacts[] = $row;
}
$stmt->close();
$db->close();

if (count($contacts) > 0) {
    sendInfoAsJson($contacts);
} else {
    returnWithError("No Contacts Found");
}

function getRequestInfo() {
    return json_decode(file_get_contents("php://input"), true);
}

function sendInfoAsJson($data) {
    header('Content-type: application/json');
    echo json_encode($data);
}

function returnWithError($err) {
    sendInfoAsJson(['id' => 0, 'FirstName' => '', 'LastName' => '', 'Email' => '', 'error' => $err]);
}
?>
