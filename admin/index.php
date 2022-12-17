<?php

include_once "../lib/authenticate.php";
include_once "../lib/form.php";
include_once "../lib/admin_database.php";

// if the user is authenticated, no need to do all this thing
// just promote him/her to admin-home.php
promote();

$db = new AdminDatabase();

$error = false;


// If page was POSTed, we must process the payload.
if (count($_POST) !== 0) {
   $form = new Form();

   $form->setSchema([
      "username" => "required",
      "password" => [
         "trim" => false
      ]
   ]);

   $form->setValidationFx(["username", "password"], function($username, $password) {
      global $db;
      $adminExists = $db->existsAdmin($username, $password);

      if (!$adminExists) {
         return "Username or password is incorrect";
      }

      return true;
   });


   $isValid = $form->validate([
      "username" => isset($_POST["username"]) ? $_POST["username"] : "",
      "password" => isset($_POST["password"]) ? $_POST["password"] : "",
   ], $validatedValues, $errorMessageList);


   if (!$isValid) {
      $error = true;
   }

   else {
      // save the username in a session
      $_SESSION["username"] = $validatedValues["username"];
      promote();
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin - Project 1</title>

   <?php include "../includes/css.php"; ?>
</head>
<body class="bg-light p-md-5">
   <div class="container" style="max-width: 500px;">
      <h1 class="text-center">Code Competition 2022</h1>
      <h3 class="text-center">Admin Panel</h3>
      <div class="container-fluid bg-white shadow rounded-3 p-4 mt-5">
         <h5>Sign in</h5>
         <?php if ($error) { ?>
         <div class="alert alert-danger container-fluid mt-4">
            <h6>Errors:</h6>
            <ul class="mb-0">
               <?php foreach ($errorMessageList as $errorMessage) {
                  echo "<li>$errorMessage</li>";
               }?>
            </ul>
         </div>
         <?php } ?>
         <form action="" method="post">
            <div class="form-group mt-5">
               <label class="form-label">Username</label>
               <input type="text" value="<?php echo isset($_POST["username"]) ? $_POST["username"] : ""?>" class="form-control" name="username" autocomplete="off">
            </div>
            <div class="form-group mt-4">
               <label class="form-label">Password</label>
               <input type="password" class="form-control" name="password">
            </div>
            <div class="form-group mt-5">
               <button class="btn btn-primary">Sign in</button>
            </div>
         </form>
      </div>
   </div>
</body>
</html>