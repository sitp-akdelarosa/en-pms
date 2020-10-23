/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 14);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/assets/js/pages/ppc/transactions/update-inventory/update-inventory.js":
/*!*****************************************************************************************!*\
  !*** ./resources/assets/js/pages/ppc/transactions/update-inventory/update-inventory.js ***!
  \*****************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

var _with_zero = 0;
$(function () {
  getProdLine('');
  getMaterials('');
  getInventory(_with_zero);
  init(); //$('#srch_received_date').daterangepicker();

  $(document).on('shown.bs.modal', function () {
    $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
  });
  $('#item_class').on('change', function () {
    if ($(this).val() == 'RAW MATERIAL') {
      $('.product_div').hide();
      $('.material_div').show();
      $('#materials_type').select2("val", "");
    } else {
      $("#product_line").select2("val", "");
      $('.product_div').show();
      $('.material_div').hide();
    }

    hideErrors('item_code');
  });
  $('#srch_item_class').on('change', function () {
    if ($(this).val() == 'RAW MATERIAL') {
      $('.srch_product_div').hide();
      $('.srch_material_div').show();
      $("#srch_material_type").select2("val", "");
    } else {
      $("#srch_product_line").select2("val", "");
      $('.srch_product_div').show();
      $('.srch_material_div').hide();
    }

    hideErrors('srch_item_code');
  });
  $("#product_line").on('change', function () {
    var $class = 'finish';

    if ($('#item_class').val() == 'CRUDE') {
      $class = 'crude';
    }

    getItemCode('', '', $class);
  });
  $("#srch_product_line").on('change', function () {
    var $class = 'finish';

    if ($('#srch_item_class').val() == 'CRUDE') {
      $class = 'crude';
    }

    getItemCode('', 'search', $class);
  });
  $("#materials_type").on('change', function () {
    getItemCode('', '', 'material');
  });
  $("#srch_materials_type").on('change', function () {
    getItemCode('', 'search', 'material');
  });
  $("#item_code").on('change', function () {
    var $type = 'product';

    if ($('#item_class').val() == 'RAW MATERIAL') {
      $type = 'material';
    }

    getItemCodeDetails('', $type);
  });
  $("#srch_item_code").on('change', function () {
    var $type = 'product';

    if ($('#item_class').val() == 'RAW MATERIAL') {
      $type = 'material';
    }

    getItemCodeDetails('search', $type);
  });
  $('.custom-file-input').on('change', function () {
    var fileName = $(this).val().split('\\').pop();
    $(this).next('.custom-file-label').addClass("selected").html(fileName);
  });
  $('#btn_add').on('click', function () {
    var d = new Date();
    var month = d.getMonth() + 1;
    var day = d.getDate();
    var date = (month < 10 ? '0' : '') + month + '/' + (day < 10 ? '0' : '') + day + '/' + d.getFullYear();
    $('#received_date').val(date);
    $('#qty_weight').val(0);
    $('#qty_pcs').val(0);
    $('#item_class').val('RAW MATERIAL');
    $('#modal_material_inventory').modal('show');
    clear();
    getItemCode('');
  });
  $('#btn_zero').on('click', function () {
    if (_with_zero == 0) {
      _with_zero = 1;
      getInventory(_with_zero);
      $(this).removeClass('bg-blue');
      $(this).addClass('bg-red');
      $(this).html('Exclude 0 quantity');
    } else {
      _with_zero = 0;
      getInventory(_with_zero);
      $(this).removeClass('bg-red');
      $(this).addClass('bg-blue');
      $(this).html('Include 0 quantity');
    }
  });
  $('#tbl_materials_body').on('click', '.edit-mainEdit', function (e) {
    e.preventDefault();
    $('#item_id').val($(this).attr('data-id'));
    $('#item_class').val($(this).attr('data-item_class')).trigger('change');

    if ($(this).attr('data-item_class') == 'RAW MATERIAL') {
      $('#receiving_no').val($(this).attr('data-receive_jo_no'));
      $('#received_date').val($(this).attr('data-received_date'));
      $('#materials_type').val($(this).attr('data-item_type_line')).trigger('change.select2');
      getItemCode($(this).attr('data-item_code'), '', 'material');
      $('#width').val($(this).attr('data-width'));
      $('#length').val($(this).attr('data-length'));
      $('#invoice_no').val($(this).attr('data-invoice_no'));
      $('#supplier').val($(this).attr('data-supplier'));
      $('#supplier_heat_no').val($(this).attr('data-supplier_heat_no'));
    } else {
      $('#jo_no').val($(this).attr('data-receive_jo_no'));
      $('#product_line').val($(this).attr('data-item_type_line')).trigger('change.select2');
      getItemCode($(this).attr('data-item_code'), '', 'product');
      $('#lot_no').val($(this).attr('data-lot_no'));
      $('#finish_weight').val($(this).attr('data-finish_weight'));
    }

    $('#description').val($(this).attr('data-description'));
    $('#item').val($(this).attr('data-item'));
    $('#alloy').val($(this).attr('data-alloy'));
    $('#schedule').val($(this).attr('data-schedule'));
    $('#size').val($(this).attr('data-size'));
    $('#qty_weight').val($(this).attr('data-qty_weight'));
    $('#qty_pcs').val($(this).attr('data-qty_pcs'));
    $('#heat_no').val($(this).attr('data-heat_no'));
    $('#modal_material_inventory').modal('show');
  });
  $('#frm_update_inventory').on('submit', function (e) {
    var formObj = $('#frm_update_inventory');
    var formURL = formObj.attr("action");
    var formData = new FormData(this);
    var fileName = $("#file_inventory").val();
    var ext = fileName.split('.').pop();
    var pros = $('#file_inventory').val().replace("C:\fakepath", "");
    var fileN = pros.substring(12, pros.length);
    e.preventDefault(); //Prevent Default action.

    if ($("#file_inventory").val() == '') {
      msg("No File", "failed");
    } else if ($("#up_item_class").val() == '') {
      msg("Please select an Item Class", "failed");
    } else {
      $('.loadingOverlay').show();

      if (fileName != '') {
        if (ext == 'xls' || ext == 'xlsx' || ext == 'XLS' || ext == 'XLSX') {
          $('.myprogress').css('width', '0%');
          $('#progress-msg').html('Uploading in progress...');
          var percent = 0; // check file

          $.ajax({
            url: checkfile,
            type: 'POST',
            mimeType: "multipart/form-data",
            contentType: false,
            cache: false,
            processData: false,
            data: formData
          }).done(function (returns, textStatus, xhr) {
            var return_datas = jQuery.parseJSON(returns);

            if (return_datas.status == "success") {
              formData.append("item_class", return_datas.item_class); // upload file

              $.ajax({
                url: uploadInventory,
                type: 'POST',
                data: formData,
                mimeType: "multipart/form-data",
                contentType: false,
                cache: false,
                processData: false
              }).done(function (returnData, textStatus, xhr) {
                $('.loadingOverlay').hide();
                var return_data = jQuery.parseJSON(returnData);
                msg(return_data.msg, return_data.status);
                document.getElementById('file_inventory_label').innerHTML = fileN;
                getInventory(_with_zero);
                var not_registedred = return_data.Material;

                if (not_registedred.length > 0) {
                  GetMateriialsNotExisting(not_registedred);
                }
              }).fail(function (xhr, textStatus, errorThrown) {
                $('.loadingOverlay').hide();
                ErrorMsg(xhr);
              }).always(function () {
                $('.loadingOverlay').hide();
              }); // $.ajax({
              // 	url: uploadInventory,
              // 	type: 'POST',
              // 	data: formData,
              // 	mimeType: "multipart/form-data",
              // 	contentType: false,
              // 	cache: false,
              // 	processData: false,
              // 	success: function (returnData) {
              // 		$('.loadingOverlay').hide();
              // 		var return_data = jQuery.parseJSON(returnData);
              // 		msg(return_data.msg, return_data.status);
              // 		document.getElementById('file_inventory_label').innerHTML = fileN;
              // 		getInventory(_with_zero);
              // 		var not_registedred = return_data.Material;
              // 		if (not_registedred.length > 0) {
              // 			GetMateriialsNotExisting(not_registedred);
              // 		}
              // 	}
              // });
            } else if (return_datas.status == "validateRequired") {
              $('.loadingOverlay').hide();
              msg("Fill up correctly the record in line " + return_datas.line, "warning");
              document.getElementById('file_inventory_label').innerHTML = "Select file...";
            } else if (return_datas.status == "heatnumber error") {
              $('.loadingOverlay').hide();
              msg(return_datas.msg, "warning");
              document.getElementById('file_inventory_label').innerHTML = "Select file...";
            } else if (return_datas.status == "not num") {
              $('.loadingOverlay').hide();
              msg("Invalid input of Quantity", "warning");
              document.getElementById('file_inventory_label').innerHTML = "Select file...";
            } else if (return_datas.status == "failed") {
              $('.loadingOverlay').hide(); //msg(return_datas.msg,return_datas.status);

              if (return_datas.msg == '') {
                msg("Please maintain data as 1 sheet only.", "warning");
              } else {
                msg(return_datas.msg, "warning");
              }

              document.getElementById('file_inventory_label').innerHTML = "Select file...";
            } else if (return_datas.status == "same_items") {
              $('.loadingOverlay').hide();
              console.log(return_datas.same_items);
              sameMaterialTable(return_datas.same_items);
              msg(return_datas.msg, "warning");
              $('#modal_same_material').modal('show');
              document.getElementById('file_inventory_label').innerHTML = "Select file...";
            } else {
              $('.loadingOverlay').hide();
              msg("Upload failed", "warning");
              document.getElementById('file_inventory_label').innerHTML = "Select file...";
            }
          }).fail(function (xhr, textStatus, errorThrown) {
            $('.loadingOverlay').hide();
            ErrorMsg(xhr);
          }).always(function () {//$('.loadingOverlay').hide();
          }); // $.ajax({
          // 	url: checkfile,
          // 	type: 'POST',
          // 	mimeType: "multipart/form-data",
          // 	contentType: false,
          // 	cache: false,
          // 	processData: false,
          // 	data: formData,
          // 	success: function (returns) {
          // 		var return_datas = jQuery.parseJSON(returns);
          // 		if (return_datas.status == "success") {
          // 			$.ajax({
          // 				url: uploadInventory,
          // 				type: 'POST',
          // 				data: formData,
          // 				mimeType: "multipart/form-data",
          // 				contentType: false,
          // 				cache: false,
          // 				processData: false,
          // 				success: function (returnData) {
          // 					$('.loadingOverlay').hide();
          // 					var return_data = jQuery.parseJSON(returnData);
          // 					msg(return_data.msg, return_data.status);
          // 					document.getElementById('file_inventory_label').innerHTML = fileN;
          // 					getInventory(_with_zero);
          // 					var not_registedred = return_data.Material;
          // 					if (not_registedred.length > 0) {
          // 						GetMateriialsNotExisting(not_registedred);
          // 					}
          // 				}
          // 			});
          // 		}
          // 		else if (return_datas.status == "validateRequired") {
          // 			$('.loadingOverlay').hide();
          // 			msg("Fill up correctly the record in line " + return_datas.line, "warning");
          // 			document.getElementById('file_inventory_label').innerHTML = "Select file...";
          // 		}
          // 		else if (return_datas.status == "heatnumber error") {
          // 			$('.loadingOverlay').hide();
          // 			msg(return_datas.msg, "warning");
          // 			document.getElementById('file_inventory_label').innerHTML = "Select file...";
          // 		}
          // 		else if (return_datas.status == "not num") {
          // 			$('.loadingOverlay').hide();
          // 			msg("Invalid input of Quantity", "warning");
          // 			document.getElementById('file_inventory_label').innerHTML = "Select file...";
          // 		}
          // 		else if (return_datas.status == "failed") {
          // 			$('.loadingOverlay').hide();
          // 			console.log(return_datas.fields);
          // 			msg("Please maintain data as 1 sheet only.", "warning");
          // 			document.getElementById('file_inventory_label').innerHTML = "Select file...";
          // 		}
          // 		else {
          // 			$('.loadingOverlay').hide();
          // 			msg("Upload failed", "warning");
          // 			document.getElementById('file_inventory_label').innerHTML = "Select file...";
          // 		}
          // 	},
          // 	statusCode: {
          // 		500: function(data) {
          // 			$('.loadingOverlay').hide();
          // 			console.log(data);
          // 			//msg('','error');
          // 		}
          // 	}
          // 	// error: function() {
          // 	// 	$('.loadingOverlay').hide();
          // 	// }
          // });
        } else {
          $('.loadingOverlay').hide();
          msg("File Format not supported.", "warning");
        }
      }
    }
  });
  $("#frm_material_inventory").on('submit', function (e) {
    e.preventDefault();

    if ($('#quantity').val() < 0 || $('#quantity').val() == 0) {
      showErrors({
        quantity: ["Please Input numbers greater than 0."]
      });
    } else {
      $('.loadingOverlay-modal').show();
      $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        dataType: 'JSON',
        data: $(this).serialize()
      }).done(function (data, textStatus, xhr) {
        msg(data.msg, data.status);
        $('#modal_material_inventory').modal('hide');
        getInventory(_with_zero);
      }).fail(function (xhr, textStatus, errorThrown) {
        var errors = xhr.responseJSON.errors;
        console.log(errors);
        showErrors(errors);
      }).always(function () {
        $('.loadingOverlay-modal').hide();
      });
    }
  });
  $('#qty_weight').on('change', function () {
    if ($(this).val() !== '') {
      hideErrors($(this).attr('id'));
    }
  });
  $('#qty_pcs').on('change', function () {
    if ($(this).val() !== '') {
      hideErrors($(this).attr('id'));
      var finished_weight = 0;

      if ($('#item_class').val() !== 'RAW MATERIAL') {
        finished_weight = parseFloat($(this).val()) * parseFloat($('#finish_weight').val());
        $('#qty_weight').val(finished_weight.toFixed(2));
      }

      console.log(finished_weight);
    }
  });
  $('#btn_excel').on('click', function () {
    window.location.href = downloadNonexistingURL;
  });
  $('#btn_download_format').on('click', function () {
    $('#modal_excel_format').modal('show'); //window.location.href = downloadFormatURL;
  });
  $('#btn_material_format').on('click', function () {
    window.location.href = downloadMaterialFormatURL;
  });
  $('#btn_product_format').on('click', function () {
    window.location.href = downloadProductFormatURL;
  });
  $('#btn_check_unregistered').on('click', function (event) {
    $.ajax({
      url: getNonexistingURL,
      type: 'GET',
      dataType: 'JSON'
    }).done(function (data, textStatus, xhr) {
      GetMateriialsNotExisting(data);
    }).fail(function (xhr, textStatus, errorThrown) {
      msg('Unregistered Products: ' + errorThrown);
    });
  });
  $('#received_date').on('change', function () {
    if ($(this).val() !== "") {
      hideErrors($(this).attr('id'));
    }
  });
  $('#btn_search_filter').on('click', function () {
    $('.srch-clear').val('');
    getMaterials('search');
    getProdLine('search');
    $('#modal_search').modal('show');
  });
  $('#srch_received_date_from').on('change', function () {
    var selected_date = new Date($(this).val()).toISOString().split('T')[0];
    document.getElementsByName("srch_received_date_to")[0].setAttribute('min', selected_date);
  });
  $("#frm_search").on('submit', function (e) {
    e.preventDefault();
    $('.loadingOverlay-modal').show();
    $.ajax({
      url: $(this).attr('action'),
      type: 'GET',
      dataType: 'JSON',
      data: $(this).serialize()
    }).done(function (data, textStatus, xhr) {
      //msg(data.msg, data.status);
      InventoryTable(data); //$('#modal_material_search').modal('hide');
    }).fail(function (xhr, textStatus, errorThrown) {
      var errors = xhr.responseJSON.errors;
      console.log(errors);
      showErrors(errors);
    }).always(function () {
      $('.loadingOverlay-modal').hide();
    });
  });
  $('#btn_search_excel').on('click', function () {
    var srch_item_class = $('#srch_item_class').val();
    var srch_received_date_from = $('#srch_received_date_from').val();
    var srch_received_date_to = $('#srch_received_date_to').val();
    var srch_receiving_no = $('#srch_receiving_no').val();
    var srch_jo_no = $('#srch_jo_no').val();
    var srch_materials_type = $('#srch_materials_type').val();
    var srch_product_line = $('#srch_product_line').val();
    var srch_item_code = $('#srch_item_code').val();
    var srch_item = $('#srch_item').val();
    var srch_alloy = $('#srch_alloy').val();
    var srch_schedule = $('#srch_schedule').val();
    var srch_size = $('#srch_size').val();
    var srch_width = $('#srch_width').val();
    var srch_length = $('#srch_length').val();
    var srch_heat_no = $('#srch_heat_no').val();
    var srch_lot_no = $('#srch_lot_no').val();
    var srch_invoice_no = $('#srch_invoice_no').val();
    var srch_supplier = $('#srch_supplier').val();
    var srch_supplier_heat_no = $('#srch_supplier_heat_no').val();
    var url = downloadSearchExcelURL + '?srch_item_class=' + srch_item_class + '&srch_received_date_from=' + srch_received_date_from + '&srch_received_date_to=' + srch_received_date_to + '&srch_receiving_no=' + srch_receiving_no + '&srch_jo_no=' + srch_jo_no + '&srch_materials_type=' + srch_materials_type + '&srch_product_line=' + srch_product_line + '&srch_item_code=' + srch_item_code + '&srch_item=' + srch_item + '&srch_alloy=' + srch_alloy + '&srch_schedule=' + srch_schedule + '&srch_size=' + srch_size + '&srch_width=' + srch_width + '&srch_length=' + srch_length + '&srch_heat_no=' + srch_heat_no + '&srch_lot_no=' + srch_lot_no + '&srch_invoice_no=' + srch_invoice_no + '&srch_supplier=' + srch_supplier + '&srch_supplier_heat_no=' + srch_supplier_heat_no;
    window.location.href = url;
  });
});

function init() {
  check_permission(code_permission, function (output) {
    if (output == 1) {}
  });
  $('#product_line_div').hide();
  $('#materials_type_div').hide();
}

function getMaterials(state) {
  $('.loadingOverlay-modal').show();
  $.ajax({
    url: materialTypeURL,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token
    }
  }).done(function (data, textStatus, xhr) {
    var mat_type = $('#materials_type');

    if (state == 'search') {
      mat_type = $('#srch_materials_type');
    }

    mat_type.select2({
      allowClear: true,
      placeholder: 'Select a Material Type',
      width: 'resolve',
      data: data
    }).val(null).trigger('change'); // var code = '<option></option>';
    // mat_type.html(code);
    // $.each(data.type, function (i, x) {
    // 	code = '<option value="' + x.material_type + '">' + x.material_type + '</option>';
    // 	mat_type.append(code);
    // });
  }).fail(function (xhr, textStatus, errorThrown) {
    msg(errorThrown, textStatus);
  }).always(function () {
    $('.loadingOverlay-modal').hide();
  });
}

function getProdLine(state) {
  $('.loadingOverlay-modal').show();
  $.ajax({
    url: productLineURL,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token
    }
  }).done(function (data, textStatus, xhr) {
    var prod_line = $('#product_line');

    if (state == 'search') {
      prod_line = $('#srch_product_line');
    }

    prod_line.select2({
      allowClear: true,
      placeholder: 'Select a Product Line',
      width: 'resolve',
      data: data
    }).val(null).trigger('change'); // var code = '<option></option>';
    // type.html(code);
    // $.each(data.type, function (i, x) {
    // 	code = '<option value="' + x.product_line + '">' + x.product_line + '</option>';
    // 	type.append(code);
    // });
  }).fail(function (xhr, textStatus, errorThrown) {
    msg(errorThrown, textStatus);
  }).always(function () {
    $('.loadingOverlay-modal').hide();
  });
}

function getItemCode(code, state, item_class) {
  var hideErr = 'item_code';
  var type = $('#product_line');
  var item_code = $('#item_code'); //var code = "<option value=''></option>";

  if (item_class == 'material') {
    type = $('#materials_type');
  }

  if (state == 'search') {
    type = $('#srch_product_line');
    item_code = $('#srch_item_code');
    hideErr = 'srch_item_code';

    if (item_class == 'material') {
      type = $('#srch_materials_type');
    }
  }

  hideErrors(hideErr); //item_code.html(code);

  $('.loadingOverlay-modal').show();
  $.ajax({
    url: GetItemCodeURL,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token,
      type: type.val(),
      item_class: item_class
    }
  }).done(function (data, textStatus, xhr) {
    item_code.empty();

    if (data.length > 0) {
      // $.each(data, function (i, x) {
      // 	code = "<option value='" + x.item_code + "'>" + x.item_code + "</option>";
      // 	item_code.append(code);
      // });
      item_code.select2({
        allowClear: true,
        placeholder: 'Select an Item Code',
        width: 'resolve',
        data: data
      }).val(code).trigger('change.select2');
    } else {
      if (type.val() !== '') {
        if (state == 'search') {
          if (data.length < 1) {
            showErrors({
              srch_item_code: ["No Item Code registered to " + type.val()]
            });
          } else {
            hideErrors('srch_item_code');
          }
        } else {
          if (data.length < 1) {
            showErrors({
              item_code: ["No Item Code registered to " + type.val()]
            });
          } else {
            hideErrors('item_code');
          }
        }
      }
    }
  }).fail(function (xhr, textStatus, errorThrown) {
    msg(errorThrown, textStatus);
  }).always(function () {
    $('.loadingOverlay-modal').hide();
  });
}

function getItemCodeDetails(state, item_class) {
  $('.loadingOverlay-modal').show();
  var item_code = $('#item_code');

  if (state == 'search') {
    item_code = $('#srch_item_code');
  }

  $.ajax({
    url: getItemCodeDetailsurl,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token,
      item_code: item_code.val(),
      item_class: item_class
    }
  }).done(function (data, textStatus, xhr) {
    if (state == 'search') {
      $('#srch_item').val(data.item);
      $('#srch_alloy').val(data.alloy);
      $('#srch_schedule').val(data.schedule);
      $('#srch_size').val(data.size);
    } else {
      $('#description').val(data.code_description);
      $('#item').val(data.item);
      $('#alloy').val(data.alloy);
      $('#schedule').val(data.schedule);
      $('#size').val(data.size);

      if (data.hasOwnProperty('finish_weight')) {
        $('#finish_weight').val(data.finish_weight);
      }

      $('#qty_weight').val(0);
      $('#qty_pcs').val(0);
    } //else {
    // 	msg("Material Code is not registered");
    // }

  }).fail(function (xhr, textStatus, errorThrown) {
    msg(errorThrown, textStatus);
  }).always(function () {
    $('.loadingOverlay-modal').hide();
  });
}

function GetMateriialsNotExisting(arr) {
  $('#modal_material_not_existing').modal('show');
  $('#tbl_material_not_existing').dataTable().fnClearTable();
  $('#tbl_material_not_existing').dataTable().fnDestroy();
  $('#tbl_material_not_existing').dataTable({
    data: arr,
    bLengthChange: false,
    paging: true,
    scrollX: true,
    order: [[5, 'asc']],
    columns: [{
      data: 'item_class',
      name: 'item_class'
    }, {
      data: 'receive_jo_no',
      name: 'receive_jo_no'
    }, {
      data: 'item_type_line',
      name: 'item_type_line'
    }, {
      data: 'item_code',
      name: 'item_code'
    }, {
      data: 'qty_weight',
      name: 'qty_weight'
    }, {
      data: 'qty_pcs',
      name: 'qty_pcs'
    }, {
      data: 'heat_no',
      name: 'heat_no'
    }, {
      data: 'lot_no',
      name: 'lot_no'
    }, {
      data: 'invoice_no',
      name: 'invoice_no'
    }, {
      data: 'received_date',
      name: 'received_date'
    }, {
      data: 'supplier',
      name: 'supplier'
    }]
  });
}

function getInventory(with_zero) {
  $('.loadingOverlay').show();
  $.ajax({
    url: materialDataTable,
    type: 'GET',
    dataType: 'JSON',
    data: {
      with_zero: with_zero
    }
  }).done(function (data, textStatus, xhr) {
    InventoryTable(data);
  }).fail(function (xhr, textStatus, errorThrown) {
    console.log("error");
  }).always(function () {
    $('.loadingOverlay').hide();
  });
}

function clear() {
  $('.clear').val('');
}

function InventoryTable(arr) {
  $('#tbl_materials').dataTable().fnClearTable();
  $('#tbl_materials').dataTable().fnDestroy();
  $('#tbl_materials').dataTable({
    data: arr,
    order: [[24, 'desc']],
    scrollX: true,
    columns: [{
      data: function data(_data) {
        return "<button type='button' name='edit-mainEdit' class='btn btn-sm btn-primary edit-mainEdit'" + "id='editinventory'" + "data-id= '" + _data.id + "' " + "data-item_class='" + _data.item_class + "' " + "data-receive_jo_no='" + _data.receive_jo_no + "' " + "data-item_type_line='" + _data.item_type_line + "' " + "data-item_code='" + _data.item_code + "'" + "data-description='" + _data.description + "'" + "data-item='" + _data.item + "'" + "data-alloy='" + _data.alloy + "'" + "data-schedule='" + _data.schedule + "'" + "data-size='" + _data.size + "'" + "data-std_weight='" + _data.std_weight + "'" + "data-std_weight_received='" + _data.std_weight_received + "'" + "data-finish_weight='" + _data.finish_weight + "'" + "data-qty_weight='" + _data.qty_weight + "'" + "data-qty_pcs='" + _data.qty_pcs + "'" + "data-current_stock='" + _data.current_stock + "'" + "data-heat_no='" + _data.heat_no + "' " + "data-lot_no='" + _data.lot_no + "' " + "data-invoice_no='" + _data.invoice_no + "'" + "data-received_date='" + _data.received_date + "'" + "data-width='" + _data.width + "' " + // "data-thickness='" + data.thickness + "' " +
        "data-length='" + _data.length + "' " + "data-supplier_heat_no='" + _data.supplier_heat_no + "' " + "data-updated_at='" + _data.updated_at + "' " + "data-supplier='" + _data.supplier + "'>" + "<i class='fa fa-edit'></i> " + "</button>";
      },
      searchable: false,
      orderable: false
    }, {
      data: 'item_class'
    }, {
      data: 'receive_jo_no'
    }, {
      data: 'item_type_line'
    }, {
      data: 'item_code'
    }, {
      data: 'description'
    }, {
      data: 'length'
    }, {
      data: function data(x) {
        return x.std_weight == null ? '' : x.std_weight.toFixed(2);
      }
    }, {
      data: 'std_weight_received'
    }, {
      data: 'finish_weight'
    }, {
      data: 'qty_weight'
    }, {
      data: 'qty_pcs'
    }, {
      data: 'current_stock'
    }, {
      data: 'heat_no'
    }, {
      data: 'lot_no'
    }, {
      data: 'item'
    }, {
      data: 'alloy'
    }, {
      data: 'schedule'
    }, {
      data: 'size'
    }, {
      data: 'width'
    }, {
      data: 'invoice_no'
    }, {
      data: 'received_date'
    }, {
      data: 'supplier'
    }, {
      data: 'supplier_heat_no'
    }, {
      data: 'updated_at'
    }],
    createdRow: function createdRow(row, data, dataIndex) {
      if (data.description == "N/A") {
        $(row).css('background-color', '#ff6266');
        $(row).css('color', '#000000');
      }

      if (data.item_class == "RAW MATERIAL") {
        $(row).css('background-color', 'rgb(121 204 241)'); // BLUE

        $(row).css('color', '#000000');
      }

      if (data.item_class == "CRUDE") {
        $(row).css('background-color', '#c6bdff'); // NAVY

        $(row).css('color', '#000000');
      }

      if (data.item_class == "FINISHED") {
        $(row).css('background-color', 'rgb(139 241 191)'); // GREEN

        $(row).css('color', '#000000');
      }
    },
    initComplete: function initComplete() {
      $('.loadingOverlay').hide();
    }
  });
}

function sameMaterialTable(arr) {
  $('#tbl_same_material').dataTable().fnClearTable();
  $('#tbl_same_material').dataTable().fnDestroy();
  $('#tbl_same_material').dataTable({
    data: arr,
    order: [[0, 'asc']],
    scrollX: true,
    columns: [{
      data: 'receiving_no'
    }, {
      data: 'materials_type'
    }, {
      data: 'materials_code'
    }, {
      data: 'qty_weight'
    }, {
      data: 'qty_pcs'
    }, {
      data: 'heat_no'
    }, {
      data: 'length'
    }, {
      data: 'invoice_no'
    }, {
      data: 'received_date'
    }, {
      data: 'supplier'
    }]
  });
}

/***/ }),

/***/ 14:
/*!***********************************************************************************************!*\
  !*** multi ./resources/assets/js/pages/ppc/transactions/update-inventory/update-inventory.js ***!
  \***********************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\laragon\www\en-pms\resources\assets\js\pages\ppc\transactions\update-inventory\update-inventory.js */"./resources/assets/js/pages/ppc/transactions/update-inventory/update-inventory.js");


/***/ })

/******/ });