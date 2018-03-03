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

    <div class="container-fluid">

    <h1>Login</h1>
    <form name="loginForm" action="index.php" method="POST">
    <div class="form-group">
        <label for="username">Username</label>
        <input type="text" class="form-control" id="username" aria-describedby="usernameHelp" placeholder="Enter username" name="username" value="Yilun">
        <small id="usernameHelp" class="form-text text-muted">This is some descriptive text. Just type your username.</small>
        <label for="password">Password</label>
        <input type="password" class="form-control" id="password" aria-describedby="passwordHelp" placeholder="Enter password" name="password">
        <small id="passwordHelp" class="form-text text-muted">Hint: 234567</small>

        <!--Username:<br>
        <input type="text" name="username" value = "Yilun"><br> //change the uid for different profile
        Password:<br>
        <input type="password" name="password"><br>-->
        <br>
        <input type="submit" class="btn btn-primary" value="Login" name="login"><br>
    </div>
    </form>


     <?php
    include 'phpconfig.php';

    if (isset($_POST['login'])){

      $db     = $psql;
        $result = pg_query($db, "SELECT * FROM users where username = '$_POST[username]'");
        $row = pg_fetch_assoc($result);
        if ($row[userid] == "") {
            echo "Wrong username.";
        } else {
            if ($row[pwd] == $_POST[password]) {
                session_start();
                $_SESSION['userID'] = $row[userid];
                echo "this is". $_SESSION['userID'];
                header("location: profile.php");
            } else {
                echo "Wrong password.";
            }
        }
    }

    ?>

    <!-- Refer to this on how to make a form to insert entries -->
    <h1>Register</h1>
    <p><span class="error">* required field</span></p>
    <form name="registerForm" action="index.php" method="POST">
        Email<span class="error">*</span> <br>
        <input type="text" name="email"> mailname@service.com<br>
        User Name<span class="error">*</span> <br>
        <input type="text" name="name"><br>
        Password<span class="error">*</span> <br>
        <input type="password" name="password"><br>
        Phone<span class="error">*</span> <br>
        <input type="text" name="phone"> 8 digits<br>
        Gender<br>
        <input type="radio" name="gender" value="Male">Male
        <input type="radio" name="gender" value="Female">Female
        <input type="radio" name="gender" value="None" checked ><br>
        Birthday<br>
        <input type="text" name="birthday"> YYYY-MM-DD<br>
        Driver License<br>
        <input type="text" name="driver_license"> 9 digits<br>
        <input type="submit" value="Register" name="registerUser"><br>
    </form>

    <?php

    include 'phpconfig.php';
    $db     = $psql;
    // damn annoying because all fields must be fields
    if (isset($_POST['registerUser'])){
        /*echo "You have selected :".$_POST['gender'];
        echo "<br>"; 
        $gender = ($_POST[gender] == "None") ? "null" : "'$_POST[gender]'";
        echo $gender;
        echo "<br>";*/ 
        $gender = ($_POST[gender] == "None") ? "null" : "'$_POST[gender]'";
        $uniqueId = uniqid();
        $_POST[birthday] = !empty($_POST[birthday]) ? "'$_POST[birthday]'" : "null";
        $_POST[driver_license] = !empty($_POST[driver_license]) ? "'$_POST[driver_license]'" : "null";

        $result = pg_query($db, "INSERT INTO users (email, userid, username, pwd, phone, gender, bday, driverLicense)
                                      VALUES ('$_POST[email]', '$uniqueId', '$_POST[name]', '$_POST[password]', '$_POST[phone]', $gender, $_POST[birthday], $_POST[driver_license])
                                ");
        if (!$result) {
            $failedresult = pg_send_query($db,  "INSERT INTO users (email, userid, username, pwd, phone, gender, bday, driverLicense)
                                                      VALUES ('$_POST[email]', '$uniqueId', '$_POST[name]', '$_POST[password]', '$_POST[phone]', $gender, $_POST[birthday], $_POST[driver_license])
                                            ");
            echo pg_result_error(pg_get_result($db));
            echo "<br>";
            echo "Registration failed!";
        } else {
            echo "Registration successful!";
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
    include 'phpconfig.php';
    $db     = $psql;
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




    </div>


</body>

</html>
