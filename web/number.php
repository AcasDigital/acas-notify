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
try {
  require('sites/default/settings.php');
  $dbh = new PDO('mysql:host=' . $databases['default']['default']['host'] . ';dbname=numbers', $databases['default']['default']['username'], $databases['default']['default']['password']);
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
  $sql = "UPDATE numbers SET individual_no = ?, group_no = ?, ecx_no = ? WHERE service = ?";
  $stmt = $dbh->prepare($sql);
  foreach($save as $key => $value) {
    $stmt->execute([$value['individual'], $value['group'], $value['ecx'], $key]);
  }
  die('ok');
}

// Handle getting a number.
$service = @$_GET['s'];
$type = @$_GET['t']; // individual, ecx or group

if (!$type || !$service) {
  die('<h1>Acas Notification Reference Number Service</h1><h2>Missing parameters! Usage:</h2><p>s = service</p><p>t = type (individual or group)</p><p>Example: https://tell.acas.org.uk/number.php?s=dev-tell.acas.org.uk&t=individual</p>');
}
if (strpos($service, '.acas.org.uk') === FALSE) {
  // A developers environment
  $service = 'dev-tell.acas.org.uk';
}
$dbh->beginTransaction();
$dbh->exec('LOCK TABLES numbers');
$number = (int) $dbh->query("SELECT " . $type . "_no FROM numbers WHERE service = '$service'")->fetchColumn();
if (@$_GET['n']) {
  $dbh->commit();
  $dbh->exec('UNLOCK TABLES');
  // Return the current number;
  die((string) $number);
}
$number++;
$sql = "UPDATE numbers SET " . $type . "_no = ? WHERE service = ?";
$stmt = $dbh->prepare($sql);
$stmt->execute([$number, $service]);
$dbh->commit();
$dbh->exec('UNLOCK TABLES');
if ($type == 'individual') {
  $return = 'R' . $number . '/' . date('y');
}
elseif ($type == 'group') {
  $return = 'MU' . $number . '/' . date('y');
}
else {
  $return = 'NE' . $number . '/' . date('y');
}
die($return);
?>