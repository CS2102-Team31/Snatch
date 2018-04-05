<!DOCTYPE html>
<head>
  <title>UPDATE PostgreSQL data with PHP</title>
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
  
    echo '<br><h1 style="text-align: center">Hello '.$row[adminname].', '.$row[employeename].'</h1> <br>';
  ?>
  
  </div>

  <div>
    <br><h2 style="text-align: center">Users</h2> <br>
    <?php
    include 'phpconfig.php';
    session_start();
    $email = $_SESSION['userID'];
    $db     = $psql;
    $result = pg_query($db, 
    "SELECT U.email, U.username, U.pwd, U.phone, U.gender, U.bday, U.driverlicense FROM Users U;");

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

    <h1>Insert User</h1>
      <form name="registerForm" action="admin.php" method="POST">
      <small class="error"> * required field</small>
      <div class="form-row">

          <input type="text" class="form-control-sm" id="email" placeholder="Enter email*" name="email">
          <input type="text" class="form-control-sm" id="name" aria-describedby="nameHelp" placeholder="Enter username*" name="name">
          <input type="password" class="form-control-sm" id="password" aria-describedby="passwordHelp" placeholder="Enter password*" name="password">
          <input type="text" class="form-control-sm" id="phone" aria-describedby="phoneHelp" placeholder="Enter phone number*" name="phone"> 

          <div class="form-check-inline">
          <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1" value="Male" checked>
          <label class="form-check-label" for="exampleRadios1">
              Male
          </label>
          </div>
          <div class="form-check-inline">
          <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="Female">
          <label class="form-check-label" for="exampleRadios2">
              Female
          </label>
          </div>
          <div class="form-check-inline">
          <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios3" value="None">
          <label class="form-check-label" for="exampleRadios2">
              None     
          </label>
          </div>
          <input type="date" class="form-control-sm" id="birthday" aria-describedby="birthdayHelp" placeholder="Enter birthday YYYY-MM-DD" name="birthday">
          <input type="text" class="form-control-sm" id="driver_license" aria-describedby="driverLicenseHelp" placeholder="Enter driver license" name="driver_license"> 
          <br>
          <input type="submit" class="btn btn-primary" value="Insert" name="registerUser"><br>
      </div>
      </form>
  </div>

  <?php

  include 'phpconfig.php';
  $db     = $psql;
  // damn annoying because all fields must be fields
  if (isset($_POST['registerUser'])){
    $gender = ($_POST[gender] == "None") ? "null" : "'$_POST[gender]'";
    $_POST[birthday] = !empty($_POST[birthday]) ? "'$_POST[birthday]'" : "null";
    $_POST[driver_license] = !empty($_POST[driver_license]) ? "'$_POST[driver_license]'" : "null";

    $result = pg_query($db, "INSERT INTO users (email, username, pwd, phone, gender, bday, driverLicense)
                                VALUES ('$_POST[email]', '$_POST[name]', '$_POST[password]', '$_POST[phone]', $gender, $_POST[birthday], $_POST[driver_license])
                                ");
    if (!$result) {
        $failedresult = pg_send_query($db,  "INSERT INTO users (email, username, pwd, phone, gender, bday, driverLicense)
                                                VALUES ('$_POST[email]', '$_POST[name]', '$_POST[password]', '$_POST[phone]', $gender, $_POST[birthday], $_POST[driver_license])
                                                ");
        echo pg_result_error(pg_get_result($db));
        echo "<br>";
        echo "Insert failed!";
    } else {
        echo "Insert successful!";
    }
  }
  ?>

  <div>
  <br><h2 style="text-align: center">All Cars</h2> <br>
    <?php
    include 'phpconfig.php';
    // Connect to the database. Please change the password in the following line accordingly
    session_start();

    $db     = $psql;
    $result = pg_query($db, "SELECT * FROM Cars C INNER JOIN OWNS O ON C.carid = O.carsid;");
    $numcar = 1;
    $cars = array();
        echo '<table id = "myTableCars" class= "table">';
        echo '<tr>
            <td> # </td>
            <td> Owner </td>
            <td> Car ID </td>
            <td> License Plate </td> 
            <td> Car Type </td> 
            <td>';
        while($row = pg_fetch_assoc($result)){    // To store the result row
            echo '<tr>
            <td> '.$numcar.'</td>
            <td> '.$row[emails].'</td>
            <td> '.$row[carid].' </td>
            <td> '.$row[licenseplate].' </td> 
            <td> '.$row[cartype].' </td> 
            <td>
            <form name="display" action="admin.php" method="POST" >
                <input type="submit" name="remove'.$numcar.'" value="Remove" />
            </form>
            </td>
            </tr>';
            array_push($cars,$row[carid]) ;
            
            if (isset($_POST['remove'.$numcar])) {
                $num = $cars[$numcar-1];
            $result = pg_query($db, "DELETE from cars where carid = '$num';");
            if (!$result) {
                echo "remove failed!!".$cars[$numcar-1];
            } else {
                echo "remove successful!";
                header("Refresh:0");
                }
            }

            $numcar+=1;

        }
        echo '</table>';

        echo'<ul><form name="display" action="admin.php" method="POST" >
                <input style="margin-top:30px" type="submit" name="add" value="Add A Car!" />
            </form></ul>';

        if (isset($_POST['add'])) {
        echo "
        <ul><form name='update' action='admin.php' method='POST' >
        <strong>Car Licence: </strong> <input type='text' name='carlicence_add' required/> </br>
        <strong>Car Type: </strong> <input type='text' name='cartype_add' required/>
        <li><input type='submit' name='newcar' value= 'Add'/></li>
        </form></ul>";
        }

         if (isset($_POST['newcar'])) { // Submit the update SQL command
            $id = uniqid();
            $result = pg_query($db, "BEGIN; INSERT INTO cars values('$id','$_POST[carlicence_add]','$_POST[cartype_add]'); INSERT INTO owns values('$email','$id'); COMMIT;");
            if (!$result) {
                echo "Add failed!!";
            } else {
                echo "Add successful!";
                header("Refresh:0");
            }
        }
    ?>
  </div>


  <div>
    <br><h2 style="text-align: center">Bid Info (Per Ride)</h2> <br>
    <?php
    include 'phpconfig.php';
    session_start();
    $email = $_SESSION['userID'];
    $db     = $psql;
    $result = pg_query($db, "WITH bidcount AS (
    SELECT ridesid, status, min(price) as minprice, count(*) as numbids
    FROM bids
    Where status = '0'
    GROUP By ridesid, status
    )

    SELECT R.rideid, R.dates, R.times, R.origin, R.destination, R.baseprice, R.capacity, coalesce(B.numbids,0) as numBids, coalesce(B.minprice, 0) as minBid, R.sidenote
    FROM rides R FULL OUTER JOIN bidcount B on (R.rideid = B.ridesid)
    ORDER BY R.dates, R.times, R.origin, R.destination, minbid
    ;

    ");
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

  <div>
    <br><h2 style="text-align: center">Bid Info (Per Person)</h2> <br>
    <?php
    include 'phpconfig.php';
    session_start();
    $email = $_SESSION['userID'];
    $db     = $psql;
    $result = pg_query($db, "
    SELECT B.emails AS UserEmail, B.price AS biddingprice, 
    B.status, B.sidenote AS BidderSidenote,
    R.rideid, R.dates, R.times, R.origin, R.destination, R.baseprice, R.capacity,
    R.sidenote AS RideSideNote 
    FROM rides R FULL OUTER JOIN bids B on (R.rideid = B.ridesid)
    ORDER BY B.emails
    ;

    ");
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
</html>
