<?php
include("../util/database.php");

$SECRET = "bizimgrc**aras";

$data = json_decode(file_get_contents("php://input"), true);
$secret = $data["secret"];

if ($secret == $SECRET) {
    $data = iterator_to_array($coll->find(array()));
    $users = Array();

    foreach ($data as $uid => $user) {
        unset($user["password"]);
        unset($user["_id"]);
        unset($user["saved_at"]);
        array_push($users, $user);
    }

    echo json_encode($users);
}