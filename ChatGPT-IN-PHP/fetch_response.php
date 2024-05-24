
<?php
$config = 'config.json';
$input = json_decode(file_get_contents('php://input'), true);
$message = $input['message'];

$jsonContent = file_get_contents($config);
$configData = json_decode($jsonContent, true);
$openAI = 'sk-proj-p1LpopO4DTRClIekXA6PT3BlbkFJBuZFl8MSXkjTGwyejGPh';//$configData['OPEN_AI_KEY']; 

$url = 'https://api.openai.com/v1/chat/completions';

$data = [
    "model" => "gpt-4o",
    "messages" => [
        ["role" => "system", "content" => "You are a helpful assistant designed to output HTML."],
        ["role" => "user", "content" => $message]
    ],
];

$options = [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $openAI,
        'Content-Type: application/json'
    ],
    CURLOPT_POSTFIELDS => json_encode($data)
];

$ch = curl_init();
curl_setopt_array($ch, $options);
$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode(['response' => 'Error communicating with the API']);
    exit;
}

var_dump(json_decode($response, true));
exit;
$html = json_decode($response, true)['choices'][0]['message']['content'];

$html = str_replace('```html', '', $html);
$html = str_replace('```', '', $html);

echo json_encode(['response' => $html]);
curl_close($ch);
?>
