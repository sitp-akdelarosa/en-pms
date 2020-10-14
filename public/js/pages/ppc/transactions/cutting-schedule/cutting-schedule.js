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

/***/ "./resources/assets/js/pages/ppc/transactions/cutting-schedule/cutting-schedule.js":
/*!*****************************************************************************************!*\
  !*** ./resources/assets/js/pages/ppc/transactions/cutting-schedule/cutting-schedule.js ***!
  \*****************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

var details = [];
var operators = [];
var d = new Date();
var month = d.getMonth() + 1;
var day = d.getDate();
var date = d.getFullYear() + '-' + (month < 10 ? '0' : '') + month + '-' + (day < 10 ? '0' : '') + day;
var total_needed_qty = 0;
var added_total_needed_qty = 0;
var allowed_issued_qty = 0;
var total_issued_qty = 0;
var qty_needed_inbox = false;
var jo_items = [];
$(function () {
  getProdline();
  getCutSchedDetails();
  autoComplete("#leader", getAllOperatorsURL, "fullname");
  getISO('#iso_control_no');
  getLeaders();
  init();
  checkAllCheckboxesInTable('.check_all_items', '.check_items');
  $('#btn_save').on('click', function () {
    saveCutSched();
  });
  $('#btn_cancel').on('click', function () {
    console.log(operators);
    clear();
  });
  $('#date_issued').val(date); // $('#withdrawal_slip').prop('readonly', true);

  $('#trans_no').on('focusout', function () {
    if ($.trim($(this).val()) != "") {
      getMaterialsForCuttingSched($(this).val());
    }
  });
  $("#trans_no").on('keyup', function () {
    if ($.trim($(this).val()) == "") {
      $('#tbl_cut_sched_body').html('<tr><td colspan="11" class="text-center">No data displayed.</td></tr>');
      $('.check_all_items').prop("checked", false);
    }
  });
  $('#btn_add').on('click', function () {
    var count = details.length;
    var Validation = 'None';
    var id = $(this).attr('data-id');
    var error = false;
    var msg_over_issuance = "";
    $(".check_items:checked").each(function () {
      var trans_no = $(this).attr('data-trans_no');
      var id = $(this).attr('data-id');
      var jo_item_id = jo_items.map(function (x) {
        return x.id;
      }).indexOf(id);
      var jo_item = jo_items[jo_item_id];

      var _allowed_qty = parseFloat(jo_item.allowed_qty);

      var _needed_qty = parseFloat(jo_item.needed_qty);

      if (_allowed_qty >= _needed_qty) {
        var new_allowed_qty = _allowed_qty - _needed_qty;
        jo_items.splice(jo_item_id, 1, {
          id: id,
          allowed_qty: new_allowed_qty,
          needed_qty: _needed_qty
        });
        $.each(details, function (i, x) {
          if (trans_no.indexOf(x.type) == -1) {
            Validation = 'Please Insert same kind of transaction';
          } // else if(x.id == id){
          // 	Validation = 'Some of the Item already in the table';
          // }

        });

        if (Validation == 'None') {
          if (trans_no.indexOf('JO') > -1) {
            var needed_qty = $(this).attr('data-needed_qty');

            if (qty_needed_inbox) {
              $('#tbl_cut_sched').find('input[data-id="' + id + '"]').each(function () {
                needed_qty = $(this).val();
              });
            }

            if (_typeof($(this).attr('data-supplier_heat_no')) === undefined) {
              alert('undefined');
            }

            details.push({
              trans_no: $(this).attr('data-trans_no'),
              no: $(this).attr('data-trans_no'),
              id: id,
              alloy: $(this).attr('data-alloy'),
              code_description: $(this).attr('data-code_description'),
              sc_no: $(this).attr('data-sc_no'),
              order_qty: $(this).attr('data-order_qty'),
              issued_qty: needed_qty,
              needed_qty: $(this).attr('data-needed_qty'),
              item: $(this).attr('data-item'),
              schedule: $(this).attr('data-schedule'),
              cut_weight: $(this).attr('data-cut_weight'),
              cut_length: $(this).attr('data-cut_length'),
              size: $(this).attr('data-size'),
              lot_no: $(this).attr('data-lot_no'),
              mat_heat_no: $(this).attr('data-mat_heat_no'),
              product_code: $(this).attr('data-product_code'),
              p_item: $(this).attr('data-p_item'),
              p_class: $(this).attr('data-p_class'),
              p_alloy: $(this).attr('data-p_alloy'),
              p_size: $(this).attr('data-p_size'),
              supplier_heat_no: $(this).attr('data-supplier_heat_no'),
              type: 'JO'
            }); // $('#withdrawal_slip').val('');
            // $('#withdrawal_slip').prop('readonly', true);
          }
        }

        console.log(jo_items);
        error = false;
      } else {
        msg_over_issuance += "<br>" + $(this).attr('data-p_item');
        error = true;
      }
    });

    if (error == true) {
      msg("Over issuance in item/s:" + msg_over_issuance, "warning");
    }

    if ($('.check_items:checked').length == 0) {
      msg('Please select at least 1 item.', 'warning');
    }

    if (Validation != 'None') {
      msg(Validation, 'warning');
    }

    makeCutDetailsTable(details);
  });
  $('#tbl_cut_sched').on('change', '.check_items', function () {
    var id = $(this).attr('data-id'); // var jo_item_id = jo_items.map(function (x) {
    // 	return x.id;
    // }).indexOf(id);
    // var jo_item = jo_items[jo_item_id];
    // var allowed_qty = parseInt(jo_item.allowed_qty);
    // var needed_qty = parseInt($(this).attr('data-needed_qty'));

    if (qty_needed_inbox) {
      $('#tbl_cut_sched').find('input[data-id="' + id + '"]').each(function () {
        if ($.trim($(this).val()) == "") {
          needed_qty = 0;
        } else {
          needed_qty = parseInt($(this).val());
        }
      });
    }

    if ($(this).prop("checked") == true) {
      $('#tbl_cut_sched').find('input[data-id="' + id + '"]').each(function () {
        $('input[class="needed_qty"][data-id="' + id + '"]').attr('disabled', 'disabled');
      }); // if (allowed_qty >= needed_qty) {
      // 	jo_items.splice(jo_item_id, 1,
      // 		{ id: id, allowed_qty: allowed_qty, needed_qty: needed_qty }
      // 	);
      // } else {
      // 	$(this).prop("checked", false);
      // 	$('#tbl_cut_sched').find('input[data-id="' + id + '"]').each(function () {
      // 		$('input[class="needed_qty"][data-id="' + id + '"]').removeAttr('disabled');
      // 	});
      // }
    } else if ($(this).prop("checked") == false) {
      $('#tbl_cut_sched').find('input[data-id="' + id + '"]').each(function () {
        $('input[class="needed_qty"][data-id="' + id + '"]').removeAttr('disabled');
      }); // jo_items.splice(jo_item_id, 1,
      // 	{ id: id, allowed_qty: allowed_qty, needed_qty: 0 }
      // );
    }

    console.log(jo_items);
  });
  $('#tbl_cut_sched').on('change', '.check_all_items', function () {
    var _needed_qty = parseInt($(this).attr('data-needed_qty'));

    if (qty_needed_inbox) {
      $('#tbl_cut_sched').find('input[class="needed_qty"]').each(function () {
        if ($.trim($(this).val()) == "") {
          _needed_qty = 0;
        } else {
          _needed_qty = parseInt($(this).val());
        }
      });
    }

    if ($(this).prop("checked") == true) {
      $('#tbl_cut_sched').find('input[class="needed_qty"]').each(function () {
        $(this).attr('disabled', 'disabled');
      }); // $(".check_items:checked").each(function () {
      // 	var id = $(this).attr('data-id');
      // 	var jo_item_id = jo_items.map(function (x) {
      // 		return x.id;
      // 	}).indexOf(id);
      // 	var jo_item = jo_items[jo_item_id];
      // 	var _allowed_qty = parseInt(jo_item.allowed_qty);
      // 	if (_allowed_qty >= _needed_qty) {
      // 		jo_items.splice(jo_item_id, 1,
      // 			{ id: id, allowed_qty: _allowed_qty, needed_qty: _needed_qty }
      // 		);
      // 	} else {
      // 		$(this).prop("checked", false);
      // 		$('#tbl_cut_sched').find('input[class="needed_qty"]').each(function () {
      // 			$(this).removeAttr('disabled');
      // 		});
      // 	}
      // 	console.log(jo_items);
      // })
    } else if ($(this).prop("checked") == false) {
      $('#tbl_cut_sched').find('input[class="needed_qty"]').each(function () {
        $(this).removeAttr('disabled');
      }); // $(".check_items:not(:checked)").each(function () {
      // 	var id = $(this).attr('data-id');
      // 	var jo_item_id = jo_items.map(function (x) {
      // 		return x.id;
      // 	}).indexOf(id);
      // 	var jo_item = jo_items[jo_item_id];
      // 	var _allowed_qty = parseInt(jo_item.allowed_qty);
      // 	jo_items.splice(jo_item_id, 1,
      // 		{ id: id, allowed_qty: _allowed_qty, needed_qty: 0 }
      // 	);
      // 	console.log(jo_items);
      // })
    }
  });
  $('#tbl_cut_details_body').on('click', '.btn_remove', function () {
    details.splice($(this).attr('data-count'), 1);
    var id = $(this).attr('data-id');
    var new_allowed_qty;
    var issued_qty = 0;

    if ($(this).attr('data-issued_qty') == "") {
      issued_qty = 0;
    } else {
      issued_qty = parseInt($(this).attr('data-issued_qty'));
    } // var jo_item_id = jo_items.map(function (x) {
    // 	return x.id;
    // }).indexOf(id);
    // var jo_item = jo_items[jo_item_id];
    // var _allowed_qty = parseInt(jo_item.allowed_qty);
    // var _needed_qty = parseInt(jo_item.needed_qty);
    // new_allowed_qty = _allowed_qty + issued_qty;
    // jo_items.splice(jo_item_id, 1,
    // 	{ id: id, allowed_qty: new_allowed_qty, needed_qty: _needed_qty }
    // );
    // console.log(jo_items);


    var count = 1;
    $.each(details, function (i, x) {
      var num = x.no;

      if (Number.isInteger(num)) {
        x.no = count;
        count++;
      }
    });
    makeCutDetailsTable(details);

    if (details.length == 0) {
      $('#trans_no').prop('readonly', false);
    }
  }); // $('#btn_print_preview').on('click', function () {
  // 	// if (details.length > 5) {
  // 	// 	msg('Form must be 5 items only','warning');
  // 	// }else
  // 	if (details.length == 0 || $('#machine_no').val() == '' || $('#prepared_by').val() == '' || $('#iso_control_no').val() == '' || $('#date_issued').val() == '') {
  // 		msg("Please fill out all required fields.", "failed");
  // 	} else {
  // 		var print_preview = pdfCuttingScheduleURL + '?ids=' + $('input[name="id[]"]').map(function () { return $(this).val(); }).get() +
  // 			'&&withdrawal_slip=' + $('#withdrawal_slip').val() +
  // 			'&&date_issued=' + $('#date_issued').val() +
  // 			'&&machine_no=' + $('#machine_no').val() +
  // 			'&&prepared_by=' + $('#prepared_by').val() +
  // 			'&&leader=' + $('#leader').val() +
  // 			'&&type=' + details[0]['type'] +
  // 			'&&iso_control_no=' + $('#iso_control_no').val();
  // 		var ids = $('input[name="id[]"]').map(function () { return $(this).val(); }).get();
  // 		if (ids.length < 1) {
  // 			msg("No materials were selected.", "failed");
  // 		} else {
  // 			window.open(print_preview, '_tab');
  // 		}
  // 	}
  // });

  $('#btn_print_preview').on('click', function () {
    // if (details.length > 5) {
    // 	msg('Form must be 5 items only','warning');
    // }else
    if (details.length == 0 || $('#machine_no').val() == '' || $('#prepared_by').val() == '' || $('#iso_control_no').val() == '' || $('#date_issued').val() == '') {
      msg("Please fill out all required fields.", "failed");
    } else {
      var print_preview = pdfCuttingScheduleURL + '?' + $('#frm_cut_sched').serialize();
      var ids = $('input[name="id[]"]').map(function () {
        return $(this).val();
      }).get();

      if (ids.length < 1) {
        msg("No materials were selected.", "failed");
      } else {
        window.open(print_preview, '_tab');
      }
    }
  });
  $('#tbl_cut_sched_reprint_body').on('click', '.btn_reprint', function () {
    window.open(pdfCuttingScheduleReprintURL + '?id=' + $(this).attr('data-id'), '_tab');
  });
});

function init() {
  check_permission(code_permission, function (output) {
    if (output == 1) {}
  });
}

function getMaterialsForCuttingSched(trans_no) {
  $.ajax({
    url: materialCuttingSchedURL,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token,
      trans_no: trans_no
    }
  }).done(function (data, textStatus, xhr) {
    if (data.length > 0) {
      $('.check_all_items').prop('checked', false);
      var tbl_cut_sched_body = '';
      $('#tbl_cut_sched_body').html(tbl_cut_sched_body);
      $('#tbl_cut_sched_body').html('');

      if (trans_no.includes('JO') == true) {
        $.each(data, function (i, x) {
          var mat_heat_no = '';
          var needed_qty;
          var plate_qty;
          var schedule = x.schedule;
          var size = x.size;
          var schedOrSize;

          if (x.material_heat_no == undefined) {
            mat_heat_no = '';
          } else {
            mat_heat_no = x.material_heat_no;
          }

          if ($.trim(schedule) != "" && $.trim(size) == "") {
            schedOrSize = schedule;
          } else if ($.trim(schedule) == "" && $.trim(size) != "") {
            schedOrSize = size;
          } else if ($.trim(schedule) != "" && $.trim(size) != "") {
            schedOrSize = size + " x " + schedule;
          }

          var cut = " x " + x.cut_weight;

          if (x.cut_weight == "") {
            cut = "";
          } else {
            if (x.cut_weight == '0.00N/A' || x.cut_weight == '0.00KG') {
              cut = " x " + x.cut_length;

              if (x.cut_length == "") {
                cut = "";
              }
            }
          }

          if (qty_needed_inbox) {
            plate_qty = "<input type='number' class='needed_qty' data-id='" + x.id + "' placeholder='Qty of Plate'></input>";
            needed_qty = "<td>" + x.needed_qty + "</td>";

            if (x.schedule == "") {
              schedule = "";
            }

            tbl_cut_sched_body = "<tr>" + "<td rowspan='2'>" + "<input type='checkbox' class='table-checkbox check_items' data-trans_no='" + trans_no + "' " + "data-no='" + x.jo_no + "' " + "data-id='" + x.id + "' " + "data-alloy='" + x.alloy + "' " + "data-cut_weight='" + x.cut_weight + "' " + "data-cut_length='" + x.cut_length + "' " + "data-code_description='" + x.code_description + "' " + "data-sc_no='" + x.sc_no + "' " + "data-order_qty='" + x.order_qty + "' " + "data-issued_qty='" + x.issued_qty + "' " + "data-needed_qty='" + x.needed_qty + "' " + "data-item='" + x.item + "' " + "data-size='" + x.size + "' " + "data-schedule='" + x.schedule + "' " + "data-lot_no='" + x.lot_no + "' " + "data-product_code='" + x.product_code + "' " + "data-p_item='" + x.p_item + "' " + "data-p_alloy='" + x.p_alloy + "' " + "data-p_size='" + x.p_size + "' " + "data-p_class='" + x.p_class + "' " + "data-supplier_heat_no='" + x.supplier_heat_no + "' " + "data-mat_heat_no='" + mat_heat_no + "'>" + "</td>" + "<td rowspan='2'>" + x.p_alloy + "</td>" + "<td rowspan='2'>" + x.p_size + "</td>" + "<td rowspan='2'>" + x.p_item + "</td>" + "<td rowspan='2'>" + x.p_class + "</td>" + "<td rowspan='2'>" + x.sc_no + "</td>" + "<td rowspan='2'>" + x.order_qty + "</td>" + "<td>" + plate_qty + "</td>" + "<td rowspan='2'></td>" + "<td><span class='pull-left'>" + x.item + "</span><span class='pull-right'>" + schedOrSize + cut + "</span></td>" + "</tr>" + "<tr>" + // "<td>"+x.product_code+"</td>"+
            // "<td>" + x.needed_qty + "</td>" +
            needed_qty + "<td><span class='pull-left'>" + x.lot_no + "</span><span class='pull-right'>" + mat_heat_no + " / " + x.supplier_heat_no + "</span></td>" + "</tr>";
            $('#tbl_cut_sched_body').append(tbl_cut_sched_body);
          } else {
            plate_qty = x.needed_qty;
            needed_qty = "<td></td>";
            tbl_cut_sched_body = "<tr>" + "<td rowspan='2'>" + "<input type='checkbox' class='table-checkbox check_items' data-trans_no='" + trans_no + "' " + "data-id='" + x.id + "' " + "data-alloy='" + x.alloy + "' " + "data-code_description='" + x.code_description + "' " + "data-sc_no='" + x.sc_no + "' " + "data-order_qty='" + x.order_qty + "' " + "data-needed_qty='" + x.needed_qty + "' " + "data-cut_weight='" + x.cut_weight + "' " + "data-cut_length='" + x.cut_length + "' " + "data-size='" + x.size + "' " + "data-item='" + x.item + "' " + "data-schedule='" + x.schedule + "' " + "data-lot_no='" + x.lot_no + "' " + "data-p_item='" + x.p_item + "' " + "data-p_alloy='" + x.p_alloy + "' " + "data-p_size='" + x.p_size + "' " + "data-p_class='" + x.p_class + "' " + "data-product_code='" + x.product_code + "' " + "data-supplier_heat_no='' " + "data-mat_heat_no='" + mat_heat_no + "'>" + "</td>" + "<td rowspan='2'>" + x.p_alloy + "</td>" + "<td rowspan='2'>" + x.p_size + "</td>" + "<td rowspan='2'>" + x.p_item + "</td>" + "<td rowspan='2'>" + x.p_class + "</td>" + "<td rowspan='2'>" + x.sc_no + "</td>" + "<td rowspan='2'>" + x.order_qty + "</td>" + "<td rowspan='2'>" + plate_qty + "</td>" + "<td rowspan='2'></td>" + //"<td class='text-center'>"+x.cut_weight+" x "+x.cut_length+"</td>"+
            "<td class='text-center'>" + schedOrSize + cut + "</td>" + "</tr>" + "<tr>" + "<td><span class='pull-left'>-------</span><span class='pull-right'>" + x.lot_no + "</span></td>" + "</tr>";
            $('#tbl_cut_sched_body').append(tbl_cut_sched_body);
          } // tbl_cut_sched_body = "<tr>" +
          // 	"<td rowspan='2'>" +
          // 	"<input type='checkbox' class='table-checkbox check_items' data-trans_no='" + trans_no + "' " +
          // 	"data-id='" + x.id + "' " +
          // 	"data-alloy='" + x.alloy + "' " +
          // 	"data-code_description='" + x.code_description + "' " +
          // 	"data-sc_no='" + x.sc_no + "' " +
          // 	"data-order_qty='" + x.order_qty + "' " +
          // 	"data-needed_qty='" + x.needed_qty + "' " +
          // 	"data-cut_weight='" + x.cut_weight + "' " +
          // 	"data-cut_length='" + x.cut_length + "' " +
          // 	"data-size='" + x.size + "' " +
          // 	"data-item='" + x.item + "' " +
          // 	"data-schedule='" + x.schedule + "' " +
          // 	"data-lot_no='" + x.lot_no + "' " +
          // 	"data-p_item='" + x.p_item + "' " +
          // 	"data-p_alloy='" + x.p_alloy + "' " +
          // 	"data-p_size='" + x.p_size + "' " +
          // 	"data-p_class='" + x.p_class + "' " +
          // 	"data-product_code='" + x.product_code + "' " +
          // 	"data-mat_heat_no='" + mat_heat_no + "'>" +
          // 	"</td>" +
          // 	"<td rowspan='2'>" + x.p_alloy + "</td>" +
          // 	"<td rowspan='2'>" + x.p_size + "</td>" +
          // 	"<td rowspan='2'>" + x.p_item + "</td>" +
          // 	"<td rowspan='2'>" + x.p_class + "</td>" +
          // 	"<td rowspan='2'>" + x.sc_no + "</td>" +
          // 	"<td rowspan='2'>" + x.order_qty + "</td>" +
          // 	"<td rowspan='2'>" + plate_qty + "</td>" +
          // 	"<td rowspan='2'></td>" +
          // 	//"<td class='text-center'>"+x.cut_weight+" x "+x.cut_length+"</td>"+
          // 	"<td class='text-center'>" + x.size + " x " + $cut + "</td>" +
          // 	"</tr>" +
          // 	"<tr>" +
          // 	"<td><span class='pull-left'>-------</span><span class='pull-right'>" + x.lot_no + "</span></td>" +
          // 	"</tr>";
          // $('#tbl_cut_sched_body').append(tbl_cut_sched_body);


          var jo_item_id = jo_items.map(function (x) {
            return x.id;
          }).indexOf(x.id);

          if (jo_item_id < 0) {
            jo_items.push({
              id: x.id,
              allowed_qty: x.needed_qty,
              needed_qty: 0
            });
          }

          console.log(jo_items);
        });
      } else {
        tbl_cut_sched_body = "<tr>" + "<td colspan='11' class='text-center'>No data displayed.</td>" + "</tr>";
        $('#tbl_cut_sched_body').append(tbl_cut_sched_body);
      } //  else {
      // 	var errormsg = false;
      // 	$.each(data, function (i, x) {
      // 		total_issued_qty = total_issued_qty + x.issued_qty
      // 		allowed_issued_qty = allowed_issued_qty + x.needed_qty;
      // 		if (x.id != null) {
      // 			var mat_heat_no = '';
      // 			var sc_no = '';
      // 			var order_qty = 0;
      // 			var plate_qty;
      // 			if (x.material_heat_no == undefined) {
      // 				mat_heat_no = '';
      // 			} else {
      // 				mat_heat_no = x.material_heat_no;
      // 			}
      // 			if (!x.sc_no || x.sc_no == "null") {
      // 				sc_no = '';
      // 			} else {
      // 				sc_no = x.sc_no;
      // 			}
      // 			if (x.order_qty !== null) {
      // 				order_qty = x.order_qty;
      // 			}
      // 			if (qty_needed_inbox) {
      // 				plate_qty = "<input type='number' class='needed_qty' data-id='" + x.id + "' placeholder='Qty of Plate'></input>";
      // 			} else {
      // 				plate_qty = x.issued_qty
      // 			}
      // 			tbl_cut_sched_body = "<tr>" +
      // 				"<td rowspan='2'>" +
      // 				"<input type='checkbox' class='table-checkbox check_items' data-trans_no='" + trans_no + "' " +
      // 				"data-no='" + x.jo_no + "' " +
      // 				"data-id='" + x.id + "' " +
      // 				"data-alloy='" + x.alloy + "' " +
      // 				"data-code_description='" + x.code_description + "' " +
      // 				"data-sc_no='" + sc_no + "' " +
      // 				"data-order_qty='" + order_qty + "' " +
      // 				"data-issued_qty='" + x.issued_qty + "' " +
      // 				"data-needed_qty='" + x.needed_qty + "' " +
      // 				"data-item='" + x.item + "' " +
      // 				"data-size='" + x.size + "' " +
      // 				"data-schedule='" + x.schedule + "' " +
      // 				"data-lot_no='" + x.lot_no + "' " +
      // 				"data-product_code='" + x.product_code + "' " +
      // 				"data-p_item='" + x.p_item + "' " +
      // 				"data-p_alloy='" + x.p_alloy + "' " +
      // 				"data-p_size='" + x.p_size + "' " +
      // 				"data-p_class='" + x.p_class + "' " +
      // 				"data-mat_heat_no='" + mat_heat_no + "'>" +
      // 				"</td>" +
      // 				"<td rowspan='2'>" + x.p_alloy + "</td>" +
      // 				"<td rowspan='2'>" + x.p_size + "</td>" +
      // 				"<td rowspan='2'>" + x.p_item + "</td>" +
      // 				"<td rowspan='2'>" + x.p_class + "</td>" +
      // 				"<td rowspan='2'>" + sc_no + "</td>" +
      // 				"<td rowspan='2'>" + order_qty + "</td>" +
      // 				"<td>" + plate_qty + "</td>" +
      // 				"<td rowspan='2'></td>" +
      // 				"<td><span class='pull-left'>" + x.item + "</span><span class='pull-right'>" + x.size + "</span></td>" +
      // 				"</tr>" +
      // 				"<tr>" +
      // 				// "<td>"+x.product_code+"</td>"+
      // 				"<td>" + x.needed_qty + "</td>" +
      // 				"<td><span class='pull-left'>" + mat_heat_no + "</span><span class='pull-right'>" + x.lot_no + "</span></td>" +
      // 				"</tr>";
      // 			$('#tbl_cut_sched_body').append(tbl_cut_sched_body);
      // 		} else {
      // 			errormsg = true;
      // 		}
      // 	});
      // }
      // if (errormsg) {
      // 	msg("Some Heat Number is not yet scheduled or wrong input of sc no", "warning");
      // }

    } else {
      msg(data.msg, data.status);
    } //if (data.msg != undefined) {
    //} else {
    //msg(data.msg,data.status);
    //}

  }).fail(function (xhr, textStatus, errorThrown) {
    msg(errorThrown, textStatus);
  });
}

function makeCutDetailsTable(arr) {
  if (arr.length > 0) {
    var tbl_cut_details_body = '';
    $('#tbl_cut_details_body').html(tbl_cut_details_body);
    var count = 0;
    $.each(arr, function (i, x) {
      var needed_qty;

      if (x.trans_no.indexOf('JO') > -1) {
        var schedule = x.schedule;
        var size = x.size;
        var schedOrSize;

        if ($.trim(schedule) != "" && $.trim(size) == "") {
          schedOrSize = schedule;
        } else if ($.trim(schedule) == "" && $.trim(size) != "") {
          schedOrSize = size;
        } else if ($.trim(schedule) != "" && $.trim(size) != "") {
          schedOrSize = size + " x " + schedule;
        }

        var cut = " x " + x.cut_weight;

        if (x.cut_weight == "") {
          cut = "";
        } else {
          if (x.cut_weight == '0.00N/A' || x.cut_weight == '0.00KG') {
            cut = " x " + x.cut_length;

            if (x.cut_length == "") {
              cut = "";
            }
          }
        }

        if (qty_needed_inbox) {
          needed_qty = "<td>" + x.needed_qty + "<input type='hidden' name='needed_qty[]' value='" + x.needed_qty + "'>" + "</td>";
          tbl_cut_details_body = "<tr>" + "<td rowspan='2'>" + x.no + "<input type='hidden' name='no[]' value='" + x.no + "'>" + "<input type='hidden' name='id[]' value='" + x.id + "'>" + "</td>" + "<td rowspan='2'>" + x.p_alloy + "<input type='hidden' name='p_alloy[]' value='" + x.p_alloy + "'>" + "</td>" + "<td rowspan='2'>" + x.p_size + "<input type='hidden' name='p_size[]' value='" + x.p_size + "'>" + "</td>" + "</td>" + "<td rowspan='2'>" + x.p_item + "<input type='hidden' name='p_item[]' value='" + x.p_item + "'>" + "</td>" + "</td>" + "<td rowspan='2'>" + x.p_class + "<input type='hidden' name='p_class[]' value='" + x.p_class + "'>" + "</td>" + "<td rowspan='2'>" + x.sc_no + "<input type='hidden' name='sc_no[]' value='" + x.sc_no + "'>" + "</td>" + "<td rowspan='2'>" + x.order_qty + "<input type='hidden' name='order_qty[]' value='" + x.order_qty + "'>" + "</td>" + "<td>" + x.issued_qty + "<input type='hidden' name='issued_qty[]' value='" + x.issued_qty + "'>" + "</td>" + "<td rowspan='2'></td>" + "<td><span class='pull-left'>" + x.item + "</span><span class='pull-right'>" + schedOrSize + cut + "</span>" + "<input type='hidden' name='item[]' value='" + x.item + "'>" + "<input type='hidden' name='schedule[]' value='" + x.schedule + "'>" + "<input type='hidden' name='qty_needed_inbox[]' value='" + qty_needed_inbox + "'>" + "<input type='hidden' name='size[]' value='" + x.size + "'>" + "</td>" + "<td rowspan='2'>" + "<a href='javascript:;' style='color:#940000' class='fa fa-times btn_remove' data-id='" + x.id + "' data-issued_qty='" + x.issued_qty + "' data-count='" + count + "'></a>" + "</td>" + "</tr>" + "<tr>" + needed_qty + "<td><span class='pull-left'>" + x.lot_no + "</span><span class='pull-right'>" + x.mat_heat_no + " / " + x.supplier_heat_no + "</span></td>" + "<input type='hidden' name='mat_heat_no[]' value='" + x.mat_heat_no + "'>" + "<input type='hidden' name='supplier_heat_no[]' value='" + x.supplier_heat_no + "'>" + "<input type='hidden' name='lot_no[]' value='" + x.lot_no + "'>" + "<input type='hidden' name='cut_weight[]' value='" + x.cut_weight + "'>" + "<input type='hidden' name='cut_length[]' value='" + x.cut_length + "'>" + "</tr>";
          count++;
        } else {
          needed_qty = "<td></td>";
          tbl_cut_details_body = "<tr>" + "<td rowspan='2'>" + x.no + "<input type='hidden' name='no[]' value='" + x.no + "'>" + "<input type='hidden' name='id[]' value='" + x.id + "'>" + "</td>" + "<td rowspan='2'>" + x.p_alloy + "<input type='hidden' name='p_alloy[]' value='" + x.p_alloy + "'>" + "</td>" + "<td rowspan='2'>" + x.p_size + "<input type='hidden' name='p_size[]' value='" + x.p_size + "'>" + "</td>" + "<td rowspan='2'>" + x.p_item + "<input type='hidden' name='p_item[]' value='" + x.p_item + "'>" + "</td>" + "<td rowspan='2'>" + x.p_class + "<input type='hidden' name='p_class[]' value='" + x.p_class + "'>" + "</td>" + "<td rowspan='2'>" + x.sc_no + "<input type='hidden' name='sc_no[]' value='" + x.sc_no + "'>" + "</td>" + "</td>" + "<td rowspan='2'>" + x.order_qty + "<input type='hidden' name='order_qty[]' value='" + x.order_qty + "'>" + "</td>" + "<td rowspan='2'>" + x.needed_qty + "<input type='hidden' name='needed_qty[]' value='" + x.needed_qty + "'>" + "</td>" + "<td rowspan='2'></td>" + "<td class='text-center'>" + schedOrSize + cut + "<input type='hidden' name='cut_weight[]' value='" + x.cut_weight + "'>" + "<input type='hidden' name='cut_length[]' value='" + x.cut_length + "'>" + "<input type='hidden' name='schedule[]' value='" + x.schedule + "'>" + "<input type='hidden' name='size[]' value='" + x.size + "'>" + "</td>" + "<td rowspan='2'>" + "<a href='javascript:;' style='color:#940000' class='fa fa-times btn_remove' data-id='" + x.id + "' data-issued_qty='" + x.needed_qty + "' data-count='" + count + "'></a>" + "</td>" + "</tr>" + "<tr>" + "<td><span class='pull-left'>-------</span><span class='pull-right'>" + x.lot_no + "</span></td>" + "<input type='hidden' name='mat_heat_no[]' value='" + x.mat_heat_no + "'>" + "<input type='hidden' name='supplier_heat_no[]' value='" + x.supplier_heat_no + "'>" + "<input type='hidden' name='lot_no[]' value='" + x.lot_no + "'>" + "</tr>";
          count++;
        }
      } else {
        tbl_cut_details_body = "<tr>" + "<td colspan='11' class='text-center'>No data displayed.</td>" + "</tr>";
      }

      $('#tbl_cut_details_body').append(tbl_cut_details_body);
    });
  } else {
    $('#tbl_cut_details_body').html('<tr><td colspan="11" class="text-center">No data displayed.</td></tr>');
  }
}

function getProdline() {
  $.ajax({
    url: getProdLineURL,
    type: 'GET',
    dataType: 'JSON'
  }).done(function (data, txtStatus, xhr) {
    if (data[0].counter > 0) {
      qty_needed_inbox = true;
    } else {
      qty_needed_inbox = false;
    }

    console.log(data[0].counter);
  }).fail(function (xhr, txtStatus, errorThrown) {
    console.log(errorThrown);
  });
}

function saveCutSched() {
  if (details.length != 0) {
    $('.loadingOverlay').show();
    $.ajax({
      url: saveCutSchedURL,
      type: 'POST',
      dataType: 'JSON',
      data: $("#frm_cut_sched").serialize()
    }).done(function (data, txtStatus, xhr) {
      console.log(data);
      msg(data.msg, data.status);
      clear();
      getCutSchedDetails();
    }).fail(function (xhr, txtStatus, errorThrown) {
      console.log(xhr);
      var errors = xhr.responseJSON.errors;
      showErrors(errors);
    }).always(function () {
      $('.loadingOverlay').hide();
    });
  } else {
    msg("Cutting Schedule Table is empty!", "warning");
  }
}

function getCutSchedDetails() {
  $.ajax({
    url: getCutSchedDetailsURL,
    type: 'GET',
    dataType: 'JSON'
  }).done(function (data, txtStatus, xhr) {
    console.log(data);
    $('#tbl_cut_sched_reprint_body').html('');
    $('#tbl_cut_sched_reprint').dataTable().fnClearTable();
    $('#tbl_cut_sched_reprint').dataTable().fnDestroy();
    $('#tbl_cut_sched_reprint').dataTable({
      data: data,
      order: [[8, 'desc']],
      columns: [{
        data: function data(x) {
          return "<button class='btn btn-primary btn_reprint' data-id=" + x.id + "> Reprint </button>";
        },
        searchable: false,
        orderable: false
      }, {
        data: function data(x) {
          var res = x.item_nos.split(",");
          var jo = "";
          $.each(res, function (i, x) {
            jo += x + "<br>";
          });
          return jo;
        }
      }, {
        data: 'withdrawal_slip_no'
      }, {
        data: 'iso_control_no'
      }, {
        data: 'date_issued'
      }, {
        data: 'machine_no'
      }, {
        data: 'leader'
      }, {
        data: 'prepared_by'
      }, {
        data: 'created_at'
      }]
    });
  }).fail(function (xhr, txtStatus, errorThrown) {
    console.log(errorThrown);
  });
}

function clear() {
  $('.clear').val('');
  $('.clear').removeAttr('readonly');
  $('#leader').val('').trigger('change');
  $('#tbl_cut_details_body').html('<tr><td colspan="11" class="text-center">No data displayed.</td></tr>');
  $('#tbl_cut_sched_body').html('<tr><td colspan="11" class="text-center">No data displayed.</td></tr>');
  $('.check_all_items').prop("checked", false);
  total_needed_qty = 0;
  added_total_needed_qty = 0;
  allowed_issued_qty = 0;
  total_issued_qty = 0;
  jo_items = [];
  details = [];
}

function getLeaders() {
  $.ajax({
    url: getLeaderURL,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token
    }
  }).done(function (data, textStatus, xhr) {
    $('#leader').select2({
      allowClear: true,
      placeholder: 'Select a Leader',
      data: data
    }).val(null).trigger('change');
  }).fail(function (xhr, textStatus, errorThrown) {
    console.log("error");
  });
}

/***/ }),

/***/ 17:
/*!***********************************************************************************************!*\
  !*** multi ./resources/assets/js/pages/ppc/transactions/cutting-schedule/cutting-schedule.js ***!
  \***********************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\laragon\www\en-pms\resources\assets\js\pages\ppc\transactions\cutting-schedule\cutting-schedule.js */"./resources/assets/js/pages/ppc/transactions/cutting-schedule/cutting-schedule.js");


/***/ })

/******/ });