<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lists;
use App\Models\Contacts;

class ListController extends Controller
{
    public function index() {
        $lists =Lists::orderBy("created_at","desc")->paginate(10);
        return view("dashboard.list-index", compact("lists"));
    }
    public function create(){
        return view("dashboard.list-create");
    }

    public function store(Request $request){
        $request->validate([
            "name"=> "required|string|max:100",
        ]);
        Lists::create([
            "name"=> $request->name
        ]);
        return redirect()->route('list.new')->with("success","List created successfully!");
    }
    public function edit($id) {
        $list =Lists::find($id);
        return view("dashboard.list-edit", compact("list"));
    }

    public function update(Request $request, $id) {
        $request->validate([
            "name"=> "required|string|max:100",
        ]);
        Lists::find($id)->update([
            "name"=> $request->name
        ]);
        return redirect()->route("list.index")->with("success","List updated");
    }
    public function view($id){
        $list = Lists::find($id);
        $contacts = Contacts::where('list_id', $id)->paginate(100);
        return view('dashboard.list-show', compact('contacts', 'list'));
    }
    public function destroy($id) {
        Lists::find($id)->delete();
        return redirect()->route("list.index")->with("success","List deleted successfully");
    }
}
