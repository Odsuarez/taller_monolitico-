<?php
                         
                          
                             
                                   
if (!isset($token,$pagina)) { header('Location: ../index.php'); exit; }
require __DIR__.'/partials/header.php';
$detalleSprint=$datos['actual'] ?? $datos['programado'] ?? null;
?>
<div class="intro"><span class="eyebrow">DETALLE DEL EQUIPO</span><h1>Sprint actual</h1><p>Consulta toda la información y modifica los datos o registros del sprint.</p></div>
<?php if ($detalleSprint && isset($sprintFormulario)): $sprint=$detalleSprint['sprint']; $yaInicio=$sprint->get_fecha_inicio()<=date('Y-m-d'); ?>
<?php if (isset($_GET['guardado'])): ?><p class="notice" role="status">Los datos del sprint se guardaron correctamente.</p><?php endif; ?>
<div class="detalle-grid">
<section class="card"><h2>Datos del sprint</h2>
<p class="muted"><?= $yaInicio ? 'En curso' : 'Programado' ?> · <?= count($detalleSprint['realizadas']) ?> objetivos realizados y <?= count($detalleSprint['pendientes']) ?> pendientes.</p>
<form method="post" action="index.php?pagina=sprint_actual">
<input type="hidden" name="token" value="<?= escaparHtml($token) ?>"><input type="hidden" name="id" value="<?= $sprint->get_id() ?>">
<label>Nombre<input name="nombre" required maxlength="100" value="<?= escaparHtml($sprintFormulario['nombre']) ?>"></label>
<label>Fecha de inicio<input type="date" name="inicio" required <?= $yaInicio ? 'readonly' : 'min="'.date('Y-m-d').'"' ?> value="<?= escaparHtml($sprintFormulario['inicio']) ?>"></label>
<?php if ($yaInicio): ?><p class="muted">La fecha de inicio se conserva porque este sprint ya comenzó.</p><?php endif; ?>
<label>Fecha final y retrospectiva<input type="date" name="fin" min="<?= escaparHtml(max(date('Y-m-d'),$sprintFormulario['inicio'])) ?>" required value="<?= escaparHtml($sprintFormulario['fin']) ?>"></label>
<div class="form-actions"><button class="button primary" name="accion" value="guardar_sprint">Guardar cambios</button></div>
</form>
<?php if ($yaInicio): ?><a class="text-link" href="index.php?pagina=retrospectiva">Iniciar retrospectiva →</a><?php endif; ?>
</section>
<section class="card"><h2>Objetivos y balance</h2>
<?php foreach (['realizadas'=>'Lo que ya se realizó','pendientes'=>'Lo que falta','logros'=>'Logros','impedimentos'=>'Impedimentos'] as $grupo=>$titulo): ?>
<h3><?= escaparHtml($titulo) ?></h3><?php $lista=$detalleSprint[$grupo]; $vacio='No hay registros en esta sección.'; require __DIR__.'/partials/lista_objetivos.php'; ?>
<?php endforeach; ?>
<details><summary>Comentarios y otros registros</summary><?php $lista=array_filter($detalleSprint['items'],fn($i)=>in_array($i->get_categoria(),['comentario','otro'],true)); require __DIR__.'/partials/retro_items.php'; ?></details>
<?php $lista=$detalleSprint['items']; require __DIR__.'/partials/gestion_registros.php'; ?>
</section></div>
<?php else: ?><section class="card"><h2>Todavía no hay un sprint actual</h2><p>Agenda un sprint para consultar aquí sus detalles.</p><a class="text-link" href="index.php?pagina=agendar">Agendar nuevo sprint →</a></section><?php endif; ?>
<?php require __DIR__.'/partials/footer.php'; ?>
