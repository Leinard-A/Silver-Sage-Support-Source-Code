<?php
$mysqli = require __DIR__. "/database.php";

$sessionID = $_GET['sessionID'];
$sqlEmail ="SELECT email FROM userSessions
            WHERE sessionHash = '{$sessionID}'";

//Get the user email address from session ID 
$sqlResult = mysqli_query($mysqli, $sqlEmail);
$result = mysqli_fetch_assoc($sqlResult);
$userEmail = $result["email"];
mysqli_free_result($sqlResult);

//Get messages that are being sent to the email
$sqlGetMessags ="SELECT * FROM userMessages
                WHERE recipientEmail = '{$userEmail}'";
$sqlMessages = mysqli_query($mysqli, $sqlGetMessags);
$resultMessages = mysqli_fetch_all($sqlMessages);
if ($resultMessages == null){
    $message = array(
        "NoMessages"=> True
    );
    print(json_encode($message));
}
else{
    print(json_encode($resultMessages));
}
mysqli_free_result($sqlMessages);
mysqli_close($mysqli);


?>