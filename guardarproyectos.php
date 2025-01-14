<?php
// Conexión a la base de datos
$servername = "localhost"; // Cambiar si tu servidor es diferente
$username = "root"; // Tu nombre de usuario de MySQL
$password = ""; // Tu contraseña de MySQL
$dbname = "plataforma_investigacion"; // Nombre de la base de datos

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos del formulario
    $title = $_POST['project-title'];
    $description = $_POST['project-description'];

    // Verificar si se ha subido un archivo
    if (isset($_FILES['project-file']) && $_FILES['project-file']['error'] == 0) {
        // Directorio donde se guardarán los archivos subidos
        $targetDir = "uploads/";
        $fileName = basename($_FILES["project-file"]["name"]);
        $targetFilePath = $targetDir . $fileName;

        // Mover el archivo desde el directorio temporal a la carpeta de destino
        if (move_uploaded_file($_FILES["project-file"]["tmp_name"], $targetFilePath)) {
            echo "<p>Archivo subido exitosamente.</p>";
        } else {
            echo "<p>Error al subir el archivo.</p>";
            $targetFilePath = null; // Si no se pudo subir el archivo, no guardamos el nombre
        }
    } else {
        $targetFilePath = null; // Si no se subió archivo, no guardar nada
    }

    // Preparar la consulta SQL para insertar el proyecto con el archivo (si existe)
    $sql = "INSERT INTO proyectos (titulo, descripcion, archivo) VALUES (?, ?, ?)";

    // Preparar la sentencia
    $stmt = $conn->prepare($sql);
    // Si hay archivo, lo insertamos, sino, insertamos NULL
    $stmt->bind_param("sss", $title, $description, $targetFilePath);

    // Ejecutar la consulta y verificar si tuvo éxito
    if ($stmt->execute()) {
        echo "<p>El proyecto se ha guardado correctamente.</p>";
        echo "<p><a href='index.html'>Volver al formulario</a></p>";
    } else {
        echo "<p>Error al guardar el proyecto: " . $stmt->error . "</p>";
    }

    // Cerrar la sentencia y la conexión
    $stmt->close();
    $conn->close();
}
?>
