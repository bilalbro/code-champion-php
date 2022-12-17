<?php

include_once "../lib/authenticate.php";
include_once "../lib/admin_database.php";

$db = new AdminDatabase();

authenticate();

// delete the given user
$db->deleteRecord($_POST["index"]);
header("Location: /admin/home.php", true, 303);

?>