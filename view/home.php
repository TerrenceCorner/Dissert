<link rel="stylesheet" type="text/css" href="Styles.css">
<!DOCTYPE html>
<html>


<body>

<h1> <p class='logo'> Flight search engine </p> </h1>

<form action="home.php" method="POST">
  
<label for="departure airport"> <p class='userinput'> Departure airport:</label>
<input type="text" id="deptAirport" name="deptAirport">

<label for="fname"> Destination airport:</label>
<input type="text" id="destAirport" name="destAirport"> 

<label for="earliest departure"> Earliest departure date:</label>
<input type="date" id="earlyDept" name="earlyDept" min="<?= date('Y-m-d'); ?>">

<label for="Latest departure"> Latest departure date:</label>
<input type="date" id="lateReturn" name="lateReturn" min="<?= date('Y-m-d'); ?>">

    <label for="Day to depart on"> Departing weekday:</label>
    <select name="departDay" id="departDay">
        <option value="monday">Monday</option>
        <option value="tuesday">Tuesday</option>
        <option value="wednesday">Wednesday</option>
        <option value="thursday">Thursday</option>
        <option value="friday">Friday</option>
        <option value="saturday">Saturday</option>
        <option value="sunday">Sunday</option>
    </select>

    <label for="Day to return on"> Returning weekday:</label>
    <select name="returnDay" id="returnDay">
        <option value="monday">Monday</option>
        <option value="tuesday">Tuesday</option>
        <option value="wednesday">Wednesday</option>
        <option value="thursday">Thursday</option>
        <option value="friday">Friday</option>
        <option value="saturday">Saturday</option>
        <option value="sunday">Sunday</option>
    </select>

    <input type="submit" value="Submit">

    </p>

</form>

</html>

<?php
$api_key = "319228101f366e4728ea650dcfc9cf21";

//The beginning of this list should contain the most important features - firstly including basic 
// information about each as the user must know the details of the flight they are taking. This 
// should include departure and return date, departure and destination airport, airline/airlines, 
// duration. The second most important features are features/factors highlighted in research of 
// determinant factors in passenger flight choice and research of existing flight search engines. 
// These should be implemented in the order, price, baggage, class, inflight Wi-Fi as these 
// were found in both these sections of research. Knowing that competitors use similar criteria 
// in their searches to allow their users to find specific flights is extra confirmation that 
// passengers use these factors to determine which flight they eventually book and thus must 
// be included. The rest of the list should include, “promotions”, “extra charges”, “flexibility for 
// ticket changes or cancellations” and “discounts” “reliability of baggage services”, “handling of 
// passenger complaints”, “seats”, “food and beverage service”, “service attitude of check in 
// crew”, “flight cabin”, “duty-free merchandising”, “direct flights”, “reputation of safety” and 
// “security of flights”, “convenience of the schedule”, “daily and weekly frequency of flights” 
// and “convenience of the flight connections” as these were the remainding important factors 
// in research by Kucukaltan and Topcu  (2019). The priority list allows for development to skip 
// past a criterion to the next most important feature/factor to implement if any of these are not 
// implementable which prevents time being wasted attempting to produce features which 
// cannot be added to the project.
  // Set the endpoint URL

$origin = "";
$destination = "";
$depart_date = "";
$return_date = "";


if(isset($_POST['deptAirport'])){
    $origin = $_POST['deptAirport'];
}

if(isset($_POST['destAirport'])) {
    $destination = $_POST['destAirport'];
}

if(isset($_POST['earlyDept'])) {
    $depart_date = $_POST['earlyDept'];
}

if(isset($_POST['lateReturn'])) {
    $return_date = $_POST['lateReturn'];
}



$url = "https://api.travelpayouts.com/v1/prices/cheap?origin=$origin&destination=$destination&depart_date=$depart_date&return_date=$return_date&currency=gbp&token=319228101f366e4728ea650dcfc9cf21";

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$data = curl_exec($ch);
$result = json_decode($data);
$error = curl_error($ch);

curl_close($ch);

echo "<br>";
if ($error) {
    echo $error;
} else {
    if(empty($result->data->LON)) {
        echo "No flights found matching this criteria";
    }
    else {
        $flights = $result->data->$destination;
        foreach($flights as $flight) {
            echo " <p class='flights'> Flight: " . $flight->airline . $flight->flight_number;
            echo " Departure: " . $flight->departure_at;
            echo " Return: " . $flight->return_at;
            echo " Price: " . $flight->price;
            echo " </p> ";
        }}

}

?>

</body>


</html>


