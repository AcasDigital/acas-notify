<?php

/**
 * @file
 * Contains the notification reference number service.
 */

// Only allow to run on Prod
if ($_SERVER['HTTP_HOST'] != 'tell.acas.org.uk') {
  die('<h1>Acas Notification Reference Number Service</h1><h2>This service can only be accessed from Production.</h2>');
}

// Connect to the DB.
// The DB connection is via this file located in a secure location
// so no connection user/password in this file (in a public git repository)
if (!file_exists('/home/ubuntu/database.inc')) {
  die('<h1>Acas Notification Reference Number Service</h1><h2>Database include file <i>/home/ubuntu/database.inc</i> not found!</h2>');
}
try {
  require('/home/ubuntu/database.inc');
} catch (PDOException $e) {
  print "Error!: " . $e->getMessage() . "<br/>";
  die();
}

if (@$_GET['data']) {
  // Return all the data as a serialized array.
  $data = $dbh->query("SELECT * FROM numbers")->fetchAll(PDO::FETCH_ASSOC);
  die(serialize($data));
}
if (@$_GET['save']) {
  // Update the DB with new numbers
  $save = unserialize($_GET['save']);
  $sql = "UPDATE numbers SET individual_no = ?, group_no = ? WHERE service = ?";
  $stmt = $dbh->prepare($sql);
  foreach($save as $key => $value) {
    $stmt->execute([$value['individual'], $value['group'], $key]);
  }
  die('ok');
}

// Handle getting a number.
$service = @$_GET['s'];
$type = @$_GET['t']; // individual or group
if (!$type || !$service) {
  die('<h1>Acas Notification Reference Number Service</h1><h2>Missing parameters! Usage:</h2><p>s = service</p><p>t = type (individual or group)</p><p>Example: https://tell.acas.org.uk/number.php?s=dev-tell.acas.org.uk&t=individual</p>');
}
$number = (int) $dbh->query("SELECT " . $type . "_no FROM numbers WHERE service = '$service'")->fetchColumn();
$number++;
$sql = "UPDATE numbers SET " . $type . "_no = ? WHERE service = ?";
$stmt = $dbh->prepare($sql);
$stmt->execute([$number, $service]);
$a = $stmt->errorInfo();
if ($type == 'individual') {
  $return = 'R' . $number . '/' . date('y');
}
else {
  $return = 'MU' . $number . '/' . date('y');
}
die($return);
?>