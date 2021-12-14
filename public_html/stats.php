<?
$startdate=$_GET['startdate'] ;
$enddate  = $_GET['enddate'] ;
$datedays = $_GET['days'] ;
$dateday = $_GET['day'] ;
$datemonth = $_GET['month'] ;




$arrhost =array("xn----htbbcfnfmiigcfbvelsf1f2f.xn--p1ai","xn--19-dlc5agpbnci3n.xn--p1ai","fasad19.ru","xn--19-1lc4bi.xn--p1ai","xn----ctbjnajdnkbfyrcfw.xn--p1ai","xn----ftbfngwgm8as.xn--p1ai","xn----8sbahi8ae3a1akrw.xn--p1ai","vektor-minusinsk.ru");
$hostname = array("стройцентр-молодежный.рф","роскровля19.рф","fasad19.ru","цкф19.рф","вектор-минусинск.рф","цкф-регион.рф","цкф-доставка.рф","vektor-minusinsk.ru");


if ($_GET['day']) {
	
$datestart = $_GET['day']." 00:00:00";
$dateend = $_GET['day']." 23:59:59" ;

} elseif ($_GET['days']) {
	
$datestart = date("Y-m-d", mktime(0, 0, 0, date('m'), date('d') - $datedays, date('Y')))." 00:00:00";
$dateend = date("Y-m-d H:i:s");

} elseif ($_GET['month']) {

$datestart = $datemonth."-01 00:00:00 ";;
$dateend = date("Y-m-t", strtotime($datemonth))." 23:59:59 ";

} 





$servername = "localhost";
$username = "root";
$password = "";
$dbname = "site_ckf_v3";
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

echo $datestart ;
echo "<br>";
echo $dateend ;




?>
<form action="./copy_of_r.php">
<input hidden type="number" value="7" name="days">

  <input type="submit" value="последние 7 дней" > 
</form>
<form action="./copy_of_r.php">
<input hidden type="number" value="14" name="days">

  <input type="submit" value="последние 14 дней" > 
</form>
<form action="./copy_of_r.php">
<input type="date" name="day" value="<?echo date("Y-m-d")?>">
<input type="submit" value="день"> 
</form>
<form action="./copy_of_r.php">
 <input type="month" name="month" value="<?echo date("Y-m")?>">
  <input type="submit" value="месяц">
</form>
<?

  echo "<table> ";
  echo "  <tr>
    <th>Сайт</th>
    <th>Поситители</th>
    <th>Вернувшиеся поситители</th>
    <th>Телефон</th>
    <th>Контакты</th>
    <th>Цена</th>
    <th>Конфигуратор</th>
    <th>Более 5 м.</th>
    <th>от 1 до 5 м.</th>
    <th>менее 1 м.</th>
  </tr>
 ";
 $x=0;
foreach ($arrhost as &$value) {
    $host= $value;


  echo "<tr> ";

$sql_id_user        = "SELECT COUNT(id_user)       FROM `user_statistics` WHERE `date_start`>='".$datestart."' AND `host`='".$host."' AND `date_start`<='".$dateend."' AND `date_end`!=''AND `employee`!=1 ";
$sql_id_user_return = "SELECT COUNT(user_return)   FROM `user_statistics` WHERE `date_start`>='".$datestart."' AND `host`='".$host."' AND `date_start`<='".$dateend."' AND `date_end`!='' AND `user_return`='1' AND `employee`!=1 ";
$sql_click_phone    = "SELECT COUNT(click_phone)   FROM `user_statistics` WHERE `date_start`>='".$datestart."' AND `host`='".$host."' AND `date_start`<='".$dateend."' AND `click_phone`='1' AND `employee`!=1";
$sql_click_contact  = "SELECT COUNT(click_contact) FROM `user_statistics` WHERE `date_start`>='".$datestart."' AND `host`='".$host."' AND `date_start`<='".$dateend."' AND `click_contact`='1' AND `employee`!=1";
$sql_clik_price     = "SELECT COUNT(clik_price)    FROM `user_statistics` WHERE `date_start`>='".$datestart."' AND `host`='".$host."' AND `date_start`<='".$dateend."' AND `clik_price`='1' AND `employee`!=1";
$sql_click_conf     = "SELECT COUNT(click_conf)    FROM `user_statistics` WHERE `date_start`>='".$datestart."' AND `host`='".$host."' AND `date_start`<='".$dateend."' AND `click_conf`='1' AND `employee`!=1 ";
$sql_time           = "SELECT `id_user`, `click_phone`, `click_contact`, `clik_price`, `click_conf`, `date_start`, `date_end`, `user_return`, `host`, `sesion_id_user`, `time`, `user_agent` FROM `user_statistics` WHERE `date_start`>='".$datestart."' AND host='".$host."' AND `date_start`<='".$dateend."' AND `date_end`!=''AND `employee`!=1 ";

/*
echo "<br>";
echo $sql_id_user;
echo "<br>";
echo $sql_id_user_return;
echo "<br>";
echo $sql_click_phone;
echo "<br>";
echo $sql_click_contact;
echo "<br>";
echo $sql_clik_price;
echo "<br>";
echo $sql_click_conf;
echo "<br>";
echo $sql_time;
echo "<br>";
*/


echo"<th>".$hostname[$x]."</th>";
$x++;
	$result = $conn->query($sql_id_user);
	if ($result->num_rows > 0) {
	    // output data of each row
	    while($row = $result->fetch_assoc()) {
	        echo "
   <th> " . $row["COUNT(id_user)"]."</th>";
	    }
	} else {
	    echo "0 results";
	}
	$result = $conn->query($sql_id_user_return);
	if ($result->num_rows > 0) {
	    // output data of each row
	    while($row = $result->fetch_assoc()) {
	        echo "<th> " . $row["COUNT(user_return)"]."</th> ";
	    }
	} else {
	    echo "0 results";
	}
	
	$result = $conn->query($sql_click_phone);
	if ($result->num_rows > 0) {
	    // output data of each row
	    while($row = $result->fetch_assoc()) {
	        echo "<th> " . $row["COUNT(click_phone)"]."</th> ";
	    }
	} else {
	    echo "0 results";
	}

	$result = $conn->query($sql_click_contact);
	if ($result->num_rows > 0) {
	    // output data of each row
	    while($row = $result->fetch_assoc()) {
	        echo "<th> " . $row["COUNT(click_contact)"]."</th> ";
	    }
	} else {
	    echo "0 results";
	}
	
	$result = $conn->query($sql_clik_price);
	if ($result->num_rows > 0) {
	    // output data of each row
	    while($row = $result->fetch_assoc()) {
	        echo "<th> " . $row["COUNT(clik_price)"]."</th> ";
	    }
	} else {
	    echo "0 results";
	}
	
	
	$result = $conn->query($sql_click_conf);
	if ($result->num_rows > 0) {
	    // output data of each row
	    while($row = $result->fetch_assoc()) {
	        echo "<th> " . $row["COUNT(click_conf)"]."</th>  ";
	    }
	} else {
	    echo "0 results";
	}
	
$minutmin3=0;
$minutmin2=0;
$minutmin1=0;
	$result_time = $conn->query($sql_time);
	if ($result_time->num_rows > 0) {
	    while($row = $result_time->fetch_assoc()) {
		if ($row["date_end"]) {
			$date1=date_create($row["date_end"]);
			$date2=date_create($row["date_start"]);
			$diff=date_diff($date1,$date2);
			$minut= $diff->format(" %i");
			if ($minut <= 1) {
				$minutmin1++ ;
			} elseif ($minut > 1 && $minut <=5 ) { 
			
				$minutmin2++ ;
			} elseif ($minut > 5  ) { 
			$minutmin3++ ;
			} else {
				$minutmin1++ ;
			}
					 
					 
		} else {
	     	$minutmin2++ ;
		}   
	    }
	} else {
	//    echo "<th>0 results<th>";
	}
	
	echo "<th>".$minutmin3."</th>";
    echo "<th>".$minutmin2."</th>";
    echo "<th>".$minutmin1."</th>";

	
	
	
	
	
	
  echo "</tr> ";
}
  echo "</tr></table> ";
?>
<style>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
</style>