<?php
// Require the initiation file. Manages class loading and is expandable.
require_once "module/init.php";

// Create an instance of the class. Validate the bitcoin address provided.
$blockpay = new BlockPay("http://***.***.***.***/BlockPay/example/callback.php?hash=", "15ks4PWRUyTqk89szb9q5baji8Y6pTxtYB", "ffa57c77-7921-4fc6-bfbb-d134b57c5c7b");
if($blockpay->_error)
  die("Sorry, we are unable to complete your payment at this point of time. It seems as though our address is invalid; please contact "
   ."the site administrator for more information.");
else {
  // It seems as though the address is valid. We now continue to generate the address.
  $hash = hash('sha256', uniqid());
  $time = time();
  $blockpay->getAddr($hash . "|" . $time);

  if($blockpay->_error) {
    // Blockchain has most likely declined the request. If you don't provide an api key, the amount of requests are limited.
    die("Sorry, we are unable to complete your payment at this point of time. It seems as though our address is invalid; please contact "
     ."the site administrator for more information.");
  } else {
    // We will now store the two addresses and callback into a variable.
    $data = $blockpay->_data;

    // Our product costs $10. The follow function converts $10 USD to BTC and stores it in a variable.
    $amount = $blockpay->getValue(10);

    // We're initiating a database connection.
    $sql = KondoSQL::instance();

    /*
     * We're now inserting the transaction data into our database.
     *
     * CREATE TABLE `temp` ( `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY , `address` VARCHAR(40) NOT NULL , 
     * `amount` INT NOT NULL , `paid` INT NOT NULL , `confirmed` INT NOT NULL DEFAULT '0' , `txd` TEXT NOT NULL , 
     * `hash` VARCHAR(255) NOT NULL , `time` INT NOT NULL ) ENGINE = InnoDB;
     */
    $sql->insert("temp", array(
      "address" => $data[0], 
      "amount" => $amount * 100000000, 
      "paid" => 0,
      "confirmed" => 0, 
      "txd" => "null",
      "hash" => $hash,
      "time" => $time));

    // We're finally finished with creating the address. Let's notify the user.
    echo "Hello, and welcome to our shop. Please send " . $amount . " to " . $data[0];
  }
}
?>