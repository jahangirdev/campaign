<?php

namespace App\Http\Controllers;

use App\Models\Contacts;
use App\Models\Lists;
use Illuminate\Http\Request;
use Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CSVContactImport;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function index(){

     }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lists = Lists::all();
        return view("dashboard.contact-create", compact("lists"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "full_name"=> "nullable|string|max:255",
            "email"=> "required|email|unique:contacts",
            "phone" => 'nullable|regex:/^\+?[0-9]{10,14}$/',
            'country' => 'nullable|string',
            'address' => 'nullable|string',
            'list_id' => 'required|integer',
        ]);
        $checkEmail = $this->validateEmail($request->email);
        if (!$checkEmail['status']) {
            return redirect()->back()->with('notice', ['type' => 'danger','message'=> $checkEmail['message']]);
        }
        $contact = new Contacts();
        $contact->fill($request->all());
        if($contact->save()){
            return redirect()->route('contact.create')->with('notice', ['type' => 'success', 'message'=> 'Contact added successfully!']);
        }
        else{
            return redirect()->route('contact.create')->with('notice', ['type' => 'error', 'message' => 'Something went wrong with creating contact!']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $contact = Contacts::find($id);
        $lists = Lists::all();
        return view('dashboard.contact-edit', compact('contact', 'lists'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            "full_name"=> "nullable|string|max:255",
            "email"=> "required|email|unique:contacts,id",
            "phone" => 'nullable|regex:/^\+?[0-9]{10,14}$/',
            'country' => 'nullable|string',
            'address' => 'nullable|string',
            'list_id' => 'required|integer',
        ]);

        $contact = Contacts::find($id);
        $contact->full_name = trim($request->input('first_name') . ' ' . $request->input('last_name'));
        $contact->fill($request->all());
        if($contact->save()){
            return redirect()->back()->with('notice', ['type' => 'success', 'message'=> 'Contact updated successfully!']);
        }
        else{
            return redirect()->back()->with('notice', ['type' => 'error', 'message' => 'Something went wrong with updating contact!']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if(Contacts::find($id)->delete()){
            return redirect()->back()->with('notice', ['type' => 'warning', 'message'=> 'Contact deleted successfully!']);
        }
    }

    private function validateEmail($email){
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ["email" => $email, "status" => false, "message"=> "Invalid format"];
        }

        // Extract the domain from the email address
        list($user, $domain) = explode('@', $email);

        // Check if the email domain has valid MX records
        $dnsRecords = dns_get_record($domain, DNS_MX);
        
        // Check if at least one MX record is found
        if (!empty($dnsRecords)) {
            return ["email" => $email, 'status'=> true,'message'=> 'Valid email'];
        } else {
            return ["email" => $email, "status" => false, "message"=> "Domain or mail server unavailable."];
        }
    }

    public function import(Request $request){

        $request->validate([
            'emails' => 'required|string',
            'list_id'=> 'required|integer',
            'country'=> 'nullable|string'
        ]);
    
        $input = $request->input('emails');
        $pattern = '/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/';
    
        preg_match_all($pattern, $input, $matches);
        $emails = $matches[0];
        $invalidEmails = [];
        $alreadyExists = 0;

        foreach ($emails as $email) {

            $validate = Validator::make(['email' => $email], [
                'email' => 'unique:contacts'
            ]);

            if ($validate->fails()) {
                $alreadyExists++;
                continue;
            }

            $check = $this->validateEmail($email);
            if($check['status']){
                Contacts::create([
                    'email'=> $email,
                    'list_id' => $request->list_id,
                    'country' => $request->country
                ]);
            }
            else{
                array_push($invalidEmails, $check);
            }
        }
        $lists = Lists::all();
        return view('dashboard.contact-create', compact('invalidEmails', 'lists', 'alreadyExists'));
    }

    public function csv_import(Request $request){
        $request->validate([
            'list_id' => 'required|integer',
            'csv_file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('csv_file');
        $list_id = $request->list_id;

        Excel::import(new CSVContactImport($list_id), $file);

        return redirect()->back()->with('notice', ['type' => 'success', 'message' => 'Contacts imported successfully!']);
    }

    public function unsubscribe(Request $request){
        $validator = Validator::make($request->all(), [
            'hash' => 'required',
            'caid'=> 'required|numeric',
            'coid'=> 'required|numeric',
        ]);

        $params = $request->all();

        if($validator->fails()){
            return '<h3 style="text-align:center">URL parameter missing or invalid</h3>';
        }

        return view('unsubscribe', compact('params'));
    }

    public function subscribe(Request $request){
        $validator = Validator::make($request->all(), [
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'full_name' => 'nullable|string',
            'email' => 'required|email',
            'phone' => 'nullable|string',
            'country' => 'nullable|string',
            'address' => 'nullable|string'
        ]);

        if($validator->fails()){
            return response()->json(['type' => 'error', 'message' => $validator->messages()]);
        }
        
        $contact = new Contacts();
        $contact->fill($request->all());
        $list_id = Lists::where('quiz_taker', 1)->first();
        $contact->list_id = $list_id->id;
        if($contact->save()){
            return response()->json(['type' => 'success', 'message' => 'Contact added successfully.']);
        }
    }
    public function afterquiz_unsubscribe(){
        return view('afterquiz-unsubscribe');
    }
    public function afterquiz_unsubscribe_submit(Request $request){
        $request->validate([
            'email' => 'required|email'
        ]);
        $contact = Contacts::where('email', $request->email)->first();
        if($contact){
            $contact->status = 'unsubscribed';
            $contact->save();
            return '<h2 style="text-align:center;color:green">Unsubscribed successfully!</h2>';
        }
        else{
            return redirect()->back()->withErrors(["Email didn't match."]);
        }
    }
}
