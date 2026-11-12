<?php
namespace app\controllers\retro;
interface RetroControllerInterface {
    public function get_resumen(?string $hoy=null): array;
    public function agendar(array $request): int;
    public function finalizar(array $request): void;
}
