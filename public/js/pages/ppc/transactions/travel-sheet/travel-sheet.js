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
/******/ 	return __webpack_require__(__webpack_require__.s = 17);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/assets/js/pages/ppc/transactions/travel-sheet/travel-sheet.js":
/*!*********************************************************************************!*\
  !*** ./resources/assets/js/pages/ppc/transactions/travel-sheet/travel-sheet.js ***!
  \*********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

var jo_details = [];
var prod_arr = [];
var cut_sched = [];
var scno_arr = [];
var sched_qty_arr = [];
$(function () {
  get_set();
  getISO('#iso_no');
  joDetailsList();
  init();
  $('#btn_cutting_schedule').on('click', function () {
    $('#prepared_by').val(auth_user);
    $('#modal_cutting_schedule').modal('show');
  });
  $('#tbl_jo_details_body').on('click', '.open_travel_sheet_modal', function () {
    $('#idJO').val($(this).attr('data-idJO'));
    $('#travel_sheet_id').val($(this).attr('data-id'));
    $('#jo_no').val($(this).attr('data-jo_no'));
    $('#prod_code').val($(this).attr('data-prod_code'));

    if ($(this).attr('data-id') !== '') {
      $('#issued_qty').val($(this).attr('data-issued_qty'));
      $('#qty_per_sheet').val($(this).attr('data-qty_per_sheet'));
      $('#iso_no').val($(this).attr('data-iso_code'));
      $('#issued_qty').val($(this).attr('data-issued_qty'));
      getPreTravelSheetData($(this).attr('data-id'));
      getSC($(this).attr('data-idJO'));
    } else {
      $('#issued_qty').val($(this).attr('data-sched_qty'));
      $('#qty_per_sheet').val('');
      $('#iso_no').val('');
      makeProdTable([]);
      $('#set').val('None');
      getBom('None', $(this).attr('data-prod_code'));
    }

    if ($(this).attr('data-status') == 2 || $(this).attr('data-status') == 5) {
      $('.disableOnProduction').prop('disabled', true);
    } else if ($(this).attr('data-status') == 0) {
      $('.disableOnProduction').prop('disabled', false);
      $('#btn_travel_sheet_preview').prop('disabled', true);
    } else {
      $('#btn_travel_sheet_preview').prop('disabled', false);
      $('.disableOnProduction').prop('disabled', false);
    }

    $('#modal_travel_sheet').modal('show');
  });
  $('#set').on('change', function () {
    getBom($(this).val(), $('#prod_code').val());
  });
  $('#tbl_product').on('change', '.issued_qty_per_sheet', function (e) {
    getTotalIssuedQty();
  });
  $('#btn_add_prod').on('click', function () {
    var issued_qty = parseInt($('#issued_qty').val());
    var qty_per_sheet = parseInt($('#qty_per_sheet').val());
    var idJO = $('#idJO').val();

    if (issued_qty < qty_per_sheet) {
      msg("Qty per sheet is greater than issued qty.", 'failed');
    } else if (issued_qty < 0 || qty_per_sheet < 0 || issued_qty == 0 || qty_per_sheet == 0) {
      msg("Please input a valid number.", 'failed');
    } else {
      $.ajax({
        url: getSc_noURL,
        type: 'POST',
        dataType: 'JSON',
        data: {
          _token: token,
          id: idJO
        }
      }).done(function (data, textStatus, xhr) {
        var scnum = data.sc_no.join();
        $('#sc_no').val(scnum);
        scno_arr = data.sc_no;
        sched_qty_arr = data.back_order_qty; // var sc_no = sc.split(',');

        prod_arr = [];
        var count = 1;
        var index = 0;

        if ($('#qty_per_sheet').val() == '' || $('#qty_per_sheet').val() == 0) {
          prod_arr.push({
            id: count,
            index: index,
            prod_code: $('#prod_code').val(),
            issued_qty: $('#issued_qty').val(),
            sc_no: scno_arr
          });
          count++;
          index++;
        } else {
          var count = 1;
          var index = 0;
          var quo = issued_qty / qty_per_sheet;

          if (quo % 1 != 0) {
            var num = quo.toString().split('.');
            var times = parseFloat(num[0]);
            var remainder = parseFloat('0.' + num[1]);

            for (var i = 0; i < times; i++) {
              prod_arr.push({
                id: count,
                index: index,
                prod_code: $('#prod_code').val(),
                issued_qty: qty_per_sheet,
                sc_no: scno_arr
              });
              count++;
              index++;
            }

            prod_arr.push({
              id: count,
              index: index,
              prod_code: $('#prod_code').val(),
              issued_qty: parseInt(qty_per_sheet * remainder),
              sc_no: scno_arr
            });
          } else {
            var count = 1;
            var index = 0;

            for (var i = 0; i < quo; i++) {
              prod_arr.push({
                id: count,
                index: index,
                prod_code: $('#prod_code').val(),
                issued_qty: qty_per_sheet,
                sc_no: scno_arr
              });
              count++;
              index++;
            }
          }
        }

        makeProdTable(prod_arr, scno_arr);
      }).fail(function (xhr, textStatus, errorThrown) {
        var errors = xhr.responseJSON.errors;
        showErrors(errors);
      });
      $('#issued_qty_table').val(issued_qty);
    }
  });
  $('#btn_remove_prod').on('click', function () {
    var chkArray = [];
    $(".prod_check:checked").each(function () {
      chkArray.push($(this).val());
    });
    $.each(chkArray, function (i, x) {
      for (var i = prod_arr.length - 1; i >= 0; --i) {
        if (prod_arr[i].id == x) {
          prod_arr.splice(i, 1);
        }
      }
    });
    makeProdTable(prod_arr, scno_arr);
  });
  $('#status').on('change', function () {
    $('#from').val("");
    $('#to').val("");
    joDetailsList($(this).val(), '', '');
  });
  $('#frm_travel_sheet').on('submit', function (e) {
    e.preventDefault();
    var validate = validateInput();
    var totalIssued = parseInt($('#issued_qty_table').val());
    var issued_qty = parseInt($('#issued_qty').val());

    if (validate == 'invalid') {
      msg("Please input valid number.", 'warning');
    } else if (validate == 'morethan') {
      msg('Issued quantity per sheet must not more than SC quantity.', 'warning');
    } else {
      if (totalIssued != issued_qty) {
        swal({
          title: "Are you sure to save?",
          text: "The total issued qty is not equal to issued qty",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#f95454",
          confirmButtonText: "Yes",
          cancelButtonText: "No",
          closeOnConfirm: true,
          closeOnCancel: false
        }, function (isConfirm) {
          if (isConfirm) {
            SaveTravelSheet();
          } else {
            swal.close();
          }
        });
      } else {
        SaveTravelSheet();
      }
    }
  });
  $('#tbl_jo_details_body').on('change', '.jo_check', function (e) {
    var status = $(this).attr('data-status');

    if ($(this).is(":checked")) {
      if (status == 0) {
        msg("No Preparation for Travel Sheet .", 'failed');
        $(this).prop('checked', false);
      }
    }
  });
  $('#btn_travel_sheet_preview').on('click', function () {
    var travel_sheet = pdfTravelSheetURL + '?jo_no=' + $('#jo_no').val();
    window.open(travel_sheet, '_tab');
  });
  $('#btn_travel_sheet_all_print_preview').on('click', function () {
    var jo_no = [];
    $(".jo_check:checked").each(function () {
      jo_no.push($(this).val());
    });

    if (jo_no.length > 0) {
      var travel_sheet = pdfTravelSheetURL + '?iso_control=' + $('#iso_no').val() + '&&jo_no=' + jo_no;
      window.open(travel_sheet, '_tab');
    } else {
      msg("Select a J.O. number.", 'failed');
    }
  });
  $('#searchPS').on('click', function () {
    if ($('#from').val() != "" && $('#to').val() != "") {
      joDetailsList($('#status').val(), $('#from').val(), $('#to').val());
    } else if ($('#from').val() != "") {
      joDetailsList($('#status').val(), $('#from').val(), '');
    } else {
      msg("From Input is required", "warning");
    }
  });
  $('#tbl_product_body').on('change', '.scno', function () {
    var sc = $(this).val();
    var qty = 0;
    $.each(scno_arr, function (i, x) {
      if (sc.includes(x[0])) {
        qty = parseFloat(qty) + parseFloat(sched_qty_arr[i]);
      }
    });
    $('#sc_qty_' + $(this).attr('data-id')).val(qty);
  });
  $('#tbl_product_body').on('change', '.issued_qty_per_sheet', function () {
    var issued_qty = parseFloat($(this).val());
    var sc_qty = parseFloat($('#sc_qty_' + $(this).attr('data-id')).val()); // if (issued_qty > sc_qty) {
    // 	msg('Issued quantity per sheet must not more than SC quantity.','failed');
    // 	// $(this).val($(this).attr('data-old_qty'));
    // }
  });
});

function init() {
  check_permission(code_permission, function (output) {
    if (output == 1) {}
  });
}

function getTotalIssuedQty() {
  var totalIssued = 0;
  var rows = $('#tbl_product')["0"].rows.length - 1;

  if (rows != 0) {
    for (var x = 0; x < rows; x++) {
      totalIssued += parseInt($('#tbl_product')["0"].children[1].children[x].cells[1].children["0"].value);
    }
  }

  $('#issued_qty_table').val(totalIssued);
}

function validateInput() {
  var rows = $('#tbl_product')["0"].rows.length - 1;

  if (rows != 0) {
    for (var x = 0; x < rows; x++) {
      var issued = parseInt($('#tbl_product')["0"].children[1].children[x].cells[1].children["0"].value);
      var sched = parseInt($('#tbl_product')["0"].children[1].children[x].cells[2].children["0"].value);

      if (issued == 0 || issued < 0) {
        return "invalid";
      } // if (issued > sched) {
      // 	return "morethan";
      // }

    }
  }

  return "NONE";
}

function SaveTravelSheet() {
  $('.loadingOverlay').show();
  $.ajax({
    url: $('#frm_travel_sheet').attr('action'),
    type: 'POST',
    dataType: 'JSON',
    data: $('#frm_travel_sheet').serialize()
  }).done(function (data, textStatus, xhr) {
    if (data.status == "success") {
      msg(data.msg, data.status);
      $('#travel_sheet_id').val(data.travel_sheet_id);
      joDetailsList($('#status').val(), '', '');
      $('#btn_travel_sheet_preview').prop('disabled', false);
    } else if (data.status == "warning") {
      msg(data.msg, data.status);
    }
  }).fail(function (xhr, textStatus, errorThrown) {
    var errors = xhr.responseJSON.errors;
    showErrors(errors);
  }).always(function () {
    $('.loadingOverlay').hide();
  });
}

function getBom(set, prod_code, old_data) {
  var pros = '<tr>' + '<td colspan="2">No data</td>' + '</tr>';
  $('#tbl_process_body').html(pros);
  $.ajax({
    url: getBomURL,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token,
      sets: set,
      prod_code: prod_code
    }
  }).done(function (data, textStatus, xhr) {
    if (data.length > 0) {
      $('#tbl_process_body').html('');
      $.each(data, function (i, x) {
        console.log(x);
        pros = '<tr>' + '<td>' + x.process[0] + '<input type="hidden" name="processes[]" value="' + x.process[0] + '">' + '<input type="hidden" name="sequence[]" value="' + x.process[2] + '">' + '</td>' + '<td>' + '<select class="form-control form-control-ms" name="div_code[]">';

        if (old_data !== undefined) {
          pros += "<option value='" + old_data[i].div_code + "' hidden>" + old_data[i].div_code + "</option>";
        }

        if (x.process[1].length > 0) {
          selected = '';
          $.each(x.process[1], function (ii, xx) {
            if (old_data !== undefined) {
              if (old_data[i].process_name == x.process[0]) {
                selected = 'selected';
              }
            }

            pros += "<option value='" + xx.div_code + "'>" + xx.div_code + "</option>";
          });
        } else {
          pros += "<option value=''></option>";
        }

        pros += '</select><div id="division_feedback"></div>' + '</td>' + '</tr>';
        $('#tbl_process_body').append(pros);
      });
    } else {
      $('#tbl_process_body').html('<tr>' + '<td colspan="2">No data displayed.</td>' + '</tr>');
    }
  }).fail(function (xhr, textStatus, errorThrown) {
    msg(errorThrown, textStatus);
  });
}

function makeProdTable(arr, all_sc) {
  $('#tbl_product').dataTable().fnClearTable();
  $('#tbl_product').dataTable().fnDestroy();
  $('#tbl_product').dataTable({
    data: arr,
    bLengthChange: false,
    scrollY: "250px",
    paging: false,
    searching: false,
    columns: [{
      data: function data(x) {
        return x.prod_code;
      },
      searchable: false,
      orderable: false
    }, {
      data: function data(x) {
        return '<input type="number" step="1" name="issued_qty_per_sheet[]" id="issued_qty_' + x.id + '" data-id="' + x.id + '" data-old_qty="' + x.issued_qty + '" class="form-control issued_qty_per_sheet" value="' + x.issued_qty + '">';
      },
      searchable: false,
      orderable: false
    }, {
      data: function data(x) {
        var scnos = x.sc_no;
        var qty = 0;
        $.each(all_sc, function (i, x) {
          if (scnos.includes(x)) {
            qty = parseFloat(qty) + parseFloat(sched_qty_arr[i]);
          }
        });
        return '<input type="number" step="1" name="sc_qty[]" id="sc_qty_' + x.id + '" class="form-control sc_qty" value="' + qty + '" readonly>';
      },
      searchable: false,
      orderable: false
    }, {
      data: function data(x) {
        var scnos = x.sc_no; // var scnos = scno.split(',');

        var options = ''; // all_sc = sc.split(',');

        $.each(all_sc, function (i, x) {
          if (scnos.includes(x)) {
            options += '<option value="' + x + '" selected>' + x + '</option>';
          } else {
            options += '<option value="' + x + '">' + x + '</option>';
          }
        });
        var index = parseFloat(x.id) - 1;

        if (index == NaN) {
          index = 0;
        }

        return '<select name="scno[' + index + '][]" data-id="' + x.id + '" class="form-control form-control-sm scno" multiple="multiple">' + options + '</select>';
      },
      searchable: false,
      orderable: false
    }]
  });

  if (arr.length > 0) {
    getTotalIssuedQty();
  } else {
    $('#issued_qty_table').val(0);
  }

  $('.scno').select2();
}

function joDetailsList(status, from, to) {
  jo_details = [];
  $.ajax({
    url: joDetailsListURL,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token,
      status: status,
      fromvalue: from,
      tovalue: to
    }
  }).done(function (data, textStatus, xhr) {
    jo_details = data;
    makeJODetailsTable(jo_details);
  }).fail(function (xhr, textStatus, errorThrown) {
    msg(errorThrown, textStatus);
  });
}

function makeJODetailsTable(arr) {
  $('#tbl_jo_details').dataTable().fnClearTable();
  $('#tbl_jo_details').dataTable().fnDestroy();
  $('#tbl_jo_details').dataTable({
    data: arr,
    order: [[2, 'asc']],
    columns: [{
      data: function data(_data) {
        return '<input type="checkbox" value="' + _data.jo_no + '"  data-status="' + _data.status + '" class="table-checkbox jo_check">';
      },
      name: 'id',
      orderable: false,
      searchable: false
    }, {
      data: function data(_data2) {
        return '<button type="button" class="btn btn-sm bg-green open_travel_sheet_modal"' + ' data-jo_no="' + _data2.jo_no + '" data-prod_code="' + _data2.product_code + '" ' + ' data-issued_qty="' + _data2.issued_qty + '"data-id="' + _data2.id + '" ' + ' data-status="' + _data2.status + '"  data-sched_qty="' + _data2.sched_qty + '" ' + ' data-qty_per_sheet="' + _data2.qty_per_sheet + '"  data-iso_code="' + _data2.iso_code + '"' + ' data-sc_no="' + _data2.sc_no + '" data-idJO="' + _data2.idJO + '"' + ' title="Travel Sheet"><i class="fa fa-file-text-o"></i> </button>';
      },
      name: 'action',
      orderable: false,
      searchable: false
    }, {
      data: 'jo_no',
      name: 'jt.jo_no'
    }, {
      data: 'product_code',
      name: 'jt.prod_code'
    }, {
      data: 'description',
      name: 'jt.description'
    }, {
      data: 'back_order_qty',
      name: 'jt.order_qty'
    }, {
      data: 'sched_qty',
      name: 'jt.sched_qty'
    }, {
      data: 'issued_qty',
      name: 'ts.issued_qty'
    }, {
      data: 'material_used',
      name: 'jt.material_used'
    }, {
      data: 'material_heat_no',
      name: 'jt.material_heat_no'
    }, {
      data: function data(_data3) {
        switch (_data3.status) {
          case 0:
            return 'No quantity issued';
            break;

          case 1:
            return 'Ready of printing';
            break;

          case 2:
            return 'On Production';
            break;

          case 3:
            return 'CANCELLED';
            break;

          case 5:
            return 'CLOSED';
            break;

          default:
            return _data3.status;
            break;
        }
      },
      name: 'ts.status'
    }]
  });
}

function get_set() {
  var set = '<option value="None"></option>';
  $('#set').html(set);
  $.ajax({
    url: getSetURL,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token
    }
  }).done(function (data, textStatus, xhr) {
    $.each(data, function (i, x) {
      set = '<option value="' + x.id + '">' + x.set + '</option>';
      $('#set').append(set);
    });
  }).fail(function (xhr, textStatus, errorThrown) {
    msg(errorThrown, textStatus);
  });
}

function getPreTravelSheetData(id) {
  prod_arr = [];
  $.ajax({
    url: getPreTravelSheetDataURL,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token,
      id: id
    }
  }).done(function (data, textStatus, xhr) {
    prod_arr = data.prod;
    makeProdTable(prod_arr, scno_arr);
    $('#set').val(data.sets);
    getBom(data.sets, data.prod_code, data.process);
  }).fail(function (xhr, textStatus, errorThrown) {
    msg(errorThrown, textStatus);
  });
}

function getSC(idJO) {
  $.ajax({
    url: getSc_noURL,
    type: 'POST',
    dataType: 'JSON',
    data: {
      _token: token,
      id: idJO
    }
  }).done(function (data, textStatus, xhr) {
    scno_arr = data.sc_no;
    sched_qty_arr = data.back_order_qty;
    makeProdTable(prod_arr, scno_arr);
  }).fail(function (xhr, textStatus, errorThrown) {
    msg(errorThrown, textStatus);
  });
}

/***/ }),

/***/ 17:
/*!***************************************************************************************!*\
  !*** multi ./resources/assets/js/pages/ppc/transactions/travel-sheet/travel-sheet.js ***!
  \***************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\xampp\htdocs\en-pms\resources\assets\js\pages\ppc\transactions\travel-sheet\travel-sheet.js */"./resources/assets/js/pages/ppc/transactions/travel-sheet/travel-sheet.js");


/***/ })

/******/ });