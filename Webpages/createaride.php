<!DOCTYPE html>
<head>
  <title>Create A Ride</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css" integrity="sha384-Zug+QiDoJOrZ5t4lssLdxGhVrurbmBWopoEl+M6BdEfwnCJZtKxi1KgxUyJq13dy" crossorigin="anonymous">
   <style>
      div {
          vertical-align: top;
          font-size: 1.2em;
          width: 50%;
          margin: auto;
          padding: 10px;
          display: flex;
              flex-wrap: wrap;
              align-content:flex-start;
        }
      li {
          list-style: none;
      }
      .center {
         text-align: center;
         padding: 30px;
       }
   </style>

</head>
<body>
    <nav class="navbar navbar-dark" style="background-color: coral;">
        <a class="navbar-brand mx-auto" href="index.php">
              Snatch
        </a>
    </nav>


    <?php

        // Connect to the database. Please change the password in the following line accordingly
        include 'phpconfig.php';
        session_start();
        $email = $_SESSION['userID'];
        $db     = $psql;
        $username = pg_fetch_assoc(pg_query($db, "SELECT * FROM users where email = '$email'"))[username];
        echo '<h1 class = "center">' .$username. '\'s Rides</h1>';
     ?>

    <h2> My Total Earnings </h2>

        <?php
            include 'phpconfig.php';
            // Connect to the database. Please change the password in the following line accordingly
            session_start();
            $email = $_SESSION['userID'];
            $sum = 0;

            $db     = $psql;
            $result = pg_query($db, "with expiredrides as (
                                     	SELECT *
                                         FROM rides
                                         WHERE expiry = -1 AND rideid IN (
                                             SELECT ridesid
                                             FROM drives
                                             WHERE email = '$email'
                                         )
                                     )

                                     SELECT SUM(B.price)
                                     FROM bids B, expiredrides R
                                     WHERE B.ridesid = R.rideid AND B.status = 1;");

            $row = pg_fetch_assoc($result);
            echo '<strong>Total rides earnings: $'.$row[sum].'</strong> </br>';

        ?>


    <div class="row">
        <div class="column">

        <?php

            // Connect to the database. Please change the password in the following line accordingly
            include 'phpconfig.php';
            session_start();
            $email = $_SESSION['userID'];
            $db     = $psql;

            $result1 = pg_query($db, "UPDATE rides
                                  SET expiry = -1
                                  WHERE (NOW()::date = rides.dates
                                  AND NOW()::time - rides.times > INTERVAL '10 minutes')
                                  OR NOW()::date > rides.dates;");
            if(!$result1) {
                $failedresult = pg_send_query($db, "UPDATE rides
                                  SET expiry = -1
                                  WHERE (NOW()::date = rides.dates
                                  AND NOW()::time - rides.times > INTERVAL '10 minutes')
                                  OR NOW()::date > rides.dates;");

                echo pg_result_error(pg_get_result($db));
                echo "update expired rides failed";
            }

            $result = pg_query($db, "SELECT * FROM cars, (SELECT * FROM rides, (SELECT ridesid, carid FROM drives where email = '$email')as R where R.ridesid = rides.rideid) as P
                             WHERE cars.carid = P.carid order by P.rideid;");
            $rides = array();
            $numride = 1;

            while($row = pg_fetch_assoc($result)){

                if($row[expiry] != -1) {
                    echo '<ul>
                    <strong> Ride '.$numride.'</strong> </br>
                    <strong> Ride ID: </strong>' .$row[rideid].'</br>
                    <strong> Date: </strong>' .$row[dates].'</br>
                    <strong> Time: </strong>' .$row[times].'</br>
                    <strong> Origin: </strong>' .$row[origin].'</br>
                    <strong> Destination: </strong>' .$row[destination].'</br>
                    <strong> Base Price: </strong>' .$row[baseprice].'</br>
                    <strong> Car: </strong>' .$row[licenseplate].'</br>
                    <strong> Capacity: </strong>' .$row[capacity].'</br>
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
                                header("Refresh:0");
                            }
                        }
                    }

                    if (isset($_POST['edit'.$numride])) {
                        $num = $rides[$numride - 1];


                        echo "<ul><form name='update' action='createaride.php' method='POST' >
                        <strong>Date: </strong> <input type='date' name='date_updated' value='$row[dates]' required /> </br>
                        <strong>Time: </strong> <input type='time' name='time_updated' value='$row[times]' required /></br>
                        <strong>Origin: </strong> <input type='text' name='origin_updated' value='$row[origin]' required /></br>
                        <strong>Destination: </strong> <input type='text' name='d_updated' value='$row[destination]' required /></br>
                        <strong>Base Price: </strong> <input type='number' name='bp_updated' value='$row[baseprice]' min='1' required /></br>
                        <strong>Car: </strong>";

                        $result1 = pg_query($db, "SELECT * FROM owns, cars WHERE emails = '$email' and cars.carid = owns.carsid;");		// Query template
                        while ($rows = pg_fetch_array($result1)) {
                            if($rows[licenseplate] == $row[licenseplate]) {
                                echo "<input type='radio' name='car_updated' value='$rows[licenseplate]' checked/>$rows[licenseplate]";
                            } else {
                                echo "<input type='radio' name='car_updated' value='$rows[licenseplate]' />$rows[licenseplate]";
                            }
                        }

                        echo "
                        </br><strong>Capacity: </strong> <input type='number' name='capacity_updated' value='$row[capacity]' min='1' required /></br>
                        <strong>Comments: </strong> <input type='text' name='comment_updated' value='$row[sidenote]'/></br>
                        <li><input type='submit' name='new".$numride."' value= 'Update'/></li>
          	            </form></ul>";
                    }

                    if (isset($_POST['new'.$numride])) {
                        $num = $rides[$numride - 1];
                        $result = pg_query($db, "UPDATE rides SET dates = '$_POST[date_updated]', times = '$_POST[time_updated]',
                        origin = '$_POST[origin_updated]', destination = '$_POST[d_updated]', baseprice = '$_POST[bp_updated]',
                        capacity = '$_POST[capacity_updated]',
                        sidenote = '$_POST[comment_updated]' WHERE rideid = '$num'");

                        if (!$result) {
                            $failedresult = pg_send_query($db, "UPDATE rides SET dates = '$_POST[date_updated]', times = '$_POST[time_updated]',
                            origin = '$_POST[origin_updated]', destination = '$_POST[d_updated]', baseprice = '$_POST[bp_updated]',
                            capacity = '$_POST[capacity_updated]',
                            sidenote = '$_POST[comment_updated]' WHERE rideid = '$num'");

                            echo pg_result_error(pg_get_result($db));
                            echo "Update failed!!";
                        } else {
                            $result = pg_query($db, "UPDATE drives SET carid = (SELECT carid FROM cars WHERE licenseplate = '$_POST[car_updated]') WHERE ridesid = '$num'");
                            if (!$result) {
                                $failedresult = pg_send_query($db, "UPDATE drives SET carid = (SELECT carid FROM cars WHERE licenseplate = '$_POST[car_updated]') WHERE ridesid = '$num'");

                                echo pg_result_error(pg_get_result($db));
                                echo "Update failed!!";
                            } else {
                                echo "Update successful!";
                                header("Refresh:0");
                            }
                        }
                    }

                    $numride += 1;
                }
            }
        ?>

        </div>

        <div class="column">

            <?php
                //print bids
                // Connect to the database. Please change the password in the following line accordingly
                include 'phpconfig.php';
                session_start();
                $email = $_SESSION['userID'];
                $db     = $psql;


                for ($i = 0; $i < count($rides); $i++) {
                    $rideid = $rides[$i];
                    $capacity = pg_fetch_assoc(pg_query($db, "SELECT capacity FROM rides WHERE rideid = '$rideid';"))[capacity];



                    $date = pg_fetch_assoc(pg_query($db, "SELECT dates FROM rides WHERE rideid = '$rideid';"))[dates];
                    $time = pg_fetch_assoc(pg_query($db, "SELECT times FROM rides WHERE rideid = '$rideid';"))[times];
                    $result = pg_query($db, "SELECT emails, price, sidenote, status FROM bids where ridesid = '$rideid' ORDER BY status desc, emails asc;");

                    $bids = array('$j' => array());
                    $numbid = 1;
                    $j = $i + 1;
                    while($row = pg_fetch_assoc($result)){
                        if($row[status] == 0) {
                            echo '<ul>
                            <strong> Bid '.$j.'-'.$numbid.'</strong> </br>
                            <strong> Email: </strong>' .$row[emails].'</br>
                            <strong> Price: </strong>' .$row[price].'</br>
                            <strong> Comments: </strong>' .$row[sidenote].'</br>

                            <form name="display" action="createaride.php" method="POST" >
                                <input type="submit" name="'.$j.'choose'.$numbid.'" value="Select" />
                            </form></ul>';
                        } else {
                            $capacity = $capacity - 1;
                            echo '<ul>
                            <strong> Bid '.$j.'-'.$numbid.' (CHOSEN)</strong> </br>
                            <strong> Email: </strong>' .$row[emails].'</br>
                            <strong> Price: </strong>' .$row[price].'</br>
                            <strong> Comments: </strong>' .$row[sidenote].'</br>

                            <form name="display" action="createaride.php" method="POST" >
                                <input type="submit" name="'.$j.'unchoose'.$numbid.'" value="Deselect" />
                            </form></ul>';
                        }

                        $result1 = pg_query($db, "With updated as (Select *
                                      from bids, rides
                                      where bids.ridesid = '$rideid'
                                      and rides.rideid = '$rideid'
                                      and status = 0
                                      order by price desc
                                      limit '$capacity')

                                      UPDATE bids
                                      set status = 1
                                      from updated
                                      where bids.ridesid = updated.rideid
                                      and bids.emails = updated.emails
                                      and updated.times - NOW()::time < INTERVAL '10 minutes'
                                      And NOW()::date = updated.dates;");
                        if(!$result1) {
                            $failedresult = pg_send_query($db, "With updated as (Select * from bids, rides
                                                                     where bids.ridesid = '$rideid'
                                                                     and rides.rideid = '$rideid'
                                                                     order by price desc
                                                                     limit '$capacity')

                                                                     UPDATE bids
                                                                     set status = 1
                                                                     from updated
                                                                     where bids.ridesid = updated.rideid
                                                                     and
                                                                     NOW()::time - updated.times < INTERVAL '10 minutes'
                                                                     And NOW()::date = updated.dates;");

                            echo pg_result_error(pg_get_result($db));
                            echo "update bid failed";
                        }

                        array_push($bids['$j'], $row[emails]);

                        if (isset($_POST[$j.'choose'.$numbid])) {
                            if($capacity > 0) {
                                $num = $bids['$j'][$numbid - 1];
                                $result = pg_query($db, "UPDATE bids SET status = 1 WHERE emails = '$num' and ridesid = '$rideid';");

                                if(!$result) {
                                    $failedresult = pg_send_query($db, "DELETE FROM drives where ridesid  = '$num'");

                                    echo pg_result_error(pg_get_result($db));
                                    echo "Choose ride failed";
                                } else {

                                    echo "Choose ride successful";

                                    header("Refresh:0");
                                }
                            } else {
                                echo "You do not have enough capacity to select more passengers. Please deselect another passenger first.";
                            }
                        }

                        if (isset($_POST[$j.'unchoose'.$numbid])) {
                            $capacity = $capacity + 1;
                            $num = $bids['$j'][$numbid - 1];
                            $result = pg_query($db, "UPDATE bids SET status = 0 WHERE emails = '$num' and ridesid = '$rideid';");

                            if(!$result) {
                                $failedresult = pg_send_query($db, "DELETE FROM drives where ridesid  = '$num'");

                                echo pg_result_error(pg_get_result($db));
                                echo "Choose ride failed";
                            } else {
                                echo "Unchoose ride successful";

                                header("Refresh:0");
                            }
                        }

                        $numbid += 1;
                    }
                }
            ?>
        </div>
    </div>

    <div class="row">
        <div class="column">

            <h2>Create A Ride</h2>

            <?php
    	        // Connect to the database. Please change the password in the following line accordingly
                include 'phpconfig.php';
                session_start();
                $email = $_SESSION['userID'];
                $db     = $psql;
                $result = pg_query($db, "SELECT * FROM owns, cars WHERE owns.emails = '$email' and cars.carid = owns.carsid");		// Query template



                echo "<ul>
                <form name='createRides' action='createaride.php' method='POST' >
                    <li>Date:</li>
                    <li><input type='date' name='dates' required/></li>
                    <li>Time:</li>
                    <li><input type='time' name='times' required/></li>
                    <li>Origin:</li>
                    <li><input type='text' name='origin' required/></li>
                    <li>Destination:</li>
                    <li><input type='text' name='destination' required/></li>
                    <li>Base Price:</li>
                    <li><input type='number' name='basePrice' min='1' required/></li>
                    <li>Car:</li>";

                while ($row = pg_fetch_array($result)) {
                    echo "<li><input type='radio' name='car' value='$row[licenseplate]' />$row[licenseplate]</li>";
                }

                echo "
                    <li>Capacity:</li>
                    <li><input type='number' name='capacity' min='1' required/></li>
                    <li>Comments:</li>
                    <li><input type='text' name='sidenote' />
                    <li><input type='submit' name='CreateARide' value='Create' /></li>
                </form>
                </ul>";

                if (isset($_POST['CreateARide'])) {
                    $rideid = uniqid('ride');
                    $_POST[sidenote] = !empty($_POST[sidenote]) ? "'$_POST[sidenote]'" : 'null';

                    $result = pg_query($db, "INSERT INTO rides values('$rideid', '$_POST[dates]', '$_POST[times]',
                    '$_POST[origin]', '$_POST[destination]', '$_POST[basePrice]', '$_POST[capacity]',
                    $_POST[sidenote], 1);");

                    if (!$result) {
                        $failedresult = pg_send_query($db, "INSERT INTO rides values('$rideid', '$_POST[dates]', '$_POST[times]',
                        '$_POST[origin]', '$_POST[destination]', '$_POST[basePrice]', '$_POST[capacity]',
                        $_POST[sidenote], 1)");

                        echo pg_result_error(pg_get_result($db));
                        echo "Creating ride failed!";

                    } else {
                        $result1 = pg_query($db, "INSERT INTO drives values('$email', '$rideid', (SELECT carid FROM cars WHERE licenseplate = '$_POST[car]'), '$_POST[dates]', '$_POST[times]');");
                        if(!$result) {
                            echo "Creating ride failed!!";
                        } else {
                            echo "Created ride successfully!";
                            header("Refresh:0");
                        }
                    }
                }

            ?>
        </div>
    </div>

    <div class = "center">
        <button type="button" ><a href="profile.php" style="text-decoration:none;">Back to HomePage!</button>
        <button type="button"><a href="bid.php" style="text-decoration:none;">Bid for A Ride!</button>
    </div>
</body>
</html>
