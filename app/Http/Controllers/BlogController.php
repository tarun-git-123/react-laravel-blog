<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    // this method will return all the blogs
    public function index(){
        $blogs = Blog::orderBy('created_at','DESC')->get();
        // foreach ($blogs as $blog) {
        //     echo public_path('uploads/blogs/'.$blog->image);
        // }
        // echo "<br>";
        return response()->json([
            'status'=>true,
            'data'=>$blogs
        ]);
    }

    // this method will return a single blog
    public function show(Request $request, $id){
        $blog = Blog::find($id);
        if($blog!==null){
            return response()->json([
                'status'=>true,
                'data'=>$blog
            ]);
        }else{
            return response()->json([
                'status'=>false,
                'message'=>'Data not found'
            ]);
        }
    }

    // this method will store or insert a blog
    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'title'=>'required|min:5',
            'author'=>'required|min:3'
        ]);

        if($validator->fails()){
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors(),
                'message'=>'Please fix the errors'
            ]);
        }else{
            $blog = new Blog();
            $blog->title = $request->title;
            $blog->shortDesc = $request->shortDesc;
            $blog->desc = $request->desc;
            $blog->author = $request->author;
            $blog->save();

            // save image here
            $tempImage = TempImage::find($request->imageId);
            if($tempImage!=''){
                $imageExtArray = explode('.',$tempImage->name);
                $ext = last($imageExtArray);
                $imageName = time().'-'.$blog->id.'.'.$ext;
                $blog->image = $imageName;
                $blog->save();

                $sourcePath = public_path('/uploads/temp/'.$tempImage->name);
                $destPath = public_path('/uploads/blogs/'.$imageName);

                File::copy($sourcePath,$destPath);
            }
            
            return response()->json([
                'status'=>true,
                'message'=>'Blog created successfully',
                'data'=>$blog
            ]);
        }
    }

    // this method will update a blog
    public function update(Request $request, $id){
        $validator = Validator::make($request->all(),[
            'title'=>'required|min:5',
            'author'=>'required|min:3'
        ]);

        if($validator->fails()){
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors(),
                'message'=>'Please fix the errors'
            ]);
        }else{
            $blog = Blog::find($id);
            if($blog==null){
                return response()->json([
                    'status'=>false,
                    'message'=>'Data not found',
                ]);
            }else{
                $blog->title = $request->title;
                $blog->shortDesc = $request->shortDesc;
                $blog->desc = $request->desc;
                $blog->author = $request->author;
                $blog->save();

                // save image here
                $tempImage = TempImage::find($request->imageId);
                if($tempImage!=''){
                    // delete old image
                    File::delete(public_path('/uploads/blogs/'.$blog->image));

                    $imageExtArray = explode('.',$tempImage->name);
                    $ext = last($imageExtArray);
                    $imageName = time().'-'.$blog->id.'.'.$ext;
                    $blog->image = $imageName;
                    $blog->save();

                    $sourcePath = public_path('/uploads/temp/'.$tempImage->name);
                    $destPath = public_path('/uploads/blogs/'.$imageName);

                    File::copy($sourcePath,$destPath);
                }

                return response()->json([
                    'status'=>true,
                    'message'=>'Blog Updated Sucessfully',
                    'data'=>$blog
                ]);
            }
        }
    }

    // this method will delete a blog
    public function destroy($id){
        $blog = Blog::find($id);

        if($blog==null){
            return response()->json([
                'status'=> false,
                'message'=>'Data not found'
            ]);
        }

        // delete blog image first
        $path = public_path('/uploads/blogs/'.$blog->image);
        File::delete($path);
        // delete bog from DB
        $blog->delete();
        return response()->json([
            'status'=> true,
            'message'=>'Blog Deleted Sucessfully'
        ]);
    }
}
