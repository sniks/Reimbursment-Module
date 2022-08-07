<?php
$GLOBALS['data'] = '';
$GLOBALS['apipath'] = 'http://127.0.0.1/Reimbursement%20Module/api/';
if (isset($_REQUEST['submit'])) {
    // From URL to get webpage contents.
    $_REQUEST['inputs'] = json_decode($_REQUEST['inputs'], true);

    $ch = curl_init();
    // Count total files
    $countfiles = count($_FILES['attachment']['name']);
    // Loop all files
    for ($index = 0; $index < $countfiles; $index++) {

        if (isset($_FILES['attachment']['name'][$index]) && $_FILES['attachment']['name'][$index] != '') {
            // File name
            $filename = $_FILES['attachment']['name'][$index];
            
            move_uploaded_file($_FILES['attachment']['tmp_name'][$index], "uploads/" . $_FILES['attachment']['name'][$index]);

                if (function_exists('curl_file_create')) {
                    //determine file path 
                    $_REQUEST['files'][$index] = curl_file_create($_REQUEST['location'] . '/uploads/' . $_FILES['attachment']['name'][$index]);
                } else { // 
                    $_REQUEST['files'][$index] = '@' . realpath($_REQUEST['location'] . '/uploads/' . $_FILES['attachment']['name'][$index]);
                }
        }
    }
    //create curl file

    curl_setopt($ch, CURLOPT_URL, $GLOBALS['apipath']);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('User-Agent: Opera/9.80 (Windows NT 6.2; Win64; x64) Presto/2.12.388 Version/12.15', 'Referer: http://127.0.0.1/Reimbursement%20Module/', 'Content-Type: multipart/form-data'));
    $_REQUEST['attachment'] = new CURLFile($_REQUEST['attachment']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($_REQUEST));

    // In real life you should use something like:
    // curl_setopt($ch, CURLOPT_POSTFIELDS, 
    //          http_build_query(array('postvar1' => 'value1')));

    // Receive server response ...
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $server_output = curl_exec($ch);
    echo $server_output;
    curl_close($ch);
}

if (isset($_REQUEST['getdata'])) {
    // From URL to get webpage contents.
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $GLOBALS['apipath']);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $_REQUEST);

    // Receive server response ...
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $data = curl_exec($ch);
    curl_close($ch);
    if(isset($_REQUEST['singledate'])){
        $date = json_decode($data, true);
        $date = json_decode($date['rdetail'],true);
        $date = json_encode( $date[$_REQUEST['singledate']]);
        print_r($date);
        die();
    }
    print_r($data);
}
