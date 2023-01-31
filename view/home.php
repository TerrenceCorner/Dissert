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
    <select name="departDay" id="departDay">
        <option disabled selected value> day of departure </option>
        <option value="Monday">Monday</option>
        <option value="Tuesday">Tuesday</option>
        <option value="Wednesday">Wednesday</option>
        <option value="Thursday">Thursday</option>
        <option value="Friday">Friday</option>
        <option value="Saturday">Saturday</option>
        <option value="Sunday">Sunday</option>
    </select>

    <label for="Day to return on" > Returning weekday:</label>
    <select name="returnDay" id="returnDay">

        <option disabled selected value> day of return </option>
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

    function dateConversion(date) {
        var date = new Date(date.value);
        date = date.toLocaleDateString().split("/").reverse().join("-");
        return new Date(date);

    }

    function getDateDifference(earliestObj, latestObj) {
        var diff = latestObj.getTime() - earliestObj.getTime();
        return diffDays = diff / (1000 * 60 * 60 * 24);
    }

    function resetDays(){
        for (var i = 0; i < 8; i++) {
            document.getElementById("returnDay").options[i].disabled = false;
            document.getElementById("departDay").options[i].disabled = false;
        }

    }

    function turnOffDays(){
        for (var i = 0; i < 8; i++) {
            document.getElementById("returnDay").options[i].disabled = true;
            document.getElementById("departDay").options[i].disabled = true;
        }

    }

    function correctReturnDays() {
        var earliestObj = dateConversion(earliest);
        var latestObj = dateConversion(latest);
        var diffDays = getDateDifference(earliestObj, latestObj);

        if (diffDays < 7) {
            diffDays = getDateDifference(earliestObj, latestObj);

            var day = earliestObj.getUTCDay();
            var i = 0;
            while (i <= diffDays) {
                document.getElementById("returnDay").options[day].disabled = false;
                day++;
                if (day === 8) {
                    day = 1;
                }
                i++;
            }
        } else {
            resetDays();
        }
    }

    function correctDepartDays() {
        var earliestObj = dateConversion(earliest);
        var latestObj = dateConversion(latest);
        var diffDays = getDateDifference(earliestObj, latestObj);
        if (diffDays < 7) {
            diffDays = getDateDifference(earliestObj, latestObj);
            var day = earliestObj.getUTCDay();
            var i = 0;
            while (i <= diffDays) {
                document.getElementById("departDay").options[day].disabled = false;
                day++;
                if (day === 8) {
                    day = 1;
                }
                i++;
            }
        } else {
            resetDays();
        }
    }

    function correctDays() {
        turnOffDays();

        correctReturnDays();

        correctDepartDays();
    }

    function forceDepartBeforeReturn() {
        forceDepartBeforeReturnDate();
        forceDepartBeforeReturnDay();
    }

    function forceDepartBeforeReturnDate() {
        if (latest.value <= earliest.value) {

            latest.value = earliest.value;
        }
    }

    function dayToInt(day) {
        if (day === "Monday") {
            var num = 1;
        }
        if (day === "Tuesday") {
            var num = 2;
        }
        if (day === "Wednesday") {
            var num = 3;
        }
        if (day === "Thursday") {
            var num = 4;
        }
        if (day === "Friday") {
            var num = 5;
        }
        if (day === "Saturday") {
            var num = 6;
        }
        if (day === "Sunday") {
            var num = 7;
        }

        return num;
    }

    function forceDepartBeforeReturnDay() {
        var i = 1;
        var earliestInt = dayToInt(document.getElementById("departDay").value);
        var earliestObj = dateConversion(earliest);
        var latestObj = dateConversion(latest);
        var diffDays = getDateDifference(earliestObj, latestObj);
        if (diffDays < 7 && earliestInt >= 1 && earliestInt <=7) {
            while (i < earliestInt) {
                document.getElementById("returnDay").options[i].disabled = true;
                i++;
            }
        }
    }

    var earliest = document.getElementById("earlyDept");
    var latest = document.getElementById("lateReturn");
    var earliestDay = dateConversion(document.getElementById("departDay"));
    var latestDay =  dateConversion(document.getElementById("returnDay"));


    earliest.addEventListener("input", function() {

        correctDays();

        forceDepartBeforeReturn();

    });
    latest.addEventListener("input", function() {

        correctDays();

        forceDepartBeforeReturn();

    });

    document.getElementById("departDay").addEventListener("change", function() {

        forceDepartBeforeReturn();

    });

    document.getElementById("returnDay").addEventListener("change", function() {

        forceDepartBeforeReturn();

    });
</script>


<?php
echo " <p class='flights'>";
$api_key = "319228101f366e4728ea650dcfc9cf21";

if(isset($_POST['deptAirport'])){
    $origin = $_POST['deptAirport'];
    $destination = $_POST['destAirport'];
    $return_date = date_create_from_format('Y-m-d', $_POST['lateReturn']);
    $depart_date = date_create_from_format('Y-m-d', $_POST['earlyDept']);
    $depart_day = $_POST['departDay'];
    $return_day = $_POST['returnDay'];

    $depart_found = false;
    $return_found = false;
    $no_data = true;


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
            if ($flight_data != "") {
                $no_data = false;
                echo $flight_data;
            }
            $return_found = false;
            $depart_found = false;
        }

    }

    if ($no_data) {
        echo "No flights matching this criteria found";
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
    echo " <p class='flights'>";
    if ($error) {
        $str = $error;
    } else {
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


