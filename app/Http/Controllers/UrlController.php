<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Url;
use App\Jobs\CheckApi;
use App\Events\UrlChecked;

use Inertia\Inertia;

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
            $unique = false;
            while ($unique == false) {
                $validatedData['hash'] = Str::random(6);
                if (!Url::where('hash', $validatedData['hash'])->exists())
                    $unique = true;
            }
            $url = Url::create($validatedData);
            event(new UrlChecked($url->url));
            CheckApi::dispatch($url);
            $status = 'Checking if URL is safe...';
        } else {
            if ($url->safe === 1)
                $status = 'Short URL already exists: ';
            else
                $status = 'URL already checked. ';
        }
        return response()->json([
            'url'       =>  $url->url,
            'short'     =>  route("url.show", $url->hash),
            'hash'      =>  $url->hash,
            'safe'      =>  $url->safe,
            'status'    =>  $status
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $url)
    {
        $url = Url::where('hash', $url)->first();
        if ($url)
            return Inertia::render('Forward', [
                'safe'      => ($url->safe ? true : false),
                'short'     => route("url.show", $url->hash),
                'url'       => $url->url
            ]);
        return Inertia::render('Forward', []);
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

    public function check()
    {
        $url = Url::where('hash', request()->get('hash'))->first();
        if ($url) {
            return response()->json([
                'url'       =>  $url->url,
                'short'     =>  route("url.show", $url->hash),
                'hash'      =>  $url->hash,
                'safe'      =>  $url->safe,
                'status'    =>  ($url->safe == 1 ? 'Short URL created. ' : '')
            ], 200);
        }
    }
}
