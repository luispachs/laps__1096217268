<?php

namespace App\Http\Controllers;

use App\Models\Contacto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\{Validator,Log,DB};
use PDOException;

class ContactoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $entidades = DB::table('contactos')->select(['contactos.name','contactos.entity_id as entityId','contactos.phone','contactos.email','entidad.name as entityName'])->join('entidad','contactos.entity_id','=','entidad.id')->get();
        return new JsonResponse($entidades->toArray(),Response::HTTP_OK,headers:["Access-Control-Allow-Origin",env('FRONT_BASE'),"Access-Control-Request-Method"=>'GET','OPTIONS']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function store(Request $request)
    {

    try{
        $validatedData = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:191',
                'phone' => 'nullable|string|max:191',
                'email' => 'nullable|email|max:191',
            ],
            [
                'required'=> ':attribute es requrido',
                'string'=>':attribute debe ser una cadena de texto',
                'max'=> ':attibute debe tener un valor maximo de :max',
                'email'=> ':attribute no es una email valido'
            ],
            [
                'name'=>'Nombre',
                'phone'=> 'Telefono',
                'email'=>'Email'
            ]

            );
            if($validatedData->fails()){

                return new JsonResponse($validatedData->errors()->toArray(),Response::HTTP_BAD_REQUEST,headers:["Access-Control-Allow-Origin",env('FRONT_BASE'),"Access-Control-Request-Method"=>'POST','OPTIONS']);
            }

             $entidad = Contacto::create($validatedData->getData());
            return new JsonResponse($entidad,Response::HTTP_CREATED,headers:["Access-Control-Allow-Origin",env('FRONT_BASE'),"Access-Control-Request-Method"=>'POST','OPTIONS']);

    }
    catch(PDOException $e){
        Log::error($e->getMessage(),['trace'=> $e->getTraceAsString()]);
        return new JsonResponse(['error'=> 'Error al insertar el valor, probablemente este ya existe'],Response::HTTP_BAD_REQUEST,headers:["Access-Control-Allow-Origin",env('FRONT_BASE'),"Access-Control-Request-Method"=>'POST','OPTIONS']);

    }
    catch(\Exception $e){
        Log::error($e->getMessage(),['trace'=> $e->getTraceAsString()]);
        return new JsonResponse(['error'=> 'Error Interno del Servidor'],Response::HTTP_BAD_GATEWAY,headers:["Access-Control-Allow-Origin",env('FRONT_BASE'),"Access-Control-Request-Method"=>'POST','OPTIONS']);

    }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contacto $contacto)
    {
        $entidad = Entidad::find($id);

        if (!$entidad) {
            return new JsonResponse(['error'=> 'Entidad no encontrada'],Response::HTTP_NOT_FOUND,headers:["Access-Control-Allow-Origin",env('FRONT_BASE'),"Access-Control-Request-Method"=>'PUT','OPTIONS']);

        }

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'entity_id' => 'nullable|numeric|exist:entidad,id',
            'phone' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255',
        ]);

        $entidad->update($validatedData);
        return new JsonResponse($entidad,Response::HTTP_CREATED,headers:["Access-Control-Allow-Origin",env('FRONT_BASE'),"Access-Control-Request-Method"=>'PUT','OPTIONS']);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, int $id)
    {
        try{
            $entidad = Contacto::find($id);

            if (!$entidad) {
                return new JsonResponse(['error' => 'Entidad no encontrada'],Response::HTTP_NOT_FOUND,headers:["Access-Control-Allow-Origin",env('FRONT_BASE'),"Access-Control-Request-Method"=>'PUT','OPTIONS']);
            }

            $entidad->delete();
            return new JsonResponse($entidad,Response::HTTP_CREATED,headers:["Access-Control-Allow-Origin",env('FRONT_BASE'),"Access-Control-Request-Method"=>'PUT','OPTIONS']);
            }
            catch(PDOException $e){
                $message =$e->getMessage();
                if($e->getCode() == 23000){
                    $message = "La Entidad no puede ser Eliminada";
                }
                return new JsonResponse(['error' => $message],Response::HTTP_BAD_REQUEST,headers:["Access-Control-Allow-Origin",env('FRONT_BASE'),"Access-Control-Request-Method"=>'PUT','OPTIONS']);
            }
            catch(\Exception $e){
                return new JsonResponse(['error' => "Error Interno del Servidor"],Response::HTTP_BAD_GATEWAY,headers:["Access-Control-Allow-Origin",env('FRONT_BASE'),"Access-Control-Request-Method"=>'PUT','OPTIONS']);
            }
    }
    function destroyMany(Request $request){
        try{

            $entities = Contacto::whereIn('id',$request->get('entities',[]))->get();


            if (count($entities)<1) {
                return new JsonResponse(['error' => 'Entidad no encontrada'],Response::HTTP_NOT_FOUND,headers:["Access-Control-Allow-Origin",env('FRONT_BASE'),"Access-Control-Request-Method"=>'PUT','OPTIONS']);
            }

            foreach($entities as $elem){
                try{
                    $elem->delete();
                }catch(PDOException $e){
                    continue;
                }
            }

            return new JsonResponse($entities->toArray(),Response::HTTP_CREATED,headers:["Access-Control-Allow-Origin",env('FRONT_BASE'),"Access-Control-Request-Method"=>'PUT','OPTIONS']);
            }

            catch(\Exception $e){
                return new JsonResponse(['error' => "Error Interno del Servidor"],Response::HTTP_BAD_GATEWAY,headers:["Access-Control-Allow-Origin",env('FRONT_BASE'),"Access-Control-Request-Method"=>'PUT','OPTIONS']);
            }

    }
}
