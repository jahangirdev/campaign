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
            <div class="form-group">
              <!-- Button trigger modal -->
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter">
                Show Variables
              </button>
            </div>
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

  <!-- Modal start -->
      <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLongTitle">Variables that can be used in email templates</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <style>
                .var-table tr td:first-child{
                  min-width: 250px;
                }
                .var-table tr td input{
                  text-align: center;
                }
              </style>
              <table class="table var-table">
                <tr>
                  <td><input type="text" class="form-control variable" value="@{{name}}" readonly></td>
                  <td>The name of the contact</td>
                </tr>
                <tr>
                  <td><input type="text" class="form-control variable" value="@{{recommended_packs}}" readonly></td>
                  <td>Recommended packs name (Quiz takers only)</td>
                </tr>
                <tr>
                  <td><input type="text" class="form-control variable" value="@{{recommended_comps}}" readonly></td>
                  <td>Recommended complementaries name (Quiz takers only)</td>
                </tr>
                <tr>
                  <td><input type="text" class="form-control variable" value="@{{cart_url}}" readonly></td>
                  <td>Add to Cart url for recommended packs (Quiz takers only)</td>
                </tr>
                <tr>
                  <td><input type="text" class="form-control variable" value="@{{cart_url}}%26apply_coupon=REPLACE_COUPON_CODE" readonly></td>
                  <td>Add to Cart url with coupon code for recommended packs (Quiz takers only)</td>
                </tr>
                <tr>
                  <td><input type="text" class="form-control variable" value="@{{subject}}" readonly></td>
                  <td>Campaign Subject (Can be used between <code>&lt;title&gt;</code> and <code>&lt;/title&gt;</code> or in other place)</td>
                </tr>
                <tr>
                  <td><input type="text" class="form-control variable" value="@{{unsubscribe}}" readonly></td>
                  <td>Unsubscribe url (Can be used in <code>href</code> attribute of a <code>&lt;a&gt;</code> tag)</td>
                </tr>
                
              </table>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>
  <!--Modal end -->
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

document.addEventListener("DOMContentLoaded", () => {
  const varFields = document.querySelectorAll(".variable");

  varFields.forEach((field) => {
    field.addEventListener("click", () => {
      navigator.clipboard.writeText(field.value)
      .then(function() {
            alert('Variable '+field.value+' copied to the clipboard!');
        })
    });
  })
});
</script>
@endsection