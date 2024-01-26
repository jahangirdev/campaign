@extends('dashboard.master')
@section('content')
<style>
  #codeEditor { width: 100%; height: 600px; color: #ffffff;}
  code.iblize_code {color: #fff;}
  #templatePreview{
    height: 100%;
    width: 100%;
  }
</style>
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Add New Template</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Add New Template</li>
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
          <h3>Create New Template</h3>
        </div>
        <div class="card-body">
          <form method="POST" action="{{route('template.store')}}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
              <label for="templateName">Template Name</label>
              <input name="name" class="form-control" id="templateName">
            </div>
            <textarea style="display:none" name="code" id="codeField" class="form-control"></textarea>
            <div class="row py-5">
              <div class="col-md-6">
                <div id="codeEditor"></div>
              </div>
              <div class="col-md-6">
                <iframe src="" frameborder="0" id="templatePreview">

                </iframe>
              </div>
            </div>
            <div class="form-group">
              <label for="screenshot">Screenshot</label>
              <input type="file" name="screenshot" class="form-control-file" id="screenshot">
            </div>
            <div class="form-group">
              <label for="country">Status</label>
              <select name="status" class="form-control">
                <option value="draft">Draft</option>
                <option value="publish">Publish</option>
              </select>
            </div>
            <button type="submit" class="btn btn-primary">Create</button>
          </form>
        </div>
      </div>

    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content -->
</div>
<script src="https://cdn.jsdelivr.net/npm/iblize/dist/iblize.min.js"></script>
<script>
  const iblize = new Iblize("#codeEditor", {
    language: "html",
    theme: "okaidia"
  });
  const codeField = document.getElementById("codeField");
  iblize.onUpdate((value) => {
    codeField.value = value;
    previewTemplate(value);
  });

  function previewTemplate(code){
    let doc = document.getElementById('templatePreview').contentWindow.document;
    doc.open();
    doc.write(code);
    doc.close();
}
</script>
@endsection