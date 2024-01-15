<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Http;

class CheckApi implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $urlId;
    public $url;
    /**
     * Create a new job instance.
     */
    public function __construct($urlId, $url)
    {
        $this->urlId = $urlId;
        $this->url = $url;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $payload = [
            'client'    =>  [
                'clientId'          =>  config('app.name'),
                'clientVersion'     =>  '0.1'
            ],
            'threatInfo'    =>  [
                'threatTypes'       =>  config('app.google-threat-types'),
                'platformTypes'     =>  config('app.google-platform-types'),
                'threatEntryTypes'  =>  config('app.google-entry-types'),
                'threatEntries'     =>  [
                    ['url'  =>  $this->url]
                ]
            ]
        ];

        $request = Http::post(config('app.google-safebrowsing-url'), $payload);
        dd($request->json());
    }
}
