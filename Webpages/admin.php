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

    <!-- Insert -->
    <h4>Insert User</h4>

       <!-- Insertion Form -->
      <form name="registerForm" action="admin.php" method="POST">
      <small class="error"> * required field</small>
      <div class="form-row">

          <input type="text" class="form-control-sm" id="email" placeholder="Enter email*" name="email">
          <input type="text" class="form-control-sm" id="name" placeholder="Enter username*" name="name">
          <input type="password" class="form-control-sm" id="password" placeholder="Enter password*" name="password">
          <input type="text" class="form-control-sm" id="phone" placeholder="Enter phone number*" name="phone"> 

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
          <input type="date" class="form-control-sm" id="birthday" placeholder="Enter birthday YYYY-MM-DD" name="birthday">
          <input type="text" class="form-control-sm" id="driver_license" placeholder="Enter driver license" name="driver_license"> 
          <br>
          <input type="submit" class="btn btn-primary" value="Insert" name="registerUser"><br>
      </div>
      </form>
  </div>

  <!-- PHP Logic Insertion -->
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

  <!-- Delete -->
  <h4>Delete User</h4>
  
  <!-- Deletion Form -->
  <form name="formDeleteUser" id="formDeleteUser" action="" method="POST">
      <select class="form-control form-control-sm" name='emailToDelete' id = 'emailToDelete' >
        <option value="">--- Select email of user to delete ---</option>
        <?php
        // This is the db to connect to
        include 'phpconfig.php';
        $db     = $psql;
        $result = pg_query($db, "SELECT * FROM users");		// Query template


        while ($row = pg_fetch_array($result)) {
          echo "<option value='" . $row['email'] ."'>" . $row['email'] ."</option>";
        }
        ?>
      </select>
      <input type="submit" class="btn btn-primary" value="Delete" name="submitUserDelete"><br>
  </form>

  <!-- PHP Delete Logic -->
  <?php
    include 'phpconfig.php';
    session_start();
    $db = $psql;
    if (isset($_POST['submitUserDelete'])) {
      $result = pg_query($db, "DELETE FROM users where email = '$_POST[emailToDelete]';");
      if (!$result) {
          echo "Delete failed!!";
      } else {
          echo "Delete successful! Refresh to see changes";
          header("Refresh:0");
      }
    }
  ?>
  </div>

  <div>

  <!-- Modify -->
  <h4>Modify User</h4>
  
  <!-- Modify Form -->
  <form name="formModifyUser" id="formModifyUser" action="" method="POST">
      <select class="form-control form-control-sm" name='emailToModify' id = 'emailToModify' >
        <option value="">--- Select email of user to modify ---</option>
        <?php
        // This is the db to connect to
        include 'phpconfig.php';
        $db     = $psql;
        $result = pg_query($db, "SELECT * FROM users");		// Query template

        while ($row = pg_fetch_array($result)) {
          echo "<option value='" . $row['email'] ."'>" . $row['email'] ."</option>";
        }
        ?>
      </select>
      <input type="submit" class="btn btn-primary" value="Modify" name="submitUserModify"><br>
  </form>

  <!-- Modify PHP Logic -->  
  <?php
    include 'phpconfig.php';
    session_start();
    $db = $psql;
    $result = pg_query($db, "SELECT * FROM users where email = '$_POST[emailToModify]'");	
    $row    = pg_fetch_assoc($result);	  

    if (isset($_POST['submitUserModify'])) {
      echo "fuck<br>";
      echo $_POST['emailToModify'] . "<br>";
      echo $row[username];
      if ($row[gender] == null) {
        $gender1 = 'Male';
        $gender2 = 'Female';
        $gender = 'None';
      } elseif ($row[gender] == 'Female') {
          $gender1 = 'Male';
          $gender2 = 'None';
          $gender = 'Female';
      } else {
          $gender1 = 'Female';
          $gender2 = 'None';
          $gender = 'Male';
      }
      // Display form to modify info
      echo "
      <ul>
        <form name='update' action='admin.php' method='POST' >
          <strong>Name: </strong> <input type='text' name='name_updated' value='$row[username]'/> </br>
          <strong>Gender: </strong> <input type='radio' name='gender_updated' value='$gender' checked />$gender
                                  <input type='radio' name='gender_updated' value='$gender1' />$gender1
                                  <input type='radio' name='gender_updated' value='$gender2'/>$gender2</br>
          <strong>Date of Birth: </strong> <input type='date' name='bday_updated' value='$row[bday]'/></br>
          <strong>Driver Licence Number: </strong> <input type='text' name='dl_updated' value='$row[driverlicense]'/></br>
          <strong>Email: </strong> <input type='text' name='email_updated' value='$row[email]' readonly/></br>
          <strong>Phone: </strong> <input type='text' name='phone_updated' value='$row[phone]'/></br>
          <li><input type='submit' name='new' value= 'Modify'/></li>
        </form>
      </ul>";

    }

    // PHP Logic to do an Update Query on SQL
    if (isset($_POST['new'])) {  // Submit the update SQL command
      $gender3 = ($_POST[gender_updated] == 'None') ? 'null' : "'$_POST[gender_updated]'";
      $bday = ($_POST[bday_updated] == null) ? 'null' : "'$_POST[bday_updated]'";
      $dl = ($_POST[dl_updated] == null) ? 'null' : "'$_POST[dl_updated]'";
      $result = pg_query($db, "UPDATE users 
                                SET username = '$_POST[name_updated]',
                                    gender = $gender3, 
                                    bday = $bday,
                                    driverLicense = $dl,
                                    phone = '$_POST[phone_updated]' 
                                WHERE email = '$_POST[email_updated]'"
      );
      if (!$result) {
          echo "Modify failed!!";
      } else {
          echo "Modify successful! Refresh to see changes";
          header("Refresh:0");
      }
    }   
  ?>
  </div>
 
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

    <!-- Insert -->
    <h4>Insert Car</h4>

    <!-- Insertion Form -->
    <form name="registerFormCar" action="admin.php" method="POST">
      <small class="error"> * required field</small>
      <div class="form-row">
          <input type="text" class="form-control-sm" id="email" placeholder="Enter email" name="email">
          <input type="text" class="form-control-sm" id="carlicense" placeholder="Enter car license" name="carlicense">
          <input type="text" class="form-control-sm" id="cartype" placeholder="Enter car type" name="cartype">
          <input type="submit" class="btn btn-primary" value="Insert" name="insertCar"><br>
      </div>
    </form>
    
    <!-- PHP Logic Insertion -->
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

  <!-- Delete -->
  <h4>Delete Car</h4>
  
  <!-- Deletion Form -->
  <form name="formDeleteCar" id="formDeleteCar" action="" method="POST">
      <select class="form-control form-control-sm" name='lcToDelete' id = 'lcToDelete' >
        <option value="">--- Select license plate of car to Delete ---</option>
        <?php
        // This is the db to connect to
        include 'phpconfig.php';
        $db     = $psql;
        $result = pg_query($db, "SELECT * FROM cars");		// Query template


        while ($row = pg_fetch_array($result)) {
          echo "<option value='" . $row['licenseplate'] ."'>" . $row['licenseplate'] ."</option>";
        }
        ?>
      </select>
      <input type="submit" class="btn btn-primary" value="Delete" name="submitCarDelete"><br>
  </form>

  <!-- PHP Delete Logic -->
  <?php
    include 'phpconfig.php';
    session_start();
    $db = $psql;
    if (isset($_POST['submitCarDelete'])) {
      $result = pg_query($db, "DELETE FROM cars where licenseplate = '$_POST[lcToDelete]';");
      if (!$result) {
          echo "Delete failed!!";
      } else {
          echo "Delete successful! Refresh to see changes";
          header("Refresh:0");
      }
    }
  ?>
  </div>

  <div>

  <!-- Modify -->
  <h4>Modify Car</h4>
  
  <!-- Modify Form -->
  <form name="formModifyCar" id="formModifyCar" action="" method="POST">
      <select class="form-control form-control-sm" name='lcToModify' id = 'lcToModify' >
        <option value="">--- Select license plate of car to Modify ---</option>
        <?php
        // This is the db to connect to
        include 'phpconfig.php';
        $db     = $psql;
        $result = pg_query($db, "SELECT emails AS carowner, carsid, licenseplate, cartype 
          FROM Cars C INNER JOIN OWNS O ON C.carid = O.carsid;");

        while ($row = pg_fetch_array($result)) {
          echo "<option value='" . $row['licenseplate'] ."'>" . $row['licenseplate'] ."</option>";
        }
        ?>
      </select>
      <input type="submit" class="btn btn-primary" value="Modify" name="submitCarModify"><br>
  </form>

  <!-- Modify PHP Logic -->  
  <?php
    include 'phpconfig.php';
    session_start();
    $db = $psql;
    $result = pg_query($db, "SELECT emails AS carowner, carsid, licenseplate, cartype 
          FROM Cars C INNER JOIN OWNS O ON C.carid = O.carsid WHERE licenseplate = '$_POST[lcToModify]';");
    $row    = pg_fetch_assoc($result);	  

    if (isset($_POST['submitCarModify'])) {
      echo "fuck<br>";
      echo $_POST['lcToModify'] . "<br>";
      echo $row[licenseplate];
      // Display form to modify info
      echo "
      <ul>
        <form name='update' action='admin.php' method='POST' >
          <strong>Email of Owner: </strong> <input type='text' name='carowner_updated' value='$row[carowner]'/> </br>
          <strong>CarsID: </strong> <input type='text' name='carsid_updated' value='$row[carsid]'/> </br>
          <strong>License Plate: </strong> <input type='text' name='lc_updated' value='$row[licenseplate]'/> </br>
          <strong>Car Type: </strong> <input type='text' name='cartype_updated' value='$row[cartype]'/> </br>
          <li><input type='submit' name='newCar' value= 'Modify'/></li>
        </form>
      </ul>";

    }

    // PHP Logic to do an Update Query on SQL
    if (isset($_POST['newCar'])) {  // Submit the update SQL command
      $result = pg_query($db, "
                                UPDATE cars 
                                SET carid = '$_POST[carsid_updated]',
                                    licenseplate = '$_POST[lc_updated]', 
                                    cartype = '$_POST[cartype_updated]' 
                                WHERE licenseplate = '$_POST[lc_updated]';"
      );
      if (!$result) {
          $failedresult = pg_send_query($db,"
                                UPDATE cars 
                                SET carid = '$_POST[carsid_updated]',
                                    licenseplate = '$_POST[lc_updated]', 
                                    cartype = '$_POST[cartype_updated]' 
                                WHERE licenseplate = '$_POST[lc_updated]';"
          );
          echo pg_result_error(pg_get_result($db));
          echo "Modify failed!!";
      } else {
          echo "Modify successful! Refresh to see changes";
          header("Refresh:0");
      }
    }   
  ?>


  </div>

   <div> 
    <br><h2 style="text-align: center">All Rides</h2><br>
    <h6 style="font-style: italic; text-align: center">With corresponding driver and car info. Sorted by rideid</h6>    
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
          AND D.timess = R.times)
      ORDER BY D.ridesid");
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
    <h6 style="font-style: italic; text-align: center">With corresponding bid info/summary. Sorted by rideid</h6>
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
