<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Models\Templates;
class TemplateController extends Controller
{
    public function index(){
        $templates = Templates::where('status', 'publish')->paginate(9);
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
}
