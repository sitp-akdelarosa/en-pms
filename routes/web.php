<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/', function () {
	if (Auth::check()) {
		$user_access = \DB::table('users as u')
                            ->join('admin_user_types as ut','u.user_type','=','ut.id')
                            ->select('ut.description','ut.category')
                            ->where('u.user_id',Auth::user()->user_id)
							->first();
							
		switch ($user_access->category) {
			case 'PRODUCTION':
				return redirect('/prod/dashboard');
				break;

			default:
				return redirect('/dashboard');
				break;
		}
	} else {
		return redirect('/login');
	}
});

Auth::routes();

Route::group(['middleware' => ['ajax-session-expired', 'auth', 'deleted_user', 'ppc', 'no.back']], function () {

	Route::group(['prefix' => 'dashboard'], function () {
		Route::get('/', 'PPC\DashboardController@index')->name('dashboard');
		Route::get('/get-dashboard', 'PPC\DashboardController@get_dashboard')
			->name('dashboard.get-dashboard');
		Route::get('/pie-graph', 'PPC\DashboardController@pie_graph')
			->name('dashboard.pie-graph');
		Route::get('/get-jono', 'PPC\DashboardController@get_jono')
			->name('dashboard.get-jono');
		Route::get('/dashboard-search-filter', 'PPC\DashboardController@searchFilter')
			->name('dashboard.dashboard-search-filter');
		Route::get('/dashboard-search-excel', 'PPC\DashboardController@downloadExcelSearchFilter')
			->name('dashboard.dashboard-search-excel');
	});

	Route::group(['prefix' => 'masters'], function () {
		Route::group(['prefix' => 'division-master'], function () {
			Route::get('/', 'PPC\Masters\DivisionMasterController@index')
				->name('masters.division-master');
			Route::get('/list', 'PPC\Masters\DivisionMasterController@division_list')
				->name('masters.division-master.list');
			Route::post('/save', 'PPC\Masters\DivisionMasterController@save')
				->name('masters.division-master.save');
			Route::post('/destroy', 'PPC\Masters\DivisionMasterController@destroy')
				->name('masters.division-master.destroy');
			Route::get('/getuserID', 'PPC\Masters\DivisionMasterController@getuserID')
				->name('masters.division-master.getuserID');
			Route::get('/get-process', 'PPC\Masters\DivisionMasterController@getProcess')
				->name('masters.division-master.get-process');
			Route::get('/get-productline', 'PPC\Masters\DivisionMasterController@getProductline')
				->name('masters.division-master.get-productline');
			Route::get('/get-leader', 'PPC\Masters\DivisionMasterController@getLeader')
				->name('masters.division-master.get-leader');
			Route::post('/disableEnableDivision', 'PPC\Masters\DivisionMasterController@disableEnableDivision')
				->name('masters.division-master.disableEnableDivision');
		});

		Route::group(['prefix' => 'dropdown-master'], function () {
			Route::get('/', 'PPC\Masters\DropdownMasterController@index')
				->name('masters.dropdown-master');
			Route::get('/names', 'PPC\Masters\DropdownMasterController@dropdown_names')
				->name('masters.dropdown-master.names');
			Route::get('/dropdown-list', 'PPC\Masters\DropdownMasterController@dropdown_list')
				->name('masters.dropdown-master.list');
			Route::get('/items', 'PPC\Masters\DropdownMasterController@get_items')
				->name('masters.dropdown-master.items');
			Route::get('/check-items', 'PPC\Masters\DropdownMasterController@check_item_if_exist')
				->name('masters.dropdown-master.check-items');
			Route::post('/save/dropdown-name', 'PPC\Masters\DropdownMasterController@save_dropdown_name')
				->name('masters.dropdown-master.save.dropdown-name');
			Route::post('/save/dropdown-item', 'PPC\Masters\DropdownMasterController@save_dropdown_items')
				->name('masters.dropdown-master.save.dropdown-item');
			Route::post('/destroy/names', 'PPC\Masters\DropdownMasterController@destroy_dropdown_name')
				->name('masters.dropdown-master.destroy.names');
			Route::post('/destroy/items', 'PPC\Masters\DropdownMasterController@destroy_dropdown_items')
				->name('masters.dropdown-master.destroy.items');
		});

		Route::group(['prefix' => 'product-master'], function () {
			Route::get('/', 'PPC\Masters\ProductMasterController@index')
				->name('masters.product-master');
			Route::get('/assembly/list', 'PPC\Masters\ProductMasterController@product_code_assembly_list')
				->name('masters.product-master.assembly.list');
			Route::post('/assembly/save', 'PPC\Masters\ProductMasterController@save_code_assembly')
				->name('masters.product-master.assembly.save');
			Route::post('/assembly/destroy', 'PPC\Masters\ProductMasterController@destroy_code_assembly')
				->name('masters.product-master.assembly.destroy');
			Route::get('/get-product-line', 'PPC\Masters\ProductMasterController@getProductline')
				->name('masters.product-master.get-product-line');

			Route::post('/getProcessURL', 'PPC\Masters\ProductMasterController@selected_process_list')
				->name('masters.product-master.getProcessURL');
			Route::get('/get_dropdown_product', 'PPC\Masters\ProductMasterController@get_dropdown_product')
				->name('masters.product-master.get_dropdown_product');

			Route::get('/code/product-type', 'PPC\Masters\ProductMasterController@product_type')
				->name('masters.product-master.code.product-type');
			Route::get('/code/show-dropdowns', 'PPC\Masters\ProductMasterController@show_dropdowns')
				->name('masters.product-master.code.show-dropdowns');
			Route::get('/code/get-process-div', 'PPC\Masters\ProductMasterController@process_div')
				->name('masters.product-master.code.get-process-div');

			Route::get('/code/get-prod-code-list', 'PPC\Masters\ProductMasterController@prod_code_list')
				->name('masters.product-master.code.get-prod-code-list');
			Route::post('/code/product/save', 'PPC\Masters\ProductMasterController@save_product_code')
				->name('masters.product-master.code.product.save');
			Route::post('/product/destroy', 'PPC\Masters\ProductMasterController@destroy_product_code')
				->name('masters.product-master.product.destroy');

			Route::get('/code/get-prod-process-list', 'PPC\Masters\ProductMasterController@prod_process_list')
				->name('masters.product-master.code.get-prod-process-list');
			Route::post('/code/processes/save', 'PPC\Masters\ProductMasterController@save_processes')
				->name('masters.product-master.code.process.save');
			Route::post('/process/destroy', 'PPC\Masters\ProductMasterController@destroy_process_code')
				->name('masters.product-master.process.destroy');

			Route::get('/update', 'PPC\Masters\ProductMasterController@updateAllData')
				->name('masters.product-master.update');
			Route::get('/get-standard-material', 'PPC\Masters\ProductMasterController@getStandardMaterial')
				->name('masters.product-master.get-standard-material');

			Route::get('/download-excel-file', 'PPC\Masters\ProductMasterController@downloadExcelFile')
				->name('masters.product-master.download-excel-file');
			Route::get('/all-product-lines', 'PPC\Masters\ProductMasterController@getAllProductLines')
				->name('masters.product-master.all-product-lines');
			Route::get('/enable-disabled-product', 'PPC\Masters\ProductMasterController@enableDisabledProducts')
				->name('masters.product-master.enable-disabled-product');
		});

		Route::group(['prefix' => 'material-master'], function () {
			Route::get('/', 'PPC\Masters\MaterialMasterController@index')
				->name('masters.material-master');
			Route::get('/material-list', 'PPC\Masters\MaterialMasterController@material_assembly_list')
				->name('masters.material-master.material-list');
			Route::post('/save', 'PPC\Masters\MaterialMasterController@save_material_assembly')
				->name('masters.material-master.save');
			Route::post('/destroy', 'PPC\Masters\MaterialMasterController@destroy_material_assembly')
				->name('masters.material-master.destroy');
			Route::get('/show-dropdowns', 'PPC\Masters\MaterialMasterController@show_dropdowns')
				->name('masters.material-master.show-dropdowns');
			Route::get('/get-mat-code-list', 'PPC\Masters\MaterialMasterController@mat_code_list')
				->name('masters.material-master.get-mat-code-list');
			Route::post('/save-code', 'PPC\Masters\MaterialMasterController@save_material_code')
				->name('masters.material-master.save-code');
			Route::post('/destroy-code', 'PPC\Masters\MaterialMasterController@destroy_code')
				->name('masters.material-master.destroy-code');
			Route::get('/get_dropdown_material_type', 'PPC\Masters\MaterialMasterController@get_dropdown_material_type')
				->name('masters.material-master.get_dropdown_material_type');
			Route::get('/enable-disabled-material', 'PPC\Masters\MaterialMasterController@enableDisabledMaterials')
				->name('masters.material-master.enable-disabled-material');
			Route::get('/download-excel-file', 'PPC\Masters\MaterialMasterController@downloadExcelFile')
				->name('masters.material-master.download-excel-file');
			Route::get('/all-material-types', 'PPC\Masters\MaterialMasterController@getAllMaterialTypes')
				->name('masters.material-master.all-material-types');
		});

		Route::group(['prefix' => 'process-master'], function () {
			Route::get('/', 'PPC\Masters\ProcessMasterController@index')
				->name('masters.process-master');
			Route::get('/process-list', 'PPC\Masters\ProcessMasterController@process_list')
				->name('masters.process-master.process-list');
			Route::get('/selected-process-list', 'PPC\Masters\ProcessMasterController@selected_process_list')
				->name('masters.process-master.selected-process-list');
			Route::post('/save', 'PPC\Masters\ProcessMasterController@save')
				->name('masters.process-master.save');
			Route::post('/save-set', 'PPC\Masters\ProcessMasterController@save_set')
				->name('masters.process-master.save-set');
			Route::get('/get-set', 'PPC\Masters\ProcessMasterController@get_set')
				->name('masters.process-master.get-set');
			Route::post('/delete-set', 'PPC\Masters\ProcessMasterController@destroy_set')
				->name('masters.process-master.delete-set');
			Route::get('/get-product-line', 'PPC\Masters\ProcessMasterController@getProductLine')
				->name('masters.process-master.get-product-line');
			Route::get('/selected-product-line', 'PPC\Masters\ProcessMasterController@selectedProductLine')
				->name('masters.process-master.selected-product-line');
		});

		Route::group(['prefix' => 'operator-master'], function () {
			Route::get('/', 'PPC\Masters\OperatorMasterController@index')
				->name('masters.operator-master');
			Route::get('/get-operators', 'PPC\Masters\OperatorMasterController@Operators')
				->name('masters.operator-master.get-operators');
			Route::post('/save', 'PPC\Masters\OperatorMasterController@save')
				->name('masters.operator-master.save');
			Route::post('/destroy', 'PPC\Masters\OperatorMasterController@destroy')
				->name('masters.operator-master.destroy');
			Route::get('/enable-disabled-operator', 'PPC\Masters\OperatorMasterController@enableDisabledOperators')
				->name('masters.operator-master.enable-disabled-operator');
		});
	});

	Route::group(['prefix' => 'transaction'], function () {
		Route::group(['prefix' => 'update-inventory'], function () {
			Route::get('/', 'PPC\Transaction\UpdateInventoryController@index')
				->name('transaction.update-inventory');

			Route::post('/CheckFileUpdateInventory', 'PPC\Transaction\UpdateInventoryController@CheckFile')
				->name('transaction.update-inventory.CheckFile');

			Route::post('/UploadInventory', 'PPC\Transaction\UpdateInventoryController@UploadInventory')
				->name('transaction.update-inventory.UploadInventory');

			Route::get('/materials', 'PPC\Transaction\UpdateInventoryController@materialDataTable')
				->name('transaction.update-inventory.materials');

			Route::post('/AddManual', 'PPC\Transaction\UpdateInventoryController@AddManual')
				->name('transaction.update-inventory.AddManual');

			Route::get('/material-type', 'PPC\Transaction\UpdateInventoryController@GetMaterialType')
				->name('transaction.update-inventory.material-type');
			
			Route::get('/prod-lines', 'PPC\Transaction\UpdateInventoryController@getProductLine')
				->name('transaction.update-inventory.prod-lines');

			Route::get('/warehouse', 'PPC\Transaction\UpdateInventoryController@GetWarehouse')
				->name('transaction.update-inventory.warehouse');

			Route::get('/GetItemCode', 'PPC\Transaction\UpdateInventoryController@GetItemCode')
				->name('transaction.update-inventory.GetItemCode');

			Route::get('/GetItemCodeDetails', 'PPC\Transaction\UpdateInventoryController@GetItemCodeDetails')
				->name('transaction.update-inventory.GetItemCodeDetails');

			Route::get('/download-unregistered-materials', 'PPC\Transaction\UpdateInventoryController@unRegisteredMaterialsExcel')
				->name('transaction.update-inventory.download-unregistered-materials');

			Route::get('/get-unregistered-materials', 'PPC\Transaction\UpdateInventoryController@unRegisteredMaterials')
				->name('transaction.update-inventory.get-unregistered-materials');

			Route::get('/download-inventory-material-format', 'PPC\Transaction\UpdateInventoryController@downloadExcelMaterialFormat')
				->name('transaction.update-inventory.download-inventory-material-format');

			Route::get('/download-inventory-product-format', 'PPC\Transaction\UpdateInventoryController@downloadExcelProductFormat')
				->name('transaction.update-inventory.download-inventory-product-format');
			
			Route::get('/download-update-inventory-search', 'PPC\Transaction\UpdateInventoryController@downloadExcelSearchFilter')
				->name('transaction.update-inventory.download-update-inventory-search');

			Route::get('/search-filter', 'PPC\Transaction\UpdateInventoryController@searchFilter')
				->name('transaction.update-inventory.search-filter');

			Route::post('/delete-inventory', 'PPC\Transaction\UpdateInventoryController@destroy')
				->name('transaction.update-inventory.delete-inventory');
			
			Route::get('/check-inventory-deletion', 'PPC\Transaction\UpdateInventoryController@checkInventoryDeletion')
				->name('transaction.update-inventory.check-inventory-deletion');
		});

		Route::group(['prefix' => 'upload-orders'], function () {
			Route::get('/', 'PPC\Transaction\UploadOrdersController@index')
				->name('transaction.upload-orders');
			Route::post('/upload-up', 'PPC\Transaction\UploadOrdersController@UploadUP')
				->name('transaction.upload-up');
			Route::get('/DatatableUpload', 'PPC\Transaction\UploadOrdersController@DatatableUpload')
				->name('transaction.upload-orders.DatatableUpload');
			Route::post('/trucateDBTempupload', 'PPC\Transaction\UploadOrdersController@trucateDBTempupload')
				->name('transaction.upload-orders.trucateDBTempupload');
			Route::get('/deletefromtemp', 'PPC\Transaction\UploadOrdersController@deletefromtemp')
				->name('transaction.upload-orders.deletefromtemp');
			Route::post('/CheckFile', 'PPC\Transaction\UploadOrdersController@CheckFile')
				->name('transaction.upload-orders.CheckFile');
			Route::post('/overwrite', 'PPC\Transaction\UploadOrdersController@overwrite')
				->name('transaction.upload-orders.overwrite');
			Route::get('/download-unregistered-products', 'PPC\Transaction\UploadOrdersController@unRegisteredProductsExcel')
				->name('transaction.upload-orders.download-unregistered-products');
			Route::get('/get-unregistered-products', 'PPC\Transaction\UploadOrdersController@unRegisteredProducts')
				->name('transaction.upload-orders.get-unregistered-products');
			Route::get('/search-orders-excel', 'PPC\Transaction\UploadOrdersController@excelFilteredData')
				->name('transaction.upload-orders.search-orders-excel');
			Route::get('/search-filter-orders', 'PPC\Transaction\UploadOrdersController@searchFilter')
				->name('transaction.upload-orders.search-filter-orders');
		});

		Route::group(['prefix' => 'production-schedule'], function () {
			Route::get('/', 'PPC\Transaction\ProductionScheduleController@index')
				->name('transaction.production-schedule');
			Route::get('/get-orders', 'PPC\Transaction\ProductionScheduleController@getOrders')
				->name('transaction.production-schedule.get-orders');
			Route::get('/search-filter-orders', 'PPC\Transaction\ProductionScheduleController@filterOrders')
				->name('transaction.production-schedule.search-filter-orders');
			Route::get('/get-materials', 'PPC\Transaction\ProductionScheduleController@getMaterials')
				->name('transaction.production-schedule.get-materials');
			Route::get('/get-products', 'PPC\Transaction\ProductionScheduleController@getProducts')
				->name('transaction.production-schedule.get-products');
			Route::post('/save-item-materials', 'PPC\Transaction\ProductionScheduleController@SaveItemMaterials')
				->name('transaction.production-schedule.save-item-materials');
			Route::get('/get-item-materials', 'PPC\Transaction\ProductionScheduleController@getItemMaterials')
				->name('transaction.production-schedule.get-item-materials');
			Route::post('/save-jo-details', 'PPC\Transaction\ProductionScheduleController@SaveJODetails')
				->name('transaction.production-schedule.save-jo-details');
			Route::get('/get-travel-sheet', 'PPC\Transaction\ProductionScheduleController@getTravel_sheet')
				->name('transaction.production-schedule.get-travel-sheet');
			Route::get('/get-jo-details', 'PPC\Transaction\ProductionScheduleController@getJODetails')
				->name('transaction.production-schedule.get-jo-details');
			Route::get('/search-filter-jo', 'PPC\Transaction\ProductionScheduleController@filterJO')
				->name('transaction.production-schedule.search-filter-jo');
			Route::post('/delete-jo-detail-item', 'PPC\Transaction\ProductionScheduleController@deleteJoDetailItem')
				->name('transaction.production-schedule.delete-jo-detail-item');
			Route::post('/edit-jo-detail-item', 'PPC\Transaction\ProductionScheduleController@editJoDetailItem')
				->name('transaction.production-schedule.edit-jo-detail-item');
			Route::post('/cancel-travel-sheet', 'PPC\Transaction\ProductionScheduleController@CancelTravelSheet')
				->name('transaction.production-schedule.cancel-travel-sheet');

			// Route::get('/get-material-used', 'PPC\Transaction\ProductionScheduleController@getMaterialUsed')
			// 	->name('transaction.production-schedule.get-material-used');
			// Route::get('/get-standard-material-used', 'PPC\Transaction\ProductionScheduleController@getStandardMaterialUsed')
			// 	->name('transaction.production-schedule.get-standard-material-used');
			// Route::get('/get-material-heat-no', 'PPC\Transaction\ProductionScheduleController@getMaterialHeatNo')
			// 	->name('transaction.production-schedule.get-material-heat-no');
			// Route::post('/SaveJODetails', 'PPC\Transaction\ProductionScheduleController@SaveJODetails')
			// 	->name('transaction.production-schedule.SaveJODetails');
			// Route::get('/JOsuggest', 'PPC\Transaction\ProductionScheduleController@JOsuggest')
			// 	->name('transaction.production-schedule.JOsuggest');
			// Route::get('/getjotables', 'PPC\Transaction\ProductionScheduleController@getJOviaJOno')
			// 	->name('transaction.production-schedule.getjotables');
			// Route::get('/getjoALL', 'PPC\Transaction\ProductionScheduleController@getJOALL')
			// 	->name('transaction.production-schedule.getjoall');
			// Route::get('/getTravelSheet', 'PPC\Transaction\ProductionScheduleController@getTravel_sheet')
			// 	->name('transaction.production-schedule.getTravelSheet');
			// Route::post('/cancelTravelSheet', 'PPC\Transaction\ProductionScheduleController@cancel_TravelSheet')
			// 	->name('transaction.production-schedule.cancelTravelSheet');
			// Route::get('/over-issuance', 'PPC\Transaction\ProductionScheduleController@calculateOverIssuance')
			// 	->name('transaction.production-schedule.over-issuance');
			
			// Route::post('/save-materials', 'PPC\Transaction\ProductionScheduleController@SaveMaterials')
			// 	->name('transaction.production-schedule.save-materials');
			// Route::get('/get-materials', 'PPC\Transaction\ProductionScheduleController@Materials')
			// 	->name('transaction.production-schedule.get-materials');
			
		});

		Route::group(['prefix' => 'raw-material-withdrawal'], function () {
			Route::get('/', 'PPC\Transaction\RawMaterialWithdrawalController@index')
				->name('transaction.raw-material-withdrawal');
			Route::get('/get-sc-no', 'PPC\Transaction\RawMaterialWithdrawalController@getScNo')
				->name('transaction.raw-material-withdrawal.get-sc-no');
			Route::get('/get-heat-no', 'PPC\Transaction\RawMaterialWithdrawalController@getHeatNo')
				->name('transaction.raw-material-withdrawal.get-heat-no');
			Route::post('/save', 'PPC\Transaction\RawMaterialWithdrawalController@save')
				->name('transaction.raw-material-withdrawal.save');
			Route::post('/destroy', 'PPC\Transaction\RawMaterialWithdrawalController@destroy')
				->name('transaction.raw-material-withdrawal.destroy');
			Route::get('/search-trans-no', 'PPC\Transaction\RawMaterialWithdrawalController@searchTransNo')
				->name('transaction.raw-material-withdrawal.search-trans-no');
			Route::get('/scnosuggest', 'PPC\Transaction\RawMaterialWithdrawalController@scnosuggest')
				->name('transaction.raw-material-withdrawal.scnosuggest');
			Route::get('/material-details', 'PPC\Transaction\RawMaterialWithdrawalController@material_details')
				->name('transaction.raw-material-withdrawal.material-details');
			Route::get('/getComputationIssuedQty', 'PPC\Transaction\RawMaterialWithdrawalController@getComputationIssuedQty')
				->name('transaction.raw-material-withdrawal.getComputationIssuedQty');
			Route::get('/search-filter-raw-material', 'PPC\Transaction\RawMaterialWithdrawalController@searchFilter')
				->name('transaction.raw-material-withdrawal.search-filter-raw-material');
			Route::get('/search-raw-material-excel', 'PPC\Transaction\RawMaterialWithdrawalController@excelFilteredData')
				->name('transaction.raw-material-withdrawal.search-raw-material-excel');
			Route::post('/confirm-raw-material-withdrawal', 'PPC\Transaction\RawMaterialWithdrawalController@ConfirmWithdrawal')
				->name('transaction.raw-material-withdrawal.confirm-raw-material-withdrawal');
			Route::get('/check-withdrawal-cancellation', 'PPC\Transaction\RawMaterialWithdrawalController@checkRMWithdrawalCancellation')
				->name('transaction.raw-material-withdrawal.check-withdrawal-cancellation');
		});

		Route::group(['prefix' => 'product-withdrawal'], function () {
			Route::get('/', 'PPC\Transaction\ProductWithdrawalController@index')
				->name('transaction.product-withdrawal');
			Route::get('/get-product-inventory', 'PPC\Transaction\ProductWithdrawalController@getInventory')
				->name('transaction.product-withdrawal.get-product-inventory');
			Route::post('/save', 'PPC\Transaction\ProductWithdrawalController@save')
				->name('transaction.product-withdrawal.save');
			Route::get('/get-withdrawal-transaction', 'PPC\Transaction\ProductWithdrawalController@getWithdrawalTransaction')
				->name('transaction.product-withdrawal.get-withdrawal-transaction');
			Route::get('/search-filter-product-withdrawal', 'PPC\Transaction\ProductWithdrawalController@searchFilter')
				->name('transaction.raw-material-withdrawal.search-filter-product-withdrawal');
			Route::get('/search-product-withdrawal-excel', 'PPC\Transaction\ProductWithdrawalController@excelFilteredData')
				->name('transaction.raw-material-withdrawal.search-product-withdrawal-excel');
			Route::post('/delete-product-withdrawal', 'PPC\Transaction\ProductWithdrawalController@destroy')
				->name('transaction.product-withdrawal.delete-product-withdrawal');
			Route::post('/confirm-product-withdrawal', 'PPC\Transaction\ProductWithdrawalController@ConfirmWithdrawal')
				->name('transaction.product-withdrawal.confirm-product-withdrawal');
			Route::get('/check-withdrawal-cancellation', 'PPC\Transaction\ProductWithdrawalController@checkRMWithdrawalCancellation')
				->name('transaction.product-withdrawal.check-withdrawal-cancellation');
		});

		Route::group(['prefix' => 'travel-sheet'], function () {
			Route::get('/', 'PPC\Transaction\TravelSheetController@index')
				->name('transaction.travel-sheet');
			Route::get('/set-up/jo-list', 'PPC\Transaction\TravelSheetController@getJoDetails')
				->name('transaction.travel-sheet.jo-list');
			Route::get('/set-up/process', 'PPC\Transaction\TravelSheetController@getProcess')
				->name('transaction.travel-sheet.process');
			Route::post('/set-up/save', 'PPC\Transaction\TravelSheetController@save_travel_sheet_setup')
				->name('transaction.travel-sheet.set-up.save');
			Route::get('/pre-travel-sheet-data', 'PPC\Transaction\TravelSheetController@getPreTravelSheetData')
				->name('transaction.travel-sheet.pre-travel-sheet-data');
			Route::post('/get-Sc_no', 'PPC\Transaction\TravelSheetController@getSc_no')
				->name('transaction.travel-sheet.get-Sc_no');
			Route::get('/get-process-div', 'PPC\Transaction\TravelSheetController@getProcessDiv')
				->name('transaction.travel-sheet.get-process-div');
			Route::post('/proceed-to-production', 'PPC\Transaction\TravelSheetController@proceedToProduction')
				->name('transaction.travel-sheet.proceed-to-production');
		});

		Route::group(['prefix' => 'cutting-schedule'], function () {
			Route::get('/', 'PPC\Transaction\CuttingScheduleController@index')
				->name('transaction.cutting-schedule');
			Route::get('/jo-list', 'PPC\Transaction\CuttingScheduleController@JOList')
				->name('transaction.cutting-schedule.jo-list');
			Route::get('/cut-sched-leader', 'PPC\Transaction\CuttingScheduleController@getLeader')
				->name('transaction.cutting-schedule.cut-sched-leader');
			Route::POST('/save-cutt-sched', 'PPC\Transaction\CuttingScheduleController@saveCuttingSched')
				->name('transaction.cutting-schedule.save-cutt-sched');
			Route::get('/cut-sched-list', 'PPC\Transaction\CuttingScheduleController@getCutSchedList')
				->name('transaction.cutting-schedule.cut-sched-list');

			// Route::get('/materials', 'PPC\Transaction\CuttingScheduleController@getJoDetailsCut')
			// 	->name('transaction.cutting-schedule.materials');
			// Route::get('/prodline', 'PPC\Transaction\CuttingScheduleController@getProdline')
			// 	->name('transaction.cutting-schedule.prodline');
			// Route::get('/cut-sched-details', 'PPC\Transaction\CuttingScheduleController@getCutSchedDetails')
			// 	->name('transaction.cutting-schedule.cut-sched-details');
			// Route::post('/save', 'PPC\Transaction\CuttingScheduleController@save')
			// 	->name('transaction.cutting-schedule.save');
			
		});
	});

	Route::group(['prefix' => 'reports'], function () {
		Route::get('/travel-sheet-status', 'PPC\Reports\TravelSheetStatusController@index')
			->name('reports.travel-sheet-status');

		Route::get('/transfer-item', 'PPC\Reports\TransferItemController@index')
			->name('reports.transfer-item');
		Route::get('/transfer-item/get-TransferEntry', 'PPC\Reports\TransferItemController@getTransferEntry')
			->name('reports.transfer-item.get-TransferEntry');

		Route::get('/summary-report', 'PPC\Reports\SummaryReportController@index')
			->name('reports.summary-report');

		Route::get('/fg-summary', 'PPC\Reports\FGSummaryController@index')
			->name('reports.fg-summary');
		Route::get('/fg-summary/get-FG', 'PPC\Reports\FGSummaryController@getFG')
			->name('reports.fg-summary.get-FG');
		Route::get('/fg-summary/get-sc-no', 'PPC\Reports\FGSummaryController@get_sc_no')
			->name('reports.fg-summary.get-sc-no');
		Route::post('/fg-summary/save', 'PPC\Reports\FGSummaryController@save_sc_no')
			->name('reports.fg-summary.save');
	});

	Route::group(['prefix' => 'for-approval'], function () {
		Route::get('/', 'PPC\ApprovalController@index')->name('for-approval');
		Route::get('/transfer-items', 'PPC\ApprovalController@getTransferItems')->name('for-approval.transfer-items');
		Route::post('/answer', 'PPC\ApprovalController@answerToRequest')->name('for-approval.answer');
	});
});

Route::group(['prefix' => 'prod', 'middleware' => ['ajax-session-expired', 'auth', 'deleted_user', 'production','no.back',]], function () {
	Route::group(['prefix' => 'dashboard'], function () {
		Route::get('/', 'Production\DashboardController@index')
			->name('prod.dashboard');
		Route::get('/details-list', 'Production\DashboardController@details_list')
			->name('prod.dashboard.details-list');
		Route::get('/summary-list', 'Production\DashboardController@summary_list')
			->name('prod.dashboard.summary-list');

		Route::get('/get_process', 'Production\DashboardController@get_process')
			->name('prod.dashboard.get_process');
		Route::get('/getDashBoardURL', 'Production\DashboardController@getDashBoardURL')
			->name('prod.dashboard.getDashBoardURL');
	});

	Route::group(['prefix' => 'production-output'], function () {
		Route::get('/', 'Production\Transaction\ProductionOutputController@index')
			->name('prod.production-output');
		Route::get('/get-output', 'Production\Transaction\ProductionOutputController@get_outputs')
			->name('prod.production-output.get-oputput');
		Route::post('/create', 'Production\Transaction\ProductionOutputController@store')
			->name('prod.production-output.create');
		Route::post('/destroy', 'Production\Transaction\ProductionOutputController@destroy')
			->name('prod.production-output.destroy');
		Route::post('/search-jo', 'Production\Transaction\ProductionOutputController@SearchJo')
			->name('prod.production-output.search-jo');
		Route::post('/get-Operator', 'Production\Transaction\ProductionOutputController@getOperator')
			->name('prod.production-output.get-Operator');
		Route::post('/check-Sequence', 'Production\Transaction\ProductionOutputController@checkSequence')
			->name('prod.production-output.check-Sequence');
		Route::post('/get-TransferQty', 'Production\Transaction\ProductionOutputController@getTransferQty')
			->name('prod.production-output.get-TransferQty');
	});

	Route::group(['prefix' => 'transfer-item'], function () {
		Route::get('/', 'Production\Transaction\TransferItemController@index')
			->name('prod.transfer-item');
		Route::get('/get-jo', 'Production\Transaction\TransferItemController@getJOdetails')
			->name('prod.get-jo');
		Route::get('/get-transfer-entry', 'Production\Transaction\TransferItemController@getTransferEntry')
			->name('prod.get-transfer-entry');
		Route::get('/get-output', 'Production\Transaction\TransferItemController@get_outputs')
			->name('prod.transfer-item.get-output');
		Route::get('/received_items', 'Production\Transaction\TransferItemController@received_items')
			->name('prod.transfer-item.received_items');
		Route::post('/destroy', 'Production\Transaction\TransferItemController@destroy')
			->name('prod.transfer-item.destroy');
		Route::post('/save', 'Production\Transaction\TransferItemController@save')
			->name('prod.transfer-item.save');
		Route::get('/div-code-process', 'Production\Transaction\TransferItemController@getDivCodeProcess')
			->name('prod.transfer-item.getDivCodeProcess');
		Route::get('/getDivisionCode', 'Production\Transaction\TransferItemController@DivisionCode')
			->name('prod.transfer-item.getDivisionCode');
		Route::post('/getCurrentGood', 'Production\Transaction\TransferItemController@getGood')
			->name('prod.transfer-item.getCurrentGood');
		Route::post('/get-unprocessed', 'Production\Transaction\TransferItemController@unprocessedItem')
			->name('prod.transfer-item.get-unprocessed');
		Route::post('/receive-process', 'Production\Transaction\TransferItemController@receiveProcess')
			->name('prod.transfer-item.receive-process');
	});

	Route::group(['prefix' => 'receive-item'], function () {
		Route::get('/get-receive-items', 'Production\Transaction\TransferItemController@received_items')
			->name('prod.receive-item.received_items');
	});

	Route::group(['prefix' => 'reports'], function () {
		Route::get('/operators-output', 'Production\Reports\OperatorsOutputController@index')
			->name('prod.reports.operators-output');
		Route::post('/operators-output/search_operator', 'Production\Reports\OperatorsOutputController@search_operator')
			->name('prod.reports.operators-output.search_operator');
		Route::get('/operators-output/downloadExcel', 'Production\Reports\OperatorsOutputController@downloadExcel')
			->name('prod.reports.operators-output.downloadExcel');

		Route::get('/summary-report', 'Production\Reports\SummaryReportController@index')
			->name('prod.reports.production-summary-report');
		Route::post('/summary-report/search_summart_report', 'Production\Reports\SummaryReportController@search_summart_report')
			->name('prod.reports.production-summary-report.search_summart_report');
		Route::get('/summary-report/downloadExcel', 'Production\Reports\SummaryReportController@downloadExcel')
			->name('prod.reports.production-summary-report.downloadExcel');
	});
});

Route::group(['prefix' => 'admin', 'middleware' => ['ajax-session-expired', 'auth', 'deleted_user', 'admin', 'no.back']], function () {

	Route::group(['prefix' => 'user-type'], function () {
		Route::get('/', 'Admin\UserTypeController@index')
			->name('admin.user-type');
		Route::get('/list', 'Admin\UserTypeController@list')
			->name('admin.user-type.list');
		Route::get('/module-list', 'Admin\UserTypeController@module_list')
			->name('admin.user-type.module-list');
		Route::post('/save', 'Admin\UserTypeController@save')
			->name('admin.user-type.save');
		Route::post('/destroy', 'Admin\UserTypeController@destroy')
			->name('admin.user-type.destroy');
	});

	Route::group(['prefix' => 'user-master'], function () {
		Route::get('/', 'Admin\UserController@index')
			->name('admin.user-master');

		Route::get('/list', 'Admin\UserController@user_list')
			->name('admin.user-master.list');

		Route::get('/create', 'Admin\UserController@create')
			->name('admin.user-master.create');

		Route::get('/{id}', 'Admin\UserController@show')
			->name('admin.user-master.show');

		Route::post('/save', 'Admin\UserController@save')
			->name('admin.user-master.save');

		Route::post('/destroy', 'Admin\UserController@destroy')
			->name('admin.user-master.destroy');

		// Route::post('/div-code', 'Admin\UserController@div_code')
		// 	->name('admin.user-master.div_code');

		
	});

	Route::get('/users-type-users', 'Admin\UserController@getUsersType')
		->name('admin.users-type-users');

	Route::get('/div-code-users', 'Admin\UserController@getDivCode')
		->name('admin.div-code-users');

	Route::get('/user-mod', 'Admin\UserController@user_modules')
		->name('admin.user-mod');

	Route::group(['prefix' => 'audit-trail'], function () {
		Route::get('/', 'Admin\AuditTrailController@index')->name('admin.audit-trail');
		Route::get('/get-data', 'Admin\AuditTrailController@getAllAuditTrail')
			->name('admin.audit-trail.get-data');
	});

	Route::group(['prefix' => 'assign-production-line'], function () {
		Route::get('/', 'Admin\AssignProductionLineController@index')
			->name('admin.assign-production-line');
		Route::get('/users', 'Admin\AssignProductionLineController@users')
			->name('admin.assign-production-line.users');
		Route::get('/list', 'Admin\AssignProductionLineController@productline_list')
			->name('admin.assign-production-line.list');
		Route::post('/save', 'Admin\AssignProductionLineController@save')
			->name('admin.assign-production-line.save');
		Route::post('/destroy', 'Admin\AssignProductionLineController@destroy')
			->name('admin.assign-production-line.destroy');
		Route::get('/productline-select', 'Admin\AssignProductionLineController@productline_selection')
			->name('admin.assign-production-line.productline-select');
	});

	Route::group(['prefix' => 'assign-material-type'], function () {
		Route::get('/', 'Admin\AssignMaterialTypeController@index')
			->name('admin.assign-material-type');
		Route::get('/users', 'Admin\AssignMaterialTypeController@users')
			->name('admin.assign-material-type.users');
		Route::get('/list', 'Admin\AssignMaterialTypeController@materialtype_list')
			->name('admin.assign-material-type.list');
		Route::post('/save', 'Admin\AssignMaterialTypeController@save')
			->name('admin.assign-material-type.save');
		Route::post('/destroy', 'Admin\AssignMaterialTypeController@destroy')
			->name('admin.assign-material-type.destroy');
		Route::get('/materialtype-select', 'Admin\AssignMaterialTypeController@material_type_selection')
			->name('admin.assign-material-type.materialtype-select');
	});

	Route::group(['prefix' => 'assign-warehouse'], function () {
		Route::get('/', 'Admin\AssignWarehouseController@index')
			->name('admin.assign-warehouse');
		Route::get('/users', 'Admin\AssignWarehouseController@users')
			->name('admin.assign-warehouse.users');
		Route::get('/list', 'Admin\AssignWarehouseController@warehouse_list')
			->name('admin.assign-warehouse.list');
		Route::post('/save', 'Admin\AssignWarehouseController@save')
			->name('admin.assign-warehouse.save');
		Route::post('/destroy', 'Admin\AssignWarehouseController@destroy')
			->name('admin.assign-warehouse.destroy');
		Route::get('/warehouse-select', 'Admin\AssignWarehouseController@warehouse_selection')
			->name('admin.assign-warehouse.warehouse-select');
	});

	Route::group(['prefix' => 'settings'], function () {
		Route::get('/', 'Admin\SettingsController@index')
			->name('admin.settings');
		Route::get('/getISOTable', 'Admin\SettingsController@getISOTable')
			->name('admin.settings.getISOTable');
		Route::post('/save', 'Admin\SettingsController@save')
			->name('admin.settings.save');
		Route::post('/destroy', 'Admin\SettingsController@destroy')
			->name('admin.settings.destroy');
	});
});

Route::group(['prefix' => 'helpers'], function () {
	Route::get('/dropdown-item-id', 'HelpersController@getDropdownItemByID')->name('helpers-dropwdown-items-id');
	Route::get('/dropdown-item-name', 'HelpersController@getDropdownItemByName')->name('helpers-dropwdown-items-name');
	Route::get('/user-type', 'HelpersController@getUserType')->name('helpers.user-type');
	Route::get('/div-code', 'HelpersController@getDivisionCode')->name('helpers.div-code');
	Route::get('/leader', 'HelpersController@getLeader')->name('helpers.leader');
	Route::get('/check-permission', 'HelpersController@check_permission')->name('helpers.check-permission');
	Route::get('/iso', 'HelpersController@getISO')->name('helpers.iso');
	Route::get('/getall-operators', 'HelpersController@getAllOperators')->name('helpers.getall-operators');
});

Route::group(['prefix' => 'pdf'], function () {
	Route::get('raw-material-withdrawal-slip', 'PDFController@RawMaterialWithdrawalSlip')->name('pdf.raw-material-withdrawal-slip');
	Route::get('product-withdrawal-slip', 'PDFController@ProductWithdrawalSlip')->name('pdf.product-withdrawal-slip');
	Route::get('cutting-schedule', 'PDFController@CuttingSchedule')->name('pdf.cutting-schedule');
	Route::get('cutting-schedule-reprint', 'PDFController@CuttingScheduleReprint')->name('pdf.cutting-schedule-reprint');
	Route::get('travel-sheet', 'PDFController@TravelSheet')->name('pdf.travel-sheet');
});

Route::group(['prefix' => 'profile', 'middleware' => ['auth', 'no.back', 'deleted_user']], function () {
	Route::get('/user/{user_id}', 'ProfileController@index');
	Route::get('/timeline', 'ProfileController@getActivity');
});

Route::group(['prefix' => 'notification', 'middleware' => ['auth', 'no.back', 'deleted_user']], function () {
	Route::get('/', 'NotificationController@index');
	Route::get('/get-unread', 'NotificationController@getUnreadNotification');
	Route::post('/read', 'NotificationController@readNotification');
	Route::get('/all', 'NotificationController@all');
});
