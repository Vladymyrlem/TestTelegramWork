<?php

$input = file_get_contents('php://input');
$data = json_decode($input);

$user_id = $data->message->from->id;
$text = $data->message->text;
$chat_id = $data->message->chat->id;
$host = 'localhost';
$dbname = 'dev_candidate_11';
$username = 'dev_candidate_11';
$password = 'iSghFRdIId';
$token = '6326761434:AAEBs-gjnSUzBu3i_k9De6SXW5rbN4M66LY';

$mysqli = new mysqli($host, $username, $password, $dbname);

if ($mysqli->connect_error) {
    die("Connection error: " . $mysqli->connect_error);
}

if ($text === '/start') {
    $user_name = $data->message->from->first_name;

    $query = "SELECT * FROM users WHERE user_id = $user_id";
    $result = $mysqli->query($query);

    if (!$result) {
        die("Response error: " . $mysqli->error);
    }

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $response_text = "Welcome back, " . $row['username'] . "! I remember you.:)";
    } else {
        $insert_query = "INSERT INTO users (user_id, username) VALUES ($user_id, '$user_name')";
        $mysqli->query($insert_query);
        $response_text = "Welcome, " . $user_name . ". I see you for the first time here.";
    }

    $url = "https://api.telegram.org/bot$token/sendMessage?text=$response_text&chat_id=$chat_id";
    file_get_contents($url);
}

$mysqli->close();

?>