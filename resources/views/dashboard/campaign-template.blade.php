@extends('dashboard.master')
@section('content')
<style>
  .template.selected{
    border: 5px solid blue;
  }
</style>
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(session('notice'))
            <div class="alert alert-{{ session('notice')['type'] }}">
                {{ session('notice')['message'] }}
            </div>
        @endif
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Select Template</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Create Campaign</a></li>
            <li class="breadcrumb-item active">Select Template</li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
      <form id="campaignForm" action="{{route('campaign.create.schedule')}}" method="post" style="display:none">
        @csrf
        <input name="name" type="text" class="form-control" id="campaignName" value="{{$details['name']}}">
        <input name="subject" type="text" class="form-control" id="subject" value="{{$details['subject']}}">
        <input name="from_name" type="text" class="form-control" id="fromName" value="{{$details['from_name']}}">
        <input name="from_email" type="text" class="form-control" id="fromEmail" value="{{$details['from_email']}}">
        <input name="lists" type="text" class="form-control" id="contactList" value="{{$details['lists']}}">
        <input name="template" type="number" class="form-control" id="template">

      </form>
      <div class="row">
        @foreach($templates as $key => $template)
        <div class="col-md-4">
          <div class="card template" data-temp="{{$template->id}}">
            <img src="{{asset('')}}{{$template->screenshot ? : 'backend/uploads/template-placeholder.jpg'}}" class="card-img-top" alt="{{$template->name}}">
            <div class="card-body">
              <h5 class="text-center mb-4">{{$template->name}}</h5>
            </div>
          </div>
        </div>
        @endforeach
      </div>

      <div class="submit-section pt-5 pb-3">
        <button id="tempSubmitBtn" form="campaignForm" type="submit" class="btn btn-lg btn-primary" disabled>Continue</button>
      </div>

    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content -->
</div>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const templates = document.querySelectorAll('.template');
    const tempField = document.getElementById('template');
    const submitBtn = document.getElementById('tempSubmitBtn');
    templates.forEach( template => {
      template.addEventListener("click", () => {
        templates.forEach( temp => {
          temp.classList.remove('selected');
        });
        template.classList.add('selected');
        tempField.value = template.getAttribute('data-temp');
        submitBtn.removeAttribute('disabled');
      });
    });
  });
</script>
@endsection