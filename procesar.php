<?php
/**
 * procesar.php
 * -----------------------------------------------------------
 * Recibe el formulario de "Comparte tu opinión" de index.html,
 * valida los datos en el servidor y los guarda en la tabla
 * `opiniones_leyes` de PostgreSQL (contenedor Docker).
 * -----------------------------------------------------------
 */

declare(strict_types=1);

// ---------------------------------------------------------
// 1. Datos de conexión a la base de datos
// ---------------------------------------------------------
// Estos valores coinciden exactamente con el servicio "db" de
// tu docker-compose.yml (POSTGRES_DB, POSTGRES_USER, POSTGRES_PASSWORD).
// "db" funciona como host porque ambos contenedores están en la
// misma red de docker-compose y "db" es el nombre del servicio,
// no "localhost".
$dbHost = 'db';
$dbPort = '5432';
$dbName = 'cafeteria_db';
$dbUser = 'usuario_cafe';
$dbPass = 'clave_secreta';

// ---------------------------------------------------------
// 2. Solo se acepta el método POST
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}

// ---------------------------------------------------------
// 3. Recolectar y limpiar los datos del formulario
// ---------------------------------------------------------
// trim() quita espacios sobrantes; el operador ?? evita
// "undefined array key" si algún campo llega vacío.
$nombre       = trim($_POST['nombre'] ?? '');
$correo       = trim($_POST['correo'] ?? '');
$telefono     = trim($_POST['telefono'] ?? '');
$leyFavorita  = trim($_POST['ley'] ?? '');
$calificacion = trim($_POST['calificacion'] ?? '');
$mensaje      = trim($_POST['mensaje'] ?? '');

// ---------------------------------------------------------
// 4. Validación en el servidor
// ---------------------------------------------------------
// El HTML ya valida en el navegador, pero esa validación se
// puede saltar fácilmente, así que se repite aquí antes de
// tocar la base de datos.
$errores = [];

if (mb_strlen($nombre) < 3 || mb_strlen($nombre) > 50) {
    $errores[] = 'El nombre debe tener entre 3 y 50 caracteres.';
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $errores[] = 'El correo electrónico no es válido.';
}

if (!preg_match('/^[0-9]{7,10}$/', $telefono)) {
    $errores[] = 'El teléfono debe tener entre 7 y 10 dígitos.';
}

if ($leyFavorita === '') {
    $errores[] = 'Debes seleccionar una ley favorita.';
}

if (!ctype_digit($calificacion) || (int) $calificacion < 1 || (int) $calificacion > 5) {
    $errores[] = 'La calificación debe ser un valor entre 1 y 5.';
}

if (mb_strlen($mensaje) < 10 || mb_strlen($mensaje) > 300) {
    $errores[] = 'El mensaje debe tener entre 10 y 300 caracteres.';
}

if (!empty($errores)) {
    http_response_code(422);
    echo '<h1>No se pudo enviar el formulario</h1><ul>';
    foreach ($errores as $error) {
        // htmlspecialchars evita que el texto del error se
        // interprete como HTML si llegara a contener < o >
        echo '<li>' . htmlspecialchars($error) . '</li>';
    }
    echo '</ul><p><a href="index.html">Volver</a></p>';
    exit;
}

// ---------------------------------------------------------
// 5. Guardar en PostgreSQL
// ---------------------------------------------------------
try {
    $dsn = "pgsql:host={$dbHost};port={$dbPort};dbname={$dbName}";

    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    // Consulta preparada: los ":marcadores" evitan inyección SQL,
    // nunca se concatenan los datos del usuario directamente.
    $consulta = $pdo->prepare('
        INSERT INTO contactos (nombre, correo, telefono, ley_favorita, calificacion, mensaje)
        VALUES (:nombre, :correo, :telefono, :ley_favorita, :calificacion, :mensaje)
    ');

    $consulta->execute([
        ':nombre'       => $nombre,
        ':correo'       => $correo,
        ':telefono'     => $telefono,
        ':ley_favorita' => $leyFavorita,
        ':calificacion' => (int) $calificacion,
        ':mensaje'      => $mensaje,
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    // En producción no se debería mostrar $e->getMessage() al usuario;
    // aquí se deja visible solo como ayuda mientras desarrollas.
    exit('Error al conectar con la base de datos: ' . htmlspecialchars($e->getMessage()));
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comentario recibido — Las 48 Leyes del Poder</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=EB+Garamond:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/estilos.css">
</head>

<body>
    <header class="cabecera">
        <div class="sello" aria-hidden="true"></div>
        <h1>Comentario recibido</h1>
        <p class="lema">Gracias por compartir tu opinión, <?= htmlspecialchars($nombre) ?></p>
    </header>

    <main>
        <section>
            <aside class="panel-cita">
                <blockquote>Tu mensaje quedó registrado correctamente.</blockquote>
                <p class="fuente-cita">Ley elegida: <?= htmlspecialchars($leyFavorita) ?></p>
            </aside>
            <p style="margin-top: 2rem;">
                <a href="index.html" class="boton">Volver al inicio</a>
            </p>
        </section>
    </main>

    <footer>
        <p>Las 48 Leyes del Poder</p>
        <p>Bogotá, Colombia · Programación Web - 2026</p>
    </footer>
</body>

</html>
