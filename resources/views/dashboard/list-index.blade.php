@extends('dashboard.master')
@section('content')
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
          <h1 class="m-0">Lists</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Lists</li>
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
      <th scope="col">Contacts</th>
      <th scope="col">Action</th>
    </tr>
  </thead>
  <tbody>
    @foreach($lists as $key => $list)
    <tr>
      <td>{{$key+1}}</td>
      <td>{{$list->name}}</td>
      <td>{{$numOfList($list->id)}}</td>
      <td>
        <a class="btn btn-info" href="{{route('list.view', $list->id)}}">View</a>
        <a class="btn btn-warning" href="{{route('list.edit', $list->id)}}">Edit</a>
        @if($list->quiz_taker != 1)<a class="btn btn-success" href="{{route('list.quiz', $list->id)}}">Set as Quiz List</a>@endif
        <form id="delete-frm-{{ $key }}" action="{{route('list.delete', $list->id)}}" method="post" style="display:none">
          @csrf
          @method('DELETE')
        </form>
        <button class="btn btn-danger" type="submit" form="delete-frm-{{ $key }}" onclick="confirm('Are you sure to delete this list?');">Delete</button>
        @if($list->quiz_taker == 1)<span class="ml-2"><i class="fa fa-check"></i> Quiz List</span>@endif
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
<div class="pt-3 paginations">
{{ $lists->links('vendor.pagination.bootstrap-4') }}
</div>
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content -->
</div>
@endsection