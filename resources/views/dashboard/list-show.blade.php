@extends('dashboard.master')
@section('content')
<style>
  span.subscribed {
    background: lightgreen;
    padding: 3px 8px;
    border-radius: 10px;
    font-size: 14px;
  }
  span.unsubscribed {
    background: yellow;
    padding: 3px 8px;
    border-radius: 10px;
    font-size: 14px;
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
          <h1 class="m-0">{{$list->name}}</h1>
          <a href="{{route('contact.create')}}" class="btn btn-primary mt-3">New Contact</a>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Lists</a></li>
            <li class="breadcrumb-item active">{{$list->name}}</li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
    <table class="table">
  <thead>
    <tr>
      <th scope="col">SI</th>
      <th scope="col">Name</th>
      <th scope="col">Email</th>
      <th scope="col">Phone</th>
      <th scope="col">Country</th>
      <th scope="col">Status</th>
      <th scope="col">Action</th>
    </tr>
  </thead>
  <tbody>
    @foreach($contacts as $key => $contact)
    <tr>
      <td>{{($key+1)*$contacts->currentPage()}}</td>
      <td>{{$contact->full_name}}</td>
      <td>{{$contact->email}}</td>
      <td>{{$contact->phone}}</td>
      <td>{{ucwords($contact->country)}}</td>
      <td><span class="{{$contact->status != 'subscribed' ? 'un' : '' }}subscribed">{{ucwords($contact->status)}}</span></td>
      <td>
        <a class="btn btn-info" href="{{route('contact.show', $contact->id)}}">View</a>
        <a class="btn btn-warning" href="{{route('contact.edit', $contact->id)}}">Edit</a>
        <form id="delete-frm-{{ $key }}" action="{{route('contact.destroy', $contact->id)}}" method="post" style="display:none">
          @csrf
          @method('DELETE')
        </form>
        <button class="btn btn-danger" type="submit" form="delete-frm-{{ $key }}" onclick="confirm('Are you sure to delete this contact?');">Delete</button>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
<div class="pt-3 paginations">
{{ $contacts->links('vendor.pagination.bootstrap-4') }}
</div>
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content -->
</div>
@endsection