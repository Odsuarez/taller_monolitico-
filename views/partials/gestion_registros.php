<?php

if (!isset($lista) || !is_array($lista) || !$lista) return;
?>
<details class="gestionar">
    <summary>Editar o eliminar registros guardados</summary>
    <ul class="lista-editable"><?php foreach ($lista as $item): ?>
            <li><span><?= escaparHtml($item->get_descripcion()) ?></span>
                <div class="acciones-registro"><a class="button light" href="index.php?pagina=editar_registro&amp;id=<?= $item->get_id() ?>">Editar</a><a class="button light" href="index.php?pagina=eliminar_registro&amp;id=<?= $item->get_id() ?>">Eliminar</a></div>
            </li>
        <?php endforeach; ?>
    </ul>
</details>