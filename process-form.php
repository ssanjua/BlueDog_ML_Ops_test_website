<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$server = "localhost";
$username = "id21118998_ppaupallares";
$password = "NEVERmind38@";
$dbname = "id21118998_db_hosting";

$conn = new mysqli($server, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $interests = is_array($_POST["interests"]) ? implode(", ", $_POST["interests"]) : $_POST["interests"];
    $education = $_POST["education"];
    $studyAvailability = $_POST["study-availability"];
    $workAvailability = $_POST["availability"];
    $futureGoals = $_POST["goals"];

    $data = array(
        'interests' => $interests,
        'education' => $education,
        'study_availability' => $studyAvailability,
        'work_availability' => $workAvailability,
        'goals' => $futureGoals
    );

    $apiUrl = 'http://127.0.0.1:8000/recommend';

    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $apiUrl);
    curl_setopt($curl, CURLOPT_POST, 1);
    $data_json = json_encode($data);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_json);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $response = curl_exec($curl);

    $stmt = $conn->prepare("INSERT INTO answers (interests, education, study, job, goals) VALUES (?, ?, ?, ?, ?)");
    if ($stmt === false) {
        die("Error preparando la consulta: " . $conn->error);
    }
    $stmt->bind_param("sssss", $interests, $education, $studyAvailability, $workAvailability, $futureGoals);

    if (!$stmt->execute()) {
        echo "Error: " . $stmt->error;
    } else {
        echo "Datos guardados exitosamente.<br>";
    }

    if (curl_errno($curl)) {
        echo 'Error al hacer la solicitud al API del modelo: ' . curl_error($curl);
    } else {
        $result = json_decode($response, true);
        if ($result && isset($result['recommendation'])) {
            $recommendation = $result['recommendation'];
            header("Location: result.html?recommendation=" . urlencode($recommendation));
            exit();
        } else {
            echo "Hubo un error al obtener la recomendación.";
        }
    }

    $stmt->close();
    $conn->close();
    curl_close($curl);
}
