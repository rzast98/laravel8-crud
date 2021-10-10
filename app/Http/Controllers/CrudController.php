<?php

namespace App\Http\Controllers;

use App\Models\Crud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CrudController extends Controller
{
      /**
     * index
     *
     * @return void
     */
    public function index()
    {
        $cruds = Crud::latest()->paginate(10);
        return view('crud.index', compact('cruds'));
    }

    /**
    * create
    *
    * @return void
    */
    public function create()
    {
    return view('crud.create');
    }


    /**
    * store
    *
    * @param  mixed $request
    * @return void
    */
    public function store(Request $request)
    {
      $this->validate($request, [
        'image'     => 'required|image|mimes:png,jpg,jpeg',
        'title'     => 'required',
        'content'   => 'required'
        ]);

    //upload image
    $image = $request->file('image');
    $image->storeAs('public/blogs', $image->hashName());

    $crud = Crud::create([
        'image'     => $image->hashName(),
        'title'     => $request->title,
        'content'   => $request->content
    ]);

    if($crud){
        //redirect dengan pesan sukses
        return redirect()->route('crud.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }else{
        //redirect dengan pesan error
        return redirect()->route('crud.index')->with(['error' => 'Data Gagal Disimpan!']);
    }
    }

    /**
* edit
*
* @param  mixed $crud
* @return void
*/
public function edit(Crud $crud)
{
    return view('crud.edit', compact('crud'));
}


/**
* update
*
* @param  mixed $request
* @param  mixed $crud
* @return void
*/
public function update(Request $request, Crud $crud)
{
    $this->validate($request, [
        'title'     => 'required',
        'content'   => 'required'
    ]);

    //get data Blog by ID
    $crud = Crud::findOrFail($crud->id);

    if($request->file('image') == "") {

        $crud->update([
            'title'     => $request->title,
            'content'   => $request->content
        ]);

    } else {

        //hapus old image
        Storage::disk('local')->delete('public/blogs/'.$crud->image);

        //upload new image
        $image = $request->file('image');
        $image->storeAs('public/blogs', $image->hashName());

        $crud->update([
            'image'     => $image->hashName(),
            'title'     => $request->title,
            'content'   => $request->content
        ]);

    }

    if($crud){
        //redirect dengan pesan sukses
        return redirect()->route('crud.index')->with(['success' => 'Data Berhasil Diupdate!']);
    }else{
        //redirect dengan pesan error
        return redirect()->route('crud.index')->with(['error' => 'Data Gagal Diupdate!']);
    }
}

/**
* destroy
*
* @param  mixed $id
* @return void
*/
public function destroy($id)
{
  $crud = Crud::findOrFail($id);
  Storage::disk('local')->delete('public/blogs/'.$crud->image);
  $crud->delete();

  if($crud){
     //redirect dengan pesan sukses
     return redirect()->route('crud.index')->with(['success' => 'Data Berhasil Dihapus!']);
  }else{
    //redirect dengan pesan error
    return redirect()->route('crud.index')->with(['error' => 'Data Gagal Dihapus!']);
  }
}
}
