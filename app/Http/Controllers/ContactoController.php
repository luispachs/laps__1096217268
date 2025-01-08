<?php

namespace App\Http\Controllers;

use App\Models\Contacto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\{Validator,Log};
use PDOException;

class ContactoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $entidades = Contacto::all();
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
     * Display the specified resource.
     */
    public function show(Contacto $contacto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contacto $contacto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contacto $contacto)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contacto $contacto)
    {
        //
    }
}
