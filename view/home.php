<link rel="stylesheet" type="text/css" href="Styles.css">
<!DOCTYPE html>
<html>
<body>

<h1> <p class='logo'> Flight search engine </p> </h1>

<form action="home.php" method="POST">
  
<label for="departure airport"> <p class='userinput'> Departure airport:</label>
<input type="text" id="deptAirport" name="deptAirport" required>

<label for="fname"> Destination airport:</label>
<input type="text" id="destAirport" name="destAirport" required>

<label for="earliest departure"> Earliest departure:</label>
<input type="date" id="earlyDept" name="earlyDept" min="<?= date('Y-m-d'); ?>" required>

<label for="Latest departure"> Latest return:</label>
<input type="date" id="lateReturn" name="lateReturn" min="<?= date('Y-m-d'); ?>" required>

    <label for="Day to depart on"> Departing weekday:</label>
    <select name="departDay" id="departDay" required>
        <option value="Monday">Monday</option>
        <option value="Tuesday">Tuesday</option>
        <option value="Wednesday">Wednesday</option>
        <option value="Thursday">Thursday</option>
        <option value="Friday">Friday</option>
        <option value="Saturday">Saturday</option>
        <option value="Sunday">Sunday</option>
    </select>

    <label for="Day to return on" > Returning weekday:</label>
    <select name="returnDay" id="returnDay" required>
        <option value="Monday">Monday</option>
        <option value="Tuesday">Tuesday</option>
        <option value="Wednesday">Wednesday</option>
        <option value="Thursday">Thursday</option>
        <option value="Friday">Friday</option>
        <option value="Saturday">Saturday</option>
        <option value="Sunday">Sunday</option>
    </select>
    <br>
    <input type="submit" value="Submit">

</form>

</html>

<script>
    var earliest = document.getElementById("earlyDept");
    var latest = document.getElementById("lateReturn");

    earliest.addEventListener("input", function() {

        if (earliest.value >= latest.value) {
            latest.value = earliest.value;
        }
    });
    latest.addEventListener("input", function() {

        if (latest.value <= earliest.value) {
            latest.value = earliest.value;
        }
    });
</script>

<?php
echo " <p class='flights'>";
$api_key = "319228101f366e4728ea650dcfc9cf21";

if(isset($_POST['deptAirport'])){
    $origin = $_POST['deptAirport'];
    $destination = $_POST['destAirport'];
    $return_date = date_create_from_format('Y-m-d', $_POST['lateReturn']);
    $depart_day = $_POST['departDay'];
    $return_day = $_POST['returnDay'];


$depart_found = false;
$return_found = false;


    $depart_date = date_create_from_format('Y-m-d', $_POST['earlyDept']);

    for ($date = $depart_date; $date <= $return_date; $date->modify('+1 day')) {
        if (!$depart_found && $date->format('l') == ucwords($depart_day)) {
            $depart = new DateTime();
            $depart = $date->format('Y-m-d');
            $depart_found = true;
        }
        elseif (!$return_found && $date->format('l') == ucwords($return_day)) {
            $return = new DateTime();
            $return = $date->format('Y-m-d');
            $return_found = true;
        }
        if ($depart_found && $return_found) {
            $flight_data = apiCall($origin, $destination, $depart, $return);
            if ($flight_data == "") {
                echo "No flights matching this criteria found";
            } else {
                echo $flight_data;
            }
            $return_found = false;
            $depart_found = false;
        }

    }
    echo "</p>";


}

function apiCall($origin, $destination, $depart, $return)
{

    $url = "https://api.travelpayouts.com/v1/prices/cheap?origin=$origin&destination=$destination&depart_date=$depart&return_date=$return&currency=gbp&token=319228101f366e4728ea650dcfc9cf21";

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_URL, $url);

    $result = json_decode(curl_exec($ch));
    $error = curl_error($ch);

    $str = "";
    if ($error) {
        $str = $error;
    } else {
        echo " <p class='flights'>";
        if (empty($result->data->$destination)) {
            $str = "";
        } else {
            $flights = $result->data->$destination;
            foreach ($flights as $flight) {
                $str .= " Flight: " . $flight->airline . $flight->flight_number;
                $str .= " Outbound: " . $flight->departure_at;
                $str .= " Return: " . $flight->return_at;
                $str .= " Price: " . $flight->price;
                $str .= "<br> <br>";

            }
        }

    }

    return $str;

}

?>

</body>


</html>


