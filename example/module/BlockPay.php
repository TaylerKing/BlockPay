<?php
class BlockPay {
  public  $_callback,
          $_addr,
          $_apikey,
          $_data,
          $_error;
  /*
   * Callback; Callback url for blockchain
   * Addr; Address for receiving transactions
   * Key; Blockchain api key
   */
  public function __construct($callback, $addr, $key = null) {
    // Setting the callback url for blockchain.
    $this->_callback = $callback;

    // Here we set the apikey. It doesn't matter if it isn't provided, it will still work.
    $this->_apikey = $key;

    // Validate the bitcoin address. See https://github.com/LinusU/php-bitcoin-address-validator for more information.
    if(!Validation::valid($addr))
      $this->_error = true;
    else {
      $this->_addr = $addr;
      $this->_error = false;
    }
  }
  /*
   * Data; Any verification data you wish to pass
   */
  public function getAddr($data = null) {
    // Resetting any errors. There shouldn't be any errors, but if somebody fudges it up, this will fix it.
    $this->_error = false;

    // Here we create the callback url. If additional data is provided, it shall be added to the original url.
    $callback = urlencode($this->_callback . (is_null($data) ? "" : $data));
 
    // We now get an address. If this fails, the error is set to true. If not, we continue.
    $resp = file_get_contents("https://blockchain.info/api/receive?"
      . "method=create&"
      . "address=" . $this->_addr . "&"
      . "anonymous=false&"
      . "callback=" . $callback
      . (is_null($this->_apikey) ? "" : "&api_key=" . $this->_apikey)
      );
    if($resp === false) 
      $this->_error = true;
    else {
      // We shall decode the response into a json object.
      $resp = json_decode($resp);

      /* 
       * We shall put the data into an array - then into our $_data variable.
       * Instead of decoding the string into an array, I like to keep it clean and minimal.
       */
      $this->_data = [$resp->input_address, $resp->destination, urldecode($callback)];
    }
  }
  /*
   * Amount; The amount (in USD) of bitcoin you want
   */
  public function getValue($amount) {
    // Resetting any errors. There shouldn't be any errors, but if somebody fudges it up, this will fix it.
    $this->_error = false;

    // This gets the AVERAGE bitcoin exchange rate (in USD) for the past twenty-four hours.
    $resp = file_get_contents("https://www.bitstamp.net/api/ticker/");
    if($resp === false)
      return false;
    else {
      // We shall decode the response into a json object.
      $resp = json_decode($resp);

      // Return the amount in bitcoin.
      return (1/$resp->vwap) * $amount;
    }
  }
}
?>