<?php
session_start();  // Start the session to access the UserID

// for debugging purposes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Function to get input data from the request body
function getRequestInfo()
{
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        returnWithError("Invalid JSON payload: " . json_last_error_msg());
        exit();
    }

    return $data;
}

// Function to send JSON response
function sendResultInfoAsJson($obj)
{
    header('Content-type: application/json');
    echo json_encode($obj);
}

// Function to return error message as JSON
function returnWithError($err)
{
    $retValue = array("error" => $err);
    sendResultInfoAsJson($retValue);
}


//fucntions above are for debbugging purposes




// Main script starts here

// Check if the user is logged in
if (!isset($_SESSION['UserID'])) {
    returnWithError("User is not logged in.");
    exit();
}

$userId = $_SESSION['UserID'];  // uses ssession start

$inData = getRequestInfo();


if (!isset($inData["firstName"]) || !isset($inData["lastName"]) || !isset($inData["email"])) {
    returnWithError("Missing required fields.");
    exit();
}

// set variables
$firstName = $inData["firstName"];
$lastName = $inData["lastName"];
$email = $inData["email"];

// Database connection
$conn = new mysqli("localhost", "root", "b+YXZI98+xeB", "SPROJECTDB");
if ($conn->connect_error) 
{
    returnWithError("Database connection failed: " . $conn->connect_error);
    exit();
}

// Insert into CONTACTS table
$stmt = $conn->prepare("INSERT INTO CONTACTS (FirstName, LastName, Email) VALUES (?, ?, ?)");
if ($stmt === false) {
    returnWithError("Failed to prepare INSERT statement for CONTACTS: " . $conn->error);
    $conn->close();
    exit();
}
$stmt->bind_param("sss", $firstName, $lastName, $email);
$stmt->execute();

// any errors
if ($stmt->error) {
    returnWithError("Failed to execute INSERT statement for CONTACTS: " . $stmt->error);
    $stmt->close();
    $conn->close();
    exit();
}

// Get the ID of the newly inserted contact
$contactID = $stmt->insert_id;  // This retrieves the auto-incremented ID from the CONTACTS table
$stmt->close();  // Close the first statement

// Insert into USERCONTACTS table linking the new contact with the user from MAINUSERS
$stmt = $conn->prepare("INSERT INTO USERCONTACTS (UserID, ContactID) VALUES (?, ?)");
if ($stmt === false) {
    returnWithError("Failed to prepare INSERT statement for USERCONTACTS: " . $conn->error);
    $conn->close();
    exit();
}
$stmt->bind_param("ii", $userId, $contactID);
$stmt->execute();

// eerrors
if ($stmt->error) {
    returnWithError("Failed to execute INSERT statement for USERCONTACTS: " . $stmt->error);
    $stmt->close();
    $conn->close();
    exit();
}

// stops connections
$stmt->close();
$conn->close();

// successful contact added
$response = array("message" => "Contact added successfully!");
sendResultInfoAsJson($response);
?>
