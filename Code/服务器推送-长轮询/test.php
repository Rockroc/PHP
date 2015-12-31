<?php
//--------------------推送代码----------------------------
//timeout in seconds
$timeout = 300;

// log start time
$start_time = time();

// get messge from local file
function get_msg(){
    return file_get_contents('data.json');
}

// get message
$last_msg = get_msg();

// start the loop
while (true){
    // get current time
    $current_time = time();
    
    // check if we are timed out
    if ($current_time - $start_time > $timeout){
        echo 'timeout! no new message!';
        break;
    }
    
    // get latest message
    $current_msg = get_msg();
    
    // check if the message has been changed
    if ($last_msg != $current_msg){
        $response['msg'] = $current_msg;
        echo json_encode($response);
        break;
    }
    // sleep 1 sec
    sleep(1);
}
