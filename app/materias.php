<?php
require 'inc.php';
require_login();
$pdo = db();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (($_POST['accion'] ?? '') === 'borrar') {
            $pdo->prepare('DELETE FROM materias WHERE id = ?')->execute([(int)$_POST['id']]);
        } else {
            $clave = trim($_POST['clave'] ?? '');
            $nombre = trim($_POST['nombre'] ?? '');
            if ($clave === '' || $nombre === '') throw new RuntimeException('Clave y nombre son obligatorios');
            $pdo->prepare('INSERT INTO materias (clave, nombre) VALUES (?, ?)')->execute([$clave, $nombre]);
        }
        header('Location: materias.php');
        exit;
    } catch (PDOException $ex) {
        $msg = 'No se pudo guardar (¿clave duplicada?)';
    } catch (RuntimeException $ex) {
        $msg = $ex->getMessage();
    }
}
$materias = $pdo->query('SELECT * FROM materias ORDER BY nombre')->fetchAll();
top('Materias');
?>
<?php if ($msg): ?><div class="err"><?= e($msg) ?></div><?php endif; ?>
<form method="post" class="f">
  <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
  <input name="clave" placeholder="Clave" required>
  <input name="nombre" placeholder="Nombre de la materia" required>
  <button>Agregar</button>
</form>
<table>
  <tr><th>Clave</th><th>Materia</th><th></th></tr>
  <?php foreach ($materias as $m): ?>
  <tr>
    <td><?= e($m['clave']) ?></td>
    <td><?= e($m['nombre']) ?></td>
    <td>
      <form method="post" onsubmit="return confirm('¿Borrar materia y sus calificaciones?')">
        <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
        <input type="hidden" name="accion" value="borrar">
        <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
        <button>Borrar</button>
      </form>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
<?php bottom();
