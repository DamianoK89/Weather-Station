<?php
    include_once 'db.php';
    if($_SERVER["REQUEST_METHOD"] == "GET")
    {   
        $date = date("Y.m.d");
        $time = date("H:i");
        $temperature = $_GET['temperature'];
        $humidity = $_GET['humidity'];
        $pressure = $_GET['pressure'];
        $sql = "INSERT INTO measurement (date, time, temperature, humidity, pressure) VALUES ('$date', '$time', '$temperature', '$humidity', '$pressure')";
        if (mysqli_query($conn, $sql)) {
            echo "New record has been added successfully!";
        } else {
            echo "Error: " . $sql . ":-" . mysqli_error($conn);
        }
        mysqli_close($conn);
    }
    //header("Location: http://swiat-wirtualny.cba.pl/weather.html");
    //$Message = '<script>alert("Data = ' . $date . ', Czas = ' . $time . '");</script>';
    //echo $Message;
    
?>