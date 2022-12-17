<?php

include_once "database.php";

class AdminDatabase extends Database {
   public function existsAdmin($username, $password) {
      $res = $this->query("SELECT COUNT(*) FROM admin WHERE username='$username'
         AND password='$password'");

      return $res[0][0];
   }
}

?>