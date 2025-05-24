<nav class="navbar navbar-expand-lg">
    <!-- Navbar Left -->
    <div class="navbar-left">
        <!-- Mobile Sidebar Toggle -->
        <button class="navbar-toggle" type="button">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Page Title -->
    <div class="d-flex justify-content-center flex-grow-1">
        <p class="h1 text-center navbar-title m-2">{{ $header ?? config('app.name') }}</p>
    </div>

    <!-- Navbar Right -->
    <div class="navbar-right">
        <!-- Search -->
        {{-- <div class="navbar-search">
            <i class="fas fa-search navbar-search-icon"></i>
            <input type="text" class="navbar-search-input" placeholder="Search...">
        </div> --}}

        <!-- Actions -->
        <div class="navbar-actions">
            <!-- Notifications -->
            <div class="navbar-dropdown">
                <button class="navbar-action" data-toggle="dropdown">
                    <i class="fas fa-bell"></i>
                    <span class="navbar-action-badge">3</span>
                </button>

                <div class="navbar-dropdown-menu">
                    <div class="navbar-dropdown-header">
                        <h6 class="navbar-dropdown-title">Notifications (comming soon) </h6>
                    </div>

                    <a href="#" class="navbar-dropdown-item">
                        <div class="navbar-dropdown-icon">
                            <i class="fas fa-check-circle text-success"></i>
                        </div>
                        <div class="navbar-dropdown-text">New report submitted</div>
                    </a>

                    <a href="#" class="navbar-dropdown-item">
                        <div class="navbar-dropdown-icon">
                            <i class="fas fa-exclamation-circle text-warning"></i>
                        </div>
                        <div class="navbar-dropdown-text">Project updated</div>
                    </a>

                    <a href="#" class="navbar-dropdown-item">
                        <div class="navbar-dropdown-icon">
                            <i class="fas fa-info-circle text-info"></i>
                        </div>
                        <div class="navbar-dropdown-text">System update completed</div>
                    </a>

                    <div class="navbar-dropdown-footer">
                        <a href="#" class="navbar-dropdown-item">
                            <div class="navbar-dropdown-text text-center">View all notifications</div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Settings -->
            {{-- <div class="navbar-dropdown">
                <button class="navbar-action" data-toggle="dropdown">
                    <i class="fas fa-cog"></i>
                </button>

                <div class="navbar-dropdown-menu">
                    <div class="navbar-dropdown-header">
                        <h6 class="navbar-dropdown-title">Settings</h6>
                    </div>

                    <a href="{{ route('profile.edit') }}" class="navbar-dropdown-item">
                        <div class="navbar-dropdown-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="navbar-dropdown-text">Profile</div>
                    </a>

                    <a href="#" class="navbar-dropdown-item">
                        <div class="navbar-dropdown-icon">
                            <i class="fas fa-palette"></i>
                        </div>
                        <div class="navbar-dropdown-text">Appearance</div>
                    </a>

                    <div class="navbar-dropdown-footer">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}" class="navbar-dropdown-item"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                <div class="navbar-dropdown-icon">
                                    <i class="fas fa-sign-out-alt"></i>
                                </div>
                                <div class="navbar-dropdown-text">{{ __('Log Out') }}</div>
                            </a>
                        </form>
                    </div>
                </div>
            </div> --}}
        </div>

        <!-- User -->
        <div class="navbar-dropdown">
            <div class="navbar-user" data-toggle="dropdown">
                <div class="navbar-user-avatar">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="navbar-user-info">
                    <h6 class="navbar-user-name">{{ Auth::user()->name }}</h6>
                    <p class="navbar-user-role">{{ ucfirst(Auth::user()->role ?? 'User') }}</p>
                </div>
            </div>

            <div class="navbar-dropdown-menu">
                <div class="navbar-dropdown-header">
                    <h6 class="navbar-dropdown-title">User Menu</h6>
                </div>

                {{-- <a href="{{ route('profile.edit') }}" class="navbar-dropdown-item">
                    <div class="navbar-dropdown-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="navbar-dropdown-text">Profile</div>
                </a>

                <a href="#" class="navbar-dropdown-item">
                    <div class="navbar-dropdown-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="navbar-dropdown-text">My Reports</div>
                </a> --}}

                <div class="navbar-dropdown-footer">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}" class="navbar-dropdown-item"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            <div class="navbar-dropdown-icon">
                                <i class="fas fa-sign-out-alt"></i>
                            </div>
                            <div class="navbar-dropdown-text">{{ __('Log Out') }}</div>
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>
