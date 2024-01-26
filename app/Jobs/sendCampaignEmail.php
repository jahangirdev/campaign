<?php

namespace App\Jobs;

use App\Models\Templates;
use App\Models\EmailSentTrackings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Bus\Batchable;
use Carbon\Carbon;

class sendCampaignEmail implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $contact, $campaign, $batchId;
    /**
     * Create a new job instance.
     */
    public function __construct($contact, $campaign)
    {
        $this->contact = $contact;
        $this->campaign = $campaign;
        $this->batchId = $campaign->batch_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $unsubscribeUrl = route('track.unsubscribe', ['caid' => $this->campaign->id, 'coid' => $this->contact->id, 'batch' => $this->batchId]);


        $html = Templates::find($this->campaign->template)->code;

        $updatedHTML = $this->prepare_email_html($html);
        $updatedHTML = str_replace('{{unsubscribe}}', $unsubscribeUrl, $updatedHTML);
        $updatedHTML = str_replace('%7B%7Bunsubscribe%7D%7D', $unsubscribeUrl, $updatedHTML);
      
      	$updatedHTML = str_replace('{{subject}}', $this->campaign->subject, $updatedHTML);
      	$updatedHTML = str_replace('%7B%7Bsubject%7D%7D', $this->campaign->subject, $updatedHTML);
      
        Mail::send([], [], function ($message) use ($updatedHTML) {
            $message->to($this->contact->email)
                ->subject($this->campaign->subject)
                ->from($this->campaign->from_email, $this->campaign->from_name)
                ->replyTo('sales@prowebsol.com')
                ->html($updatedHTML);
        });

        $sentTrack = EmailSentTrackings::where('campaign_id', $this->campaign->id)->where('batch_id', $this->batchId)->first();
        if($sentTrack){
            $sentTrack->total_sent++;
            $sentTrack->save();
        }
        $this->contact->attemps++;
        $this->contact->point--;
        $this->contact->last_sent = Carbon::now();
        $this->contact->save();
      
      	sleep(10);

    }

    public function prepare_email_html($html){

      	$imageUrl = asset('frontend/images/transparent.png');
        $trackUrl = route('track.opens', ['image' => $imageUrl, 'caid' => $this->campaign->id, 'coid' => $this->contact->id, 'batch' => $this->batchId]);


        // Create a DOMDocument object for the existing HTML
        $dom = new \DOMDocument();
        $dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        $image = $dom->createElement('img');
        $image->setAttribute('src',$trackUrl);
      	$image->setAttribute('alt',"ProWebSol");

        if ($body->hasChildNodes()) {
            $body->insertBefore($image,$body->firstChild);
        } else {
            $body->appendChild($image);
        }
        

        // Find all anchor (a) elements
        $anchors = $dom->getElementsByTagName('a');

        foreach ($anchors as $anchor) {
            $originalUrl = $anchor->getAttribute('href');

            if(filter_var($originalUrl, FILTER_VALIDATE_URL) !== false){
                $replacementUrl = route('track.clicks', ['url' => $originalUrl, 'caid' => $this->campaign->id, 'coid' => $this->contact->id , 'batch' => $this->batchId]);

                // Update the href attribute with the new URL
                $anchor->setAttribute('href', $replacementUrl);
            }
            
        }

        // Get the updated HTML
        $updatedHtml = $dom->saveHTML();

        return $updatedHtml;
  }

}
