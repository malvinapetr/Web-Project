<?php 
session_start();
if (isset($_GET['functionToCall']) && function_exists($_GET['functionToCall'])) {
  call_user_func($_GET['functionToCall']);
}


function checkLogin()
{
$user_info = json_decode(file_get_contents("php://input"), true);
$username = trim($user_info['username']);
$password = trim($user_info['password']);
$password = str_replace(' ','', $password);
$con_type = $user_info['type'];


$check = 0;

if(!$username || !$password) echo "Username και κωδικός δεν μπορούν να είναι κενά!";          //if username or password is blank
else $check = checkPassword($password);   //check that the password has correct format before accessing the database


if($check){          //if password has correct format then start accessing the database
  try{    
    //connection variables
    $host = "localhost";
    $dbname = "ekatanalotis";
    $con_username = "root";
    $con_password = "";

    $conn = mysqli_connect($host, $con_username, $con_password, $dbname);

        $sql = "SELECT username, password,type FROM user WHERE username = '$username'";
        $result = $conn->query($sql);

        if($con_type == "login"){            //case = login
          $fetched = $result->fetch_assoc();

          if(mysqli_num_rows($result) == 0 || $fetched["password"] != $password) {     //if username not in the database
            header("Refresh:0; url=Login.html");    
            echo "Λάθος username ή κωδικός!";
          }
          else{
            $_SESSION['username'] = $username;
            if($fetched["type"] == "user") {
              echo "Σύνδεση χρήστη";
              $_SESSION['user_type'] = 'user';}
            else {
              echo "Σύνδεση διαχειριστή";
              $_SESSION['user_type'] = 'admin';}
          }
        }
        else if ($con_type == "signup") {                              //case = sign-up
        
        $email = trim($user_info['email']);
        $email = str_replace(' ','', $email);

        $check2 = 0;
        
        if(!$email) echo "Δεν δόθηκε email!";          //if username or password is blank
        else $check2 = checkEmail($email);   //check that the password has correct format before accessing the database


        if($check2)  //if email has correct format then start accessing the database
        {  if(mysqli_num_rows($result) == 0){          // if no other user with the same username check the email

            $sql = "SELECT * FROM user WHERE email = '$email'";
            $result = $conn->query($sql);

                if(mysqli_num_rows($result) == 0)    //if no other user has the same email the sign-up is successful
                  {$sql = "INSERT INTO user (username, password,email, type,t_score,m_score,t_tokens,m_tokens,signup_date)
                  VALUES (?,?,?,?,?,?,?,?,?)";
                  $stmt = mysqli_stmt_init($conn);
                  mysqli_stmt_prepare($stmt,$sql);
                  $user_type = "user";
                  $zero = 0;
                  $date = date("Y-m-d");
                  mysqli_stmt_bind_param($stmt,"ssssiiiis",$username,$password,$email,$user_type,$zero,$zero,$zero,$zero,$date);
                  mysqli_stmt_execute($stmt);

                  $_SESSION['username'] = $username;
                  $_SESSION['user_type'] = 'user';
                  echo "Επιτυχία";}
                else  echo "Υπάρχει ήδη χρήστης με αυτό το email!";
            }
          else  echo "Υπάρχει ήδη χρήστης με αυτό το username!";}
    
        }
    }
  //catch exception
  catch(Exception $e) {
      echo 'Message: ' .$e->getMessage();
    }
  finally{
    mysqli_close($conn);
  }  
}}

//check password format
function checkPassword($password){
    if(strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password) 
    || !preg_match('/[@_!.,#$%^&*()<>?|}{~:]/',$password)) {
        echo "Δεν δόθηκε έγκυρος κωδικός!";
        return 0;
    }
return 1;    
}

//check email format
function checkEmail($email){
  if(!preg_match('/^[a-zA-Z0-9\_.]+@email.gr/',$email)) {
      echo "Δεν δόθηκε έγκυρο email!";
      return 0;
  }
return 1;    
}

//getter function used for getting the user's username in .js files
function getUsername(){
  if(isset($_SESSION['username'])) echo $_SESSION['username'];
  else echo '';
}


//getter function used for getting the user's type in .js files
function getUserType(){
  if(isset($_SESSION['user_type'])) echo $_SESSION['user_type'];
  else echo '';
}






