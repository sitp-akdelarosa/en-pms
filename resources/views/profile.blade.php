@extends('layouts.app')

@push('styles')
    <style>
        .stuck {
            position: fixed;
            width: 25%;
            max-width: 25%;
        }
    </style>
@endpush

@section('title')
	{{ $user->firstname.' '.$user->lastname }}
@endsection

@section('content')
<section class="content-header">
    <h1>Profile</h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-3">
            <div class="box box-default">
                <div class="fx-card-item">
                    <div class="fx-card-avatar fx-overlay-1">
                        <img src="{{ asset($user->photo) }}" alt="user" class="img-fluid" height="200px">
                    </div>
                    <div class="fx-card-content text-center">
                        <h5 class="box-title">{{ $user->firstname.' '.$user->lastname }}</h5>
                        {{-- <span>{{ $user->user_type }}</span> --}}
                        <input type="hidden" id="user_id" value="{{ $user->id }}">

                        <br> 
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <ul class="timeline" id="timeline"></ul>
            <div class="row" id="timeline-loading">
                <div class="offset-md-4 col-md-3">
                    <img src="{{ asset('images/spinner.gif') }}" width="185px" height="60px">
                </div>
            </div>
            <div class="row" id="show-more-row">
                <div class="offset-md-4 col-md-3 text-center">
                    <button class="btn btn-outline-secondary btn-block" id="show-more">Show more</button>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
    <script src="{{ mix('/js/pages/profile.js') }}"></script>
@endpush
