<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
table {
  margin-left: auto;
  margin-right: auto;
  margin-top: 20px;  
  width: 40%;
  border-collapse: collapse;
}

table, td, th {
  border: 1px solid black;
  padding: 10px;
  text-align: center;
}

</style>
</head>
<body>

<?php
session_start();
if(!isset($_SESSION['tpages'])){
    $_SESSION['tpages']=0;
}

 if (isset($_GET['functionToCall']) && function_exists($_GET['functionToCall'])) {
  call_user_func($_GET['functionToCall']);
}

function sessiondestroy() 
{$_SESSION['tpages']=0;}

function selectnext()
{$con = mysqli_connect('localhost','root','');
  mysqli_select_db($con,"ekatanalotis");
  $sql="SELECT * FROM user";
  $result = mysqli_query($con,$sql);
  $results_cnt = mysqli_num_rows($result); 
  $total_pages = ceil($results_cnt/10);
if($_SESSION['tpages'] < $total_pages-1) $_SESSION['tpages']++;}

function selectprev()
{if($_SESSION['tpages'] > 0) $_SESSION['tpages']--;}

$con = mysqli_connect('localhost','root','');
mysqli_select_db($con,"ekatanalotis");
$offset = $_SESSION['tpages']*10;
$sql="SELECT username,t_score, m_tokens, t_tokens FROM user ORDER BY t_score DESC LIMIT $offset,10"; 
$result = mysqli_query($con,$sql);

echo "<table>
<tr>
<th>Rank</th>
<th>Username</th>
<th>Συνολικό σκορ</th>
<th>Μηνιαία tokens</th>
<th>Συνολικά tokens</th>
</tr>";

$i = 1;
while($row = mysqli_fetch_array($result)) {
  echo "<td> #". $i + 10*$_SESSION['tpages'] . "</td>";
  echo "<td>" . $row['username'] . "</td>";
  echo "<td>" . $row['t_score'] . "</td>";
  echo "<td>" . $row['m_tokens'] . "</td>";
  echo "<td>" . $row['t_tokens'] . "</td>";
  echo "</tr>";
  $i++;}
echo "</table>";
    
mysqli_close($con);
?>

</body>
</html>

