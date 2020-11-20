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

/***/ "./resources/assets/js/pages/ppc/transactions/production-schedule/production-schedule.js":
/*!***********************************************************************************************!*\
  !*** ./resources/assets/js/pages/ppc/transactions/production-schedule/production-schedule.js ***!
  \***********************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

var EditMode = false;
var schedmode = false;
var MainData;
var joDetails_arr = [];
var count;
var travel_Sheet = [];
var p_pcode = "";
var msg_non_standard;
var selected_heat_no = [];
var unique_heat_no = [];
var tbl_materials = $('#tbl_materials').DataTable();
var arr_materials = [];
var material_count = 0;
$(function () {
  initializePage();
  autoComplete("#jono", getjosuggest, "jo_no");
  $(document).on('shown.bs.tab', function () {
    $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
  });
  $(document).on('shown.bs.modal', function () {
    $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
  });
  $('#tbl_prod_sum_body').on('change', '.check_item_prod_sum', function (e) {
    e.preventDefault();

    if (schedmode == false) {
      if ($(this).is(":checked")) {
        var Exist = 0;
        var sc_no = $(this).attr('data-sc_no');
        var prod_code = $(this).attr('data-prod_code');
        var quantity = $(this).attr('data-quantity');
        $.each(joDetails_arr, function (i, x) {
          if (x.sc_no == sc_no && x.prod_code == prod_code && x.quantity == quantity) {
            Exist = "The item already on the table";
          }

          if (EditMode == true && x.prod_code != prod_code) {
            Exist = "Just choose same product code of " + x.prod_code;
          }
        });

        if (Exist == 0) {
          $('#no_data').remove();
          count = joDetails_arr.length;
          joDetails_arr.push({
            count: count,
            id: count,
            dataid: $(this).attr('data-id'),
            sc_no: sc_no,
            prod_code: prod_code,
            description: $(this).attr('data-description'),
            quantity: quantity,
            material_heat_no: "",
            rmw_issued_qty: 0,
            uom: "",
            material_used: "",
            lot_no: "",
            sched_qty: 0,
            material_type: "",
            for_over_issuance: 0,
            inv_id: 0,
            rmw_id: 0,
            totalsched_qty: 0,
            heat_no_id: 0,
            ship_date: ''
          });
        } else {
          msg(Exist, 'warning');
          $(this).prop('checked', false);
        }
      } else {
        var count = 0;
        var id = $(this).attr('data-id');
        $.each(joDetails_arr, function (i, x) {
          if (x.dataid == id) {
            joDetails_arr.splice(count, 1);
            return false;
          }

          count++;
        });
        var counts = 0;
        $.each(joDetails_arr, function (i, x) {
          joDetails_arr.push({
            count: counts,
            id: x.id,
            dataid: x.dataid,
            sc_no: x.sc_no,
            prod_code: x.prod_code,
            description: x.description,
            quantity: x.quantity,
            material_heat_no: x.material_heat_no,
            rmw_issued_qty: x.rmw_issued_qty,
            uom: x.uom,
            material_used: x.material_used,
            lot_no: x.lot_no,
            sched_qty: x.sched_qty,
            material_type: x.material_type,
            for_over_issuance: x.for_over_issuance,
            inv_id: x.inv_id,
            rmw_id: x.rmw_id,
            totalsched_qty: x.totalsched_qty,
            heat_no_id: x.heat_no_id,
            ship_date: x.ship_date
          });
          counts++;
        });

        while (counts != 0) {
          joDetails_arr.splice(0, 1);
          counts--;
        }

        if ($('#tbl_jo_details_body > tr').length < 1) {
          $('#tbl_jo_details_body').html('<tr id="no_data">' + '<td colspan="9" class="text-center">No data available.</td>' + '</tr>');
        }

        if (joDetails_arr.length < 1) {
          $('#btn_save_div').hide();
          $('#btn_check_over_issuance_div').hide();
          $('#btn_edit_div').show();
          $('#btn_cancel_div').hide();
        }
      }

      makeJODetailsList(joDetails_arr); // getMaterialHeatNo($('#rmw_no').val());

      $("#tbl_jo_details").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
    } else {
      msg("Not Allowed!!!", "warning");
      $('.check_item_prod_sum').prop('checked', false);
      joDetails_arr = [];
    } //console.log(joDetails_arr);

  });
  $('#jono').on('change', function () {
    $('.check_item_prod_sum').prop('checked', false);

    if ($(this).val() == "") {
      $(".check_item_prod_sum").prop("disabled", true);
      clear();
    } else {
      $(".check_item_prod_sum").prop("disabled", false);
    }

    getTables();
  });
  $("#status").on('change', changestatus);
  $('#tbl_jo_details_body').on('click', '.remove_jo_details', function () {
    var count = $(this).attr('data-count');
    var id = $(this).attr('data-dataid');
    joDetails_arr.splice(count, 1);
    $('#prod_sum_chk_' + id).prop('checked', false);
    var counts = 0;
    $.each(joDetails_arr, function (i, x) {
      joDetails_arr.push({
        count: counts,
        id: x.id,
        dataid: x.dataid,
        sc_no: x.sc_no,
        prod_code: x.prod_code,
        description: x.description,
        quantity: x.quantity,
        material_heat_no: x.material_heat_no,
        rmw_issued_qty: x.rmw_issued_qty,
        uom: x.uom,
        material_used: x.material_used,
        lot_no: x.lot_no,
        sched_qty: x.sched_qty,
        material_type: x.material_type,
        for_over_issuance: x.for_over_issuance,
        inv_id: x.inv_id,
        rmw_id: x.rmw_id,
        totalsched_qty: x.totalsched_qty,
        heat_no_id: x.heat_no_id,
        ship_date: x.ship_date
      });
      counts++;
    });

    while (counts != 0) {
      joDetails_arr.splice(0, 1);
      counts--;
    }

    getMaterialHeatNo($('#rmw_no').val());
    makeJODetailsList(joDetails_arr);

    if ($('#tbl_jo_details_body > tr').length < 1) {
      $('#tbl_jo_details_body').html('<tr id="no_data">' + '<td colspan="10" class="text-center">No data selected.</td>' + '</tr>');
    }

    if (joDetails_arr.length < 1 && $('#jono').val() == '') {
      $('#btn_save_div').hide();
      $('#btn_check_over_issuance_div').hide();
      $('#btn_edit_div').show();
      $('#btn_cancel_div').hide();
    }
  });
  $('#tbl_jo_details_body').on('change', '.material_used', function () {
    var material_used = $(this).val();

    if ($('#is_same').is(':checked')) {
      var count = $(this).attr('data-count');
      $.each(joDetails_arr, function (i, x) {
        x.matematerial_used = material_used;
        $('#material_used_' + x.count).val(material_used);
      });
    }

    compareStandardMaterialUsed(count, $(this).attr('data-pcode'), material_used);
    $('#btn_save_div').show();
    $('#btn_check_over_issuance_div').show();
    $('#btn_edit_div').hide();
    $('#btn_cancel_div').show();
  });
  $('#tbl_jo_details_body').on('keyup', '.lot_no', function () {
    var lot_no = $(this).val();
    var count = $(this).attr('data-count');

    if ($('#is_same').is(':checked')) {
      $.each(joDetails_arr, function (i, x) {
        x.lot_no = lot_no;
        $('#lot_no_' + x.count).val(lot_no);
      });
    } else {
      joDetails_arr[count].lot_no = lot_no;
    }
  });
  $('#tbl_jo_details_body').on('change', '.sched_qty', function (event) {
    var table_count = $(this).attr('data-count');
    var inputs = $(".sched_qty");
    var total = 0;

    for (var i = 0; i < inputs.length; i++) {
      if ($(inputs[i]).val() == '') {
        total = parseFloat(total) + 0;
      } else {
        var input = $(inputs[i]).val() == '' ? 0 : $(inputs[i]).val();
        total = parseFloat(total) + parseFloat(input);
      }
    }

    if (total === NaN) {
      total = 0;
    }

    $('#total_sched_qty').val(total);

    if ($(this).val() !== "") {
      var rmw_issued_qty = parseFloat($('#rmw_issued_qty_' + table_count).val());
      var this_heat_no = $('#material_heat_no_' + table_count).find('option:selected').attr('data-heat_no');
      var sched_qty = parseFloat($(this).val() == "" ? 0 : $(this).val());
      var count_same = 0;
      $.each(unique_heat_no, function (i, x) {
        if (x.heat_no == this_heat_no && x.rmw_qty == rmw_issued_qty) {
          count_same++;
        }
      });

      if (count_same > 0) {
        sched_qty = 0;

        for (var i = 0; i < inputs.length; i++) {
          var heat_no = $('#material_heat_no_' + i).find('option:selected').attr('data-heat_no');

          if (heat_no == this_heat_no) {
            sched_qty = sched_qty + parseInt($(inputs[i]).val() == "" ? 0 : $(inputs[i]).val());
          }
        }
      }

      var over_qty = 0;
      var is_over = false;
      var material_type = $('#material_type_' + table_count).val();
      var over = $('#for_over_issuance_' + table_count).val(); // check type of withdrawal if MATERIAL or PRODUCT

      if (material_type !== 'FINISHED' && material_type !== 'CRUDE') {
        switch (material_type) {
          case 'BAR':
            var bar_length = parseFloat(over);
            var bar_pcs = sched_qty / bar_length;
            over_qty = bar_pcs - rmw_issued_qty;
            $('#remaining_qty_' + table_count).val((rmw_issued_qty - bar_pcs).toFixed(2));

            if (over_qty > 0) {
              is_over = true;
            }

            break;

          case 'PIPE':
            var pipe_pcs = parseFloat(over);
            $('#remaining_qty_' + table_count).val((pipe_pcs - sched_qty).toFixed(2));

            if (sched_qty > pipe_pcs) {
              is_over = true;
            }

            break;

          case 'PLATE':
            var stock = parseFloat(over);
            $('#remaining_qty_' + table_count).val((stock - sched_qty).toFixed(2));

            if (sched_qty > stock) {
              is_over = true;
            }

            break;

          default:
            break;
        }
      } else {
        if (sched_qty > rmw_issued_qty) {
          over_qty = sched_qty - rmw_issued_qty;
          is_over = true;
        }
      }

      if (is_over) {
        error = "Your issuance has an over of " + over_qty.toFixed(2);
        $('#sched_qty_' + table_count).addClass('is-invalid');
        $('#sched_qty_' + table_count + '_feedback').addClass('invalid-feedback');
        $('#sched_qty_' + table_count + '_feedback').html(error);
      } else {
        //sum_sched_qty();
        $('#sched_qty_' + table_count).removeClass('is-invalid');
        $('#sched_qty_' + table_count + '_feedback').removeClass('invalid-feedback');
        $('#sched_qty_' + table_count + '_feedback').html('');
      }
    }
  });
  $('#btn_save').on('click', function () {
    console.log(joDetails_arr);

    if (joDetails_arr.length != 0) {
      var validate = "NONE";
      valid = validateTable(); // if (parseInt($('#total_sched_qty').val()) > parseInt($('#total_heat_qty').val())) {
      //     msg("You still have an over issuance." ,"warning");
      // } else 

      if (valid == "false") {
        msg("All fields are required.", "warning");
      } else if (valid == "true") {
        for (var y = 0; y < joDetails_arr.length; y++) {
          var quantity = parseInt($('#quantity_' + y).val());
          var sched_qty = parseInt($('#sched_qty_' + y).val());
          var totalsched_qty = parseInt($('#totalsched_qty' + y).val()) + sched_qty;

          if (quantity < sched_qty) {
            validate = "Some of Sched Qty is greater than quantity!";
          } else if (quantity < totalsched_qty) {
            validate = "Some of Total Sched Qty already reach the total quantity!";
          }
        }

        if (validate == "NONE") {
          $(".check_item_prod_sum").prop("disabled", false);
          SaveJODetails();
        } else {
          swal({
            title: "Are you sure to save?",
            text: validate,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#f95454",
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            closeOnConfirm: true,
            closeOnCancel: false
          }, function (isConfirm) {
            if (isConfirm) {
              $(".check_item_prod_sum").prop("disabled", false);
              SaveJODetails();
            } else {
              swal("Cancelled", "Saving production schedule is cancelled.");
            }
          });
        }
      } else {
        msg("Please input valid number in Sched Qty.", "warning");
      }
    } else {
      msg("Please input production schedule.", 'warning');
    }
  });
  $('#btn_cancel').on('click', function () {
    joDetails_arr = [];
    EditMode = false;
    makeJODetailsList(joDetails_arr);
    $('.check_item_prod_sum').prop('checked', false);
    clear();
    $('#btn_save_div').hide();
    $('#btn_check_over_issuance_div').hide();
    $('#btn_edit_div').show();
    $('#btn_cancel_div').hide();
    $('#jono').prop('readonly', true);
    $(".check_item_prod_sum").prop("disabled", false);
  });
  $('#btn_edit').on('click', function () {
    joDetails_arr = [];
    makeJODetailsList(joDetails_arr);
    $('#btn_save_div').show();
    $('#btn_check_over_issuance_div').show();
    $('#btn_edit_div').hide();
    $('#btn_cancel_div').show();
    $('.check_item_prod_sum').prop('checked', false);
    $('#jono').prop('readonly', false);
    $(".check_item_prod_sum").prop("disabled", true);
  });
  $('#tbl_travel_sheet').on('click', '.cancel_travel_sheet', function () {
    var id = $(this).attr('data-id');
    var jo_no = $(this).attr('data-jo_no');
    var idJTS = $(this).attr('data-idJO');
    swal({
      title: "Are you sure to cancel the travel sheet?",
      text: "All of the process in this travel sheet will be cancelled!",
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
          url: cancelTravelSheetURL,
          type: 'POST',
          dataType: 'JSON',
          data: {
            _token: token,
            id: id,
            jo_no: jo_no,
            idJTS: idJTS
          }
        }).done(function (data, textStatus, xhr) {
          if (data.status == 'success') {
            swal("Successful", "The travel sheet has been cancelled.");
            ProdSummaries(prodSummariesURL);
            travel_Sheet = [];
            getTravelSheet();
          } else {
            swal("Failed", "The travel sheet has been processing of goods.");
          }
        }).fail(function (xhr, textStatus, errorThrown) {
          ErrorMsg(xhr);
        });
      } else {
        swal("Cancelled", "The travel sheet is safe and not cancelled.");
      }
    });
  });
  $('#btn_search_withdrawal').on('click', function () {
    if ($('#rmw_no').val() == "") {
      msg("Please provide a Withdrawal Slip.", "failed");
    } else {
      //akeJODetailsList(joDetails_arr);
      $('.material_used').val('');
      getMaterialHeatNo($('#rmw_no').val(), $('#prod_code').val());
      $("#tbl_materials").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
    }
  });
  $('#tbl_materials_body').on('change', '.material_heat_no', function () {
    //$('.loadingOverlay').show();
    p_pcode = $(this).attr('data-pcode');
    var rmw_issued_qty = $(this).find('option:selected').attr('data-rmw_issued_qty');
    var uom = $(this).find('option:selected').attr('data-uom');
    var inv_id = $(this).find('option:selected').attr('data-inv_id');
    var rmw_id = $(this).find('option:selected').attr('data-rmw_id');
    var heat_no = $(this).find('option:selected').attr('data-heat_no');
    var material_heat_no = $(this).val(); // rmw_id

    var material_used_code = $(this).find('option:selected').attr('data-item_code');
    var material_used_size = $(this).find('option:selected').attr('data-size') == undefined ? "" : $(this).find('option:selected').attr('data-size').toString().toUpperCase().replace(/\s+/g, '');
    var material_used_schedule = $(this).find('option:selected').attr('data-schedule');
    var material_used_description = $(this).find('option:selected').attr('data-description');
    var standard_material_used = $(this).find('option:selected').attr('data-standard_material_used') == undefined ? "" : $(this).find('option:selected').attr('data-standard_material_used').toString().toUpperCase().replace(/\s+/g, '');
    var for_over_issuance = $(this).find('option:selected').attr('data-for_over_issuance');
    var material_type = $(this).find('option:selected').attr('data-material_type');
    var lot_no = $(this).find('option:selected').attr('data-lot_no');
    var table_index = $(this).attr('data-count');

    if ($('#is_same').is(':checked')) {
      $.each(joDetails_arr, function (i, x) {
        // assign values
        $('.material_heat_no').select2().val(material_heat_no);
        $('#rmw_issued_qty_' + x.count).val(rmw_issued_qty);
        $('#uom_' + x.count).val(uom);
        $('#uom_span_' + x.count).html(uom);
        $('#inv_id_' + x.count).val(inv_id);
        $('#rmw_id_' + x.count).val(rmw_id);
        $('#heat_no_id_' + x.count).val(material_heat_no);
        var selected_mat_used = ""; // check standard material used

        if (material_type !== 'FINISHED' && material_type !== 'CRUDE') {
          switch (standard_material_used) {
            case material_used_code:
              selected_mat_used = material_used_code;
              break;

            case material_used_size:
              selected_mat_used = material_used_size;
              break;

            case material_used_schedule:
              selected_mat_used = material_used_schedule;
              break;

            default:
              selected_mat_used = material_used_description;
              break;
          }

          $('#material_used_' + x.count).val(material_used_description);

          if (standard_material_used != selected_mat_used) {
            var error = "This is not the Product's Standard Material";
            $('#material_used_' + x.count).addClass('is-invalid');
            $('#material_used_' + x.count + '_feedback').addClass('invalid-feedback');
            $('#material_used_' + x.count + '_feedback').html(error);
          } else {
            $('#material_used_' + x.count).removeClass('is-invalid');
            $('#material_used_' + x.count + '_feedback').removeClass('invalid-feedback');
            $('#material_used_' + x.count + '_feedback').html('');
          }
        } else {
          $('#material_used_' + x.count).val(material_used_description);
          $('#lot_no_' + x.count).val(lot_no);
        } // alot material type and comparison for over issuance


        $('#for_over_issuance_' + x.count).val(for_over_issuance);
        $('#material_type_' + x.count).val(material_type);
        x.material_heat_no = heat_no;
        x.rmw_issued_qty = rmw_issued_qty;
        x.uom = uom;
        x.material_used = selected_mat_used;
        x.material_type = material_type;
        x.for_over_issuance = for_over_issuance;
        x.inv_id = inv_id;
        x.rmw_id = rmw_id;
        x.heat_no_id = material_heat_no;
      });
    } else {
      $('#rmw_issued_qty_' + table_index).val(rmw_issued_qty);
      $('#uom_' + table_index).val(uom);
      $('#uom_span_' + table_index).html(uom);
      $('#inv_id_' + table_index).val(inv_id);
      $('#rmw_id_' + table_index).val(rmw_id);
      $('#heat_no_id_' + table_index).val(material_heat_no);
      var selected_mat_used = "";

      if (material_type !== 'FINISHED' && material_type !== 'CRUDE') {
        // check standard material used
        switch (standard_material_used) {
          case material_used_code:
            selected_mat_used = material_used_code;
            break;

          case material_used_size:
            selected_mat_used = material_used_size;
            break;

          case material_used_schedule:
            selected_mat_used = material_used_schedule;
            break;

          default:
            selected_mat_used = material_used_description;
            break;
        }

        $('#material_used_' + table_index).val(material_used_description);

        if (standard_material_used != selected_mat_used) {
          var error = "This is not the Product's Standard Material";
          $('#material_used_' + table_index).addClass('is-invalid');
          $('#material_used_' + table_index + '_feedback').addClass('invalid-feedback');
          $('#material_used_' + table_index + '_feedback').html(error);
        } else {
          $('#material_used_' + table_index).removeClass('is-invalid');
          $('#material_used_' + table_index + '_feedback').removeClass('invalid-feedback');
          $('#material_used_' + table_index + '_feedback').html('');
        } // alot material type and comparison for over issuance


        $('#for_over_issuance_' + table_index).val(for_over_issuance);
        $('#material_type_' + table_index).val(material_type);
        joDetails_arr[table_index].material_heat_no = heat_no;
        joDetails_arr[table_index].rmw_issued_qty = rmw_issued_qty;
        joDetails_arr[table_index].uom = uom;
        joDetails_arr[table_index].material_used = selected_mat_used;
        joDetails_arr[table_index].material_type = material_type;
        joDetails_arr[table_index].for_over_issuance = for_over_issuance;
        joDetails_arr[table_index].inv_id = inv_id;
        joDetails_arr[table_index].rmw_id = rmw_id;
        joDetails_arr[table_index].heat_no_id = material_heat_no;
      } else {
        $('#material_used_' + x.count).val(material_used_description);
        $('#lot_no_' + x.count).val(lot_no);
      }
    }

    var inputs = $(".rmw_issued_qty");
    var total = 0;

    if ($('#is_same').is(':checked')) {
      $('#total_heat_qty').val($('#rmw_issued_qty_' + table_index).val());
    } else {
      for (var i = 0; i < inputs.length; i++) {
        if ($(inputs[i]).val() == '') {
          total = parseInt(total) + 0;
        } else {
          total = parseInt(total) + parseInt($(inputs[i]).val());
        }
      }

      if (total === NaN) {
        total = 0;
      }

      $('#total_heat_qty').val(total);
    } // get all selected HEAT NUMBER


    var heat_nos = $('.material_heat_no');
    selected_heat_no = [];

    for (var i = 0; i < heat_nos.length; i++) {
      if ($(heat_nos[i]).val() !== "" && $(heat_nos[i]).val() !== null) {
        selected_heat_no.push({
          heat_no: $(heat_nos[i]).find('option:selected').attr('data-heat_no'),
          rmw_qty: $(heat_nos[i]).find('option:selected').attr('data-rmw_issued_qty')
        });
      }
    } // get all UNIQUE HEAT NUMBER


    var unique = [];
    unique_heat_no = [];

    for (var _i = 0; _i < selected_heat_no.length; _i++) {
      if (!unique[selected_heat_no[_i].heat_no]) {
        if (!unique[selected_heat_no[_i].rmw_qty]) {
          unique_heat_no.push({
            heat_no: selected_heat_no[_i].heat_no,
            rmw_qty: selected_heat_no[_i].rmw_qty
          });
        }

        unique[selected_heat_no[_i].rmw_qty] = 1;
      }
    }

    $('#btn_save_div').show();
    $('#btn_check_over_issuance_div').show();
    $('#btn_edit_div').hide();
    $('#btn_cancel_div').show();
    $("#tbl_materials").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");

    if ($(this).val() == "") {
      $('.loadingOverlay').hide();
    }
  });
  $('#btn_filter').on('click', function () {
    $('.srch-clear').val('');
    $('#modal_order_search').modal('show');
  });
  $("#frm_search").on('submit', function (e) {
    e.preventDefault();
    $('.loadingOverlay-modal').show();
    var search_param = objectifyForm($(this).serializeArray());
    ProdSummariesTable($(this).attr('action'), search_param);
  });
  $('#btn_search_excel').on('click', function () {
    window.location.href = excelSearchFilterURL + "?srch_date_upload_from=" + $('#srch_date_upload_from').val() + "&srch_date_upload_to=" + $('#srch_date_upload_to').val() + "&srch_sc_no=" + $('#srch_sc_no').val() + "&srch_prod_code=" + $('#srch_prod_code').val() + "&srch_description=" + $('#srch_description').val() + "&srch_po=" + $('#srch_po').val();
  });
  $('#tbl_jo_details').on('click', '.open_materials', function () {
    var arr_items = [];
    arr_materials = [];
    $('#ship_date').val('');
    $('#rmw_no').val('');
    arr_items.push({
      sc_no: $(this).attr('data-sc_no'),
      prod_code: $(this).attr('data-prod_code'),
      description: $(this).attr('data-description'),
      order_qty: $(this).attr('data-quantity')
    });
    $('#sc_no').val($(this).attr('data-sc_no'));
    $('#prod_code').val($(this).attr('data-prod_code'));
    $('#code_description').val($(this).attr('data-description'));
    $('#back_order_qty').val($(this).attr('data-quantity'));
    makeItemDataTable(arr_items);
    makeMaterialsDataTable(arr_materials);
    $('#modal_item_materials').modal('show');
  });
  $('#tbl_materials_body').on('click', '.btn_remove_material', function () {
    populateOldMaterialData();
    arr_materials.splice($(this).attr('data-count'), 1);
    makeMaterialsDataTable(arr_materials);
  });
  $('#add_material_row').on('click', function () {
    tbl_materials.row.add(["<button type='button' class='btn btn-sm bg-red btn_remove_material'>" + "<i class='fa fa-times'></i>" + "</button>", "<input type='number' step='0.01' name='sched_qty[]' class='form-control form-control-sm sched_qty' />", "<select id='material_heat_no_" + material_count + "' name='material_heat_no[]' class='form-control form-control-sm material_heat_no'></select>", "<div class='input-group input-group-sm'>" + "<input type='number' step='0.01' name='rmw_issued_qty[]' class='form-control form-control-sm rmw_issued_qty' readonly>" + "<div class='input-group-append'>" + "<span class='input-group-text'>PCS</span>" + "</div>", "<input type='text' name='material_used[]' class='form-control form-control-sm material_used' readonly/>", "<input type='text' name='lot_no[]' class='form-control form-control-sm lot_no' />", "<input type='text' name='blade_consumption[]' class='form-control form-control-sm blade_consumption' />", "<input type='text' name='cut_weight[]' class='form-control form-control-sm cut_weight' />", "<input type='text' name='cut_length[]' class='form-control form-control-sm cut_length' />", "<input type='text' name='cut_width[]' class='form-control form-control-sm cut_width' />", "<input type='text' name='mat_length[]' class='form-control form-control-sm mat_length' />", "<input type='text' name='mat_weight[]' class='form-control form-control-sm mat_weight' />", "<input type='number' step='0.01' name='assign_qty[]' class='form-control form-control-sm assign_qty' />", "<input type='number' step='0.01' name='remaining_qty[]' class='form-control form-control-sm remaining_qty' />"]).draw(false);
    material_count++;
  });
  $('#btn_save_material').on('click', function () {
    var error = 0;
    $('.mat_validate').each(function () {
      if ($(this).is(":empty")) {
        error++;
      }
    });

    if ($('#ship_date').val() == "" && $('#ship_date').val() == null) {
      msg('Please fill out Ship Date input field.', 'warning');
    } else if (error > 0) {
      msg('Please fill out all input field in the table.', 'warning');
    } else if ($('#rmw_no').val() == "" && $('#rmw_no').val() == null) {
      msg('Please fill out Withdrawal Slip input field.', 'warning');
    } else {
      saveMaterials();
    }
  });
  $('#tbl_materials_body').on('change', '.sched_qty', function () {
    var count = $(this).attr('data-count');
    var material_type = $('#material_type_' + count).val();
    var material_length = $('#mat_length_' + count).val() == '' ? 0 : parseFloat($('#mat_length_' + count).val());
    var cut_legnth = $('#cut_legnth_' + count).val() == '' ? 0 : parseFloat($('#cut_length_' + count).val());
    var cut_weight = $('#cut_weight_' + count).val() == '' ? 0 : parseFloat($('#cut_weight_' + count).val());
    var size = $('#size_' + count).val() == '' ? 0 : parseFloat($('#size_' + count).val());
    var sched_qty = $(this).val() == '' ? 0 : parseFloat($(this).val());
    var blade_consumption = $('#blade_consumption_' + count).val() == '' ? 0 : parseFloat($('#blade_consumption_' + count).val());
    var qty_pcs = 0;

    switch (material_type) {
      case 'BAR':
        if (cut_weight !== 0 && $('#blade_consumption_' + count).val() !== '') {
          qty_pcs = material_length / (cut_legnth + blade_consumption);
          $('#computed_per_piece_' + count).val(qty_pcs.toFixed(2));
        } else if (cut_weight == 0 && $('#cut_weight_' + count).val() == '' && $('#blade_consumption_' + count).val() !== '') {
          var length = cut_weight / size / size / 6.2 * 1000000;
          qty_pcs = material_length / (length + blade_consumption);
          $('#computed_per_piece_' + count).val(qty_pcs.toFixed(2));
        }

        break;

      default:
        break;
    }

    if ($('#assign_qty_' + count).val() !== '') {
      checkOverIssuance(count);
    }
  });
  $('#tbl_materials_body').on('change', '.blade_consumption', function () {
    var count = $(this).attr('data-count');
    var material_type = $('#material_type_' + count).val();
    var material_length = $('#mat_length_' + count).val() == '' ? 0 : parseFloat($('#mat_length_' + count).val());
    var cut_legnth = $('#cut_legnth_' + count).val() == '' ? 0 : parseFloat($('#cut_length_' + count).val());
    var cut_weight = $('#cut_weight_' + count).val() == '' ? 0 : parseFloat($('#cut_weight_' + count).val());
    var size = $('#size_' + count).val() == '' ? 0 : parseFloat($('#size_' + count).val());
    var sched_qty = $('#sched_qty_' + count).val() == '' ? 0 : parseFloat($('#sched_qty_' + count).val());
    var blade_consumption = $(this).val() == '' ? 0 : parseFloat($(this).val());
    var qty_pcs = 0;

    switch (material_type) {
      case 'BAR':
        if (cut_weight !== 0 && $(this).val() !== '') {
          qty_pcs = material_length / (cut_legnth + blade_consumption);
          $('#computed_per_piece_' + count).val(qty_pcs.toFixed(2));
        } else if (cut_weight == 0 && $('#cut_weight_' + count).val() == '' && $(this).val() !== '') {
          var length = cut_weight / size / size / 6.2 * 1000000;
          qty_pcs = material_length / (length + blade_consumption);
          $('#computed_per_piece_' + count).val(qty_pcs.toFixed(2));
        }

        break;

      default:
        break;
    }

    if ($('#assign_qty_' + count).val() !== '') {
      checkOverIssuance(count);
    }
  });
  $('#tbl_materials_body').on('change', '.assign_qty', function () {
    var count = $(this).attr('data-count');
    checkOverIssuance(count);
  });
});

function checkOverIssuance(count) {
  var computed_per_piece_whole = $('#computed_per_piece_' + count).val() == '' ? 0 : parseInt($('#computed_per_piece_' + count).val());
  var computed_per_piece_deci = $('#computed_per_piece_' + count).val() == '' ? 0 : parseFloat($('#computed_per_piece_' + count).val());
  var excess = (computed_per_piece_deci - computed_per_piece_whole).toFixed(2);
  var assign_qty = $('#assign_qty_' + count).val() == '' ? 0 : parseFloat($('#assign_qty_' + count).val());
  var over = 0;
  var sched_qty = $('#sched_qty_' + count).val() == '' ? 0 : parseFloat($('#sched_qty_' + count).val());
  over = sched_qty - computed_per_piece_whole * assign_qty;

  if (over > 0) {
    $('#sched_qty_' + count).addClass('is-invalid');
    $('#sched_qty_' + count).next().addClass('invalid-feedback').html("Over Issuance.");
  } else {
    $('#sched_qty_' + count).removeClass('is-invalid');
    $('#sched_qty_' + count).next().removeClass('invalid-feedback').html('');
  }

  $('#remaining_qty_' + count).val((assign_qty * excess).toFixed(2));
}

function initializePage() {
  check_permission(code_permission, function (output) {
    if (output == 1) {}
  });
  ProdSummariesTable(prodSummariesURL, {
    _token: token
  });
  checkAllCheckboxesInTable('.check-all_prod_sum', '.check_item');
  makeJODetailsList(joDetails_arr);
  getTravelSheet();
  $('.material_heat_no').prop('disabled', true);
  $('#btn_save_div').hide();
  $('#btn_check_over_issuance_div').hide();
  $('#btn_edit_div').show();
  $('#btn_cancel_div').hide();
}

function changestatus() {
  if ($('#status').val() == "") {
    $('#status').val("");
    $('#tbl_jo_details').dataTable().fnClearTable();
    $('#tbl_jo_details').dataTable().fnDestroy();
    joDetails_arr = [];
    makeJODetailsList(joDetails_arr);
    var showform = document.getElementById('formbaba').style.display = 'inline';
    schedmode = false;
    joDetails_arr = [];
    $('#sched_qty').attr('disabled', false);
    $('.material_heat_no').attr('disabled', false);
    $('.material_used').attr('disabled', false);
    $('.lot_no').attr('disabled', false);
    $(".remove_jo_details").css("visibility", "visible");
    clear();
  } else {
    var hideform = document.getElementById('formbaba').style.display = 'none';
    getTablesAll();
    schedmode = true;
    joDetails_arr = [];
    $('.check_item_prod_sum').prop('checked', false);
  }
}

function ProdSummaries(PRODURL) {
  $('.loadingOverlay').show();
  $.ajax({
    url: PRODURL,
    type: 'GET',
    dataType: 'JSON'
  }).done(function (data, textStatus, xhr) {
    ProdSummariesTable(data);
  }).fail(function (xhr, textStatus, errorThrown) {
    $('.loadingOverlay').hide();
    ErrorMsg(xhr);
  }).always(function () {});
}

function ProdSummariesTable(ajax_url, object_data) {
  $('#tbl_prod_sum').dataTable().fnClearTable();
  $('#tbl_prod_sum').dataTable().fnDestroy();
  $('#tbl_prod_sum').dataTable({
    ajax: {
      url: ajax_url,
      data: object_data
    },
    processing: true,
    deferRender: true,
    order: [[1, 'asc']],
    language: {
      aria: {
        sortAscending: ": activate to sort column ascending",
        sortDescending: ": activate to sort column descending"
      },
      emptyTable: "No data available in table",
      info: "Showing _START_ to _END_ of _TOTAL_ records",
      infoEmpty: "No records found",
      infoFiltered: "(filtered1 from _MAX_ total records)",
      lengthMenu: "Show _MENU_",
      search: "Search:",
      zeroRecords: "No matching records found",
      paginate: {
        "previous": "Prev",
        "next": "Next",
        "last": "Last",
        "first": "First"
      }
    },
    columns: [{
      data: function data(_data) {
        return "<input type='checkbox' class='table-checkbox check_item_prod_sum'" + "id='prod_sum_chk_" + _data.id + "'" + "data-id='" + _data.id + "'" + "data-sc_no='" + _data.sc_no + "'" + "data-prod_code='" + _data.prod_code + "'" + "data-description='" + _data.description + "'" + "data-quantity='" + _data.quantity + "'" + "data-status='" + _data.status + "'" + "data-sched_qty='" + _data.sched_qty + "'>";
      },
      name: 'id',
      'searchable': false,
      'orderable': false
    }, {
      data: 'sc_no',
      name: 'ps.sc_no'
    }, {
      data: 'prod_code',
      name: 'ps.prod_code'
    }, {
      data: 'description',
      name: 'ps.description'
    }, {
      data: 'quantity',
      name: 'ps.quantity'
    }, {
      data: 'sched_qty',
      name: 'ps.sched_qty'
    }, {
      data: 'po',
      name: 'ps.po'
    }, {
      data: 'status',
      name: 'ps.status'
    }, {
      data: 'date_upload',
      name: 'ps.date_upload'
    }],
    initComplete: function initComplete() {
      $('.loadingOverlay').hide();
      $('.loadingOverlay-modal').hide();
    }
  });
}

function getDatatablesearch() {
  if ($('#from').val() != "" && $('#to').val() != "") {
    ProdSummaries(prodSummariesURL + '?fromvalue=' + $('#from').val() + '&tovalue=' + $('#to').val()); // getDatatable('tbl_prod_sum',prodSummariesURL + '?fromvalue='+ $('#from').val()+'&tovalue='+ $('#to').val(),dataColumn,[],0);
  } else if ($('#from').val() != "") {
    ProdSummaries(prodSummariesURL + '?fromvalue=' + $('#from').val()); // getDatatable('tbl_prod_sum',prodSummariesURL + '?fromvalue='+ $('#from').val(),dataColumn,[],0);
  } else {
    msg("From Input is required", "warning");
  }
}

function makeJODetailsList(arr) {
  var data = arr.sort(function (a, b) {
    return a.prod_code < b.prod_code ? -1 : a.prod_code > b.prod_code ? 1 : 0;
  });
  $('#tbl_jo_details').dataTable().fnClearTable();
  $('#tbl_jo_details').dataTable().fnDestroy();
  $('#tbl_jo_details').dataTable({
    data: data,
    bLengthChange: false,
    paging: false,
    order: [[1, 'asc']],
    //scrollX: true,
    columns: [{
      data: function data(x) {
        return "<button type='button' class='btn btn-sm bg-blue open_materials' data-count='" + x.count + "' " + "data-id='" + x.id + "' data-dataid='" + x.dataid + "' " + "data-sc_no='" + x.sc_no + "' data-prod_code='" + x.prod_code + "' " + "data-description='" + x.description + "' data-quantity='" + x.quantity + "' " + "data-ship_date='" + x.ship_date + "' " + ">" + "<i class='fa fa-edit'></i>" + "</button>" + "<button type='button' class='btn btn-sm bg-red remove_jo_details' data-count='" + x.count + "' data-id='" + x.id + "' data-dataid='" + x.dataid + "'>" + "<i class='fa fa-times'></i>" + "</button>" + "<input type='hidden' name='dataid[]' value='" + x.dataid + "'>";
      },
      searchable: false,
      orderable: false,
      width: '5%'
    }, {
      data: function data(x) {
        return x.sc_no + "<input type='hidden' class='form-control form-control-sm' name='sc_no[]' value='" + x.sc_no + "'>";
      },
      searchable: true,
      orderable: false,
      width: '15%'
    }, {
      data: function data(x) {
        return x.prod_code + "<input type='hidden' class='form-control form-control-sm' name='prod_code[]' value='" + x.prod_code + "'>";
      },
      searchable: true,
      orderable: false,
      width: '30%'
    }, {
      data: function data(x) {
        return x.description + "<input type='hidden' class='form-control form-control-sm' name='description[]' value='" + x.description + "'>";
      },
      searchable: true,
      orderable: false,
      width: '40%'
    }, {
      data: function data(x) {
        return x.quantity + "<input type='hidden' id='quantity_" + x.count + "' class='form-control form-control-sm' name='quantity[]' value='" + x.quantity + "'>";
      },
      searchable: true,
      orderable: false,
      width: '10%'
    }],
    initCompelete: function initCompelete(settings, json) {
      $("#tbl_jo_details").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
      $('.material_heat_no').select2({
        allowClear: true,
        placeholder: 'Select a Heat No.' // data: data

      }).val(null);
      $('.material_heat_no').prop('disabled', true);
    }
  });
}

function getMaterialHeatNo(withdrawal_slip_no, prod_code, state) {
  var materials = [];
  var $msg = "";
  var $status = "";
  $('.loadingOverlay-modal').show(); // var op = "<option value=''></option>";
  //         $('.material_heat_no').html(op);

  $.ajax({
    url: getMaterialHeatNoURL,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token,
      rmw_no: withdrawal_slip_no,
      prod_code: prod_code,
      state: state
    }
  }).done(function (data, textStatus, xhr) {
    materials = data.materials;
    $msg = data.msg;
    $status = data.status;
    material_count = 0;

    if (arr_materials.length > 0) {
      arr_materials = [];
    }

    console.log(materials);

    if (materials.length > 0) {
      $.each(materials, function (indx, mat) {
        arr_materials.push({
          count: '',
          sched_qty: '',
          material_heat_no: mat.heat_no,
          rmw_issued_qty: mat.rmw_issued_qty,
          material_code: mat.mat_code,
          standard_material_used: mat.standard_material_used,
          lot_no: mat.lot_no,
          blade_consumption: '',
          cut_weight: mat.cut_weight,
          cut_length: mat.cut_length,
          cut_width: mat.cut_width,
          mat_length: mat.rmw_length,
          mat_weight: mat.std_weight,
          description: mat.description,
          assign_qty: '',
          remaining_qty: '',
          upd_inv_id: mat.upd_inv_id,
          inv_id: mat.inv_id,
          rmwd_id: mat.rmw_id,
          size: mat.size,
          material_type: mat.material_type
        });
        material_count++; // op += "<option value='" + mat.rmw_id + "' " +
        //     "data-heat_no='" + mat.heat_no + "' " +
        //     "data-uom='" + mat.uom + "' " +
        //     "data-rmw_issued_qty='" + mat.rmw_issued_qty + "' " +
        //     "data-rmw_scheduled_qty='" + mat.rmw_scheduled_qty + "' " +
        //     "data-rmw_id='" + mat.rmw_id + "' " +
        //     "data-inv_id='" + mat.inv_id + "' " +
        //     "data-rmw_length='" + mat.rmw_length + "' " +
        //     "data-upd_inv_id='" + mat.upd_inv_id + "' " +
        //     "data-material_type='" + mat.material_type + "' " +
        //     "data-for_over_issuance='" + mat.for_over_issuance + "' " +
        //     "data-standard_material_used='" + mat.standard_material_used + "' " +
        //     "data-description='" + mat.description + "' " +
        //     "data-size='" + mat.size + "' " +
        //     "data-item_code='" + mat.item_code + "' " +
        //     "data-schedule='" + mat.schedule + "' " +
        //     "data-lot_no='" + mat.lot_no + "' " +
        //     "'>" +
        //     mat.text +
        //     "</option>";
      });

      if (state == 'edit') {// $.each(arr_materials, function(i,x) {
        //     $('#material_heat_no_' + i).append(op);
        //     $('#material_heat_no_' + i).select2({
        //         allowClear: true,
        //         placeholder: 'Select a Heat No.',
        //         width: '100%'
        //     });
        //     $('#material_heat_no_' + i).val(x.heat_no_id).trigger('change')
        // });
      } else {
        // $('.material_heat_no').append(op);
        // $('.material_heat_no').select2({
        //     allowClear: true,
        //     placeholder: 'Select a Heat No.',
        //     width: '100%'
        // });
        console.log(arr_materials);
        makeMaterialsDataTable(arr_materials);
      }
    } else {
      msg('No Materials available for this Withdrawal Transaction.', 'failed');
    }
  }).fail(function (xhr, textStatus, errorThrown) {
    ErrorMsg(xhr);
  }).always(function () {
    $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
    $('.loadingOverlay-modal').hide();
  }); // if (materials.length > 0) {
  //     msg($msg, $status);
  // }
}

function SaveJODetails() {
  $('.loadingOverlay').show();
  var rows = $('#tbl_jo_details')["0"].rows.length - 1;
  var jo_no = "";

  if (EditMode) {
    jo_no = $('#jono').val();
  }

  $.ajax({
    url: savejodetailsURL,
    type: 'POST',
    dataType: 'JSON',
    data: {
      _token: token,
      id: $('input[name="dataid[]"]').map(function () {
        return $(this).val();
      }).get(),
      sc_no: $('input[name="sc_no[]"]').map(function () {
        return $(this).val();
      }).get(),
      prod_code: $('input[name="prod_code[]"]').map(function () {
        return $(this).val();
      }).get(),
      description: $('input[name="description[]"]').map(function () {
        return $(this).val();
      }).get(),
      quantity: $('input[name="quantity[]"]').map(function () {
        return $(this).val();
      }).get(),
      material_heat_no: $('.material_heat_no').map(function () {
        return $(this).find('option:selected').attr('data-heat_no');
      }).get(),
      rmw_issued_qty: $('.rmw_issued_qty').map(function () {
        return $(this).val();
      }).get(),
      uom: $('.uom').map(function () {
        return $(this).val();
      }).get(),
      material_used: $('.material_used').map(function () {
        return $(this).val();
      }).get(),
      lot_no: $('input[name="lot_no[]"]').map(function () {
        return $(this).val();
      }).get(),
      totalsched_qty: $('input[name="totalsched_qty[]"]').map(function () {
        return $(this).val();
      }).get(),
      sched_qty: $('input[name="sched_qty[]"]').map(function () {
        return $(this).val();
      }).get(),
      material_type: $('input[name="material_type[]"]').map(function () {
        return $(this).val();
      }).get(),
      for_over_issuance: $('input[name="for_over_issuance[]"]').map(function () {
        return $(this).val();
      }).get(),
      rmw_id: $('input[name="rmw_id[]"]').map(function () {
        return $(this).val();
      }).get(),
      heat_no_id: $('input[name="heat_no_id[]"]').map(function () {
        return $(this).val();
      }).get(),
      inv_id: $('input[name="inv_id[]"]').map(function () {
        return $(this).val();
      }).get(),
      ship_date: $('input[name="ship_date[]"]').map(function () {
        return $(this).val();
      }).get(),
      filtercount: rows,
      total_sched_qty: $('#total_sched_qty').val(),
      rmw_no: $('#rmw_no').val(),
      jo_no: jo_no
    }
  }).done(function (data, textStatus, xhr) {
    EditMode = false;
    clear();
    msg("Scheduled Successful \n JO Number:" + data.jocode, "success"); // if(data.result == 'warning'){
    //     msg("Some of Schedule Quantity is greater than Quantity","warning");
    // }

    $('#jono').prop('readonly', true);
    $('#tbl_jo_details').dataTable().fnClearTable();
    $('#tbl_jo_details').dataTable().fnDestroy();
    $('#total_sched_qty').val('');
    $('#btn_save_div').hide();
    $('#btn_check_over_issuance_div').hide();
    $('#btn_edit_div').show();
    $('#btn_cancel_div').hide();
    ProdSummariesTable(prodSummariesURL, {
      _token: token
    });
    joDetails_arr = [];
    makeJODetailsList(joDetails_arr);
    getTravelSheet();
  }).fail(function (xhr, textStatus, errorThrown) {
    ErrorMsg(xhr);
  }).always(function (xhr, textStatus) {
    $('#rmw_no').prop('readonly', false);
    $('.loadingOverlay').hide();
  });
}

function validateTable() {
  var rows = $('#tbl_jo_details')["0"].rows.length - 1;
  var checkvalues = [];

  for (var x = 0; x < rows; x++) {
    if ($('#tbl_jo_details')["0"].children[1].children[x].cells[1].textContent == "") {
      return "false";
    }

    if ($('#tbl_jo_details')["0"].children[1].children[x].cells[3].textContent == "") {
      return "false";
    }

    if ($('#tbl_jo_details')["0"].children[1].children[x].cells[2].textContent == "") {
      return "false";
    }

    if ($('#tbl_jo_details')["0"].children[1].children[x].cells[4].textContent == "") {
      return "false";
    }

    if ($('#tbl_jo_details')["0"].children[1].children[x].cells[6].children["0"].value == "") {
      return "false";
    }

    if ($('#tbl_jo_details')["0"].children[1].children[x].cells[7].children["0"].value == "") {
      return "false";
    }

    if ($('#tbl_jo_details')["0"].children[1].children[x].cells[8].children["0"].value == "") {
      return "false";
    }

    if ($('#tbl_jo_details')["0"].children[1].children[x].cells[5].children["0"].value == "0" || $('#tbl_jo_details')["0"].children[1].children[x].cells[5].children["0"].value < 0) {
      return "validate_sched";
    }
  }

  return "true";
}

function getTables() {
  var datas = $("#jono").val();
  $.ajax({
    url: getjotables,
    type: 'GET',
    datatype: "json",
    loadonce: true,
    data: {
      _token: token,
      JOno: datas
    }
  }).done(function (data, textStatus, xhr) {
    console.log(data);
    var totalsched = 0;
    joDetails_arr = [];
    MainData = data;
    EditMode = true;

    if (data.length > 0) {
      for (var x = 0; x < data.length; x++) {
        joDetails_arr.push({
          count: x,
          dataid: data[x].id,
          id: data[x].id,
          sc_no: data[x].sc_no,
          prod_code: data[x].product_code,
          description: data[x].description,
          quantity: data[x].back_order_qty,
          sched_qty: data[x].sched_qty,
          totalsched_qty: 0,
          material_used: data[x].material_used,
          material_heat_no: data[x].material_heat_no,
          uom: data[x].uom,
          lot_no: data[x].lot_no,
          inv_id: data[x].inv_id,
          rmw_id: data[x].rmw_id,
          heat_no_id: data[x].heat_no_id,
          ship_date: data[x].ship_date
        });
        totalsched += data[x].sched_qty;
        $('#created_by').val(data[x].create_user);
        $('#created_date').val(data[x].created_at);
        $('#updated_by').val(data[x].update_user);
        $('#updated_date').val(data[x].updated_at);
      }

      $('#rmw_no').val(data[0].rmw_no);
      $('#total_sched_qty').val(totalsched);
      makeJODetailsList(joDetails_arr);
      getMaterialHeatNo(data[0].rmw_no, 'edit');
      var showform = document.getElementById('formbaba').style.display = 'inline';
      makeTravelSheet(travel_Sheet);
    } else {
      msg('No J.O. details found.', 'failed');
    }
  }).fail(function (xhr, textStatus, errorThrown) {
    ErrorMsg(xhr);
  });
}

function getTablesAll() {
  $.ajax({
    url: getjotablesALL,
    type: 'GET',
    datatype: "json",
    loadonce: true,
    data: {
      _token: token
    },
    success: function success(returnData) {
      for (var x = 0; x < returnData.length; x++) {
        joDetails_arr.push({
          count: x,
          id: x,
          sc_no: returnData[x].sc_no,
          prod_code: returnData[x].product_code,
          description: returnData[x].description,
          quantity: returnData[x].back_order_qty,
          sched_qty: returnData[x].sched_qty,
          material_used: returnData[x].material_used,
          material_heat_no: returnData[x].material_heat_no,
          uom: returnData[x].uom,
          lot_no: returnData[x].lot_no,
          inv_id: returnData[x].inv_id,
          rmw_id: returnData[x].rmw_id,
          heat_no_id: returnData[x].heat_no_id,
          ship_date: returnData[x].ship_date
        });
      }

      makeJODetailsList(joDetails_arr);
      $('.sched_qty').attr('disabled', true);
      $('.material_heat_no').attr('disabled', true);
      $('.material_used').attr('disabled', true);
      $('.lot_no').attr('disabled', true);
      $(".remove_jo_details").css("visibility", "hidden");
      getMaterialHeatNoEdit();
    },
    error: function error(xhr, textStatus, errorThrown) {
      msg(errorThrown, textStatus);
    }
  });
}

function getTravelSheet() {
  travel_Sheet = [];
  $.ajax({
    url: getTravelSheetURL,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token
    }
  }).done(function (data, textStatus, xhr) {
    travel_Sheet = data;
    makeTravelSheet(travel_Sheet);
  }).fail(function (xhr, textStatus, errorThrown) {
    ErrorMsg(xhr);
  });
}

function makeTravelSheet(arr) {
  $('#tbl_travel_sheet').dataTable().fnClearTable();
  $('#tbl_travel_sheet').dataTable().fnDestroy();
  $('#tbl_travel_sheet').dataTable({
    data: arr,
    order: [[11, 'desc']],
    columns: [{
      data: function data(_data2) {
        return '<span class="cancel_travel_sheet"' + ' data-jo_no="' + _data2.jo_no + '" data-prod_code="' + _data2.product_code + '" ' + ' data-issued_qty="' + _data2.issued_qty + '"data-id="' + _data2.id + '" ' + ' data-status="' + _data2.status + '"  data-sched_qty="' + _data2.sched_qty + '" ' + ' data-qty_per_sheet="' + _data2.qty_per_sheet + '"  data-iso_code="' + _data2.iso_code + '"' + ' data-sc_no="' + _data2.sc_no + '" data-idJO="' + _data2.idJO + '"' + ' title="Cancel Travel Sheet"><i class="text-red fa fa-times"></i> </span>';
      },
      name: 'action',
      orderable: false,
      searchable: false
    }, {
      data: 'jo_no',
      name: 'jo_no'
    }, {
      data: 'sc_no',
      name: 'sc_no'
    }, {
      data: 'product_code',
      name: 'prod_code'
    }, {
      data: 'description',
      name: 'description'
    }, {
      data: 'back_order_qty',
      name: 'order_qty'
    }, {
      data: 'sched_qty',
      name: 'sched_qty'
    }, {
      data: 'issued_qty',
      name: 'issued_qty'
    }, {
      data: 'material_used',
      name: 'material_used'
    }, {
      data: 'material_heat_no',
      name: 'material_heat_no'
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
            return 'Cancelled';
            break;

          case 5:
            return 'CLOSED';
            break;
        }
      },
      name: 'status'
    }, {
      data: 'updated_at',
      name: 'updated_at'
    }]
  });
}

function clear() {
  $('.clear').val("");
  $('#total_sched_qty').val(0);
}

function makeItemDataTable(arr) {
  $('#tbl_item').dataTable().fnClearTable();
  $('#tbl_item').dataTable().fnDestroy();
  $('#tbl_item').dataTable({
    data: arr,
    searching: false,
    lengthChange: false,
    paging: false,
    columns: [{
      data: 'sc_no',
      searchable: false,
      sortable: false
    }, {
      data: 'prod_code',
      searchable: false,
      sortable: false
    }, {
      data: 'description',
      searchable: false,
      sortable: false
    }, {
      data: 'order_qty',
      searchable: false,
      sortable: false
    }],
    initComplete: function initComplete() {
      $('.dataTables_info').hide();
    }
  });
}

function makeMaterialsDataTable(arr) {
  var count = 0;
  $('#tbl_materials').dataTable().fnClearTable();
  $('#tbl_materials').dataTable().fnDestroy();
  $('#tbl_materials').dataTable({
    data: arr,
    searching: false,
    lengthChange: false,
    scrollY: '300px',
    scrollX: true,
    paging: false,
    columns: [{
      data: function data(x) {
        var indx = count;

        if (x.count !== '') {
          indx = x.count;
        }

        return "<button type='button' class='btn btn-sm bg-red btn_remove_material' data-count='" + indx + "'>" + "<i class='fa fa-times'></i>" + "</button>" + "<input type='hidden' data-count='" + indx + "' name='upd_inv_id[]' id='upd_inv_id_" + indx + "' class='upd_inv_id' value='" + x.upd_inv_id + "'/>" + "<input type='hidden' data-count='" + indx + "' name='inv_id[]' id='inv_id_" + indx + "' class='inv_id' value='" + x.inv_id + "'/>" + "<input type='hidden' data-count='" + indx + "' name='rmwd_id[]' id='rmwd_id_" + indx + "' class='rmwd_id' value='" + x.rmwd_id + "'/>" + "<input type='hidden' data-count='" + indx + "' name='size[]' id='size_" + indx + "' class='size' value='" + x.size + "'/>" + "<input type='hidden' data-count='" + indx + "' name='computed_per_piece[]' id='computed_per_piece_" + indx + "' class='computed_per_piece'/>" + "<input type='hidden' data-count='" + indx + "' name='material_type[]' id='material_type_" + indx + "' class='material_type' value='" + x.material_type + "'/>";
      },
      searchable: false,
      sortable: false,
      width: "3.14%"
    }, {
      data: function data(x) {
        var indx = count;

        if (x.count !== '') {
          indx = x.count;
        }

        return "<input type='number' step='0.01' data-count='" + indx + "' name='sched_qty[]' id='sched_qty_" + indx + "' class='form-control form-control-sm mat_validate sched_qty' value='" + x.sched_qty + "'/>" + "<div id='sched_qty_" + indx + "_feedback' class='sched_qty__feedback'></div>";
      },
      searchable: false,
      sortable: false,
      width: "7.14%"
    }, {
      data: function data(x) {
        var indx = count;

        if (x.count !== '') {
          indx = x.count;
        }

        return "<input type='text' data-count='" + indx + "' name='material_heat_no[]' id='material_heat_no_" + indx + "' class='form-control form-control-sm material_heat_no' value='" + x.material_heat_no + "' readonly />";
      },
      searchable: false,
      sortable: false,
      width: "13.14%"
    }, // { data: function(x) {
    //     return "<select name='material_heat_no[]' id='material_heat_no_"+indx+"' class='form-control form-control-sm material_heat_no' value='"+x.material_heat_no+"'></select>";
    // }, searchable: false, sortable: false },
    {
      data: function data(x) {
        var indx = count;

        if (x.count !== '') {
          indx = x.count;
        }

        return "<div class='input-group input-group-sm'>" + "<input type='number' data-count='" + indx + "' step='0.01' id='rmw_issued_qty_" + indx + "' name='rmw_issued_qty[]' class='form-control form-control-sm rmw_issued_qty' value='" + x.rmw_issued_qty + "' readonly>" + "<div class='input-group-append'>" + "<span class='input-group-text'>PCS</span>" + "</div>";
      },
      searchable: false,
      sortable: false,
      width: "7.14%"
    }, {
      data: function data(x) {
        var indx = count;

        if (x.count !== '') {
          indx = x.count;
        }

        return "<input type='text' data-count='" + indx + "' name='material_used[]' id='material_used_" + indx + "' class='form-control form-control-sm material_used' value='" + x.description + "' readonly/>" + "<div id='material_used_" + indx + "_feedback' class='material_used__feedback'></div>";
      },
      searchable: false,
      sortable: false,
      width: "20.14%"
    }, {
      data: function data(x) {
        var indx = count;

        if (x.count !== '') {
          indx = x.count;
        }

        return "<input type='text' data-count='" + indx + "' name='lot_no[]' class='form-control form-control-sm mat_validate lot_no' value='" + x.lot_no + "'/>";
      },
      searchable: false,
      sortable: false,
      width: "6.14%"
    }, {
      data: function data(x) {
        var indx = count;

        if (x.count !== '') {
          indx = x.count;
        }

        return "<input type='number' data-count='" + indx + "' step='0.01' id='blade_consumption_" + indx + "' name='blade_consumption[]' class='form-control form-control-sm mat_validate blade_consumption' value='" + x.blade_consumption + "'/>";
      },
      searchable: false,
      sortable: false,
      width: "7.14%"
    }, {
      data: function data(x) {
        var indx = count;

        if (x.count !== '') {
          indx = x.count;
        }

        return "<input type='number' data-count='" + indx + "' step='0.01' id='cut_weight_" + indx + "' name='cut_weight[]' class='form-control form-control-sm mat_validate cut_weight' value='" + x.cut_weight + "'/>";
      },
      searchable: false,
      sortable: false,
      width: "5.14%"
    }, {
      data: function data(x) {
        var indx = count;

        if (x.count !== '') {
          indx = x.count;
        }

        return "<input type='number' data-count='" + indx + "' step='0.01' id='cut_length_" + indx + "' name='cut_length[]' class='form-control form-control-sm mat_validate cut_length'value='" + x.cut_length + "' />";
      },
      searchable: false,
      sortable: false,
      width: "5.14%"
    }, {
      data: function data(x) {
        var indx = count;

        if (x.count !== '') {
          indx = x.count;
        }

        return "<input type='number' data-count='" + indx + "' step='0.01' id='cut_width_" + indx + "' name='cut_width[]' class='form-control form-control-sm mat_validate cut_width' value='" + x.cut_width + "'/>";
      },
      searchable: false,
      sortable: false,
      width: "5.14%"
    }, {
      data: function data(x) {
        var indx = count;

        if (x.count !== '') {
          indx = x.count;
        }

        return "<input type='number' data-count='" + indx + "' step='0.01' id='mat_length_" + indx + "' name='mat_length[]' class='form-control form-control-sm mat_validate mat_length' value='" + x.mat_length + "' readonly/>";
      },
      searchable: false,
      sortable: false,
      width: "5.14%"
    }, {
      data: function data(x) {
        var indx = count;

        if (x.count !== '') {
          indx = x.count;
        }

        return "<input type='number' data-count='" + indx + "' step='0.01' id='mat_weight_" + indx + "' name='mat_weight[]' class='form-control form-control-sm mat_validate mat_weight' value='" + x.mat_weight + "' readonly/>";
      },
      searchable: false,
      sortable: false,
      width: "5.14%"
    }, {
      data: function data(x) {
        var indx = count;

        if (x.count !== '') {
          indx = x.count;
        }

        return "<input type='number' data-count='" + indx + "' step='0.01' id='assign_qty_" + indx + "' name='assign_qty[]' class='form-control form-control-sm mat_validate assign_qty' value='" + x.assign_qty + "'/>";
      },
      searchable: false,
      sortable: false,
      width: "5.14%"
    }, {
      data: function data(x) {
        var indx = count;

        if (x.count !== '') {
          indx = x.count;
        }

        return "<input type='number' data-count='" + indx + "' step='0.01' id='remaining_qty_" + indx + "' name='remaining_qty[]' class='form-control form-control-sm remaining_qty' value='" + x.remaining_qty + "' readonly/>";
      },
      searchable: false,
      sortable: false,
      width: "5.14%"
    }],
    createdRow: function createdRow(row, data, dataIndex) {
      var dataRow = $(row);
      var mat_used_input = $(dataRow[0].cells[4].firstChild);
      var cut_weight_input = $(dataRow[0].cells[7].firstChild);
      var cut_length_input = $(dataRow[0].cells[8].firstChild);
      var cut_width_input = $(dataRow[0].cells[9].firstChild);

      if (data.material_code == data.standard_material_used) {
        mat_used_input.removeClass('is-invalid');
        mat_used_input.next().removeClass('invalid-feedback').html('');
        cut_weight_input.prop('readonly', true);
        cut_length_input.prop('readonly', true);
        cut_width_input.prop('readonly', true);
      } else {
        var error = "This is not the Product's Standard Material";
        mat_used_input.addClass('is-invalid');
        mat_used_input.next().addClass('invalid-feedback').html(error);
        cut_weight_input.prop('readonly', false);
        cut_length_input.prop('readonly', false);
        cut_width_input.prop('readonly', false);
      }

      $(row).attr('id', 'tr_' + count);
      count++;
    },
    fnDrawCallback: function fnDrawCallback(oSettings) {
      $('.dataTables_scrollBody').slimScroll({
        height: '300px'
      });
    },
    initComplete: function initComplete() {
      $('#tbl_materials_wrapper > .dt-buttons > .btn').removeClass('btn-secondary');
      $('#tbl_materials_wrapper > .dt-buttons > .btn').addClass('bg-green');
      $('.dataTables_info').hide(); // $('#add_material_row').click();

      $('.sched_qty:first').focus();
    }
  });
}

function populateOldMaterialData() {
  for (var index = 0; index < arr_materials.length; index++) {
    var tr = $('#tr_' + index);
    arr_materials[index].sched_qty = tr.children()[1].firstChild.value;
    arr_materials[index].lot_no = tr.children()[5].firstChild.value;
    arr_materials[index].blade_consumption = tr.children()[6].firstChild.value;
    arr_materials[index].cut_weight = tr.children()[7].firstChild.value;
    arr_materials[index].cut_length = tr.children()[8].firstChild.value;
    arr_materials[index].cut_width = tr.children()[9].firstChild.value;
    arr_materials[index].mat_length = tr.children()[10].firstChild.value;
    arr_materials[index].mat_weight = tr.children()[11].firstChild.value;
    arr_materials[index].assign_qty = tr.children()[12].firstChild.value;
    arr_materials[index].remaining_qty = tr.children()[13].firstChild.value;
  }
}

function saveMaterials() {
  //$('.loadingOverlay-modal').show();
  var param = {
    _token: token,
    sched_qty: $('input[name="sched_qty[]"]').map(function () {
      return $(this).val();
    }).get(),
    material_heat_no: $('input[name="material_heat_no[]"]').map(function () {
      return $(this).val();
    }).get(),
    rmw_issued_qty: $('input[name="rmw_issued_qty[]"]').map(function () {
      return $(this).val();
    }).get(),
    material_used: $('input[name="material_used[]"]').map(function () {
      return $(this).val();
    }).get(),
    lot_no: $('input[name="lot_no[]"]').map(function () {
      return $(this).val();
    }).get(),
    blade_consumption: $('input[name="blade_consumption[]"]').map(function () {
      return $(this).val();
    }).get(),
    cut_weight: $('input[name="cut_weight[]"]').map(function () {
      return $(this).val();
    }).get(),
    cut_length: $('input[name="cut_length[]"]').map(function () {
      return $(this).val();
    }).get(),
    cut_width: $('input[name="cut_width[]"]').map(function () {
      return $(this).val();
    }).get(),
    mat_length: $('input[name="mat_length[]"]').map(function () {
      return $(this).val();
    }).get(),
    mat_weight: $('input[name="mat_weight[]"]').map(function () {
      return $(this).val();
    }).get(),
    assign_qty: $('input[name="assign_qty[]"]').map(function () {
      return $(this).val();
    }).get(),
    remaining_qty: $('input[name="remaining_qty[]"]').map(function () {
      return $(this).val();
    }).get(),
    computed_per_piece: $('input[name="computed_per_piece[]"]').map(function () {
      return $(this).val();
    }).get(),
    inv_id: $('input[name="inv_id[]"]').map(function () {
      return $(this).val();
    }).get(),
    rmwd_id: $('input[name="rmwd_id[]"]').map(function () {
      return $(this).val();
    }).get(),
    upd_inv_id: $('input[name="upd_inv_id[]"]').map(function () {
      return $(this).val();
    }).get(),
    size: $('input[name="size[]"]').map(function () {
      return $(this).val();
    }).get(),
    material_type: $('input[name="material_type[]"]').map(function () {
      return $(this).val();
    }).get(),
    rmw_no: $('#rmw_no').val(),
    ship_date: $('#ship_date').val(),
    sc_no: $('#sc_no').val(),
    prod_code: $('#prod_code').val(),
    description: $('#code_description').val(),
    quantity: $('#back_order_qty').val()
  };
  console.log(param); // $.ajax({
  //     url: savejodetailsURL,
  //     type: 'POST',
  //     dataType: 'JSON',
  //     data: param
  // }).done(function(data, textStatus, xhr) {
  // }).fail(function(xhr, textStatus, errorThrown) {
  //     ErrorMsg(xhr);
  // }).always(function(xhr, textStatus) {
  //     $('.loadingOverlay-modal').hide();
  // });
}

/***/ }),

/***/ 17:
/*!*****************************************************************************************************!*\
  !*** multi ./resources/assets/js/pages/ppc/transactions/production-schedule/production-schedule.js ***!
  \*****************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\laragon\www\en-pms\resources\assets\js\pages\ppc\transactions\production-schedule\production-schedule.js */"./resources/assets/js/pages/ppc/transactions/production-schedule/production-schedule.js");


/***/ })

/******/ });