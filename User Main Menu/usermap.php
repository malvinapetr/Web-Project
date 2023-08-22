<?php
    if (isset($_GET['functionToCall']) && function_exists($_GET['functionToCall'])) {
        call_user_func($_GET['functionToCall']);
      }


function pois_with_offers(){
    try{   
        $host = "localhost";
        $dbname = "ekatanalotis";
        $con_username = "root";
        $con_password = "";

        $conn = new PDO("mysql:host=$host;dbname=$dbname",$con_username,$con_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $current_error_mode = $conn->getAttribute(PDO::ATTR_ERRMODE);
         
       //get the ids, names, and coordinates of the POIs with an active offer, as well as the category of the product with the offer
        $sql = "SELECT DISTINCT pois.id,pois.name,latitude,longitude, categories.name FROM pois INNER JOIN offers on pois.id = offers.poi_id
        INNER JOIN products on products.id = offers.p_id INNER JOIN categories on cid = products.category";
        $pois_info = $conn->query($sql)->fetchAll();
        echo json_encode($pois_info);
        }
        
      //catch exception
      catch(Exception $e) {
          echo 'Message: ' .$e->getMessage();
      }
      finally{
        $conn = null;
      }
}     


function pois_without_offers(){
  try{   
      $host = "localhost";
      $dbname = "ekatanalotis";
      $con_username = "root";
      $con_password = "";

      $conn = new PDO("mysql:host=$host;dbname=$dbname",$con_username,$con_password);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $current_error_mode = $conn->getAttribute(PDO::ATTR_ERRMODE);
       
     //get the ids, names, and coordinates of the POIs with an active offer
      $sql = "SELECT DISTINCT pois.id,name,latitude,longitude FROM pois EXCEPT SELECT pois.id,name,latitude,longitude 
      from pois inner join offers on pois.id = offers.poi_id";
      $pois_info = $conn->query($sql)->fetchAll();
      echo json_encode($pois_info);
      }
      
    //catch exception
    catch(Exception $e) {
        echo 'Message: ' .$e->getMessage();
    }
    finally{
      $conn = null;
    }
}   



function getCategories(){
  try{   
      $host = "localhost";
      $dbname = "ekatanalotis";
      $con_username = "root";
      $con_password = "";

      $conn = new PDO("mysql:host=$host;dbname=$dbname",$con_username,$con_password);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $current_error_mode = $conn->getAttribute(PDO::ATTR_ERRMODE);
       
     //get the ids, names, and coordinates of the POIs with an active offer
      $sql = "SELECT name FROM categories";
      $categories = $conn->query($sql)->fetchAll();
      echo json_encode($categories);
      }
      
    //catch exception
    catch(Exception $e) {
        echo 'Message: ' .$e->getMessage();
    }
    finally{
      $conn = null;
    }
}     


function getOffers(){
  $poi_id = file_get_contents("php://input");
   
  $con = mysqli_connect('localhost','root','');
  mysqli_select_db($con,"ekatanalotis");
  $sql="SELECT offers.id,products.name,lcount,dcount,price,ful_criteria,sub_date,stock FROM offers INNER JOIN products
   on offers.poi_id = $poi_id and offers.p_id = products.id"; 
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
    echo "</tr>";
   }
  echo "</table>";
    
mysqli_close($con);
}



      
