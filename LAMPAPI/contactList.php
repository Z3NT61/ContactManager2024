<?php

// Function to retrieve request data from the input
function getRequestInfo() {
    return json_decode(file_get_contents('php://input'), true);
}

// Function to send a JSON response back to the client
function sendResultInfoAsJson($obj) {
    header('Content-type: application/json');
    echo $obj;
}

// Function to handle errors and return a structured error response
function returnWithError($err) {
    $retValue = '{"id":0,"firstName":"","lastName":"","error":"' . $err . '"}';
    sendResultInfoAsJson($retValue);
}

// Function to handle successful data retrieval and return a structured data response
function returnWithInfo($resArray) {
    $retValue = json_encode($resArray);
    sendResultInfoAsJson($retValue);
}

// Retrieve request information
$inData = getRequestInfo();

// Create a connection to the database
$conn = new mysqli("localhost", "root", "b+YXZI98+xeB", "SPROJECTDB");

// Check for connection errors
if ($conn->connect_error) {
    returnWithError($conn->connect_error);
} else {
    // Prepare a name pattern for SQL LIKE matching, including wildcard characters
    $paddedName = "%" . strtolower($inData["name"]) . "%";

    // Prepare an SQL statement to select contact details where the user's input matches either first name, last name, or full name
    $stmt = $conn->prepare("SELECT ID, FirstName, LastName, Email, Phone, Address FROM Contacts WHERE (LOWER(FirstName) LIKE ? OR LOWER(LastName) LIKE ? OR LOWER(CONCAT(FirstName, ' ', LastName)) LIKE ?) AND User_ID = ?");
    $stmt->bind_param("sssi", $paddedName, $paddedName, $paddedName, $inData["userId"]);

    // Execute the statement
    $stmt->execute();

    // Get the result of the query
    $result = $stmt->get_result();

    // Check if the query returned any rows
    if ($result->num_rows > 0) {
        // Fetch all results as an associative array
        $arr = $result->fetch_all(MYSQLI_ASSOC);
        returnWithInfo($arr);
    } else {
        returnWithError("No records found.");
    }

    // Close the statement and connection to free up resources
    $stmt->close();
    $conn->close();
}

?>
