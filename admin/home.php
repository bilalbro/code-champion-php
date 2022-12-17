<?php

include_once "../lib/authenticate.php";
include_once "../lib/admin_database.php";

$db = new AdminDatabase();

authenticate();

$adminUsername = $_SESSION["username"];

$signups = $db->getAllRecords();

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Project 1</title>

   <?php include "../includes/css.php"; ?>
</head>

<body class="bg-light p-md-5">
   <div class="container p-4" style="max-width: 800px;">
      <div class="row align-items-center">
         <div class="col-8">
            <h1>CodeChampion 2022</h1>
            <h3>Admin Portal</h3>
         </div>
         <div class="col text-end">
            <div class="row">
               <div>@<?php echo $adminUsername; ?> </div>
            </div>
            <div class="row">
               <a href="signout.php">Sign out</a>
            </div>
         </div>
      </div>

      <div class="modal fade" id="modal1">
         <div class="modal-dialog">
            <div class="modal-content">
               <div class="modal-header">
                  <h3>Update information</h3>
               </div>
               <div class="modal-body">
                  <form action="update.php" method="post" id="update-form">
                     <div class="form-group mt-4">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" value="" class="form-control">
                     </div>
                     <div class="form-group mt-4">
                        <label class="form-label">Email</label>
                        <input type="text" name="email" value="" class="form-control">
                     </div>
                     <div class="form-group mt-4">
                        <div class="form-label">Competitions:</div>
                        <div class="form-group">
                           <input class="input-checkbox" type="checkbox" id="i1" value="Python" name="competitions[]">
                           <label for="i1">Python</label>
                        </div>
                        <div class="form-group">
                           <input class="input-checkbox" type="checkbox" id="i2" value="C++" name="competitions[]">
                           <label for="i2">C++</label>
                        </div>
                        <div class="form-group">
                           <input class="input-checkbox" type="checkbox" id="i3" value="Java" name="competitions[]">
                           <label for="i3">Java</label>
                        </div>
                     </div>
                     <input type="hidden" name="index" value="">
                  </form>
               </div>
               <div class="modal-footer">
                  <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button class="btn btn-warning" form="update-form">Submit</button>
               </div>
            </div>
         </div>
      </div>

      <form action="del.php" method="post" id="deletion-form"></form>

      <div class="modal fade" id="modal2">
         <div class="modal-dialog">
            <div class="modal-content">
               <div class="modal-header"><h3 class="modal-title">Delete</h3></div>
               <div class="modal-body">Are you sure you want to delete the given entry?</div>
               <div class="modal-footer">
                  <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button class="btn btn-danger" name="index" form="deletion-form" value="" id="del-button">Delete</button>
               </div>
            </div>
         </div>
      </div>

      <table class="table table-striped table-bordered mt-5">
         <tr><th>#</th><th>Name</th><th>Email</th><th>Competitions</th><th>Actions</th></tr>
         <?php $i = 0; foreach ($signups as $signup) {
            $i++;
            $name = $signup[0];
            $email = $signup[1];
            $competitions = $signup[2];
            $index = $signup[3];

            echo <<<END
            <tr><td>$i</td><td>$name</td><td>$email</td><td>$competitions</td><td><button class="init-delete-button btn btn-sm btn-danger rounded-pill" value="$index" data-bs-toggle="modal" data-bs-target="#modal2">Delete</button><button class="btn btn-warning rounded-pill btn-sm ms-1 init-update-button" data-bs-toggle="modal" data-bs-target="#modal1">Update</button></td></tr>
            END;
         } ?>
      </table>
   </div>
   <script>
      var deletionForm = document.forms[1];
      var recordUpdateForm = document.forms[0];
      window.addEventListener('click', function(e) {
         if (e.target.classList.contains('init-update-button')) {
            recordUpdateForm.reset();

            var tr = e.target.parentNode.parentNode;

            // update name
            recordUpdateForm[0].value = tr.childNodes[1].innerText;

            // update email
            recordUpdateForm[1].value = tr.childNodes[2].innerText;

            // update competitions
            var competitionsList = tr.childNodes[3].innerText.split(',');
            for (var checkbox of recordUpdateForm["competitions[]"]) {
               if (competitionsList.includes(checkbox.value)) {
                  checkbox.checked = true;
               }
            }

            // update index
            recordUpdateForm.index.value = tr.childNodes[4].childNodes[0].value;
            console.log(recordUpdateForm.index.value);
         }

         else if (e.target.classList.contains('init-delete-button')) {
            document.getElementById('del-button').value = e.target.value;
         }
      });
   </script>
</body>
</html>