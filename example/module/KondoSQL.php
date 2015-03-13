<?php
  class KondoSQL {
    private static $_instance = null;
    private $_pdo,
    $_query,
    $_error = false,
    $_results,
    $_count = 0;
    private function __construct() {
      try {
        $this->_pdo = new PDO('mysql:host=' . DB[0] . ';dbname=' . DB[3], DB[1], DB[2]/*, array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION)*/);
        $this->_success = true;
      } catch (PDOExceptioin $e) {
        $this->_success = false;
      }
    }
    public static function instance(){
      if(is_null(self::$_instance)) {
        self::$_instance = new self;
      }
      return self::$_instance;
    }
    public function query($sql, $args = array(), $type = 3) {
      if($this->_query = $this->_pdo->prepare($sql)) {
        $x = 1;
        if(count($args)) {
          foreach($args as $arg) {
            $this->_query->bindValue($x, $arg);
            $x++;
          }
        }
        if($this->_query->execute()) {
          if($type == 3) {
            $this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
            $this->_count = $this->_query->rowCount();
          }
        } else {
          $this->_error = true;
        }
      }
      return $this;
    }
    public function action($action, $table, $where = array()) {
      if(count($where) === 3) {
        $operators = array('=', '>', '<', '>=', '<=');
        $field = $where[0];
        $operator = $where[1];
        $value = $where[2];
        if(in_array($operator, $operators)) {
          $sql = "{$action} FROM {$table} WHERE {$field} {$operator} ?";
          if(!$this->query($sql, array($value), 3)->error()) {
            return $this;
          } else $this->_error = true;
        }
      }
      return false;
    }
    public function insert($table, $args = array()) {
      if(count($args)) {
        $keys = array_keys($args);
        $values = '';
        $x = 1;
        foreach($args as $arg) {
          $values .= "?";
          if($x < count($args)) {
            $values .= ', ';
          }
          $x++;
        }
        $sql = "INSERT INTO {$table} (`" . implode('`,`', $keys) . "`) VALUES ({$values})";
        if(!$this->query($sql, $args, 1)->error()) {
          return true;
        }
      }
      return false;
    }
    public function update($table, $where = array(), $args) {
      if(count($where) === 3) {
        $operators = array('=', '>', '<', '>=', '<=');
        $field = $where[0];
        $operator = $where[1];
        $value = $where[2];
        if(in_array($operator, $operators)) {
          $set = '';
          $x = 1;
          $ark = array();
          foreach($args as $arg => $vark) {
            array_push($ark, $vark);
            $set .= "`{$arg}` = ?";
            if($x < count($args)) {
              $set .= ', ';
            }
            $x++;
          }
          $sql = "UPDATE {$table} SET {$set} WHERE {$field} {$operator} {$value}";
          if(!$this->query($sql, $args, 1)->error()) {
            return $this;
          } else echo $sql;
        } else echo "no";
      } else echo "no";
      return false;
    }
    public function get($table, $where) {
      return $this->action("SELECT *", $table, $where);
    }
    public function delete($table, $where) {
      return $this->action("DELETE", $table, $where);
    }
    public function error() {
      return $this->_error;
    }
    public function count() {
      return $this->_count;
    }
    public function results() {
      if($this->_count >= 1) {
        return $this->_results;
      } else return false;
    }
    public function first() {
      if($this->_count >= 1) {
        return $this->_results[0];
      } else return false;
    }
  }
  ?>