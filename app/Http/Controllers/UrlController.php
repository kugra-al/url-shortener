<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Url;
use App\Jobs\CheckApi;

class UrlController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'url'   =>  'required|max:2000|url'
        ]);

        $url = Url::where('url', $validatedData['url'])->first();
        if (!$url) {
            $validatedData['hash'] = Str::random(6);
            $url = Url::create($validatedData);
            CheckApi::dispatch($url);
            $status = 'Url created';
        } else
            $status = 'Url already exists';

        return response()->json([
            'url'       =>  $url->url,
            'short'     =>  'http://127.0.0.1:8000/url/'.$url->hash,
            'status'    =>  $status
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $url)
    {
        $url = Url::where('hash', $url)->first();
        if ($url) {
            if ($url->safe === null) {
                CheckApi::dispatch($url);
                dd("Url not checked");
            }
            if ($url->safe === 0) {
                dd("Url is unsafe");
            }
            return redirect()->to($url->url);
        }
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
    public function destroy(string $id)
    {
        //
    }
}
