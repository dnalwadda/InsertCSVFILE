<?php
/**
 * This script reads the CSV data file and imports it into the "hub_monitoring_comm_ug" table in the "hwcoredata" database.
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

include 'dbconfig2.php'; // Include connection commands and credentials for the "hwcoredata" database

$pdo = pdo_connect_mysql();
$msg = '';

//A function for cleaning user text input
function clean($string) {
    $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
 
    return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special characters.
}

// Check if the user has issued the import command in the UI
if (isset($_POST['save_excel_data'])) {
    $fileName = $_FILES['import_file']['name'];
    $file_ext = pathinfo($fileName, PATHINFO_EXTENSION);
    $allowed_ext = ['csv'];

    if (in_array($file_ext, $allowed_ext)) {
        $inputFileNamePath = $_FILES['import_file']['tmp_name'];

        // Load CSV data into an array
        $data = array_map('str_getcsv', file($inputFileNamePath));

        // Assuming the first row contains headers
        $header = $data[0];
        unset($data[0]);

        // Initialize counters
        $count = 0;
        $maxRows = count($data);

        // Prepare and execute SQL query
        $query = "INSERT INTO hub_monitoring_comm_ug (start_time, end_time, phone_number, username, email, hub_info_hub, date, hub_community_hub_com_tabletavailable, hub_community_hub_com_notablets, hub_community_hub_com_agemin, hub_community_hub_com_agemax, hub_community_hub_training_hub_trainings, hub_community_hub_training_hub_training_number, sim_serial, days, hub_info_region, published, hub_community_hub_training_Mentorship_trainings, hub_community_hub_training_Mentorship_trainings_women, hub_community_hub_training_Mentorship_trainings_children, hub_community_hub_training_Mentorship_trainings_men, hub_community_hub_training_Mentorship_trainings_pwds, hub_community_hub_training_life_skills_trainings_lifeskills, hub_community_hub_training_life_skills_trainings_lifeskillswomen, hub_community_hub_training_life_skills_trainings_children, hub_community_hub_training_life_skills_trainings_lifeskillsmen, hub_community_hub_training_life_skills_trainings_pwds) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($query);

        foreach ($data as $row) {
            if (count($row) === count($header)) {
               // $dateValue = date("Y-m-d", strtotime($row[7])); // Convert date format
              //  $stmt->execute(array_merge([$dateValue], $row));
                $count++;
            }
        }

        $_SESSION['message'] = "$count/$maxRows Records Successfully Imported";
        header('Location: index.php');
        exit();
    } else {
        $_SESSION['message'] = "Invalid File Format. Only CSV files are allowed.";
        header('Location: index.php');
        exit();
    }
} else {
    $_SESSION['message'] = "Invalid Request";
    header('Location: index.php');
    exit();
}
?>



