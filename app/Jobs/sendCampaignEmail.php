<?php

namespace App\Jobs;

use App\Models\Templates;
use App\Models\EmailSentTrackings;
use App\Models\TrashContacts;
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
        $verify = $this->smtpVerify($this->contact->email);
        if($this->contact->validity === null && $verify['type']  == 'invalid'){
            $this->contact->validity = "invalid";
            $this->contact->reason = $verify['message'];
            TrashContacts::create($this->contact->toArray());
                $this->contact->delete();
                if($this->sentTrack){
                    $this->sentTrack->invalid++;
                }
                return;
        }
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

        
        if($this->sentTrack){
            $this->sentTrack->total_sent++;
            $this->sentTrack->save();
        }
        $this->contact->attemps++;
        $this->contact->point--;
        $this->contact->validity = 'valid';
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

    public function getMXRecords($domain) {
        $mxRecords = [];
        $dnsRecords = dns_get_record($domain, DNS_MX);

        foreach ($dnsRecords as $record) {
            $mxRecords[] = $record['target'];
        }

        return $mxRecords;
    }

    public function smtpVerify($toEmail, $fromEmail = 'contact@jahangirdev.com') {
        // Extract the domain from the recipient email
        $domain = explode('@', $toEmail)[1];

        // Get the mail server's MX records
        $mxRecords = $this->getMXRecords($domain);

        if (empty($mxRecords)) {
            return "Invalid: MX records not found for $domain";
        }

        $errorMessage = "Invalid: Unable to verify with any MX record for $domain";

        // Iterate through MX records and try each one
        foreach ($mxRecords as $mxRecord) {
            // Open a connection to the current mail server
            $smtpConnection = stream_socket_client("tcp://$mxRecord:25", $errno, $errstr, 30);

            if (!$smtpConnection) {
                $errorMessage .= "\nFailed to connect to $mxRecord: $errstr ($errno)";
                continue; // Try the next MX record
            }

            // Read the welcome message from the server
            fread($smtpConnection, 1024);

            // Send HELO command
            fwrite($smtpConnection, "HELO example.com\r\n");
            fread($smtpConnection, 1024);

            // Send MAIL FROM command
            fwrite($smtpConnection, "MAIL FROM:<$fromEmail>\r\n");
            fread($smtpConnection, 1024);

            // Send RCPT TO command
            fwrite($smtpConnection, "RCPT TO:<$toEmail>\r\n");
            $response = fread($smtpConnection, 1024);

            // Close the connection
            fwrite($smtpConnection, "QUIT\r\n");
            fclose($smtpConnection);

            // Check the response and return result if valid
            if (strpos($response, '250') !== false) {
                return ['type' => 'valid'];
            } else {
                $errorMessage .= "\nAttempted $mxRecord: $response";
            }
        }

        return ['type' => 'invalid', 'message' => $errorMessage];
    }

}
