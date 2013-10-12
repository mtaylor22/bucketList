<?php
require 'functions.php';
connect();
$jar = getJar('school');
print $jar[0]['idea'];
?>