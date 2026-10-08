<?php
require 'inc.php';
require_login();
$pdo = db();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (($_POST['accion'] ?? '') === 'borrar') {
            $pdo->prepare('DELETE FROM alumnos WHERE id = ?')->execute([(int)$_POST['id']]);
        } else {
            $d = [trim($_POST['matricula'] ?? ''), trim($_POST['nombre'] ?? ''), trim($_POST['apellidos'] ?? ''),
                  trim($_POST['grupo'] ?? ''), trim($_POST['email'] ?? '')];
            if ($d[0] === '' || $d[1] === '') throw new RuntimeException('Matrícula y nombre son obligatorios');
            if ($d[4] !== '' && !filter_var($d[4], FILTER_VALIDATE_EMAIL)) throw new RuntimeException('Email inválido');
            if (!empty($_POST['id'])) {
                $d[] = (int)$_POST['id'];
                $pdo->prepare('UPDATE alumnos SET matricula=?, nombre=?, apellidos=?, grupo=?, email=? WHERE id=?')->execute($d);
            } else {
                $pdo->prepare('INSERT INTO alumnos (matricula, nombre, apellidos, grupo, email) VALUES (?,?,?,?,?)')->execute($d);
            }
        }
        header('Location: alumnos.php');
        exit;
    } catch (PDOException $ex) {
        $msg = 'No se pudo guardar (¿matrícula duplicada?)';
    } catch (RuntimeException $ex) {
        $msg = $ex->getMessage();
    }
}

$edit = [];
if (isset($_GET['edit'])) {
    $s = $pdo->prepare('SELECT * FROM alumnos WHERE id = ?');
    $s->execute([(int)$_GET['edit']]);
    $edit = $s->fetch() ?: [];
}
$alumnos = $pdo->query('SELECT * FROM alumnos ORDER BY apellidos, nombre')->fetchAll();
top('Alumnos');
?>
<?php if ($msg): ?><div class="err"><?= e($msg) ?></div><?php endif; ?>
<form method="post" class="f">
  <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
  <input type="hidden" name="id" value="<?= e($edit['id'] ?? '') ?>">
  <input name="matricula" placeholder="Matrícula" value="<?= e($edit['matricula'] ?? '') ?>" required>
  <input name="nombre" placeholder="Nombre" value="<?= e($edit['nombre'] ?? '') ?>" required>
  <input name="apellidos" placeholder="Apellidos" value="<?= e($edit['apellidos'] ?? '') ?>">
  <input name="grupo" placeholder="Grupo" value="<?= e($edit['grupo'] ?? '') ?>">
  <input name="email" placeholder="Email" value="<?= e($edit['email'] ?? '') ?>">
  <button><?= $edit ? 'Actualizar' : 'Agregar' ?></button>
</form>
<table>
  <tr><th>Matrícula</th><th>Nombre</th><th>Grupo</th><th>Email</th><th></th></tr>
  <?php foreach ($alumnos as $a): ?>
  <tr>
    <td><?= e($a['matricula']) ?></td>
    <td><?= e($a['nombre'] . ' ' . $a['apellidos']) ?></td>
    <td><?= e($a['grupo']) ?></td>
    <td><?= e($a['email']) ?></td>
    <td>
      <a href="?edit=<?= (int)$a['id'] ?>">Editar</a>
      <form method="post" style="display:inline" onsubmit="return confirm('¿Borrar alumno y sus calificaciones?')">
        <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
        <input type="hidden" name="accion" value="borrar">
        <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
        <button>Borrar</button>
      </form>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
<?php bottom();
