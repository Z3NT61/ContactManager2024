<?php
session_start();  // Start the session to access the UserID

$inData = getRequestInfo();

$contactId = $inData["contactId"];
$firstName = $inData["firstName"];
$lastName = $inData["lastName"];
$email = $inData["email"];

// Debugging log
error_log("Updating contact with ID: $contactId");
error_log("First Name: $firstName, Last Name: $lastName, Email: $email");

// Connect to the database
$conn = new mysqli("localhost", "root", "b+YXZI98+xeB", "SPROJECTDB");
if ($conn->connect_error) {
    returnWithError($conn->connect_error);
} else {
    // Check if the contact exists
    $ret = $conn->prepare("SELECT ID FROM CONTACTS WHERE ID=?");
    $ret->bind_param("i", $contactId);  // Contact ID is an integer
    $ret->execute();
    $ret->store_result();
    
    // If the contact exists, proceed to update
    if ($ret->num_rows > 0) {
        // Prepare the UPDATE statement
        $stmt = $conn->prepare("UPDATE CONTACTS SET FirstName=?, LastName=?, Email=? WHERE ID=?");
        $stmt->bind_param("sssi", $firstName, $lastName, $email, $contactId);  // Contact ID is last, and it's an integer

        // Execute the query and check if successful
        if ($stmt->execute()) {
            error_log("SQL executed successfully.");
            if ($stmt->affected_rows > 0) {
                returnWithError("");  // No error, contact updated successfully
            } else {
                returnWithError("No changes made to the contact.");
            }
        } else {
            error_log("SQL execution failed: " . $stmt->error);
            returnWithError("Failed to update contact.");
        }
        
        // Close the statement
        $stmt->close();
    } else {
        returnWithError("No contact found with this ID.");
    }
    
    // Close the result and connection
    $ret->close();
    $conn->close();
}

// Helper functions
function getRequestInfo()
{
    return json_decode(file_get_contents('php://input'), true);
}

function sendResultInfoAsJson($obj)
{
    header('Content-type: application/json');
    echo $obj;
}

function returnWithError($err)
{
    $retValue = '{"error":"' . $err . '"}';
    sendResultInfoAsJson($retValue);
}
?>
