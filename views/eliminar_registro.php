<?php
/** @var string $token */
/** @var string $pagina */
/** @var \app\models\entities\RetroItem|null $registro */
if (!isset($token,$pagina)) { header('Location: ../index.php'); exit; }
require __DIR__.'/partials/header.php';
?>
<section class="card workflow"><h1>¿Eliminar este registro?</h1>
<?php if (isset($registro)): ?>
<p><?= nl2br(e($registro->get_descripcion())) ?></p>
<p>Se eliminará esta entrada de la retrospectiva. El sprint se conservará.</p>
<form method="post" action="index.php?pagina=eliminar_registro&amp;id=<?= $registro->get_id() ?>">
<input type="hidden" name="token" value="<?= e($token) ?>"><input type="hidden" name="id" value="<?= $registro->get_id() ?>">
<div class="form-actions"><a class="button light" href="index.php?pagina=anteriores">Cancelar</a><button class="button primary" name="accion" value="eliminar_registro">Sí, eliminar</button></div></form>
<?php endif; ?></section>
<?php require __DIR__.'/partials/footer.php'; ?>
