<?php
if (!isset($token, $pagina)) { header('Location: ../index.php'); exit; }
require __DIR__.'/partials/header.php';
?>
<div class="intro"><span class="eyebrow">SIGUIENTE PASO</span><h1>Agendar nuevo sprint</h1>
<?php if (isset($_GET['finalizado'])): ?><p class="notice">Retrospectiva finalizada. El sprint ya está en el historial.</p><?php endif; ?>
<p>Define las fechas y los objetivos del próximo sprint.</p></div>
<?php if ($datos && ($datos['actual'] || $datos['programado'])): ?>
<section class="card"><h2>Ya tienes un sprint abierto</h2><p>Finaliza su retrospectiva antes de agendar otro.</p><a class="text-link" href="index.php">Ver sprint →</a></section>
<?php elseif ($datos): ?>
<form class="card workflow" method="post" action="index.php?pagina=agendar">
<input type="hidden" name="token" value="<?= e($token) ?>"><input type="hidden" name="formulario" value="agenda">
<label>Nombre del sprint<input name="nombre" required maxlength="100" value="<?= e($agenda['nombre']) ?>" placeholder="Ejemplo: Sprint 2"></label>
<div class="date-grid"><label>Fecha de inicio<input type="date" name="inicio" required value="<?= e($agenda['inicio']) ?>"></label><label>Fecha de finalización<input type="date" name="fin" required value="<?= e($agenda['fin']) ?>"></label></div>
<label for="nuevo-objetivo">Nuevo objetivo por realizar</label>
<div class="objetivo-input"><input id="nuevo-objetivo" name="nuevo" maxlength="2000" value="<?= e($agenda['nuevo']) ?>" placeholder="Escribe un objetivo"><button type="submit" class="button primary" name="accion" value="anadir" formnovalidate>Añadir</button></div>
<p class="muted">Añade los objetivos uno por uno. Se guardarán al agendar el sprint.</p>
<ul class="lista-editable" aria-label="Objetivos añadidos">
<?php foreach ($agenda['objetivos'] as $indice=>$objetivo): ?>
<li><span><?= e($objetivo) ?></span><button class="button light" name="accion" value="quitar:<?= $indice ?>" formnovalidate>Quitar</button></li>
<?php endforeach; ?>
</ul>
<?php $ultimo=$datos['anteriores'][0] ?? null; if ($ultimo && $ultimo['pendientes']): ?>
<h3>Retomar pendientes del último sprint</h3><p class="muted">Selecciona cuáles quieres incluir como objetivos del nuevo sprint.</p>
<?php foreach ($ultimo['pendientes'] as $item): ?><label class="check-row"><input type="checkbox" name="retomar[]" value="<?= $item->get_id() ?>" <?= in_array((string)$item->get_id(),$agenda['retomar'],true) ? 'checked' : '' ?>><span><?= e($item->get_descripcion()) ?></span></label><?php endforeach; endif; ?>
<div class="form-actions"><a class="button light" href="index.php">Volver al inicio</a><button class="button primary" name="accion" value="agendar">Agendar sprint</button></div>
</form><?php endif; ?>
<?php require __DIR__.'/partials/footer.php'; ?>
