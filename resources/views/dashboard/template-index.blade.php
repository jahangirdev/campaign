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
          <h1 class="m-0">Templates</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Templates</li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">

      <div class="row">
        @foreach($templates as $key => $template)
        <div class="col-md-4">
          <div class="card">
            <img src="{{asset('')}}{{$template->screenshot ? : 'backend/uploads/template-placeholder.jpg'}}" class="card-img-top img-fluid" alt="{{$template->name}}">
            <div class="card-body">
              <h5 class="text-center mb-4">{{$template->name}}
              @if($template->after_quiz == 1)
                (After Quiz)
              @endif
              </h5>
              <div class="d-flex justify-content-between mt-4">
                <a href="{{route('template.preview', $template->id)}}" class="btn btn-primary"><i class="fa fa-eye"></i></a>
                @if($template->after_quiz != 1)
                  <a href="{{route('template.quiz', $template->id)}}" class="btn btn-primary">Set as After Quiz</a>
                @endif
                <a href="{{route('template.edit', $template->id)}}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                <form style="display:none" id="delete-temp-{{$key}}" action="{{route('template.destroy', $template->id)}}" method="post">
                  @csrf
                  @method('DELETE')
                </form>
                <button type="submit" form="delete-temp-{{$key}}" class="btn btn-danger" onclick="confirm('Are you sure to delete the template?')"><i class="fa fa-trash"></i></button>
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>
      <div class="pt-3 paginations">
      {{ $templates->links('vendor.pagination.bootstrap-4') }}
      </div>
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content -->
</div>
@endsection