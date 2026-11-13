<?php
if (!isset($token,$pagina)) { header('Location: ../index.php'); exit; }
require __DIR__.'/partials/header.php';
?>
<div class="intro"><span class="eyebrow">REUNIÓN DEL EQUIPO</span><h1>Retrospectiva del sprint</h1><p>Revisa los objetivos, registra lo ocurrido y finaliza la reunión.</p></div>
<?php if ($datos && $datos['actual'] && $revision): $detalle=$datos['actual']; $s=$detalle['sprint']; $paso=$revision['paso']; ?>
<section class="card workflow"><h2><?= e($s->get_nombre()) ?></h2><p class="muted"><?= e($s->get_fecha_inicio()) ?> — <?= e($s->get_fecha_fin()) ?></p>
<p class="notice">Los datos de las etapas se conservan temporalmente en la sesión. Se guardan en la base de datos al finalizar.</p>
<ol class="steps" aria-label="Etapas"><?php foreach ([1=>'Objetivos',2=>'Balance',3=>'Finalizar'] as $numero=>$titulo): ?><li <?= $numero===$paso ? 'aria-current="step"' : '' ?>><?= $numero ?>. <?= e($titulo) ?></li><?php endforeach; ?></ol>
<form method="post" action="index.php?pagina=retrospectiva">
<input type="hidden" name="token" value="<?= e($token) ?>"><input type="hidden" name="sprint_id" value="<?= $s->get_id() ?>">
<?php if ($paso===1): ?>
<h3>1. Revisar los objetivos del sprint</h3><p>Marca lo realizado. Los objetivos sin marcar quedarán pendientes.</p>
<?php foreach ($detalle['items'] as $item): if ($item->get_categoria()!=='accion') continue; ?>
<label class="check-row"><input type="checkbox" name="realizadas[]" value="<?= $item->get_id() ?>" <?= in_array((string)$item->get_id(),$revision['realizadas'],true) ? 'checked' : '' ?>><span><?= e($item->get_descripcion()) ?></span></label>
<?php endforeach; ?>
<label>Otras tareas que ya se realizaron<textarea name="adicionales" rows="4" placeholder="Una tarea por línea"><?= e($revision['adicionales']) ?></textarea></label>
<?php elseif ($paso===2): ?>
<h3>2. Registrar el balance del equipo</h3>
<?php if ($detalle['logros'] || $detalle['impedimentos']): ?><details><summary>Ver lo registrado hasta ahora</summary><?php $lista=array_merge($detalle['logros'],$detalle['impedimentos']); require __DIR__.'/partials/retro_items.php'; ?></details><?php endif; ?>
<label>¿Qué salió bien? Logros<textarea name="logros" rows="4" placeholder="Un logro por línea"><?= e($revision['logros']) ?></textarea></label>
<label>¿Qué dificultó el trabajo? Impedimentos<textarea name="impedimentos" rows="4" placeholder="Un impedimento por línea"><?= e($revision['impedimentos']) ?></textarea></label>
<label>Comentarios y acuerdos para mejorar<textarea name="comentarios" rows="4" placeholder="Un acuerdo por línea"><?= e($revision['comentarios']) ?></textarea></label>
<?php else:
$acciones=array_values(array_filter($detalle['items'],fn($i)=>$i->get_categoria()==='accion'));
$hechas=count(array_filter($acciones,fn($i)=>in_array((string)$i->get_id(),$revision['realizadas'],true)));
?>
<h3>3. Finalizar la retrospectiva</h3>
<p><?= $hechas ?> objetivos realizados y <?= count($acciones)-$hechas ?> pendientes.</p>
<?php foreach (['adicionales'=>'Otras tareas realizadas','logros'=>'Logros','impedimentos'=>'Impedimentos','comentarios'=>'Acuerdos'] as $campo=>$titulo): if ($revision[$campo]==='') continue; ?>
<h3><?= e($titulo) ?></h3><p><?= nl2br(e($revision[$campo])) ?></p>
<?php endforeach; ?>
<p>Al finalizar, el sprint pasará al historial y podrás agendar el siguiente.</p>
<?php endif; ?>
<div class="form-actions wizard-nav">
<?php if ($paso>1): ?><button class="button light" name="accion" value="anterior">Anterior</button><?php endif; ?>
<?php if ($paso<3): ?><button class="button primary" name="accion" value="continuar">Continuar</button>
<?php else: ?><button class="button primary" name="accion" value="finalizar">Finalizar retrospectiva</button><?php endif; ?>
</div></form></section>
<?php elseif ($datos): ?><section class="card"><h2>No hay un sprint actual para revisar</h2><p><?= $datos['programado'] ? 'El sprint agendado todavía no ha comenzado.' : 'Agenda un sprint y sus objetivos para empezar.' ?></p><a class="text-link" href="index.php?pagina=agendar">Agendar nuevo sprint →</a></section><?php endif; ?>
<?php require __DIR__.'/partials/footer.php'; ?>
