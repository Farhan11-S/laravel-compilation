<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AvatarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;


class AvatarController extends Controller
{
    public function store(Request $request)
    {
        $avatarService = new AvatarService();

        $msg = null;
        $error = $success = 0;
        $code = 200;

        // if there is a [file]
        if ($request->hasFile('avatar')) {
            // allowed extensions
            $allowed_images = $avatarService->getAllowedImages();

            $file = $request->file('avatar');
            // check file size
            if ($file->getSize() < $avatarService->getMaxUploadSize()) {
                if (in_array(strtolower($file->extension()), $allowed_images)) {
                    // delete the older one
                    if (Auth::user()->avatar != config('chatify.user_avatar.default')) {
                        $avatar = Auth::user()->avatar;
                        if ($avatarService->storage()->exists($avatar)) {
                            $avatarService->storage()->delete($avatar);
                        }
                    }
                    // upload
                    $avatar = Str::uuid() . "." . $file->extension();
                    $update = User::where('id', Auth::user()->id)->update(['avatar' => $avatar]);
                    $file->storeAs(config('chatify.user_avatar.folder'), $avatar, config('chatify.storage_disk_name'));
                    $success = $update ? 1 : 0;
                } else {
                    $msg = "File extension not allowed!";
                    $error = 1;
                    $code = 422;
                }
            } else {
                $msg = "File size you are trying to upload is too large!";
                $error = 1;
                $code = 422;
            }
        } else {
            $msg = "Image is empty";
            $error = 1;
            $code = 422;
        }

        if ($error) abort($code, $msg);

        // send the response
        return Response::json([
            'status' => $success ? 1 : 0,
            'error' => $error ? 1 : 0,
            'message' => $error ? $msg : 0,
        ], $code);
    }
}
