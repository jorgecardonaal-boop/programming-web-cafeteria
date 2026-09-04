<?php
/**
 * listar.php
 * -----------------------------------------------------------
 * Consulta y muestra los registros guardados en la tabla
 * `contactos` de PostgreSQL (contenedor Docker).
 * -----------------------------------------------------------
 */

declare(strict_types=1);

// ---------------------------------------------------------
// 1. Datos de conexión (idénticos a procesar.php)
// ---------------------------------------------------------
$dbHost = 'db';
$dbPort = '5432';
$dbName = 'cafeteria_db';
$dbUser = 'usuario_cafe';
$dbPass = 'clave_secreta';

try {
    $dsn = "pgsql:host={$dbHost};port={$dbPort};dbname={$dbName}";
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $consulta = $pdo->query('
        SELECT id, nombre, correo, telefono, ley_favorita, calificacion, mensaje, fecha_registro
        FROM contactos
        ORDER BY fecha_registro DESC
    ');

    $registros = $consulta->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    http_response_code(500);
    exit('Error al conectar con la base de datos: ' . htmlspecialchars($e->getMessage()));
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opiniones registradas — Las 48 Leyes del Poder</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=EB+Garamond:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/estilos.css">
</head>

<body>
    <header class="cabecera">
        <div class="sello" aria-hidden="true"></div>
        <h1>Opiniones registradas</h1>
        <p class="lema">Todo lo que los lectores han compartido hasta ahora</p>
    </header>

    <main>
        <section id="registros">
            <h2>Registros almacenados</h2>

            <?php if (empty($registros)): ?>
                <p>Todavía no hay opiniones registradas.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                            <th>Ley favorita</th>
                            <th>Calificación</th>
                            <th>Mensaje</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($registros as $fila): ?>
                        <tr>
                            <td><?= htmlspecialchars($fila['nombre']) ?></td>
                            <td><?= htmlspecialchars($fila['correo']) ?></td>
                            <td><?= htmlspecialchars($fila['telefono']) ?></td>
                            <td><?= htmlspecialchars($fila['ley_favorita']) ?></td>
                            <td><?= str_repeat('★', (int) $fila['calificacion']) ?></td>
                            <td><?= htmlspecialchars($fila['mensaje']) ?></td>
                            <td><?= htmlspecialchars($fila['fecha_registro']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

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
