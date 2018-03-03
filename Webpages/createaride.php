<!DOCTYPE html>
<head>
  <title>Create A Ride</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <style>li {list-style: none;}</style>
</head>
<body>

  <?php
    // Connect to the database. Please change the password in the following line accordingly
    include 'phpconfig.php';
    session_start();
    $userid = $_SESSION['userID'];
    $db     = $psql;
    $username = pg_fetch_assoc(pg_query($db, "SELECT * FROM users where userid = '$userid'"))[username];
    $result = pg_query($db, "SELECT * FROM rides where exists (SELECT 1 FROM (SELECT ridesid FROM drives where usersid = '$userid') as R where R.ridesid = rides.rideid);");
    echo '<h1>' .$username. '\'s Rides</h1>';

    $rides = array();
    $numride = 1;
    while($row = pg_fetch_assoc($result)){
        echo '<ul>
        <strong> Ride '.$numride.'</strong> </br>
        <strong> Ride ID: </strong>' .$row[rideid].'</br>
        <strong> Date: </strong>' .$row[dates].'</br>
        <strong> Time: </strong>' .$row[times].'</br>
        <strong> Origin: </strong>' .$row[origin].'</br>
        <strong> Destination: </strong>' .$row[destination].'</br>
        <strong> Base Price: </strong>' .$row[baseprice].'</br>
        <strong> Capacity: </strong>' .$row[capacity].'</br>
        <strong> Bidding Type: </strong>' .$row[biddingtype].'</br>
        <strong> Comments: </strong>' .$row[sidenote].'</br>
        <form name="display" action="createaride.php" method="POST" >
              <input type="submit" name="edit'.$numride.'" value="Edit" />
              <input type="submit" name="remove'.$numride.'" value="Remove" />
        </form></ul>';



        array_push($rides, $row[rideid]);

        if (isset($_POST['remove'.$numride])) {
            $num = $rides[$numride - 1];
            $result = pg_query($db, "DELETE FROM drives where ridesid  = '$num'");
            if(!$result) {
                $failedresult = pg_send_query($db, "DELETE FROM drives where ridesid  = '$num'");

                echo pg_result_error(pg_get_result($db));
                echo "Remove ride failed";
            } else {
                $result = pg_query($db, "DELETE FROM rides where rideid = '$num'");
                if(!$result) {
                    $failedresult = pg_send_query($db, "DELETE FROM rides where rideid = '$num'");

                    echo pg_result_error(pg_get_result($db));
                    echo "Remove ride failed";
                } else {
                    echo "Remove ride successful";
                    header("Location: http://localhost:8080/demo/createaride.php");
                }
            }
        }

        if (isset($_POST['edit'.$numride])) {
            $num = $rides[$numride - 1];
            $type = ($row[biddingtype] == 'Auto') ? 'Self' : 'Auto';
            echo "<ul><form name='update' action='createaride.php' method='POST' >
            <strong>Date: </strong> <input type='date' name='date_updated' value='$row[dates]' required /> </br>
            <strong>Time: </strong> <input type='time' name='time_updated' value='$row[times]' required /></br>
            <strong>Origin: </strong> <input type='text' name='origin_updated' value='$row[origin]' required /></br>
            <strong>Destination: </strong> <input type='text' name='d_updated' value='$row[destination]' required /></br>
            <strong>Base Price: </strong> <input type='number' name='bp_updated' value='$row[baseprice]' min='1' required /></br>
            <strong>Capacity: </strong> <input type='number' name='capacity_updated' value='$row[capacity]' min='1' required /></br>
            <strong>Bidding Type: </strong> <input type='radio' name='bt_updated' value='$row[biddingtype]' checked />$row[biddingtype]
            <input type='radio' name='bt_updated' value='$type' />$type</br>
            <strong>Comments: </strong> <input type='text' name='comment_updated' value='$row[sidenote]'/></br>
            <li><input type='submit' name='new".$numride."' value= 'Update'/></li>
          	</form></ul>";
        }

        if (isset($_POST['new'.$numride])) {
            $num = $rides[$numride - 1];
            $result = pg_query($db, "UPDATE rides SET dates = '$_POST[date_updated]', times = '$_POST[time_updated]',
             origin = '$_POST[origin_updated]', destination = '$_POST[d_updated]', baseprice = '$_POST[bp_updated]',
             capacity = '$_POST[capacity_updated]', biddingtype = '$_POST[bt_updated]',
             sidenote = '$_POST[comment_updated]' WHERE rideid = '$num'");
             if (!$result) {
                $failedresult = pg_send_query($db, "UPDATE rides SET dates = '$_POST[date_updated]', times = '$_POST[time_updated]',
                origin = '$_POST[origin_updated]', destination = '$_POST[d_updated]', baseprice = '$_POST[bp_updated]',
                capacity = '$_POST[capacity_updated]', biddingtype = '$_POST[bt_updated]',
                sidenote = '$_POST[comment_updated]' WHERE rideid = '$num'");

                echo pg_result_error(pg_get_result($db));
                echo "Update failed!!";
             } else {
                echo "Update successful!";
                header("Location: http://localhost:8080/demo/createaride.php");
             }
        }

        $numride += 1;
    }

    ?>

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
      <li><input type="submit" name="CreateARide" value="Create" /></li>
    </form>
  </ul>
  <?php
  	// Connect to the database. Please change the password in the following line accordingly
    include 'phpconfig.php';
    session_start();
    $userid = $_SESSION['userID'];
    $db     = $psql;
    if (isset($_POST['CreateARide'])) {
        $rideid = uniqid('ride');
        $_POST[sidenote] = !empty($_POST[sidenote]) ? "'$_POST[sidenote]'" : "null";

        $result = pg_query($db, "INSERT INTO rides values('$rideid', '$_POST[dates]', '$_POST[times]',
        '$_POST[origin]', '$_POST[destination]', '$_POST[basePrice]', '$_POST[capacity]', '$_POST[biddingType]',
        $_POST[sidenote])");

        if (!$result) {
            $failedresult = pg_send_query($db, "INSERT INTO rides values('$rideid', '$_POST[dates]', '$_POST[times]',
            '$_POST[origin]', '$_POST[destination]', '$_POST[basePrice]', '$_POST[capacity]', '$_POST[biddingType]',
            $_POST[sidenote])");

            echo pg_result_error(pg_get_result($db));
            echo "Creating ride failed!";

        } else {
            $carid = pg_fetch_assoc(pg_query("SELECT carsid FROM owns WHERE usersid = '$userid'"))[carsid];
            $result1 = pg_query($db, "INSERT INTO drives values('$userid', '$rideid', '$carid')");
            if(!$result) {
                $failedresult = pg_send_query($db, "INSERT INTO drives values('$userid', '$rideid', '$carid')");

                echo pg_result_error(pg_get_result($db));
                echo "Creating ride failed!!";
            } else {
                echo "Created ride successfully!";
                header("Location: http://localhost:8080/demo/createaride.php");
            }
        }
    }

  ?>



</body>
</html>
