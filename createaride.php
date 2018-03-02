<!DOCTYPE html>  
<head>
  <title>Create A Ride</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <style>li {list-style: none;}</style>
</head>
<body>
  <h1>Create A Ride</h1>
  <ul>
    <form name="createRides" action="createaride.php" method="POST" >
      <li>Date:</li>
      <li><input type="date" name="dates" required/></li>
      <li>Time:</li>
      <li><input type="time" name="times" required/></li>
      <li>Origin:</li>
      <li><input type="text" name="origin" required/></li>
      <li>Destination:</li>
      <li><input type="text" name="destination" required/></li>
      <li>Base Price:</li>
      <li><input type="number" name="basePrice" min="1" required/></li>
      <li>Capacity:</li>
      <li><input type="number" name="capacity" min="1" required/></li>
      <li>Bidding Type:</li>
      <li><input type="radio" name="biddingType" value="Auto" checked />Auto<br>
          <input type="radio" name="biddingType" value="Self" />Self</li>
      <li>Comments:</li>
      <li><input type="text" name="sidenote" />
      <li><input type="submit" name="CreateARide" /></li>
    </form>
  </ul>
  <?php
  	// Connect to the database. Please change the password in the following line accordingly
    $db     = pg_connect("host=localhost port=5432 dbname=Project1 user=postgres password=A0158271A");
    if (isset($_POST['CreateARide'])) {
        $rideid = uniqid('ride');
        $_POST[sidenote] = !empty($_POST[sidenote]) ? "'$_POST[sidenote]'" : "null";
        $result = pg_query($db, "INSERT INTO rides values('$rideid', '$_POST[dates]', '$_POST[times]', '$_POST[origin]', '$_POST[destination]', '$_POST[basePrice]', '$_POST[capacity]', '$_POST[biddingType]', $_POST[sidenote])");
        if (!$result) {
            $failedresult = pg_send_query($db, "INSERT INTO rides values('$rideid', '$_POST[dates]', '$_POST[times]', '$_POST[origin]', '$_POST[destination]', '$_POST[basePrice]', '$_POST[capacity]', '$_POST[biddingType]', $_POST[sidenote])");

            echo pg_result_error(pg_get_result($db));
            echo "Created failed!!";
        } else {
            echo "Created ride successful!";
        }
    }

    ?>  
</body>
</html>
