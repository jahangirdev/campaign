@extends('dashboard.master')
@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Edit {{$campaign->name}}</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Edit Campaign</li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <div class="content">
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
      <div class="card">
        <div class="card-header">
          <h3>Edit Campaign</h3>
        </div>
        <div class="card-body">
          <form method="POST" action="{{route('campaign.edit.template', $campaign->id)}}">
            @csrf
            <div class="form-group mt-3">
              <label for="campaignName">Campaign Name</label>
              <input name="name" type="text" class="form-control" id="campaignName" value="{{old('name') ? : $campaign->name}}">
            </div>
            <div class="form-group mt-3">
              <label for="subject">Subject</label>
              <input name="subject" type="text" class="form-control" id="subject" value="{{old('subject') ? : $campaign->subject}}">
            </div>
            <div class="row gx-4 mt-3">
              <div class="col">
                <label for="fromName">From Name</label>
                <input name="from_name" type="text" class="form-control" id="fromName" value="{{old('from_name') ? : $campaign->from_name}}">
              </div>
              <div class="col">
                <label for="fromEmail">From Email</label>
                <input name="from_email" type="text" class="form-control" id="fromEmail" value="{{old('from_email') ? : $campaign->from_email}}">
              </div>
            </div>
            <div class="form-group mt-3">
              <label>Contact Lists</label>

              <ul class="list-group list-group-flush">
                @foreach($lists as $list)
                <li class="list-group-item">
                  <input name="lists[]" class="form-check-input" type="checkbox" value="{{$list->id}}" id="defaultCheck2" {{in_array($list->id, explode(',', trim($campaign->lists, '"'))) ? 'checked': ''}}>
                  <label class="form-check-label" for="defaultCheck{{$list->id}}">
                    {{$list->name}}
                  </label>
                </li>
                @endforeach
              </ul>
            </div>

            <button type="submit" class="btn btn-primary">Update & Continue</button>
          </form>
        </div>
      </div>


    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content -->
</div>
@endsection