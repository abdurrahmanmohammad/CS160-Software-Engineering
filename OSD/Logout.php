<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/DatabaseSecurityMethods.php'; // Directory of file
destroy_session_and_data(); // Destroy session and go to sign in page
echo "You have been successfully logged out!";
header('Refresh: 3; URL=Signin.php');