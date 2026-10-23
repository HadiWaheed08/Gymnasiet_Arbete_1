<?php
// Initierar sessionhantering
session_start();
// Raderar all information i sessionvariabler
session_unset();
// Avslutar den aktuella sessionen
session_destroy();
// Skickar en användare till startsidan
header('Location: index.php');
