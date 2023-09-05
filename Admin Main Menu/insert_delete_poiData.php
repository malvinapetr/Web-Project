<?php
    if (isset($_GET['functionToCall']) && function_exists($_GET['functionToCall'])) {
        call_user_func($_GET['functionToCall']);
      }


    function delete(){
      try{
          $host = "localhost";
          $dbname = "ekatanalotis";
          $con_username = "root";
          $con_password = "";
      
          $conn = mysqli_connect($host, $con_username, $con_password, $dbname);
          
          $sql = "DELETE FROM pois";
          $conn->query($sql); 
          }
      //catch exception
      catch(Exception $e) {
          echo 'Message: ' .$e->getMessage();
      }
      finally{
        mysqli_close($conn);
      }
    } 

  
  function insert_update(){
        try{   
          $host = "localhost";
          $dbname = "ekatanalotis";
          $con_username = "root";
          $con_password = "";
  
          $conn = new PDO("mysql:host=$host;dbname=$dbname",$con_username,$con_password);
          $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          $current_error_mode = $conn->getAttribute(PDO::ATTR_ERRMODE);
           
        //code for updating the 'pois' table 
          $stmt = $conn->prepare("INSERT IGNORE INTO pois(id,name,latitude,longitude) VALUES(?,?,?,?)");
          $json_data = file_get_contents("JSON/POIs.geojson",TRUE);
          $poi_info = json_decode($json_data, JSON_OBJECT_AS_ARRAY);
          
          $feats = $poi_info["features"];
        
        //code for filling the 'pois' table
        $count=0;    
          foreach ($feats as $ft){
            $pid = preg_replace('|node/|','', $ft["id"]);
            $properties = $ft["properties"];
            $geometry = $ft["geometry"];
            $coordinates = $geometry["coordinates"];
            $name = $properties["name"];
            $latitude = $coordinates[0];
            $longitude = $coordinates[1];
            
            $stmt->bindParam(1,$pid,PDO::PARAM_INT);
            $stmt->bindParam(2,$name,PDO::PARAM_STR);
            $stmt->bindParam(3,$latitude,PDO::PARAM_STR);
            $stmt->bindParam(4,$longitude,PDO::PARAM_STR);
            $stmt->execute();
            echo $count;
            $count++;
            }
          
        }
        //catch exception
        catch(Exception $e) {
            echo 'Message: ' .$e->getMessage();
        }
        finally{
          $conn = null;
        }
    }     