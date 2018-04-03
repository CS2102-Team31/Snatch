<!DOCTYPE html>
<head>
  <title>Profile</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <style>

      li {list-style: none;}
      body {background-color: coral;}
      h1 {
          font-family: Impact;
      }
     .left-div{
          display: inline-block;
          width: 500px;
          text-align: left;
          padding: 30px;
          background-color: #FFFFFF;
          border-radius: 3px;
          margin: 15px;
          vertical-align: top;
          font-size: 1.2em;
          line-height: 1.8em;
        }

      .right-div
        {
          display: inline-block;
          width: 500px;
          text-align: left;
          padding: 30px;
          background-color: #FFFFFF;
          border-radius: 3px;
          margin: 15px;
          font-size: 1.2em;
        }
      .content {
        max-width: 500px;
        margin: auto;
        text-align: center;
      }

    </style>
</head>
<body class = "content">
  <?php
      include 'phpconfig.php';
    // Connect to the database. Please change the password in the following line accordingly
    session_start();
    $userID = $_SESSION['userID'];
    $db     = $psql;
    $result = pg_query($db, "SELECT * FROM users where userid = '$userID'");    // need to replace the uid accordingly
    $row    = pg_fetch_assoc($result);    // To store the result row
        echo '<h1> Hello '.$row[username].'!</h1>
        <div class="left-div">
      <strong>Name: </strong>'.$row[username].'</br>
        <strong>Gender: </strong>'.$row[gender].'</br>
      <strong>Date of Birth: </strong>'.$row[bday].'</br>
      <strong>Driver Licence Number: </strong>'.$row[driverlicense].'</br>
        <strong>Email: </strong>'.$row[email].'</br>
        <strong>Phone Number: </strong>'.$row[phone].'</br>
        <strong>User ID: </strong>'.$row[userid].'</br>
        <form name="display" action="profile.php" method="POST" >
            <input type="submit" name="edit" value="Edit" />
        </form>';

        if (isset($_POST['edit'])) {
        echo "<ul><form name='update' action='profile.php' method='POST' >
      <strong>Name: </strong> <input type='text' name='name_updated' value='$row[username]'/> </br>
        <strong>Gender: </strong> <input type='text' name='gender_updated' value='$row[gender]'/></br>
        <strong>Date of Birth: </strong> <input type='date' name='bday_updated' value='$row[bday]'/></br>
        <strong>Driver Licence Number: </strong> <input type='text' name='dl_updated' value='$row[driverlicense]'/></br>
        <strong>Email: </strong> <input type='text' name='email_updated' value='$row[email]'  disabled/></br>
        <strong>Phone: </strong> <input type='text' name='phone_updated' value='$row[phone]'/></br>
        <li><input type='submit' name='new' value= 'Update'/></li>
      </form></ul>";
        }

         if (isset($_POST['new'])) {  // Submit the update SQL command
            $result = pg_query($db, "UPDATE users SET username = '$_POST[name_updated]',
            gender = '$_POST[gender_updated]', bday = '$_POST[bday_updated]',
            driverLicense = '$_POST[dl_updated]', email = '$_POST[email_updated]',
            phone = '$_POST[phone_updated]' WHERE userid = '$userID'");
            if (!$result) {
                echo "Update failed!!";
            } else {
                echo "Update successful!";
                header("Refresh:0");
            }
        }

     ?>
    </div>

    <div class = "right-div">

    <h2> My Cars </h2>
    <?php
    include 'phpconfig.php';
    // Connect to the database. Please change the password in the following line accordingly
    session_start();
    $userID = $_SESSION['userID'];

    $db     = $psql;
    $result = pg_query($db, "select * from cars where exists ( Select 1 from (SELECT carsid FROM owns where usersid = '$userID' ) as R where R.carsid = cars.carid);");
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

        echo'<form name="display" action="profile.php" method="POST" >
                <input type="submit" name="add" value="Add A Car!" />
            </form></ul>';

        if (isset($_POST['add'])) {
        echo "<ul><form name='update' action='profile.php' method='POST' >
      <strong>Car Licence: </strong> <input type='text' name='carlicence_add'/> </br>
        <strong>Car Type: </strong> <input type='text' name='cartype_add'/>
        <li><input type='submit' name='newcar' value= 'Add'/></li>
      </form></ul>";
        }

         if (isset($_POST['newcar'])) { // Submit the update SQL command
             $id = uniqid(); 
             $result = pg_query($db, "Begin; INSERT INTO cars values('$id','$_POST[carlicence_add]','$_POST[cartype_add]');INSERT INTO owns values('$userID','$id'); commit;");
            if (!$result) {
                echo "Add failed!!";
            } else {
                echo "Add successful!";
                header("Refresh:0");
            }
        }
    ?>

    </div>

       <div class = "right-div">

    <h2> My Bids Status</h2>
    <?php
    include 'phpconfig.php';
    // Connect to the database. Please change the password in the following line accordingly
    session_start();
    $userID = $_SESSION['userID'];

    $db     = $psql;
    $result = pg_query($db, "select * from rides where exists ( Select 1 from (SELECT ridesid FROM bids where usersid = '$userID' ) as R where R.ridesid = rides.rideid);");
    $numcar = 1;
    $bids = array();
        while($row = pg_fetch_assoc($result)){    // To store the result row
            $numbids = pg_fetch_assoc(pg_query($db, "select ridesid, count(*) , min(price) from bids where ridesid = '$row[rideid]' group by ridesid;"));
             $mybid = pg_fetch_assoc(pg_query($db, "select price,status from bids where ridesid = '$row[rideid]' and usersid = '$userID'"));
            echo '<ul>
            <strong>Ride '.$numcar.'</strong> </br>
            <strong>Ride ID: </strong>'.$row[rideid].'</br>
            <strong>Date: </strong>'.$row[dates].'</br>
            <strong>Time: </strong>'.$row[times].'</br>
            <strong>Origin: </strong>'.$row[origin].'</br>
            <strong>Destination: </strong>'.$row[destination].'</br>
            <strong>Capacity: </strong>'.$row[capacity].'</br>
            <strong>Num bidders: </strong>'.$numbids[count].'</br>
            <strong>Min bid: </strong>'.$numbids[min].'</br>
            <strong>My bid: </strong>'.$mybid[price].'</br>
            <strong>Status: </strong>'.$mybid[status].'</br>
            <form name="display" action="profile.php" method="POST" >
                <input type="submit" name="remove'.$numcar.'" value="Retract bid" />
                <input type="submit" name="edit'.$numcar.'" value="Edit bid" />
            </form></ul>
            ';
            array_push($bids,$row[rideid]) ;
            if (isset($_POST['remove'.$numcar])) {
                $num = $bids[$numcar-1];
            $result = pg_query($db, "DELETE from bids where ridesid = '$num' and usersid='$userID';");
            if (!$result) {
                echo "remove failed!!".$bids[$numcar-1];
            } else {
                echo "remove successful!";
                header("Refresh:0");
                }
            }
             if (isset($_POST['edit'.$numcar])) {
              echo "<ul><form name='update' action='profile.php' method='POST' >
            <strong>New Bid: </strong> <input type='integer' name='bid_updated' value='$mybid[price]'/> </br>
              <li><input type='submit' name='bid-edit' value= 'Update'/></li>
            </form></ul>";
              }

         if (isset($_POST['bid-edit'])) {  // Submit the update SQL command
            $result = pg_query($db, "UPDATE bids SET price = '$_POST[bid_updated]' WHERE ridesid = '$row[rideid]' AND usersid = '$userID'");
            if (!$result) {
                echo "Update failed!!";
            } else {
                echo "Update successful!";
                header("Refresh:0");
            }
          }
             $numcar+=1;

        }
    ?>

    </div>

  <button type="button"><a href="bid.php" style="text-decoration:none;">Book A Ride!</button>
  <button type="button" ><a href="createaride.php" style="text-decoration:none;">Create A Ride!</button>
</body>
</html>
