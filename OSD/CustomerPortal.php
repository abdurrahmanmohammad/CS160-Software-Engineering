<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/Data Layer/DatabaseSecurityMethods.php'; // Load methods for error and sanitization

/** Authenticate user on page */
$account = authenticate();