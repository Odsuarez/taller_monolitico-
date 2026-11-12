<?php
$tipos = ['accion'=>'Acción','logro'=>'Logro','impedimento'=>'Impedimento','comentario'=>'Comentario','otro'=>'Otro'];
?>
<?php if (!$lista): ?>
<p class="empty">Este sprint todavía no tiene registros de retrospectiva.</p>
<?php else: ?>
<ul class="items">
<?php foreach ($lista as $item):
    $estado = $item->get_categoria() === 'accion'
        ? ($item->get_cumplida() === null ? 'Sin evaluar' : ($item->get_cumplida() ? 'Realizada' : 'Pendiente'))
        : $tipos[$item->get_categoria()];
?>
<li><span class="badge"><?= e($estado) ?></span><div><small><?= e($tipos[$item->get_categoria()]) ?></small><p><?= nl2br(e($item->get_descripcion())) ?></p></div></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
