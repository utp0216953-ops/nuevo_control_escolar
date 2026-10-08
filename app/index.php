<?php
require 'inc.php';
require_login();
$pdo = db();
$n = fn(string $t): int => (int)$pdo->query("SELECT COUNT(*) FROM $t")->fetchColumn();
$prom = $pdo->query('SELECT ROUND(AVG(calificacion), 2) FROM calificaciones')->fetchColumn();
top('Panel');
?>
<div class="cards">
  <div class="card"><b><?= $n('alumnos') ?></b>Alumnos</div>
  <div class="card"><b><?= $n('materias') ?></b>Materias</div>
  <div class="card"><b><?= $n('calificaciones') ?></b>Calificaciones</div>
  <div class="card"><b><?= $prom !== null ? e($prom) : '–' ?></b>Promedio general</div>
</div>
<?php bottom();
