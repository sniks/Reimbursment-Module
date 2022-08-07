<?php

$conn = new mysqli('localhost', 'nihal', 'Nihal@1234', 'reimbursment') or die("Could not connect to mysql" . mysqli_error($con));

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

if (isset($_REQUEST['submit'])) {
    // From URL to get webpage contents.
    unset($_REQUEST['submit']);
    $data = $_REQUEST['inputs'];
    $rtype = $data['rtype'];
    $date = $data['month'];
    unset($data['rtype']);
    unset($data['month']);
    $check = $conn->query("SELECT * from alldata Where date = '$date' ");
    if ($check->num_rows < 1) {
        $stmt = $conn->prepare("INSERT INTO alldata (rtype, rdetail,date) VALUES (?,?,?)");
    } else {
        $stmt = $conn->prepare("UPDATE alldata SET rtype=?, rdetail=?  WHERE date=?");
    }
    $stmt->bind_param("sss", $rtype, $rdetail, $date);

    $i = 0;
    foreach($data as $key=>$val){
        
        // Get extension
        $ext = strtolower(pathinfo($_REQUEST['files'][$i]['name'], PATHINFO_EXTENSION));
        $file =  file_get_contents($_REQUEST['files'][$i]['name']);
        $tmpname = generateRandomString().$ext;
        $path = file_put_contents("uploads/".$tmpname,$file);
        $data[$key]['attachment'] = $tmpname;
    }
    $rdetail = json_encode($data);
    if($stmt->execute()){
        printf("Record inserted successfully.<br />");
    }
    $stmt->close();

    if ($conn->errno) {
        printf("Could not insert record into table: %s<br />", $conn->error);
    }
    $conn->close();
}

if (isset($_REQUEST['getdata'])) {

    $date = $_REQUEST['date'];

    $result = $conn->query("SELECT * from alldata Where date = '$date' ");
    print_r(json_encode($result->fetch_assoc()));
    if ($conn->errno) {
        printf("Could not find record into table: %s<br />", $conn->error);
    }
    $conn->close();
}
