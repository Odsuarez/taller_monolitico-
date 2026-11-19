<?php




if (!isset($token, $pagina)) {
    header('Location: ../index.php');
    exit;
}
require __DIR__ . '/partials/header.php';
?>
<div class="intro">
    <h1>Editar registro</h1>
    <p>Corrige la información guardada en la retrospectiva.</p>
</div>
<?php if (isset($registro, $registroFormulario)): ?>
    <form class="card workflow" method="post" action="index.php?pagina=editar_registro&amp;id=<?= $registro->get_id() ?>">
        <input type="hidden" name="token" value="<?= escaparHtml($token) ?>"><input type="hidden" name="id" value="<?= $registro->get_id() ?>">
        <label>Categoría<select name="categoria" required>
                <?php foreach (['accion' => 'Acción o acuerdo', 'logro' => 'Logro', 'impedimento' => 'Impedimento', 'comentario' => 'Comentario', 'otro' => 'Otro'] as $valor => $titulo): ?>
                    <option value="<?= $valor ?>" <?= $registroFormulario['categoria'] === $valor ? 'selected' : '' ?>><?= escaparHtml($titulo) ?></option>
                <?php endforeach; ?>
            </select></label>
        <label>Descripción<textarea name="descripcion" required maxlength="2000" rows="5"><?= escaparHtml($registroFormulario['descripcion']) ?></textarea></label>
        <label>Cumplimiento de la acción<select name="cumplida">
                <option value="0" <?= $registroFormulario['cumplida'] === '0' ? 'selected' : '' ?>>Pendiente</option>
                <option value="1" <?= $registroFormulario['cumplida'] === '1' ? 'selected' : '' ?>>Realizada</option>
            </select></label>
        <p class="muted">El cumplimiento solo se aplica a acciones y acuerdos. Para las demás categorías se ignora.</p>
        <div class="form-actions"><a class="button light" href="index.php?pagina=anteriores">Cancelar</a><button class="button primary" name="accion" value="guardar_registro">Guardar cambios</button></div>
    </form><?php endif; ?>
<?php require __DIR__ . '/partials/footer.php'; ?>