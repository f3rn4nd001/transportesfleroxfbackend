<?php

namespace App\Http\Controllers\Sistemas;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Ramsey\Uuid\Uuid;
use App\Http\Requests\Auth\LoginReuest;
use App\Http\Controllers\seg\encriptar;

class relpermisos extends Controller
{
    public function getDetalles(Request $request){
        $encriptar = new encriptar();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $json = (isset($jsonX->data)&&$jsonX->data!="" ? "".(trim($jsonX->data))."":   Null);
        $selectRelUsuarioPermisos="SELECT rmsc.ecodMenu , rmsc.ecodSubmenu, rmsc.ecodController, cm.tNombre AS tNombreMenu, cs.tNombre AS tNombreSubMenu, cc.tNombre AS tNombreController  FROM relusuariomenusubmenucontroller AS rmsc 	
        LEFT JOIN catmenu cm ON cm.ecodMenu = rmsc.ecodMenu
        LEFT JOIN catsubmenu cs ON cs.ecodSubmenu = rmsc.ecodSubmenu
        LEFT JOIN catcontroller cc ON cc.ecodControler= rmsc.ecodController
        WHERE rmsc.ecodUsuario = ? ORDER BY cm.tNombre, cs.tNombre ASC";
        $sqlRelUsuarioPermisos = DB::select($selectRelUsuarioPermisos,[$json]);
        $jsonData = json_encode($sqlRelUsuarioPermisos);
        $returResponse =$encriptar->shiftText($jsonData, 23);        
        return response()->json($returResponse);
    }

    public function postRegistro(Request $request){
        $encriptar = new encriptar();
        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));
        $ecodUsuario = (isset($jsonX->ecodUsuario)&&$jsonX->ecodUsuario!="" ? "'".(trim($jsonX->ecodUsuario))."'":   "NULL");
        $deleteRelUsuarioMenuSubContro="DELETE FROM relusuariomenusubmenucontroller WHERE ecodUsuario = " .$ecodUsuario;
        $responsedeleteRelMenuSubContro = DB::select($deleteRelUsuarioMenuSubContro); 
        $responsCorreo = DB::select("SELECT * FROM relusuariocorreo WHERE ecodUsuario = ".$ecodUsuario); 
        $ecodCorreo = (isset($responsCorreo[0]->ecodCorreo)&&$responsCorreo[0]->ecodCorreo!='' ? '"'.(trim($responsCorreo[0]->ecodCorreo)).'"':   "NULL");
        $responseuser = DB::select("SELECT * FROM bitcorreo WHERE ecodCorreo = ".$ecodCorreo); 
        $user=User::all()->where('tCorreo', $responseuser[0]->tCorreo)->first();
        $json = isset($jsonX->Rutas) ? $jsonX->Rutas : [];
        if (count($json) > 0) {
           foreach ($json as $key => $value1) {
            $ecodMenu = (isset($value1->ecodMenu)&&$value1->ecodMenu!="" ? "'".(trim($value1->ecodMenu))."'":   "NULL");
            $ecodSubmenu = (isset($value1->ecodSubmenu)&&$value1->ecodSubmenu!="" ? "'".(trim($value1->ecodSubmenu))."'":   "NULL");
            $ecodController = (isset($value1->ecodController)&&$value1->ecodController!="" ? "'".(trim($value1->ecodController))."'":   "NULL");      
            $uuieControlador = Uuid::uuid4();
            $uuidControlador2 = (isset($uuieControlador)&&$uuieControlador!="" ? "'".(trim($uuieControlador))."'":   "NULL");                    
            $token=JWTAuth::fromUser($user);   
            $tokenv = (isset($token) && $token != "" ? "'" . (trim($token)) . "'" : "");             
            $inserRelUsuarioMenuSubContro=" CALL `stpInsertarRelUsuarioMenuSubContro`(".$uuidControlador2.",".$ecodUsuario.",".$ecodMenu.",".$ecodSubmenu.",".$ecodController.",".$tokenv.")";
            $responseRelMenuSubContro = DB::select($inserRelUsuarioMenuSubContro); 
       
            
            }
        }
        
    }
}
