<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Controller;
use App\Models\Proj;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class ProjController extends Controller
{

    
private function validation($data) {
  //$unique_title_rule = ($id) ? "|unique:projs,title,$id" : "|unique:projs";
  //$unique_description_rule = ($id) ? "|unique:projs,description,$id" : "|unique:projs";
  //$unique_image_rule = ($id) ? "|unique:projs,image,$id" : "|unique:projs";

  $validator = Validator::make(
    $data,
    [
      'title' => 'required|string|max:50', //$unique_title_rule,
      'description' => 'required|string|max:200',//$unique_description_rule,
      'image' => 'nullable|image|mimes:jpg,png,jpeg',//$unique_image_rule,
      'type_id' => 'nullable|exists:types,id'
    ],
    [
      'title.required' => 'Il title è obbligatorio',
      'title.string' => 'Il title deve essere una stringa',
      'title.max' => 'Il title deve essere massimo di 50 caratteri',

      'description.required' => 'La description è obbligatoria',
      'description.string' => 'La description deve essere una stringa',
      'description.max' => 'La description deve essere massimo di 200 caratteri',

      'image.image' => 'Il file deve essere un immagine',
      'image.mimes' =>'il file deve essere di tipo jpg,png o jpeg',

      'type_id.exists' => 'L\ id della categoria non è valido',



      
    ]
  )->validate();

  return $validator;
}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $projs=Proj::all();
        return view('admin.projs.index',compact('projs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   
        $proj=new Proj;
        $types=Type::all();
        return view('admin.projs.create',compact('proj','types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {    

        $data=$request->all();
         
        if(Arr::exists($data,'image')){
          $path=Storage::put('uploads/projs',$data['image']);
          $path=Storage::disk('public')->put('uploads',$data['image']);
          $data['image']=$path;}

       
        

        $data = $this->validation($request->all());
        $proj=new Proj;
        $proj->fill($data);
        $proj->save();

        return to_route('admin.projs.show', $proj);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Proj  $proj
     * @return \Illuminate\Http\Response
     */
    public function show(Proj $proj)
    {
        return view('admin.projs.show',compact('proj'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Proj  $proj
     * @return \Illuminate\Http\Response
     */
    public function edit(Proj $proj)
    {
        $types=Type::all();
        return view('admin.projs.edit', compact('proj','types'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Proj  $proj
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Proj $proj)
    {
        $data = $this->validation($request->all(), $proj->id);
        $data = $request->all();
        $proj->update($data);
        return redirect()->route('admin.projs.show', $proj);
       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Proj  $proj
     * @return \Illuminate\Http\Response
     */
    public function destroy(Proj $proj)
    {
         $proj->delete();
         return redirect()->route('admin.projs.index');
    }
}
