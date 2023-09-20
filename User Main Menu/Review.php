<?php
    session_start();
 
    if (isset($_GET['functionToCall']) && function_exists($_GET['functionToCall'])) {
          call_user_func($_GET['functionToCall']);
        }
    
   
   function getPoiOffers(){
  
        $poi_id = file_get_contents("php://input");
        date_default_timezone_set("Europe/Athens"); 
        $date = date('Y-m-d');

        $con = mysqli_connect('localhost','root','');
        mysqli_select_db($con,"ekatanalotis");
        $sql="SELECT offers.id,username,products.name,lcount,dcount,price,ful_criteria,sub_date,stock FROM offers INNER JOIN products
          on offers.poi_id = $poi_id and offers.p_id = products.id and offers.exp_date > '".$date."'";
        $result = mysqli_query($con,$sql);
        
        echo "<table id=offers_table>
        <tr>
        <th>ID προσφοράς</th>
        <th>Προϊόν</th>
        <th>Likes</th>
        <th>Dislikes</th>
        <th>Τιμή (&#x20AC)</th>
        <th>Πλήρωση κριτηρίων</th>
        <th>Ημερομηνία καταχώρησης</th>
        <th>Απόθεμα</th>
        <th id=moreless>Περισσότερα</th>
        </tr>";

      
        while($row = mysqli_fetch_array($result)) {
          echo "<td>" . $row['id'] . "</td>";
          echo "<td>" . $row['name'] . "</td>";
          echo "<td id=likes>" . $row['lcount'] . "</td>";
          echo "<td id=dislikes>" . $row['dcount'] . "</td>";
          echo "<td>" . $row['price'] . "</td>";
      
          if($row['ful_criteria'] == "yes") echo "<td>&#10003</td>";  //if criteria is fulfilled, print a check icon
          else if ($row['ful_criteria'] == "no") echo "<td></td>";
      
          echo "<td>" . $row['sub_date'] . "</td>";
          echo "<td>" . $row['stock'] . "</td>";
          echo "<td class=more_btn>&#x25BC</td>";
          echo "</tr>";
          }
        echo "</table>";
          
      mysqli_close($con);
    }
      
      

    function getMoreInformation()  
      {
        $offer_id = file_get_contents("php://input");
        
        $con = mysqli_connect('localhost','root','');
        mysqli_select_db($con,"ekatanalotis");
        $sql="SELECT offers.username,stock,user.t_score, url FROM offers INNER JOIN user INNER JOIN images
          on offers.id = $offer_id and user.username = offers.username and offers.p_id = images.p_id"; 
        $result = mysqli_query($con,$sql);
        
        echo "<tr>";
      while($row = mysqli_fetch_array($result)) {
          echo "<td><img id=prodimg src='images/".$row['url']."' alt='Φωτογραφία προϊόντος'></td>";
          echo "<td>" . $row['username'] . "</td>";
          echo "<td>" . $row['t_score'] . "</td>";
          echo "<td class=stockupd><i class='fa fa-paste' style='font-size:110%;'></i></td>";
          if($row['stock'] == 'όχι'){
            echo "<td class=nostockthumbdown><i class='fa fa-thumbs-down' style='font-size:110%;'></i></td>";
            echo "<td class=nostockthumbup><i class='fa fa-thumbs-up' style='font-size:110%;'></i></td>";} 
          else if($row['stock'] == 'ναι'){
            echo "<td class=thumbdown><i class='fa fa-thumbs-down' style='font-size:110%;'></i></td>";
            echo "<td class=thumbup><i class='fa fa-thumbs-up' style='font-size:110%;'></i></td>";}
          if($_SESSION['user_type'] == 'admin') echo "<td class=delete><i class='fa fa-trash style='font-size:110%;'></i></td>";  
        }
        echo "</tr>";
        
      mysqli_close($con);
    }

 
    function updateOfferLikes()  
      {
      $offer_info = json_decode(file_get_contents("php://input"), true);
      $offer_id = $offer_info['offerid'];
      $offer_likes = $offer_info['likescount'];
      $click_type = $offer_info['typeclick'];
      
      $con = mysqli_connect('localhost','root','');
      mysqli_select_db($con,"ekatanalotis");

      $sql = "UPDATE offers SET lcount='" .$offer_likes."' WHERE id='".$offer_id."' ";
      mysqli_query($con, $sql);

      $sql="SELECT lcount FROM offers WHERE id = $offer_id"; 
      $result = mysqli_query($con,$sql);
        
      while($row = mysqli_fetch_array($result)) {
          echo $row['lcount'];
        }

      date_default_timezone_set("Europe/Athens");    
      $sql="INSERT INTO ldhistory (id,username, type, offer_id,datetime) VALUES (null,'".$_SESSION['username']."',
      '".$click_type."','".$offer_id."','".date('Y-m-d H:i:s')."')";   
      mysqli_query($con, $sql);   

      mysqli_close($con);
    }


     function updateOfferDislikes()  
     {
      $offer_info = json_decode(file_get_contents("php://input"), true);
      $offer_id = $offer_info['offerid'];
      $offer_dislikes = $offer_info['dislikescount'];
      $click_type = $offer_info['typeclick'];
      
      $con = mysqli_connect('localhost','root','');
      mysqli_select_db($con,"ekatanalotis");

      $sql = "UPDATE offers SET dcount='" .$offer_dislikes."' WHERE id='".$offer_id."' ";
      mysqli_query($con, $sql);

      $sql="SELECT dcount FROM offers WHERE id = $offer_id"; 
      $result = mysqli_query($con,$sql);
      
      while($row = mysqli_fetch_array($result)) {
          echo $row['dcount'];}

      date_default_timezone_set("Europe/Athens");    
      $sql="INSERT INTO ldhistory (id,username, type, offer_id,datetime) VALUES (null,'".$_SESSION['username']."',
      '".$click_type."','".$offer_id."','".date('Y-m-d H:i:s')."')";   
      mysqli_query($con, $sql);    

    mysqli_close($con);
  }


  function checkLikeStatus()  
   {
      $offer_id = file_get_contents("php://input");
      
      $con = mysqli_connect('localhost','root','');
      mysqli_select_db($con,"ekatanalotis");

      $username = strval($_SESSION['username']);
      $sql= "SELECT count(*) FROM ldhistory WHERE offer_id ='".$offer_id."' and type like 'Προσθήκη like' or type like 'Αφαίρεση like' 
      and username= '".$username."'"; 
      $likescnt = mysqli_query($con,$sql);

      while($row = mysqli_fetch_array($likescnt)) {
        if($row[0] % 2 == 0) echo 0;
        else echo 1;
        }
      
    mysqli_close($con);
  }


  function checkDislikeStatus()  
  {
     $offer_id = file_get_contents("php://input");
     
     $con = mysqli_connect('localhost','root','');
     mysqli_select_db($con,"ekatanalotis");

     $username = strval($_SESSION['username']);
     $sql= "SELECT count(*) FROM ldhistory WHERE offer_id ='".$offer_id."' and type like 'Προσθήκη dislike' or type like 'Αφαίρεση dislike' 
     and username= '".$username."'"; 
     $likescnt = mysqli_query($con,$sql);

     while($row = mysqli_fetch_array($likescnt)) {
       if($row[0] % 2 == 0) echo 0;
       else echo 1;
       }
     
   mysqli_close($con);
  }


 function updateStock()  
  {
    $offer_id = file_get_contents("php://input");
    
    $con = mysqli_connect('localhost','root','');
    mysqli_select_db($con,"ekatanalotis");

    $sql="SELECT stock FROM offers WHERE id = $offer_id"; 
    $result = mysqli_query($con,$sql);
    $new_stock;
      
    while($row = mysqli_fetch_array($result)) {
        if ($row['stock'] == 'όχι') $new_stock = 'ναι';
        else if ($row['stock'] == 'ναι') $new_stock = 'όχι';
      }

    $sql = "UPDATE offers SET stock='" .$new_stock."' WHERE id='".$offer_id."' ";
    mysqli_query($con, $sql);  
    echo $new_stock;

  mysqli_close($con);
  }


  function deleteOffer(){
    $offer_id = file_get_contents("php://input");

    $con = mysqli_connect('localhost','root','');
    mysqli_select_db($con,"ekatanalotis");
    $sql = "DELETE FROM offers WHERE id='".$offer_id."' ";
    mysqli_query($con, $sql); 

    //clears POIs from cache
    apcu_delete('pois_with_offers');
    apcu_delete('pois_without_offers');
  }
      

    
    