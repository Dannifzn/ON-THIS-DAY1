<?php
// Ganti kata 'rahasia' dengan password yang kamu mau
$password_kalian = "rahasia"; 

// Ini akan menghasilkan kode acak
echo password_hash($password_kalian, PASSWORD_DEFAULT);
?>
