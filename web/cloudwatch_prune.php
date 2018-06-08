<?php
$params = unserialize(file_get_contents('/tmp/cloudwatch_prune.txt'));
$distribution = $params['id'];
$access_key = $params['key'];
$epoch = date('U');
$paths = '';
foreach($params['urls'] as $url) {
  $paths .= '<Path>' . $url . '</Path>';
}
$xml = "<InvalidationBatch>$paths<CallerReference>{$distribution}{$epoch}</CallerReference></InvalidationBatch>";
$len = strlen($xml);
$date = gmdate('D, d M Y G:i:s T');
$sig = base64_encode(
  hash_hmac('sha1', $date, $params['secret'], true)
);
$msg = "POST /2010-11-01/distribution/{$distribution}/invalidation HTTP/1.0\r\n";
$msg .= "Host: cloudfront.amazonaws.com\r\n";
$msg .= "Date: {$date}\r\n";
$msg .= "Content-Type: text/xml; charset=UTF-8\r\n";
$msg .= "Authorization: AWS {$access_key}:{$sig}\r\n";
$msg .= "Content-Length: {$len}\r\n\r\n";
$msg .= $xml;
$fp = fsockopen('ssl://cloudfront.amazonaws.com', 443, 
  $errno, $errstr, 30
);
if (!$fp) {
  die("Connection failed: {$errno} {$errstr}\n");
}
fwrite($fp, $msg);
$resp = '';
while(! feof($fp)) {
  $resp .= fgets($fp, 1024);
}
fclose($fp);
  
unlink('/tmp/cloudwatch_prune.txt');
?>