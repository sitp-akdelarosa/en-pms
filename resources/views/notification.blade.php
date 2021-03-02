@extends('layouts.app')

@section('title')
	Notifications
@endsection

@push('styles')
    <style>
    </style>
@endpush

@section('content')
<section class="content-header">
    <h1>Notification</h1>
</section>

<section class="content">
	<div class="row justify-content-center">
		<div class="col-lg-10 col-md-8 col-sm-12 col-xs-12">

			<div class="box">
				<div class="box-body" id="noti_items">
					
				</div>
			</div>

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
    <script src="{{ mix('/js/pages/notification.js') }}"></script>
@endpush
