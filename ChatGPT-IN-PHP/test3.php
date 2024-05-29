<?php 
    class OpenAPIClient {
        private $apiKey;
        private $apiUrl;
        
        public function __construct($apiKey, $apiUrl = 'https://api.openai.com/v1/chat/completions') {
            $this->apiKey       = $apiKey;
            $this->apiUrl       = $apiUrl;
        }

        public function request($message) {
            $data = [
                "model"     => "gpt-4o",
                "messages"  => [
                    ["role" => "system", "content" => "You are a helpful assistant designed to output Markdown."],
                    ["role" => "user", "content" => $message]
                ],
            ];

            $options = [
                CURLOPT_URL             => $this->apiUrl,
                CURLOPT_RETURNTRANSFER  => true,
                CURLOPT_POST            => true,
                CURLOPT_HTTPHEADER      => [
                    'Authorization: Bearer ' . $this->apiKey,
                    'Content-Type: application/json'
                ],
                CURLOPT_POSTFIELDS => json_encode($data)
            ];

            $ch = curl_init();
            curl_setopt_array($ch, $options);
            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                return json_encode(['response' => 'Error communicating with the API']);
            }
            curl_close($ch);
            return json_encode(['response' => json_decode($response, true)['choices'][0]['message']['content']]);
        }
    };
    
?>
