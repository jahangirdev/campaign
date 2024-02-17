@extends('dashboard.master')
@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Add New Contact</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Add New Contact</li>
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
          <h3>Create New Contact</h3>
        </div>
        <div class="card-body">
          <form method="POST" action="{{route('contact.store')}}">
            @csrf
            <div class="row gx-4">
              <div class="col">
                <label for="firstName">First Name</label>
                <input name="first_name" type="text" class="form-control" id="firstName">
              </div>
              <div class="col">
                <label for="lastName">Last Name</label>
                <input name="last_name" type="text" class="form-control" id="lastName">
              </div>
            </div>
            <div class="row gx-4 mt-3">
              <div class="col">
                <label for="emailAddress">Email</label>
                <input name="email" type="email" class="form-control" id="emailAddress">
              </div>
              <div class="col">
                <label for="phoneNumber">Phone</label>
                <input name="phone" type="text" class="form-control" id="phoneNumber">
              </div>
            </div>
            <div class="form-group mt-3">
              <label for="country">Country</label>
              <select class="form-control" name="country" id="country">
                @php
                  $countries = [ 'afghanistan' => 'Afghanistan', 'albania' => 'Albania', 'algeria' => 'Algeria', 'andorra' => 'Andorra', 'angola' => 'Angola', 'antigua_and_barbuda' => 'Antigua and Barbuda', 'argentina' => 'Argentina', 'armenia' => 'Armenia', 'australia' => 'Australia', 'austria' => 'Austria', 'azerbaijan' => 'Azerbaijan', 'bahamas' => 'Bahamas', 'bahrain' => 'Bahrain', 'bangladesh' => 'Bangladesh', 'barbados' => 'Barbados', 'belarus' => 'Belarus', 'belgium' => 'Belgium', 'belize' => 'Belize', 'benin' => 'Benin', 'bhutan' => 'Bhutan', 'bolivia' => 'Bolivia', 'bosnia_and_herzegovina' => 'Bosnia and Herzegovina', 'botswana' => 'Botswana', 'brazil' => 'Brazil', 'brunei' => 'Brunei', 'bulgaria' => 'Bulgaria', 'burkina_faso' => 'Burkina Faso', 'burundi' => 'Burundi', 'cabo_verde' => 'Cabo Verde', 'cambodia' => 'Cambodia', 'cameroon' => 'Cameroon', 'canada' => 'Canada', 'central_african_republic' => 'Central African Republic', 'chad' => 'Chad', 'chile' => 'Chile', 'china' => 'China', 'colombia' => 'Colombia', 'comoros' => 'Comoros', 'congo' => 'Congo', 'costa_rica' => 'Costa Rica', 'croatia' => 'Croatia', 'cuba' => 'Cuba', 'cyprus' => 'Cyprus', 'czechia' => 'Czechia', 'denmark' => 'Denmark', 'djibouti' => 'Djibouti', 'dominica' => 'Dominica', 'dominican_republic' => 'Dominican Republic', 'ecuador' => 'Ecuador', 'egypt' => 'Egypt', 'el_salvador' => 'El Salvador', 'equatorial_guinea' => 'Equatorial Guinea', 'eritrea' => 'Eritrea', 'estonia' => 'Estonia', 'eswatini' => 'Eswatini', 'ethiopia' => 'Ethiopia', 'fiji' => 'Fiji', 'finland' => 'Finland', 'france' => 'France', 'gabon' => 'Gabon', 'gambia' => 'Gambia', 'georgia' => 'Georgia', 'germany' => 'Germany', 'ghana' => 'Ghana', 'greece' => 'Greece', 'grenada' => 'Grenada', 'guatemala' => 'Guatemala', 'guinea' => 'Guinea', 'guinea_bissau' => 'Guinea-Bissau', 'guyana' => 'Guyana', 'haiti' => 'Haiti', 'honduras' => 'Honduras', 'hungary' => 'Hungary', 'iceland' => 'Iceland', 'india' => 'India', 'indonesia' => 'Indonesia', 'iran' => 'Iran', 'iraq' => 'Iraq', 'ireland' => 'Ireland', 'israel' => 'Israel', 'italy' => 'Italy', 'jamaica' => 'Jamaica', 'japan' => 'Japan', 'jordan' => 'Jordan', 'kazakhstan' => 'Kazakhstan', 'kenya' => 'Kenya', 'kiribati' => 'Kiribati', 'korea_north' => 'Korea, North', 'korea_south' => 'Korea, South', 'kosovo' => 'Kosovo', 'kuwait' => 'Kuwait', 'kyrgyzstan' => 'Kyrgyzstan', 'laos' => 'Laos', 'latvia' => 'Latvia', 'lebanon' => 'Lebanon', 'lesotho' => 'Lesotho', 'liberia' => 'Liberia', 'libya' => 'Libya', 'liechtenstein' => 'Liechtenstein', 'lithuania' => 'Lithuania', 'luxembourg' => 'Luxembourg', 'madagascar' => 'Madagascar', 'malawi' => 'Malawi', 'malaysia' => 'Malaysia', 'maldives' => 'Maldives', 'mali' => 'Mali', 'malta' => 'Malta', 'marshall_islands' => 'Marshall Islands', 'mauritania' => 'Mauritania', 'mauritius' => 'Mauritius', 'mexico' => 'Mexico', 'micronesia' => 'Micronesia', 'moldova' => 'Moldova', 'monaco' => 'Monaco', 'mongolia' => 'Mongolia', 'montenegro' => 'Montenegro', 'morocco' => 'Morocco', 'mozambique' => 'Mozambique', 'myanmar' => 'Myanmar', 'namibia' => 'Namibia', 'nauru' => 'Nauru', 'nepal' => 'Nepal', 'netherlands' => 'Netherlands', 'new_zealand' => 'New Zealand', 'nicaragua' => 'Nicaragua', 'niger' => 'Niger', 'nigeria' => 'Nigeria', 'north_macedonia' => 'North Macedonia', 'norway' => 'Norway', 'oman' => 'Oman', 'pakistan' => 'Pakistan', 'palau' => 'Palau', 'panama' => 'Panama', 'papua_new_guinea' => 'Papua New Guinea', 'paraguay' => 'Paraguay', 'peru' => 'Peru', 'philippines' => 'Philippines', 'poland' => 'Poland', 'portugal' => 'Portugal', 'qatar' => 'Qatar', 'romania' => 'Romania', 'russia' => 'Russia', 'rwanda' => 'Rwanda', 'saint_kitts_and_nevis' => 'Saint Kitts and Nevis', 'saint_lucia' => 'Saint Lucia', 'saint_vincent_and_the_grenadines' => 'Saint Vincent and the Grenadines', 'samoa' => 'Samoa', 'san_marino' => 'San Marino', 'sao_tome_and_principe' => 'Sao Tome and Principe', 'saudi_arabia' => 'Saudi Arabia', 'senegal' => 'Senegal', 'serbia' => 'Serbia', 'seychelles' => 'Seychelles', 'sierra_leone' => 'Sierra Leone', 'singapore' => 'Singapore', 'slovakia' => 'Slovakia', 'slovenia' => 'Slovenia', 'solomon_islands' => 'Solomon Islands', 'somalia' => 'Somalia', 'south_africa' => 'South Africa', 'south_sudan' => 'South Sudan', 'spain' => 'Spain', 'sri_lanka' => 'Sri Lanka', 'sudan' => 'Sudan', 'suriname' => 'Suriname', 'sweden' => 'Sweden', 'switzerland' => 'Switzerland', 'syria' => 'Syria', 'taiwan' => 'Taiwan', 'tajikistan' => 'Tajikistan', 'tanzania' => 'Tanzania', 'thailand' => 'Thailand', 'timor_leste' => 'Timor-Leste', 'togo' => 'Togo', 'tonga' => 'Tonga', 'trinidad_and_tobago' => 'Trinidad and Tobago', 'tunisia' => 'Tunisia', 'turkey' => 'Turkey', 'turkmenistan' => 'Turkmenistan', 'tuvalu' => 'Tuvalu', 'uganda' => 'Uganda', 'ukraine' => 'Ukraine', 'united_arab_emirates' => 'United Arab Emirates', 'united_kingdom' => 'United Kingdom', 'united_states' => 'United States', 'uruguay' => 'Uruguay', 'uzbekistan' => 'Uzbekistan', 'vanuatu' => 'Vanuatu', 'vatican_city' => 'Vatican City', 'venezuela' => 'Venezuela', 'vietnam' => 'Vietnam', 'yemen' => 'Yemen', 'zambia' => 'Zambia', 'zimbabwe' => 'Zimbabwe'];
                @endphp
                @foreach($countries as $key => $country)
                  <option value="{{$key}}">{{$country}}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group">
              <label for="address">Address</label>
              <input name="address" class="form-control" id="address">
            </div>
            <div class="form-group">
              <label for="country">Assign to List</label>
              <select class="form-control" name="list_id" id="country">
                @foreach($lists as $list)
                  <option value="{{$list->id}}">{{$list->name}}</option>
                @endforeach
              </select>
            </div>
            <button type="submit" class="btn btn-primary">Create</button>
          </form>
        </div>
      </div>

      <!-- One email per line import -->

      <div class="card mt-5">
        <div class="card-header">
          <h3>CSV Import</h3>
        </div>
        <div class="card-body">
          <form method="POST" action="{{route('contact.import')}}">
            @csrf
            <div class="form-group">
              <label for="country">Assign to List</label>
              <select class="form-control" name="list_id" id="country">
                @foreach($lists as $list)
                  <option value="{{$list->id}}">{{$list->name}}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="address">Select CSV file</label>
              <input type="file" name="csv_file" class="form-control-file" id="address" placeholder="Upload CSV" accept=".csv">
            </div>
            <button type="submit" class="btn btn-primary">Check and Import</button>
          </form>
        </div>
      </div>
      @if(isset($alreadyExists) || isset($invalidEmails))
      <h2 class="mt-5">{{$alreadyExists}} already exists. {{count($invalidEmails)}} has issues</h2>
      @endif
      @if(isset($invalidEmails) && count($invalidEmails) > 0)
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
      @endif

      <!-- CSV import -->

    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content -->
</div>
@endsection