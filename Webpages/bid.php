<!DOCTYPE html>
<head>
  <title>UPDATE PostgreSQL data with PHP</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <style>li {list-style: none;}</style>
</head>
<body>

  <h1>Ride bidding info</h1>

  <?php
  // Connect to the database. Please change the password in the following line accordingly
  $db     = pg_connect("host=localhost port=5432 dbname=Project1 user=postgres password=A0158271A");
  $result = pg_query($db, "with bidcount as (
  SELECT ridesid, status, min(price) as minprice, count(*) as numbids
  FROM bids
  Where status = '0'
  GROUP By ridesid, status
  )

  SELECT R.rideid, R.dates, R.times, R.origin, R.destination, R.baseprice, R.capacity, coalesce(B.numbids,0) as numbidders, coalesce(B.minprice, 0) as minbid
  FROM rides R full outer join bidcount B on (R.rideid = B.ridesid)
  Order by R.dates, R.times, R.origin, R.destination, minbid
  ;

  ");
  // print out table of bids

  $i = 0;
  echo '<table><tr>';
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

  <h1> Bid for a ride </h1>


  <form name="form1" id="form1" action="" method="POST">
    <select name='rideid' id = 'rideid' >
      <option value="">--- Select Ride ID ---</option>
      <?php
      // This is the db to connect to
      $db     = pg_connect("host=localhost port=5432 dbname=Project1 user=postgres password=A0158271A");
      $result = pg_query($db, "SELECT rideid FROM rides");		// Query template

      while ($row = pg_fetch_array($result)) {
        echo "<option value='" . $row['rideid'] ."'>" . $row['rideid'] ."</option>";
      }
      ?>
    </select>
    <input type="submit" value="Select Ride ID" name="submitRideId"><br>
  </form>

  <?php
  // This is the db to connect to
  $db     = pg_connect("host=localhost port=5432 dbname=Project1 user=postgres password=A0158271A");
  $result = pg_query($db, "SELECT * FROM rides where rideid = '$_POST[rideid]'");		// Query template
  $row    = pg_fetch_assoc($result);		// To store the result row

  if (isset($_POST['submitRideId'])) { // User will have to key in userID for now. Will get UserID from login in the future.
    echo "
    <form name='bid' method='POST'>
    RideID:<br>
    <input type='text' name='rideid' value='$row[rideid]'><br>
    Date:<br>
    <input type='text' name='dates' value='$row[dates]'><br>
    Origin:<br>
    <input type='text' name='origin' value='$row[origin]'><br>
    Destination:<br>
    <input type='text' name='destination' value='$row[destination]'><br>
    Your bid:<br>
    <input type='text' name='bid' value='--Enter your bid--'><br>
    UserID:<br>
    <input type='text' name='userid' value='--Enter your ID--'><br>
    <input type='submit' name='new'><br>
    </form>
    ";
  }
  if (isset($_POST['new'])) {	// Submit the update SQL command, update if user has already bid for ride, else insert.
    $result = pg_query($db, " UPDATE bids SET price = '$_POST[bid]' WHERE usersid = '$_POST[userid]' and ridesid = '$_POST[rideid]' ;
      INSERT INTO bids
      SELECT ('$_POST[userid]', '$_POST[rideid]', '$_POST[bid]', 0, null)
      WHERE NOT EXISTS (SELECT 1 FROM bids WHERE usersid = '$_POST[userid]' and ridesid = '$_POST[rideid]');");
      if (!$result) {
        echo "Bid failed!!";
      } else {
        echo "Bid successful!";
      }
  }

  ?>



  </body>
</html>
