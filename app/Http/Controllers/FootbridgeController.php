<?php

namespace Anuncia\Http\Controllers;

use Anuncia\Footbridge;
use Anuncia\Image;
use Anuncia\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FootbridgeController extends Controller
{

    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // $footbridges = Footbridge::paginate(10);
        // $footbridges->load('municipality');

        $footbridges = Footbridge::with('municipality')->paginate(10);

        return view('footbridge.home')->with(['footbridges' => $footbridges]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $states = State::all();
        return view('footbridge.create')->with([
            'states' => $states,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'         =>  'required',
            'availability' =>  'required',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('footbridge_create_path')
                ->withErrors($validator)
                ->withInput();
        }


        $footbridge = new Footbridge();
        $footbridge->name            = $request->get('name');
        $footbridge->availability    = $request->get('availability');
        $footbridge->description     = $request->get('description');
        $footbridge->position        = $request->get('position');
        $footbridge->views           = $request->get('views');
        $footbridge->frontal         = $request->get('frontal');
        $footbridge->crusade         = $request->get('crusade');
        $footbridge->mega            = $request->get('mega');
        $footbridge->side            = $request->get('side');
        $footbridge->street          = $request->get('street');
        $footbridge->reference_c     = $request->get('reference_c');
        $footbridge->reference_n     = $request->get('reference_n');
        $footbridge->reference_s     = $request->get('reference_s');
        $footbridge->reference_o     = $request->get('reference_o');
        $footbridge->reference_p     = $request->get('reference_p');
        $footbridge->municipality_id = $request->get('municipality_id');
        $footbridge->order           = $request->get('order');
        $footbridge->latitude        = $request->get('latitude');
        $footbridge->length          = $request->get('length');
        $footbridge->save();



        if($request->hasFile('url')){

            $files     = $request->file('url');
            $order=1;
            foreach ($files as $file) {
                if($file != null){
                    $name = $file->getClientOriginalName();
                    $count_number_imgs = 1;
                    while(file_exists(public_path('images/footbridges/'.$name))){
                        $name= $count_number_imgs.$name;
                        $count_number_imgs++;
                    }
                    Storage::disk('footbridges')->put($name,File::get($file));
                    $image = new Image();
                    $image->name          = $name;
                    $image->order         = $order;
                    $image->url           = url('images/footbridges/'.$name);
                    $image->footbridge_id = $footbridge->id;
                    $image->save();
                    $order++;
                }
            }
        }

        return redirect()->route('footbridge_home_path');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $footbridge = Footbridge::findOrFail($id);
        $images = DB::table('images')->where('footbridge_id','=',$footbridge->id)
            ->orderBy('order', 'asc')
            ->get();
        $footbridges_close = DB::table('footbridges')
            ->join('images','footbridges.id','=','footbridge_id')
            ->select('footbridges.id','footbridges.name', 'images.url')
            ->where(function ($query) use ($footbridge) {
                $query->where('footbridges.municipality_id', $footbridge->municipality_id)
                    ->where('footbridges.id', '!=', $footbridge->id);
            })
            ->groupBy('footbridges.name')
            ->orderBy('images.order','asc')
            ->take(6)
            ->get();
        return view('footbridge.show',[
            'footbridge' => $footbridge,
            'footbridges_close' => $footbridges_close,
            'images'     => $images,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $footbridge = Footbridge::findOrFail($id);
        $states = State::all();
        $municipalities = DB::table('municipalities')
            ->where('state_id','=',$footbridge->municipality->state->id)
            ->get();
        $images = DB::table('images')->where('footbridge_id','=',$footbridge->id)
            ->orderBy('order', 'asc')
            ->get();
        return view('footbridge.edit', [
            'footbridge' => $footbridge,
            'states'     => $states,
            'municipalities' => $municipalities,
            'images' => $images
        ]);



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

        $footbridge = Footbridge::findOrFail($id);
        $footbridge->name            = $request->get('name');
        $footbridge->availability    = $request->get('availability');
        $footbridge->description     = $request->get('description');
        $footbridge->position        = $request->get('position');
        $footbridge->views           = $request->get('views');
        $footbridge->frontal         = $request->get('frontal');
        $footbridge->crusade         = $request->get('crusade');
        $footbridge->mega            = $request->get('mega');
        $footbridge->side            = $request->get('side');
        $footbridge->street          = $request->get('street');
        $footbridge->reference_c     = $request->get('reference_c');
        $footbridge->reference_n     = $request->get('reference_n');
        $footbridge->reference_s     = $request->get('reference_s');
        $footbridge->reference_o     = $request->get('reference_o');
        $footbridge->reference_p     = $request->get('reference_p');
        $footbridge->municipality_id = $request->get('municipality_id');
        $footbridge->order           = $request->get('order');
        $footbridge->latitude        = $request->get('latitude');
        $footbridge->length          = $request->get('length');


        $band=1; //Esta es una variable para validar que haya imagenes en el dom

        if($request->hasFile('url') || $band==1){

            $files     = $request->file('url');
            $id        = $request->get('id');
            //var_dump($files);
            //var_dump($id);
            $tam_files = count($files);
            $order=1;


            //Buscamos cuales estan en la base de datos y pedimos una coleccion
            $images_bd = DB::table('images')->where('footbridge_id','=',$footbridge->id);
            $collection_img = collect($images_bd->get());
            //var_dump($collection_img);
            $keys_order_by_id =  $collection_img->keyBy('id');
            $keys = $keys_order_by_id->keys();
            //Ya tenemos todos los items de la base de datos guardados en $key


            $diff = $keys->diff($id);
            //var_dump($diff->all());

            /*Si la diferencia tiene elementos entonces quiere decir que ya no estan en el
             * Dom y se pueden eliminar
             */
            if(!$diff->isEmpty()){

                foreach($diff as $item){
                    //var_dump("Entra a eliminar".$item);
                    $name_delete = DB::table('images')->where('id','=',$item)->value('name');
                    Storage::disk('footbridges')->delete($name_delete);
                    Image::destroy($item);
                    //var_dump($name_delete);
                }


            }

            //dd("Stop");


            for($i=0;$i<$tam_files;$i++){
                //var_dump("problema puede aqui: ".empty($id[$i]));

                if( $files[$i] != null && $id[$i] != 'new') {
                    $valor_id = $id[$i];
                    $valor_file = $files[$i];
                    //var_dump($valor_id);
                    //var_dump($valor_file);
                    //Proceso de Actualización
                    $image = Image::findOrFail($valor_id);
                    var_dump($image);
                    if ($image) {
                        $anterior = $image->name;
                        //Guardo en el storage
                        $name = $valor_file->getClientOriginalName();
                        //var_dump($name);
                        $count_number_imgs = 1;
                        while (file_exists(public_path('images/footbridges/' . $name))) {
                            $name = $count_number_imgs . $name;
                            $count_number_imgs++;
                        }
                        Storage::disk('footbridges')->put($name, File::get($valor_file));
                        //Termina el proceso de guardado
                        //Actualización del registro en la bd
                        $image->name = $name;
                        $image->order = $order;
                        $image->url = url('images/footbridges/' . $name);
                        $image->save();
                        //var_dump("Entro a actualizar");
                        //Termina proceso de actualizacion
                        //Empieza eliminación en el Storage Path
                        Storage::disk('footbridges')->delete($anterior);
                        //Termina la eliminación en el Storage Path
                        //var_dump('Termino la actualización junto con la eliminacion');
                        $order++;
                    }
                }


                if($files[$i]  != null && $id[$i] == 'new'){
                    //var_dump("Entro a dar de alta");
                    // Se da de alta si no se encuentra en la base de datos
                    $valor_file = $files[$i];
                    $name = $valor_file->getClientOriginalName();
                    $count_number_imgs = 1;
                    while(file_exists(public_path('images/footbridges/'.$name))){
                        $name= $count_number_imgs.$name;
                        $count_number_imgs++;
                    }
                    Storage::disk('footbridges')->put($name,File::get($valor_file));
                    $image = new Image();
                    $image->name          = $name;
                    $image->order         = $order;
                    $image->url           = url('images/footbridges/'.$name);
                    $image->footbridge_id = $footbridge->id;
                    $image->save();
                    //dd("Guardo la imagen");
                    $order++;
                }


                if($files[$i] == null && $id[$i] != null && $id[$i]!='new' ){
                    //var_dump("Entra aqui el id esta lleno");
                    $valor_id = $id[$i];
                    $image = Image::findOrFail($valor_id);
                    //var_dump($image);
                    if ($image) {
                        $image->order = $order;
                        $image->save();
                    }
                    $order++;

                }


                if($files[$i] == null && empty($id[$i])){
                    var_dump("No debe hacer nada");
                }
            }

            //dd("Termino proceso");
        }


        $footbridge->save();



        return redirect($request->get('url_home_catalog'));

    }

    
    
    public function question_destroy($id)
    {
        $footbridge = Footbridge::findOrFail($id);
        return view('footbridge.delete',['footbridge' => $footbridge]);
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $footbridge = Footbridge::findOrFail($id);
        $footbridge_images = Footbridge::findOrFail($id)->images;
        foreach($footbridge_images as $footbridge_image){
            $name = $footbridge_image->name;
            Storage::disk('footbridges')->delete($name);
            $footbridge_image->delete();
        }
        $footbridge->delete();

        return redirect()->route('footbridge_home_path');

    }
    

    public function select(Request $request){

        $id = $request->get('id');
        $state = State::find($id);
        $municipalities = $state->municipalities;
        return view('footbridge.select')->with([
            'municipalities' => $municipalities,
        ]);
    }
}
