<?php

namespace app\controllers\retro;

use app\models\repositories\retro_repository\RetroRepositoryInterface;
use app\models\entities\RetroItem;
use RuntimeException;

class RegistrosController
{
    public function __construct(private RetroRepositoryInterface $repository) {}
    public function get_item($id): RetroItem
    {
        $id = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$id) throw new RuntimeException('Identificador de registro no válido.');
        return $this->repository->get_item($id);
    }
    public function guardar(array $request): int
    {
        $item = $this->get_item($request['id'] ?? null);
        $categoria = $request['categoria'] ?? '';
        $descripcion = $request['descripcion'] ?? '';
        if (!is_string($categoria) || !in_array($categoria, ['accion', 'logro', 'impedimento', 'comentario', 'otro'], true)) throw new RuntimeException('Selecciona una categoría válida.');
        if (!is_string($descripcion) || trim($descripcion) === '' || mb_strlen(trim($descripcion)) > 2000) throw new RuntimeException('Escribe una descripción de entre 1 y 2000 caracteres.');
        $cumplida = null;
        if ($categoria === 'accion') {
            $estado = $request['cumplida'] ?? '0';
            if (!in_array($estado, ['0', '1'], true)) throw new RuntimeException('Selecciona un estado válido.');
            $cumplida = $estado === '1';
        }
        $this->repository->update_item($item->get_id(), $categoria, trim($descripcion), $cumplida);
        return $item->get_sprint_id();
    }
    public function eliminar($id): int
    {
        $item = $this->get_item($id);
        $this->repository->delete_item($item->get_id());
        return $item->get_sprint_id();
    }
}
