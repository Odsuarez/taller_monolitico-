<?php
namespace app\controllers\retro;
use RuntimeException;
class FormulariosController {
    public static function agenda_inicial(): array {
        return ['nombre'=>'','inicio'=>'','fin'=>'','nuevo'=>'','objetivos'=>[],'retomar'=>[]];
    }
    private static function texto(array $request, string $campo): string {
        $valor=$request[$campo] ?? '';
        if (!is_string($valor)) throw new RuntimeException('Revisa los datos del formulario.');
        return trim($valor);
    }
    public static function editar_agenda(array &$agenda, array $request): bool {
        foreach (['nombre','inicio','fin'] as $campo) $agenda[$campo]=self::texto($request,$campo);
        $agenda['nuevo']=self::texto($request,'nuevo');
        $retomar=$request['retomar'] ?? [];
        if (!is_array($retomar) || count(array_filter($retomar,'is_scalar'))!==count($retomar)) throw new RuntimeException('Revisa los pendientes seleccionados.');
        $agenda['retomar']=array_map('strval',$retomar);
        $accion=$request['accion'] ?? '';
        if (!is_string($accion)) throw new RuntimeException('Operación no válida.');
        if (str_starts_with($accion,'quitar:')) {
            $indice=filter_var(substr($accion,7),FILTER_VALIDATE_INT,['options'=>['min_range'=>0]]);
            if ($indice===false || !isset($agenda['objetivos'][$indice])) throw new RuntimeException('El objetivo ya no está en la lista.');
            array_splice($agenda['objetivos'],$indice,1);
            return false;
        }
        if (!in_array($accion,['anadir','agendar'],true)) throw new RuntimeException('Operación no válida.');
        if ($agenda['nuevo']!=='') {
            if (mb_strlen($agenda['nuevo'])>2000 || preg_match('/\R/u',$agenda['nuevo'])) throw new RuntimeException('Escribe un objetivo de hasta 2000 caracteres, en una sola línea.');
            if (!in_array($agenda['nuevo'],$agenda['objetivos'],true)) {
                if (count($agenda['objetivos'])>=100) throw new RuntimeException('Puedes añadir hasta 100 objetivos.');
                $agenda['objetivos'][]=$agenda['nuevo'];
            }
            $agenda['nuevo']='';
        } elseif ($accion==='anadir') throw new RuntimeException('Escribe un objetivo antes de añadirlo.');
        return $accion==='agendar';
    }
    public static function revision_inicial(array $detalle): array {
        $realizadas=[];
        foreach ($detalle['items'] as $i) if ($i->get_categoria()==='accion' && $i->get_cumplida()===true) $realizadas[]=(string)$i->get_id();
        return ['sprint_id'=>$detalle['sprint']->get_id(),'paso'=>1,'realizadas'=>$realizadas,'adicionales'=>'','logros'=>'','impedimentos'=>'','comentarios'=>''];
    }
    public static function editar_revision(array &$revision, array $request): bool {
        if ((string)($request['sprint_id'] ?? '')!==(string)$revision['sprint_id']) throw new RuntimeException('Este formulario pertenece a otro sprint.');
        if ($revision['paso']===1) {
            $seleccion=$request['realizadas'] ?? [];
            if (!is_array($seleccion) || count(array_filter($seleccion,'is_scalar'))!==count($seleccion)) throw new RuntimeException('Revisa los objetivos seleccionados.');
            $revision['realizadas']=array_map('strval',$seleccion);
            $revision['adicionales']=self::texto($request,'adicionales');
        } elseif ($revision['paso']===2) {
            foreach (['logros','impedimentos','comentarios'] as $campo) $revision[$campo]=self::texto($request,$campo);
        }
        $accion=$request['accion'] ?? '';
        if ($accion==='anterior') $revision['paso']=max(1,$revision['paso']-1);
        elseif ($accion==='continuar') $revision['paso']=min(3,$revision['paso']+1);
        elseif ($accion==='finalizar' && $revision['paso']===3) return true;
        else throw new RuntimeException('Operación no válida.');
        return false;
    }
}
