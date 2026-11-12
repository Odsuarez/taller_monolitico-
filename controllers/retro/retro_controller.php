<?php
namespace app\controllers\retro;
use app\models\repositories\retro_repository\RetroRepositoryInterface;
use app\controllers\mappers\SprintsMapperController;
use DateTimeImmutable;
use RuntimeException;
class RetroController implements RetroControllerInterface {
    public function __construct(private RetroRepositoryInterface $repository) {}
    private function lineas($texto): array {
        if (!is_string($texto) || mb_strlen($texto)>50000) throw new RuntimeException('El texto es demasiado largo.');
        $lineas=array_values(array_filter(array_map('trim',preg_split('/\R/u',$texto)),fn($v)=>$v!==''));
        if (count($lineas)>100) throw new RuntimeException('Escribe un máximo de 100 elementos por sección.');
        foreach ($lineas as $linea) if (mb_strlen($linea)>2000) throw new RuntimeException('Cada elemento admite hasta 2000 caracteres.');
        return $lineas;
    }
    public function agendar(array $request): int {
        foreach (['nombre','inicio','fin'] as $campo) if (!isset($request[$campo]) || !is_string($request[$campo])) throw new RuntimeException('Completa los datos del sprint.');
        $sprint=SprintsMapperController::to_entity($request);
        $valida=function($v) { $d=DateTimeImmutable::createFromFormat('!Y-m-d',$v); return $d && $d->format('Y-m-d')===$v; };
        if ($sprint->get_nombre()==='' || mb_strlen($sprint->get_nombre())>100 || !$valida($sprint->get_fecha_inicio())
            || !$valida($sprint->get_fecha_fin()) || $sprint->get_fecha_fin()<$sprint->get_fecha_inicio()) throw new RuntimeException('Revisa el nombre y las fechas del sprint.');
        $objetivos=$this->lineas($request['objetivos'] ?? '');
        $retomar=$request['retomar'] ?? [];
        if (!is_array($retomar)) throw new RuntimeException('Revisa los pendientes que deseas retomar.');
        if ($retomar) {
            $datos=$this->get_resumen();
            $ultimo=$datos['anteriores'][0] ?? null;
            $permitidos=[];
            foreach ($ultimo['pendientes'] ?? [] as $item) $permitidos[$item->get_id()]=$item->get_descripcion();
            foreach (array_unique($retomar, SORT_REGULAR) as $itemId) {
                if (!is_scalar($itemId) || !isset($permitidos[$itemId])) throw new RuntimeException('El pendiente seleccionado no pertenece al último sprint.');
                $objetivos[]=$permitidos[$itemId];
            }
        }
        $objetivos=array_values(array_unique($objetivos));
        if (!$objetivos) throw new RuntimeException('Agrega al menos un objetivo para el nuevo sprint.');
        return $this->repository->agendar($sprint,$objetivos);
    }
    public function finalizar(array $request): void {
        $id=filter_var($request['sprint_id'] ?? null,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
        $seleccion=$request['realizadas'] ?? [];
        if (!$id || !is_array($seleccion)) throw new RuntimeException('Revisa los objetivos seleccionados.');
        $realizadas=[];
        foreach ($seleccion as $v) {
            $item=filter_var($v,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
            if (!$item) throw new RuntimeException('Objetivo no válido.');
            $realizadas[]=$item;
        }
        $nuevos=[];
        foreach (['accion'=>'adicionales','logro'=>'logros','impedimento'=>'impedimentos','comentario'=>'comentarios'] as $cat=>$campo) $nuevos[$cat]=$this->lineas($request[$campo] ?? '');
        $this->repository->finalizar($id,array_unique($realizadas),$nuevos);
    }
    private function detalle($sprint, array $items): array {
        $realizadas=[]; $pendientes=[]; $logros=[]; $impedimentos=[];
        foreach ($items as $item) {
            if ($item->get_categoria()==='accion') {
                if ($item->get_cumplida()===true) $realizadas[]=$item;
                else $pendientes[]=$item;
            } elseif ($item->get_categoria()==='logro') $logros[]=$item;
            elseif ($item->get_categoria()==='impedimento') $impedimentos[]=$item;
        }
        return compact('sprint','items','realizadas','pendientes','logros','impedimentos');
    }
    public function get_resumen(?string $hoy=null): array {
        $hoy ??= date('Y-m-d'); $actual=null; $programado=null; $anteriores=[]; $historial=[];
        $porSprint=[];
        foreach ($this->repository->get_items() as $item) $porSprint[$item->get_sprint_id()][]=$item;
        foreach ($this->repository->get_sprints() as $sprint) {
            $items=$porSprint[$sprint->get_id()] ?? [];
            $detalle=$this->detalle($sprint,$items);
            if ($sprint->esta_finalizado()) { $anteriores[]=$detalle; $historial=array_merge($historial,$items); }
            elseif ($sprint->get_fecha_inicio()<=$hoy && $actual===null) $actual=$detalle;
            elseif ($sprint->get_fecha_inicio()>$hoy && $programado===null) $programado=$detalle;
        }
        $global=$this->detalle(null,$historial);
        return compact('actual','programado','anteriores','global');
    }
}
