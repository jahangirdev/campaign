@php
$currentRoute = Route::currentRouteName();
@endphp
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
      <img src="https://healthbox.store/wp-content/uploads/2020/04/Logo-Neutral-favicon-512x512-1.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">Campaign</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{asset('backend/dist/img/user.png')}}" class="img-circle elevation-2" alt="{{auth()->user()->name}}">
        </div>
        <div class="info">
          <a href="#" class="d-block">{{auth()->user()->name}}</a>
        </div>
      </div>

      <!-- SidebarSearch Form -->
      {{-- <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>
      --}}

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item menu-open">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-address-book"></i>
              <p>
                Contacts
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{route('list.index')}}" class="nav-link {{ $currentRoute == 'list.index' ? 'active' : '' }}">  
                  <i class="far fa-circle nav-icon"></i>
                  <p>All List</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('list.new')}}" class="nav-link {{ $currentRoute == 'list.new' ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Add New List</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item menu-open">
            <a href="#" class="nav-link">
            <i class="nav-icon fa fa-paint-brush"></i>
              <p>
                Templates
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{route('template.index')}}" class="nav-link {{ $currentRoute == 'template.index' ? 'active' : '' }}">  
                  <i class="far fa-circle nav-icon"></i>
                  <p>All Template</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('template.create')}}" class="nav-link {{ $currentRoute == 'template.create' ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Add New Template</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item menu-open">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-paper-plane"></i>
              <p>
                Campaigns
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{route('campaign.index')}}" class="nav-link {{ $currentRoute == 'campaign.index' ? 'active' : '' }}">  
                  <i class="far fa-circle nav-icon"></i>
                  <p>All Campaign</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('campaign.create')}}" class="nav-link {{ $currentRoute == 'campaign.create' ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Add New Campaign</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <form method="POST" action="{{ route('logout') }}">
              @csrf

              <a href="route('logout')" class="btn btn-danger d-block mt-3"
                      onclick="event.preventDefault();
                                  this.closest('form').submit();">
                  {{ __('Log Out') }}
              </a>
            </form>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
  <script>
    document.addEventListener("DOMContentLoaded", ()=> {
        const activeItem = document.querySelector(".nav-link.active");
        if(activeItem != null){
            activeItem.parentElement.parentElement.parentElement.querySelector('a').classList.add("active");
        }
    });
</script>