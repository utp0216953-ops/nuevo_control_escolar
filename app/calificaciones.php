<?php
require 'inc.php';
require_login();
$pdo = db();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (($_POST['accion'] ?? '') === 'borrar') {
            $pdo->prepare('DELETE FROM calificaciones WHERE id = ?')->execute([(int)$_POST['id']]);
        } else {
            $al = (int)($_POST['alumno_id'] ?? 0);
            $ma = (int)($_POST['materia_id'] ?? 0);
            $pa = (int)($_POST['parcial'] ?? 0);
            $ca = filter_var($_POST['calificacion'] ?? '', FILTER_VALIDATE_FLOAT);
            if (!$al || !$ma || $pa < 1 || $pa > 3 || $ca === false || $ca < 0 || $ca > 10) {
                throw new RuntimeException('Datos inválidos: la calificación va de 0 a 10 y el parcial de 1 a 3');
            }
            $pdo->prepare('INSERT INTO calificaciones (alumno_id, materia_id, parcial, calificacion) VALUES (?,?,?,?)
                           ON DUPLICATE KEY UPDATE calificacion = VALUES(calificacion)')
                ->execute([$al, $ma, $pa, $ca]);
        }
        header('Location: calificaciones.php');
        exit;
    } catch (PDOException $ex) {
        $msg = 'No se pudo guardar la calificación';
    } catch (RuntimeException $ex) {
        $msg = $ex->getMessage();
    }
}

$alumnos = $pdo->query('SELECT id, matricula, nombre, apellidos FROM alumnos ORDER BY apellidos, nombre')->fetchAll();
$materias = $pdo->query('SELECT id, nombre FROM materias ORDER BY nombre')->fetchAll();
$cals = $pdo->query('SELECT c.id, a.matricula, a.nombre, a.apellidos, m.nombre AS materia, c.parcial, c.calificacion
                     FROM calificaciones c JOIN alumnos a ON a.id = c.alumno_id JOIN materias m ON m.id = c.materia_id
                     ORDER BY a.apellidos, a.nombre, m.nombre, c.parcial')->fetchAll();
$proms = $pdo->query('SELECT a.matricula, a.nombre, a.apellidos, ROUND(AVG(c.calificacion), 2) AS prom
                      FROM calificaciones c JOIN alumnos a ON a.id = c.alumno_id
                      GROUP BY a.id ORDER BY prom DESC')->fetchAll();
top('Calificaciones');
?>
<?php if ($msg): ?><div class="err"><?= e($msg) ?></div><?php endif; ?>
<form method="post" class="f">
  <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
  <select name="alumno_id" required>
    <option value="">Alumno…</option>
    <?php foreach ($alumnos as $a): ?>
      <option value="<?= (int)$a['id'] ?>"><?= e($a['matricula'] . ' – ' . $a['nombre'] . ' ' . $a['apellidos']) ?></option>
    <?php endforeach; ?>
  </select>
  <select name="materia_id" required>
    <option value="">Materia…</option>
    <?php foreach ($materias as $m): ?>
      <option value="<?= (int)$m['id'] ?>"><?= e($m['nombre']) ?></option>
    <?php endforeach; ?>
  </select>
  <select name="parcial"><option>1</option><option>2</option><option>3</option></select>
  <input type="number" name="calificacion" min="0" max="10" step="0.01" placeholder="0 – 10" required>
  <button>Guardar</button>
</form>

<h3>Promedios</h3>
<table>
  <tr><th>Matrícula</th><th>Alumno</th><th>Promedio</th></tr>
  <?php foreach ($proms as $p): ?>
  <tr><td><?= e($p['matricula']) ?></td><td><?= e($p['nombre'] . ' ' . $p['apellidos']) ?></td><td><?= e($p['prom']) ?></td></tr>
  <?php endforeach; ?>
</table>

<h3>Detalle</h3>
<table>
  <tr><th>Alumno</th><th>Materia</th><th>Parcial</th><th>Calif.</th><th></th></tr>
  <?php foreach ($cals as $c): ?>
  <tr>
    <td><?= e($c['nombre'] . ' ' . $c['apellidos']) ?></td>
    <td><?= e($c['materia']) ?></td>
    <td><?= (int)$c['parcial'] ?></td>
    <td><?= e($c['calificacion']) ?></td>
    <td>
      <form method="post" onsubmit="return confirm('¿Borrar calificación?')">
        <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
        <input type="hidden" name="accion" value="borrar">
        <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
        <button>Borrar</button>
      </form>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
<?php bottom();
