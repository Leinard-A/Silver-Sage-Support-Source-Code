<?php
$mysqli = require __DIR__. "/database.php";

//Get staff emails from the database
$sqlRequest_staffEmail = "SELECT email FROM users 
                        WHERE employee = 1 ";

$sqlResult = mysqli_query($mysqli, $sqlRequest_staffEmail);
$result = mysqli_fetch_all($sqlResult);
print(json_encode($result));
mysqli_free_result($sqlResult);

mysqli_close($mysqli);


?>