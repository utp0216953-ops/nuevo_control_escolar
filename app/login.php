<?php
require 'inc.php';
$pdo = db();
$primero = (int)$pdo->query('SELECT COUNT(*) FROM usuarios')->fetchColumn() === 0;
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $u = trim($_POST['usuario'] ?? '');
    $p = (string)($_POST['password'] ?? '');
    if ($primero) {
        if ($u === '' || strlen($p) < 8) {
            $err = 'Usuario obligatorio y contraseña de al menos 8 caracteres';
        } else {
            $pdo->prepare('INSERT INTO usuarios (usuario, password) VALUES (?, ?)')
                ->execute([$u, password_hash($p, PASSWORD_DEFAULT)]);
            $primero = false;
        }
    }
    if (!$primero && !$err) {
        $s = $pdo->prepare('SELECT * FROM usuarios WHERE usuario = ?');
        $s->execute([$u]);
        $row = $s->fetch();
        if ($row && password_verify($p, $row['password'])) {
            session_regenerate_id(true);
            $_SESSION['uid'] = $row['id'];
            header('Location: index.php');
            exit;
        }
        $err = 'Credenciales incorrectas';
    }
}
?>
<!doctype html>
<html lang="es"><head><meta charset="utf-8"><title>Acceso</title>
<style>body{font-family:system-ui;background:#f4f6f8;display:grid;place-items:center;height:100vh;margin:0}
form{background:#fff;padding:24px;display:grid;gap:10px;width:280px}input,button{padding:8px;font:inherit}
button{background:#1f3a5f;color:#fff;border:0}.err{color:#b00}</style></head><body>
<form method="post">
  <h3><?= $primero ? 'Crear administrador' : 'Control Escolar' ?></h3>
  <?php if ($err): ?><div class="err"><?= e($err) ?></div><?php endif; ?>
  <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
  <input name="usuario" placeholder="Usuario" required>
  <input type="password" name="password" placeholder="Contraseña" required>
  <button><?= $primero ? 'Crear y entrar' : 'Entrar' ?></button>
</form></body></html>
