<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        $message = $request->input('message');
        $apiKey = env('GEMINI_API_KEY'); // Get API key from .env

        if (!$apiKey) {
            return response()->json(['error' => 'API key is missing'], 500);
        }

        try {
            $client = new Client();
            $response = $client->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . $apiKey, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    "contents" => [
                        [
                            "role" => "user",
                            "parts" => [
                                [
                                    "text" => $message
                                ]
                            ]
                        ]
                    ],
                    "generationConfig" => [
                        "temperature" => 0.7,
                        "maxOutputTokens" => 1024
                    ]
                ]
            ]);

            $responseBody = json_decode($response->getBody(), true);
            
            // Extract response text
            $reply = $responseBody['candidates'][0]['content']['parts'][0]['text'] ?? 'No response from AI';
            
            return response()->json(['reply' => $reply]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}