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
  /*Admin*/
  session_start();
  $adminname = $_SESSION['sessionID'];
  $result = pg_query($db, "SELECT * FROM admins where adminname = '$adminname'");
  $row    = pg_fetch_assoc($result);
  $adminid = $row[adminid];
  /*endAdmin*/

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
        /*Admin*/
        echo '<br>Modified as '.$adminid." ";
        $result = pg_query($db, "INSERT INTO manages(adminsid, managetype, typeid, history)
                                  VALUES ('$adminid', 'User', '$_POST[email]', 'Insert')
                          ");
        if (!$result) {
          $failedresult = pg_send_query($db,  "INSERT INTO manages(adminsid, managetype, typeid, history)
                                                VALUES ('$adminid', 'User', '$_POST[email], 'Insert')
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

  <div>

  <!-- Delete -->
  <h4>Delete User 1</h4>

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
    /*Admin*/
    session_start();
    $adminname = $_SESSION['sessionID'];
    $result = pg_query($db, "SELECT * FROM admins where adminname = '$adminname'");
    $row    = pg_fetch_assoc($result);
    $adminid = $row[adminid];
    /*endAdmin*/

    include 'phpconfig.php';
    session_start();
    $db = $psql;
    if (isset($_POST['submitUserDelete'])) {
      $result = pg_query($db, "DELETE FROM users where email = '$_POST[emailToDelete]';
                                DELETE FROM rides R WHERE NOT EXISTS ( SELECT 1 FROM drives D where R.rideid = D.ridesid);
                                DELETE FROM cars C WHERE NOT EXISTS ( SELECT 1 FROM drives D where C.carid = D.carid);");
      if (!$result) {
          echo "Delete failed!!";
      } else {
          echo "Delete successful! Refresh to see changes";
          /*Admin*/
          echo '<br>Modified as'.$adminid." ";
          $result = pg_query($db, "INSERT INTO manages(adminsid, managetype, typeid, history)
                                    VALUES ('$adminid', 'User', '$_POST[emailToDelete]', 'Delete')
                                  ");
          if (!$result) {
            $result = pg_query($db, "INSERT INTO manages(adminsid, managetype, typeid, history)
                                      VALUES ('$adminid', 'User', '$_POST[emailToDelete]', 'Delete')
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
    /*Admin*/
    session_start();
    $adminname = $_SESSION['sessionID'];
    $result = pg_query($db, "SELECT * FROM admins where adminname = '$adminname'");
    $row    = pg_fetch_assoc($result);
    $adminid = $row[adminid];
    /*endAdmin*/

    include 'phpconfig.php';
    session_start();
    $db = $psql;
    $result = pg_query($db, "SELECT * FROM users where email = '$_POST[emailToModify]'");
    $row    = pg_fetch_assoc($result);

    if (isset($_POST['submitUserModify'])) {
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

          /*Admin*/
          echo '<br>Modified as '.$adminid." ";
          $result = pg_query($db, "INSERT INTO manages(adminsid, managetype, typeid, history)
                                    VALUES ('$adminid', 'User', '$_POST[email_updated]', 'Modify')
                            ");
          if (!$result) {
            $failedresult = pg_send_query($db,   "INSERT INTO manages(adminsid, managetype, typeid, history)
            VALUES ('$adminid', 'User', '$_POST[email_updated]', 'Modify')
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

      /*Admin*/
      session_start();
      $adminname = $_SESSION['sessionID'];
      $result = pg_query($db, "SELECT * FROM admins where adminname = '$adminname'");
      $row    = pg_fetch_assoc($result);
      $adminid = $row[adminid];
      /*endAdmin*/



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

            /*Admin*/
            echo '<br>Modified as '.$adminid." ";
            $result = pg_query($db, "INSERT INTO manages(adminsid, managetype, typeid, history)
                                      VALUES ('$adminid', 'Car', '$_POST[carlicense]', 'Insert')
                              ");
            if (!$result) {
              $failedresult = pg_send_query($db, "INSERT INTO manages(adminsid, managetype, typeid, history)
              VALUES ('$adminid', 'Car', '$_POST[carlicense]', 'Insert')
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

    /*Admin*/
    session_start();
    $adminname = $_SESSION['sessionID'];
    $result = pg_query($db, "SELECT * FROM admins where adminname = '$adminname'");
    $row    = pg_fetch_assoc($result);
    $adminid = $row[adminid];
    /*endAdmin*/


    include 'phpconfig.php';
    session_start();
    $db = $psql;
    if (isset($_POST['submitCarDelete'])) {
      $result = pg_query($db, "DELETE FROM cars where licenseplate = '$_POST[lcToDelete]';
                               DELETE FROM rides R WHERE NOT EXISTS ( SELECT 1 FROM drives D where R.rideid = D.ridesid);");
      if (!$result) {
          echo "Delete failed!!";
      } else {
          echo "Delete successful! Refresh to see changes";

          /*Admin*/
          echo '<br>Modified as '.$adminid." ";
          $result = pg_query($db, "INSERT INTO manages(adminsid, managetype, typeid, history)
                                    VALUES ('$adminid', 'Car', '$_POST[lcToDelete]', 'Delete')
                            ");
          if (!$result) {
            $failedresult = pg_send_query($db, "INSERT INTO manages(adminsid, managetype, typeid, history)
            VALUES ('$adminid', 'Car', '$_POST[lcToDelete]', 'Delete')
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
    $old_licenseplate = $_POST[lcToModify];

    if (isset($_POST['submitCarModify'])) {

      // Display form to modify info
      echo "
      <ul>
        <form name='update' action='admin.php' method='POST' >
          <strong>Email of Owner: </strong> <input type='text' name='carowner_updated' value='$row[carowner]'/> </br>
          <strong>CarsID: </strong> <input type='text' name='carsid_updated' value='$row[carsid]'/> </br>
          <strong>License Plate (old): </strong> <input type='text' name='lc_old' value='$row[licenseplate]' readonly/> </br>
          <strong>License Plate (new): </strong> <input type='text' name='lc_updated' value='$row[licenseplate]'/> </br>
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
                                WHERE licenseplate = '$_POST[lc_old]';"
      );
      if (!$result) {
          $failedresult = pg_send_query($db,"
                                UPDATE cars
                                SET carid = '$_POST[carsid_updated]',
                                    licenseplate = '$_POST[lc_updated]',
                                    cartype = '$_POST[cartype_updated]'
                                WHERE licenseplate = '$_POST[lc_old]';"
          );
          echo pg_result_error(pg_get_result($db));
          echo "Modify failed!!";
      } else {
          echo "Modify successful! Refresh to see changes";

          /*Admin*/
          echo '<br>Modified as '.$adminid." ";
          $result = pg_query($db, "INSERT INTO manages(adminsid, managetype, typeid, history)
                                    VALUES ('$adminid', 'Car', '$_POST[lc_updated]', 'Modify')
                            ");
          if (!$result) {
            $failedresult = pg_send_query($db,  "INSERT INTO manages(adminsid, managetype, typeid, history)
            VALUES ('$adminid', 'Car', '$_POST[lc_updated]', 'Modify')
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

  <!-- Modify -->
  <h4>Transfer Ownership</h4>

  <!-- Modify Form -->
  <form name="formTransferCar" id="formTransferCar" action="" method="POST">
      <select class="form-control form-control-sm" name='lcToTransfer' id = 'lcTransfer' >
        <option value="">--- Select license plate of car to change owner ---</option>
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
      <input type="submit" class="btn btn-primary" value="Transfer" name="submitCarTransfer"><br>
  </form>

  <!-- Modify PHP Logic -->
  <?php

    /*Admin*/
    session_start();
    $adminname = $_SESSION['sessionID'];
    $result = pg_query($db, "SELECT * FROM admins where adminname = '$adminname'");
    $row    = pg_fetch_assoc($result);
    $adminid = $row[adminid];
    /*endAdmin*/



    include 'phpconfig.php';
    session_start();
    $db = $psql;
    $result = pg_query($db, "SELECT emails AS carowner, carsid, licenseplate, cartype
          FROM Cars C INNER JOIN OWNS O ON C.carid = O.carsid WHERE licenseplate = '$_POST[lcToTransfer]';");
    $row    = pg_fetch_assoc($result);
    $old_licenseplate = $_POST[lcToTransfer];

    if (isset($_POST['submitCarTransfer'])) {
      // Display form to modify info
      echo "
      <ul>
        <form name='update' action='admin.php' method='POST' >
          <strong>Email of Old Owner: </strong> <input type='text' name='carowner_old' value='$row[carowner]' readonly/> </br>
          <strong>Email of New Owner: </strong> <input type='text' name='carowner_updated' value='$row[carowner]'/> </br>
          <strong>CarsID: </strong> <input type='text' name='carsid_same' value='$row[carsid]' readonly/> </br>
          <li><input type='submit' name='changeOwner' value= 'Transfer'/></li>
        </form>
      </ul>";
    }

    // PHP Logic to do an Update Query on SQL
    if (isset($_POST['changeOwner'])) {  // Submit the update SQL command
      $result = pg_query($db, "
                                UPDATE owns
                                SET emails = '$_POST[carowner_updated]',
                                    carsid = '$_POST[carsid_same]'
                                WHERE emails = '$_POST[carowner_old]';"
      );
      if (!$result) {
          $failedresult = pg_send_query($db,"
                                UPDATE owns
                                SET emails = '$_POST[carowner_updated]',
                                    carsid = '$_POST[carsid_same]'
                                WHERE emails = '$_POST[carowner_old]';"
          );
          echo pg_result_error(pg_get_result($db));
          echo "Modify failed!!";
      } else {
          echo "Modify successful! Refresh to see changes";

          /*Admin*/
          echo '<br>Modified as '.$adminid." ";
          $result = pg_query($db, "INSERT INTO manages(adminsid, managetype, typeid, history)
                                    VALUES ('$adminid', 'Car', '$_POST[carsid_same]', 'Change Ownership')
                            ");
          if (!$result) {
            $failedresult = pg_send_query($db,   "INSERT INTO manages(adminsid, managetype, typeid, history)
            VALUES ('$adminid', 'Car', '$_POST[carsid_same]', 'Change Ownership')
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

  <!-- Modify -->
  <h4>Transfer Car</h4>
  <form name="fromTransferCar" action="admin.php" method="POST">
      <div class="form-row">
          <input type="text" class="form-control-sm" id="carowner_same" placeholder="Enter email of owner" name="carowner_same">
          <input type="text" class="form-control-sm" id="carsid_old" placeholder="Enter old car id" name="carsid_old">
          <input type="text" class="form-control-sm" id="carsid_new" placeholder="Enter new car id" name="carsid_new">
          <input type="submit" class="btn btn-primary" value="Transfer" name="changeCar"><br>
      </div>
    </form>

    <?php
    /*Admin*/
    session_start();
    $adminname = $_SESSION['sessionID'];
    $result = pg_query($db, "SELECT * FROM admins where adminname = '$adminname'");
    $row    = pg_fetch_assoc($result);
    $adminid = $row[adminid];
    /*endAdmin*/

    // PHP Logic to do an Update Query on SQL
    if (isset($_POST['changeCar'])) {  // Submit the update SQL command
      $result = pg_query($db, "
                                UPDATE owns
                                SET emails = '$_POST[carowner_same]',
                                    carsid = '$_POST[carsid_new]'
                                WHERE carsid = '$_POST[carsid_old]';"
      );
      if (!$result) {
          $failedresult = pg_send_query($db,"
                                UPDATE owns
                                SET emails = '$_POST[carowner_same]',
                                    carsid = '$_POST[carsid_new]'
                                WHERE carsid = '$_POST[carsid_old]';"
          );
          echo pg_result_error(pg_get_result($db));
          echo "Modify failed!!";
      } else {
          echo "Modify successful! Refresh to see changes";

          /*Admin*/
          echo '<br>Modified as '.$adminid." ";
          $result = pg_query($db, "INSERT INTO manages(adminsid, managetype, typeid, history)
                                    VALUES ('$adminid', 'Car', '$_POST[carsid_new]', 'Same owner modified owned car')
                            ");
          if (!$result) {
            $failedresult = pg_send_query($db,   "INSERT INTO manages(adminsid, managetype, typeid, history)
            VALUES ('$adminid', 'Car', '$_POST[carsid_new]', 'Same owner modified owned car')
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

<!-- Insert -->
<h4>Insert Rides</h4>

<!-- Insertion Form -->
<form name="registerFormRide" action="admin.php" method="POST">
  <small class="error">All except sidenote required</small>
  <div class="form-row">
      <input type="text" class="form-control-sm" placeholder="Enter email" name="email">
      <input type="text" class="form-control-sm" placeholder="Enter carid" name="car">
      <input type="date" class="form-control-sm" placeholder="Enter date" name="dates">
      <input type="time" class="form-control-sm" placeholder="Enter time" name="times">
      <input type="text" class="form-control-sm" placeholder="Enter origin" name="origin">
      <input type="text" class="form-control-sm" placeholder="Enter destination" name="destination">
      <input type="text" class="form-control-sm" placeholder="Enter baseprice" name="basePrice">
      <input type="text" class="form-control-sm" placeholder="Enter capacity" name="capacity">
      <input type="text" class="form-control-sm" placeholder="Enter sidenote (optional)" name="sidenote">
      <input type="submit" class="btn btn-primary" value="Insert" name="insertRide"><br>
  </div>
</form>

<?php
/*Admin*/
session_start();
$adminname = $_SESSION['sessionID'];
$result = pg_query($db, "SELECT * FROM admins where adminname = '$adminname'");
$row    = pg_fetch_assoc($result);
$adminid = $row[adminid];
/*endAdmin*/

// Connect to the database. Please change the password in the following line accordingly
include 'phpconfig.php';
session_start();
$db     = $psql;

if (isset($_POST['insertRide'])) {
        $rideid = uniqid('ride');
        $email = $_POST[email];
        $_POST[sidenote] = !empty($_POST[sidenote]) ? "'$_POST[sidenote]'" : "null";

        $result = pg_query($db, "INSERT INTO rides values('$rideid', '$_POST[dates]', '$_POST[times]',
        '$_POST[origin]', '$_POST[destination]', '$_POST[basePrice]', '$_POST[capacity]',
        $_POST[sidenote]);");

        if (!$result) {
            $failedresult = pg_send_query($db, "INSERT INTO rides values('$rideid', '$_POST[dates]', '$_POST[times]',
            '$_POST[origin]', '$_POST[destination]', '$_POST[basePrice]', '$_POST[capacity]',
            $_POST[sidenote])");

            echo pg_result_error(pg_get_result($db));
            echo "Creating ride failed!";

        } else {
            $result1 = pg_query($db, "INSERT INTO drives values('$email', '$rideid', '$_POST[car]', '$_POST[dates]', '$_POST[times]');");
            if(!$result) {
                echo "Creating ride failed!!";
            } else {
                echo "Created ride successfully!";

                /*Admin*/
                echo '<br>Modified as '.$adminid." ";
                $result = pg_query($db, "INSERT INTO manages(adminsid, managetype, typeid, history)
                                          VALUES ('$adminid', 'Rides', '$rideid', 'Insert Ride')
                                  ");
                if (!$result) {
                  $failedresult = pg_send_query($db,  "INSERT INTO manages(adminsid, managetype, typeid, history)
                  VALUES ('$adminid', 'Rides', '$rideid', 'Insert Ride')
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

  <!-- Delete -->
  <h4>Delete Ride</h4>

  <!-- Deletion Form -->
  <form name="formDeleteRide" id="formDeleteRide" action="" method="POST">
      <select class="form-control form-control-sm" name='rideToDelete' id = 'rideToDelete' >
        <option value="">--- Select rideid of ride to delete ---</option>
        <?php
        // This is the db to connect to
        include 'phpconfig.php';
        $db     = $psql;
        $result = pg_query($db, "SELECT * FROM rides");		// Query template


        while ($row = pg_fetch_array($result)) {
          echo "<option value='" . $row['rideid'] ."'>" . $row['rideid'] ."</option>";
        }
        ?>
      </select>
      <input type="submit" class="btn btn-primary" value="Delete" name="submitRideDelete"><br>
  </form>

  <!-- PHP Delete Logic -->
  <?php

    /*Admin*/
    session_start();
    $adminname = $_SESSION['sessionID'];
    $result = pg_query($db, "SELECT * FROM admins where adminname = '$adminname'");
    $row    = pg_fetch_assoc($result);
    $adminid = $row[adminid];
    /*endAdmin*/

    include 'phpconfig.php';
    session_start();
    $db = $psql;
    if (isset($_POST['submitRideDelete'])) {
      $result = pg_query($db, "DELETE FROM rides where rideid = '$_POST[rideToDelete]';");
      if (!$result) {
          echo "Delete failed!!";
      } else {
          echo "Delete successful! Refresh to see changes";

          /*Admin*/
          echo '<br>Modified as '.$adminid." ";
          $result = pg_query($db, "INSERT INTO manages(adminsid, managetype, typeid, history)
                                    VALUES ('$adminid', 'Rides', '$_POST[rideToDelete]', 'Delete Ride')
                            ");
          if (!$result) {
            $failedresult = pg_send_query($db,  "INSERT INTO manages(adminsid, managetype, typeid, history)
            VALUES ('$adminid', 'Rides', '$_POST[rideToDelete]', 'Delete Ride')
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
  </div>

<!-- Modify -->
<h4>Modify Ride</h4>

  <!-- Modify Form -->
  <form name="formModifyRide" id="formModifyRide" action="" method="POST">
      <select class="form-control form-control-sm" name='idToModify' id = 'idToModify' >
        <option value="">--- Select id of ride to modify ---</option>
        <?php
        // This is the db to connect to
        include 'phpconfig.php';
        $db     = $psql;
        $result = pg_query($db, "SELECT * FROM rides");		// Query template

        while ($row = pg_fetch_array($result)) {
          echo "<option value='" . $row['rideid'] ."'>" . $row['rideid'] ."</option>";
        }
        ?>
      </select>
      <input type="submit" class="btn btn-primary" value="Modify" name="submitRideModify"><br>
  </form>

  <!-- Modify PHP Logic -->
  <?php

    /*Admin*/
    session_start();
    $adminname = $_SESSION['sessionID'];
    $result = pg_query($db, "SELECT * FROM admins where adminname = '$adminname'");
    $row    = pg_fetch_assoc($result);
    $adminid = $row[adminid];
    /*endAdmin*/


    include 'phpconfig.php';
    session_start();
    $db = $psql;
    $result = pg_query($db, "SELECT * FROM rides where rideid = '$_POST[idToModify]'");
    $row    = pg_fetch_assoc($result);
      // Display form to modify info

      if (isset($_POST['submitRideModify'])) {
      echo "
      <ul>
        <form name='updateRide' action='admin.php' method='POST' >
          <strong>Ride id: </strong> <input type='text' name='id_same' value='$row[rideid]' readonly/> </br>
          <strong>Date: </strong> <input type='date' name='date_updated' value='$row[dates]'/></br>
          <strong>Time: </strong> <input type='time' name='time_updated' value='$row[times]'/></br>
          <strong>Origin: </strong> <input type='text' name='org_updated' value='$row[origin]'/></br>
          <strong>Destination: </strong> <input type='text' name='dst_updated' value='$row[destination]'/></br>
          <strong>Baseprice: </strong> <input type='text' name='bp_updated' value='$row[baseprice]'/></br>
          <strong>Capacity: </strong> <input type='text' name='cap_updated' value='$row[capacity]'/></br>
          <strong>Sidenote: </strong> <input type='text' name='sn_updated' value='$row[sidenote]'/></br>
          <li><input type='submit' name='modifyRide' value= 'Modify'/></li>
        </form>
      </ul>";
    }

    if (isset($_POST['modifyRide'])) {  // Submit the update SQL command
      $_POST[sn_updated] = !empty($_POST[sn_updated]) ? "'$_POST[sn_updated]'" : 'null';
      $result = pg_query($db, "UPDATE rides
                                SET dates = '$_POST[date_updated]',
                                    times = '$_POST[time_updated]',
                                    origin = '$_POST[org_updated]',
                                    destination = '$_POST[dst_updated]',
                                    baseprice = '$_POST[bp_updated]',
                                    capacity = '$_POST[cap_updated]',
                                    sidenote = $_POST[sn_updated]
                                    WHERE rideid = '$_POST[id_same]'");
      if (!$result) {
          $failedresult = pg_send_query($db,  "UPDATE rides
                                                SET dates = '$_POST[date_updated]',
                                                    times = '$_POST[time_updated]',
                                                    origin = '$_POST[org_updated]',
                                                    destination = '$_POST[dst_updated]',
                                                    baseprice = '$_POST[bp_updated]',
                                                    capacity = '$_POST[cap_updated]',
                                                    sidenote = $_POST[sn_updated]
                                                    WHERE rideid = '$_POST[id_same]'");
          echo pg_result_error(pg_get_result($db));
          echo "<br>";
          echo "Modify failed!!";
      } else {
          echo "Modify successful! Refresh to see changes";


          /*Admin*/
          echo '<br>Modified as '.$adminid." ";
          $result = pg_query($db, "INSERT INTO manages(adminsid, managetype, typeid, history)
                                    VALUES ('$adminid', 'Rides', '$_POST[id_same]', 'Modify Ride')
                            ");
          if (!$result) {
            $failedresult = pg_send_query($db, "INSERT INTO manages(adminsid, managetype, typeid, history)
            VALUES ('$adminid', 'Rides', '$_POST[id_same]', 'Modify Ride')
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

<div>
    <br><h2 style="text-align: center">All Bids</h2><br>
    <h6 style="font-style: italic; text-align: center">Just bid info with emails and ridesid referencing to Users and Rides</h6>
    <?php
    include 'phpconfig.php';
    session_start();
    $adminname = $_SESSION['sessionID'];
    $db     = $psql;
    $result = pg_query($db,
    "SELECT * FROM bids ORDER BY emails;");
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
    <h4>Insert Bid</h4>

      <form name="form1" id="form1" action="" method="POST">
        <select name='rideid' id = 'rideid' >
          <option value="">--- Select Ride ID ---</option>
          <?php
            // This is the db to connect to
            include 'phpconfig.php';
            $db     = $psql;
            $result = pg_query($db, "SELECT rideid FROM rides");		// Query template


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



        $result = pg_query($db, "SELECT * FROM rides where rideid = '$_POST[rideid]'");		// Query template
        $row    = pg_fetch_assoc($result);		// To store the result row

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
        if (isset($_POST['new'])) {	// Submit the update SQL command, update if user has already bid for ride, else insert.
          if($_POST[bid] >= $_POST[baseprice]) {
            $email = $_POST[email];
            $sidenote = ($_POST[sidenote] == "") ? null : '$_POST[sidenote]';
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

    <!-- Delete -->
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
            $result = pg_query($db, "SELECT rideid FROM rides");		// Query template


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

                         $sidenote = ($_POST[sidenote_updated] == "") ? null : "'$_POST[sidenote_updated]'";
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
