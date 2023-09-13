<?php

if (isset($_GET['functionToCall']) && function_exists($_GET['functionToCall'])) {
    call_user_func($_GET['functionToCall']);
  }


function numOfOffers(){  
    $conn = mysqli_connect('localhost', 'root', '', 'ekatanalotis');
    if (!$conn) {
        die('Database connection error: ' . mysqli_connect_error());
    }

    // Ensure you have the month and year parameters in the GET request
    if (isset($_POST['selectedMonth']) && isset($_POST['selectedYear'])) {
        $selectedMonth = $_POST['selectedMonth'];
        $selectedYear = $_POST['selectedYear'];

        // Perform your database query to retrieve statistics data
        // Replace 'offers' with your actual table name, and 'date_column' with your date column
        $query = "SELECT DAY(sub_date) AS day, COUNT(*) AS offers_count
            FROM offers
            WHERE MONTH(sub_date) = ? AND YEAR(sub_date) = ?
            GROUP BY DAY(sub_date)
            ORDER BY DAY(sub_date)";
                    
        // Prepare and execute the query (make sure to use the database connection)
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $selectedMonth, $selectedYear);  
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Fetch the data and format it as an associative array
        $data = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = array('day' => $row['day'], 'offers' => $row['offers_count']);
        }

        // Close the database connection
        mysqli_stmt_close($stmt);

        // Set the response header to indicate JSON content
        header('Content-Type: application/json');

        // Output the JSON data
        echo json_encode($data);
    } else {
        // Handle cases where month and year parameters are missing
        echo json_encode(array('error' => 'Missing parameters'));
    }
}


function averageDiscount(){

    $conn = mysqli_connect('localhost', 'root', '', 'ekatanalotis');
    if (!$conn) {
        die('Database connection error: ' . mysqli_connect_error());
    }

    //get current day
    date_default_timezone_set("Europe/Athens"); 
    $day = date('w'); //gets today's day in the form of an integer (0 for Sunday, 6 for Saturday)
    
    // Ensure you have the month and year parameters in the GET request
    if (isset($_POST['selectedValue']) && isset($_POST['Type'])) {
        $selectedValue = $_POST['selectedValue'];
        $type = $_POST['Type'];

        // Perform your database query to retrieve statistics data
        //if only category was chosen
        if($type == 'categories'){




        }
        //if subcategory was chosen
        else if($type == 'subcategories'){
            //get uuid of chosen subcategory 
            $query = "SELECT uuid from subcategories where name like ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $selectedValue);  
            $stmt->execute();
            $result = $stmt->get_result(); 

            while ($row = mysqli_fetch_assoc($result)) $uuid = $row['uuid'];
            
            //run it for every day of the current week 
            $data = array();
            for($i=$day; $i>=0; $i--)
            {
            //get the corresponding (average) last week low and offer prices for each offer
            $query = "SELECT sum(last_week_low) as last_week_avg, sum(price) as subcat_avg_price, count(*) as count from offers 
            inner join lows inner join products 
            on offers.p_id = products.id and products.subcategory like ? and offers.p_id = lows.p_id and offers.exp_date >= ?";
                        
            // Prepare and execute the query (make sure to use the database connection)
            $date = date('Y-m-d',strtotime("-$i days"));
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $uuid, $date);  
            $stmt->execute();
            $result = $stmt->get_result();
        
    
            // Fetch the data and format it as an associative array
            while ($row = mysqli_fetch_assoc($result)) {
                if($row['count'] > 0) $avg_discount = 100*($row['last_week_avg'] - $row['subcat_avg_price']) / $row['count'];
                else $avg_discount = 0; 
                $temp = array();
                $temp = array('average_discount' => $avg_discount);
                array_push($data,$temp);
            }}
        }
            
        // Close the database connection
        mysqli_stmt_close($stmt);

        // Set the response header to indicate JSON content
        header('Content-Type: application/json');

        // Output the JSON data
        echo json_encode($data);
    } else {
        // Handle cases where month and year parameters are missing
        echo json_encode(array('error' => 'Missing parameters'));
    }
}