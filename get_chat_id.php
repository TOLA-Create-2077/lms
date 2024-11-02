<?php
// Replace with your bot's API token
$token = '7134281925:AAGKHJmwYGOsVOtNhBci74FZedERUlSMfgE';
// Set up the URL to get updates
$url = "https://api.telegram.org/bot{$token}/getUpdates";
// Initialize cURL session
$ch = curl_init($url);
// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Execute the cURL session and store the response
$response = curl_exec($ch);
// Close cURL session
curl_close($ch);
// Decode the JSON response
$data = json_decode($response, true);
// Check if the response contains updates
if (isset($data['result'])) {
    echo "Recent Chat IDs:<br>";
    foreach ($data['result'] as $update) {
        // Check if the 'message' key exists and extract chat ID
        if (isset($update['message']['chat']['id'])) {
            $chatID = $update['message']['chat']['id'];
            echo "Chat ID: " . $chatID . "<br>";
        }
    }
} else {
    echo 'No updates found.';
}
?>
