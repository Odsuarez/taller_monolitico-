<?php
/** @var string $token */
/** @var string $pagina */
/** @var string|null $error */
$error = $error ?? null;
$retrospectivaVencida = $retrospectivaVencida ?? false;

if (!isset($token, $pagina)) { header('Location: ../index.php'); exit; }
require_once __DIR__.'/../helpers.php';
?>
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Retrospectivas Ágiles</title><link rel="stylesheet" href="public/css/principal.css"></head><body>
<header class="app-header">
<?php if ($retrospectivaVencida): ?>
<span class="page-title">Retrospectivas Ágiles</span>
<nav aria-label="Navegación principal"><span class="button primary" aria-current="page">Retrospectiva pendiente</span><span class="button" aria-disabled="true">Ver sprint actual</span><span class="button" aria-disabled="true">Ver sprints anteriores</span></nav>
<?php else: ?>
<a class="page-title" href="index.php">Retrospectivas Ágiles</a>
<nav aria-label="Navegación principal"><a class="button primary" href="index.php?pagina=retrospectiva">Iniciar retrospectiva</a><a class="button" href="index.php#actual">Ver sprint actual</a><a class="button" href="index.php?pagina=anteriores">Ver sprints anteriores</a></nav>
<?php endif; ?>
</header>
<main><?php if ($error): ?><p class="alert" role="alert"><?= e($error) ?></p><?php endif; ?>
