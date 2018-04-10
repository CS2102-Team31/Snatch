<!DOCTYPE html>
<head>
  <title>Administration Page</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <style>
  .error {
    color: #FF0000;
  }

  li {
    list-style: none;
  }
  </style>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css" integrity="sha384-Zug+QiDoJOrZ5t4lssLdxGhVrurbmBWopoEl+M6BdEfwnCJZtKxi1KgxUyJq13dy" crossorigin="anonymous">
</head>
<body>
  <nav class="navbar navbar-dark" style="background-color: black;">
    <a class="navbar-brand mx-auto" href="index.php">
      Snatch (Admin)
    </a>
  </nav>


  <div>

  <?php
    include 'phpconfig.php';
    // Connect to the database. Please change the password in the following line accordingly
    session_start();
    $adminname = $_SESSION['sessionID'];
    $db     = $psql;
    $result = pg_query($db, "SELECT * FROM admins where adminname = '$adminname'");    // need to replace the uid accordingly
    $row    = pg_fetch_assoc($result);  
  
    echo '<br><h1 style="text-align: center">Hello '.$row[adminname].', '.$row[employeename].'</h1><br>';
  ?>
  
  </div>

  <div>
    <h2 style="text-align: center">All Bids</h2><br>
    <h6 style="font-style: italic; text-align: center">With corresponding ride info. Sorted by email</h6>
    <?php
    include 'phpconfig.php';
    session_start();
    $adminname = $_SESSION['sessionID'];
    $db     = $psql;
    $result = pg_query($db, "
    SELECT B.emails AS UserEmail, R.rideid, B.price AS biddingprice, 
    B.status, B.sidenote AS BidderSidenote,
    R.dates, R.times, R.origin, R.destination, R.baseprice, R.capacity,
    R.sidenote AS RideSideNote 
    FROM rides R INNER JOIN bids B on (R.rideid = B.ridesid)
    ORDER BY B.emails
    ;");
    // print out table of bids
    $i = 0;
    echo '<table id = "myTable" class= "table"><tr>';
    while ($i < pg_num_fields($result))
    {
      $fieldName = pg_field_name($result, $i);
      echo '<td>' . $fieldName . '</td>';
      $i = $i + 1;
    }
    echo '</tr>';
    $i = 0;

    while ($row = pg_fetch_row($result))
    {
      echo '<tr>';
      $count = count($row);
      $y = 0;
      while ($y < $count)
      {
        $c_row = current($row);
        echo '<td>' . $c_row . '</td>';
        next($row);
        $y = $y + 1;
      }
      echo '</tr>';
      $i = $i + 1;
    }
    pg_free_result($result);

    echo '</table>';

    ?>
  </div>

   <h4>Insert Bid</h4>

      <form name="form1" id="form1" action="" method="POST">
        <select name='rideid' id = 'rideid' >
          <option value="">--- Select Ride ID ---</option>
          <?php
            // This is the db to connect to
            include 'phpconfig.php';
            $db     = $psql;
            $result = pg_query($db, "SELECT rideid FROM rides");    // Query template


            while ($row = pg_fetch_array($result)) {
              echo "<option value='" . $row['rideid'] ."'>" . $row['rideid'] ."</option>";
            }
          ?>
        </select>
        <input type="submit" style="text-align: center" value="Select Ride ID" name="submitRideId"><br>
      </form>

        <?php

        // This is the db to connect to
        include 'phpconfig.php';
        $db     = $psql;
        session_start();

        /*Admin*/
        session_start();
        $adminname = $_SESSION['sessionID'];
        $result = pg_query($db, "SELECT * FROM admins where adminname = '$adminname'");
        $row    = pg_fetch_assoc($result);  
        $adminid = $row[adminid];
        /*endAdmin*/



        $result = pg_query($db, "SELECT * FROM rides where rideid = '$_POST[rideid]'");   // Query template
        $row    = pg_fetch_assoc($result);    // To store the result row

        if (isset($_POST['submitRideId'])) {
          echo "
          <form name='bid' method='POST'>
          Email:<br>
          <input type='text' name='email' ><br>
          RideID:<br>
          <input type='text' name='rideid' value='$row[rideid]' readonly><br>
          Date:<br>
          <input type='text' name='dates' value='$row[dates]' readonly><br>
          Time:<br>
          <input type='text' name='times' value='$row[times]' readonly><br>
          Origin:<br>
          <input type='text' name='origin' value='$row[origin]' readonly><br>
          Destination:<br>
          <input type='text' name='destination' value='$row[destination]' readonly><br>
          Base price:<br>
          <input type='text' name='baseprice' value='$row[baseprice]' readonly><br>
          Your bid:<br>
          <input type='text' name='bid' placeholder='Enter your bid' required><br>
          Comments:<br>
          <input type='text' name='sidenote' placeholder='Comments for driver' ><br>
          <input type='submit' name='new'><br>
          </form>
          ";
        }
        if (isset($_POST['new'])) { // Submit the update SQL command, update if user has already bid for ride, else insert.
          if($_POST[bid] >= $_POST[baseprice]) {
            $email = $_POST[email];
            $sidenote = ($_POST[sidenote] == "") ? "null" : '$_POST[sidenote]';
            $result = pg_query($db, " UPDATE bids SET price = '$_POST[bid]', sidenote = $sidenote WHERE emails = '$email' and ridesid = '$_POST[rideid]' ;
              INSERT INTO bids
              SELECT '$email', '$_POST[rideid]', '$_POST[bid]', 0, $sidenote
              WHERE NOT EXISTS (SELECT 1 FROM bids WHERE emails = '$email' and ridesid = '$_POST[rideid]');");
              if (!$result) {
                echo "Bid failed!!";
              } else {
                echo "Bid successful!";

                /*Admin*/
                echo '<br>Modified as '.$adminid." ";
                $result = pg_query($db, "INSERT INTO manages(adminsid, managetype, typeid, history)
                                          VALUES ('$adminid', 'Bid', '$_POST[rideid]' , 'Insert')
                                  ");
                if (!$result) {
                  $failedresult = pg_send_query($db, "INSERT INTO manages(adminsid, managetype, typeid, history)
                  VALUES ('$adminid', 'Bid', '$_POST[rideid]' , 'Insert')
                  ");
                  echo pg_result_error(pg_get_result($db));
                  echo "<br>";
                  echo "Admin fail";
                } else {
                  echo "Admin pass";
                }
                /*Admin*/

              }
            } else {
              echo "Bid failed. Bid price lower than base price!";
            }
          }
          ?>

      <h4>Delete Bid</h4>

      <form name="form2" id="form2" action="" method="POST">
        Email:<br>
        <input type='text' name='email' ><br>
        <select name='rideid' id = 'rideid' >
          <option value="">--- Select Ride ID ---</option>
          <?php
            // This is the db to connect to
            include 'phpconfig.php';
            $db     = $psql;
            $result = pg_query($db, "SELECT rideid FROM rides");    // Query template


            while ($row = pg_fetch_array($result)) {
              echo "<option value='" . $row['rideid'] ."'>" . $row['rideid'] ."</option>";
            }
          ?>
        </select>
        <input type="submit" style="text-align: center" value="Delete Bid" name="submitRideToDelete"><br>
      </form>

      <?php
        // This is the db to connect to
        include 'phpconfig.php';
        $db     = $psql;

        /*Admin*/
        session_start();
        $adminname = $_SESSION['sessionID'];
        $result = pg_query($db, "SELECT * FROM admins where adminname = '$adminname'");
        $row    = pg_fetch_assoc($result);  
        $adminid = $row[adminid];
        /*endAdmin*/



        if (isset($_POST['submitRideToDelete'])) {
          $result = pg_query($db, "DELETE FROM bids where emails = '$_POST[email]' AND ridesid = '$_POST[rideid]';");
          if (!$result) {
              $failedresult = pg_send_query($db, "DELETE FROM bids where emails = '$_POST[email]' AND ridesid = '$_POST[rideid]';");
              echo pg_result_error(pg_get_result($db));
              echo "<br>";
              echo "Delete failed!!";
          } else {
              echo "Delete successful! Refresh to see changes";

              /*Admin*/
              echo '<br>Modified as '.$adminid." ";
              $result = pg_query($db, "INSERT INTO manages(adminsid, managetype, typeid, history)
                                        VALUES ('$adminid', 'Bids', '$_POST[rideid]', 'Delete')
                                ");
              if (!$result) {
                $failedresult = pg_send_query($db, "INSERT INTO manages(adminsid, managetype, typeid, history)
                VALUES ('$adminid', 'Bids', '$_POST[rideid]', 'Delete')
                ");
                echo pg_result_error(pg_get_result($db));
                echo "<br>";
                echo "Admin fail";
              } else {
                echo "Admin pass";
              }
              /*Admin*/

              header("Refresh:0");
          }
        }
      ?>

       <h4>Modify Bid</h4>
    <!-- TODO: For Renee -->
    <form name="form2" id="form2" action="" method="POST">
        Email:<br>
        <input type='text' name='email' ><br>
        <select name='rideid' id = 'rideid' >
          <option value="">--- Select Ride ID ---</option>
          <?php
            // This is the db to connect to
            include 'phpconfig.php';
            $db     = $psql;
            $result = pg_query($db, "SELECT rideid FROM rides");    // Query template


            while ($row = pg_fetch_array($result)) {
              echo "<option value='" . $row['rideid'] ."'>" . $row['rideid'] ."</option>";
            }
          ?>
        </select>
        <input type="submit" style="text-align: center" value="Modify Bid" name="submitBidToModify"><br>
      </form>

    <!-- TODO: Given a userinput email and ride id::: can modify price, status and sidenote -->

     <?php
        // This is the db to connect to
        include 'phpconfig.php';
        $db     = $psql;

        /*Admin*/
        session_start();
        $adminname = $_SESSION['sessionID'];
        $result = pg_query($db, "SELECT * FROM admins where adminname = '$adminname'");
        $row    = pg_fetch_assoc($result);  
        $adminid = $row[adminid];
        /*endAdmin*/



        if (isset($_POST['submitBidToModify'])) {
          $result = pg_query($db, "SELECT * FROM bids where emails = '$_POST[email]' AND ridesid = '$_POST[rideid]';");
          $row    = pg_fetch_assoc($result);  

          $a = pg_query($db, "SELECT * FROM rides where rideid = '$_POST[rideid]'");   // Query template
          $b    = pg_fetch_assoc($a); 

           if ($row[status] == 1) {
                $status1 = 'Accepted';
                $status2 = 'Pending';
            } else if($row[status] == 0) {
                 $status1 = 'Pending';
                 $status2 = 'Accepted';
            } 

           echo "
                  <ul>
                    <form name='newbidform' method='POST' >
                     <strong> email: </strong><input type='text' name='rideremail' value='$_POST[email]'/></br>
                      <strong> rideid: </strong><input type='text' name='ridesid' value='$_POST[rideid]'/></br>
                      <strong> base price of ride: </strong>$b[baseprice]</br>
                      <strong>price: </strong> <input type='integer' name='price_updated' value='$row[price]'/> </br>
                      <strong>status: </strong> <input type='radio' name='status_updated' value='$status1' checked /> $status1                                                <input type='radio' name='status_updated' value='$status2' /> $status2</br>
                      <strong>sidenote: </strong> <input type='text' name='sidenote_updated' value='$row[sidenote]'/></br>
                      <li><input type='submit' name='biddingModification' value= 'Modify'/></li>
                    </form>
                  </ul>";

              }

                     if (isset($_POST['biddingModification'])) { 
                       $state = ($_POST[status_updated] == 'Accepted') ? 1 : 0;
                        echo $_POST[status_updated];

                        if($state == 1){ $status = 1; }else{ $status = 0; }

                         $sidenote = ($_POST[sidenote_updated] == "") ? "null" : "'$_POST[sidenote_updated]'";
                         if($_POST[price_updated] < $b[baseprice]){ 

                          echo "Bid was below base price";

                           }else{
                          $result = pg_query($db, "UPDATE bids SET price = '$_POST[price_updated]', sidenote = $sidenote , status = $status WHERE ridesid = '$_POST[ridesid]' AND emails = '$_POST[rideremail]'");
                          if (!$result) {
                              $failedresult = pg_send_query($db, "UPDATE bids SET price = '$_POST[bid_updated]', sidenote = $sidenote , status = $status WHERE ridesid = '$_POST[ridesid]' AND emails = $_POST[rideremail]");

                              echo pg_result_error(pg_get_result($db));
                              echo "Update failed!!";
                          } else {
                              echo "Update successful!";
                               /*Admin*/
                              echo '<br>Modified as '.$adminid." ";
                              $result = pg_query($db, "INSERT INTO manages(adminsid, managetype, typeid, history)
                                                        VALUES ('$adminid', 'User', '$_POST[email]', 'Modify Bid')
                                                ");
                              if (!$result) {
                                $failedresult = pg_send_query($db,  "INSERT INTO manages(adminsid, managetype, typeid, history)
                                                                      VALUES ('$adminid', 'User', '$_POST[email], 'Modify Bid')
                                                              ");
                                echo pg_result_error(pg_get_result($db));
                                echo "<br>";
                                echo "Admin fail";
                              } else {
                                echo "Admin pass";
                              }
                              /*Admin*/
                              header("Refresh:0");
                          }

             }
           }
      ?>

</div>


</html>