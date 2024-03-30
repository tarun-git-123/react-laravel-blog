<?php

namespace App\Http\Controllers;

use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TempImageController extends Controller
{
    public function store(Request $request){
        // apply validation
        $validator = Validator::make($request->all(),[
            'image'=>'required|image'
        ]);

        if($validator->fails()){
            return response()->json([
                'status'=>false,
                'message'=>'Please fix the error',
                'errors'=>$validator->errors()
            ]);
        }

        // upload image here
        $image = $request->image;
        $ext = $image->getClientOriginalExtension();
        $image_name = time().'.'.$ext;

        // store image info into the database
        $tempImage = new TempImage();
        $tempImage->name = $image_name;
        $tempImage->save();

        // move image in temp directory
        $image->move(public_path('/uploads/temp'),$image_name);

        return response()->json([
            'status'=>true,
            'message'=>'Image Uploaded successfully',
            'image'=>$tempImage
        ]);

    }
}
