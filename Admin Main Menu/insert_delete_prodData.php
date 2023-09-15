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
          
          $sql = "DELETE FROM categories";
          $conn->query($sql); 

          //clears categories, allproducts, subcategories- and products- cache in case of deleting file
          apcu_delete('categories');
          apcu_delete('allproducts');
          $iterator = new APCUIterator('#^subcategories-#');
          foreach($iterator as $entry_name) {
            apcu_delete($entry_name);
          }
          $iterator = new APCUIterator('#^products-#');
          foreach($iterator as $entry_name) {
            apcu_delete($entry_name);
          }    

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

      if(file_exists("JSON/products.json") && file_exists("JSON/prices.json"))
      { try{   
          $host = "localhost";
          $dbname = "ekatanalotis";
          $con_username = "root";
          $con_password = "";

          $conn = new PDO("mysql:host=$host;dbname=$dbname",$con_username,$con_password);
          $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          $current_error_mode = $conn->getAttribute(PDO::ATTR_ERRMODE);

        
        //clears categories, allproducts, subcategories- and products- cache in case of loading new file
        apcu_delete('categories');
        apcu_delete('allproducts');
        $iterator = new APCUIterator('#^subcategories-#');
            foreach($iterator as $entry_name) {
              apcu_delete($entry_name);
            }
        $iterator = new APCUIterator('#^products-#');
            foreach($iterator as $entry_name) {
              apcu_delete($entry_name);
            }    

        //code to check if there's any products in the database that don't exist in the file that will be uploaded   
          $json_data = file_get_contents("JSON/products.json",TRUE);
          $results_json = json_decode($json_data, JSON_OBJECT_AS_ARRAY);
          $prods_json = $results_json["products"];
          $sql = "SELECT id FROM products";
          $results_db = $conn->query($sql)->fetchAll();
    
          $count = 0;
          $items_db = 0;
          $common = array();
          foreach ($results_db as $rsdb){
            $id = $rsdb["id"];
            
            foreach ($prods_json as $rsjson){
              $json_id = $rsjson["id"];
            
              if($json_id == $id) {
                array_push($common,$id);
                $count++;}
            }
            $items_db++; 
          }

        if($items_db == $count) delete();    //if all the products of the database are included in the file that will be loaded  
                                            //then delete everything that was already there 
        elseif($items_db > $count && $count > 0){    // otherwise only delete the products that are also in the file
            $inQuery = str_repeat('?,', count($common) - 1) . '?'; 
            $stmt = $conn->prepare("DELETE FROM products WHERE id IN($inQuery)");
            $stmt->execute($common);
        }
      

        //code for updating the 'categories' ans 'subcategories' tables  
          $stmt = $conn->prepare("INSERT IGNORE INTO categories(cid,name) VALUES(?,?)");
          $stmt1 = $conn->prepare("INSERT IGNORE INTO subcategories(uuid,name,category) VALUES(?,?,?)");
          $json_data = file_get_contents("JSON/products.json",TRUE);
          $info = json_decode($json_data, JSON_OBJECT_AS_ARRAY);
          $cat_info = $info["categories"];

          foreach ($cat_info as $cat){
            //code for filling the 'categories' table
            $cid = $cat["id"];
            $name = $cat["name"];
            $subcats = $cat["subcategories"];
          
            
            $stmt->bindParam(1,$cid,PDO::PARAM_STR);
            $stmt->bindParam(2,$name,PDO::PARAM_STR);
            $stmt->execute();

            //code for filling the 'subcategories' table
            foreach ($subcats as $sub){
              $uuid = $sub["uuid"];
              $sub_name = $sub["name"];
            
              $stmt1->bindParam(1,$uuid,PDO::PARAM_STR);
              $stmt1->bindParam(2,$sub_name,PDO::PARAM_STR);
              $stmt1->bindParam(3,$cid,PDO::PARAM_STR);
              $stmt1->execute();
            }
          }


        //code for updating the 'products' table
          $stmt = $conn->prepare("INSERT INTO products(id,name,category,subcategory) VALUES(?,?,?,?)");
          $json_data = file_get_contents("JSON/products.json");
          $info = json_decode($json_data, JSON_OBJECT_AS_ARRAY);
          $products = $info["products"];
          
          foreach ($products as $product){
            $id = $product["id"];
            $name = $product["name"];
            $category = $product["category"];
            $subcategory = $product["subcategory"];
            
            //code for filling the 'products' table
            $stmt->bindParam(1,$id,PDO::PARAM_INT);
            $stmt->bindParam(2,$name,PDO::PARAM_STR);
            $stmt->bindParam(3,$category,PDO::PARAM_STR);
            $stmt->bindParam(4,$subcategory,PDO::PARAM_STR);
            $stmt->execute();
            }

        
          //code for updating the 'prices' table
          $stmt = $conn->prepare("INSERT INTO prices(name,date,price) VALUES(?,?,?)");
          $json_data = file_get_contents("JSON/prices.json",TRUE);
          $info = json_decode($json_data, JSON_OBJECT_AS_ARRAY);
          $price_info = $info["data"];

          foreach ($price_info as $pr){
            //code for filling the 'categories' table
            $pr_name = $pr["name"];
            $pr_info = $pr["prices"];
          
            //code for filling the 'subcategories' table
            foreach ($pr_info as $pr2){
              $date = $pr2["date"];
              $price = $pr2["price"];
            
              $stmt->bindParam(1,$pr_name,PDO::PARAM_STR);
              $stmt->bindParam(2,$date,PDO::PARAM_STR);
              $stmt->bindParam(3,$price,PDO::PARAM_STR);
              $stmt->execute();
            }
          }               
        }
        //catch exception
        catch(Exception $e) {
            echo 'Message: ' .$e->getMessage();
        }
        finally{
          $conn = null;
        }}
      else echo "error";  
    } 