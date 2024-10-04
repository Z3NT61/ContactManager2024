<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
    $inData = getRequestInfo();

    $login = $inData["login"];
    $password = $inData["password"];
    $firstName = $inData["firstName"];
    $lastName = $inData["lastName"];
    
    $conn = new mysqli("localhost", "root", "b+YXZI98+xeB", "SPROJECTDB");
    if( $conn->connect_error )
	{
		returnWithError( $conn->connect_error );
	}
    else
	{
        $stmt = $conn->prepare("INSERT into MAINUSERS (firstName, lastName, login, password) VALUES(?,?,?,?)");
        $stmt->bind_param("ssss", $firstName, $lastName, $login, $password);
        $stmt->execute();
	$stmt->close();
	$conn->close();
	returnWithError("");
    }

    function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
	
	function returnWithError( $err )
	{
		$retValue = '{"error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
?>
