<?php
/** @var array $lista */
if (!isset($lista) || !is_array($lista)) { return; }
 if (!$lista): ?><p class="muted"><?= e($vacio ?? 'No hay elementos registrados.') ?></p>
<?php else: ?><ul class="objetivos"><?php foreach ($lista as $item): ?><li><?= nl2br(e($item->get_descripcion())) ?></li><?php endforeach; ?></ul><?php endif; ?>
