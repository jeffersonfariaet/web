<?php
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "myDB";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Cuando se envía el formulario de registro
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST["username"];
  $password = $_POST["password"];

  $sql = "INSERT INTO Users (username, password)
  VALUES ('$username', '$password')";

  if ($conn->query($sql) === TRUE) {
    echo "Usuario registrado con éxito";
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<body>

<h2>Formulario de registro</h2>

<form method="post" action="<?php echo $_SERVER["PHP_SELF"];?>">
  Username: <input type="text" name="username">
  <br>
  Password: <input type="password" name="password">
  <br>
  <input type="submit">
</form>

</body>
</html>
