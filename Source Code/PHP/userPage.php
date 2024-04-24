<?php



//Login details for the database are stored in the external file.
$mysqli = require __DIR__ . "/database.php";

$sessionID = $_GET['sessionID'];

$sqlSessionInfo = "SELECT * FROM userSessions 
                WHERE sessionHash = '{$sessionID}' 
                AND timeOfSession >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
//Check if the hash value is valid for the email and duration.
$sessionInfo = mysqli_query($mysqli, $sqlSessionInfo);
if (mysqli_num_rows($sessionInfo) == 0) {
        $response  = array(
                "sessionID" => "False",
        );
        print(json_encode($response));
}
else{
        $result = mysqli_fetch_assoc($sessionInfo);
        $email = $result['email'];
        $sqlSTMT = "SELECT id, fullname, email, 
                        username, dateofBirth, gender, 
                        nhsNumber, insuranceProvider, insuranceNumber 
                FROM users
                WHERE email = '{$email}'
        ";

        //Convert the raw data into a JSON string
        $rawData = mysqli_query($mysqli, $sqlSTMT);
        $data = mysqli_fetch_assoc($rawData);
        print json_encode($data);
        mysqli_free_result($rawData);

}
mysqli_free_result($sessionInfo);
mysqli_close($mysqli);
 ?>
