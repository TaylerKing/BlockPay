<?php
// Set the header for blockchain to verify the response.
header("Content-Type:text/plain");
require_once("module/init.php");

// We get the post details and log them to a file, although you can (and SHOULD) remove this.
$post = json_encode($_GET);
file_put_contents('callback.txt', $post . "\nValidation: " . (isset($_SERVER['HTTP_X_FORWARDED_FOR'])
                                                              ? $_SERVER['HTTP_X_FORWARDED_FOR'] :
                                                                $_SERVER['REMOTE_ADDR']) . "\n", FILE_APPEND | LOCK_EX);

// We now decode the json string. If it's a test transaction, we ignore it. We then update the database, and everything is complete.
$post = json_decode($post);
if(isset($post->test))
  die("Sorry, but this was a test transaction.");
$sql = KondoSQL::instance();
$hash = explode('|', $post->hash)[0];
$sql->update("temp", array("hash", "=", "'{$hash}'"), array(
  "paid" => $post->value,
  "txd" => $post->transaction_hash
  ));

// This is required so blockchain doesn't keep on sending the transaction to the callback url.
die("*ok*");
?>