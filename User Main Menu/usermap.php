<?php
    session_start();
    if (isset($_GET['functionToCall']) && function_exists($_GET['functionToCall'])) {
        call_user_func($_GET['functionToCall']);
      }


function pois_with_offers(){

$data = apcu_fetch('pois_with_offers');

if ($data == FALSE) { //if required data not in cache 
    try{   
        $host = "localhost";
        $dbname = "ekatanalotis";
        $con_username = "root";
        $con_password = "";

        $conn = new PDO("mysql:host=$host;dbname=$dbname",$con_username,$con_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $current_error_mode = $conn->getAttribute(PDO::ATTR_ERRMODE);
         
        date_default_timezone_set("Europe/Athens"); 
        $date = date('Y-m-d');
        //get the ids, names, and coordinates of the POIs with an active offer, as well as the category of the product with the offer
        $sql = "SELECT DISTINCT pois.id,pois.name,latitude,longitude, categories.name FROM pois INNER JOIN offers on pois.id = offers.poi_id
        INNER JOIN products on products.id = offers.p_id INNER JOIN categories on cid = products.category and offers.exp_date > '".$date."'"; 
        $pois_info = $conn->query($sql)->fetchAll();
        
        echo json_encode($pois_info);
        apcu_store('pois_with_offers', $pois_info);
        }
      //catch exception
      catch(Exception $e) {
          echo 'Message: ' .$e->getMessage();
      }
      finally{
        $conn = null;
      }
    }  
  else echo json_encode($data);
}     


function pois_without_offers(){


$data = apcu_fetch('pois_without_offers');

if ($data == FALSE) { //if required data not in cache 
  try{   
      $host = "localhost";
      $dbname = "ekatanalotis";
      $con_username = "root";
      $con_password = "";

      $conn = new PDO("mysql:host=$host;dbname=$dbname",$con_username,$con_password);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $current_error_mode = $conn->getAttribute(PDO::ATTR_ERRMODE);
       
      date_default_timezone_set("Europe/Athens"); 
      $date = date('Y-m-d');
      //get the ids, names, and coordinates of the POIs without an active offer
      $sql = "SELECT DISTINCT pois.id,name,latitude,longitude FROM pois EXCEPT SELECT pois.id,name,latitude,longitude 
      from pois inner join offers on pois.id = offers.poi_id and offers.exp_date > '".$date."'"; 
      $pois_info = $conn->query($sql)->fetchAll();
      echo json_encode($pois_info);

      apcu_store('pois_without_offers', $pois_info);
      }
      
    //catch exception
    catch(Exception $e) {
        echo 'Message: ' .$e->getMessage();
    }
    finally{
      $conn = null;
    }}
  else echo json_encode($data);
}   



function getCategories(){

$data = apcu_fetch('categories');

if ($data == FALSE) { //if required data not in cache
    try{   
        $host = "localhost";
        $dbname = "ekatanalotis";
        $con_username = "root";
        $con_password = "";

        $conn = new PDO("mysql:host=$host;dbname=$dbname",$con_username,$con_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $current_error_mode = $conn->getAttribute(PDO::ATTR_ERRMODE);
        
      //get all the existing categories
        $sql = "SELECT name FROM categories";
        $categories = $conn->query($sql)->fetchAll();
        echo json_encode($categories);

        apcu_store('categories', $categories);
        }
        
      //catch exception
      catch(Exception $e) {
          echo 'Message: ' .$e->getMessage();
      }
      finally{
        $conn = null;
      }}
  else echo json_encode($data);    
}     


function getSubcategories(){

  $cat_name = file_get_contents("php://input");
  $data = apcu_fetch('subcategories-'.$cat_name);

  if ($data == FALSE) { //if required data not in cache
    try{   
        
        $con = mysqli_connect('localhost','root','');
        mysqli_select_db($con,"ekatanalotis");

        //get the cid of the chosen category
        $sql= "SELECT cid FROM categories where name like '".$cat_name."'";
        $result = mysqli_query($con,$sql);
        $cid = null; 

        while($row = mysqli_fetch_array($result)) {
        $cid = $row['cid']; }
        
        mysqli_close($con);

        $host = "localhost";
        $dbname = "ekatanalotis";
        $con_username = "root";
        $con_password = "";

        $conn = new PDO("mysql:host=$host;dbname=$dbname",$con_username,$con_password);
      
        //get the subcategories of the chosen category
        $sql = "SELECT name FROM subcategories where category like '".$cid."'";
        $subcategories = $conn->query($sql)->fetchAll();

        echo json_encode($subcategories);
        apcu_store('subcategories-'.$cat_name, $subcategories);
        }
        
      //catch exception
      catch(Exception $e) {
          echo 'Message: ' .$e->getMessage();
      }
      finally{
        $conn = null;
      }}
  else echo json_encode($data);

}     


function getProducts(){

  $subcat_name = file_get_contents("php://input");
  $data = apcu_fetch('products-'.$subcat_name);

if ($data == FALSE) { //if required data not in cache
  try{   
      $con = mysqli_connect('localhost','root','');
      mysqli_select_db($con,"ekatanalotis");

      //get the cid of the chosen category
      $sql= "SELECT uuid FROM subcategories where name like '".$subcat_name."'";
      $result = mysqli_query($con,$sql);
      $uuid = null; 

      while($row = mysqli_fetch_array($result)) {
       $uuid = $row['uuid']; }

      mysqli_close($con);
      $host = "localhost";
      $dbname = "ekatanalotis";
      $con_username = "root";
      $con_password = "";

      $conn = new PDO("mysql:host=$host;dbname=$dbname",$con_username,$con_password);
    
      //get the subcategories of the chosen category
      $sql = "SELECT name FROM products where subcategory like '".$uuid."'";
      $products = $conn->query($sql)->fetchAll();

      echo json_encode($products);
      apcu_store('products-'.$subcat_name, $products);
    }
    //catch exception
    catch(Exception $e) {
        echo 'Message: ' .$e->getMessage();
    }
    finally{
      $conn = null;
    }}
  else echo json_encode($data);
}     



function getAllProducts(){
  $data = apcu_fetch('allproducts');

  if ($data == FALSE) { //if required data not in cache
    try{   
        $host = "localhost";
        $dbname = "ekatanalotis";
        $con_username = "root";
        $con_password = "";

        $conn = new PDO("mysql:host=$host;dbname=$dbname",$con_username,$con_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $current_error_mode = $conn->getAttribute(PDO::ATTR_ERRMODE);
        
      //get all the existing products
        $sql = "SELECT name FROM products";
        $prods = $conn->query($sql)->fetchAll();
        echo json_encode($prods);

        apcu_store('allproducts', $prods);
        }
        
      //catch exception
      catch(Exception $e) {
          echo 'Message: ' .$e->getMessage();
      }
      finally{
        $conn = null;
      }}
  else echo json_encode($data);
}     



function getOffers(){
  $poi_id = file_get_contents("php://input");
  date_default_timezone_set("Europe/Athens"); 
  $date = date('Y-m-d'); 

  $con = mysqli_connect('localhost','root','');
  mysqli_select_db($con,"ekatanalotis");
  $sql="SELECT offers.id,products.name,lcount,dcount,price,ful_criteria,sub_date,stock FROM offers INNER JOIN products
   on offers.poi_id = $poi_id and offers.p_id = products.id and exp_date > '".$date."'";  
  $result = mysqli_query($con,$sql);
  
  echo "<table id=info_table>
  <tr>
  <th>ID προσφοράς</th>
  <th>Προϊόν</th>
  <th>Likes</th>
  <th>Dislikes</th>
  <th>Τιμή (&#x20AC)</th>
  <th>Πλήρωση κριτηρίων</th>
  <th>Ημερομηνία καταχώρησης</th>
  <th>Απόθεμα</th>
  </tr>";

  while($row = mysqli_fetch_array($result)) {
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['name'] . "</td>";
    echo "<td>" . $row['lcount'] . "</td>";
    echo "<td>" . $row['dcount'] . "</td>";
    echo "<td>" . $row['price'] . "</td>";

    if($row['ful_criteria'] == "yes") echo "<td>&#10003</td>";  //if criteria is fulfilled, print a check icon
    else if ($row['ful_criteria'] == "no") echo "<td></td>";

    echo "<td>" . $row['sub_date'] . "</td>";
    echo "<td>" . $row['stock'] . "</td>";
    if($_SESSION['user_type'] == 'admin') echo "<td class=delete><i class='fa fa-trash'></i></td>";
    echo "</tr>";
   }
  echo "</table>";
    
mysqli_close($con);
}


function newOffer(){

  $offer_info = json_decode(file_get_contents("php://input"), true);
  $username = $offer_info['user'];
  $price = $offer_info['price'];
  $poi_id = $offer_info['poi'];
  $prod_name = $offer_info['prod_name'];
  
  $con = mysqli_connect('localhost','root','');
  mysqli_select_db($con,"ekatanalotis");

  //get the products.id of the chosen product
  $sql= "SELECT id FROM products where name like '".$prod_name."'";
  $result = mysqli_query($con,$sql);
  $pid = null; 

  while($row = mysqli_fetch_array($result)) {
    $pid = $row['id']; }

  date_default_timezone_set("Europe/Athens"); 
  $date = date('Y-m-d');   

  //check if the same active offer (same product and same POI) already exists
  $sql= "SELECT * FROM offers where p_id = $pid and poi_id = $poi_id and exp_date > '".$date."'";
  $result = mysqli_query($con,$sql);

  $count = 0; 
  $existing_price = -1;
  while($row = mysqli_fetch_array($result)) {
    $existing_price = $row['price'];
    $count++;}

  mysqli_close($con);

  //if offer is eligible for publication
  if(($count == 0 && $existing_price == -1) || ($count != 0 && $price < ($existing_price - 0.2*$existing_price))){

      //new offer is about to be registered, check which cache entry needs to be deleted 
      $delete_check = 0;
      $data = apcu_fetch('pois_with_offers');

      foreach ($data as $poi){
        if($poi["id"] == $poi_id) {
          $delete_check++;
          break;}}

      if($delete_check == 0){  //if the selected POI has no other offers so far
        apcu_delete('pois_with_offers'); 
        apcu_delete('pois_without_offers');} 
      else apcu_delete('pois_with_offers');  //if the selected POI has at least one other offer
      
      //insert the new offer into the database
      $host = "localhost";
      $dbname = "ekatanalotis";
      $con_username = "root";
      $con_password = "";

      $conn = new PDO("mysql:host=$host;dbname=$dbname",$con_username,$con_password);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $current_error_mode = $conn->getAttribute(PDO::ATTR_ERRMODE);

      $ful_criteria = null;
      $points = null;

      //if case of existing same offer but the new price is 20% lower than the old one, old one gets deleted
      if($count != 0 && $price < ($existing_price - 0.2*$existing_price)){
        $sql = "DELETE FROM offers WHERE p_id = $pid and poi_id = $poi_id";
        $conn->query($sql);
      }

      //get lowest price of the week to compare
      $stmt = $conn->prepare("SELECT last_week_low, yesterday_low FROM lows WHERE p_id=?");
      $stmt->execute([$pid]); 
      $lows_info = $stmt->fetch();
      $lw_low = $lows_info['last_week_low'];
      $yd_low = $lows_info['yesterday_low'];

      //if price (at least) 20% lower than yesterday's lowest price and price (at least) 20% lower than last week's lowest price
      if($price < ($yd_low - 0.2*$yd_low) && $price < ($lw_low - 0.2*$lw_low)){
        echo "Κερδίσατε 70 πόντους!";
        $ful_criteria = 'yes';
        $points = 70;
      }
      //if price (at least) 20% lower than yesterday's lowest price
      else if($price < ($yd_low - 0.2*$yd_low)){
        echo "Κερδίσατε 50 πόντους!";
        $ful_criteria = 'yes';
        $points = 50;
      }
      //if price (at least) 20% lower than last week's lowest price
      else if($price < ($lw_low - 0.2*$lw_low)){
        echo "Κερδίσατε 20 πόντους!";
        $ful_criteria = 'yes';
        $points = 20;
      }
      else {
        echo "Η προσφορά σας θα δημιοσευθεί αλλά δεν θα κερδίσετε πόντους";
        $ful_criteria = 'no';
        $points = 0;
      }

      //update total and monthly points
      $stmt = $conn->prepare("SELECT t_score, m_score FROM user WHERE username like ?");
      $stmt->execute([$username]); 
      $user_info = $stmt->fetch();
      $t_score = $user_info['t_score'];
      $m_score = $user_info['m_score'];

      $t_score = $t_score + $points;
      $m_score = $m_score + $points;

      $stmt = $conn->prepare("UPDATE user SET t_score =?, m_score=? WHERE username like ?");
      $stmt->execute([$t_score, $m_score, $username]);

      //create the new offer
      date_default_timezone_set("Europe/Athens"); 
      $date = date('Y-m-d'); 
      $exp_date = date("Y-m-d", strtotime("+1 week"));
      
      $stmt = $conn->prepare("INSERT INTO offers(id,username,p_id,lcount,dcount,price,ful_criteria,sub_date,poi_id,stock,exp_date) 
        VALUES(null,?,?,default,default,?,?,?,?,'ναι',?)");

      $stmt->bindParam(1,$username,PDO::PARAM_STR);
      $stmt->bindParam(2,$pid,PDO::PARAM_INT);
      $stmt->bindParam(3,$price,PDO::PARAM_STR);
      $stmt->bindParam(4,$ful_criteria,PDO::PARAM_STR);
      $stmt->bindParam(5,$date,PDO::PARAM_STR);
      $stmt->bindParam(6,$poi_id,PDO::PARAM_INT);
      $stmt->bindParam(7,$exp_date,PDO::PARAM_STR);
      $stmt->execute();
    }
  else echo("Αυτή η προσφορά υπάρχει ήδη, η προσφορά σας δεν θα δημοσιευτεί");    
}



      
