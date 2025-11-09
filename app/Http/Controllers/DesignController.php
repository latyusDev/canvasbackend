<?php

namespace App\Http\Controllers;

use App\Http\Requests\DesignRequest;
use App\Models\Design;
use Illuminate\Http\Request;

class DesignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $designs = Design::whereUserId(auth()->user()->id)->get();
        // $designs = Design::all();
        return response([
            'designs'=>$designs
        ],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DesignRequest $request)
    {
        $designData = $request->validated();
        $designData['user_id'] = auth()->user()->id;
        $design = Design::updateOrCreate([
        'id' => $request->id,
        'user_id'=>auth()->user()->id],
        $designData);
        return response([
            'design'=>$design
        ],201);        
    }

    /**
     * Display the specified resource.
     */
    public function show(Design $design)
    {
         return response([
            'design'=>$design
        ],200);   

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Design $design)
    {
        if(!$design){
            return response(['message'=>'design not found']);
        }
          $design->delete();
          return response([
            'message'=>'design deleted successfully'
        ],204); 
    }
}
