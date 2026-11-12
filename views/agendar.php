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
<input type="hidden" name="token" value="<?= e($token) ?>"><input type="hidden" name="operacion" value="agendar">
<label>Nombre del sprint<input name="nombre" required maxlength="100" value="<?= e($_POST['nombre'] ?? '') ?>" placeholder="Ejemplo: Sprint 2"></label>
<div class="date-grid"><label>Fecha de inicio<input type="date" name="inicio" required value="<?= e($_POST['inicio'] ?? '') ?>"></label><label>Fecha de finalización<input type="date" name="fin" required value="<?= e($_POST['fin'] ?? '') ?>"></label></div>
<div id="editor-objetivos" hidden>
<label for="nuevo-objetivo">Nuevo objetivo por realizar</label>
<div class="objetivo-input"><input id="nuevo-objetivo" maxlength="2000" placeholder="Escribe un objetivo"><button type="button" class="button primary" id="anadir-objetivo">Añadir</button></div>
<p class="muted">Escribe un objetivo y pulsa “Añadir”. Repite para agregar todos los que necesites.</p>
<p id="mensaje-objetivos" role="status" aria-live="polite"></p>
<ul id="lista-objetivos" class="lista-editable" aria-label="Objetivos añadidos"></ul>
</div>
<label id="objetivos-alternativa">Nuevos objetivos por realizar<textarea id="objetivos-datos" name="objetivos" rows="6" placeholder="Escribe un objetivo por línea"><?= e($_POST['objetivos'] ?? '') ?></textarea></label>
<?php $ultimo=$datos['anteriores'][0] ?? null; if ($ultimo && $ultimo['pendientes']): ?>
<h3>Retomar pendientes del último sprint</h3><p class="muted">Selecciona cuáles quieres incluir como objetivos del nuevo sprint.</p>
<?php foreach ($ultimo['pendientes'] as $item): ?><label class="check-row"><input type="checkbox" name="retomar[]" value="<?= $item->get_id() ?>" <?= in_array((string)$item->get_id(),is_array($_POST['retomar'] ?? null) ? $_POST['retomar'] : [],true) ? 'checked' : '' ?>><span><?= e($item->get_descripcion()) ?></span></label><?php endforeach; endif; ?>
<div class="form-actions"><a class="button light" href="index.php">Volver al inicio</a><button class="button primary">Agendar sprint</button></div>
</form><script src="public/js/agendar.js"></script><?php endif; ?>
<?php require __DIR__.'/partials/footer.php'; ?>
