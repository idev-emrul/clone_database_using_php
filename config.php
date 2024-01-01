<?php
// Define configuration settings
$config = [
    'servername' => 'localhost',
    'username' => 'root',
    'password' => ''
    // Add more settings as needed...
];

// Optionally, define constants for easy access
define('DB_SERVER', $config['servername']);
define('DB_USERNAME', $config['username']);
define('DB_PASSWORD', $config['password']);
// Define more constants as needed...

return $config; // Return the configuration array
?>
