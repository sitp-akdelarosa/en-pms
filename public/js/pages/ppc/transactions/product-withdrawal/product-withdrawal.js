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
/******/ 	return __webpack_require__(__webpack_require__.s = 21);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/assets/js/pages/ppc/transactions/product-withdrawal/product-withdrawal.js":
/*!*********************************************************************************************!*\
  !*** ./resources/assets/js/pages/ppc/transactions/product-withdrawal/product-withdrawal.js ***!
  \*********************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

var product_arr = [];
var vState = '';
$(function () {
  init();
  $(document).on('shown.bs.modal', function () {
    $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
  });
  $('#btn_clear').on('click', function () {
    clear();
    $('#btn_add').html('<i class="fa fa-plus"></i> Add');
  });
  $('#btn_first').on('click', function () {
    getWithdrawalTransaction('first', $('#trans_no').val());
  });
  $('#btn_prev').on('click', function () {
    getWithdrawalTransaction('prev', $('#trans_no').val());
  });
  $('#btn_next').on('click', function () {
    getWithdrawalTransaction('next', $('#trans_no').val());
  });
  $('#btn_last').on('click', function () {
    getWithdrawalTransaction('last', $('#trans_no').val());
  });
  $('#btn_search_item_code').on('click', function () {
    if ($('#item_class').val() == "") {
      showErrors({
        item_class: ["Please select an Item Class."]
      });
    } else {
      getInventory($('#item_class').val(), $('#item_code').val(), 0, null);
    }
  });
  $('#btn_new').on('click', function () {
    product_arr = [];
    clear();
    $('#trans_no').val('');
    $('#id').val('');
    ProductDataTable(product_arr);
    viewState('ADD');
  });
  $('#btn_edit').on('click', function () {
    viewState('EDIT');
  });
  $('#btn_cancel').on('click', function () {
    clear();
    viewState('');
    getWithdrawalTransaction('', $('#trans_no').val());
  });
  $('#tbl_inventory_body').on('click', '.btn_pick_item', function () {
    $('#inv_id').val($(this).attr('data-id'));
    $('#item_class').val($(this).attr('data-item_class'));
    $('#jo_no').val($(this).attr('data-jo_no'));
    $('#item_code').val($(this).attr('data-item_code'));
    $('#product_line').val($(this).attr('data-product_line'));
    $('#item').val($(this).attr('data-item'));
    $('#alloy').val($(this).attr('data-alloy'));
    $('#size').val($(this).attr('data-size'));
    $('#schedule').val($(this).attr('data-schedule'));
    $('#qty_weight').val($(this).attr('data-qty_weight'));
    $('#inv_qty').val($(this).attr('data-current_stock'));
    $('#heat_no').val($(this).attr('data-heat_no'));
    $('#lot_no').val($(this).attr('data-lot_no'));
    $('#modal_inventory').modal('hide');
  });
  $('#btn_add').on('click', function () {
    var total_pcs = 0;

    if ($('#old_issued_qty').val() == '' || $('#old_issued_qty').val() == null) {
      total_pcs = parseInt($('#inv_qty').val());
    } else {
      total_pcs = parseInt($('#old_issued_qty').val()) + parseInt($('#inv_qty').val());
    }

    $same_code_total_qty = 0; // $.each(product_arr, function(i,x) {
    //     if (x.inv_id == $('#inv_id').val()) {
    //         $same_code_total_qty = (parseInt(x.issued_qty) + parseInt($('#issued_qty').val()));
    //     }
    // });

    if ($('#item_class').val() == '') {
      msg("Please select an Item Class.", "failed");
    } else if ($('#item_code').val() == '') {
      msg("Please select an Item Code.", "failed");
    } else if ($('#lot_no').val() == '' || $('#lot_no').val() == '') {
      msg("Please select an Item Code to populate other details.", "failed");
    } else if ($('#sc_no').val() == '') {
      msg("Please provide SC Number.", "failed");
    } else if (parseInt($('#issued_qty').val()) == 0 || $('#issued_qty').val() == '') {
      msg("Please provide Issuance Quantity.", "failed");
    } else if (parseInt($('#issued_qty').val()) > total_pcs) {
      msg("Please do not withdraw more than " + total_pcs + ".", "failed");
    } else if ($same_code_total_qty > parseInt($('#inv_qty').val())) {
      msg("You are withdrawing total of " + $same_code_total_qty + " for this inventory item.", "failed");
    } else {
      if (parseInt($('#item_count').val()) > 0) {
        var _product_arr$key;

        var key = parseInt($('#item_count').val()) - 1;
        product_arr[key] = (_product_arr$key = {
          count: $('#item_count').val(),
          item_id: $('#item_id').val(),
          inv_id: $('#inv_id').val(),
          item_class: $('#item_class').val(),
          item_code: $('#item_code').val()
        }, _defineProperty(_product_arr$key, "inv_id", $('#inv_id').val()), _defineProperty(_product_arr$key, "old_issued_qty", $('#old_issued_qty').val()), _defineProperty(_product_arr$key, "jo_no", $('#jo_no').val()), _defineProperty(_product_arr$key, "lot_no", $('#lot_no').val()), _defineProperty(_product_arr$key, "heat_no", $('#heat_no').val()), _defineProperty(_product_arr$key, "alloy", $('#alloy').val()), _defineProperty(_product_arr$key, "item", $('#item').val()), _defineProperty(_product_arr$key, "size", $('#size').val()), _defineProperty(_product_arr$key, "schedule", $('#schedule').val()), _defineProperty(_product_arr$key, "remarks", $('#remarks').val()), _defineProperty(_product_arr$key, "sc_no", $('#sc_no').val()), _defineProperty(_product_arr$key, "issued_qty", $('#issued_qty').val()), _defineProperty(_product_arr$key, "create_user", $('#create_user').val()), _defineProperty(_product_arr$key, "update_user", $('#update_user').val()), _defineProperty(_product_arr$key, "created_at", $('#created_at').val()), _defineProperty(_product_arr$key, "updated_at", $('#updated_at').val()), _defineProperty(_product_arr$key, "deleted", 0), _product_arr$key);
        clear();
      } else {
        var _product_arr$push;

        var count = product_arr.length + 1;
        product_arr.push((_product_arr$push = {
          count: count,
          item_id: $('#item_id').val(),
          inv_id: $('#inv_id').val(),
          item_class: $('#item_class').val(),
          item_code: $('#item_code').val()
        }, _defineProperty(_product_arr$push, "inv_id", $('#inv_id').val()), _defineProperty(_product_arr$push, "old_issued_qty", $('#old_issued_qty').val()), _defineProperty(_product_arr$push, "jo_no", $('#jo_no').val()), _defineProperty(_product_arr$push, "lot_no", $('#lot_no').val()), _defineProperty(_product_arr$push, "heat_no", $('#heat_no').val()), _defineProperty(_product_arr$push, "alloy", $('#alloy').val()), _defineProperty(_product_arr$push, "item", $('#item').val()), _defineProperty(_product_arr$push, "size", $('#size').val()), _defineProperty(_product_arr$push, "schedule", $('#schedule').val()), _defineProperty(_product_arr$push, "remarks", $('#remarks').val()), _defineProperty(_product_arr$push, "sc_no", $('#sc_no').val()), _defineProperty(_product_arr$push, "issued_qty", $('#issued_qty').val()), _defineProperty(_product_arr$push, "create_user", $('#create_user').val()), _defineProperty(_product_arr$push, "update_user", $('#update_user').val()), _defineProperty(_product_arr$push, "created_at", $('#created_at').val()), _defineProperty(_product_arr$push, "updated_at", $('#updated_at').val()), _defineProperty(_product_arr$push, "deleted", 0), _product_arr$push));
        clear();
      }
    }

    $('#btn_add').html('<i class="fa fa-plus"></i> Add');
    ProductDataTable(product_arr);
  });
  $('#frm_product').on('submit', function (e) {
    e.preventDefault();
    $('.loadingOverlay').show();
    $.ajax({
      url: $(this).attr('action'),
      type: 'POST',
      dataType: 'JSON',
      data: $(this).serialize()
    }).done(function (data, textStatus, xhr) {
      clear();
      viewState('');
      msg(data.msg, data.status);
      plotValues(data.info, data.details);
    }).fail(function (xhr, textStatus, errorThrown) {
      ErrorMsg(xhr);
    }).always(function () {
      $('.loadingOverlay').hide();
    });
  });
  $('#tbl_product_body').on('click', '.btn_withdrawal_detail', function () {
    $('.loadingOverlay').show();
    $('#item_count').val($(this).attr('data-count'));
    $('#item_id').val($(this).attr('data-item_id'));
    $('#item_class').val($(this).attr('data-item_class'));
    $('#item_code').val($(this).attr('data-item_code'));
    $('#inv_id').val($(this).attr('data-inv_id'));
    $('#old_issued_qty').val($(this).attr('data-old_issued_qty'));
    $('#jo_no').val($(this).attr('data-jo_no'));
    $('#lot_no').val($(this).attr('data-lot_no'));
    $('#heat_no').val($(this).attr('data-heat_no'));
    $('#alloy').val($(this).attr('data-alloy'));
    $('#item').val($(this).attr('data-item'));
    $('#size').val($(this).attr('data-size'));
    $('#schedule').val($(this).attr('data-schedule'));
    $('#remarks').val($(this).attr('data-remarks'));
    $('#sc_no').val($(this).attr('data-sc_no'));
    $('#issued_qty').val($(this).attr('data-issued_qty'));
    $('#create_user').val($(this).attr('data-create_user'));
    $('#update_user').val($(this).attr('data-update_user'));
    $('#created_at').val($(this).attr('data-created_at'));
    $('#updated_at').val($(this).attr('data-updated_at'));
    getInventory($(this).attr('data-item_class'), $(this).attr('data-item_code'), $(this).attr('data-issued_qty'), $(this).attr('data-inv_id'));
    $('#btn_add').html('<i class="fa fa-edit"></i> Update');
  });
  $('#tbl_product_body').on('click', '.btn_withdrawal_detail_delete', function () {
    var key = parseInt($(this).attr('data-count')) - 1; // console.log(product_arr[key]);

    product_arr[key].deleted = 1;
    ProductDataTable(product_arr);
  });
  $('#btn_prepare_print').on('click', function () {
    $('#modal_withdrawal_slip').modal('show');
  });
  $('.btn_print').on('click', function () {
    var print_format = $(this).attr('data-print_format');
    var print_link = ProductWithdrawalSlipPrintURL + '?trans_id=' + $('#id').val() + '&&trans_no=' + $('#trans_no').val() + '&&date=' + $('#date').val() + '&&prepared_by=' + $('#prepared_by').val() + '&&issued_by=' + $('#issued_by').val() + '&&print_format=' + print_format + '&&plant=' + $('#plant').val() + '&&received_by=' + $('#received_by').val();

    if ($('#trans_no').val() == '' || $('#id').val() == '') {
      msg('Please Navigate to a Transaction Number first.', 'failed');
    } else if ($('#date').val() == '') {
      msg('Please input date of withdrawal.', 'failed');
    } else {
      window.open(print_link, '_tab');
    }
  });
  $('#btn_search_filter').on('click', function () {
    $('.srch-clear').val('');
    searchDataTable([]);
    $('#modal_product_search').modal('show');
  });
  $('#btn_search_excel').on('click', function () {
    window.location.href = excelSearchRawMaterialURL + "?srch_date_withdrawal_from=" + $('#srch_date_withdrawal_from').val() + "&srch_date_withdrawal_to=" + $('#srch_date_withdrawal_to').val() + "&srch_trans_no=" + $('#srch_trans_no').val() + "&srch_heat_no=" + $('#srch_heat_no').val() + "&srch_mat_code=" + $('#srch_mat_code').val() + "&srch_alloy=" + $('#srch_alloy').val() + "&srch_item=" + $('#srch_item').val() + "&srch_size=" + $('#srch_size').val() + "&srch_length=" + $('#srch_length').val() + "&srch_schedule=" + $('#srch_schedule').val();
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
      searchDataTable(data);
    }).fail(function (xhr, textStatus, errorThrown) {
      ErrorMsg(xhr);
    }).always(function () {
      $('.loadingOverlay-modal').hide();
    });
  });
  $('#tbl_search').on('click', '.btn_pick_search_item', function () {
    getWithdrawalTransaction('', $(this).attr('data-trans_no'));
    $('#modal_product_search').modal('hide');
  });
});

function init() {
  check_permission(code_permission, function (output) {
    if (output == 1) {}
  });
  viewState('');
  getWithdrawalTransaction('', '');
}

function clear() {
  $('.clear').val('');
}

function viewState(state) {
  switch (state) {
    case 'ADD':
      $('.btn_navigation').prop('disabled', true);
      $('#trans_no').prop('readonly', true);
      $('#item_class').prop('disabled', false);
      $('#item_code').prop('readonly', false);
      $('#schedule').prop('readonly', true);
      $('#remarks').prop('disabled', false);
      $('#sc_no').prop('readonly', false);
      $('#issued_qty').prop('readonly', false);
      $('#btn_search_item_code').prop('disabled', false);
      $('#controls').show(); // $('#btn_add').hide();
      // $('#btn_clear').hide();

      $('.btn_withdrawal_detail').prop('disabled', true);
      $('.btn_withdrawal_detail_delete').prop('disabled', false);
      $('#add_new').hide();
      $('#edit').hide();
      $('#save').show();
      $('#delete').hide();
      $('#cancel').show();
      $('#print').hide();
      $('#search').hide();
      vState = 'ADD';
      break;

    case 'EDIT':
      $('.btn_navigation').prop('disabled', true);
      $('#trans_no').prop('readonly', true);
      $('#item_class').prop('disabled', false);
      $('#item_code').prop('readonly', false);
      $('#schedule').prop('readonly', true);
      $('#remarks').prop('disabled', false);
      $('#sc_no').prop('readonly', false);
      $('#issued_qty').prop('readonly', false);
      $('#btn_search_item_code').prop('disabled', false);
      $('#controls').show(); // $('#btn_add').hide();
      // $('#btn_clear').hide();

      $('.btn_withdrawal_detail').prop('disabled', false);
      $('.btn_withdrawal_detail_delete').prop('disabled', false);
      $('#add_new').hide();
      $('#edit').hide();
      $('#save').show();
      $('#delete').hide();
      $('#cancel').show();
      $('#print').hide();
      $('#search').hide();
      vState = 'EDIT';
      break;

    default:
      $('.btn_navigation').prop('disabled', false);
      $('#trans_no').prop('readonly', false);
      $('#item_class').prop('disabled', true);
      $('#item_code').prop('readonly', true);
      $('#schedule').prop('readonly', true);
      $('#remarks').prop('disabled', true);
      $('#sc_no').prop('readonly', true);
      $('#issued_qty').prop('readonly', true);
      $('#btn_search_item_code').prop('disabled', true);
      $('#controls').hide(); // $('#btn_add').hide();
      // $('#btn_clear').hide();

      $('.btn_withdrawal_detail').prop('disabled', true);
      $('.btn_withdrawal_detail_delete').prop('disabled', true);
      $('#add_new').show();
      $('#edit').show();
      $('#save').hide();
      $('#delete').hide();
      $('#cancel').hide();
      $('#print').show();
      $('#search').show();
      vState = '';
      break;
  }
}

function getWithdrawalTransaction(to, trans_no) {
  $('.loadingOverlay').show();
  $.ajax({
    url: getWithdrawalTransactionURL,
    type: 'GET',
    dataType: 'JSON',
    data: {
      to: to,
      trans_no: trans_no
    }
  }).done(function (data, textStatus, xhr) {
    plotValues(data.info, data.details);
  }).fail(function (xhr, textStatus, errorThrown) {
    ErrorMsg(xhr);
  }).always(function () {
    $('.loadingOverlay').hide();
  });
}

function plotValues(info, details) {
  $('#id').val(info.id);
  $('#trans_no').val(info.trans_no);
  $('#status').val(info.status);
  product_arr = [];
  var count = product_arr.length;
  console.log(details);
  $.each(details, function (i, x) {
    var _product_arr$push2;

    count++;
    product_arr.push((_product_arr$push2 = {
      count: count,
      item_id: x.id,
      inv_id: x.inv_id,
      item_class: x.item_class,
      item_code: x.item_code
    }, _defineProperty(_product_arr$push2, "inv_id", x.inv_id), _defineProperty(_product_arr$push2, "old_issued_qty", x.issued_qty), _defineProperty(_product_arr$push2, "jo_no", x.jo_no), _defineProperty(_product_arr$push2, "lot_no", x.lot_no), _defineProperty(_product_arr$push2, "heat_no", x.heat_no), _defineProperty(_product_arr$push2, "alloy", x.alloy), _defineProperty(_product_arr$push2, "item", x.item), _defineProperty(_product_arr$push2, "size", x.size), _defineProperty(_product_arr$push2, "schedule", x.schedule), _defineProperty(_product_arr$push2, "remarks", x.remarks), _defineProperty(_product_arr$push2, "sc_no", x.sc_no), _defineProperty(_product_arr$push2, "issued_qty", x.issued_qty), _defineProperty(_product_arr$push2, "create_user", x.create_user), _defineProperty(_product_arr$push2, "update_user", x.update_user), _defineProperty(_product_arr$push2, "created_at", x.created_at), _defineProperty(_product_arr$push2, "updated_at", x.updated_at), _defineProperty(_product_arr$push2, "deleted", 0), _product_arr$push2));
  });
  ProductDataTable(product_arr);
}

function ProductDataTable(arr) {
  $('.loadingOverlay').show();
  var table = $('#tbl_product');
  table.dataTable().fnClearTable();
  table.dataTable().fnDestroy();
  table.dataTable({
    data: arr,
    order: [[1, 'asc']],
    // scrollX: true,
    columns: [{
      data: function data(x) {
        return "<button class='btn btn-sm bg-blue btn_withdrawal_detail' type='button'" + "data-item_id='" + x.item_id + "' " + "data-count='" + x.count + "' " + "data-item_class='" + x.item_class + "' " + "data-item_code='" + x.item_code + "' " + "data-inv_id='" + x.inv_id + "' " + "data-old_issued_qty='" + x.issued_qty + "' " + "data-jo_no='" + x.jo_no + "' " + "data-lot_no='" + x.lot_no + "' " + "data-heat_no='" + x.heat_no + "' " + "data-alloy='" + x.alloy + "' " + "data-item='" + x.item + "' " + "data-size='" + x.size + "' " + "data-schedule='" + x.schedule + "' " + "data-remarks='" + x.remarks + "' " + "data-sc_no='" + x.sc_no + "' " + "data-issued_qty='" + x.issued_qty + "' " + "data-create_user='" + x.create_user + "' " + "data-update_user='" + x.update_user + "' " + "data-created_at='" + x.created_at + "' " + "data-updated_at='" + x.updated_at + "' " + ">" + "<i class='fa fa-edit'></i>" + "</button>" + "<button class='btn btn-sm bg-red btn_withdrawal_detail_delete' type='button'" + "data-item_id='" + x.item_id + "' " + "data-count='" + x.count + "' " + ">" + "<i class='fa fa-times'></i>" + "</button>";
      },
      orderable: false,
      searchable: false
    }, {
      data: function data(x) {
        return x.count + "<input type='hidden' name='detail_count[]' value='" + x.count + "'>";
      }
    }, {
      data: function data(x) {
        return x.item_class + "<input type='hidden' name='detail_item_class[]' value='" + x.item_class + "'>" + "<input type='hidden' name='detail_inv_id[]' value='" + x.inv_id + "'>" + "<input type='hidden' name='detail_old_issued_qty[]' value='" + x.old_issued_qty + "'>" + "<input type='hidden' name='detail_item_id[]' value='" + x.item_id + "'>" + "<input type='hidden' name='detail_deleted[]' value='" + x.deleted + "'>";
      }
    }, {
      data: function data(x) {
        return x.jo_no + "<input type='hidden' name='detail_jo_no[]' value='" + x.jo_no + "'>";
      }
    }, {
      data: function data(x) {
        return x.item_code + "<input type='hidden' name='detail_item_code[]' value='" + x.item_code + "'>";
      }
    }, {
      data: function data(x) {
        return x.lot_no + "<input type='hidden' name='detail_lot_no[]' value='" + x.lot_no + "'>";
      }
    }, {
      data: function data(x) {
        return x.heat_no + "<input type='hidden' name='detail_heat_no[]' value='" + x.heat_no + "'>";
      }
    }, {
      data: function data(x) {
        return x.sc_no + "<input type='hidden' name='detail_sc_no[]' value='" + x.sc_no + "'>";
      }
    }, {
      data: function data(x) {
        return x.alloy + "<input type='hidden' name='detail_alloy[]' value='" + x.alloy + "'>";
      }
    }, {
      data: function data(x) {
        return x.item + "<input type='hidden' name='detail_item[]' value='" + x.item + "'>";
      }
    }, {
      data: function data(x) {
        return x.size + "<input type='hidden' name='detail_size[]' value='" + x.size + "'>";
      }
    }, {
      data: function data(x) {
        return x.schedule + "<input type='hidden' name='detail_schedule[]' value='" + x.schedule + "'>";
      }
    }, {
      data: function data(x) {
        return x.issued_qty + "<input type='hidden' name='detail_issued_qty[]' value='" + x.issued_qty + "'>";
      }
    }, {
      data: function data(x) {
        return x.remarks + "<input type='hidden' name='detail_remarks[]' value='" + x.remarks + "'>";
      }
    }],
    initComplete: function initComplete() {
      if (vState == '') {
        $('.btn_withdrawal_detail').prop('disabled', true);
        $('.btn_withdrawal_detail_delete').prop('disabled', true);
      } else {
        $('.btn_withdrawal_detail').prop('disabled', false);
        $('.btn_withdrawal_detail_delete').prop('disabled', false);
      }

      $('.loadingOverlay').hide();
    },
    createdRow: function createdRow(row, data, dataIndex) {
      if (data.deleted === 1) {
        $(row).css('background-color', '#ff6266');
        $(row).css('color', '#fff');
      }
    }
  });
}

function getInventory(item_class, item_code, issued_qty, inv_id) {
  $('.loadingOverlay-modal').show();
  $.ajax({
    url: getInventoryURL,
    type: 'GET',
    dataType: 'JSON',
    data: {
      item_class: item_class,
      item_code: item_code,
      issued_qty: issued_qty,
      inv_id: inv_id
    }
  }).done(function (data, textStatus, xhr) {
    console.log(data);

    if (data.length > 0) {
      if (data.length > 1) {
        InventoryDataTable(data);
        $('#modal_inventory').modal('show');
      } else {
        var product = data[0];
        $('#inv_id').val("");
        $('#jo_no').val("");
        $('#lot_no').val("");
        $('#heat_no').val("");
        $('#alloy').val("");
        $('#item').val("");
        $('#size').val("");
        $('#schedule').val("");
        $('#inv_qty').val("");
        $('#qty_weight').val("");
        $('#inv_id').val("");

        if (product.item_code == undefined) {
          $('#issued_qty').prop('readonly', true);
          msg("Item Code is not in the list.", "failed");
        } else if (product.current_stock == 0 && $('#item_id').val() == '') {
          $('#issued_qty').prop('readonly', true);
          msg("Item Code have no qty inventory.", "failed");
        } else {
          $('#jo_no').val(product.jo_no);
          $('#lot_no').val(product.lot_no);
          $('#heat_no').val(product.heat_no);
          $('#alloy').val(product.alloy);
          $('#item').val(product.item);
          $('#size').val(product.size);
          $('#schedule').val(product.schedule);
          $('#inv_qty').val(product.current_stock);
          $('#qty_weight').val(product.qty_weight);
          $('#issued_qty').prop('readonly', false);
          $('#inv_id').val(product.id);
        }
      }
    } else {
      msg("No Items found for this Item Code or maybe the Stock Quantities are all 0.", 'warning');
    }
  }).fail(function (xhr, textStatus, errorThrown) {
    //msg(errorThrown, textStatus);
    var response = jQuery.parseJSON(xhr.responseText);
    ErrorMsg(response);
  }).always(function () {
    $('.loadingOverlay').hide();
    $('.loadingOverlay-modal').hide();
  });
}

function InventoryDataTable(arr) {
  $('.loadingOverlay-modal').show();
  $('#tbl_inventory').dataTable().fnClearTable();
  $('#tbl_inventory').dataTable().fnDestroy();
  $('#tbl_inventory').dataTable({
    data: arr,
    order: [[13, 'asc']],
    scrollX: true,
    columns: [{
      data: function data(x) {
        return "<button class='btn btn-sm bg-blue btn_pick_item' type='button'" + "data-id='" + x.id + "' " + "data-item_class='" + x.item_class + "' " + "data-jo_no='" + x.jo_no + "' " + "data-item_code='" + x.item_code + "' " + "data-product_line='" + x.product_line + "' " + "data-description='" + x.description + "' " + "data-item='" + x.item + "' " + "data-alloy='" + x.alloy + "' " + "data-size='" + x.size + "' " + "data-schedule='" + x.schedule + "' " + "data-qty_weight='" + x.qty_weight + "' " + "data-qty_pcs='" + x.qty_pcs + "' " + "data-current_stock='" + x.current_stock + "' " + "data-heat_no='" + x.heat_no + "' " + "data-lot_no='" + x.lot_no + "' " + "data-received_id='" + x.received_id + "' " + ">" + "<i class='fa fa-edit'></i>" + "</button>";
      },
      orderable: false,
      searchable: false
    }, {
      data: 'item_class'
    }, {
      data: 'jo_no'
    }, {
      data: 'item_code'
    }, {
      data: 'description'
    }, {
      data: 'product_line'
    }, {
      data: 'lot_no'
    }, {
      data: 'heat_no'
    }, {
      data: 'qty_weight'
    }, {
      data: 'qty_pcs'
    }, {
      data: 'current_stock'
    }, {
      data: 'alloy'
    }, {
      data: 'item'
    }, {
      data: 'size'
    }, {
      data: 'schedule'
    }],
    initComplete: function initComplete() {
      $('.loadingOverlay-modal').hide();
    }
  });
}

function searchDataTable(arr) {
  $('.loadingOverlay-modal').show();
  $('#tbl_search').dataTable().fnClearTable();
  $('#tbl_search').dataTable().fnDestroy();
  $('#tbl_search').dataTable({
    data: arr,
    order: [[14, 'asc']],
    scrollX: true,
    columns: [{
      data: function data(x) {
        return "<button class='btn btn-sm bg-blue btn_pick_search_item' type='button'" + "data-trans_no='" + x.trans_no + "'" + "data-item_class='" + x.item_class + "'" + "data-item_code='" + x.item_code + "'" + "data-jo_no='" + x.jo_no + "'" + "data-lot_no='" + x.lot_no + "'" + "data-heat_no='" + x.heat_no + "'" + "data-sc_no='" + x.sc_no + "'" + "data-alloy='" + x.alloy + "'" + "data-item='" + x.item + "'" + "data-size='" + x.size + "'" + "data-schedule='" + x.schedule + "'" + "data-issued_qty='" + x.issued_qty + "'" + "data-remarks='" + x.remarks + "'" + "data-created_at='" + x.created_at + "'" + ">" + "<i class='fa fa-edit'></i>" + "</button>";
      },
      orderable: false,
      searchable: false
    }, {
      data: 'trans_no'
    }, {
      data: 'item_class'
    }, {
      data: 'item_code'
    }, {
      data: 'jo_no'
    }, {
      data: 'lot_no'
    }, {
      data: 'heat_no'
    }, {
      data: 'sc_no'
    }, {
      data: 'alloy'
    }, {
      data: 'item'
    }, {
      data: 'size'
    }, {
      data: 'schedule'
    }, {
      data: 'issued_qty'
    }, {
      data: 'remarks'
    }, {
      data: 'created_at'
    }],
    initComplete: function initComplete() {
      $('.loadingOverlay-modal').hide();
    }
  });
}

/***/ }),

/***/ 21:
/*!***************************************************************************************************!*\
  !*** multi ./resources/assets/js/pages/ppc/transactions/product-withdrawal/product-withdrawal.js ***!
  \***************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\laragon\www\en-pms\resources\assets\js\pages\ppc\transactions\product-withdrawal\product-withdrawal.js */"./resources/assets/js/pages/ppc/transactions/product-withdrawal/product-withdrawal.js");


/***/ })

/******/ });