<!DOCTYPE html>

<head>
    <title>UPDATE PostgreSQL data with PHP</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        li {
            list-style: none;
        }
    </style>
</head>

<body>
    
    
    <h1>Login</h1>
    <form name="loginForm" action="index.php" method="POST">
        UserID:<br>
        <input type="text" name="userID" value = "234567891"><br> //change the uid for different profile
        Password:<br>
        <input type="password" name="password"><br>
        <input type="submit" value="Login" name="login"><br>
    </form>
    
    
     <?php
    if (isset($_POST['login'])){
        session_start();
        $_SESSION['userID'] = $_POST[userID];
        echo "this is". $_SESSION['userID'];
         header("location: profile.php");
        }
    ?>

    <!-- Refer to this on how to make a form to insert entries -->
    <h1>Register</h1>
    <p>Fill ALL fields (current alpha limitations)</p>
    <form name="registerForm" action="index.php" method="POST">
        Email* <br>
        <input type="text" name="email"> mailname@service.com<br>
        User Name*<br>
        <input type="text" name="name"><br>
        Password*<br>
        <input type="password" name="password"><br>
        Phone*<br>
        <input type="text" name="phone"> 8 digits<br>
        Gender<br>
        <input type="radio" name="gender" value="Male"> Male
        <input type="radio" name="gender" value="Female"> Female
        <input type="radio" name="gender" value="None" checked> <br>
        Birthday<br>
        <input type="text" name="birthday"> YYYY-MM-DD<br>
        Driver License<br>        
        <input type="text" name="driver_license"> 9 digits<br>        
        <input type="submit" value="Register" name="registerUser"><br>
    </form>
    
    <?php
    $db     = pg_connect("host=localhost port=5432 dbname=Project1 user=postgres password=**********");
    // damn annoying because all fields must be fields
    if (isset($_POST['registerUser'])){
        $uniqueId = uniqid();;
        $result = pg_query($db, "INSERT INTO users (email, userid, username, pwd, phone, bday, driverLicense)
                                      VALUES ('$_POST[email]', '$uniqueId', '$_POST[name]', '$_POST[password]', '$_POST[phone]', '$_POST[birthday]', '$_POST[driver_license]')
                                ");
        if (!$result) {
            $failedresult = pg_send_query($db,  "INSERT INTO users (email, userid, username, pwd, phone, bday, driverLicense)
                                                      VALUES ('$_POST[email]', '$uniqueId', '$_POST[name]', '$_POST[password]', '$_POST[phone]', '$_POST[birthday]', '$_POST[driver_license]')
                                            ");
            echo pg_result_error(pg_get_result($db));
            echo "<br>";
            echo "Insert failed!";
        } else {
            echo "Insert successful!";
        }
    }
    ?>

    <!-- Refer here to know how to query from database -->
    <h1>Display User</h1>
    <form name="searchByUser" action="index.php" method="POST">
        Name of user:<br>
        <input type="text" name="username"><br>
        <input type="submit" value="Display User" name="submitUser"><br>
    </form>
    
    <?php
    // This is the db to connect to
    $db     = pg_connect("host=localhost port=5432 dbname=Project1 user=postgres password=T0mat0r0ck5");	
    $result = pg_query($db, "SELECT * FROM users where username = '$_POST[username]'");		// Query template
    $row    = pg_fetch_assoc($result);		// To store the result row
    if (isset($_POST['submitUser'])) {
        echo "
        <form>
        Name:<br>
        <input type='text' name='name' value='$row[username]'><br>
        Gender:<br>
        <input type='text' name='gender' value='$row[gender]'><br>
        Birthday:<br>
        <input type='text' name='birthday' value='$row[bday]'><br>
        Email:<br>
        <input type='text' name='email' value='$row[email]'><br>
        UserID:<br>
        <input type='text' name='userID' value='$row[userid]'><br>
        Password:<br>
        <input type='password' name='password' value='$row[pwd]'><br>
        Driver License:<br>        
        <input type='text' name='driver_license' value='$row[driverlicense]'><br>
        </form>
        ";
    }
    
    ?>
   



    

 
</body>

</html>