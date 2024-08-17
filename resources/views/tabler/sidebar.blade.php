  <!-- Sidebar -->
  <aside class="navbar navbar-vertical navbar-expand-sm navbar-dark">
    <div class="container-fluid">
      <button class="navbar-toggler" type="button">
        <span class="navbar-toggler-icon"></span>
      </button>
      <h1 class="navbar-brand navbar-brand-autodark">
        <div class="shrink-0 flex items-center">
          <a href="{{ route('dashboard') }}">
            <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
          </a>
        </div>
      </h1>
      <div class="navbar-collapse" id="sidebar-menu">
        <ul class="navbar-nav pt-lg-3">
          <li class="nav-item">
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
              {{ __('Dashboard') }}
            </x-nav-link>
          </li>
          <li class="nav-item">
            <x-nav-link :href="route('reporting')" :active="request()->routeIs('reporting')">
                {{ __('Reporting') }}
            </x-nav-link>
          </li>
{{--            @dd(auth()->user()->role);--}}
          @if(auth()->user()->role == 'admin' || auth()->user()->role == 'manager')
          <li class="nav-item">
            <x-nav-link :href="route('users')" :active="request()->routeIs('users')">
                {{ __('Users') }}
            </x-nav-link>
          </li>
            <li class="nav-item">
                <x-nav-link :href="route('projects')" :active="request()->routeIs('projects')">
                    {{ __('Projects') }}
                </x-nav-link>
          </li>
          @endif
        </ul>
      </div>
    </div>
  </aside>
