<?php
include("../connection/connection.php");

$sql = "ALTER TABLE ... ";

$query = $connection->prepare($sql);
$query->execute();

echo "Table(s) Updated!";

?>