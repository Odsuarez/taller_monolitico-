<?php



if (!isset($token, $pagina, $datos) || !$datos['actual']) {
    header('Location: ../index.php');
    exit;
}
require __DIR__ . '/partials/header.php';
$sprint = $datos['actual']['sprint'];
?>
<section class="card workflow">
    <span class="eyebrow">RETROSPECTIVA ANTICIPADA</span>
    <h1>¿Quieres realizarla ahora?</h1>
    <p>La retrospectiva de <strong><?= escaparHtml($sprint->get_nombre()) ?></strong> está programada para el <strong><?= escaparHtml($sprint->get_fecha_fin()) ?></strong>.</p>
    <p>¿Seguro que quieres hacerla ahora? Si continúas, podrás revisar los objetivos y finalizar el sprint antes de esa fecha.</p>
    <form method="post" action="index.php?pagina=retrospectiva">
        <input type="hidden" name="token" value="<?= escaparHtml($token) ?>">
        <input type="hidden" name="sprint_id" value="<?= $sprint->get_id() ?>">
        <div class="form-actions"><button class="button light" name="accion" value="cancelar_adelanto">No, volver al inicio</button><button class="button primary" name="accion" value="confirmar_adelanto">Sí, realizar ahora</button></div>
    </form>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>