<?php
    session_start();

    if (isset($_GET['functionToCall']) && function_exists($_GET['functionToCall'])) {
          call_user_func($_GET['functionToCall']);
        }



    function getUserInfo()  
        {

        $con = mysqli_connect('localhost','root','');
        mysqli_select_db($con,"ekatanalotis");
        $sql="SELECT * FROM user WHERE username like '".$_SESSION['username']."'"; 
        $result = mysqli_query($con,$sql);
          
        echo "<table id=user_info_table>
        <tr>
        <th>Username</th>
        <th>Κωδικός</th>
        <th>Email</th>
        <th>Συνολικό σκορ</th>
        <th>Μηνιαίο σκορ</th>
        <th>Συνολικά tokens</th>
        <th>Μηνιαία tokens</th>
        <th>Ημερομηνία εγγραφής</th>
        <tr>";

        while($row = mysqli_fetch_array($result)) {
            echo "<td>" . $row['username'] . "</td>";
            echo "<td>" . $row['password'] . "</td>";
            echo "<td>" . $row['email'] . "</td>";
            echo "<td>" . $row['t_score'] ."</td>";
            echo "<td>" . $row['m_score'] ."</td>";
            echo "<td>" . $row['t_tokens'] ."</td>";
            echo "<td>" . $row['m_tokens'] ."</td>";
            echo "<td>" . $row['signup_date'] ."</td>";
            
          echo "</tr>";
        }
        echo "</table>";
          
        mysqli_close($con);
        }     


    function getUserLDHistory(){
      
      $con = mysqli_connect('localhost','root','');
      mysqli_select_db($con,"ekatanalotis");
      $sql="SELECT type, offer_id, datetime FROM ldhistory WHERE username like '".$_SESSION['username']."'"; 
      $result = mysqli_query($con,$sql);
        
      echo "<table id=user_ldhistory_table>
      <tr>
      <th>Τύπος</th>
      <th>ID Προσφοράς</th>
      <th>Ημερομηνία</th>
      <tr>";

      while($row = mysqli_fetch_array($result)) {
          echo "<td>" . $row['type'] . "</td>";
          echo "<td>" . $row['offer_id'] . "</td>";
          echo "<td>" . $row['datetime'] ."</td>";   
        echo "</tr>";
      }
      echo "</table>";
        
      mysqli_close($con);
    }      


    function getUserOfferHistory(){
      
      $con = mysqli_connect('localhost','root','');
      mysqli_select_db($con,"ekatanalotis");
      $sql="SELECT * FROM offers WHERE username like '".$_SESSION['username']."'"; 
      $result = mysqli_query($con,$sql);
        
      echo "<table id=user_offerhistory_table>
      <tr>
      <th>ID προϊόντος</th>
      <th>Likes</th>
      <th>Dislikes</th>
      <th>Τιμή</th>
      <th>Πλήρωση κριτηρίων</th>
      <th>Ημερομηνία υποβολής</th>
      <th>Ημερομηνία λήξης</th>
      <th>ID POI</th>
      <th>Stock</th>
      <tr>";

      while($row = mysqli_fetch_array($result)) {
          echo "<td>" . $row['p_id'] . "</td>";
          echo "<td>" . $row['lcount'] ."</td>"; 
          echo "<td>" . $row['dcount'] ."</td>"; 
          echo "<td>" . $row['price'] . "</td>";

          if($row['ful_criteria'] == "yes") echo "<td>&#10003</td>";
          else if ($row['ful_criteria'] == "no") echo "<td></td>";
            
          echo "<td>" . $row['sub_date'] ."</td>";  
          echo "<td>" . $row['exp_date'] ."</td>";   
          echo "<td>" . $row['poi_id'] ."</td>";  
          echo "<td>" . $row['stock'] ."</td>";   
        echo "</tr>";
      }
      echo "</table>";
        
      mysqli_close($con);
    }      


    function changeUsername(){

      $new_username = file_get_contents("php://input");
      
      $con = mysqli_connect('localhost','root','');
      mysqli_select_db($con,"ekatanalotis");
      $sql="SELECT * FROM user WHERE username like '".$new_username."'"; 
      $result = mysqli_query($con,$sql);
        
      $count = 0;
      while($row = mysqli_fetch_array($result)) {
          $count++;}

      mysqli_close($con);    
      
      if($count == 0){
        
        $host = "localhost";
        $dbname = "ekatanalotis";
        $con_username = "root";
        $con_password = "";

        $conn = new PDO("mysql:host=$host;dbname=$dbname",$con_username,$con_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $current_error_mode = $conn->getAttribute(PDO::ATTR_ERRMODE);

        $stmt = $conn->prepare("UPDATE user SET username=? WHERE username like ?");
        $stmt->execute([$new_username, $_SESSION['username']]);

        $_SESSION['username'] = $new_username;
        echo "Επιτυχής αλλαγή!";
      } 
      else echo "Υπάρχει ήδη χρήστης με αυτό το username!";
        
    }   


    function changePassword(){

      $new_password = file_get_contents("php://input");

      $check = 1;
      if(strlen($new_password) < 8 || !preg_match('/[A-Z]/', $new_password) || !preg_match('/[0-9]/', $new_password) 
        || !preg_match('/[@_!.,#$%^&*()<>?|}{~:]/',$new_password))  $check=0;
        
      if($check == 1){
        
        $host = "localhost";
        $dbname = "ekatanalotis";
        $con_username = "root";
        $con_password = "";

        $conn = new PDO("mysql:host=$host;dbname=$dbname",$con_username,$con_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $current_error_mode = $conn->getAttribute(PDO::ATTR_ERRMODE);

        $stmt = $conn->prepare("UPDATE user SET password=? WHERE username like ?");
        $stmt->execute([$new_password, $_SESSION['username']]);

        echo "Επιτυχής αλλαγή!";
      } 
      else echo "Ο κωδικός δεν πληροί τα κριτήρια!";
    }  