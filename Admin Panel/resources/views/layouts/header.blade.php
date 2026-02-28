@php
    $user = Auth::user();
    $is_logged_in = Auth::check();
    $logout_route = route('logout');
    $profile_route = '#';
    
    if(Auth::guard('admin')->check()){
        $user = Auth::guard('admin')->user();
        $is_logged_in = true;
        $logout_route = route('admin.logout'); 
        $profile_route = route('admin.users.profile');
    } elseif(Auth::guard('expert')->check()){
        $user = Auth::guard('expert')->user();
        $is_logged_in = true;
        $logout_route = route('expert.logout'); // Assuming expert.logout exists
        $profile_route = route('expert.profile.show');
    } elseif(Auth::check()) {
        // Default customer or generic web guard
        if (Route::has('account.profile')) {
            $profile_route = route('account.profile');
        }
    }
@endphp

<div class="navbar-header">
    <a class="navbar-brand" href="<?php echo URL::to('/'); ?>">
        <b class="text-white h3 m-0" style="display: flex; align-items: center; justify-content: center;">
            <i class="mdi mdi-leaf mr-2 text-success"></i> <span style="font-weight: 700;">Plantix-AI</span>
        </b>
        <span>
        
        </span>
    </a>
</div>
<div class="navbar-collapse">
    <ul class="navbar-nav mr-auto mt-md-0">
        <li class="nav-item"><a class="nav-link nav-toggler hidden-md-up text-muted waves-effect waves-dark"
                                href="javascript:void(0)"><i class="mdi mdi-menu"></i></a></li>
        <li class="nav-item m-l-10"><a class="nav-link sidebartoggler hidden-sm-down text-muted waves-effect waves-dark"
                                       href="javascript:void(0)"><i class="ti-menu"></i></a></li>
    </ul>
    <div style="visibility: hidden;" class="language-list icon d-flex align-items-center text-light ml-2"
         id="language_dropdown_box">
        <div class="language-select">
            <i class="fa fa-globe"></i>
        </div>
        <div class="language-options">
            <select class="form-control changeLang text-dark" id="language_dropdown">

            </select>
        </div>
    </div>
    <ul class="navbar-nav my-lg-0">

        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark" href="" data-toggle="dropdown"
               aria-haspopup="true" aria-expanded="false"><img src="{{ asset('/images/users/user-new.png') }}"
                                                               alt="user" class="profile-pic"></a>
            <div class="dropdown-menu dropdown-menu-right scale-up">
                <ul class="dropdown-user">
                    <li>
                        <div class="dw-user-box">
                            <div class="u-img"><img src="{{ asset('/images/users/user-2.png') }}" alt="user"
                                                    style="max-width: 45px;"></div>
                            <div class="u-text">
                                @if($is_logged_in)
                                    <h4>{{ $user->name }}</h4>
                                    <p class="text-muted">{{ session()->has('user_role') ? session()->get('user_role') : '' }}</p>
                                @else
                                    <h4>Guest User</h4>
                                    <p class="text-muted">Not Logged In</p>
                                @endif
                            </div>
                        </div>
                    </li>
                    <li role="separator" class="divider"></li>
                    @if($is_logged_in)
                        <li><a href="{{ $profile_route }}"><i
                                        class="ti-user"></i> {!! trans('lang.user_profile') !!}</a></li>
                        <li role="separator" class="divider"></li>
                        
                        {{-- Generic Logout Form Submission --}}
                        <li><a href="#"
                               onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();"><i
                                        class="fa fa-power-off"></i> {{ __('Logout') }}</a></li>
                        <form id="logout-form" action="{{ $logout_route }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    @else
                        <li><a href="{{ url('signin') }}"><i class="ti-user"></i> Sign In</a></li>
                    @endif
                </ul>
            </div>
        </li>
    </ul>
</div>