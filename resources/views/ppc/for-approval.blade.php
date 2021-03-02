@extends('layouts.app')

@section('title')
	Transfer Items Approval
@endsection

@push('styles')
    <style>
    </style>
@endpush

@section('content')
<section class="content-header">
    <h1>Transfer Items for Approval</h1>
</section>

<section class="content">
	<div class="row justify-content-center">
		<div class="col-lg-10 col-md-8 col-sm-12 col-xs-12">

			<div class="row" id="timeline-loading">
		        <div class="offset-md-4 col-md-3">
		            <img src="{{ asset('images/spinner.gif') }}" width="185px" height="60px">
		        </div>
		    </div>


			<div class="box">
				<div class="box-body" id="transfer_item_approval"></div>
			</div>

	    </div>
	</div>
</section>
@endsection

@push('scripts')
    <script type="text/javascript">
        var getTransferItemsURL = "{{ url('/for-approval/transfer-items') }}";
        var answerRequestURL = "{{ url('/for-approval/answer') }}";
    </script>
    <script src="{{ mix('/js/pages/ppc/for-approval.js') }}"></script>
@endpush
