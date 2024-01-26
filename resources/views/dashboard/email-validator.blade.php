@extends('dashboard.master')
@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Email Validator</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Email Validator</li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
      <div class="card">
        <div class="card-header">
          <h3>Email Validator</h3>
        </div>
        <div class="card-body">
          <form method="POST" action="{{route('email.verify')}}">
            @csrf
            <div class="form-group">
              <label for="address">Emails</label>
              <textarea rows="10" name="emails" class="form-control" id="address" placeholder="Enter emails, one email per line."></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Verify</button>
          </form>
        </div>
      </div>
      @if(isset($validEmails))
      <section class="valid-results">
        <h2>Valid Emails</h2>

        <table class="table">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($validEmails as $email)
                    <tr>
                        <td>{{$email['email']}}</td>
                        <td>{{$email['status'] ? 'Valid' : 'Invalid'}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
      </section>
      @endif

      @if(isset($invalidEmails))
      <section class="valid-results">
        <h2>Valid Emails</h2>

        <table class="table">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Reason</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invalidEmails as $email)
                    <tr>
                        <td>{{$email['email']}}</td>
                        <td>{{$email['status'] ? 'Valid' : 'Invalid'}}</td>
                        <td>{{$email['message']}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
      </section>
      @endif


    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content -->
</div>
@endsection