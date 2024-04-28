<?php
$mysqli = require __DIR__. "/database.php";

$sessionID = $_GET['sessionID'];
//Get User Email from session ID
$sqlEmail ="SELECT email FROM userSessions
            WHERE sessionID = '$sessionID'";
 
$sqlResult = mysqli_query($mysqli, $sqlEmail);
$result = mysqli_fetch_assoc($sqlResult);
$userEmail = $result["email"];
$currentDate = date('Y-m-d H:i:s');
$sqlStoreMessage = "INSERT INTO userMessages
                    (senderEmail, message, dateOfMessage, recipientEmail)
                    VALUE('{$userEmail}','{$_GET['userMessage']}','{$currentDate}','{$_GET['recipientEmail']}'
                    )";
if (!mysqli_query($mysqli,$mysql)) {
    echo("<h4>SQL error description: " . mysqli_error($mysqli) . "</h4>");
}
$returnMessage = array(
    "Message" => "Sent",
);
print(json_encode($returnMessage));
mysqli_free_result($sqlResult);
mysqli_close($mysqli);
?>