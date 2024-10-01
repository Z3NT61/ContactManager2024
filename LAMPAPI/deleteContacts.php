<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define response as an empty array to store any errors or success messages
$response = array();

// Get the raw POST data (assuming JSON payload)
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Check if contact ID is provided
if (!isset($data['contactId'])) {
    $response['error'] = "Contact ID is required.";
    echo json_encode($response);
    exit();
}

$contactId = $data['contactId'];

// Debugging: log the contactId for reference
$response['debug'] = "Attempting to delete contact with ID: " . $contactId;

// Database connection
$servername = "localhost"; // Replace with your DB host
$username = "root"; // Replace with your DB username
$password = "b+YXZI98+xeB"; // Replace with your DB password
$dbname = "SPROJECTDB"; // Replace with your DB name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    $response['error'] = "Connection failed: " . $conn->connect_error;
    echo json_encode($response);
    exit();
}

// Prepare and execute the SQL DELETE statement to remove the contact from USERCONTACTS
$sql_usercontacts = "DELETE FROM USERCONTACTS WHERE ContactID = ?";
$stmt = $conn->prepare($sql_usercontacts);
if (!$stmt) {
    $response['error'] = "Failed to prepare USERCONTACTS delete query: " . $conn->error;
    echo json_encode($response);
    exit();
}

$stmt->bind_param("i", $contactId);

// Check if the statement executed successfully for USERCONTACTS
if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $response['success'][] = "Contact deleted from USERCONTACTS.";
    } else {
        $response['error'][] = "No contact found in USERCONTACTS with the provided ID.";
    }
} else {
    $response['error'][] = "Error deleting contact from USERCONTACTS: " . $stmt->error;
}

// Close the statement for USERCONTACTS
$stmt->close();

// Prepare and execute the SQL DELETE statement to remove the contact from CONTACTS
$sql_contacts = "DELETE FROM CONTACTS WHERE ID = ?";
$stmt = $conn->prepare($sql_contacts);
if (!$stmt) {
    $response['error'] = "Failed to prepare CONTACTS delete query: " . $conn->error;
    echo json_encode($response);
    exit();
}

$stmt->bind_param("i", $contactId);

// Check if the statement executed successfully for CONTACTS
if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $response['success'][] = "Contact deleted from CONTACTS.";
    } else {
        $response['error'][] = "No contact found in CONTACTS with the provided ID.";
    }
} else {
    $response['error'][] = "Error deleting contact from CONTACTS: " . $stmt->error;
}

// Close the prepared statement and the database connection
$stmt->close();
$conn->close();

// Return the response as JSON
echo json_encode($response);
?>
