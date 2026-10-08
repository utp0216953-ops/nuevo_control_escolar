<?php
declare(strict_types=1);
session_start();

const DB_HOST = 'localhost', DB_NAME = 'control_escolar', DB_USER = 'root', DB_PASS = '';

function db(): PDO {
    static $pdo = null;
    return $pdo ??= new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
}
function e($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function csrf(): string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(16)); }
function csrf_check(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !hash_equals(csrf(), (string)($_POST['csrf'] ?? ''))) {
        http_response_code(400);
        exit('Token CSRF inválido');
    }
}
function require_login(): void {
    if (empty($_SESSION['uid'])) { header('Location: login.php'); exit; }
    csrf_check();
}
function top(string $titulo): void { ?>
<!doctype html>
<html lang="es"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($titulo) ?> · Control Escolar</title>
<style>
body{font-family:system-ui,sans-serif;margin:0;background:#f4f6f8;color:#222}
nav{background:#1f3a5f;padding:12px 20px;display:flex;gap:16px;align-items:center}
nav a{color:#fff;text-decoration:none}nav .sp{flex:1}
main{max-width:960px;margin:20px auto;padding:0 16px}
table{width:100%;border-collapse:collapse;background:#fff}
th,td{padding:8px;border-bottom:1px solid #ddd;text-align:left}th{background:#e8edf3}
form.f{background:#fff;padding:12px;margin-bottom:16px;display:flex;flex-wrap:wrap;gap:8px}
input,select,button{padding:7px;font:inherit}
button{background:#1f3a5f;color:#fff;border:0;border-radius:4px;cursor:pointer}
.err{background:#fde2e2;padding:8px;margin-bottom:12px}
.cards{display:flex;gap:12px}.card{background:#fff;padding:20px;flex:1;text-align:center}
.card b{font-size:2em;display:block}
</style></head><body>
<nav><a href="index.php">Inicio</a><a href="alumnos.php">Alumnos</a><a href="materias.php">Materias</a>
<a href="calificaciones.php">Calificaciones</a><span class="sp"></span><a href="logout.php">Salir</a></nav>
<main><h2><?= e($titulo) ?></h2>
<?php }
function bottom(): void { echo '</main></body></html>'; }
