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
  
    echo '<br><h1 style="text-align: center">Hello '.$row[adminname].', '.$row[employeename].'</h1><br>';
  ?>
  
  </div>

  <div>
    <br><h2 style="text-align: center">All Users</h2> <br>
    <?php
    include 'phpconfig.php';
    session_start();
    $db     = $psql;
    $result = pg_query($db, 
    "SELECT U.email, U.username, U.pwd, U.phone, U.gender, U.bday, U.driverlicense 
    FROM Users U;");

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

    <h4>Insert User</h4>
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
        echo "Insert successful! Refresh to see changes";
        header("Refresh:0");
    }
  }
  ?>

  <div>
  <br><h2 style="text-align: center">All Cars</h2> <br>
  <h6 style="font-style: italic; text-align: center">With corresponding owners. Sorted by email of owner</h6>

    <?php
    include 'phpconfig.php';
    session_start();
    $adminname = $_SESSION['sessionID'];
    $db     = $psql;
    $result = pg_query($db, 
    "SELECT emails AS carowner, carsid, licenseplate, cartype 
    FROM Cars C INNER JOIN OWNS O ON C.carid = O.carsid;");

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

    <h4>Insert Car</h4>
      <form name="registerFormCar" action="admin.php" method="POST">
      <small class="error"> * required field</small>
      <div class="form-row">
          <input type="text" class="form-control-sm" id="email" placeholder="Enter email" name="email">
          <input type="text" class="form-control-sm" id="carlicense" placeholder="Enter car license" name="carlicense">
          <input type="text" class="form-control-sm" id="cartype" placeholder="Enter car type" name="cartype">
          <input type="submit" class="btn btn-primary" value="Insert" name="insertCar"><br>
      </div>
      </form>
    
    <?php
      include 'phpconfig.php';
      $db     = $psql;
      if (isset($_POST['insertCar'])){
        $id = uniqid();
        $result = pg_query($db, "
        BEGIN; 
        INSERT INTO cars Values('$id','$_POST[carlicense]','$_POST[cartype]'); 
        INSERT INTO owns values('$_POST[email]','$id'); 
        COMMIT;");
        if (!$result) {
            $failedresult = pg_send_query($db, "
            BEGIN; 
            INSERT INTO cars Values('$id','$_POST[carlicense]','$_POST[cartype]'); 
            INSERT INTO owns values('$_POST[email]','$id'); 
            COMMIT;");
            echo pg_result_error(pg_get_result($db));
            echo "<br>";
            echo "Insert failed!";
        } else {
            echo "Insert successful! Refresh to see changes";
            header("Refresh:0");
        }
      }
    ?>


  </div>

   <div> 
    <br><h2 style="text-align: center">Rides List</h2><br>
    <?php
      include 'phpconfig.php';
      session_start();
      $adminname = $_SESSION['sessionID'];
      $db     = $psql;
      $result = pg_query($db, 
      "SELECT D.ridesid, D.email, D.carid, D.datess, D.timess, 
      R.origin, R.destination, R.baseprice, R.capacity, R.sidenote
      FROM drives D INNER JOIN rides R 
        ON (D.ridesid = R.rideid 
          AND D.datess = R.dates
          AND D.timess = R.times)");
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

  <div>
    <br><h2 style="text-align: center">All Rides</h2><br>
    <h6 style="font-style: italic; text-align: center">With corresponding bid info. Sorted by rideid</h6>
    <?php
    include 'phpconfig.php';
    session_start();
    $adminname = $_SESSION['sessionID'];
    $db     = $psql;
    $result = pg_query($db, "WITH bidcount AS (
    SELECT ridesid, status, min(price) AS minprice, count(*) AS numbids
    FROM bids
    WHERE status = '0'
    GROUP By ridesid, status
    )

    SELECT R.rideid, R.dates, R.times, R.origin, R.destination, R.baseprice, R.capacity, 
      coalesce(B.numbids,0) as numBids, coalesce(B.minprice, 0) as minBid, 
      R.sidenote
    FROM rides R FULL OUTER JOIN bidcount B ON (R.rideid = B.ridesid)
    ORDER BY R.rideid, R.dates, R.times, R.origin, R.destination, minbid
    ;");
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
    <br><h2 style="text-align: center">All Admins</h2><br>
    <?php
    include 'phpconfig.php';
    session_start();
    $adminname = $_SESSION['sessionID'];
    $db     = $psql;
    $result = pg_query($db, 
    "SELECT * FROM admins;");
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
    <br><h2 style="text-align: center">Management History</h2><br>
    <?php
    include 'phpconfig.php';
    session_start();
    $adminname = $_SESSION['sessionID'];
    $db     = $psql;
    $result = pg_query($db, 
    "SELECT * FROM manages;");
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
