@extends('layouts.app')

@section('content')
<section class="content-header">
    <h1>Dashboard</h1>
</section>

<section class="content">
    <div class="form-group row">
        <div class="col-md-5">
            <div class="input-group input-group-sm mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">Date From</span>
                </div>
                <input type="date" class="form-control" name="date_from" id="date_from" value="{{ $from }}">
            </div>
        </div>

        <div class="col-md-5">
            <div class="input-group input-group-sm mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">Date To</span>
                </div>
                <input type="date" class="form-control" name="date_to" id="date_to" value="{{ $to }}">
            </div>
        </div>

        <div class="col-md-2">
            <button  id="search" class="btn btn-block btn-lg bg-blue">
                <i class="fa fa-search"></i> Search
            </button>
        </div>
    </div>

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Work In Progress</h3>
        </div>

        <div class="box-body">
            {{-- <div class="table-responsive"> --}}
                <table class="table table-sm table-hover table-bordered table-striped nowrap" id="tbl_dashboard" style="width: 100%">
                    <thead class="thead-dark">
                        <tr>
                            <th>JO No.</th>
                            <th>Item Code</th>
                            <th>Description</th>
                            <th>Divison</th>
                            <th>Plant</th>
                            <th>Process</th>
                            <th>Material</th>
                            <th>Heat No.</th>
                            <th>Lot No.</th>
                            <th>Sched Qty.</th> 
                            <th>Unprocess</th>
                            <th>Good</th>
                            <th>Scrap</th>
                            <th>Total Ouput</th>
                            <th>Order Qty.</th>
                            <th>Total Issued Qty</th>
                            <th>Issued Qty</th> 
                            <th>End Date</th>                      
                            <th>Status</th>

                        </tr>
                    </thead>
                    <tbody id="tbl_dashboard_body"></tbody>
                </table>
            {{-- </div> --}}
            
        </div>
    </div>

   <form action="" method="post" role="form">
        <div class="form-group row">
            <div class="col-md-5">
                <div class="input-group input-group-sm mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Job Order</span>
                    </div>
                    <select class="form-control" name="jo_no" id="jo_no" ></select>
                </div>
            </div>
        </div>
    </form>

    <div class="row" id="chart"></div>
</section>
@include('includes.modals.system-modals')
@endsection

@push('scripts')
    <script type="text/javascript">
        var token = $('meta[name="csrf-token"]').attr('content');
        var get_dashboard = "{{ url('/dashboard/get-dashboard') }}";
        var get_chartURl = "{{ url('/dashboard/pie-graph') }}";
        var get_jonoURL = "{{ url('/dashboard/get-jono') }}";
    </script>
    <script src="{{ mix('/js/pages/ppc/dashboard/dashboard.js') }}"></script>
@endpush
