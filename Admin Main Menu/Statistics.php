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

    // Ensure you have the month and year parameters in the GET request
    if (isset($_POST['selectedValue']) && isset($_POST['Type'])){
        $selectedValue = $_POST['selectedValue'];
        $type = $_POST['Type'];}
    else {
        // Handle cases where month and year parameters are missing
        echo json_encode(array('error' => 'Missing parameters'));
    }
    
    //get current day
    date_default_timezone_set("Europe/Athens"); 
    $day = date('w'); //gets today's day in the form of an integer (0 for Sunday, 6 for Saturday)

    averageDiscountCalculator($day,$selectedValue,$type,0); //offset = 1 (we want the current week), day = whatever integer corresponds to today's day
}


function prevWeeksAverageDiscount(){
    // Ensure you have the month and year parameters in the GET request
    if (isset($_POST['selectedValue']) && isset($_POST['Type']) && isset($_POST['weeksBack'])){
        $selectedValue = $_POST['selectedValue'];
        $type = $_POST['Type'];
        $offset = $_POST['weeksBack'];}
    else {
        // Handle cases where month and year parameters are missing
        echo json_encode(array('error' => 'Missing parameters'));
    }
    
    averageDiscountCalculator(6,$selectedValue,$type,$offset); //offset = how many weeks back we'll go, day = 6 (we want the entire week)
}


function nextWeeksAverageDiscount(){
    // Ensure you have the month and year parameters in the GET request
    if (isset($_POST['selectedValue']) && isset($_POST['Type']) && isset($_POST['weeksFor'])){
        $selectedValue = $_POST['selectedValue'];
        $type = $_POST['Type'];
        $offset = $_POST['weeksFor'];}
    else {
        // Handle cases where month and year parameters are missing
        echo json_encode(array('error' => 'Missing parameters'));
    }
    
    averageDiscountCalculator(6,$selectedValue,$type,$offset); //offset = how many weeks back we'll go, day = 6 (we want the entire week)
}


function averageDiscountCalculator($day,$selectedValue,$type,$offset){

    $conn = mysqli_connect('localhost', 'root', '', 'ekatanalotis');
    if (!$conn) {
        die('Database connection error: ' . mysqli_connect_error());
    }

    $data = array();
    $date2 = null;
    $date3 = null;
    //if only category was chosen get all the eligible data for that category
    if($type == 'categories'){
    
        //get cid of chosen subcategory 
        $query = "SELECT cid from categories where name like ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $selectedValue);  
        $stmt->execute();
        $result = $stmt->get_result(); 

        while ($row = mysqli_fetch_assoc($result)) $cid = $row['cid'];
        
        //run it for every day of the current week 
        for($i=$day; $i>=0; $i--)
        {
        //get the corresponding (average) last week low and offer prices for each offer
        $query = "SELECT sum(week_avg) as last_week_avg, sum(price) as cat_avg_price, count(*) as count from offers 
        inner join total_week_averages inner join products on offers.p_id = products.id and products.category like ? 
        and offers.p_id = total_week_averages.p_id and offers.sub_date <= ? and offers.exp_date >= ? and starting_date >= ? and ending_date <= ?";
   
        //shift determines how many days back we'll start iterating from
        if($offset > 0) $shift = 7*$offset + date('w') - (6 - $i);
        else if ($offset == 0) $shift = $i;

        $date = date('Y-m-d',strtotime("-$shift days"));
        if($offset > 0 && $i == $day){
            $shift2 = $shift + 7;
            $shift3 = $shift + 1;
            $date2 = date('Y-m-d',strtotime("-$shift2 days"));
            $date3 = date('Y-m-d',strtotime("-$shift3 days"));}
        else if($offset == 0 && $i == $day){
            $shift2 = $day + 7;
            $shift3 = $day + 1;
            $date2 = date('Y-m-d',strtotime("-$shift2 days"));
            $date3 = date('Y-m-d',strtotime("-$shift3 days"));}


        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssss", $cid, $date, $date, $date2, $date3);  
        $stmt->execute();
        $result = $stmt->get_result();
    

        // Fetch the data and format it as an associative array
        while ($row = mysqli_fetch_assoc($result)) {
            if($row['count'] > 0) $avg_discount = 100*($row['last_week_avg'] - $row['cat_avg_price']) / $row['count'];
            else $avg_discount = 0; 
            $temp = array();
            $temp = array('average_discount' => $avg_discount);
            array_push($data,$temp);
        }}

    }
    //if subcategory was chosen get all the eligible data for that subcategory
    else if($type == 'subcategories'){

        //get uuid of chosen subcategory 
        $query = "SELECT uuid from subcategories where name like ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $selectedValue);  
        $stmt->execute();
        $result = $stmt->get_result(); 

        while ($row = mysqli_fetch_assoc($result)) $uuid = $row['uuid'];
        
        //run it for every day of the current week 
        for($i=$day; $i>=0; $i--)
        {
        //get the corresponding (average) last week low and offer prices for each offer
        $query = "SELECT sum(week_avg) as last_week_avg, sum(price) as subcat_avg_price, count(*) as count from offers 
        inner join total_week_averages inner join products on offers.p_id = products.id and products.subcategory like ? 
        and offers.p_id = total_week_averages.p_id and offers.sub_date <= ? and offers.exp_date >= ? and starting_date >= ? and ending_date <= ?";
                    
        //shift determines how many days back we'll start iterating from
        if($offset > 0) $shift = 7*$offset + date('w') - (6 - $i);
        else if ($offset == 0) $shift = $i;

        $date = date('Y-m-d',strtotime("-$shift days"));
        if($offset > 0 && $i == $day){
            $shift2 = $shift + 7;
            $shift3 = $shift + 1;
            $date2 = date('Y-m-d',strtotime("-$shift2 days"));
            $date3 = date('Y-m-d',strtotime("-$shift3 days"));}
        else if($offset == 0 && $i == $day){
            $shift2 = $day + 7;
            $shift3 = $day + 1;
            $date2 = date('Y-m-d',strtotime("-$shift2 days"));
            $date3 = date('Y-m-d',strtotime("-$shift3 days"));}

        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssss", $uuid, $date, $date, $date2, $date3);  
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
}