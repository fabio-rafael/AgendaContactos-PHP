<?php
session_start();
<<<<<<< HEAD
=======

>>>>>>> 1fde340b54337ff7f8eb4b3988330c62046f9d13
if (!isset($_SESSION['usuario_id'])) {
  header("Location: login.php");
  exit();
}
