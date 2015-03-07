# lib
lib.php in all in one library that needed for web programming, using MySQL as database and using artisteer 4 for interface design.

Step 1
rename index.html to index.php

Step 2
add this script at top of your index.php
<?php 
  session_start();
?>

Step 3
replace nav menu to:
<?php
  menu();
?>

Step 4
replace content of your site with this:
<?php
  crud();
?>



Enjoy this lib.php

:-)
