<?php
$input = json_decode(file_get_contents('php://input'), true);
$message = $input['message'];

$config = 'config.json';
$jsonContent = file_get_contents($config);
$configData = json_decode($jsonContent, true);
$api_key = $configData['OPEN_AI_KEY']; 
$assistant_id = $configData['ASSISTANT_ID'];

$api_url = 'https://api.openai.com/v1';

$create_thread_url = $api_url . '/threads';
$thread_res = CreateThread($api_key, $create_thread_url);
if ($thread_res) {
    $thread_id = $thread_res;
    // $message = 'Google API';
    //Step 2: Add a Message to thread using endpoint v1/threads/$thread_id/messages
    $add_message_to_thread_url = $api_url . "/threads/$thread_id/messages";
    $add_msg_res = addMessageToThread($api_key, $message, $add_message_to_thread_url);
    if ($add_msg_res) {
        // Step 3: Run the Assistant on the Thread
        $data_ass=["assistant_id"=>"$assistant_id"];
        $run_url = $api_url . '/threads/' . $thread_id . '/runs';
        $run_id = runMessageThread($api_key, $data_ass, $run_url);
        if ($run_id) {
            //Step 4:run steps 
            $run_steps_url = "$api_url/threads/$thread_id/runs/$run_id/steps";
            getRunSteps($api_key,$run_steps_url);           
            //Step 5: status of run
            $runs_sts_url = "$api_url/threads/$thread_id/runs/$run_id";
            $sts_chk = getRunStatus($api_key,$runs_sts_url);
            //get messages using endpoint v1/threads/{{thread_id}}/messages
            $ass_res_rul = "$api_url/threads/$thread_id/messages";
            sleep(10);
            $res = getAssResponseMessages($api_key,$ass_res_rul);
            if ($res) {
                // Output the Assistant's response
                //echo $res;
                $data_d = json_decode($res, true);
                // echo 'Assistant Response: '.$data_d['data'][0]['content'][0]['text']['value'];
                $html = $data_d['data'][0]['content'][0]['text']['value'];
                $html = str_replace("```html", '', $html);
                $html = str_replace("```", '', $html);
                echo json_encode(['response' => $html]);
            }
            else
            {
                return false;
            }
        }
    }
    
}

function CreateThread($api_key, $url) {
    $response = curlAPIPost($api_key, $url);
    if($response)
    {
        $thread_data = json_decode($response, true);
        if(isset($thread_data['id']))
        {
            $thread_id = $thread_data['id'];
            //echo "THREAD ID:$thread_id<r>";
            return $thread_id;
        }
        else
        {
            return false;
        }
    }
    else
    {
        return false;
    }
}

function addMessageToThread($api_key, $message, $create_thread_url)
{
    $add_thread_data = ["role" => "user", "content" => $message . "\nYou are a helpful assistant designed to output HTML."];
    $response = curlAPIPost($api_key, $create_thread_url, $add_thread_data);
    if($response)
    {
        $msg_data = json_decode($response, true);
        if(isset($msg_data['id']))
        {
            return $msg_data['id'];
        }
        else
        {
            return false;
        }
    }
    else
    {
        return false;
    }
    
}

//run message thread
function runMessageThread($api_key,$message,$run_thread_url)
{
    $response = curlAPIPost($api_key,$run_thread_url,$message);
    if($response)
    {
        //echo "runMessageThread:$response";
        $run_data = json_decode($response, true);
        if(isset($run_data['id']))
        {
            return $run_data['id'];
        }
        else
        {
            return false;
        }
    }
    else
    {
        return false;
    }
}

function getRunSteps($api_key,$url) {
    $response = getCurlCall($api_key, $url);
    return $response;
}

function getRunStatus($api_key,$url) {
    $response = getCurlCall($api_key,$url);
    //echo "getRunStatus:$response<br>";
    return $response;
}

function getAssResponseMessages($api_key, $url) {
    $response = getCurlCall($api_key, $url);
    return $response;
}


function curlAPIPost($api_key, $url, $data='') {
    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key,
        'OpenAI-Beta: assistants=v2',
    ];
    $curl = curl_init($url);
    if ($data != '') {
        $json_data = json_encode($data);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);  
    if ($err) {
        echo("cURL Error #:" . $err);
        return false;
    }
    else {
        return $response;
    }
}

function getCurlCall($api_key,$url) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'OpenAI-Beta: assistants=v2',
            'Authorization: Bearer '.$api_key,
        ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);  
    if ($err) {
        echo("cURL Error #:" . $err);
        return false;
    }
    else {
        return $response;
    }   
}
?>
