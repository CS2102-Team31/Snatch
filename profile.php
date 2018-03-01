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
  	// Connect to the database. Please change the password in the following line accordingly
    session_start();
    $userID = $_SESSION['userID'];
    $db     = pg_connect("host=localhost port=5432 dbname=Project1 user=postgres password=*******");	
    $result = pg_query($db, "SELECT * FROM users where userid = '$userID'");		// need to replace the uid accordingly
    $row    = pg_fetch_assoc($result);		// To store the result row
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
        <strong>Date of Birth: </strong> <input type='text' name='bday_updated' value='$row[bday]'/></br>
        <strong>Driver Licence Number: </strong> <input type='text' name='dl_updated' value='$row[driverlicense]'/></br>
        <strong>Email: </strong> <input type='text' name='email_updated' value='$row[email]'/></br>
        <strong>Phone: </strong> <input type='text' name='phone_updated' value='$row[phone]'/></br>
        <li><input type='submit' name='new' value= 'Update'/></li>  
    	</form></ul>";
        }
    
         if (isset($_POST['new'])) {	// Submit the update SQL command
            $result = pg_query($db, "UPDATE users SET username = '$_POST[name_updated]',
            gender = '$_POST[gender_updated]' WHERE userid = '$userID '");
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
    
    <h2> Cars </h2>    
    <?php
  	// Connect to the database. Please change the password in the following line accordingly
    session_start();
    $userID = $_SESSION['userID'];
    $db     = pg_connect("host=localhost port=5432 dbname=Project1 user=postgres password=*********");	
    $result = pg_query($db, "select * from cars where exists ( Select 1 from (SELECT carsid FROM owns where usersid = '$userID' ) as R where R.carsid = cars.carid);");
    $numcar = 1; 
    $cars = array();
        while($row = pg_fetch_assoc($result)){ 		// To store the result row
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
        
         if (isset($_POST['newcar'])) {	// Submit the update SQL command
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

  <button type="button"><a href="bookaride.php" style="text-decoration:none;">Book A Ride!</button>
  <button type="button" ><a href="bookaride.php" style="text-decoration:none;">Create A Ride!</button>
</body>
</html>
