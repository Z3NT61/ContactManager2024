<?php

$inData = getRequestInfo();

$db = new mysqli("localhost", "root", "b+YXZI98+xeB", "SPROJECTDB"); #connects with the DB using the users login n password
if($db->connect_error){
    returnWithError($db->connect_error);
}
else{
    $getID = $db->prepare("select ContactID from USERCONTACTS where UserID=?");
    $getID->bind_param("s", $inData["id"]);
    $getID->execute();
    $retID= $getID->get_result();
    if($ret = $retID->fetch_assoc()){
	    $arr = null;
	    $stmt = $db->prepare("select * from CONTACTS where ID=?");
	    do{
		    $stmt->bind_param("s", $ret["ContactID"]);
		    $stmt->execute();
		    $contact = $stmt->get_result();
		    if($tack = $contact->fetch_assoc()){
			    $sendarr = returnWithInfo($tack["FirstName"], $tack["LastName"], $tack["Email"], $arr);
			    $arr = $sendarr;
		    }
		    else{
			    returnWithError("No Contacts Found");
		    }
	    }while ($ret = $retID->fetch_assoc());
	    sendInfoAsJson(($sendarr));
    }
    else{
	    returnWithError("No UserID Found");
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
    $retValue = '{"id":0, "FirstName":"", "LastName":"error", "error": "'. $err . '"}';
    sendInfoAsJson($retValue);
}

function returnWithInfo($FirstName, $LastName, $id, $arr){
    #to do
	$retvalue = '{"Email":"' . $id . '","FirstName":"'. $FirstName . '","LastName":"'. $LastName . '"}';
	if($arr === NULL)
		return $retvalue;
	else{
		$user[] = json_decode($arr, true);
		$user[] = json_decode($retvalue, true);
		$json_merge = json_encode($user);
		return $json_merge;
	}

}
