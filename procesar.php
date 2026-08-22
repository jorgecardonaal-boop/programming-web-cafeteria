<?php
$host = "db"; // Nombre del servicio en docker-compose
$db   = "cafeteria_db";
$user = "usuario_cafe";
$pass = "clave_secreta";
$port = "5432";

try {
    // Conexión a PostgreSQL con PDO
    $dsn = "pgsql:host=$host;port=$port;dbname=$db;";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $nombre       = trim($_POST['nombre']);
        $correo       = trim($_POST['correo']);
        $telefono     = trim($_POST['telefono']);
        $producto     = trim($_POST['producto']);
        $calificacion = (int)$_POST['calificacion'];
        $mensaje      = trim($_POST['mensaje']);

        // Insertar en PostgreSQL
        $sql = "INSERT INTO contactos (nombre, correo, telefono, producto, calificacion, mensaje) 
                VALUES (:nombre, :correo, :telefono, :producto, :calificacion, :mensaje)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre'       => $nombre,
            ':correo'       => $correo,
            ':telefono'     => $telefono,
            ':producto'     => $producto,
            ':calificacion' => $calificacion,
            ':mensaje'      => $mensaje
        ]);

        echo "<div style='font-family: sans-serif; text-align: center; margin-top: 50px;'>";
        echo "<h1 style='color: #27ae60;'>¡Gracias por tu mensaje, $nombre!</h1>";
        echo "<p>Registramos tu calificación de <strong>$calificacion estrellas ★</strong> sobre $producto.</p>";
        echo "<a href='index.html' style='background: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Volver al sitio</a>";
        echo "</div>";
    }
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}
?>
