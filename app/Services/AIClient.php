<?php
namespace app\Services;

class AIClient
{
    private $apiKey;
    private $model;
    private $timeout;
    private $provider;

    public function __construct()
    {
        $config = require __DIR__ . '/../Config/config.php';
        $this->apiKey = $config['ai']['api_key'];
        $this->model = $config['ai']['model'];
        $this->timeout = $config['ai']['timeout'];
        $this->provider = $config['ai']['provider'];
    }

    public function generateQuestions(string $prompt): ?array
    {
        if ($this->provider !== 'openai') {
            // In a real application, you would have different implementations for different providers
            throw new \Exception('Unsupported AI provider');
        }

        $url = 'https://api.openai.com/v1/chat/completions';
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey,
        ];
        $data = [
            'model' => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful assistant that generates SNBT questions.'],
                ['role' => 'user', 'content' => $prompt]
            ],
            'response_format' => ['type' => 'json_object']
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            // Handle cURL error
            error_log('AI API cURL Error: ' . $error);
            return null;
        }

        $responseData = json_decode($response, true);

        if (isset($responseData['choices'][0]['message']['content'])) {
            return json_decode($responseData['choices'][0]['message']['content'], true);
        }

        error_log('AI API Error: Unexpected response format. Response: ' . $response);
        return null;
    }
}