<?php

namespace App\Http\Controllers\Catalogo;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\seg\encriptar;
use App\Http\Controllers\seg\objetArray;

class controlles extends Controller
{
    
    
    public function getRegistro(Request $request){
        $encriptar = new encriptar();
        $objetArray = new objetArray();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $json    = isset($jsonX->filtros) ? $jsonX->filtros : [];
        $metodos = isset($jsonX->metodos) ? $jsonX->metodos : [];
        if (is_array($json) || is_object($json)){
            $result = array();
            foreach ($json as $key => $value){
                $result[$key] = $objetArray->objeto_a_array($value);
            }
            $result; 
        }

        foreach ($result as $key => $value) {
            if(array_key_exists($key, $result) ){
				if ($value != ''){
					${$key} =$value ;
				}
			}
        }

        $selectEstructura="SELECT cct.ecodControler, cct.tNombre, ce.tNombre AS estatus FROM catcontroller AS cct 
        LEFT JOIN catestatus ce ON ce.ecodEstatus =cct.ecodEstatus". " WHERE 1=1 ".  
        (isset($ecodControler)  ? " AND cct.ecodControler  LIKE ('%".$ecodControler ."%')"        : '').
        (isset($tNombre)        ? " AND cct.tNombre LIKE ('%".$tNombre."%')"       : '').
        (isset($estatus)        ? " AND ce.tNombre LIKE ('%".$estatus."%')"        : '').
        (isset($metodos->orden) ? 'ORDER BY '.$metodos->tMetodoOrdenamiento." ".$metodos->orden : 'ASC')." ".
        (isset($metodos->eNumeroRegistros) && (int)$metodos->eNumeroRegistros>0 ? 'LIMIT '.$metodos->eNumeroRegistros : '');
        $sql = DB::select($selectEstructura);
        $jsonData = json_encode($sql);
        $returResponse =$encriptar->shiftText($jsonData, 23);  
        return response()->json( $returResponse);  
    }

    public function getDetalles(Request $request){
        $encriptar = new encriptar();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $json = (isset($jsonX->data)&&$jsonX->data!="" ? "".(trim($jsonX->data))."":   Null);
        $selectControllers="SELECT  concat_ws('',cuc.tNombre,' ',cuc.tApellido) AS nombresCreador, cct.ecodEstatus, ce.tNombre as Estatus,concat_ws('',cue.tNombre,' ',cue.tApellido) AS nombresEditor,
        cct.ecodControler,cct.tNombre,cct.tUrl,cct.fhCreacion, cct.fhEdicion,cct.ecodEdicion FROM catcontroller cct
            LEFT JOIN catestatus ce ON ce.EcodEstatus = cct.ecodEstatus 
            LEFT JOIN catusuarios cuc ON cuc.ecodUsuario = cct.ecodCreacion
            LEFT JOIN catusuarios cue ON cue.ecodUsuario = cct.ecodEdicion
            WHERE cct.ecodControler = ?";
        $sql = DB::select($selectControllers,[$json]);
        $jsonData = json_encode($sql[0]);
        $sqlControllers =$encriptar->shiftText($jsonData, 23);
        return response()->json($sqlControllers);
    }

    public function postRegistro(Request $request){
        $encriptar = new encriptar();
        $jsonX = json_decode($encriptar->shiftText($request['datos'], -23));
        $json = isset($jsonX->Controlador) ? $jsonX->Controlador : [];
        $jsonH =json_decode($encriptar->shiftText($request['headers'], -23));
       
        $tNombre  = (isset($json->tNombre)&&$json->tNombre!=""  ? "".(trim($json->tNombre))."":   Null);
        $ecodControllers = (isset($json->ecodControllers)&&$json->ecodControllers!="" ? "".(trim($json->ecodControllers))."":   Null);
        $tUrl = (isset($json->tUrl)&&$json->tUrl!="" ? "".(trim($json->tUrl))."":   Null);
        $selectEcodUsuario="SELECT * FROM relusuariocorreo ruc WHERE ruc.ecodCorreo =".$jsonH->ecodCorreo;
        $sqlEcodUsuario = DB::select($selectEcodUsuario); 
        $ecodUsuario = (isset($sqlEcodUsuario[0]->ecodUsuario) && $sqlEcodUsuario[0]->ecodUsuario != "" ? "" . (trim($sqlEcodUsuario[0]->ecodUsuario)) . "" : "");
        if ($ecodControllers == Null) {
            $uui = Uuid::uuid4();
            $uuid2 = (isset($uui)&&$uui!="" ? "".(trim($uui))."":   Null);
            $ecodEstatus = "2660376e-dbf8-44c1-b69f-b2554e3e5d4c";
            $inserControllers=" CALL `stpInsertarCatControllers`(?, ?, ?, ?, ?)";
            $responseControllers = DB::select($inserControllers,[$uuid2,$tNombre,$tUrl,$ecodEstatus,$ecodUsuario]); 
        }
        else{
            $selectlogcatControllers="SELECT * FROM catcontroller cc WHERE cc.ecodControler = ?";
            $sqllogCatControllers = DB::select($selectlogcatControllers,[$ecodControllers]);
            $logtNombre = (isset($sqllogCatControllers[0]->tNombre) && $sqllogCatControllers[0]->tNombre != "" ? "" . (trim($sqllogCatControllers[0]->tNombre)) . "" : "");             
            $logtUrl = (isset($sqllogCatControllers[0]->tUrl) && $sqllogCatControllers[0]->tUrl != "" ? "" . (trim($sqllogCatControllers[0]->tUrl)) . "" : "");             
            $logecodCreacion = (isset($sqllogCatControllers[0]->ecodCreacion) && $sqllogCatControllers[0]->ecodCreacion != "" ? "" . (trim($sqllogCatControllers[0]->ecodCreacion)) . "" : "");             
            $logfhCreacion = (isset($sqllogCatControllers[0]->fhCreacion) && $sqllogCatControllers[0]->fhCreacion != "" ? "" . (trim($sqllogCatControllers[0]->fhCreacion)) . "" : "");             
            $logecodEdicion = (isset($sqllogCatControllers[0]->ecodEdicion) && $sqllogCatControllers[0]->ecodEdicion != "" ? "" . (trim($sqllogCatControllers[0]->ecodEdicion)) . "" : Null);             
            $logfhEdicion = (isset($sqllogCatControllers[0]->fhEdicion) && $sqllogCatControllers[0]->fhEdicion != "" ? "" . (trim($sqllogCatControllers[0]->fhEdicion)) . "" : Null);             
            $logecodEstatus = (isset($sqllogCatControllers[0]->ecodEstatus) && $sqllogCatControllers[0]->ecodEstatus != "" ? "" . (trim($sqllogCatControllers[0]->ecodEstatus)) . "" : "");             
            $loguuid = Uuid::uuid4();
            $loguuid2 = (isset($loguuid)&&$loguuid!="" ? "".(trim($loguuid))."":   Null);
            $insertarLogSubMenu=" CALL `stpInsertarLogControllers`(?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $responseinsertarLogSubMenu = DB::select($insertarLogSubMenu,[$loguuid2,$ecodControllers,$logtNombre,$logtUrl,$logecodCreacion,$logfhCreacion,$logecodEdicion,$logfhEdicion,$logecodEstatus]);
            $ecodEstatus = (isset($json->ecodEstatus)&&$json->ecodEstatus!="" ? "".(trim($json->ecodEstatus))."":   Null);
            $inserControllers=" CALL `stpInsertarCatControllers`(?, ?, ?, ?, ?)";
            $responseControllers = DB::select($inserControllers,[$ecodControllers,$tNombre,$tUrl,$ecodEstatus,$ecodUsuario]); 
        }
        $jsonData = json_encode($responseControllers[0]);
        $returResponse2 =$encriptar->shiftText($jsonData, 23);
        return response()->json($returResponse2);
    }

    public function postEliminar(Request $request){
        $encriptar = new encriptar();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $json = isset($jsonX->formGroup) ? $jsonX->formGroup : [];
        $jsonH =json_decode($encriptar->shiftText($request['headers'], -23));
        $selectEcodUsuario="SELECT * FROM relusuariocorreo ruc WHERE ruc.ecodCorreo =".$jsonH->ecodCorreo;
        $sqlEcodUsuario = DB::select($selectEcodUsuario); 
        $InsertecodUsuario = (isset($sqlEcodUsuario[0]->ecodUsuario) && $sqlEcodUsuario[0]->ecodUsuario != "" ? "" . (trim($sqlEcodUsuario[0]->ecodUsuario)) . "" : "");  
        $ecod = (isset($jsonX->ecod)&&$jsonX->ecod!="" ? "".(trim($jsonX->ecod))."":   Null);
        $mEliminacion = (isset($json->mEliminacion)&&$json->mEliminacion!="" ? "".(trim($json->mEliminacion))."":   Null);
        $selectLogCat="SELECT * FROM catcontroller WHERE ecodControler   = ?";
        $sqllogCat= DB::select($selectLogCat,[$ecod]); 
        $logtNombre  = (isset($sqllogCat[0]->tNombre) && $sqllogCat[0]->tNombre != ""          ? "" . (trim($sqllogCat[0]->tNombre)) . "" : Null);             
        $logtUrl  = (isset($sqllogCat[0]->tUrl) && $sqllogCat[0]->tUrl != ""          ? "" . (trim($sqllogCat[0]->tUrl)) . "" : Null);             
        $logfhCreacion  = (isset($sqllogCat[0]->fhCreacion) && $sqllogCat[0]->fhCreacion != ""          ? "" . (trim($sqllogCat[0]->fhCreacion)) . "" : Null);             
        $logecodCreacion  = (isset($sqllogCat[0]->ecodCreacion) && $sqllogCat[0]->ecodCreacion != ""          ? "" . (trim($sqllogCat[0]->ecodCreacion)) . "" : Null);             
        $logecodEstatus = "fa6cc9a2-f221-4e27-b575-1fac2698d27a";
        $loguuid = Uuid::uuid4();
        $loguuid2 = (isset($loguuid)&&$loguuid!="" ? "".(trim($loguuid))."":   Null);
        $inserLogControllers=" CALL `stpInsertarLogCatControllersEliminar`(?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $responseLogControllers = DB::select($inserLogControllers,[$loguuid2,$ecod,$logtNombre,$logtUrl,$logecodEstatus,$logecodCreacion,$logfhCreacion,$mEliminacion,$InsertecodUsuario]);         
        $jsonData = json_encode($responseLogControllers[0]);
        $returResponse2 =$encriptar->shiftText($jsonData, 23);
        return response()->json($returResponse2,202);
    }

    public function getComprementos(Request $request){
        $encriptar = new encriptar();
        $objetArray = new objetArray();
        $jsonX = json_decode($encriptar->shiftText($request['datos'], -23));
        $json  = isset($jsonX->filtros) ? $jsonX->filtros : [];
        if (is_array($json) || is_object($json)){
            $result = array();
            foreach ($json as $key => $value){
                $result[$key] = $objetArray->objeto_a_array($value);
            }
            $result; 
        }
        foreach ($result as $key => $value) {
            if(array_key_exists($key, $result) ){
				if ($value != ''){
					${$key} =$value ;
				}
			}
        }
        $selectEstustus="SELECT cc.tNombre, cc.ecodControler, cc.tUrl FROM catcontroller cc 
        LEFT JOIN catestatus ce ON ce.ecodEstatus = cc.ecodEstatus
        WHERE 1=1 AND ce.tNombre = 'Activo' ".  
        (isset($tNombre)        ? " AND cc.tNombre LIKE ('%".$tNombre."%')"        : '')." ".
        'LIMIT 7' ;
        $sql = DB::select($selectEstustus);
        $jsonData = json_encode($sql);
        $returResponse2 =$encriptar->shiftText($jsonData, 23);
        return response()->json($returResponse2);
    }
}
