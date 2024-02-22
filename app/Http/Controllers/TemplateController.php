<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Models\Templates;
use App\Models\Contacts;
class TemplateController extends Controller
{
    public function index(){
        $templates = Templates::paginate(9);
        return view("dashboard.template-index", compact("templates"));
    }

    public function create(){
        return view("dashboard.template-create");
    }

    public function store(Request $request){
        $request->validate([
            "name"=> "required|string",
            "code"=> "required|string",
            "screenshot"=> "image|mimes:jpg,jpeg,png,gif,webp|max:1024",
            "status"=> ["required", "string", Rule::in(['draft', 'publish'])],
        ]);

        // Prepare file details
        
        $template = new Templates;

        if($request->hasFile('screenshot')){
            $dir = public_path('backend/uploads/');
            $imageName = time().'.'.$request->screenshot->extension();  
        
            $request->screenshot->move($dir, $imageName);
            $template->screenshot = 'backend/uploads/'.$imageName;
        }

        $template->fill($request->all());
        if($template->save()){
            return redirect()->route('template.index')->with('notice', ['type' => 'success','message'=> 'Template created successfully!']);
        }
        else{
            return redirect()->route('template.index')->with('notice', ['type' => 'danger','message'=> 'Something went wrong. Please try again later.']);
        }
    }

    public function edit($id){
        $template = Templates::find($id);
        return view('dashboard.template-edit', compact('template'));
    }

    public function update(Request $request, $id){
        $request->validate([
            "name"=> "required|string",
            "code"=> "required|string",
            "screenshot"=> "image|mimes:jpg,jpeg,png,gif,webp|max:1024",
            "status"=> ["required", "string", Rule::in(['draft', 'publish'])],
        ]);

        $template = Templates::find($id);

        if($request->hasFile('screenshot')){
            $dir = public_path('backend/uploads/');
            $imageName = time().'.'.$request->screenshot->extension();  
        
            if($request->screenshot->move($dir, $imageName) && $template->screenshot != null){
              if(file_exists(public_path($template->screenshot))){
                unlink(public_path($template->screenshot));
              }
            }
            $template->screenshot = 'backend/uploads/'.$imageName;

        }

        $template->fill($request->all());
        if($template->save()){
            return redirect()->route('template.index')->with('notice', ['type' => 'success','message'=> 'Template updated successfully!']);
        }
        else{
            return redirect()->route('template.index')->with('notice', ['type' => 'danger','message'=> 'Something went wrong. Please try again later.']);
        }
    }

    public function preview($id){
        $template = Templates::find($id);
        return $template->code;
    }
    public function destroy($id){
        $template = Templates::find($id);
        if($template->delete()){
            return redirect()->route('template.index')->with('notice', ['type' => 'warning','message'=> 'Template deleted successfully.']);
        }
        else{
            return redirect()->route('template.index')->with('notice', ['type' => 'danger','message'=> 'Something went wrong. Please try again later.']);
        }

    }
    public function after_quiz($id){
        $after_quiz = Templates::where('after_quiz', 1)->first();
        $template = Templates::find($id);
        $template->after_quiz = 1;
        if($template->save()){
            if($after_quiz){
                $after_quiz->after_quiz = 0;
                $after_quiz->save();
            }
            return redirect()->route('template.index')->with('notice', ['type' => 'success','message'=> 'Successfully set the template as After Quiz.']);
        }
        else{
            return redirect()->route('template.index')->with('notice', ['type' => 'danger','message'=> 'Failed to set the tempalte as After Quiz.']);
        }
    }
    
    public function get_after_quiz_template(Request $request){
        if(empty($request->email)){
            $data = ['name' => '', 'code' => ''];
            return response()->json($data);
        }
        $contact = Contacts::where('email', $request->email)->first();
        $after_quiz = Templates::where('after_quiz', 1)->first();
        if($after_quiz && $contact){
            $unsubscribeUrl = route('afterquiz.unsubscribe');
            $html = $after_quiz->code;
            // Replace variables in the HTML
            $html = str_replace('{{cart_url}}', $this->recommendedCartLink($request->email), $html);
            $html = str_replace('%7B%7Bcart_url%7D%7D', $this->recommendedCartLink($request->email), $html);

            
            $html = str_replace('{{subject}}', $after_quiz->name, $html);
            $html = str_replace('%7B%7Bsubject%7D%7D', $after_quiz->name, $html);
            $html = str_replace('{{name}}', ($contact->full_name ?? 'there'), $html);
            $html = str_replace('%7B%7Bname%7D%7D', ($request->full_name ?? 'there'), $html);
            $html = str_replace('{{recommended_packs}}', $this->recommendedPacks($request->email), $html);
            $html = str_replace('%7B%7Brecommended_packs%7D%7D', $this->recommendedPacks($request->email), $html);
            $html = str_replace('{{recommended_comps}}', $this->recommendedComps($request->email), $html);
            $html = str_replace('%7B%7Brecommended_comps%7D%7D', $this->recommendedComps($request->email), $html);
            $html = str_replace('{{unsubscribe}}', $unsubscribeUrl, $html);
            $html = str_replace('%7B%7Bunsubscribe%7D%7D', $unsubscribeUrl, $html);
            return ['name' => str_replace('{{name}}', $contact->full_name, $after_quiz->name), 'code' => $html];
        }
        else{
            $data = ['name' => '', 'code' => ''];
            return response()->json($data);
        }
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
