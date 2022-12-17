<?php

class Database {
   private $isConnected = false;
   private $mysqli;
   
   public function addRecord($userInput) {
      $name = $userInput["name"];
      $email = $userInput["email"];
      $competitions = join(",", $userInput["competitions"]);

      $res = $this->query("INSERT INTO signups(name, email, competitions)
         VALUES('$name', '$email', '$competitions');");
   }

   public function query($query) {
      // make connection if there is no prior connection
      $this->connect();

      // run the given query
      $res = $this->mysqli->query($query);

      if (is_bool($res)) {
         return $res;
      }

      // return the array
      return $res->fetch_all();
   }

   public function deleteRecord($id) {
      $this->query("DELETE FROM signups WHERE id=$id");
   }

   public function updateRecord($userInput) {
      $name = $userInput["name"];
      $email = $userInput["email"];
      $competitions = join(",", $userInput["competitions"]);
      $index = $userInput["index"];

      $this->query("UPDATE signups
                    SET name='$name', email='$email', competitions='$competitions'
                    WHERE id=$index");
   }

   public function existsRecord($email) {
      $res = $this->query("SELECT COUNT(*) FROM signups WHERE email='$email';");
      return $res[0][0];
   }

   public function getAllRecords() {
      $res = $this->query("SELECT name, email, competitions, id FROM signups ORDER BY id DESC");
      return $res;
   }

   public function connect() {
      if (!($this->isConnected)) {
         $this->isConnected = true;
         $this->mysqli = mysqli_connect("localhost", "root", "Global123503014589?", "p1");

         if ($this->mysqli->connect_errno) {
            die("Can't connect to database.");
         }
      }
   }

   public function disconnect() {
      $this->mysqli->close();
      $this->mysqli = null;
   }
}

?>