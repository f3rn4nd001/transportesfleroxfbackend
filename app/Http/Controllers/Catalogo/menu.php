<?php

namespace App\Http\Controllers\Catalogo;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\seg\encriptar;
use App\Http\Controllers\seg\objetArray;

class menu extends Controller
{
    public function getRegistro(Request $request){
        $encriptar = new encriptar();
        $objetArray = new objetArray();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $json               = isset($jsonX->filtros) ? $jsonX->filtros : [];
        $metodos            = isset($jsonX->metodos) ? $jsonX->metodos : [];
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
        
        $selectEstustus="SELECT cm.ecodMenu, cm.tNombre, ce.tNombre AS estatus FROM catmenu AS cm 
        LEFT JOIN catestatus ce ON ce.ecodEstatus =cm.ecodEstatus". " WHERE 1=1 ".  
        (isset($ecodMenu)       ? " AND cm.ecodMenu LIKE ('%".$ecodMenu."%')"        : '').
        (isset($tNombre)        ? " AND cm.tNombre LIKE ('%".$tNombre."%')"        : '').
        (isset($estatus)        ? " AND ce.tNombre LIKE ('%".$estatus."%')"        : '').
        (isset($metodos->orden) ? 'ORDER BY '.$metodos->tMetodoOrdenamiento." ".$metodos->orden : 'ASC')." ".
        (isset($metodos->eNumeroRegistros) && (int)$metodos->eNumeroRegistros>0 ? 'LIMIT '.$metodos->eNumeroRegistros : '');
        $sql = DB::select($selectEstustus);
        $sql = DB::select($selectEstustus);
        $jsonData = json_encode($sql);
        $returResponse =$encriptar->shiftText($jsonData, 23);  
        return response()->json( $returResponse);  
    }

    public function getDetalles(Request $request){
        $encriptar = new encriptar();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $json = (isset($jsonX->data)&&$jsonX->data!="" ? "".(trim($jsonX->data))."":  Null);
        $selectMenu="SELECT concat_ws('',cuc.tNombre,' ',cuc.tApellido) AS nombresCreador, cm.ecodEstatus, ci.tNombre AS nombreIcono, cm.ecodIconos,concat_ws('',cue.tNombre,' ',cue.tApellido) AS nombresEditor, cm.ecodMenu, cm.tNombre, ci.tIcono AS Iconos, cm.ecodCreacion, cm.fhCreacion,cm.ecodEdicion,cm.fhEdicion, ce.tNombre as Estatus FROM catmenu cm 
        LEFT JOIN catestatus ce ON ce.EcodEstatus = cm.ecodEstatus 
        LEFT JOIN catusuarios cuc ON cuc.ecodUsuario = cm.ecodCreacion
        LEFT JOIN caticono ci ON ci.ecodIcono =cm.ecodIconos
        LEFT JOIN catusuarios cue ON cue.ecodUsuario = cm.ecodEdicion WHERE cm.ecodMenu =?";
        $sql = DB::select($selectMenu,[$json]);
        $jsonData = json_encode($sql[0]);
        $sqlMenu =$encriptar->shiftText($jsonData, 23);
        return response()->json($sqlMenu);
    }
      
    public function postRegistro(Request $request){
        $encriptar = new encriptar();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $json = isset($jsonX->Menu) ? $jsonX->Menu : [];
        $jsonH =json_decode($encriptar->shiftText($request['headers'], -23));
        $tNombre    = (isset($json->tNombre)&&$json->tNombre!=""        ? "".(trim($json->tNombre))."":   Null);
        $ecodMenu    = (isset($json->ecodMenu)&&$json->ecodMenu!=""        ? "".(trim($json->ecodMenu))."":   Null);
        $ecodIconos    = (isset($json->ecodIconos)&&$json->ecodIconos!=""        ? "".(trim($json->ecodIconos))."":   Null);
        $selectEcodUsuario="SELECT * FROM relusuariocorreo ruc WHERE ruc.ecodCorreo =".$jsonH->ecodCorreo;
        $sqlEcodUsuario = DB::select($selectEcodUsuario); 
        $ecodUsuario = (isset($sqlEcodUsuario[0]->ecodUsuario) && $sqlEcodUsuario[0]->ecodUsuario != "" ? "" . (trim($sqlEcodUsuario[0]->ecodUsuario)) . "" : "");             
       if ($ecodMenu == Null) {
            $uui = Uuid::uuid4();
            $uuid2 = (isset($uui)&&$uui!="" ? "".(trim($uui))."":   Null);
            $ecodEstatus = "2660376e-dbf8-44c1-b69f-b2554e3e5d4c";
            $inserMenu=" CALL `stpInsertarCatMenu`(?, ?, ?, ?, ?)";
            $responseMenu = DB::select($inserMenu,[$uuid2,$tNombre,$ecodIconos,$ecodEstatus,$ecodUsuario]); 
        }
        else{
            $selectlogcatMenu="SELECT * FROM catmenu cu WHERE cu.ecodMenu = ?";
            $sqllogCatMenu = DB::select($selectlogcatMenu,[$ecodMenu]);
            $logtNombre = (isset($sqllogCatMenu[0]->tNombre) && $sqllogCatMenu[0]->tNombre != "" ? "" . (trim($sqllogCatMenu[0]->tNombre)) . "" : "");             
            $logecodIconos = (isset($sqllogCatMenu[0]->ecodIconos) && $sqllogCatMenu[0]->ecodIconos != "" ? "" . (trim($sqllogCatMenu[0]->ecodIconos)) . "" : Null);             
            $logecodCreacion = (isset($sqllogCatMenu[0]->ecodCreacion) && $sqllogCatMenu[0]->ecodCreacion != "" ? "" . (trim($sqllogCatMenu[0]->ecodCreacion)) . "" : "");             
            $logfhCreacion = (isset($sqllogCatMenu[0]->fhCreacion) && $sqllogCatMenu[0]->fhCreacion != "" ? "" . (trim($sqllogCatMenu[0]->fhCreacion)) . "" : "");             
            $logecodEdicion = (isset($sqllogCatMenu[0]->ecodEdicion) && $sqllogCatMenu[0]->ecodEdicion != "" ? "" . (trim($sqllogCatMenu[0]->ecodEdicion)) . "" : Null);             
            $logfhEdicion = (isset($sqllogCatMenu[0]->fhEdicion) && $sqllogCatMenu[0]->fhEdicion != "" ? "" . (trim($sqllogCatMenu[0]->fhEdicion)) . "" : Null);             
            $logecodEstatus = (isset($sqllogCatMenu[0]->ecodEstatus) && $sqllogCatMenu[0]->ecodEstatus != "" ? "" . (trim($sqllogCatMenu[0]->ecodEstatus)) . "" : "");             
            $ecodEstatus    = (isset($json->ecodEstatus)&&$json->ecodEstatus!=""        ? "".(trim($json->ecodEstatus))."":   Null);
            $loguuid = Uuid::uuid4();
            $loguuid2 = (isset($loguuid)&&$loguuid!="" ? "".(trim($loguuid))."":   Null);
            $insertarLogMenu=" CALL `stpInsertarLogMenu`(?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $responseinsertarLogMenu = DB::select($insertarLogMenu,[$loguuid2,$ecodMenu,$logtNombre,$logecodIconos,$logecodCreacion,$logfhCreacion,$logecodEdicion,$logfhEdicion,$logecodEstatus]);
            $inserMenu=" CALL `stpInsertarCatMenu`(?, ?, ?, ?, ?)";
            $responseMenu = DB::select($inserMenu,[$ecodMenu,$tNombre,$ecodIconos,$ecodEstatus,$ecodUsuario]); 
        }
        $jsonData = json_encode($responseMenu[0]);
        $returResponse2 =$encriptar->shiftText($jsonData, 23);
        return response()->json($returResponse2);
    
    }
    
    public function getComprementos(Request $request){
        $encriptar = new encriptar();
        $objetArray = new objetArray();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $json               = isset($jsonX->filtros) ? $jsonX->filtros : [];
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
        $selectEstustus="SELECT cm.tNombre,cm.ecodMenu  FROM catmenu cm
        LEFT JOIN catestatus ce ON ce.ecodEstatus = cm.ecodEstatus
        WHERE 1=1 AND ce.tNombre = 'Activo' ".  
        (isset($tNombre)        ? " AND cm.tNombre LIKE ('%".$tNombre."%')"        : '')." ".
        'LIMIT 7' ;
        $sql = DB::select($selectEstustus);
        $jsonData = json_encode($sql);
        $returResponse2 =$encriptar->shiftText($jsonData, 23);
        return response()->json($returResponse2);
    }
}
