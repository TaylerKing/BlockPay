<?php
class BlockPay {
  public  $_callback,
          $_addr,
          $_apikey,
          $_data,
          $_error;
  public function __construct($callback, $addr, $key = null) {
    $this->_callback = $callback;
    $this->_apikey = $key;
    if(!Validation::valid($addr))
      $this->_error = true;
    else {
      $this->_addr = $addr;
      $this->_error = false;
    }
  }
  public function getAddr($data = null) {
    $this->_error = false;
    $callback = urlencode($this->_callback . (is_null($data) ? "" : $data));
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
      $resp = json_decode($resp);
      $this->_data = [$resp->input_address, $resp->destination, urldecode($callback)];
    }
  }
  public function getValue($amount) {
    $this->_error = false;
    $resp = file_get_contents("https://www.bitstamp.net/api/ticker/");
    if($resp === false)
      return false;
    else {
      $resp = json_decode($resp);
      return (1/$resp->vwap) * $amount;
    }
  }
}
?>