<?php 
 include "conn.php";
  session_destroy();
  session_unset();
  header("Location: index.php");
  exit();
?>