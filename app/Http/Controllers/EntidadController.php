<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Entidad;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PDOException;

class EntidadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $entidades = Entidad::all();
        return new JsonResponse($entidades->toArray(),Response::HTTP_OK,headers:["Access-Control-Allow-Origin",env('FRONT_BASE'),"Access-Control-Request-Method"=>'GET','OPTIONS']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request):JsonResponse
    {

    try{
        $validatedData = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:191',
                'nit' => 'required|string|max:191',
                'address' => 'nullable|string|max:191',
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
                'nit'=> 'NIT',
                'phone'=> 'Telefono',
                'address'=>'Dirección',
                'email'=>'Email'
            ]

            );
            if($validatedData->fails()){

                ///return response(headers:["Access-Control-Allow-Origin",env('FRONT_BASE'),"Access-Control-Request-Method"=>'POST','OPTIONS'])->json($validatedData->errors()->toArray(), Response::HTTP_BAD_REQUEST);

                return new JsonResponse($validatedData->errors()->toArray(),Response::HTTP_BAD_REQUEST,headers:["Access-Control-Allow-Origin",env('FRONT_BASE'),"Access-Control-Request-Method"=>'POST','OPTIONS']);
            }

             $entidad = Entidad::create($validatedData->getData());
            //return response(headers:["Access-Control-Allow-Origin",env('FRONT_BASE'),"Access-Control-Request-Method"=>'POST','OPTIONS'])->json($entidad, Response::HTTP_CREATED);
            return new JsonResponse($entidad,Response::HTTP_CREATED,headers:["Access-Control-Allow-Origin",env('FRONT_BASE'),"Access-Control-Request-Method"=>'POST','OPTIONS']);

    }
    catch(PDOException $e){
        return new JsonResponse(['error'=> 'Error al insertar el valor, probablemente este ya existe'],Response::HTTP_BAD_REQUEST,headers:["Access-Control-Allow-Origin",env('FRONT_BASE'),"Access-Control-Request-Method"=>'POST','OPTIONS']);

    }
    catch(\Exception $e){
        return new JsonResponse(['error'=> 'Error Interno del Servidor'],Response::HTTP_BAD_GATEWAY,headers:["Access-Control-Allow-Origin",env('FRONT_BASE'),"Access-Control-Request-Method"=>'POST','OPTIONS']);

    }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $entidad = Entidad::find($id);

        if (!$entidad) {
            return response()->json(['error' => 'Entidad no encontrada'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($entidad, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $entidad = Entidad::find($id);

        if (!$entidad) {
            return new JsonResponse(['error'=> 'Entidad no encontrada'],Response::HTTP_NOT_FOUND,headers:["Access-Control-Allow-Origin",env('FRONT_BASE'),"Access-Control-Request-Method"=>'PUT','OPTIONS']);

        }

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255',
        ]);

        $entidad->update($validatedData);
        return new JsonResponse($entidad,Response::HTTP_CREATED,headers:["Access-Control-Allow-Origin",env('FRONT_BASE'),"Access-Control-Request-Method"=>'PUT','OPTIONS']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
        $entidad = Entidad::find($id);

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

    public function deleteMuch(Request $request){
        try{

            $entities = Entidad::whereIn('id',$request->get('entities',[]))->get();

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
