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
        return new Date(date.toLocaleDateString().split("/").reverse().join("-"));

    }

    function getDateDifference(earliestObj, latestObj) {
        var diff = latestObj.getTime() - earliestObj.getTime();
        return diffDays = diff / (60 * 60 * 24 * 1000);
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
            $depart = date("d.m.Y", strtotime($date->format('Y-m-d')));
            $depart_found = true;
        }
        elseif (!$return_found && $date->format('l') == ucwords($return_day)) {
            $return = new DateTime();
            $return = date("d.m.Y", strtotime($date->format('Y-m-d')));
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
    $ch = curl_init();
    $value = "";
    $url = "https://www.azair.eu/azfin.php?tp=0&searchtype=nonflexi&srcAirport=London+%28Luton%29+%5B$origin%5D&srcTypedText=$origin&srcFreeTypedText=&srcMC=&srcFreeAirport=&dstAirport=Dublin+%5B$destination%5D&dstTypedText=dUBLIN&dstFreeTypedText=&dstMC=&adults=1&children=0&infants=0&minHourStay=0%3A45&maxHourStay=23%3A20&minHourOutbound=0%3A00&maxHourOutbound=24%3A00&minHourInbound=0%3A00&maxHourInbound=24%3A00&dstFreeAirport=&depdate=$depart&arrdate=$return&flex=0&nextday=0&autoprice=true&currency=EUR&wizzxclub=false&flyoneclub=false&blueairbenefits=false&megavolotea=false&schengen=false&transfer=false&samedep=true&samearr=true&dep0=true&dep1=true&dep2=true&dep3=true&dep4=true&dep5=true&dep6=true&arr0=true&arr1=true&arr2=true&arr3=true&arr4=true&arr5=true&arr6=true&maxChng=1&isOneway=return&resultSubmit=Search";

    curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
    ));

    $output = curl_exec($ch);

    $doc = new DOMDocument();
    @$doc->loadHTML($output);

    $xpath = new DOMXPath($doc);

// Use XPath to find the reslist element
    $reslist = $xpath->query('//*[@id="reslist"]')->item(0);

// If the reslist element was found
    if ($reslist) {
        // Loop through each child node of the reslist element
        foreach ($reslist->childNodes as $node) {
            // Do something with each node...

            $flightDetail = explode(" ", $node->nodeValue);

            if (sizeof($flightDetail) > 3) {
                echo $flightDetail[6];
                print_r($flightDetail);
                echo "hey". "<br>";
            }
        }
    } else {
        // Handle error - reslist element not found
        echo "reslist element not found";
    }

    return $value;

}



?>

</body>


</html>


