<?php
session_start();
session_destroy();
header("UserInterfaceModel/index.php");
?>