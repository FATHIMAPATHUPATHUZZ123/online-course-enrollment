<?php
include '../database.php';
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
$question = $data['question'];
$user = $data['user'];

$payload = [
  "model" => "gpt-4o-mini",
  "messages" => [
    ["role" => "system", "content" => "You are an instructor assistant for an online course platform. Answer clearly."],
    ["role" => "user", "content" => $question]
  ]
];

$curl = curl_init("https://api.openai.com/v1/chat/completions");
curl_setopt($curl, CURLOPT_HTTPHEADER, [
  "Content-Type: application/json",
  "Authorization: Bearer YOUR_OPENAI_API_KEY"
]);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($curl);
curl_close($curl);

$res = json_decode($response, true);
$answer = $res['choices'][0]['message']['content'];

mysqli_query($conn,
  "INSERT INTO discussion_board (user, text, reply, replied_by)
   VALUES ('$user', '$question', '$answer', 'bot')"
);

echo json_encode(["bot_reply" => $answer]);
?>
