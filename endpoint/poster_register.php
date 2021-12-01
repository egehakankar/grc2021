<?php
include("../util/database.php");
include("../util/mailer.php");

header("Content-Type: application/json; charset=UTF-8");

$PASSWORD_LENGTH = 20;
function random_string($length)
{
    return substr(md5(rand()), 0, $length - 1);
}

function ends_with($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

function validate_email($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function is_bilkent_mail($email)
{
    return validate_email($email) && ends_with($email, "bilkent.edu.tr");
}

//Checks if email exists or not.
function ifEmail($email)
{
    include("../util/database.php");
    $sql = "SELECT * FROM grc2020 WHERE email='".$email."'";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) > 0)
    {
        return 'true';
    }
    else
    {
        return 'false';
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data["email"];
    if (is_bilkent_mail($email)) {
        $password = random_string(20);
        $existing_user_check = ifEmail($email);

        if ($existing_user_check === 'false')
        {
            $id = $data["id"];
            $email = $data["email"];
            $firstname = $data["firstname"];
            $lastname = $data["lastname"];
            $poster = null;
            $date = date('Y-m-d H:i:s');

            $sql = "INSERT INTO grc2020(idNo, email, firstname, lastname, password1, poster, savedAt)
            VALUES ('$id', '$email', '$firstname', '$lastname', '$password', '$poster', '$date')";
            mysqli_query($conn, $sql);
        }
        else
        {
            $id = $data["id"];
            $email = $data["email"];
            $firstname = $data["firstname"];
            $lastname = $data["lastname"];
            $poster = null;
            $date = date("Y-m-d H:i:s");

            $sql = "UPDATE grc2020 SET idNo = '$id', firstname = '$firstname', lastname = '$lastname', password1 = '$password', poster = '$poster',
            savedAt = '$date' WHERE email='".$email."'";
            mysqli_query($conn, $sql);
        }

        send_password($data["firstname"], $data["lastname"], $password, $data["email"]);
        $variable = "Variable";
        $response = array(
            "status" => "OK",
            "email" => $email,
            "password" => $password
        );
        echo json_encode($response);

    } else {
        $response = array(
            "status" => "ERROR",
            "error" => "invalid email",
            "email" => $email
        );
        echo json_encode($response);
    }
}
