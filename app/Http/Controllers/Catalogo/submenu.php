<?php

namespace App\Http\Controllers\Catalogo;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\seg\encriptar;
use App\Http\Controllers\seg\objetArray;

class submenu extends Controller
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
        $selectEstustus="SELECT csm.ecodSubmenu , csm.tNombre, ce.tNombre AS estatus FROM catsubmenu AS csm 
        LEFT JOIN catestatus ce ON ce.ecodEstatus =csm.ecodEstatus". " WHERE 1=1 ".  
        (isset($ecodSubmenu)    ? " AND csm.ecodSubmenu  LIKE ('%".$ecodSubmenu ."%')"        : '').
        (isset($tNombre)        ? " AND csm.tNombre LIKE ('%".$tNombre."%')"       : '').
        (isset($estatus)        ? " AND ce.tNombre LIKE ('%".$estatus."%')"        : '').
        (isset($metodos->orden) ? 'ORDER BY '.$metodos->tMetodoOrdenamiento." ".$metodos->orden : 'ASC')." ".
        (isset($metodos->eNumeroRegistros) && (int)$metodos->eNumeroRegistros>0 ? 'LIMIT '.$metodos->eNumeroRegistros : '');
        $sql = DB::select($selectEstustus);
        $jsonData = json_encode($sql);
        $returResponse =$encriptar->shiftText($jsonData, 23);  
        return response()->json( $returResponse);  
    }

    public function getDetalles(Request $request){
        $encriptar = new encriptar();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $json = (isset($jsonX->data)&&$jsonX->data!="" ? "".(trim($jsonX->data))."":   Null);
        $selectSubMenu="SELECT  concat_ws('',cuc.tNombre,' ',cuc.tApellido) AS nombresCreador, csm.ecodEstatus, ce.tNombre as Estatus,concat_ws('',cue.tNombre,' ',cue.tApellido) AS nombresEditor,csm.ecodSubmenu,csm.tNombre,csm.tUrl,csm.fhCreacion, csm.fhEdicion,csm.ecodEdicion FROM catsubmenu csm
        LEFT JOIN catestatus ce ON ce.EcodEstatus = csm.ecodEstatus 
        LEFT JOIN catusuarios cuc ON cuc.ecodUsuario = csm.ecodCreacion
				LEFT JOIN catusuarios cue ON cue.ecodUsuario = csm.ecodEdicion
				WHERE csm.ecodSubmenu = ?";
        $sql = DB::select($selectSubMenu,[$json]);
        $jsonData = json_encode($sql[0]);
        $sqlSubMenu =$encriptar->shiftText($jsonData, 23);
        return response()->json($sqlSubMenu);
    }

    public function postRegistro(Request $request){
        $encriptar = new encriptar();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $json  = isset($jsonX->SubMenu) ? $jsonX->SubMenu : [];
        $jsonH =json_decode($encriptar->shiftText($request['headers'], -23));
        $tNombre = (isset($json->tNombre)&&$json->tNombre!="" ? "".(trim($json->tNombre))."":   Null);
        $ecodSubMenu = (isset($json->ecodSubMenu)&&$json->ecodSubMenu!="" ? "".(trim($json->ecodSubMenu))."":   Null);
        $tUrl = (isset($json->tUrl)&&$json->tUrl!="" ? "".(trim($json->tUrl))."":   Null);
        $selectEcodUsuario="SELECT * FROM relusuariocorreo ruc WHERE ruc.ecodCorreo =".$jsonH->ecodCorreo;
        $sqlEcodUsuario = DB::select($selectEcodUsuario); 
        $ecodUsuario = (isset($sqlEcodUsuario[0]->ecodUsuario) && $sqlEcodUsuario[0]->ecodUsuario != "" ? "" . (trim($sqlEcodUsuario[0]->ecodUsuario)) . "" : "");             
        if ($ecodSubMenu == Null) {
            $uui = Uuid::uuid4();
            $uuid2 = (isset($uui)&&$uui!="" ? "".(trim($uui))."":   Null);
            $ecodEstatus = "2660376e-dbf8-44c1-b69f-b2554e3e5d4c";
            $inserSubMenu=" CALL `stpInsertarCatSubMenu`(?, ?, ?, ?, ?)";
            $responseSubMenu = DB::select($inserSubMenu,[$uuid2,$tNombre,$tUrl,$ecodEstatus,$ecodUsuario]); 
        }
        else{
            $selectlogcatSubMenu="SELECT * FROM catsubmenu csu WHERE csu.ecodSubmenu = ?";
            $sqllogCatSubMenu = DB::select($selectlogcatSubMenu,[$ecodSubMenu]);
            $logtNombre = (isset($sqllogCatSubMenu[0]->tNombre) && $sqllogCatSubMenu[0]->tNombre != "" ? "" . (trim($sqllogCatSubMenu[0]->tNombre)) . "" : "");             
            $logtUrl = (isset($sqllogCatSubMenu[0]->tUrl) && $sqllogCatSubMenu[0]->tUrl != "" ? "" . (trim($sqllogCatSubMenu[0]->tUrl)) . "" : "");             
            $logecodCreacion = (isset($sqllogCatSubMenu[0]->ecodCreacion) && $sqllogCatSubMenu[0]->ecodCreacion != "" ? "" . (trim($sqllogCatSubMenu[0]->ecodCreacion)) . "" : "");             
            $logfhCreacion = (isset($sqllogCatSubMenu[0]->fhCreacion) && $sqllogCatSubMenu[0]->fhCreacion != "" ? "" . (trim($sqllogCatSubMenu[0]->fhCreacion)) . "" : "");             
            $logecodEdicion = (isset($sqllogCatSubMenu[0]->ecodEdicion) && $sqllogCatSubMenu[0]->ecodEdicion != "" ? "" . (trim($sqllogCatSubMenu[0]->ecodEdicion)) . "" : Null);             
            $logfhEdicion = (isset($sqllogCatSubMenu[0]->fhEdicion) && $sqllogCatSubMenu[0]->fhEdicion != "" ? "" . (trim($sqllogCatSubMenu[0]->fhEdicion)) . "" : Null);             
            $logecodEstatus = (isset($sqllogCatSubMenu[0]->ecodEstatus) && $sqllogCatSubMenu[0]->ecodEstatus != "" ? "" . (trim($sqllogCatSubMenu[0]->ecodEstatus)) . "" : "");             
            $loguuid = Uuid::uuid4();
            $loguuid2 = (isset($loguuid)&&$loguuid!="" ? "".(trim($loguuid))."":   Null);
            $insertarLogSubMenu=" CALL `stpInsertarLogSubMenu`(?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $responseinsertarLogSubMenu = DB::select($insertarLogSubMenu,[$loguuid2,$ecodSubMenu,$logtNombre,$logtUrl,$logecodCreacion,$logfhCreacion,$logecodEdicion,$logfhEdicion,$logecodEstatus]);
            $ecodEstatus = (isset($json->ecodEstatus)&&$json->ecodEstatus!="" ? "".(trim($json->ecodEstatus))."":   Null);
            $inserSubMenu=" CALL `stpInsertarCatSubMenu`(?, ?, ?, ?, ?)";
            $responseSubMenu = DB::select($inserSubMenu,[$ecodSubMenu,$tNombre,$tUrl,$ecodEstatus,$ecodUsuario]); 
        }
        $jsonData = json_encode($responseSubMenu[0]);
        $returResponse2 =$encriptar->shiftText($jsonData, 23);
        return response()->json($returResponse2,200);
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
        $selectEstustus="SELECT cm.tNombre, cm.ecodSubmenu, cm.tUrl   FROM catsubmenu cm
        LEFT JOIN catestatus ce ON ce.ecodEstatus = cm.ecodEstatus
        WHERE 1=1 AND ce.tNombre = 'Activo' ".  
        (isset($tNombre)        ? " AND cm.tNombre LIKE ('%".$tNombre."%')"  : '')." ".
        'LIMIT 7' ;
        $sql = DB::select($selectEstustus);
        $jsonData = json_encode($sql);
        $returResponse2 =$encriptar->shiftText($jsonData, 23);
        return response()->json($returResponse2);
    }
}
