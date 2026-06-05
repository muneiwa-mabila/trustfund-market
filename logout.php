<?php

session_start();

/* DESTROY SESSION */

session_unset();

setcookie(

    "remember_user",

    "",

    time() - 3600,

    "/"

);

session_destroy();

/* GO BACK TO HOMEPAGE */

header("Location: index.php");

exit();

?>