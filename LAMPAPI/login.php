<?php

#this is funcionality assuming you have already logged in, therefore, this is
#is just a request get function, returning
#first name, last name, contacts, and more
    function getRequestInfo(){
        return json_decode(file_get_contents("php://input"), true);
    }

    function sendInfoAsJson($obj){
        header('Content-type: application/json')
        echo $obj;
    }

    function returnWithError($err){
    $retValue = '{"id":0, "FirstName":"", "LastName":"error", "error": "'. $err . '"}';
        sendInfoAsJson($retValue);
    }

function returnWithInfo($FirstName, $LastName){
    #to do
}

