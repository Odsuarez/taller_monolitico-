<?php
if (!isset($token, $pagina)) { header('Location: ../index.php'); exit; }
require __DIR__.'/partials/header.php';
?>
<div class="intro"><span class="eyebrow">REUNIÓN DEL EQUIPO</span><h1>Retrospectiva del sprint</h1><p>Revisa los objetivos, registra lo ocurrido y finaliza la reunión.</p></div>
<?php if ($datos && $datos['actual']): $detalle=$datos['actual']; $s=$detalle['sprint']; ?>
<section class="card workflow"><h2><?= e($s->get_nombre()) ?></h2><p class="muted"><?= e($s->get_fecha_inicio()) ?> — <?= e($s->get_fecha_fin()) ?></p>
<p class="notice">Los cambios se guardan al pulsar “Finalizar retrospectiva”.</p>
<ol class="steps" aria-label="Etapas"><li data-indicador>1. Objetivos</li><li data-indicador>2. Balance</li><li data-indicador>3. Finalizar</li></ol>
<form method="post" action="index.php?pagina=retrospectiva" id="revision">
<input type="hidden" name="token" value="<?= e($token) ?>"><input type="hidden" name="operacion" value="finalizar"><input type="hidden" name="sprint_id" value="<?= $s->get_id() ?>">
<section data-etapa><h3>1. Revisar los objetivos del sprint</h3><p>Marca lo que se realizó. Lo que dejes sin marcar quedará pendiente.</p>
<?php $acciones=array_filter($detalle['items'],fn($i)=>$i->get_categoria()==='accion'); if (!$acciones): ?><p class="muted">No se registraron objetivos para este sprint.</p><?php endif; ?>
<?php foreach ($acciones as $item):
$marcada=($_SERVER['REQUEST_METHOD']==='POST') ? in_array((string)$item->get_id(),is_array($_POST['realizadas'] ?? null) ? $_POST['realizadas'] : [],true) : $item->get_cumplida()===true;
?>
<label class="check-row"><input type="checkbox" name="realizadas[]" value="<?= $item->get_id() ?>" <?= $marcada ? 'checked' : '' ?>><span><?= e($item->get_descripcion()) ?></span></label>
<?php endforeach; ?>
<label>Otras tareas que ya se realizaron<textarea name="adicionales" rows="4" placeholder="Una tarea por línea"><?= e($_POST['adicionales'] ?? '') ?></textarea></label>
</section>
<section data-etapa><h3>2. Registrar el balance del equipo</h3>
<?php if ($detalle['logros'] || $detalle['impedimentos']): ?><details><summary>Ver lo registrado hasta ahora</summary><?php $lista=array_merge($detalle['logros'],$detalle['impedimentos']); require __DIR__.'/partials/retro_items.php'; ?></details><?php endif; ?>
<label>¿Qué salió bien? Logros<textarea name="logros" rows="4" placeholder="Un logro por línea"><?= e($_POST['logros'] ?? '') ?></textarea></label>
<label>¿Qué dificultó el trabajo? Impedimentos<textarea name="impedimentos" rows="4" placeholder="Un impedimento por línea"><?= e($_POST['impedimentos'] ?? '') ?></textarea></label>
<label>Comentarios y acuerdos para mejorar<textarea name="comentarios" rows="4" placeholder="Un acuerdo por línea"><?= e($_POST['comentarios'] ?? '') ?></textarea></label>
</section>
<section data-etapa><h3>3. Finalizar la retrospectiva</h3><p id="balance-seleccion"></p><p>El sprint pasará a “Sprints anteriores”. Sus objetivos realizados y pendientes aparecerán en el resumen del proyecto.</p><p>Después podrás agendar el siguiente sprint y elegir qué pendientes retomar.</p>
<button class="button primary" type="submit">Finalizar retrospectiva</button></section>
<div class="form-actions wizard-nav"><button type="button" class="button light" id="atras">Anterior</button><button type="button" class="button primary" id="siguiente">Continuar</button></div>
</form></section><script src="public/js/retrospectiva.js"></script>
<?php elseif ($datos): ?><section class="card"><h2>No hay un sprint actual para revisar</h2><p><?= $datos['programado'] ? 'El sprint agendado todavía no ha comenzado.' : 'Agenda un sprint y sus objetivos para empezar.' ?></p><a class="text-link" href="index.php?pagina=agendar">Agendar nuevo sprint →</a></section><?php endif; ?>
<?php require __DIR__.'/partials/footer.php'; ?>
