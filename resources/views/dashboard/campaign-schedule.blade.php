@extends('dashboard.master')
@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Create New Campaign</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="#">New Campaign</a></li>
            <li class="breadcrumb-item active">Campaign Schedule</li>
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
          <h3>Schedule</h3>
        </div>
        <div class="card-body">
          <form id="scheduleForm" method="POST" action="{{route('campaign.store')}}">
            @csrf
            <div class="d-none">
              <input name="name" type="text" class="form-control" id="campaignName" value="{{$details['name']}}">
              <input name="subject" type="text" class="form-control" id="subject" value="{{$details['subject']}}">
              <input name="from_name" type="text" class="form-control" id="fromName" value="{{$details['from_name']}}">
              <input name="from_email" type="text" class="form-control" id="fromEmail" value="{{$details['from_email']}}">
              <input name="lists" type="text" class="form-control" id="contactList" value="{{json_encode($details['lists'])}}">
              <input name="template" type="number" class="form-control" id="template" value="{{$details['template']}}">
            </div>
            <div class="form-group mt-3">
              <label for="campaignRunAt">Run At</label>
              <select name="run_at" class="form-control" id="campaignRunAt">
                <option value="instant">Instant</option>
                <option value="schedule">Schedule</option>
              </select>
            </div>
            <div class="form-group mt-3" style="display:none">
              <label for="campaignSchedule">Schedule</label>
              <input name="schedule" type="datetime-local" class="form-control" id="campaignSchedule">
            </div>
            <div class="form-group mt-3">
              <label for="contactList">Status</label>
              <select class="form-control" name="status" id="contactList">
                  <option value="draft">Draft</option>
                  <option value="publish">Publish</option>
              </select>
            </div>

            <button type="submit" class="btn btn-primary">Finish</button>
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