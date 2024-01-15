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

    public $url;
    /**
     * Create a new job instance.
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!$this->url)
            return;

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
                    ['url'  =>  $this->url->url]
                ]
            ]
        ];

        $request = Http::post(config('app.google-safebrowsing-url'), $payload);
        $response = $request->json();
        if (isset($response["matches"]))
            $this->url->safe = false;
        else
            $this->url->safe = true;
        $this->url->save();
    }
}
