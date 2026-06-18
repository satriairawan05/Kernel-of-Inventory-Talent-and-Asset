@extends('admin.layouts.app')

@push('css')
    <style>
        .profile-shell {
            padding-top: 1rem;
        }

        .profile-hero {
            border-radius: 28px;
            padding: 24px;
            background: linear-gradient(135deg, #111827 0%, #2563eb 45%, #38bdf8 100%);
            color: #fff;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.18);
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: #2563eb;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .soft-card {
            border-radius: 24px;
            border: 1px solid rgba(148, 163, 184, .18);
            box-shadow: 0 18px 40px rgba(15, 23, 42, .08);
            overflow: hidden;
        }

        .soft-card .card-header {
            border-bottom: 1px solid rgba(148, 163, 184, .18);
            background: linear-gradient(180deg, #fff 0%, #f8fbff 100%);
        }

        .form-label {
            font-weight: 600;
            color: #1e293b;
        }

        .form-control,
        .form-select {
            border-radius: 14px;
            border-color: #cbd5e1;
            padding: .6rem 1rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, .15);
        }

        .btn-primary {
            background: #2563eb;
            border: none;
            border-radius: 40px;
            padding: 0.5rem 1.5rem;
            font-weight: 500;
        }

        hr {
            opacity: 0.3;
        }

        .avatar-preview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            background: #f1f5f9;
        }

        .password-toggle-wrapper {
            position: relative;
        }

        .password-toggle-wrapper input {
            padding-right: 35px;
        }

        .toggle-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            z-index: 10;
        }

        .password-toggle-icon:hover {
            color: #2563eb;
        }
    </style>
@endpush

@push('js')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    {{-- <script src="{{ asset('assets/js/jquery-3.6.4.min.js') }}"></script> --}}
    <script type="text/javascript">
        $(document).ready(function() {
            function togglePassword(inputId, iconId) {
                var input = $('#' + inputId);
                $(document).off('click', '#' + iconId).on('click', '#' + iconId, function(e) {
                    e.preventDefault();

                    var icon = $(this);

                    if (input.prop('type') === 'password') {
                        input.prop('type', 'text');
                        icon.removeClass('fa-eye-slash').addClass('fa-eye');
                    } else {
                        input.prop('type', 'password');
                        icon.removeClass('fa-eye').addClass('fa-eye-slash');
                    }
                });
            }

            togglePassword('current_password', 'toggle_current_password');
            togglePassword('new_password', 'toggle_new_password');
            togglePassword('confirm_password', 'toggle_confirm_password');

            // Avatar preview
            $('#avatar_input').on('change', function(e) {
                var file = e.target.files[0];
                var preview = $('#avatar_preview');
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(event) {
                        preview.attr('src', event.target.result);
                    };
                    reader.readAsDataURL(file);
                } else {
                    var defaultSrc = preview.data('default');
                    if (defaultSrc) preview.attr('src', defaultSrc);
                }
            });
        });
    </script>
@endpush

@section('content')
    @if ($access['Profile']['Read'] == 1)
        <div class="container-fluid py-4 profile-shell">
            <!-- Hero Section -->
            <div class="profile-hero d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-4 flex-wrap">
                    <div class="profile-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-1">My Profile</h2>
                        <p class="mb-0 opacity-75">Kelola informasi akun dan keamanan Anda</p>
                    </div>
                </div>
                <div class="text-light-emphasis bg-white bg-opacity-10 px-3 py-2 rounded-pill">
                    <i class="far fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </div>
            </div>

            <div class="row justify-content-center mt-4">
                <div class="col-lg-10">
                    @if ($access['Profile']['Update'] == 1)
                        <!-- Update Profile Card -->
                        <div class="card soft-card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-user-edit me-2 text-primary"></i> Edit Profile</h5>
                            </div>
                            <div class="card-body">
                                @if (session('profile_success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('profile_success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif
                                @if ($errors->profile->any() && !$errors->has('current_password'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                <form action="{{ route('setting.profile.update', $profile->id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="row g-3">
                                        <!-- Avatar Upload -->
                                        <div class="col-12 text-center mb-3">
                                            <label class="form-label d-block">Profile Avatar</label>
                                            <div class="d-flex justify-content-center">
                                                <img id="avatar_preview" class="avatar-preview"
                                                    src="{{ $profile->avatar ? asset('storage/' . $profile->avatar) : asset('assets/img/avatar/default-avatar.png') }}"
                                                    alt="Avatar"
                                                    data-default="{{ $profile->avatar ? asset('storage/' . $profile->avatar) : asset('assets/img/avatar/default-avatar.png') }}">
                                            </div>
                                            <input type="file" name="avatar" id="avatar_input" class="form-control mt-3"
                                                accept="image/jpeg,image/png,image/jpg,image/webp">
                                            <small class="text-muted">Format: JPG, PNG, WEBP (Max 2MB)</small>
                                            @error('avatar')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Full Name</label>
                                            <input type="text" name="name" value="{{ old('name', $profile->name) }}"
                                                class="form-control @error('name') is-invalid @enderror" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Email Address</label>
                                            <input type="email" name="email" value="{{ old('email', $profile->email) }}"
                                                class="form-control @error('email') is-invalid @enderror" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <hr class="my-4">
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary px-4">
                                            <i class="fas fa-save me-2"></i> Update Profile
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Update Role Card -->
                        <div class="card soft-card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-users me-2 text-primary"></i> Change Role</h5>
                            </div>
                            <div class="card-body">
                                @if (session('group_success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('group_success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif
                                @if ($errors->group->any())
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <ul class="mb-0">
                                            @foreach ($errors->group->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                <form action="{{ route('setting.profile.group', $profile->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <label class="form-label">User Role / Group</label>
                                            <select name="group_id"
                                                class="form-select select2 @error('group_id', 'group') is-invalid @enderror">
                                                <option value="">Select Role</option>
                                                @foreach ($groups as $group)
                                                    <option value="{{ $group->id }}"
                                                        {{ old('group_id', $profile->group_id) == $group->id ? 'selected' : '' }}>
                                                        {{ $group->group_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('group_id', 'group')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <hr class="my-4">
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary px-4">
                                            <i class="fas fa-save me-2"></i> Update Role
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Update Outlet Card -->
                        <div class="card soft-card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-building me-2 text-primary"></i> Change Oulet</h5>
                            </div>
                            <div class="card-body">
                                @if (session('company_success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('company_success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif
                                @if ($errors->company->any())
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <ul class="mb-0">
                                            @foreach ($errors->company->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                <form action="{{ route('setting.profile.company', $profile->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <label class="form-label">Outlet</label>
                                            <select name="company_id"
                                                class="form-select select2 @error('company_id', 'company') is-invalid @enderror">
                                                <option value="">Select Outlet</option>
                                                @foreach ($companies as $company)
                                                    <option value="{{ $company->id }}"
                                                        {{ old('company_id', $profile->company_id) == $company->id ? 'selected' : '' }}>
                                                        {{ $company->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('company_id', 'company')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <hr class="my-4">
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary px-4">
                                            <i class="fas fa-save me-2"></i> Update Outlet
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Change Password Card -->
                        <div class="card soft-card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-lock me-2 text-primary"></i> Change Password</h5>
                            </div>
                            <div class="card-body">
                                @if (session('password_success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('password_success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif
                                @if (
                                    $errors->password->has('current_password') ||
                                        $errors->password->has('password') ||
                                        $errors->password->has('password_confirmation'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                <form action="{{ route('setting.profile.update', $profile->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Current Password</label>
                                            <div class="password-toggle-wrapper" id="wrapper-current">
                                                <input type="password" placeholder="********" name="current_password"
                                                    id="current_password"
                                                    class="form-control @error('current_password') is-invalid @enderror"
                                                    required>
                                                <i class="fas fa-eye-slash" id="toggle_current_password"
                                                    style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"></i>
                                            </div>
                                            @error('current_password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">New Password</label>
                                            <div class="password-toggle-wrapper" id="wrapper-new">
                                                <input type="password" placeholder="********" name="password"
                                                    id="new_password"
                                                    class="form-control @error('password') is-invalid @enderror" required>
                                                <i class="fas fa-eye-slash" id="toggle_new_password"
                                                    style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"></i>
                                            </div>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Confirm New Password</label>
                                            <div class="password-toggle-wrapper" id="wrapper-confirm">
                                                <input type="password" placeholder="********"
                                                    name="password_confirmation" id="confirm_password"
                                                    class="form-control" required>
                                                <i class="fas fa-eye-slash" id="toggle_confirm_password"
                                                    style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="my-4">
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary px-4">
                                            <i class="fas fa-key me-2"></i> Update Password
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
@endsection
