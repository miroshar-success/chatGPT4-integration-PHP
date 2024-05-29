<?php
    include("test2.php");
    include("test3.php");
    
    $input = json_decode(file_get_contents('php://input'), true);
    $message = $input['message'];

    $config = json_decode(file_get_contents('config.json'), true);
    if ($config['CUSTOM']) {
        $client = new CustomOpenAPIClient($config['OPEN_AI_KEY'], $config['ASSISTANT_ID']);
    }
    else {
        $client = new OpenAPIClient($config['OPEN_AI_KEY']);
    }
    echo $client->request($message);
?>
