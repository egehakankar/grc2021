<?php
include("../util/database.php");
include("../util/mailer.php");

$SALT = "ImtFKFllRqFURBY7KGGF";
$FILE_SIZE_LIMIT = 209715200; // 200 MB
$BASE_URL = "https://ieee.bilkent.edu.tr/grc2020";

// dosya sınırı bcc
// posterleri gönderen kişiler tablosu

function authenticate($email, $password)
{
    include("../util/database.php");
    $sql = "SELECT * FROM grc2020 WHERE email='".$email."'";
    $result = mysqli_query($conn, $sql);
    $sql2 = "SELECT * FROM grc2020 WHERE password1='".$password."'";
    $result2 = mysqli_query($conn, $sql2);
    if(mysqli_num_rows($result) > 0 && mysqli_num_rows($result2) > 0)
    {
        return true;
    }
    else
    {
        return false;
    }
}

$oversize = false;
$not_pdf = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_FILES['files'])) {
        if (authenticate($_POST["email"], $_POST["password"])) {
            $path = '../docs/submissions/';
            $extensions = ['pdf'];

            $file_name = $_FILES['files']['name'][0];
            $file_tmp = $_FILES['files']['tmp_name'][0];
            $file_type = $_FILES['files']['type'][0];
            $file_size = $_FILES['files']['size'][0];
            $tmp = explode('.', $file_name);
            $file_ext = strtolower(end($tmp));

            $file = $path . $file_name;

            if (!in_array($file_ext, $extensions)) {
                $not_pdf = true;

                $response = array(
                    "status" => "ERROR",
                    "error" => "not pdf"
                );
                echo json_encode($response);

                return;
            }

            if ($file_size > $FILE_SIZE_LIMIT) {
                $oversize = true;

                $response = array(
                    "status" => "ERROR",
                    "error" => "oversize",
                    "limit" => $FILE_SIZE_LIMIT
                );
                echo json_encode($response);

                return;
            }

            $new_file_name = $path . md5($SALT . $_POST['email']) . ".pdf";
            move_uploaded_file($file_tmp, $file);
            rename($file, $new_file_name);

            $poster_link = $BASE_URL . substr($new_file_name, 2);

            $emaill = $_POST["email"];
            $sql = "UPDATE grc2020 SET poster = '$poster_link' WHERE email='".$emaill."'";
            mysqli_query($conn, $sql);

            $sqln = "SELECT firstname FROM grc2020 WHERE email='".$email."'";
            $sqll = "SELECT lastname FROM grc2020 WHERE email='".$email."'";
            $firstname = mysqli_fetch_assoc(mysqli_query($conn, $sqln));
            $lastname = mysqli_fetch_assoc(mysqli_query($conn, $sqll));
            $firstname = $firstname["firstname"];
            $lastname = $lastname["lastname"];

            send_confirmation($firstname, $lastname, $poster_link, $_POST["email"]);

            $response = array(
                "status" => "OK",
                "poster" => $poster_link
            );
            echo json_encode($response);
        }
    }
}
