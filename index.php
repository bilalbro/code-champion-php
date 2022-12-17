<?php

include_once "lib/form.php";
include_once "lib/database.php";

$db = new Database();


$error = false;

// If something is there in the $_POST superglobal, that means that this page
// was requested as part of a POST request.
// Hence, process that POST payload.
if (count($_POST) !== 0) {

   $form = new Form();

   $form->setSchema([
      "name" => "required",
      "email" => [
         "type" => "email",
         "fx" => function($email) {
            // check whether $email is already in database or not

            global $db;
            $emailExists = $db->existsRecord($email);

            if ($emailExists) {
               return 'Email already exists';
            }

            return true;
         }
      ],
      "competitions" => "required"
   ]);

   $keyValueMap = [];
   $keyValueMap["name"] = isset($_POST["name"]) ? $_POST["name"] : "";
   $keyValueMap["email"] = isset($_POST["email"]) ? $_POST["email"] : "";
   $keyValueMap["competitions"] = isset($_POST["competitions"]) ? $_POST["competitions"] : "";

   $isValid = $form->validate($keyValueMap, $validatedValues, $errorMessageList);

   if (!$isValid) {
      $error = true;
   }

   else {
      $db->addRecord($validatedValues);
      header("Location: success.php", true, 303);
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Document</title>

   <?php include('./includes/css.php'); ?>
</head>

<body class="bg-light p-5 mb-5">
   <div class="container mt-5" style="max-width: 500px;">
      <h1>Code Competition 2022</h1>
      <hr>
      <p>Hello and welcome to CodeChampion 2022!</p>
      <p>There are 3 different competitions for three of the most popular programming languages in the world right now: <b>Python</b>, <b>C++</b>, <b>Java</b>.</p>
      <p>If you are interested in taking part in this competition and getting informed about each of the latest updates in CodeChampion 2022, please sign up using the form below.</p>
      
      <div class="container-fluid bg-white p-4 mt-5 shadow rounded-2">
         <h5>Sign up</h5>
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
               <div class="form-label">Name</div>
               <input type="text" value="<?php echo isset($_POST["name"]) ? $_POST["name"] : ""?>" class="form-control" name="name" autocomplete="off">
            </div>
            <div class="form-group mt-4">
               <div class="form-label">Email</div>
               <div class="input-group">
                  <div class="input-group-text">@</div>
                  <input type="text" class="form-control" value="<?php echo isset($_POST["email"]) ? $_POST["email"] : ""?>" name="email" autocomplete="off">
               </div>
            </div>
            <div class="form-group mt-4">
               <div class="form-label">Programming languages:</div>
               <div class="form-check">
                  <input type="checkbox" class="form-check-input" <?php echo (isset($_POST["competitions"]) and in_array("Python", $_POST["competitions"])) ? "checked" : ""?> name="competitions[]" value="Python" id="p1">
                  <label for="p1" class="form-check-label">Python</label>
               </div>
               <div class="form-check">
                  <input type="checkbox" class="form-check-input" <?php echo (isset($_POST["competitions"]) and in_array("Java", $_POST["competitions"])) ? "checked" : ""?> name="competitions[]" value="Java" id="p2">
                  <label for="p2" class="form-check-label">Java</label>
               </div>
               <div class="form-check">
                  <input type="checkbox" class="form-check-input" <?php echo (isset($_POST["competitions"]) and in_array("C++", $_POST["competitions"])) ? "checked" : ""?> name="competitions[]" value="C++" id="p3">
                  <label for="p3" class="form-check-label">C++</label>
               </div>
            </div>
            <div class="form-group mt-5">
               <button class="btn btn-warning">Submit</button>
            </div>
         </form>
      </div>
   </div>
</body>
</html>