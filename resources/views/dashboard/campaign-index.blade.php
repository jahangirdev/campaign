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
          <h1 class="m-0">Campaigns</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Campaigns</li>
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
        @foreach($campaigns as $campaign)
        @php
          $status = $campaign->status == 'publish' ? 'scheduled' : $campaign->status;
          $statusColor = [
            'running' => 'success',
            'draft' => 'warning',
            'failed' => 'danger',
            'scheduled' => 'primary',
            'completed' => 'secondary',
            'cancelled' => 'dark',
            'stopped' => 'warning'
          ];
        @endphp
        <div class="campaign-wrap">
          <div class="card mb-3">
            <div class="row g-0">
              <div class="col-md-3">
                <img style="max-height:100%;max-width:250px" src="{{asset('')}}{{$campaign->templates != null && $campaign->templates->screenshot ? $campaign->templates->screenshot : 'backend/uploads/template-placeholder.jpg'}}" class="img-fluid rounded-start" alt="...">
              </div>
              <div class="col-md-9">
                <div class="card-body flex-column">
                  <div class="row">
                    <div class="col-md-6">
                      <a href="{{route('campaign.view', $campaign->id)}}">
                        <h5 class="">{{$campaign->name}}</h5>
                      </a>
                    </div>
                    <div class="col-md-6 text-right">
                      <span class="ml-1" style="font-size:20px">Runned: {{$campaign->total_runs}}</span>
                    </div>
                  </div>
                  <div>
                    <span class="badge text-white bg-{{$statusColor[$status]}}">{{ucwords($status)}}</span>
                    <span class="text-muted ml-4">{{$status == 'scheduled' ? "Will run at $campaign->schedule (UTC)": ""}}</span>
                  </div>
                  <div class="row mt-4">
                    <div class="col">
                      Sent: {{$campaign->sentTrackings()->sum('total_sent')}} | Failed: {{$campaign->sentTrackings()->sum('failed')}}
                      <small class="text-muted"><br>Sent Tracking</small>
                    </div>
                    <div class="col">
                      {{$campaign->trackings()->sum('opens')}}
                      <small class="text-muted"><br>Opened</small>
                    </div>
                    <div class="col">
                    {{$campaign->trackings()->sum('clicks')}}
                      <small class="text-muted"><br>Clicks</small>
                    </div>
                    <div class="col">
                    {{$campaign->trackings()->sum('unsubscribe')}}
                      <small class="text-muted"><br>Unsubscribed</small>
                    </div>
                  </div>
                  <div class="buttons pt-3">
                    @if($status == 'running')
                      <form id="campaign-form-{{$campaign->id}}" action="{{route('campaign.stop', $campaign->id)}}" method="post" style="display:none">
                          @csrf
                          @method('put')
                          <input type="text" name="batch_id" value="{{$campaign->batch_id}}">
                      </form>
                      <button type="submit" form="campaign-form-{{$campaign->id}}" class="btn btn-sm btn-danger"><i class="far fa-stop-circle"></i> Stop</button>
                    @endif
                    @if($status != 'running')<a href="{{route('campaign.edit', $campaign->id)}}" class="btn btn-sm btn-success"><i class="fas fa-edit"></i> Edit</a>@endif
                    @if($status == 'completed' || $status == 'cancelled' || $status == 'failed')<a href="{{route('campaign.trash', $campaign->id)}}" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Delete</a>@endif
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>
      <div class="pt-3 paginations">
      {{ $campaigns->links('vendor.pagination.bootstrap-4') }}
      </div>
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content -->
</div>
<script>
  setTimeout(function(){
    window.location.reload();
  }, 60000);
</script>
@endsection