<?php
require("../util/database.php");

header("Content-Type: application/json; charset=UTF-8");

function authenticate($email, $password)
{
    include("../util/database.php");
    $sql = "SELECT * FROM grc2020 WHERE email='".$email."'";
    $result = mysqli_query($conn, $sql);
    $sql2 = "SELECT * FROM grc2020 WHERE password1='".$password."'";
    $result2 = mysqli_query($conn, $sql2);
    if(mysqli_num_rows($result) > 0 && mysqli_num_rows($result2) > 0)
    {
        return 'true';
    }
    else
    {
        return 'false';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    global $coll;
    $data = json_decode(file_get_contents('php://input'), true);

    $checker = authenticate($data["email"], $data["password"]);
    if ($checker === 'true') {
        $email = $data["email"];
        $sqln = "SELECT firstname FROM grc2020 WHERE email='".$email."'";
        $sqll = "SELECT lastname FROM grc2020 WHERE email='".$email."'";
        $sqlp = "SELECT poster FROM grc2020 WHERE email='".$email."'";
        $firstname = mysqli_fetch_assoc(mysqli_query($conn, $sqln));
        $lastname = mysqli_fetch_assoc(mysqli_query($conn, $sqll));
        $poster = mysqli_fetch_assoc(mysqli_query($conn, $sqlp));
        $firstname = $firstname["firstname"];
        $lastname = $lastname["lastname"];
        $poster = $poster["poster"];
        if($poster === "")
        {
            $poster = null;
        }
        $response = array(
            "firstname" => $firstname,
            "lastname" => $lastname,
            "status" => "OK",
            "poster" => $poster
        );
        echo json_encode($response);
    }
    else {
        $response = array(
            "status" => "ERROR"
        );
        echo json_encode($response);
    }
}
