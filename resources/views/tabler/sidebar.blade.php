@php use Illuminate\Support\Facades\Gate; @endphp
<aside class="sidebar">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <div class="sidebar-logo">
            @if(auth()->user()->role == 'admin')
                <a href="{{ route('dashboard') }}" class="sidebar-logo">
            @else
                <a href="{{ route('reporting') }}" class="sidebar-logo">
            @endif
                <img src="{{ asset('images/logo.svg') }}" alt="Logo">
                <span class="sidebar-logo-text">{{ config('app.name', 'Reporter') }}</span>
            </a>
        </div>

        <button class="sidebar-toggle" type="button">
            <i class="fas fa-chevron-left"></i>
        </button>
    </div>

    <!-- Sidebar Navigation -->
    <div class="sidebar-nav">
        @can('is-admin')
            <div class="sidebar-nav-item">
                <a href="{{ route('dashboard') }}" class="sidebar-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <div class="sidebar-nav-icon">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                    <span class="sidebar-nav-text">{{ __('Dashboard') }}</span>
                </a>
            </div>
        @endcan

        <div class="sidebar-nav-item">
            <a href="{{ route('reporting') }}" class="sidebar-nav-link {{ request()->routeIs('reporting') ? 'active' : '' }}">
                <div class="sidebar-nav-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <span class="sidebar-nav-text">{{ __('Reporting') }}</span>
            </a>
        </div>

        @can('is-admin')
            <div class="sidebar-nav-item">
                <a href="{{ route('users') }}" class="sidebar-nav-link {{ request()->routeIs('users') ? 'active' : '' }}">
                    <div class="sidebar-nav-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <span class="sidebar-nav-text">{{ __('Users') }}</span>
                </a>
            </div>

            <div class="sidebar-nav-item">
                <a href="{{ route('projects') }}" class="sidebar-nav-link {{ request()->routeIs('projects') ? 'active' : '' }}">
                    <div class="sidebar-nav-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <span class="sidebar-nav-text">{{ __('Projects') }}</span>
                </a>
            </div>
        @endcan
    </div>

    <!-- Sidebar User -->
    <div class="sidebar-user">
        <div class="sidebar-user-avatar">
            {{ substr(Auth::user()->name, 0, 1) }}
        </div>
        <div class="sidebar-user-info">
            <h6 class="sidebar-user-name">{{ Auth::user()->name }}</h6>
            <p class="sidebar-user-role">{{ ucfirst(Auth::user()->role ?? 'User') }}</p>
        </div>
    </div>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}</p>
    </div>
</aside>
