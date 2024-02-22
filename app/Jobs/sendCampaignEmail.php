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

    public $contact, $campaign, $batchId, $sentTrack;
    /**
     * Create a new job instance.
     */
    public function __construct($contact, $campaign)
    {
        $this->contact = $contact;
        $this->campaign = $campaign;
        $this->batchId = $campaign->batch_id;
        $this->sentTrack = EmailSentTrackings::where('campaign_id', $this->campaign->id)->where('batch_id', $this->batchId)->first();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $unsubscribeUrl = route('track.unsubscribe', ['caid' => $this->campaign->id, 'coid' => $this->contact->id, 'batch' => $this->batchId]);


        $html = Templates::find($this->campaign->template)->code;

        //replace {{cart_url}} variable with cart link

        $html = str_replace('{{cart_url}}', $this->recommendedCartLink($this->contact->email), $html);
        $html = str_replace('%7B%7Bcart_url%7D%7D', $this->recommendedCartLink($this->contact->email), $html);

        $updatedHTML = $this->prepare_email_html($html);
        
        $updatedHTML = str_replace('{{subject}}', $this->campaign->subject, $updatedHTML);
      	$updatedHTML = str_replace('%7B%7Bsubject%7D%7D', $this->campaign->subject, $updatedHTML);

        $updatedHTML = str_replace('{{name}}', ($this->contact->full_name ? : 'there'), $updatedHTML);
      	$updatedHTML = str_replace('%7B%7Bsubject%7D%7D', ($this->contact->full_name ? : 'there'), $updatedHTML);

        $updatedHTML = str_replace('{{recommended_packs}}', $this->recommendedPacks($this->contact->email), $updatedHTML);
        $updatedHTML = str_replace('%7B%7Brecommended_packs%7D%7D', $this->recommendedPacks($this->contact->email), $updatedHTML);

        $updatedHTML = str_replace('{{recommended_comps}}', $this->recommendedComps($this->contact->email), $updatedHTML);
        $updatedHTML = str_replace('%7B%7Brecommended_comps%7D%7D', $this->recommendedComps($this->contact->email), $updatedHTML);

        $updatedHTML = str_replace('{{unsubscribe}}', $unsubscribeUrl, $updatedHTML);
        $updatedHTML = str_replace('%7B%7Bunsubscribe%7D%7D', $unsubscribeUrl, $updatedHTML);
      
      	
      
        Mail::send([], [], function ($message) use ($updatedHTML) {
            $message->to($this->contact->email)
                ->subject($this->campaign->subject)
                ->from($this->campaign->from_email, $this->campaign->from_name)
                ->html($updatedHTML);
        });

        
        if($this->sentTrack){
            $this->sentTrack->total_sent++;
            $this->sentTrack->save();
        }
        $this->contact->attemps++;
        $this->contact->last_sent = Carbon::now();
        $this->contact->save();
      
      	sleep(10);

    }

    public function failed(\Exception $exception)
    {
        if($this->sentTrack){
            $this->sentTrack->failed++;
            $this->sentTrack->save();
        }
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

    public function recommendedPacks($email){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://healthbox.store/wp-json/healthbox-quiz/v1/packs/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('email' => $email),
        ));

        $response = json_decode(curl_exec($curl));

        curl_close($curl);
        return implode(', ', $response->products);
    }

    public function recommendedComps($email){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://healthbox.store/wp-json/healthbox-quiz/v1/comps/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('email' => $email),
        ));

        $response = json_decode(curl_exec($curl));

        curl_close($curl);
        return implode(', ', $response->products);
    }

    public function recommendedCartLink($email){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://healthbox.store/wp-json/healthbox-quiz/v1/cart-url/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('email' => $email),
        ));

        $response = json_decode(curl_exec($curl));

        curl_close($curl);
        return $response->cart_url;
    }

}
