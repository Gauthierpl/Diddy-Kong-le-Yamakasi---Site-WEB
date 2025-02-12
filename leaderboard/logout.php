<?php
session_start();
session_unset();   // supprime les variables de session
session_destroy(); // détruit la session
header("Location: leaderboard.php"); 
exit;
?>
