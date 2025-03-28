<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
/*use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;*/
use DB;
use App\Http\Controllers\seg\encriptar;

class sesionMiddleware
{
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    
    public function handle(Request $request, Closure $next)
    {

        $encriptar = new encriptar();
        $datos =json_decode($encriptar->shiftText($request['headers'], -23));
        $selectEcodCorreo = "SELECT * FROM bitcorreo bc WHERE bc.ecodCorreo = ".$datos->ecodCorreo."
        AND bc.tToken = ".$datos->token;
        $sqlEcodCorreo = DB::select(($selectEcodCorreo)); 
        if($sqlEcodCorreo){
            $ecodCorreo = (isset($sqlEcodCorreo[0]->ecodCorreo) && $sqlEcodCorreo[0]->ecodCorreo != "" ? "'" . (trim($sqlEcodCorreo[0]->ecodCorreo)) . "'" : "");             
            $selectEcodUsuario="SELECT * FROM relusuariocorreo ruc WHERE ruc.ecodCorreo =".$ecodCorreo;
            $sqlEcodUsuario = DB::select(($selectEcodUsuario));          
            $ecodUsuario = (isset($sqlEcodUsuario[0]->ecodUsuario) && $sqlEcodUsuario[0]->ecodUsuario != "" ? "'" . (trim($sqlEcodUsuario[0]->ecodUsuario)) . "'" : "");             
            $selectact="SELECT ce.tNombre as Estatus FROM catusuarios cu 
            LEFT JOIN catestatus ce ON ce.ecodEstatus = cu.ecodEstatus
            WHERE cu.ecodUsuario=".$ecodUsuario;
            $sqlact = DB::select(($selectact));          
            $Estatus   = (isset($sqlact[0]->Estatus) && $sqlact[0]->Estatus != "" ? "'" . (trim($sqlact[0]->Estatus)) . "'" : "");             
            if ($Estatus == "'Activo'") {
                return $next($request);
            }
            else {
                $data = [
                    'mensaje'=>"Usuario invalido, Inicie sesion nuevamente",
                ];
                $jsonData = json_encode($data);
                $returResponse =$encriptar->shiftText($jsonData, 23);
                return response()->json($returResponse,401); 
            }
        }
        else{
            $data = [
                'mensaje'=>"Token invalido, Inicie sesion nuevamente",
            ];
            $jsonData = json_encode($data);
            $returResponse =$encriptar->shiftText($jsonData, 23);
            return response()->json($returResponse,401); 
        }
    }
}
