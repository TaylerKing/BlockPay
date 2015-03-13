<?php
  // We're just setting the configuration for KondoSQL.php
  const DB = ["localhost", "root", "", "pdo"];
  /*
   * This function registers classes. If, for say, you set the variable database
   * to new Database(Database::PDO, "localhost", "root", ""), this function will
   * call require_once [Directory Of File][\\][Database][.php]; Quick and Easy..
   */
  spl_autoload_register(function($a) {
    require_once dirname(__FILE__) . "\\" . $a . ".php";
  });
?>