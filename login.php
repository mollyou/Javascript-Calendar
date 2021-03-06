<?php
  require 'database.php';
  header("Content-Type: application/json");
  
    // Much of the following code is taken from the course wiki and modified slightly
    $stmt = $mysqli->prepare("SELECT COUNT(*), username, password, user_id FROM users WHERE username=?");
    if(!$stmt){
  	  printf("Query Prep Failed: %s\n", $mysqli->error);
  	  exit;
    }
    $user = (String)$_POST['username'];
    $stmt->bind_param('s', $user);
    $stmt->execute();
    $stmt->bind_result($cnt, $username, $pwd_hash, $user_id);
    $stmt->fetch();

    $pwd_guess = (String)$_POST['password'];
    // Make sure that the passwords match and there is only one user that matches
    // the given username
    if($cnt == 1 && password_verify($pwd_guess, $pwd_hash)){
      // Login succeeded!
      ini_set("session.cookie_httponly", 1);
      session_start();
      $_SESSION['username'] = $username;
      // Create token for CSRF
      $_SESSION['token'] = substr(md5(rand()), 0, 10);
      $_SESSION['user_id'] = $user_id;

      echo json_encode(array(
        "success" => true,
        "token" => $_SESSION['token'],
        "id" => $user_id,

      ));
      exit;
    }
    else{
      echo json_encode(array(
        "success" => false,
        "message" => "Incorrect Username or Password"
      ));
      exit;
    }
//}
