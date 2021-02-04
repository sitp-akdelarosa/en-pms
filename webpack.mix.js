let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/assets/js/app.js', 'public/js')
	.styles([
	    'resources/assets/plugins/bootstrap/dist/css/bootstrap.css',
	    'resources/assets/plugins/bootstrap/dist/css/bootstrap-extend.css',
	    'resources/assets/plugins/font-awesome/css/font-awesome.css',
	    'resources/assets/plugins/Ionicons/css/ionicons.css',
	    'resources/assets/plugins/sweetalert/sweetalert.css',
        'resources/assets/plugins/toast/jquery.toast.css',
	    'resources/assets/plugins/DataTables/css/datatables.min.css',
        'resources/assets/plugins/DataTables/css/dataTables.bootstrap4.min.css',
        'resources/assets/plugins/DataTables/css/dataTables.checkboxes.css',
        'resources/assets/plugins/bootstrap-slider/slider.css',
        'resources/assets/plugins/select2/dist/css/select2.min.css', 
        'resources/assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css',
        'resources/assets/plugins/autocomplete/jquery-ui.min.css',
	    'resources/assets/css/master_style.css',
	    'resources/assets/css/custom_styles.css',
	    'resources/assets/css/skins/_all-skins.css'
	], 'public/css/main.css')   

    .scripts([
        'public/js/app.js',
        //'resources/assets/plugins/jquery/dist/jquery.min.js',
	    'resources/assets/plugins/bootstrap/dist/js/bootstrap.min.js',
	    'resources/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js',
	    'resources/assets/plugins/fastclick/lib/fastclick.js',
	    'resources/assets/plugins/jquery-sparkline/dist/jquery.sparkline.min.js',
        'resources/assets/plugins/canvasjs/jquery.canvasjs.min.js',
	    'resources/assets/plugins/sweetalert/sweetalert.min.js',
        'resources/assets/plugins/toast/jquery.toast.js',
        'resources/assets/plugins/jquery-mask/jquery.mask.min.js',
	    'resources/assets/js/template.js',
	    'resources/assets/plugins/DataTables/js/datatables.min.js',
        'resources/assets/plugins/DataTables/js/dataTables.bootstrap4.min.js',
        'resources/assets/plugins/DataTables/js/datatable-fixedColumns.min.js',
        'resources/assets/plugins/DataTables/js/datatable-row-show.js',
        'resources/assets/plugins/DataTables/js/dataTables.checkboxes.min.js',
        'resources/assets/plugins/timeago/timeago.min.js',
        'resources/assets/plugins/select2/dist/js/select2.full.js',
        'resources/assets/plugins//moment/min/moment.min.js',
        'resources/assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js',
        'resources/assets/plugins/autocomplete/jquery-ui.min.js',
        'resources/assets/plugins/session-timeout/jTimeout.min.js',
        'resources/assets/plugins/popper/dist/popper.min.js',
        'resources/assets/js/resizer.js',
        'resources/assets/js/helpers.js',
        'resources/assets/js/csrf_token.js'        
    ], 'public/js/main.js')

    .styles([
        'resources/assets/plugins/bootstrap/dist/css/bootstrap.css',
        'resources/assets/plugins/bootstrap/dist/css/bootstrap-extend.css',
        'resources/assets/plugins/font-awesome/css/font-awesome.css',
        'resources/assets/plugins/Ionicons/css/ionicons.css',
        'resources/assets/css/master_style.css',
        'resources/assets/css/custom_styles.css',
        'resources/assets/css/skins/_all-skins.css'
    ], 'public/css/login.css')

    .scripts([
        'public/js/app.js',
        //'resources/assets/plugins/jquery/dist/jquery.min.js',
        'resources/assets/plugins/bootstrap/dist/js/bootstrap.min.js',
        'resources/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js',
        'resources/assets/plugins/fastclick/lib/fastclick.js',
        'resources/assets/js/template.js',
        'resources/assets/js/resizer.js',
        'resources/assets/js/csrf_token.js'
    ], 'public/js/login.js')


    .js('resources/assets/js/pages/profile.js', 'public/js/pages/')
    .js('resources/assets/js/pages/notification.js', 'public/js/pages/')
    

    .js('resources/assets/js/pages/ppc/for-approval.js', 'public/js/pages/ppc/')

    .js('resources/assets/js/pages/ppc/dashboard/dashboard.js', 'public/js/pages/ppc/dashboard')

    // Admin
    .js('resources/assets/js/pages/admin/user-master/user-master.js', 'public/js/pages/admin/user-master')
    .js('resources/assets/js/pages/admin/assign-production-line/assign-production-line.js', 'public/js/pages/admin/assign-production-line')
    .js('resources/assets/js/pages/admin/assign-material-type/assign-material-type.js', 'public/js/pages/admin/assign-material-type')
    .js('resources/assets/js/pages/admin/assign-warehouse/assign-warehouse.js', 'public/js/pages/admin/assign-warehouse')
    .js('resources/assets/js/pages/admin/user-type/user-type.js', 'public/js/pages/admin/user-type/')
    .js('resources/assets/js/pages/admin/audit-trail/audit-trail.js', 'public/js/pages/admin/audit-trail/')
    .js('resources/assets/js/pages/admin/settings/settings.js', 'public/js/pages/admin/settings/')

    // Masters
    
    .js('resources/assets/js/pages/ppc/masters/division-master/division-master.js', 'public/js/pages/ppc/masters/division-master')
    .js('resources/assets/js/pages/ppc/masters/dropdown-master/dropdown-master.js', 'public/js/pages/ppc/masters/dropdown-master')
    .scripts([
    	'resources/assets/js/pages/ppc/masters/product-master/product-code-assembly.js',
    	'resources/assets/js/pages/ppc/masters/product-master/product-code.js'
    ], 'public/js/pages/ppc/masters/product-master/product-master.js')

    .scripts([
        'resources/assets/js/pages/ppc/masters/material-master/material-code-assembly.js',
        'resources/assets/js/pages/ppc/masters/material-master/material-code.js'
    ], 'public/js/pages/ppc/masters/material-master/material-master.js')

    .scripts([
        'resources/assets/plugins/sortablejs/Sortable.js',
        'resources/assets/plugins/sortablejs/jquery-sortable.js',
        'resources/assets/js/pages/ppc/masters/process-master/process-master.js'
    ], 'public/js/pages/ppc/masters/process-master/process-master.js')

    .js('resources/assets/js/pages/ppc/masters/operator-master/operator-master.js', 'public/js/pages/ppc/masters/operator-master')

    //transaction
    .js('resources/assets/js/pages/ppc/transactions/upload-orders/upload-orders.js', 'public/js/pages/ppc/transactions/upload-orders/upload-orders.js')
    .js('resources/assets/js/pages/ppc/transactions/update-inventory/update-inventory.js', 'public/js/pages/ppc/transactions/update-inventory/update-inventory.js')
    .js('resources/assets/js/pages/ppc/transactions/production-schedule/production-schedule.js', 'public/js/pages/ppc/transactions/production-schedule/production-schedule.js')
    
    .scripts([
        'resources/assets/plugins/sortablejs/Sortable.js',
        'resources/assets/plugins/sortablejs/jquery-sortable.js',
        'resources/assets/js/pages/ppc/transactions/travel-sheet/travel-sheet.js'
    ], 'public/js/pages/ppc/transactions/travel-sheet/travel-sheet.js')

    .js('resources/assets/js/pages/ppc/transactions/cutting-schedule/cutting-schedule.js', 'public/js/pages/ppc/transactions/cutting-schedule/cutting-schedule.js')
    .js('resources/assets/js/pages/ppc/transactions/raw-material-withdrawal/raw-material-withdrawal.js', 'public/js/pages/ppc/transactions/raw-material-withdrawal/raw-material-withdrawal.js')
    .js('resources/assets/js/pages/ppc/transactions/product-withdrawal/product-withdrawal.js', 'public/js/pages/ppc/transactions/product-withdrawal/product-withdrawal.js')

    .js('resources/assets/js/pages/production/dashboard/dashboard.js', 'public/js/pages/production/dashboard')
    .js('resources/assets/js/pages/production/transactions/production-output/production-output.js', 'public/js/pages/production/transactions/production-output/production-output.js')
    .js('resources/assets/js/pages/production/transactions/transfer-items/transfer-items.js', 'public/js/pages/production/transactions/transfer-items/transfer-items.js')

    .js('resources/assets/js/pages/ppc/reports/fg-summary.js', 'public/js/pages/ppc/reports/')
    .js('resources/assets/js/pages/ppc/reports/transfer-item-report.js', 'public/js/pages/ppc/reports/')
    .js('resources/assets/js/pages/ppc/reports/travel-sheet-status.js', 'public/js/pages/ppc/reports/')

    .js('resources/assets/js/pages/production/reports/operators-output.js', 'public/js/pages/production/reports')
    .js('resources/assets/js/pages/production/reports/summary-report.js', 'public/js/pages/production/reports')
    