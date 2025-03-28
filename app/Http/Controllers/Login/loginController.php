<?php

namespace App\Http\Controllers\Login;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\LoginReuest;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\seg\encriptar;

class loginController extends Controller
{
    public function objeto_a_array($data){
        if (is_array($data) || is_object($data)){
            $result = array();
            foreach ($data as $key => $value){$result[$key] = $this->objeto_a_array($value);}
            return $result;
        }
        return $data;
    }

    function posLogin(LoginReuest $request) {
        $encriptar = new encriptar();
        $dadsad = (isset($request['haders']) && $request['haders'] != "" ? "" . (trim($request['haders'])) . "" : "" );           
         if ($dadsad=="Ox_mSak@t~r}uh_GoerfQly_=EM$4iIYk#v4oFguL)TY2b0~O[") {
            $datos =json_decode($encriptar->shiftText($request['datos'], -23)) ;
            if (is_array($datos) || is_object($datos)){
                $result = array();
                foreach ($datos as $key => $value){
                    $result[$key] = $this->objeto_a_array($value);
                }
                $result; 
            }
            try{
                $exito = 1;
                $Email = (isset($result['email']) && $result['email'] != "" ? "" . (trim($result['email'])) . "" : "");           
                if ((preg_match('/^[a-zA-Z0-9.,"]+$/u', $result['password']) == 1)  && (preg_match('/^[a-zA-Z0-9.,"@]+$/u', $result['email']) == 1)) {
                    $password = (isset($result['password']) && $result['password'] != "" ? "" . (trim($result['password'])) . "" : "");
                }
                else { 
                    $data = [
                        'mensaje'=>"No dijite caracteres especiales ni espacios",
                    ];
                    $jsonData = json_encode($data);
                    $returResponse =$encriptar->shiftText($jsonData, 23);
                    return response()->json($returResponse,401); 
                }
                $selectEcodCorreo = "SELECT * FROM bitcorreo WHERE tCorreo = ? AND tpassword = ?";
                $sqlEcodCorreo = DB::select($selectEcodCorreo, [$Email, $password]);
                
                if($sqlEcodCorreo){
                    $ecodCorreo = (isset($sqlEcodCorreo[0]->ecodCorreo) && $sqlEcodCorreo[0]->ecodCorreo != "" ? "" . (trim($sqlEcodCorreo[0]->ecodCorreo)) . "" : "");                                
                    $sqlEcodUsuario = DB::table('relusuariocorreo')->where('ecodCorreo', $ecodCorreo)->get();
                    $ecodUsuario = (isset($sqlEcodUsuario[0]->ecodUsuario) && $sqlEcodUsuario[0]->ecodUsuario != "" ? "" . (trim($sqlEcodUsuario[0]->ecodUsuario)) . "" : "");             
                    $selectact="SELECT ce.tNombre as Estatus, ctu.tNombre AS TipoUsuario FROM catusuarios cu 
                    LEFT JOIN catestatus ce ON ce.ecodEstatus = cu.ecodEstatus
                    LEFT JOIN cattipousuario ctu ON ctu.ecodTipoUsuario = cu.ecodTipoUsuario
                    WHERE cu.ecodUsuario = ?";
                    $sqlact = DB::select($selectact, [$ecodUsuario]); 
                    $Estatus = (isset($sqlact[0]->Estatus) && $sqlact[0]->Estatus != "" ? "'" . (trim($sqlact[0]->Estatus)) . "'" : "");             
                    if ($Estatus == "'Activo'") {
                        $user=User::all()->where('tCorreo', $result['email'] )->first();
                        $token=JWTAuth::fromUser($user);
                        $tokenv   = (isset($token) && $token != "" ? "" . (trim($token)) . "" : ""); 
                        $ip_address='127.0.0.1';
                        $ip = (isset($ip_address) && $ip_address != "" ? "" . (trim($ip_address)) . "" : "");
                        $insert=" CALL `stpInsertarLogin`(?, ?, ?)";
                        $response = DB::select($insert,[$ecodCorreo,$tokenv,$ip]);
                        $selectMenu="SELECT rumspc.ecodRelusRarioMenuSubmenuController, cm.tNombre AS Menu,rumspc.tToken,cs.tNombre AS submenuNombre, ci.tIcono AS Iconos,
                        cs.tUrl as urlSubMenu, cct.tNombre AS nombreController, cct.turl AS urlController, cp.tNombre AS Permisos, cp.tNombreCorto As PermisosCorto
                        FROM relusuariomenusubmenucontroller rumspc 
                            LEFT JOIN catmenu cm ON cm.ecodMenu= rumspc.ecodMenu 
                            LEFT JOIN catsubmenu cs ON cs.ecodSubmenu = rumspc.ecodSubmenu
                            LEFT JOIN catcontroller cct on cct.ecodControler = rumspc.ecodController
                            LEFT JOIN catpermisos cp ON cp.ecodPermisos = rumspc.ecodPermisos
                            LEFT JOIN caticono ci ON ci.ecodIcono =cm.ecodIconos
                            LEFT JOIN catestatus cesm ON cesm.ecodEstatus= cs.ecodEstatus
                            LEFT JOIN catestatus cect ON cect.ecodEstatus= cct.ecodEstatus
                            WHERE rumspc.ecodUsuario = ? AND cesm.tNombre = 'Activo'".
                            " ORDER BY cm.tNombre, cs.tNombre ASC";
                        $sqlMenu = DB::select($selectMenu,[$ecodUsuario]); 
                        foreach ($sqlMenu as $key => $v){
                            $arrsqlmenu[]=array(
                                'Menu' => $v->Menu,
                                'submenu'=>$v->submenuNombre,
                                'urlSubMenu'=>$v->urlSubMenu,
                                'Permisos'=>$v->Permisos,
                                'PermisosCorto'=>$v->PermisosCorto,
                                'Controller' => $v->nombreController,
                                'urlController'=>$v->urlController,
                                'Iconos'=>$v->Iconos,
                                'Token'=>$v->tToken,
                                'ecod'=>$v->ecodRelusRarioMenuSubmenuController
                            );
                        }         
                        $exito = 0;
                    }
                    else {
                        $data = [
                            'mensaje'=>"Esta cuenta no se encuentra activa",
                        ];
                        $jsonData = json_encode($data);
                        $returResponse =$encriptar->shiftText($jsonData, 23);
                        return response()->json($returResponse,202);
                    }
                }
                else {
                    $data = [
                        'mensaje'=>"Usuario o contraseña inválida",
                    ];
                    $jsonData = json_encode($data);
                    $returResponse =$encriptar->shiftText($jsonData, 23);
                    return response()->json($returResponse,401); 
                }
                if ($exito == 0) {
                    DB::rollback();
                } 
                else {
                    DB::commit();
                }
            }
            catch (Exception $e) {
                DB::rollback();
                $exito = $e->getMessage();
            }
            $data = [
                'token' => $token,
                'Menu' => isset($arrsqlmenu) ? $arrsqlmenu : "",
                'ecodCorreo' => isset($sqlEcodCorreo[0]->ecodCorreo) ? $sqlEcodCorreo[0]->ecodCorreo : "",
                'TipoUsuario' => isset($sqlact[0]->TipoUsuario) ? $sqlact[0]->TipoUsuario : ""
            ];
            $jsonData = json_encode($data);
            $returResponse =$encriptar->shiftText($jsonData, 23);
          
            return response()->json( $returResponse);
        }
        $data = [
            'mensaje'=>"No cuenta con los permisos",
        ];
        $jsonData = json_encode($data);
        $returResponse =$encriptar->shiftText($jsonData, 23);
        return response()->json($returResponse,202);
    }  
    function postValidadContrasena(Request $request){
        $encriptar = new encriptar();

        $jsonX =json_decode($encriptar->shiftText($request['datos'], -23));

        $contrasena    = (isset($jsonX->contrasena) && $jsonX->contrasena != "" ? "'" . (trim($jsonX->contrasena)) . "'" : "");           
        $ecodCorreo    = (isset($jsonX->ecodCorreo) && $jsonX->ecodCorreo != "" ? "'" . (trim($jsonX->ecodCorreo)) . "'" : "");            
        if (preg_match('/^[a-zA-Z0-9.,"]+$/u', $jsonX->contrasena) == 1) {
          
                $selectcontra="SELECT count(*) AS dl FROM bitcorreo bc WHERE bc.ecodCorreo = ".$jsonX->ecodCorreo."  AND bc.tpassword =".$contrasena;
                $sqlcontra = DB::select($selectcontra);
                $jsonData = json_encode($sqlcontra[0]);
                $returResponse =$encriptar->shiftText($jsonData, 23);
              
                return response()->json( $returResponse);          
        }
        else { 
            $data = [
                'mensaje'=>"No dijite caracteres especiales ni espacios",
            ];
            $jsonData = json_encode($data);
            $returResponse =$encriptar->shiftText($jsonData, 23);
            return response()->json($returResponse,401); 
             
        }

    }
}
