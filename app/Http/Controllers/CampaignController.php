<?php

namespace App\Http\Controllers;

use App\Jobs\createCampaignEmailQueue;
use App\Models\Campaigns;
use App\Models\Lists;
use App\Models\Trackings;
use Illuminate\Http\Request;
use App\Models\Templates;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Models\EmailSentTrackings;


class CampaignController extends Controller
{
    public function index(){
        $nextSchedule = function ($campaign) {
            return $this->nextSchedule($campaign)->toDateTimeString();
        };
        $campaigns = Campaigns::where('status', '!=', 'trash')->with('templates', 'trackings', 'sentTrackings')->orderBy("created_at","desc")->paginate(10);
        return view("dashboard.campaign-index", compact("campaigns", "nextSchedule"));
    }

    public function create(){
        $lists = Lists::all();
        return view("dashboard.campaign-create", compact("lists"));
    }

    public function template(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'subject'=> 'required|string|max:255',
            'from_name'=> 'required|string|max:100',
            'from_email'=> 'required|email',
            'lists' => 'required|array',
            'lists.*' => 'integer'
        ]);

        $details = $request->all();
        $details['lists'] = implode(',', $details['lists']);
        $templates = Templates::where('status', 'publish')->get();

        return view('dashboard.campaign-template', compact('details', 'templates'));
    }

    public function schedule(Request $request){
        $validator = Validator::make( $request->all(), [
            'name' => 'required|string|max:255',
            'subject'=> 'required|string|max:255',
            'from_name'=> 'required|string|max:100',
            'from_email'=> 'required|email',
            'lists' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('campaign.create')->with('notice', ['type'=> 'danger', 'message' => 'Some data was missing or invalid. Please fill out the form again']);
        }

        $validator = Validator::make( $request->all(), [
            'template' => 'required|integer'
        ] );

        if( $validator->fails()) {
            return redirect()->back()->with('notice', ['type'=> 'danger', 'message' => 'You must select an template to continue.']);
        }
        $details = $request->all();
        return view('dashboard.campaign-schedule', compact('details'));
    }

    public function store(Request $request){
        $validator = Validator::make( $request->all(), [
            'name' => 'required|string|max:255',
            'subject'=> 'required|string|max:255',
            'from_name'=> 'required|string|max:100',
            'from_email'=> 'required|email',
            'lists' => 'required|string',
            'template' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return redirect()->route('campaign.create')->with('notice', ['type'=> 'danger', 'message' => 'Some data was missing or invalid. Please fill out the form again']);
        }

        $validator = Validator::make( $request->all(), [
            'schedule' => 'nullable|string',
            'run_at' => 'required|string',
            'status' => 'required|string',
        ] );

        if( $validator->fails()) {
            return redirect()->route('campaign.create')->with('notice', ['type'=> 'danger', 'message' => 'Some data was missing or invalid. Please fill out the form again']);
        }

        $campaign = new Campaigns();

        $campaign->fill($request->all());
        $campaign->lists = trim($request->lists, '"');
        $campaign->schedule = !empty($request->schedule) ? Carbon::parse($request->schedule)->utc() : Carbon::now();

        if( $campaign->save() ){
            
            if( $request->run_at == 'instant' && $request->status == 'publish'){
                createCampaignEmailQueue::dispatch($campaign->id);
            }

            return redirect()->route('campaign.index')->with('notice', ['type'=> 'success', 'message' => 'Campaign created successfully.']);
        }
        else{
            return redirect()->route('campaign.index')->with('notice', ['type'=> 'danger', 'message' => 'Something was wrong with creating campaign. Please try again later.']);
        }
    }

    public function nextSchedule($campaign)
    {
        switch ($campaign->repeat) {
            case 'every5minutes':
                return Carbon::parse($campaign->last_run)->addMinutes(5);
            case 'everyday':
                return Carbon::parse($campaign->last_run)->addDay();
    
            case 'everyweek':
                return Carbon::parse($campaign->last_run)->addWeek();
    
            case 'every15days':
                return Carbon::parse($campaign->last_run)->addDays(15);
    
            case 'everymonth':
                return Carbon::parse($campaign->last_run)->addMonth();
    
            case 'every3months':
                return Carbon::parse($campaign->last_run)->addMonths(3);
    
            case 'every6months':
                return Carbon::parse($campaign->last_run)->addMonths(6);
    
            case 'everyyear':
                return Carbon::parse($campaign->last_run)->addYear();
    
            default:
                return;
        }
    }


    /**
     * Stop campaign if it is running
     * We should also cancel the batch job associated with the campaign
     */
    
     public function stop(Request $request, $id){
        $request->validate([
            'batch_id' => 'required|string'
        ]);
        $campaign = Campaigns::where('id', $id)->where('batch_id', $request->batch_id)->where('status', 'running')->first();
        if(!$campaign){
            return redirect()->route('campaign.index')->with('notice', ['type' => 'danger', 'message'=> 'Campaign is not running or not found.']);
        }

        try {
            DB::table('jobs')->where('payload', 'like', '%' . $request->batch_id . '%')->delete();
            // Artisan::call('queue:restart');
            $campaign->status = 'stopped';
            $campaign->save();

            return redirect()->route('campaign.index')->with('notice', ['message' => 'Campaign Stopped. Queue cleared successfully', 'type' => 'warning']);
        } catch (\Exception $e) {
            // Handle the exception
            return redirect()->route('campaign.index')->with('notice', ['message' => $e->getMessage(), 'type' => 'danger']);
        }
     }


     public function edit($id){
        $campaign = Campaigns::find($id);
        $lists = Lists::all();
        if($campaign->status != 'running'){
            return view('dashboard.campaign-edit', compact('campaign', 'lists'));
        }
        return redirect()->route('campaign.index')->with('notice', ['type'=> 'danger', 'message' => 'Campaign not found or it is running.']);
     }

     public function edit_template(Request $request, $id){
        $request->validate([
            'name' => 'required|string|max:255',
            'subject'=> 'required|string|max:255',
            'from_name'=> 'required|string|max:100',
            'from_email'=> 'required|email',
            'lists' => 'required|array',
            'lists.*' => 'integer',
        ]);

        $campaign = Campaigns::find($id);

        $campaign->name = $request->name;
        $campaign->subject = $request->subject;
        $campaign->from_name = $request->from_name;
        $campaign->from_email = $request->from_email;
        $campaign->lists = implode(',', $request->lists);
        
        if($campaign->save()){
            $templates = Templates::where('status', 'publish')->get();
            return view('dashboard.campaign-edit-template', compact('campaign', 'templates'))->with('notice', ['type' => 'success','message'=> 'Campaign destails updated successfully.']);
        }
        else{
            return redirect()->route('campaign.edit', $id)->with('notice', ['type' => 'danger' ,'message'=> 'Something Went Wrong. Please try again.']);
        }
     }

     public function edit_schedule(Request $request, $id){
        $request->validate([
            'template' => 'required|integer'
        ]);

        $campaign = Campaigns::find($id);
        $campaign->template = $request->template;
        if($campaign->save()){
            return view('dashboard.campaign-edit-schedule', compact('campaign'))->with('notice', ['type' => 'success','message'=> 'Template updated successfully.']);
        }
        else{
            return redirect()->route('campaign.edit', $id)->with('notice', ['type' => 'danger', 'message' => 'Something went wrong, please try again.']);
        }
     }

     public function update( Request $request, $id){
        $validator = Validator::make($request->all(), [
            'schedule' => 'nullable|date_format:Y-m-d\TH:i|after_or_equal:'.Carbon::now()->utc(),
            'run_at' => 'required|string',
            'status' => 'required|string',
        ]);

        if($validator->fails()){
            return redirect()->route('campaign.edit', $id)->withErrors($validator)->withInput();
        }

        $campaign = Campaigns::find($id);

        $campaign->schedule = Carbon::parse($request->schedule);
        $campaign->run_at = $request->run_at;
        $campaign->status = $request->status;

        if($campaign->save()){
            if($campaign->run_at == 'instant' && $campaign->status == 'publish'){
                createCampaignEmailQueue::dispatch($campaign->id);
            }
            return redirect()->route('campaign.index')->with('notice', ['type' => 'success', 'message'=> 'Campaign updated successfully.']);
        }
        else{
            return redirect()->route('campaign.edit', $id)->with('notice', ['type' => 'danger', 'message' => 'Something went wrong, please try again.']);
        }

     }

     //campaign view

     public function view($id){
        $instances = EmailSentTrackings::where('campaign_id', $id)->with('trackings')->paginate(10);
        $campaign = Campaigns::find($id);
        return view('dashboard.campaign-instances', compact('instances','campaign'));
     }

     public function trash($id){
        $campaign = Campaigns::find($id);
        if($campaign && $campaign->status != 'running'){
            $campaign->delete();
            EmailSentTrackings::where('campaign_id', $campaign->id)->delete();
            Trackings::where('campaign_id', $campaign->id)->delete();
            return redirect()->back()->with('notice', ['type' => 'warning','message'=> 'Campaign deleted successfully.']);
        }
        else{
            return redirect()->back()->with('notice', ['type' => 'danger','message'=> 'Running campaign cannot be deleted.']);
        }
    }
}
