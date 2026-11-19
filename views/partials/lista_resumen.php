<?php
                        
if (!isset($lista) || !is_array($lista)) return;
$elementosResumen=array_values($lista);
$primerosResumen=array_slice($elementosResumen,0,3);
$restantesResumen=array_slice($elementosResumen,3);
?>
<?php if (!$elementosResumen): ?><p class="muted"><?= escaparHtml($vacio ?? 'No hay elementos registrados.') ?></p>
<?php else: ?>
<ul class="objetivos"><?php foreach ($primerosResumen as $itemResumen): ?><li><?= nl2br(escaparHtml($itemResumen->get_descripcion())) ?></li><?php endforeach; ?></ul>
<?php if ($restantesResumen): ?><details class="mas-elementos"><summary>Ver <?= count($restantesResumen) ?> <?= count($restantesResumen)===1 ? 'elemento más' : 'elementos más' ?></summary><ul class="objetivos"><?php foreach ($restantesResumen as $itemResumen): ?><li><?= nl2br(escaparHtml($itemResumen->get_descripcion())) ?></li><?php endforeach; ?></ul></details><?php endif; ?>
<?php endif; ?>
