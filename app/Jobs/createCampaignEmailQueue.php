<?php

namespace App\Jobs;

use App\Models\Campaigns;
use App\Models\Contacts;
use App\Models\EmailSentTrackings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Jobs\sendCampaignEmail;
use App\Jobs\campaignBatchJob;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use Illuminate\Bus\Batch;
use Carbon\Carbon;

class createCampaignEmailQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $campaign, $contacts;


    /**
     * Create a new job instance.
     */
    public function __construct($campaign_id)
    {
        $this->campaign = Campaigns::find($campaign_id);
        $this->campaign->status = 'running';
        $this->campaign->last_run = Carbon::now();
        $this->campaign->save();

        $this->contacts = Contacts::whereIn("list_id", explode(',', $this->campaign->lists))->where('status', '!=', 'unsubscribed')->get();
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $jobs = [];

        $batch = Bus::batch([]);

        foreach ($this->contacts as $contact) {
            $job = new SendCampaignEmail($contact, $this->campaign);
            $jobs[] = $job;
            $batch->add($job);
        }

        $campaign = $this->campaign;
        $carbon = new Carbon();
        $nextRun = $this->nextRun($campaign->repeat);
        $batch = Bus::batch($jobs)->finally( function(Batch $batch) use ( $campaign, $carbon, $nextRun) {
            if($campaign->type == "repeat" && $nextRun  <= $carbon->parse($campaign->stop_at)) {
                $campaign->status = "completed";
            }
            else{
                $campaign->status = 'finished';
            }
            $campaign->total_runs++;
            $campaign->save();


        } )->dispatch();

        $this->campaign->batch_id = $batch->id;
        $this->campaign->save();

        EmailSentTrackings::create([
            'campaign_id' => $this->campaign->id,
            'batch_id' => $batch->id,
        ]);
        return $batch->id;

    }

    public function failed($exception): void
    {
        $this->campaign->status = 'failed';
        $this->campaign->save();

        Mail::raw("Failed to create email queues for {$this->campaign->name}", function ($message){
            $message->to("designermja@gmail.com")
            ->subject("Campaign failed!")
            ->from("hello@prowebsol.com", "ProWebSol Marketing");
        });
        
    }

    public function nextRun($repeat)
    {
        switch ($repeat) {
            case 'every5minutes':
                return Carbon::now()->addMinutes(5);
            case 'everyday':
                return Carbon::now()->addDay();
    
            case 'everyweek':
                return Carbon::now()->addWeek();
    
            case 'every15days':
                return Carbon::now()->addDays(15);
    
            case 'everymonth':
                return Carbon::now()->addMonth();
    
            case 'every3months':
                return Carbon::now()->addMonths(3);
    
            case 'every6months':
                return Carbon::now()->addMonths(6);
    
            case 'everyyear':
                return Carbon::now()->addYear();
    
            default:
                // If not recognized, default to subtracting one year
                return Carbon::now()->subYears(10);
        }
    }
}
