<!DOCTYPE html>
<head>
  <title>Ride bidding</title>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <style>
  .error {
    color: #FF0000;
  }

  .selected {
    background-color: coral;
    color: #FFF;
  }

  li {
    list-style: none;
  }
  </style>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css" integrity="sha384-Zug+QiDoJOrZ5t4lssLdxGhVrurbmBWopoEl+M6BdEfwnCJZtKxi1KgxUyJq13dy" crossorigin="anonymous">
</head>
<body>
  <nav class="navbar navbar-dark" style="background-color: coral;">
    <a class="navbar-brand mx-auto" href="index.php">
      Snatch
    </a>
    <a class="button" href="profile.php">
      Back to Profile
    </a>

  </nav>


  <div>

    <br><h1 style="text-align: center">Ride bidding info</h1> <br>
    <h4 style="text-align: center"> Click on a ride to bid for it </h4> <br>

    <?php
    // Connect to the database. Please change the password in the following line accordingly

    include 'phpconfig.php';
    session_start();
    $email = $_SESSION['userID'];
    $db     = $psql;
    $result = pg_query($db, "
    with bidrank as (
      SELECT rideid, capacity, price, RANK() OVER(PARTITION BY rideid ORDER BY price) AS rnk
      FROM bids inner join rides on bids.ridesid = rides.rideid
    ),
    bidcount as (
      SELECT ridesid, status, count(*) as numbids
      FROM bids
      Where status = '0'
      GROUP By ridesid, status
    ),
    biddiff as (
      SELECT ridesid, sum(numbids - capacity) as diff
      FROM bidcount inner join rides on ridesid = rideid
      group by ridesid
    ),
    validbids as (
      SELECT *
      FROM biddiff inner join bidrank on ridesid = rideid
      WHERE rnk > diff
    ),
    minbidtable as (
      SELECT rideid, capacity, price, rnk
      FROM (
          SELECT rideid, capacity, price, rnk, row_number()
                 over (partition by rideid order by rnk asc) as rn
          FROM validbids
      ) AS T
      WHERE rn = 1
    ),

    bidcountmin as (
      SELECT ridesid, status, price as minprice, numbids
      FROM bidcount inner join minbidtable on rideid = ridesid
    ),

    ridesexceptown as (
      Select rideid, dates, times, origin, destination, baseprice, capacity, sidenote
      FROM rides inner join drives on rideid = ridesid AND expiry = 1
      WHERE email <> '$email'
    )
    SELECT *
    FROM (
    SELECT R.rideid, R.dates, R.times, R.origin, R.destination, R.baseprice, R.capacity, coalesce(B.numbids,0) as numBids, coalesce(B.minprice, 0) as minBid, R.sidenote
    FROM ridesexceptown R full outer join bidcountmin B on (R.rideid = B.ridesid)
    Order by R.dates, R.times, R.origin, R.destination, minbid) as R
    WHERE rideid <> 'null'
    ;

    ");
    // print out table of bids
    echo 'Origin:<input type="text" class="form-control" id="myOrigin" onkeyup="bidFilter()" placeholder="Filter by origin..">';
    echo 'Destination:<input type="text" class="form-control" id="myDest" onkeyup="bidFilter()" placeholder="Filter by destination.."> <br>';


    echo '<table id = "bidTable" class= "table"><tr>';
    echo '<td> Ride ID </td>';
    echo '<td> Date </td>';
    echo '<td> Time </td>';
    echo '<td> Origin </td>';
    echo '<td> Destination </td>';
    echo '<td> Base Bidding Price </td>';
    echo '<td> Ride Capacity </td>';
    echo '<td> Number of Bids </td>';
    echo '<td> Current Lowest Bid </td>';
    echo '<td> Side Notes </td>';

    echo '</tr>';
    $i = 0;

    while ($row = pg_fetch_row($result))
    {
      echo '<tr>';
      $count = count($row);
      $y = 0;
      while ($y <= $count)
      {
        if( $y < $count) {
          $c_row = current($row);
          echo '<td>' . $c_row . '</td>';
          next($row);
        }
        else {
          next($row);
        }
        $y = $y + 1;
      }
      echo '</tr>';
      $i = $i + 1;
    }
    pg_free_result($result);

    echo '</table>';

    ?>
  </div>
  <div style="text-align: center">



    <!-- <form name="form1" id="form1" action="" method="POST">
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
</form> -->

<?php
// This is the db to connect to
include 'phpconfig.php';
$db     = $psql;
session_start();
$email = $_SESSION['userID'];
$id = strval($_GET['id']);
if($id != null){
  $result = pg_query($db, "SELECT * FROM rides where rideid = '$id'");
}
else{
  $result = pg_query($db, "SELECT * FROM rides where rideid = '$_POST[rideid]'");

}
$row    = pg_fetch_assoc($result);		// To store the result row
$location_id = filter_input(INPUT_POST, 'locationID', FILTER_SANITIZE_NUMBER_INT);


if (isset($_POST['submitRideId']) || $id != null ) {

  echo "
  <form name='bid' method='POST'>
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
    $sidenote = ($_POST[sidenote] == "") ? null : $_POST[sidenote];
    $result = pg_query($db, " UPDATE bids SET price = '$_POST[bid]', sidenote = '$sidenote' WHERE emails = '$email' and ridesid = '$_POST[rideid]' ;
      INSERT INTO bids
      SELECT '$email', '$_POST[rideid]', '$_POST[bid]', 0, '$sidenote'
      WHERE NOT EXISTS (SELECT 1 FROM bids WHERE emails = '$email' and ridesid = '$_POST[rideid]');");
      if (!$result) {
        echo "Bid failed!!";
      } else {
        header("Refresh:0; url=bid.php");
        $message = "Bid successful!";
        echo "<script type='text/javascript'>alert('$message');</script>";
      }
    } else {
      echo "Bid failed. Bid price lower than base price!";
    }
  }

  ?>
</div>



</body>
<script>
function bidFilter() {

  var input, filter, table, tr, td, i;
  input = document.getElementById("myOrigin");
  input2 = document.getElementById("myDest");
  filter = input.value.toUpperCase();
  filter2 = input2.value.toUpperCase();
  table = document.getElementById("bidTable");
  tr = table.getElementsByTagName("tr");

  // Loop through all table rows, and hide those who don't match the search query
  for (i = 1; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[3];
    td2 = tr[i].getElementsByTagName("td")[4];
    if (td) {
      if (td.innerHTML.toUpperCase().indexOf(filter) > -1 && td2.innerHTML.toUpperCase().indexOf(filter2) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}

$("#bidTable tr").click(function(){
  $(this).addClass('selected').siblings().removeClass('selected');
  var value=$(this).find('td:first').html();
  window.location = window.location.pathname + "?id=" + value;

});








</script>
</html>
