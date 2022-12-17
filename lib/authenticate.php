<?php

session_start([ "cookie_lifetime" => 86400 ]);

// Authenticate the user

function authenticate() {
   if (!isset($_SESSION["username"])) {
      header("Location: /admin/", true, 303);
      exit();
   }
}

function promote() {
   if (isset($_SESSION["username"])) {
      header("Location: /admin/home.php", true, 303);
      exit();
   }
}

?>