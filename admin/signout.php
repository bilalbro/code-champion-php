<?php

include_once "../lib/authenticate.php";

authenticate();

session_destroy();
unset($_SESSION);

authenticate();

?>