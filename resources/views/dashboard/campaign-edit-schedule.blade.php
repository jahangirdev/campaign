@extends('dashboard.master')
@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Edit Campaign</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Campaign</a></li>
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
          <form id="scheduleForm" method="POST" action="{{route('campaign.update', $campaign->id)}}">
            @csrf
            <div class="form-group mt-3">
              <label for="campaignRunAt">Run At</label>
              <select name="run_at" class="form-control" id="campaignRunAt">
                <option value="instant">Instant</option>
                <option value="schedule" {{$campaign->run_at == 'schedule' ? 'selected' : ''}}>Schedule</option>
              </select>
            </div>
            <div class="form-group mt-3" @if($campaign->run_at == 'instant')style="display:none"@endif >
              <label for="campaignSchedule">Schedule</label>
              <input name="schedule" type="datetime-local" class="form-control" id="campaignSchedule" value="{{ !empty($campaign->schedule && $campaign->run_at != 'instant') ? DateTime::createFromFormat('Y-m-d H:i:s', $campaign->schedule)->format('Y-m-d\TH:i') : ''}}">
            </div>
            <div class="form-group mt-3">
              <label for="contactList">Status</label>
              <select class="form-control" name="status" id="contactList">
                  <option value="publish">Publish</option>
                  <option value="draft" {{$campaign->status == 'draft' ? 'selected' : ''}}>Draft</option>
              </select>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
          </form>
        </div>
      </div>


    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content -->
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const daysOfWeek = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
        const form = document.getElementById('scheduleForm');

        form.run_at.addEventListener("change", () => {
            if(form.run_at.value == "schedule"){
                form.schedule.parentElement.style.display = "block";
            }
            else{
                form.schedule.parentElement.style.display = "none";
            }
        });
    });
</script>
@endsection