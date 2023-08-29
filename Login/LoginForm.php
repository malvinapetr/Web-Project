<?php 
session_start();
if (isset($_GET['functionToCall']) && function_exists($_GET['functionToCall'])) {
  call_user_func($_GET['functionToCall']);
}

function checkLogin()
{
echo "<body style=background-color:darkblue;>";
if(isset($_POST["login"])) $con_type = "login";   // for log-in
else if(isset($_POST["signup"])) $con_type = "sign up";  //for sign-up
else header("Location: Login.html");   //for attempt to type the url right away, goes back to log-in form

//remove potential space from around the inputs
$username = trim($_POST["username"]);   
$password = trim($_POST["password"]);

//remove potential space from inside the inputs
$username = str_replace(' ','', $username);
$password = str_replace(' ','', $password);

$check = 0;

if(!$username || !$password) {              //if username or password is blank
    header("Refresh:0; url=Login.html");   
    echo "<script>alert('Κανένα πεδίο δεν μπορεί να είναι κενό!')</script>";  
    }
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
            echo "<script>alert('Λάθος username ή κωδικός!')</script>";
          }
          else{
            $_SESSION['username'] = $username;
            if($fetched["type"] == "user") header("Refresh:0; url=UserMain.html");
            else header("Refresh:0; url=AdminMain.html");   
            echo "<script>alert('Επιτυχημένη σύνδεση!')</script>";
          }
        }
        else {                              //case = sign-up
        
        if(mysqli_num_rows($result) == 0){          // if no other user with the same username create user
            $sql = "INSERT INTO user (username, password, type,t_score,m_score,t_tokens,m_tokens,signup_date)
            VALUES (?,?,?,?,?,?,?,?)";
            $stmt = mysqli_stmt_init($conn);
            mysqli_stmt_prepare($stmt,$sql);
            $user_type = "user";
            $zero = 0;
            $date = date("Y-m-d");
            mysqli_stmt_bind_param($stmt,"sssiiiis",$username,$password,$user_type,$zero,$zero,$zero,$zero,$date);
            mysqli_stmt_execute($stmt);

            $_SESSION['usename'] = $username;
            header("Refresh:0; url=UserMain.html");    
            echo "<script>alert('Επιτυχημένη εγγραφή!')</script>";
        }
        else {   //if there is another user with same username
            header("Refresh:0; url=Login.html");    
            echo "<script>alert('Υπάρχει ήδη χρήστης με αυτό το username!')</script>";  
        }
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
        header("Refresh:0; url=Login.html");    
        echo "<script>alert('Δεν δόθηκε έγκυρος κωδικός!')</script>";
        return 0;
    }
return 1;    
}


//getter function used for getting the user's username in .js files
function getUsername(){
  if(isset($_SESSION['username'])) echo $_SESSION['username'];
  else echo '';
}







