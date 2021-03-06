<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Constraint;
use Intervention\Image\Facades\Image;
use TCG\Voyager\Facades\Voyager;

class VoyagerController extends Controller
{
    public function index()
    {
        return view('voyager::index');
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('voyager.login');
    }

    public function upload(Request $request)
    {
        $fullFilename = null;
        $resizeWidth = 1800;
        $resizeHeight = null;
        $slug = $request->input('type_slug');
        $file = $request->file('image');
        $filename = Str::random(20);
        $fullPath = $slug.'/'.date('F').date('Y').'/'.$filename.'.'.$file->getClientOriginalExtension();

        $ext = $file->guessClientExtension();

        if (in_array($ext, ['jpeg', 'jpg', 'png', 'gif'])) {
            $image = Image::make($file)
                ->resize($resizeWidth, $resizeHeight, function (Constraint $constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode($file->getClientOriginalExtension(), 75);

            // move uploaded file from temp to uploads directory
            if (Storage::disk(config('voyager.storage.disk'))->put($fullPath, (string) $image, 'public')) {
                $status = 'Image successfully uploaded!';
                $fullFilename = $fullPath;
            } else {
                $status = 'Upload Fail: Unknown error occurred!';
            }
        } else {
            $status = 'Upload Fail: Unsupported file format or It is too large to upload!';
        }

        // echo out script that TinyMCE can handle and update the image in the editor
        return "<script> parent.setImageValue('".Voyager::image($fullFilename)."'); </script>";
    }

    public function profile()
    {
        return view('voyager::profile');
    }

    public function uploadForKindEditor(Request $request)
    {
        $file = $request->file('imgFile');
        $ext = $file->guessClientExtension();
        $filename = Str::random(20);
        $fullPath = 'pages/'.$filename.'.'.$file->getClientOriginalExtension();

        if (!($path = $file->store("upload", config('voyager.storage.disk')))) {
        /* if (!Storage::disk(config('voyager.storage.disk'))->put($fullPath, $file->path(), 'public')) { */
            return response()->json(['error' => 1, 'message' => 'upload failed']);
        } 
        return response()->json(['error' => 0, 'url' => Storage::disk(config('voyager.storage.disk'))->url("$path")]);
    }

    public function redirect(Request $request)
    {
        return view('voyager::redirect', ['link' => $request->link]);
    }
}
