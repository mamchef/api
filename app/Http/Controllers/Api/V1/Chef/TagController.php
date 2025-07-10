<?php

namespace App\Http\Controllers\Api\V1\Chef;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\Chef\Tag\TagResource;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{

    public function index(Request $request)
    {
        $tags = Tag::query();
        if ($request->has('per_page')) {
            $tags = $tags->paginate($request->input('per_page'));
        } else {
            $tags = $tags->get();
        }

        return TagResource::collection(
            $tags
        );
    }
}