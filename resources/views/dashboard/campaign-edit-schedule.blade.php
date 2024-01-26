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
              <label for="campaignType">Campaign Run</label>
              <select name="type" class="form-control" id="campaignType">
                <option value="once">Once</option>
                <option value="repeat" {{$campaign->type == 'repeat' ? 'selected':''}}>Repeat</option>
              </select>
            </div>
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
            <div class="form-group mt-3" @if($campaign->type != 'repeat')style="display:none"@endif>
              <label for="contactList">Repeat</label>
              <select class="form-control" name="repeat" id="contactList">
                  <option value="everyday" {{$campaign->repeat == "everyday" ? 'selected' : ''}}>Everyday</option>
                  <option value="everyweek" {{$campaign->repeat == "everyweek" ? 'selected' : ''}}>Every Week</option>
                  <option value="every15days" {{$campaign->repeat == "every15days" ? 'selected' : ''}}>Every 15 days</option>
                  <option value="everymonth" {{$campaign->repeat == "everymonth" ? 'selected' : ''}}>Every Month</option>
                  <option value="every3months" {{$campaign->repeat == "every3months" ? 'selected' : ''}}>Every 3 Months</option>
                  <option value="every6months" {{$campaign->repeat == "every6months" ? 'selected' : ''}}>Every 6 Months</option>
                  <option value="everyyear" {{$campaign->repeat == "everyyear" ? 'selected' : ''}}>Every Year</option>
              </select>
            </div>
            <div class="form-group mt-3" @if($campaign->type=='once')style="display:none"@endif>
              <label for="campaignStopAt">Stop At</label>
              <input name="stop_at" type="datetime-local" class="form-control" id="campaignStopAt" value="{{ !empty($campaign->stop_at && $campaign->type == 'repeat') ? DateTime::createFromFormat('Y-m-d H:i:s', $campaign->stop_at)->format('Y-m-d\TH:i') : ''}}">
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
        form.type.addEventListener("change", () => {
            if(form.type.value == "repeat"){
                form.repeat.parentElement.style.display = "block";
                form.stop_at.parentElement.style.display = "block";
            }
            else{
                form.repeat.parentElement.style.display = "none";
                form.stop_at.parentElement.style.display = "none";
            }
        });

        form.run_at.addEventListener("change", () => {
            if(form.run_at.value == "schedule"){
                form.schedule.parentElement.style.display = "block";
            }
            else{
                form.schedule.parentElement.style.display = "none";
            }
        });
        
        form.schedule.addEventListener("change", () => {
            let date = new Date(form.schedule.value);
            let dayIndex = date.getDay();
            let day = daysOfWeek[dayIndex];
            let time = date.getHours()+':'+date.getMinutes();
            form.repeat.querySelectorAll('option').forEach(option => {
                option.innerText += ` at ${time}`;
            });
            form.repeat.querySelector('option[value="everyweek"]').innerText = `Every Week (${day}) at ${time}`;
        });
    });
</script>
@endsection