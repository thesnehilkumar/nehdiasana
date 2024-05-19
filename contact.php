<?php
header('Content-Type: application/json');


$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    $response = array("success" => false, "message" => "Database connection failed: " . $conn->connect_error);
    echo json_encode($response);
    exit;
}

function send_discord_notification()
{
    $webhook_url = "";
    $data = array(
        "content" => "@everyone !! You have new client. 
        Check it --> url"
    );

    $json_data = json_encode($data);

    $ch = curl_init($webhook_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = isset($_POST["name"]) ? trim($_POST["name"]) : "";
    $email = isset($_POST["email"]) ? filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL) : "";
    $subject = isset($_POST["subject"]) ? trim($_POST["subject"]) : "";
    $message = isset($_POST["message"]) ? trim($_POST["message"]) : "";


    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $response = array("success" => false, "message" => "Please fill in all fields.");
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response = array("success" => false, "message" => "Invalid email format.");
    } else {

        $name = $conn->real_escape_string($name);
        $email = $conn->real_escape_string($email);
        $subject = $conn->real_escape_string($subject);
        $message = $conn->real_escape_string($message);

        $current_date = date("Y-m-d");

        $sql = "INSERT INTO contact (date_added, name, email, subject, message) VALUES ('$current_date', '$name', '$email', '$subject', '$message')";

        if ($conn->query($sql) === TRUE) {
            $response = array("success" => true, "message" => "Your message has been stored successfully!");


            send_discord_notification($name, $email, $subject, $message);
        } else {
            $response = array("success" => false, "message" => "Error: " . $sql . "<br>" . $conn->error);
        }
    }
    echo json_encode($response);
    exit;
} else {

    $response = array("success" => false, "message" => "Invalid request method.");
    echo json_encode($response);
    exit;
}

$conn->close();
