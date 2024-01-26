<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  </head>
  <body>
    <div class="row justify-content-center align-items-center" style="height:99vh">
      <div class="col-4">
        <h2 class="text-center">Unsubscribe</h2>
        @if ($errors->any())
          <div class="alert alert-danger">
              <ul>
                  @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                  @endforeach
              </ul>
          </div>
        @endif
        <div class="card">
          <div class="card-body">
            <form action="{{route('track.unsubscribe.submit')}}" method="post">
              <div class="form-group">
                @csrf
                <input type="hidden" name="coid" value="{{$details['coid']}}">
                <input type="hidden" name="caid" value="{{$details['caid']}}">
                <input type="hidden" name="batch" value="{{$details['batch']}}">
                <label for="validationServer01" class="form-label">Confirm email:</label>
                <input type="email" name="email" class="form-control" id="validationServer01" placeholder="Enter your email" required>
              </div>
              <div class="d-grid mt-3">
                <button type="submit" class="btn btn-warning">Unsubscribe</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  </body>
</html>