<!DOCTYPE html>
<head>
  <title>Profile</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css" integrity="sha384-Zug+QiDoJOrZ5t4lssLdxGhVrurbmBWopoEl+M6BdEfwnCJZtKxi1KgxUyJq13dy" crossorigin="anonymous">
   <style>
      div {
          vertical-align: top;
          font-size: 1.2em;
          width: 50%;
          margin: auto;
          padding: 10px;
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
      include 'phpconfig.php';
    // Connect to the database. Please change the password in the following line accordingly
    session_start();
    $email = $_SESSION['userID'];
    $db     = $psql;
    $result = pg_query($db, "SELECT * FROM users where email = '$email'");    // need to replace the uid accordingly
    $row    = pg_fetch_assoc($result);    // To store the result row
        echo '<h1 class="center"> Hello '.$row[username].'!</h1>
        <div>
          <h2> My Profile </h2>
          <ul>
          <strong>Name: </strong>'.$row[username].'</br>
          <strong>Gender: </strong>'.$row[gender].'</br>
          <strong>Date of Birth: </strong>'.$row[bday].'</br>
          <strong>Driver Licence Number: </strong>'.$row[driverlicense].'</br>
          <strong>Email: </strong>'.$row[email].'</br>
          <strong>Phone Number: </strong>'.$row[phone].'</br>
          <form name="display" action="profile.php" method="POST" >
              <input type="submit" name="edit" value="Edit" />
          </form></ul>';

        if (isset($_POST['edit'])) {
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
        echo "<ul><form name='update' action='profile.php' method='POST' >
        <strong>Name: </strong> <input type='text' name='name_updated' value='$row[username]'/> </br>
        <strong>Gender: </strong> <input type='radio' name='gender_updated' value='$gender' checked />$gender
                                              <input type='radio' name='gender_updated' value='$gender1' />$gender1
                                              <input type='radio' name='gender_updated' value='$gender2'/>$gender2</br>
        <strong>Date of Birth: </strong> <input type='date' name='bday_updated' value='$row[bday]'/></br>
        <strong>Driver Licence Number: </strong> <input type='text' name='dl_updated' value='$row[driverlicense]'/></br>
        <strong>Email: </strong> <input type='text' name='email_updated' value='$row[email]'  disabled/></br>
        <strong>Phone: </strong> <input type='text' name='phone_updated' value='$row[phone]'/></br>
        <li><input type='submit' name='new' value= 'Update'/></li>
      </form></ul>";
        }

        if(!is_null($row[driverlicense])){ 
           echo  "Warning: removing license will cause all existing cars to be removed";
        }

         if (isset($_POST['new'])) {  // Submit the update SQL command
            $gender3 = ($_POST[gender_updated] == 'None') ? 'null' : "'$_POST[gender_updated]'";
            echo $_POST[bday_updated];
            $bday = ($_POST[bday_updated] == null) ? 'null' : "'$_POST[bday_updated]'";
            $dl = ($_POST[dl_updated] == null) ? 'null' : "'$_POST[dl_updated]'";

            $result = pg_query($db, "UPDATE users SET username = '$_POST[name_updated]',
            gender = $gender3, bday = $bday,
            driverLicense = $dl,
            phone = '$_POST[phone_updated]' WHERE email = '$email'");

            //check if driver License is null, remove all cars, no license means no car
             $a = pg_query($db, "SELECT * FROM users where email = '$email';");    // need to replace the uid accordingly
             $b   = pg_fetch_assoc($a);
             echo $b[driverLicense];
            if(is_null($b[driverlicense])){ 
              //get carid 
              $result = pg_query($db, "DELETE FROM owns where emails = '$email';");
              echo " HEELLO";
             
            }


            if (!$result) {
                echo "Update failed!!";
            } else {
                echo "Update successful!";
                header("Refresh:0");
            }
        }

     ?>
    </div>

    <div>

    <h2> My Cars </h2>
    <?php
    include 'phpconfig.php';
    // Connect to the database. Please change the password in the following line accordingly
    session_start();
    $email = $_SESSION['userID'];

    $db     = $psql;
    $result = pg_query($db, "select * from cars where exists ( Select 1 from (SELECT carsid FROM owns where emails = '$email' ) as R where R.carsid = cars.carid);");
    $numcar = 1;
    $cars = array();
        while($row = pg_fetch_assoc($result)){    // To store the result row
            echo '<ul>
            <strong>Car '.$numcar.'</strong> </br>
            <strong>Car ID: </strong>'.$row[carid].'</br>
            <strong>Car licence: </strong>'.$row[licenseplate].'</br>
            <strong>Car type: </strong>'.$row[cartype].'</br>
            <form name="display" action="profile.php" method="POST" >
                <input type="submit" name="remove'.$numcar.'" value="Remove" />
            </form></ul>';
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

        $license_exist = pg_query($db, "SELECT driverlicense FROM users where email = '$email'; "); 
        $ans = pg_fetch_assoc($license_exist);
                echo '<ul><form name="display" action="profile.php" method="POST" >
                  <input style="margin-top:30px" type="submit" name="add" value="Add A Car!" />
              </form></ul>';
            
              if (isset($_POST['add'])) {
                 if(is_null($ans[driverlicense])){ 
                 echo "<ul> No driver license</ul>";
              }else{ 
              echo "<ul><form name='update' action='profile.php' method='POST' >
            <strong>Car Licence: </strong> <input type='text' name='carlicence_add' required/> </br>
              <strong>Car Type: </strong> <input type='text' name='cartype_add' required/>
              <li><input type='submit' name='newcar' value= 'Add'/></li>
            </form></ul>";
              }
            }

           if (isset($_POST['newcar'])) { // Submit the update SQL command
              $id = uniqid();
                $result = pg_query($db, "Begin; INSERT INTO cars values('$id','$_POST[carlicence_add]','$_POST[cartype_add]');INSERT INTO owns values('$email','$id'); commit;");
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

    <h2> My Bids Status</h2>
    <?php
    include 'phpconfig.php';
    // Connect to the database. Please change the password in the following line accordingly
    session_start();
    $email = $_SESSION['userID'];

    $db     = $psql;
    $result = pg_query($db, "select * from rides where exists ( Select 1 from (SELECT ridesid FROM bids where emails = '$email' ) as R where R.ridesid = rides.rideid);");
    $numcar = 1;
    $bids = array();
        while($row = pg_fetch_assoc($result)){    // To store the result row
            $numbids = pg_fetch_assoc(pg_query($db, "select ridesid, count(*) , min(price) from bids where ridesid = '$row[rideid]' group by ridesid;"));
             $mybid = pg_fetch_assoc(pg_query($db, "select price,status,sidenote from bids where ridesid = '$row[rideid]' and emails = '$email';"));
             $driver = pg_fetch_assoc(pg_query($db, "select * from drives where ridesid = '$row[rideid]';"));
             $driverdetails = pg_fetch_assoc(pg_query($db, "select * from users where email = '$driver[email]';"));
             $cardetails = pg_fetch_assoc(pg_query($db, "select * from cars where carid = '$driver[carid]';"));

              if($mybid[status] == 1){ 
                $status = "Accepted";
              }else{
                $status = "Pending";
              }

              if(is_null($mybid[sidenote])){ 
                $note= "";
              }else{ 
                $note = $mybid[sidenote];
              }


            echo '<ul>
            <strong>Ride '.$numcar.'</strong> </br>
            <strong>Driver Name: '.$driverdetails[username].'</strong> </br>
            <strong>Driver Phone: '.$driverdetails[phone].'</strong> </br>
            <strong>Driver Car type: '.$cardetails[licenseplate].'</strong> </br>
            <strong>Driver Car plate: '.$cardetails[cartype].'</strong> </br>
            <strong>Ride ID: </strong>'.$row[rideid].'</br>
            <strong>Date: </strong>'.$row[dates].'</br>
            <strong>Time: </strong>'.$row[times].'</br>
            <strong>Origin: </strong>'.$row[origin].'</br>
            <strong>Destination: </strong>'.$row[destination].'</br>
            <strong>Capacity: </strong>'.$row[capacity].'</br>
            <strong>Num bidders: </strong>'.$numbids[count].'</br>
            <strong>Base price: </strong>'.$numbids[count].'</br>
            <strong>Min bid: </strong>'.$row[baseprice].'</br>
            <strong>My bid: </strong>'.$mybid[price].'</br>
            <strong>Status: '.$status.'</strong></br>
            <strong>Comments: </strong>'.$note.'</br>
            <form name="display" action="profile.php" method="POST" >
                <input type="submit" name="removebid'.$numcar.'" value="Retract bid" />
                <input type="submit" name="editbid'.$numcar.'" value="Edit bid" />
            </form></ul>
            ';
            array_push($bids,$row[rideid]) ;
            if (isset($_POST['removebid'.$numcar])) {
                $num = $bids[$numcar-1];
            $result = pg_query($db, "DELETE from bids where ridesid = '$num' and emails ='$email';");
            if (!$result) {
                echo "remove failed!!".$bids[$numcar-1];
            } else {
                echo "remove successful!";
                header("Refresh:0");
                }
            }
             if (isset($_POST['editbid'.$numcar])) {
              echo "<ul><form name='update' action='profile.php'  method='POST'>
            <strong>New Bid: </strong> <input type='integer' name='bid_updated' value='$mybid[price]'/> </br>
            <strong>Comments: </strong> <input type='text' name='sidenote' value= '$note'/> </br>";
             echo '<li><input type="submit" name= "bid-edit'.$numcar.'" value= "Update"/></li>
            </form></ul>';
              }

         if (isset($_POST['bid-edit'.$numcar])) {  // Submit the update SQL command
            $sidenote = ($note == "") ? "null" : "'$_POST[sidenote]'";
            $num = $bids[$numcar-1];
            if($_POST[bid_updated] < $row[baseprice]){ 

              echo "Bid was below base price";

          }else{
            $result = pg_query($db, "UPDATE bids SET price = '$_POST[bid_updated]', sidenote = $sidenote WHERE ridesid = '$num' AND emails = '$email'");
            if (!$result) {
                $failedresult = pg_send_query($db, "UPDATE bids SET price = '$_POST[bid_updated]', sidenote = $sidenote WHERE ridesid = '$num' AND emails = '$email'");

                                    echo pg_result_error(pg_get_result($db));
                echo "Update failed!!";
            } else {
                echo "Update successful!";
                header("Refresh:0");
            }
          }
        }
             $numcar+=1;

        }
    ?>
    </div>
    <div class = "center">
    <button type="button"><a href="bid.php" style="text-decoration:none;">Bid for A Ride!</button>
    <button type="button" ><a href="createaride.php" style="text-decoration:none;">Manage your Rides!</button>
    </div>

</body>
</html>
