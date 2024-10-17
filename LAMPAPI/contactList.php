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
    $paddedName = "%" . strtolower($inData["searchContactItem"]) . "%";
    $getContacts = $conn->prepare("select * from USERCONTACTS where UserID = ?");
    $getContacts->bind_param("i", $inData["userId"]);
    // Prepare an SQL statement to select contact details where the user's input matches either first name, last name, or full name
    $getContacts->execute();
    $contactIDGet = $getContacts->get_result();
    $contacts = [];
    while($row=$contactIDGet->fetch_assoc()){
        $stmt = $conn->prepare("SELECT * FROM CONTACTS WHERE (LOWER(FirstName) LIKE ? OR LOWER(LastName) LIKE ? OR LOWER(CONCAT(FirstName, ' ', LastName)) LIKE ?) AND ID = ?");
        $stmt->bind_param("sssi", $paddedName, $paddedName, $paddedName, $row["ContactID"]);
        $stmt->execute();

        // Get the result of the query
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        if($result->num_rows > 0){
            $contacts[] = [
                'contactId' => $data['ID'],  // Include the ContactID in the response
                'firstName' => $data['FirstName'],
                'lastName' => $data['LastName'],
                'email' => $data['Email']
            ];
        }
    }

    // Check if the query returned any rows
    if ($contacts) {
        // Fetch all results as an associative array
        returnWithInfo($contacts);
    } else {
        returnWithError("No records found.");
    }

    // Close the statement and connection to free up resources
    $conn->close();
}

?>
