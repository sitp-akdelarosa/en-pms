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
/******/ 	return __webpack_require__(__webpack_require__.s = 25);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/assets/js/pages/production/transactions/transfer-items/transfer-items.js":
/*!********************************************************************************************!*\
  !*** ./resources/assets/js/pages/production/transactions/transfer-items/transfer-items.js ***!
  \********************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

var transfer_item_arr = [];
var received_items_arr = [];
$(function () {
  getTransferEntry(getTransferEntryURL, {
    _token: token
  });
  getReceiveItems();
  checkAllCheckboxesInTable('.check_all_transfer_item', '.check_item');
  init();
  $('#jo_no').on('change', function () {
    getJOdetails($(this).val());
  });
  $('#jo_no').on('keydown', function (e) {
    if (e.keyCode === 13) {
      getJOdetails($(this).val());
    }
  });
  $('#ostatus').on('change', function () {
    if ($('#curr_process').val() == '') {
      msg('Please select current Process.', 'failed');
    } else {
      if ($('#curr_process').val() != '') {
        getDivCodeProcess($('#jo_no').val(), $('#curr_process').find("option:selected").text());
        getProcessQty($('#curr_process').find("option:selected").text(), $('#jo_no').val(), $('#curr_process').val(), $(this).val());
      } else {
        $('#unprocessed').val(0);
      }
    }
  });
  $('#process').on('change', function () {
    DivisionCode($(this).val(), '');
    checkIfSameDivCode();
  });
  $('#div_code').on('change', function () {//checkIfSameDivCode();
  });
  $('#qty').on('change', function () {
    var qty = parseInt($(this).val());
    var unprocessed = parseInt($('#unprocessed').val());

    if (qty > unprocessed) {
      var curr_process = $("#curr_process").find("option:selected").text();

      if (curr_process !== 'CUTTING') {
        msg('Qty must be less than in # of item to transfer', 'warning');
      }
    }
  });
  $('#btn_add').on('click', function () {
    $('#process').html("<option value=''></option>");
    clear();
    $('#hide_create').show();
    $('#modal_transfer_entry').modal('show');
  });
  $("#frm_transfer_items").on('submit', function (e) {
    e.preventDefault();
    transfer_item_arr = [];
    var totalqty = parseInt($('#UnprocessTransfer').val()) + parseInt($('#qty').val());

    if ($('#process').val() == $("#curr_process").find("option:selected").text() && $('#userDivCode').val() == $("#div_code").find("option:selected").text()) {
      msg("You cannot transfer to your division with the same process", "warning");
    } else if (parseInt($('#unprocessed').val()) < parseInt($('#UnprocessTransfer').val())) {
      totalqty -= parseInt($('#unprocessed').val());
      msg('The total of pending qty is greather than ' + totalqty + ' to # of item to transfer', 'warning');
    } else if (parseFloat($('#qty').val()) !== parseFloat($('#unprocessed').val())) {
      msg("Partial Transfer is not allowed", "warning");
    } else if ($('#qty').val() < 0) {
      msg("Please Input valid number", "warning");
    } else {
      var curr_process = $("#curr_process").find("option:selected").text();

      if (curr_process !== 'CUTTING' && parseInt($('#qty').val()) > parseInt($('#unprocessed').val())) {
        msg('Qty must be less than in # of item to transfer', 'warning');
      } else {
        $('.loadingOverlay-modal').show();
        $.ajax({
          dataType: 'json',
          type: 'POST',
          url: $(this).attr("action"),
          data: $(this).serialize()
        }).done(function (data, textStatus, xhr) {
          msg(data.msg, data.status);
          getTransferEntry(getTransferEntryURL, {
            _token: token
          }); // transfer_item_arr = data.transfer_item;
          // makeTransferItemTable(transfer_item_arr);

          getReceiveItems();
          $('#modal_transfer_entry').modal('hide');
        }).fail(function (xhr, textStatus, errorThrown) {
          ErrorMsg(xhr);
        }).always(function () {
          $('.loadingOverlay-modal').hide();
        });
      }
    }
  });
  $('#tbl_transfer_entry_body').on('click', '.btn_edit', function (e) {
    var data = [];
    data['id'] = $(this).attr('data-id');
    data['jo_no'] = $(this).attr('data-jo_no');
    data['prod_order_no'] = $(this).attr('data-prod_order_no');
    data['prod_code'] = $(this).attr('data-prod_code');
    data['description'] = $(this).attr('data-description');
    data['current_process'] = $(this).attr('data-current_process');
    data['div_code'] = $(this).attr('data-div_code');
    data['process'] = $(this).attr('data-process');
    data['qty'] = $(this).attr('data-qty');
    data['status'] = $(this).attr('data-status');
    data['remarks'] = $(this).attr('data-remarks');
    data['create_user'] = $(this).attr('data-create_user');
    data['created_at'] = $(this).attr('data-created_at');
    data['update_user'] = $(this).attr('data-update_user');
    data['updated_at'] = $(this).attr('data-updated_at');
    data['div_code_code'] = $(this).attr('data-div_code_code');
    data['output_status'] = $(this).attr('data-output_status');
    data.length = 1;
    getJOdetails($(this).attr('data-jo_no'), data);
    $('#modal_transfer_entry').modal('show');
  });
  $('#btn_delete_set').on('click', function () {
    delete_set('.check_item', deleteTransferItem);
  });
  $('#tbl_received_items_body').on('click', '.btn_receive', function (event) {
    event.preventDefault();
    $('#id_r').val($(this).attr('data-id'));
    $('#jo_no_r').val($(this).attr('data-jo_no'));
    $('#prod_code_r').val($(this).attr('data-prod_code'));
    $('#process_r').val($(this).attr('data-process'));
    $('#qty_r').val($(this).attr('data-remaining_qty'));
    $('#transferred_qty_r').val($(this).attr('data-qty'));
    $('#qty').val($(this).attr('data-qty'));
    $('#receive_qty').val($(this).attr('data-receive_qty'));
    $('#remaining_qty').val($(this).attr('data-remaining_qty'));
    $('#current_process_r').val($(this).attr('data-current_process'));
    $('#div_code_code_r').val($(this).attr('data-div_code_code'));
    $('#current_div_code_r').val($(this).attr('data-current_div_code'));
    $('#current_process_name_r').val($(this).attr('data-current_process_name'));
    $('#current_process_name_r').val($(this).attr('data-current_process_name'));
    $('#status_r').val($(this).attr('data-status'));
    $('#note').val($(this).attr('data-remarks'));
    $('#modal_receive_item').modal('show');
  });
  $("#frm_receive_item").on('submit', function (e) {
    e.preventDefault();

    if (parseInt($('#transferred_qty_r').val()) < parseInt($('#qty_r').val())) {
      msg('Input qty is greather than transfer qty', 'warning');
    } else if ($('#qty_r').val() < 0) {
      msg("Please Input valid number", "warning");
    } else {
      $('.lodaingOverlay-modal').show();
      $.ajax({
        dataType: 'json',
        type: 'POST',
        url: $(this).attr("action"),
        data: $(this).serialize()
      }).done(function (data, textStatus, xhr) {
        msg('Process Received', 'success');
        getReceiveItems();
        getTransferEntry(getTransferEntryURL, {
          _token: token
        });
        $('#modal_receive_item').modal('hide');
        console.log(data);
      }).fail(function (xhr, textStatus, errorThrown) {
        ErrorMsg(xhr);
      }).always(function () {
        $('.lodaingOverlay-modal').hide();
      });
    }
  });
});

function init() {
  check_permission(code_permission, function (output) {
    if (output == 1) {}
  });
} // function getTransferEntry() {
//     transfer_item_arr = [];
//     $.ajax({
//         url: getTransferEntryURL,
//         type: 'GET',
//         dataType: 'JSON',
//         data: {
//             _token: token
//         },
//     }).done(function(data, textStatus, xhr) {
//         transfer_item_arr = data;
//         makeTransferItemTable(transfer_item_arr);
//     }).fail(function(xhr, textStatus, errorThrown) {
//         ErrorMsg(xhr);
//     });
// }


function getTransferEntry(ajax_url, object_data) {
  var tbl_transfer_entry = $('#tbl_transfer_entry').DataTable();
  tbl_transfer_entry.clear();
  tbl_transfer_entry.destroy();
  tbl_transfer_entry = $('#tbl_transfer_entry').DataTable({
    ajax: {
      url: ajax_url,
      data: object_data,
      error: function error(xhr, textStatus, errorThrown) {
        ErrorMsg(xhr);
      }
    },
    // bLengthChange : false,
    searching: true,
    // paging: false,
    order: [[10, 'desc']],
    lengthMenu: [[5, 10, 15, 20, -1], [5, 10, 15, 20, "All"]],
    pageLength: 10,
    columns: [{
      data: function data(x) {
        return "<input type='checkbox' class='table-checkbox check_item' value='" + x.id + "'>";
      },
      searchable: false,
      orderable: false,
      width: '3.33%'
    }, {
      data: 'action',
      name: 'action',
      searchable: false,
      orderable: false,
      width: '3.33%'
    }, {
      data: 'jo_no',
      name: 'jo_no',
      width: '10.33%'
    }, {
      data: 'prod_code',
      name: 'prod_code',
      width: '8.33%'
    }, {
      data: 'current_process_name',
      name: 'current_process_name',
      width: '10.33%'
    }, {
      data: 'div_code_code',
      name: 'div_code_code',
      width: '8.33%'
    }, {
      data: 'process',
      name: 'process',
      width: '10.33%'
    }, {
      data: 'qty',
      name: 'qty',
      width: '8.33%'
    }, {
      data: 'status',
      name: 'status',
      width: '8.33%'
    }, {
      data: 'remarks',
      name: 'remarks',
      width: '8.33%'
    }, {
      data: 'created_at',
      name: 'created_at',
      width: '10.33%'
    }, {
      data: 'item_status',
      name: 'item_status',
      width: '10.33%'
    }],
    fnDrawCallback: function fnDrawCallback() {
      $("#tbl_transfer_entry").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
    },
    initComplete: function initComplete() {
      $('.loadingOverlay').hide();
    }
  });
}

function getReceiveItems() {
  received_items_arr = [];
  $.ajax({
    url: getReceiveItemsURL,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token
    }
  }).done(function (data, textStatus, xhr) {
    received_items_arr = data;
    console.log(received_items_arr);
    makeReceiveItemsTable(received_items_arr);
  }).fail(function (xhr, textStatus, errorThrown) {
    ErrorMsg(xhr);
  });
}

function makeReceiveItemsTable(arr) {
  $('#tbl_received_items').dataTable().fnClearTable();
  $('#tbl_received_items').dataTable().fnDestroy();
  $('#tbl_received_items').dataTable({
    data: arr,
    bLengthChange: false,
    searching: true,
    paging: false,
    order: [[11, 'asc']],
    columns: [{
      data: function data(x) {
        return "<input type='checkbox' class='table-checkbox check_receive_item' value='" + x.id + "'>";
      },
      searchable: false,
      orderable: false
    }, {
      data: function data(x) {
        var disabled = '';

        if (x.item_status == 1) {
          disabled = 'disabled';
        }

        return "<button class='btn btn-sm btn-primary btn_receive' " + "data-id='" + x.id + "'" + "data-jo_no='" + x.jo_no + "'" + "data-current_process_name='" + x.current_process_name + "'" + "data-div_code_code='" + x.div_code_code + "'" + "data-current_process='" + x.current_process + "'" + "data-qty='" + x.qty + "'" + "data-receive_qty='" + x.receive_qty + "'" + "data-remaining_qty='" + x.remaining_qty + "'" + "data-process='" + x.process + "'" + "data-current_div_code='" + x.current_div_code + "'" + "data-prod_order_no='" + x.prod_order_no + "'" + "data-prod_code='" + x.prod_code + "'" + "data-description='" + x.description + "'" + "data-div_code='" + x.div_code + "'" + "data-status='" + x.status + "'" + "data-remarks='" + x.remarks + "'" + "data-create_user='" + x.create_user + "'" + "data-created_at='" + x.created_at + "'" + "data-item_status='" + x.item_status + "' " + "data-update_user='" + x.update_user + "' " + "data-updated_at='" + x.updated_at + "' " + disabled + ">" + '<i class="fa fa-edit"></i> Receive' + '</button>';
      },
      searchable: false,
      orderable: false
    }, {
      data: 'jo_no'
    }, {
      data: 'prod_code'
    }, {
      data: 'current_div_code'
    }, {
      data: 'current_process_name'
    }, {
      data: 'div_code_code'
    }, {
      data: 'process'
    }, {
      data: 'qty'
    }, {
      data: 'status'
    }, {
      data: 'remarks'
    }, {
      data: 'created_at'
    }, {
      data: 'receive_qty'
    }, {
      data: 'remaining_qty'
    }, {
      data: 'receive_remarks'
    }],
    createdRow: function createdRow(row, data, dataIndex) {
      if (data.item_status == 0 || data.item_status == '0') {
        $(row).css('background-color', '#ff6266'); // RED

        $(row).css('color', '#fff');
      }
    },
    fnInitComplete: function fnInitComplete() {
      $('.dataTables_scrollBody').slimscroll();
      $("#tbl_received_items").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
    }
  });
}

function delete_set(checkboxClass, deleteTransferItem) {
  var chkArray = [];
  $(checkboxClass + ":checked").each(function () {
    chkArray.push($(this).val());
  });

  if (chkArray.length > 0) {
    swal({
      title: "Are you sure?",
      text: "You will not be able to recover your data!",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#f95454",
      confirmButtonText: "Yes",
      cancelButtonText: "No",
      closeOnConfirm: true,
      closeOnCancel: false
    }, function (isConfirm) {
      if (isConfirm) {
        $.ajax({
          url: deleteTransferItem,
          type: 'POST',
          dataType: 'JSON',
          data: {
            _token: token,
            id: chkArray
          }
        }).done(function (data, textStatus, xhr) {
          $('.check_all_transfer_item').prop('checked', false);
          msg(data.msg, data.status);
          getTransferEntry(getTransferEntryURL, {
            _token: token
          });
          getReceiveItems();
          clear();
        }).fail(function (xhr, textStatus, errorThrown) {
          msg(errorThrown, 'error');
        });
      } else {
        swal("Cancelled", "Your data is safe and not deleted.");
      }
    });
  } else {
    msg("Please select at least 1 item.", "failed");
  }
}

function getJOdetails(jo_no, edit) {
  var curr_process = '<option value=""></option>';
  $('#curr_process').html(curr_process);
  $('.loadingOverlay-modal').show();
  $.ajax({
    url: getJOdetailsURL,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token,
      jo_no: jo_no
    }
  }).done(function (data, textStatus, xhr) {
    console.log(data);
    $('#unprocessed').val(0);
    var jo = data.jo;

    if (jo.status == 3) {
      msg("J.O. Number is already cancelled", "failed");
    } else if (jo != '') {
      $('#prod_order_no').val(jo.prod_order_no);
      $('#prod_code').val(jo.prod_code);
      $('#description').val(jo.description);
      $('#travel_sheet_id').val(jo.id);
    } else {
      $('#prod_order_no').val('');
      $('#prod_code').val('');
      $('#description').val('');
      $('#travel_sheet_id').val('');
      msg("J.O. Number is not in the list.", "failed");
    }

    $.each(data.current_processes, function (i, x) {
      curr_process = '<option value="' + x.id + '">' + x.process + '</option>';
      $('#curr_process').append(curr_process);
    });

    if (edit !== undefined) {
      if (edit.length > 0) {
        DivisionCode(edit['process'], edit['div_code_code']);
        getProcessQty(edit['process'], edit['jo_no'], edit['current_process'], edit['output_status']);
        getDivCodeProcess(edit['jo_no'], edit['process']);
        $('#id').val(edit['id']);
        $('#jo_no').val(edit['jo_no']);
        $('#curr_process').val(edit['current_process']);
        $('#qty').val(edit['qty']);
        $('#status').val(edit['status']);
        $('#remarks').val(edit['remarks']);
        $('#create_user').val(edit['create_user']);
        $('#created_date').val(edit['created_at']);
        $('#update_user').val(edit['update_user']);
        $('#updated_date').val(edit['updated_at']);
      }
    }
  }).fail(function (xhr, textStatus, errorThrown) {
    ErrorMsg(xhr);
  }).always(function () {
    $('.loadingOverlay-modal').hide();
  });
}

function getDivCodeProcess(jo_no, process) {
  $('.lodaingOverlay-modal').show();
  var options = '<option value=""></option>';
  $('#process').html(options);
  $.ajax({
    url: getDivCodeProcessURL,
    type: 'GET',
    dataType: 'JSON',
    data: {
      jo_no: jo_no
    }
  }).done(function (data, textStatus, xhr) {
    $.each(data, function (i, x) {
      if (x.process == process) {
        options = "<option value='" + x.process + "' selected>" + x.process + "</option>";
      } else {
        options = "<option value='" + x.process + "'>" + x.process + "</option>";
      }

      $('#process').append(options);
    });
    $('#process').trigger('change');
  }).fail(function (xhr, textStatus, errorThrown) {
    ErrorMsg(xhr);
  }).always(function () {
    $('.lodaingOverlay-modal').hide();
  });
}

function DivisionCode(process, div_code) {
  var opt = "<option value=''></option>";
  $('#div_code').html(opt);
  $.ajax({
    url: getDivisionCode,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token,
      process: process
    }
  }).done(function (data, textStatus, xhr) {
    var select = '';
    $.each(data, function (i, x) {
      if (x.div_code == div_code) {
        select = 'selected';
      } else if ($('#userDivCode').val() == x.div_code) {
        select = 'selected';
      }

      $('#div_code').append("<option value='" + x.id + "'" + select + ">" + x.div_code + "</option>");
      select = '';
    });
  }).fail(function (xhr, textStatus, errorThrown) {
    console.log(errorThrown);
  });
}

function getProcessQty(process, jo_no, current_process, output_status) {
  $('.lodaingOverlay-modal').show();
  $.ajax({
    url: unprocessedItem,
    type: 'POSt',
    dataType: 'JSON',
    data: {
      _token: token,
      process: process,
      jo_no: jo_no,
      current_process: current_process,
      output_status: output_status
    }
  }).done(function (data, textStatus, xhr) {
    //$.each(data.UnprocessTravel, function(i, x) {
    $('#unprocessed').val(data.UnprocessTravel['total_qty']);
    $('#userDivCode').val(data.UnprocessTravel['div_code']); //});

    $('#UnprocessTransfer').val(data.UnprocessTransfer);
  }).fail(function (xhr, textStatus, errorThrown) {
    ErrorMsg(xhr);
  }).always(function () {
    $('.lodaingOverlay-modal').hide();
  });
}

function checkIfSameDivCode() {
  if ($('#process').val() == $("#curr_process").find("option:selected").text() && $('#userDivCode').val() == $("#div_code").find("option:selected").text()) {
    msg("You cannot transfer to your division with the same process", "warning");
  }
}

function clear() {
  $('.clear').val('');
}

/***/ }),

/***/ 25:
/*!**************************************************************************************************!*\
  !*** multi ./resources/assets/js/pages/production/transactions/transfer-items/transfer-items.js ***!
  \**************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\laragon\www\en-pms\resources\assets\js\pages\production\transactions\transfer-items\transfer-items.js */"./resources/assets/js/pages/production/transactions/transfer-items/transfer-items.js");


/***/ })

/******/ });