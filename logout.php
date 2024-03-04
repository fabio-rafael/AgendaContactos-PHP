<?php
<<<<<<< HEAD
=======

>>>>>>> 1fde340b54337ff7f8eb4b3988330c62046f9d13
session_start();

$_SESSION = array();

// Destroy the session
session_destroy();


header("Location: login.php");
exit();
