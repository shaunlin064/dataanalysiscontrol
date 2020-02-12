<?php

namespace App\Jobs;

use App\Mail\CronTab;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SentMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $action;
    private $arg;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $action,array $arg)
    {
        //
        $this->action = $action;
        $this->arg = $arg;
       
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        switch ($this->action){
            case 'crontab':
                extract($this->arg);
                Mail::to('shaun@js-adways.com.tw')
                    ->send(new CronTab($name, $title));
                break;
        }
    }
}
