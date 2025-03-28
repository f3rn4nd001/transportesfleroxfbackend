<?php

namespace App\Http\Controllers\Sistemas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use App\Http\Controllers\seg\encriptar;

class relrutas extends Controller
{
    public function getRegistro(Request $request){
        $encriptar = new encriptar();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $json               = isset($jsonX->filtros) ? $jsonX->filtros : [];
        $metodos            = isset($jsonX->metodos) ? $jsonX->metodos : [];
        if (is_array($json) || is_object($json)){
            $result = array();
            foreach ($json as $key => $value){
                $result[$key] = $this->objeto_a_array($value);
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
        $select="SELECT rmsc.ecodMenu , rmsc.ecodSubmenu, rmsc.ecodController, cm.tNombre AS tNombreMenu, cs.tNombre AS tNombreSubMenu, cc.tNombre AS tNombreController  FROM relmenusubmenucontroller AS rmsc 	
        LEFT JOIN catmenu cm ON cm.ecodMenu = rmsc.ecodMenu
        LEFT JOIN catsubmenu cs ON cs.ecodSubmenu = rmsc.ecodSubmenu
        LEFT JOIN catcontroller cc ON cc.ecodControler= rmsc.ecodController
        ORDER BY cm.tNombre, cs.tNombre ASC";
        $sql = DB::select($select);
        $jsonData = json_encode($sql);
        $returResponse =$encriptar->shiftText($jsonData, 23);  
        return response()->json( $returResponse);  
    }
    
    public function postRegistro(Request $request){
        $encriptar = new encriptar();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $json  = isset($jsonX->rutas) ? $jsonX->rutas : [];
        if (count($json) > 0) {
            $deleteRelMenuSubControl="TRUNCATE TABLE relmenusubmenucontroller";
            $responsdeleteRelMenuSubControl = DB::select($deleteRelMenuSubControl); 
           foreach ($json as $key => $value) {
                $uuie = Uuid::uuid4();
                $uuid2 = (isset($uuie)&&$uuie!="" ? "".(trim($uuie))."":   Null);   
                $ecodMenu = (isset($value->ecodMenu)&&$value->ecodMenu!="" ? "".(trim($value->ecodMenu))."":   Null);
                $ecodSubmenu = (isset($value->ecodSubmenu)&&$value->ecodSubmenu!="" ? "".(trim($value->ecodSubmenu))."":   Null);
                $ecodController = (isset($value->ecodController)&&$value->ecodController!="" ? "".(trim($value->ecodController))."":   Null);
                $inserRelMenuSubContro=" CALL `stpInsertarRelMenuSubContro`(?, ?, ?, ?)";
                $responseRelMenuSubContro = DB::select($inserRelMenuSubContro,[$uuid2,$ecodMenu,$ecodSubmenu,$ecodController]);    
            }
            $data = [
                'mensaje'=>"exito",
            ];
            $jsonData = json_encode($data);
            $returResponse =$encriptar->shiftText($jsonData, 23);
            return response()->json($returResponse,200);
        }
        else {
            $data = [
                'mensaje'=>"No se guardaron datos",
            ];
            $jsonData = json_encode($data);
            $returResponse =$encriptar->shiftText($jsonData, 23);
            return response()->json($returResponse,202);
        }

    }
}
