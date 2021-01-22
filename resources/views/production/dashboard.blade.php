@extends('layouts.app')

@section('title')
	Production Dashboard
@endsection

@section('content')
<section class="content-header">
	<h1>Dashboard</h1>
</section>
<section class="content" >
	<div class="box">
        <div class="box-body">
			<table class="table table-sm table-hover table-striped table-bordered nowrap" id="tbl_prod_dashboard" style="width: 100%">
				<thead class="thead-dark">
                    <tr>
                        <th>JO No.</th>
                        <th>Item Code</th>
                        <th>Description</th>
                        <th>Lot No.</th>
                        <th>Issued Qty.</th>
                        <th>Process</th>
                        <th>Unprocessed</th>
                        <th>Good</th>
                        <th>Rework</th>
                        <th>Scrap</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody id="tbl_dashboard_body"></tbody>
			</table>
		</div>
	</div>
</section>
@endsection

@push('scripts')
	<script type="text/javascript">
		var token = $('meta[name="csrf-token"]').attr('content');
		var detailsURL = "{{ url('/prod/dashboard/details-list') }}";
		var URLprocess = "{{ url('/prod/dashboard/get_process') }}";
		var getDashBoardURL = "{{ url('/prod/dashboard/getDashBoardURL') }}";
		
	</script>
	<script src="{{ mix('/js/pages/production/dashboard/dashboard.js') }}"></script>
@endpush
