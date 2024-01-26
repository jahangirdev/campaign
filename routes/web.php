<?php

use App\Http\Controllers\CampaignController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MailController;
use App\Http\Controllers\ListController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard.welcome');
})->middleware(['auth', 'verified'])->name('dashboard.welcome');

Route::middleware('auth')->group(function () {
    //breeze routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //end breeze routes

    Route::get('/send-email', [MailController::class, 'sendEmail']);

    Route::get('/list/new', [ListController::class,'create'])->name('list.new');
    Route::post('/list/new', [ListController::class,'store'])->name('list.store');
    Route::get('/list', [ListController::class,'index'])->name('list.index');
    Route::get('/list/{id}', [ListController::class,'view'])->name('list.view');
    Route::get('/list/{id}/edit', [ListController::class,'edit'])->name('list.edit');
    Route::put('/list/{id}', [ListController::class,'update'])->name('list.update');
    Route::delete('/list/{id}', [ListController::class,'destroy'])->name('list.delete');


    //contacts

    Route::resource('/contact', ContactController::class);
    Route::get('/contacts/trashed', [ContactController::class, 'trash_index'])->name('contact.trashed');
    Route::post('/contacts/import', [ContactController::class,'import'])->name('contact.import');
    Route::post('contacts/csv-import', [ContactController::class,'csv_import'])->name('contact.csv.import');
    Route::get('/contacts/unsubscribe', [ContactController::class,'unsubscribe'])->name('contact.unsubscribe');




    //templates

    Route::get('templates', [TemplateController::class,'index'])->name('template.index');
    Route::get('templates/create', [TemplateController::class,'create'])->name('template.create');
    Route::post('templates/store', [TemplateController::class,'store'])->name('template.store');
    Route::get('templates/{id}/edit', [TemplateController::class,'edit'])->name('template.edit');
    Route::post('templates/{id}/update', [TemplateController::class,'update'])->name('template.update');
    Route::get('templates/{id}/preview', [TemplateController::class, 'preview'])->name('template.preview');
    Route::delete('templates/{id}/', [TemplateController::class, 'destroy'])->name('template.destroy');


    //campaign

    Route::get('campaigns', [CampaignController::class, 'index'])->name('campaign.index');
    Route::get('/campaigns/create', [CampaignController::class, 'create'])->name('campaign.create');
    Route::post('/campaigns/create/template', [CampaignController::class, 'template'])->name('campaign.create.template');
    Route::post('/campaigns/create/schedule', [CampaignController::class, 'schedule'])->name('campaign.create.schedule');
    Route::post('/campaigns/store', [CampaignController::class, 'store'])->name('campaign.store');
    Route::get('/campaigns/{id}/edit', [CampaignController::class, 'edit'])->name('campaign.edit');
    Route::post('/campaigns/{id}/edit/template', [CampaignController::class, 'edit_template'])->name('campaign.edit.template');
    Route::post('/campaigns/{id}/edit/schedule', [CampaignController::class, 'edit_schedule'])->name('campaign.edit.schedule');
    Route::post('/campaigns/{id}/update', [CampaignController::class, 'update'])->name('campaign.update');
    Route::get('/campaigns/{id}/view', [CampaignController::class,'view'])->name('campaign.view');

    Route::put('/campaigns/{id}/stop/', [CampaignController::class,'stop'])->name('campaign.stop');
    Route::get('/campaigns/{id}/trash', [CampaignController::class,'trash'])->name('campaign.trash');
    Route::get('/campaigns/trash', [CampaignController::class, 'trash_index'])->name('campaign.trash.index');
    Route::get('/campaigns/{id}/restore', [CampaignController::class,'restore'])->name('campaign.restore');



});



//trackings

Route::get('/opens', [TrackingController::class,'track_opens'])->name('track.opens');
Route::get('/clicks', [TrackingController::class,'track_clicks'])->name('track.clicks');
Route::get('/unsubscribe', [TrackingController::class, 'unsubscribe_form'])->name('track.unsubscribe');
Route::post('/unsubscribe', [TrackingController::class, 'unsubscribe'])->name('track.unsubscribe.submit');


require __DIR__.'/auth.php';
