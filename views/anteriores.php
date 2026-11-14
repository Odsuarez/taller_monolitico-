<?php
/** @var string $token */
/** @var string $pagina */
/** @var array|null $datos */
/** @var string|null $error */
$datos = $datos ?? null;
$error = $error ?? null;

if (!isset($token, $pagina)) { header('Location: ../index.php'); exit; }
require __DIR__.'/partials/header.php';
?>
<div class="intro"><span class="eyebrow">HISTORIAL DEL PROYECTO</span><h1>Sprints anteriores</h1><p>Consulta los resultados de cada retrospectiva finalizada.</p></div>
<?php if ($datos && !$datos['anteriores']): ?><section class="card"><p>Todavía no hay sprints finalizados. Acá encontrarás sus resultados cuando completes la primera retrospectiva.</p></section><?php endif; ?>
<?php foreach ($datos['anteriores'] ?? [] as $detalle): $s=$detalle['sprint']; ?>
<section class="card history-card"><h2><?= e($s->get_nombre()) ?></h2><p class="muted"><?= e($s->get_fecha_inicio()) ?> — <?= e($s->get_fecha_fin()) ?> · Finalizado</p>
<h3>Lo que ya se realizó</h3><?php $lista=$detalle['realizadas']; $vacio='No hubo objetivos marcados como realizados.'; require __DIR__.'/partials/lista_objetivos.php'; ?>
<h3>Lo que faltó</h3><?php $lista=$detalle['pendientes']; $vacio='No quedaron objetivos pendientes.'; require __DIR__.'/partials/lista_objetivos.php'; ?>
<details><summary>Logros, impedimentos y acuerdos de la reunión</summary><?php $lista=array_filter($detalle['items'],fn($i)=>$i->get_categoria()!=='accion'); require __DIR__.'/partials/retro_items.php'; ?></details></section>
<?php endforeach; ?>
<?php require __DIR__.'/partials/footer.php'; ?>
