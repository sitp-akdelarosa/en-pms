@extends('layouts.auth')

@section('content')
<form action="{{ route('login') }}" method="post" class="form-element">
    @csrf

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    
    <div class="form-group has-feedback">
        <input type="text" class="form-control{{ $errors->has('user_id') ? ' is-invalid' : '' }}" name="user_id" placeholder="User ID" value="{{ old('user_id') }}" required autofocus>
        <span class="ion ion-person form-control-feedback"></span>
        @if ($errors->has('user_id'))
            <span class="invalid-feedback">
                <strong>{{ $errors->first('user_id') }}</strong>
            </span>
        @endif
    </div>

    <div class="form-group has-feedback">
        <input type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="Password" name="password" required>
        <span class="ion ion-locked form-control-feedback"></span>
        @if ($errors->has('password'))
            <span class="invalid-feedback">
                <strong>{{ $errors->first('password') }}</strong>
            </span>
        @endif
    </div>

    <div class="row">
        <div class="col-12">
            <div class="checkbox">
                <label><input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                Remember Me</label>
            </div>
        </div>
        <div class="col-6">
            <button type="submit" class="btn btn-info btn-block btn-flat margin-top-10">LOGIN</button>
        </div>
        <div class="col-6">
            <button type="button" class="btn btn-info btn-block btn-flat margin-top-10">RESET</button>
        </div>
    </div>
</form>
@endsection
