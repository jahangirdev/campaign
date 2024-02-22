<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trackings;
use App\Models\Contacts;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TrackingController extends Controller
{
    public function track_opens(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'coid'  => 'required|numeric',
            'caid'  => 'required|numeric',
            'image' => 'required|url',
            'batch' => 'required|string'
        ]);

        if ($validator->fails()) {
            return redirect($request->query('image'));
        }
        $tracking = Trackings::where('contact_id', $request->query('coid'))->where('campaign_id', $request->query('caid'))->where('batch_id', $request->query('batch'))->first();
        
        if (!$tracking) {
            $tracking = new Trackings();
        }
        //trackings
        $tracking->contact_id = $request->query('coid');
        $tracking->campaign_id = $request->query('caid');
        $tracking->opens = 1;
        $tracking->batch_id = $request->query('batch');
        $tracking->save();

        //contacts

        $contact = Contacts::find($request->query('coid'));
        if($contact){
            $contact->opens++;
            $contact->save();
        }

        return redirect($request->query('image'));
    }

    public function track_clicks(Request $request){
        $validator = Validator::make($request->query(), [
            'coid' => 'required|numeric',
            'caid' => 'required|numeric',
            'url'  => 'required|url',
            'batch' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->redirectTo(urldecode($request->query('url')));
        }

        $targetUrl = urldecode($request->query('url'));

        //trackings
        $tracking = Trackings::where('contact_id', $request->query('coid'))->where('campaign_id', $request->query('caid'))->where('batch_id', $request->query('batch'))->first();
        if (!$tracking) {
            $tracking = new Trackings();
        }
        $tracking->contact_id = $request->query('coid');
        $tracking->campaign_id = $request->query('caid');
        $tracking->clicks++;
        $tracking->opens = 1;
        $tracking->batch_id = $request->query('batch');
        $tracking->save();

        //contacts

        $contact = Contacts::find($request->query('coid'));
        if($contact){
            $contact->clicks++;
            $contact->save();
        }

        return response()->redirectTo($targetUrl);
    }


    public function unsubscribe_form(Request $request){
        $validator = Validator::make($request->query(), [
            'caid'=> 'required|numeric',
            'coid'=> 'required|numeric',
            'batch' => 'required|string'
        ]);
        if ($validator->fails()) {
            return "Invalid link! Please try using a valid link.";
        }
        $details = $request->all();
        return view('unsubscribe', compact('details'));
    }

    public function unsubscribe(Request $request){

        $request->validate([
            'caid'  => 'required|numeric',
            'coid'  => 'required|numeric',
            'email' => [
                'required',
                'email',
                Rule::exists('contacts', 'email'),
            ],
            'batch' => 'required|string',
        ], [
            'email.exists' => 'The provided email does not exist.',
        ]);

        $contact = Contacts::find($request->coid);
        if ($contact && $contact->status == 'unsubscribed') {
            return '<h2 style="text-align:center;color:green">Already unsubscribed</h2>';
        }
        if($contact && $contact->email == $request->email){
            $contact->status = 'unsubscribed';
            $contact->save();
            $tracking = Trackings::where('contact_id', $request->coid)->where('campaign_id', $request->caid)->where('batch_id', $request->batch)->first();
            if (!$tracking) {
                $tracking = new Trackings();
            }
            $tracking->contact_id = $request->coid;
            $tracking->campaign_id = $request->caid;
            $tracking->unsubscribe = 1;
            $tracking->batch_id = $request->batch;
            $tracking->save();

            return '<h2 style="text-align:center;color:green">Unsubscribed successfully!</h2>';
            
        }
        return redirect()->back()->withErrors(["Email didn't match."]);

    }

}
