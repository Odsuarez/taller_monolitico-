<?php

/** @var string $token */
/** @var string $pagina */
/** @var array|null $datos */
/** @var string|null $error */
$datos = $datos ?? null;
$error = $error ?? null;

if (!isset($token, $pagina)) {
    header('Location: ../index.php');
    exit;
}
require __DIR__ . '/partials/header.php';
?>
<div class="intro"><span class="eyebrow">SEGUIMIENTO DEL EQUIPO</span>
    <h1>El avance de tu proyecto, sprint a sprint.</h1>
    <p>Revisa lo realizado y los objetivos que siguen pendientes.</p>
</div>
<?php if ($datos): ?>
    <div class="resumen-escalonado">
        <section class="card actual-card" id="actual"><span class="eyebrow">EN ESTE MOMENTO</span>
            <h2>Sprint actual</h2>
            <?php if ($datos['actual']): $detalle = $datos['actual'];
                $s = $detalle['sprint']; ?>
                <h3><?= e($s->get_nombre()) ?></h3>
                <p class="muted"><?= e($s->get_fecha_inicio()) ?> — <?= e($s->get_fecha_fin()) ?></p>
                <h3>Lo que ya se realizó</h3><?php $lista = $detalle['realizadas'];
                                                $vacio = 'Todavía no hay objetivos marcados como realizados.';
                                                require __DIR__ . '/partials/lista_objetivos.php'; ?>
                <h3>Lo que falta</h3><?php $lista = $detalle['pendientes'];
                                        $vacio = 'Todos los objetivos están realizados.';
                                        require __DIR__ . '/partials/lista_objetivos.php'; ?>
                <a class="text-link" href="index.php?pagina=retrospectiva">Revisar este sprint →</a>
            <?php elseif ($datos['programado']): $s = $datos['programado']['sprint']; ?>
                <h3><?= e($s->get_nombre()) ?></h3>
                <p>Tu próximo sprint empieza el <?= e($s->get_fecha_inicio()) ?>.</p>
                <h3>Objetivos por realizar</h3><?php $lista = $datos['programado']['pendientes'];
                                                require __DIR__ . '/partials/lista_objetivos.php'; ?>
            <?php else: ?><div class="empty">
                    <h3>Todavía no hay un sprint actual</h3>
                    <p>Acá encontrarás el resumen del sprint actual: lo realizado y lo que falta.</p><a class="text-link" href="index.php?pagina=agendar">Agendar nuevo sprint →</a>
                </div><?php endif; ?>
        </section>
        <section class="card global-card"><span class="eyebrow">TODO LO ACUMULADO</span>
            <h2>Proyecto global</h2>
            <?php if ($datos['anteriores']): ?>
                <p class="muted">Resumen de <?= count($datos['anteriores']) ?> sprints finalizados.</p>
                <h3>Resumen de lo que ya se realizó</h3><?php $lista = $datos['global']['realizadas'];
                                                        $vacio = 'Aún no hay objetivos realizados en el historial.';
                                                        require __DIR__ . '/partials/lista_objetivos.php'; ?>
                <h3>Lo que falta</h3><?php $lista = $datos['global']['pendientes'];
                                        $vacio = 'No quedaron objetivos pendientes en los sprints anteriores.';
                                        require __DIR__ . '/partials/lista_objetivos.php'; ?>
                <p class="muted"><?= count($datos['global']['logros']) ?> logros y <?= count($datos['global']['impedimentos']) ?> impedimentos registrados.</p>
                <a class="text-link" href="index.php?pagina=anteriores">Consultar sprints anteriores →</a>
            <?php else: ?><div class="empty">
                    <p>Acá encontrarás el resumen acumulado del proyecto: lo realizado y lo que quedó pendiente en los sprints anteriores.</p>
                    <p>El historial aparecerá al finalizar tu primera retrospectiva.</p>
                </div><?php endif; ?>
        </section>
    </div><?php endif; ?>
<?php require __DIR__ . '/partials/footer.php'; ?>