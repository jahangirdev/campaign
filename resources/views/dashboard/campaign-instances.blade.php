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
          <h1 class="m-0">{{$campaign->name}}</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Campaigns</a></li>
            <li class="breadcrumb-item active">Instances</li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
      
      <div class="campaigns-section d-flex flex-column">
        @foreach($instances as $instance)
        <div class="campaign-wrap">
          <div class="card mb-3">
            <div class="row g-0">
              <div class="col-md-3">
                <img style="max-height:100%;max-width:250px" src="{{asset('')}}{{$campaign->templates != null && $campaign->templates->screenshot ? $campaign->templates->screenshot : 'backend/uploads/template-placeholder.jpg'}}" class="img-fluid rounded-start" alt="...">
              </div>
              <div class="col-md-9">
                <div class="card-body pl-3">
                  <h5 class="">{{$instance->created_at}}</h5>
                  <div class="row mt-4">
                    <div class="col">
                      {{$instance->total_sent}}
                      <small class="text-muted"><br>Sent</small>
                    </div>
                    <div class="col">
                      {{$instance->trackings()->sum('opens')}}
                      <small class="text-muted"><br>Opened</small>
                    </div>
                    <div class="col">
                    {{$instance->trackings()->sum('clicks')}}
                      <small class="text-muted"><br>Clicks</small>
                    </div>
                    <div class="col">
                    {{$instance->trackings()->sum('unsubscribe')}}
                      <small class="text-muted"><br>Unsubscribed</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>
      <div class="pt-3 paginations">
      {{ $instances->links('vendor.pagination.bootstrap-4') }}
      </div>
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content -->
</div>
@endsection