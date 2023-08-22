<?php
    if (isset($_GET['functionToCall']) && function_exists($_GET['functionToCall'])) {
        call_user_func($_GET['functionToCall']);
      }
    
   
   function getPoiOffers(){
  
        $poi_id = file_get_contents("php://input");
        $con = mysqli_connect('localhost','root','');
        mysqli_select_db($con,"ekatanalotis");
        $sql="SELECT offers.id,username,products.name,lcount,dcount,price,ful_criteria,sub_date,stock FROM offers INNER JOIN products
          on offers.poi_id = $poi_id and offers.p_id = products.id"; 
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
        <th>Ενημέρωση αποθέματος</th>
        <th></th>
        <th></th>
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
          echo "<td id=stockupd><i class='fa fa-paste'></i></td>";
          if($row['stock'] == 'όχι'){
            echo "<td class=nostockthumbdown><i class='fa fa-thumbs-down'></i></td>";
            echo "<td class=nostockthumbup><i class='fa fa-thumbs-up'></i></td>";} 
          else if($row['stock'] == 'ναι'){
            echo "<td class=thumbdown><i class='fa fa-thumbs-down'></i></td>";
            echo "<td class=thumbup><i class='fa fa-thumbs-up'></i></td>";}
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
          echo "<td><img id=prodimg src='".$row['url']."' alt='Φωτογραφία προϊόντος'></td>";
          echo "<td>" . $row['username'] . "</td>";
          echo "<td>" . $row['t_score'] . "</td>";
          // echo "<td id=stockupd><i class='fa fa-paste'></i></td>";
          // if($row['stock'] == 'όχι'){
          //   echo "<td class=nostockthumbdown><i class='fa fa-thumbs-down'></i></td>";
          //   echo "<td class=nostockthumbup><i class='fa fa-thumbs-up'></i></td>";} 
          // else if($row['stock'] == 'ναι'){
          //   echo "<td class=thumbdown><i class='fa fa-thumbs-down'></i></td>";
          //   echo "<td class=thumbup><i class='fa fa-thumbs-up'></i></td>";}
        }
        echo "</tr>";
        
      mysqli_close($con);
    }
      

    
    