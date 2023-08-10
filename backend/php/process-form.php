<?php

// Enable the display of runtime errors for debugging purposes.
// This is useful during development to catch and fix errors early.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Set the error reporting level to report all errors.
// E_ALL will catch all PHP errors, warnings, and notices.
error_reporting(E_ALL);

// Database connection parameters.
$server = "localhost";
$username = "id21118998_ppaupallares";
$password = "";
$dbname = "id21118998_db_hosting";


// Create a new connection to the MySQL database using the provided parameters.
// The '3307' at the end is the port number. MySQL's default port is usually 3306. 
$conn = new mysqli($server, $username, $password, $dbname, 3307);

// Check if the connection was successful.
// If there was a connection error, terminate the script and show the error message.
if ($conn->connect_error) {
    die("Conexion failed: " . $conn->connect_error);
}

// Check if the server has received a POST request.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Retrieve from the POST request. If 'interests' is an array, convert it to a comma-separated string.
    $interests = is_array($_POST["interests"]) ? implode(", ", $_POST["interests"]) : $_POST["interests"];
    $education = $_POST["education"];
    $studyAvailability = $_POST["study-availability"];
    $workAvailability = $_POST["availability"];
    $futureGoals = $_POST["goals"];

    // Combine the data into an associative array.
    $data = array(
        'interests' => $interests,
        'education' => $education,
        'study_availability' => $studyAvailability,
        'work_availability' => $workAvailability,
        'goals' => $futureGoals
    );

    // Set the API URL where the data will be sent.
    $apiUrl = '/127.0.0.1:8000/recommend';

    // Initialize cURL session to send a POST request.
    $curl = curl_init();

    // Set cURL options to make a POST request with the data to the API.
    curl_setopt($curl, CURLOPT_URL, $apiUrl);
    curl_setopt($curl, CURLOPT_POST, 1);
    $data_json = json_encode($data);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_json);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    // Execute the cURL session and get the response.
    $response = curl_exec($curl);

    // Check if the cURL request resulted in a 405 error
    $curlInfo = curl_getinfo($curl);
    if ($curlInfo['http_code'] == 405) {
        echo "HTTP 405 Error: Method Not Allowed.<br>";
        echo "Response: " . $response . "<br>";
        exit();
    }   

    // Prepare a SQL statement to insert the form data into the 'answers' table in the database.
    $stmt = $conn->prepare("INSERT INTO answers (interests, education, study, job, goals) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $interests, $education, $studyAvailability, $workAvailability, $futureGoals);


    $outputMessage = "";


    // Execute the SQL statement and check for errors.
    if (!$stmt->execute()) {
        $outputMessage = "Error: " . $stmt->error;
    } else {
        $outputMessage = "Data saved in database.<br>";
    }

    // If there's an error with the cURL request, display it.
    if (curl_errno($curl)) {
        header("Location: badresult.html");
        exit();
    } else {
        // Decode the JSON response from the API.
        $result = json_decode($response, true);
         // If the response contains a recommendation, redirect to the results page with the recommendation.
        if ($result && isset($result['recommendation'])) {
            $recommendation = $result['recommendation'];
            header("Location: result.html?recommendation=" . urlencode($recommendation));
            exit();
        } else {
            // Si la respuesta no contiene una recomendación, redirige a badresult.html.
            header("Location: badresult.html");
            exit();
        }
    }

    if ($outputMessage) {
        echo $outputMessage;
    }

    // Close the SQL statement, the database connection, and the cURL session.
    $stmt->close();
    $conn->close();
    curl_close($curl);
}