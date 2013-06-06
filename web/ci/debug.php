<?php
header("Cache-Control: must-revalidate, max-age=300");
header("Vary: Accept-Encoding");
echo time()."<br>";
?>