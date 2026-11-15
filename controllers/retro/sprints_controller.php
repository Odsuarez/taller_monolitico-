<?php
namespace app\controllers\retro;
use app\models\repositories\retro_repository\RetroRepositoryInterface;
use app\models\entities\Sprint;
use DateTimeImmutable;
use DateTimeZone;
use RuntimeException;
class SprintsController {
    public function __construct(private RetroRepositoryInterface $repository) {}
    public function guardar(array $request): void {
        $id=filter_var($request['id'] ?? null,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
        if (!$id) throw new RuntimeException('Sprint no válido.');
        $original=$this->repository->get_sprint_abierto($id);
        foreach (['nombre','inicio','fin'] as $campo) if (!is_string($request[$campo] ?? null)) throw new RuntimeException('Completa los datos del sprint.');
        $nombre=trim($request['nombre']);$inicio=$request['inicio'];$fin=$request['fin'];
        $valida=function($texto) { $fecha=DateTimeImmutable::createFromFormat('!Y-m-d',$texto); return $fecha && $fecha->format('Y-m-d')===$texto; };
        if ($nombre==='' || mb_strlen($nombre)>100 || !$valida($inicio) || !$valida($fin) || $fin<$inicio) throw new RuntimeException('Revisa el nombre y las fechas del sprint.');
        $hoy=(new DateTimeImmutable('today',new DateTimeZone('America/Bogota')))->format('Y-m-d');
        if ($original->get_fecha_inicio()<=$hoy && $inicio!==$original->get_fecha_inicio()) throw new RuntimeException('El sprint ya comenzó; su fecha de inicio se conserva.');
        if ($inicio!==$original->get_fecha_inicio() && $inicio<$hoy) throw new RuntimeException('La fecha de inicio no puede ser anterior a hoy.');
        if ($fin<$hoy) throw new RuntimeException('La fecha final no puede ser anterior a hoy.');
        if ($original->get_fecha_fin()<$hoy) throw new RuntimeException('Primero realiza la retrospectiva pendiente.');
        $this->repository->update_sprint(new Sprint($id,$nombre,$inicio,$fin));
    }
}
