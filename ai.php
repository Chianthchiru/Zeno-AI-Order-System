<?php

function extractOrderData($message){

    $url = "http://localhost:1234/v1/chat/completions";

    $prompt = $prompt = "
Return ONLY valid JSON.

{
  \"intent\": \"\",
  \"part_name\": \"\",
  \"material\": \"\",
  \"quantity\": 0,
  \"deadline\": \"\",
  \"order_id\": 0,
  \"status\": \"\",
  \"quality_note\": \"\"
}

Message:
$message
";

    $data = [
        "model" => "phi-3-mini-4k-instruct",

        "messages" => [
            [
                "role" => "system",
                "content" =>
                "You are an NLP extraction engine.
                 Always return valid JSON only."
            ],

            [
                "role" => "user",
                "content" => $prompt
            ]
        ],

        "temperature" => 0
    ];

    $options = [
        "http" => [

            "header" =>
                "Content-Type: application/json\r\n",

            "method" => "POST",

            "content" => json_encode($data)
        ]
    ];

    $context = stream_context_create($options);

    $result = file_get_contents(
        $url,
        false,
        $context
    );

    return json_decode($result, true);

}

?>