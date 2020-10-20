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
/******/ 	return __webpack_require__(__webpack_require__.s = 18);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/assets/js/pages/ppc/transactions/raw-material-withdrawal/raw-material-withdrawal.js":
/*!*******************************************************************************************************!*\
  !*** ./resources/assets/js/pages/ppc/transactions/raw-material-withdrawal/raw-material-withdrawal.js ***!
  \*******************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

var rawMaterial = [];
$(function () {
  getRawMaterialList();
  checkAllCheckboxesInTable('.check_all', '.check_item');
  init();
  $(document).on('shown.bs.modal', function () {
    $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
  });
  $('#btn_search_heat_no').on('click', function () {
    var current_stock = 0;
    $.each(rawMaterial, function (i, x) {
      if (x.material_heat_no == $('#material_heat_no').val() && x.trans_id == undefined) {
        current_stock -= x.issued_qty;
      }
    });

    if (current_stock != 0) {
      getMaterialDetails($('#material_heat_no').val(), current_stock, '');
    } else {
      getMaterialDetails($('#material_heat_no').val(), 0, '');
    } //getscnosuggest(undefined, $(this).val());

  });
  $('#btn_new').on('click', function () {
    addState();
  }); // $('#issued_qty').on('change', function () {
  // 	var inv_qty = parseInt($('#inv_qty').val());
  // 	var issued_qty = parseInt($(this).val());
  // 	var material_heat_no = $('#material_heat_no').val();
  // 	var trans_no = $('#trans_no').val();
  // 	if ($('#inv_qty').val() != '') {
  // 		if (issued_qty > inv_qty) {
  // 			msg('Issued Quantity is greater than stocks', 'warning');
  // 		}
  // 	}
  // 	if ($(this).val() == '') {
  // 		$(this).val(0)
  // 	}
  // });

  $('#needed_qty').on('change', function () {
    if ($(this).val() == '') {
      $(this).val(0);
    }
  });
  $('#returned_qty').on('change', function () {
    if ($(this).val() == '') {
      $(this).val(0);
    }
  });
  $('#btn_clear').on('click', function () {
    clear();
    $('#btn_add').removeClass('bg-blue');
    $('#btn_add').addClass('bg-green');
    $('#btn_add').html('<i class="fa fa-plus"></i> Add');
  });
  $('#btn_cancel').on('click', function () {
    current_stock = 0;
    getRawMaterialList();
    $('#btn_add').removeClass('bg-blue');
    $('#btn_add').addClass('bg-green');
    $('#btn_add').html('<i class="fa fa-plus"></i> Add');
  });
  $('#btn_edit').on('click', function () {
    if ($('#trans_no').val() == '' && rawMaterial.length == 0) {
      msg('Please search a transaction number.');
    } else {
      editState();
    }
  });
  $('#trans_no').on('change', function () {
    getRawMaterialList($(this).val());
  });
  $('#btn_add').unbind('click').click(function () {
    addDetails();
  });
  $('#frm_raw_material').on('submit', function (e) {
    e.preventDefault();
    $('.loadingOverlay').show();

    if (rawMaterial.length > 0) {
      $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        dataType: 'JSON',
        data: $(this).serialize()
      }).done(function (data, textStatus, xhr) {
        if (textStatus) {
          msg(data.msg, data.status);
          current_stock = 0;
          getRawMaterialList($('#trans_no').val());
          $('#btn_add').removeClass('bg-blue');
          $('#btn_add').addClass('bg-green');
          $('#btn_add').html('<i class="fa fa-plus"></i> Add');
        }
      }).fail(function (xhr, textStatus, errorThrown) {
        console.log(xhr.responseJSON);
        var errors = xhr.responseJSON.errors;
        showErrors(errors);
        viewState();
      }).always(function (xhr, textStatus) {
        $('.loadingOverlay').hide();
      });
    } else {
      msg("Please add materials to withdraw.", "failed");
      $('.loadingOverlay').hide();
    }
  });
  $('#btn_first').on('click', function () {
    navigate('first');
  });
  $('#btn_prev').on('click', function () {
    navigate('prev');
  });
  $('#btn_next').on('click', function () {
    navigate('next');
  });
  $('#btn_last').on('click', function () {
    navigate('last');
  });
  $('#tbl_raw_material_body').on('click', '.btn_edit_item', function () {
    var current_stock = 0;
    var count = parseInt($(this).attr('data-row')) + 1;
    var mat_heat_no = $(this).attr('data-material_heat_no');
    var issued_qty = parseInt($(this).attr('data-save-issued_qty'));
    $.each(rawMaterial, function (i, x) {
      if (x.material_heat_no == mat_heat_no && x.count != count && x.trans_id == undefined) {
        if (x.count != count) {
          current_stock -= parseInt(x.issued_qty);
        } else {
          current_stock += parseInt(x.issued_qty);
        }

        issued_qty -= parseInt(x.issued_qty);
      }
    });

    if (current_stock != 0 && $(this).attr('data-save-issued_qty') == '') {
      getMaterialDetails(mat_heat_no, current_stock, $(this).attr('data-inv_id'));
    } else if ($(this).attr('data-id') != 'NONE') {
      getMaterialDetails(mat_heat_no, issued_qty, $(this).attr('data-inv_id'));
      $('#save_issued_qty').val(issued_qty);
    } else {
      getMaterialDetails(mat_heat_no, 0, $(this).attr('data-inv_id'));
    }

    $('#detail_id').val($(this).attr('data-id'));
    $('#inv_id').val($(this).attr('data-inv_id'));
    $('#old_issued_qty').val($(this).attr('data-old_issued_qtys'));
    $('#hide_schedule').val($(this).attr('data-schedule'));
    $('#schedule').val($(this).attr('data-schedule'));
    $('#item_id').val($(this).attr('data-row'));
    $('#lot_no').val($(this).attr('data-lot_no'));
    $('#material_heat_no').val($(this).attr('data-material_heat_no'));
    $('#trans_no').val($(this).attr('data-trans_no'));
    var remarks = $(this).attr('data-remarks') == null || $(this).attr('data-remarks') == '' || $(this).attr('data-remarks') == 'null' ? '' : $(this).attr('data-remarks');
    var sc_no = $(this).attr('data-sc_no') == 'null' ? '' : $(this).attr('data-sc_no'); //$('#sc_no').val(sc_no);
    //getscnosuggest(sc_no, $(this).attr('data-material_heat_no'));

    $('#remarks').val(remarks);
    $('#issued_qty').val($(this).attr('data-issued_qty'));
    $('#saved_issued_qty').val($(this).attr('data-issued_qty')); // $('#needed_qty').val($(this).attr('data-needed_qty'));
    // $('#returned_qty').val($(this).attr('data-returned_qty'));

    $('#issued_uom').val($(this).attr('data-issued_uom')); // $('#needed_uom').val($(this).attr('data-needed_uom'));
    // $('#returned_uom').val($(this).attr('data-returned_uom'));

    $('#create_user').val($(this).attr('data-create_user'));
    $('#created_at').val($(this).attr('data-created_at'));
    $('#update_user').val($(this).attr('data-update_user'));
    $('#updated_at').val($(this).attr('data-updated_at'));
    $('#btn_add').removeClass('bg-green');
    $('#btn_add').addClass('bg-blue');
    $('#btn_add').html('<i class="fa fa-check"></i> Update');
  });
  $('#tbl_raw_material_body').on('click', '.btn_remove_item', function () {
    var id = $(this).attr('data-row'); //rawMaterial.splice(id, 1);

    rawMaterial[id].deleted = 1;
    RawMaterialList(rawMaterial);
    clear(); // msg("Item was successfully remove.", "success");

    $(".btn_remove_item").css("visibility", "visible");
    $('.btn_edit_item').prop('disabled', false);
    $('#btn_add').removeClass('bg-blue');
    $('#btn_add').addClass('bg-green');
    $('#btn_add').html('<i class="fa fa-plus"></i> Add');
  });
  $('#btn_delete').on('click', function () {
    var id = $('#id').val();
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
          url: deleteRawMaterial,
          type: 'POST',
          dataType: 'JSON',
          data: {
            _token: token,
            id: id
          }
        }).done(function (data, textStatus, xhr) {
          if (data.status == 'success') {
            msg(data.msg, data.status);
          } else {
            msg(data.msg, data.status);
          }

          getRawMaterialList($('#trans_no').val());
        }).fail(function (xhr, textStatus, errorThrown) {
          msg(errorThrown, 'error');
        });
      } else {
        swal("Cancelled", "Your data is safe and not deleted.");
      }
    });
  });
  $('#btn_prepare_print').on('click', function () {
    $('#modal_withdrawal_slip').modal('show');
  });
  $('#btn_print').on('click', function () {
    var print_link = RawMaterialWithdrawalSlipURL + '?trans_id=' + $('#id').val() + '&&trans_no=' + $('#trans_no').val() + '&&date=' + $('#date').val() + '&&prepared_by=' + $('#prepared_by').val() + '&&print_format=' + $('#print_format').val() + '&&plant=' + $('#plant').val() + '&&received_by=' + $('#received_by').val();

    if ($('#trans_no').val() == '' || $('#id').val() == '') {
      msg('Please Navigate to a Transaction Number first.', 'failed');
    } else if ($('#date').val() == '') {
      msg('Please Input date.', 'failed');
    } else {
      window.open(print_link, '_tab');
    }
  });
  $('#tbl_inventory').on('click', '.btn_pick_item', function () {
    if (parseInt($(this).attr('data-quantity')) == 0 && $('#item_id').val() == '') {
      $('#issued_qty').prop('readonly', true);
      msg("Material Heat Number have no qty inventory.", "failed");
    } else {
      $('#item').val($(this).attr('data-item'));
      $('#alloy').val($(this).attr('data-alloy'));
      $('#size').val($(this).attr('data-size'));
      $('#length').val($(this).attr('data-length'));
      $('#mat_code').val($(this).attr('data-materials_code'));
      $('#inv_qty').val($(this).attr('data-current_stock'));
      $('#qty_weight').val($(this).attr('data-qty_weight'));
      $('#issued_qty').prop('readonly', false);

      if ($('#hide_schedule').val() == "") {
        $('#schedule').val($(this).attr('data-schedule'));
      } else {
        $('#hide_schedule').val("");
      }

      $('#inv_id').val($(this).attr('data-inv_id'));
      $('#modal_inventory').modal('hide');
    }
  });
  $('#btn_search_filter').on('click', function () {
    $('.srch-clear').val('');
    searchDataTable([]);
    $('#modal_raw_material_search').modal('show');
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
      var errors = xhr.responseJSON.errors;
      console.log(errors);
      showErrors(errors);
    }).always(function () {
      $('.loadingOverlay-modal').hide();
    });
  });
});

function init() {
  check_permission(code_permission, function (output) {
    if (output == 1) {}
  });
}

function viewState() {
  var data = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
  clear();
  $('#btn_edit_item').hide();
  $('#controls').hide();
  $('#add_new').show();
  $('#search').show();
  $('#edit').show();
  $('#save').hide();
  $('.btn_edit_item').prop('disabled', true);
  $(".btn_remove_item").css("visibility", "hidden");
  $('#delete').show();
  $('#cancel').hide();
  $('#print').show();
  $('.input').prop('disabled', true);
  $('#trans_no').prop('readonly', false);
  RawMaterialList(data);
}

function addState() {
  clear();
  $('#needed_qty').val(0);
  $('#issued_qty').val(0);
  $('#returned_qty').val(0);
  $('#needed_uom').val('');
  $('#issued_uom').val('');
  $('#returned_uom').val('');
  $('#trans_no').val('');
  $('#controls').show();
  $('#add_new').hide();
  $('#search').hide();
  $('#edit').hide();
  $('#save').show();
  $('#delete').hide();
  $('#cancel').show();
  $('#print').hide();
  $('#btn_edit_item').show();
  $('.input').prop('disabled', false);
  $('#trans_no').prop('readonly', true);
  $('#issued_qty').prop('readonly', false);
  $('#needed_uom').prop('readonly', false);
  $('#issued_uom').prop('readonly', false);
  $('#returned_uom').prop('readonly', false);
  rawMaterial = [];
  RawMaterialList([]);
  $(".btn_remove_item").css("visibility", "visible");
}

function editState() {
  $('#needed_qty').val(0);
  $('#issued_qty').val(0);
  $('#returned_qty').val(0);
  $('#needed_uom').val('');
  $('#issued_uom').val('');
  $('#returned_uom').val('');
  $('#controls').show();
  $('#add_new').hide();
  $('#search').hide();
  $('#edit').hide();
  $('#save').show();
  $(".btn_remove_item").css("visibility", "visible");
  $('#delete').hide();
  $('#cancel').show();
  $('#print').hide();
  $('.btn_edit_item').prop('disabled', false);
  $('.input').prop('disabled', false);
  $('#trans_no').prop('readonly', true);
  $('#issued_qty').prop('readonly', false);
  $('#needed_uom').prop('readonly', false);
  $('#issued_uom').prop('readonly', false);
  $('#returned_uom').prop('readonly', false);
}

function navigate(to) {
  getRawMaterialList($('#trans_no').val(), to);
}

function getRawMaterialList(trans_no) {
  var to = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
  $('.loadingOverlay').show();
  $.ajax({
    url: searchTransNoURL,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token,
      trans_no: trans_no,
      to: to
    }
  }).done(function (data, textStatus, xhr) {
    if (data.status == 'failed') {
      msg('Transaction Number is not in the list.', 'failed');
    } else {
      rawMaterial = [];
      var count = rawMaterial.length + 1;
      $.each(data.details, function (i, x) {
        rawMaterial.push({
          count: count,
          id: x.id,
          trans_id: x.trans_id,
          trans_no: data.trans_no,
          mat_code: x.mat_code,
          alloy: x.alloy,
          item: x.item,
          size: x.size,
          schedule: x.schedule,
          // lot_no: x.lot_no,
          material_heat_no: x.material_heat_no,
          // sc_no: x.sc_no,
          remarks: x.remarks,
          issued_qty: x.issued_qty,
          saveIssued_qty: x.issued_qty,
          old_issued_qtys: x.issued_qty,
          // needed_qty: x.needed_qty,
          // returned_qty: x.returned_qty,
          issued_uom: x.issued_uom == null || x.issued_uom == 'N/A' ? '' : x.issued_uom,
          // needed_uom: (x.needed_uom == null || x.needed_uom == 'N/A')? '' : x.needed_uom,
          // returned_uom: (x.returned_uom == null || x.returned_uom == 'N/A')? '' : x.returned_uom,
          create_user: x.create_user,
          update_user: x.update_user,
          created_at: x.created_at,
          updated_at: x.updated_at,
          deleted: x.deleted,
          inv_id: x.inv_id,
          detail_id: x.detail_id
        });
        count++;
        $('#create_user').val(x.create_user);
        $('#created_at').val(x.created_at);
        $('#update_user').val(x.update_user);
        $('#updated_at').val(x.updated_at);
      });
      $('#id').val(data.trans_id);
      $('#trans_no').val(data.trans_no);
      viewState(rawMaterial);
      $('#item_id').val('');
      $(".btn_remove_item").css("visibility", "hidden");
    }
  }).fail(function (xhr, textStatus, errorThrown) {
    viewState();
    msg("There was an error while searching for Transaction No.");
  }).always(function () {
    $('.loadingOverlay').hide();
  });
}

function RawMaterialList(data) {
  var row = -1;
  $('#tbl_raw_material').dataTable().fnClearTable();
  $('#tbl_raw_material').dataTable().fnDestroy();
  $('#tbl_raw_material').dataTable({
    data: data,
    bLengthChange: false,
    paging: false,
    order: [[2, 'asc']],
    columns: [{
      data: function data(x) {
        row++;
        return "<button type='button' class='btn btn-sm bg-blue btn_edit_item' " + "data-id='" + x.id + "'" + "data-row='" + row + "'" + "data-trans_id='" + x.trans_id + "'" + "data-trans_no='" + x.trans_no + "'" + "data-mat_code='" + x.mat_code + "'" + "data-alloy='" + x.alloy + "'" + "data-item='" + x.item + "'" + "data-size='" + x.size + "'" + "data-schedule='" + x.schedule + "'" + // "data-lot_no='" + x.lot_no + "'" +
        "data-material_heat_no='" + x.material_heat_no + "'" + // "data-sc_no='" + x.sc_no + "'" +
        "data-remarks='" + x.remarks + "'" + "data-issued_qty='" + x.issued_qty + "'" + // "data-issued_uom='" + x.issued_uom + "'" +
        "data-save-issued_qty='" + x.saveIssued_qty + "'" + "data-old_issued_qtys='" + x.old_issued_qtys + "'" + "data-inv_id='" + x.inv_id + "'" + // "data-needed_qty='" + x.needed_qty + "'" +
        // "data-returned_qty='" + x.returned_qty + "'" +
        // "data-needed_uom='" + x.needed_uom + "'" +
        "data-deleted='" + x.deleted + "'" + "data-create_user='" + x.create_user + "'" + "data-update_user='" + x.update_user + "'" + "data-created_at='" + x.created_at + "'" + "data-updated_at='" + x.updated_at + "' disabled>" + "<i class='fa fa-edit'></i>" + "</button>" + "<button type='button' class='btn btn-sm bg-red btn_remove_item' data-row='" + row + "' data-issued_qty='" + x.issued_qty + "'>" + "<i class=' fa fa-times'></i>" + "</button >" + "<input type='hidden' name='ids[]' value='" + x.id + "'>" + "<input type='hidden' name='old_issued_qtys[]' value='" + x.old_issued_qtys + "'>" + "<input type='hidden' name='detail_ids[]' value='" + x.detail_id + "'>" + "<input type='hidden' name='inv_ids[]' value='" + x.inv_id + "'>" + "<input type='hidden' name='deleted[]' value='" + x.deleted + "'>";
      },
      searchable: false,
      orderable: false
    }, {
      data: function data(x) {
        return x.mat_code + "<input type='hidden' name='mat_code[]' value='" + x.mat_code + "'>";
      }
    }, {
      data: function data(x) {
        return x.alloy + "<input type='hidden' name='alloy[]' value='" + x.alloy + "'>";
      }
    }, {
      data: function data(x) {
        return x.item + "<input type='hidden' name='item[]' value='" + x.item + "'>";
      }
    }, {
      data: function data(x) {
        var slash = ' ';

        if (x.schedule !== '') {
          slash = ' / ';
        }

        return x.size + slash + x.schedule + "<input type='hidden' name='size[]' value='" + x.size + "'>" + "<input type='hidden' name='schedule[]' value='" + x.schedule + "'>";
      }
    }, {
      data: function data(x) {
        var issued_qty = x.issued_qty == '' || x.issued_qty == null ? 0 : x.issued_qty;
        var issued_uom = x.issued_uom == '' || x.issued_uom == null ? '' : x.issued_uom;
        return issued_qty + issued_uom + "<input type='hidden' name='issued_qty[]' value='" + issued_qty + "'>" + "<input type='hidden' name='issued_uom[]' value='" + issued_uom + "'>";
      }
    }, // {
    // 	data: function (x) {
    // 		var needed_qty = (x.needed_qty == '' || x.needed_qty == null) ? 0 : x.needed_qty;
    // 		var needed_uom = (x.needed_uom == '' || x.needed_uom == null) ? '' : x.needed_uom;
    // 		return needed_qty+ needed_uom + "<input type='hidden' name='needed_qty[]' value='" + needed_qty + "'>"+
    // 										"<input type='hidden' name='needed_uom[]' value='" + needed_uom + "'>";
    // 	}
    // },
    // {
    // 	data: function (x) {
    // 		var returned_qty = (x.returned_qty == '' || x.returned_qty == null) ? 0 : x.returned_qty;
    // 		var returned_uom = (x.returned_uom == '' || x.returned_uom == null) ? '' : x.returned_uom;
    // 		return returned_qty+returned_uom + "<input type='hidden' name='returned_qty[]' value='" + returned_qty + "'>"+
    // 										"<input type='hidden' name='returned_uom[]' value='" + returned_uom + "'>";
    // 	}
    // },
    // {
    // 	data: function (x) {
    // 		return x.lot_no + "<input type='hidden' name='lot_no[]' value='" + x.lot_no + "'>";
    // 	}
    // },
    {
      data: function data(x) {
        return x.material_heat_no + "<input type='hidden' name='material_heat_no[]' value='" + x.material_heat_no + "'>";
      }
    }, // {
    // 	data: function (x) {
    // 		if (x.sc_no == '' || x.sc_no == null) {
    // 			return "<input type='hidden' name='sc_no[]' value='" + x.sc_no + "'>";
    // 		} else if (x.trans_id == undefined) {
    // 			return x.sc_no[0] + ",...<input type='hidden' name='sc_no[]' value='" + x.sc_no + "'>";
    // 		} else {
    // 			return ellipsis(x.sc_no, 8) + "<input type='hidden' name='sc_no[]' value='" + x.sc_no + "'>";
    // 		}
    // 	}
    // },
    {
      data: function data(x) {
        if (x.remarks == '' || x.remarks == null) {
          return "<input type='hidden' name='remarks[]' value='" + x.remarks + "'>";
        } else {
          return ellipsis(x.remarks, 15) + "<input type='hidden' name='remarks[]' value='" + x.remarks + "'>";
        }
      }
    }],
    createdRow: function createdRow(row, data, dataIndex) {
      if (data.deleted === 1) {
        $(row).css('background-color', '#ff6266');
        $(row).css('color', '#fff');
      }
    }
  });
}

function clear() {
  $('#needed_qty').val(0);
  $('#issued_qty').val(0);
  $('#returned_qty').val(0);
  $('.clear').val('');
  $("#sc_no").val([]);
  $('#sc_no').select2(); //getscnosuggest();
}

function getscnosuggest(scno, heat_no) {
  var options = '<option value=""></option>';
  var scnos = [];
  $('#sc_no').html(options);
  $.ajax({
    url: scnosuggestURL,
    type: 'GET',
    datatype: "json",
    loadonce: true,
    data: {
      _token: token,
      heat_no: heat_no
    }
  }).done(function (data, textStatus, xhr) {
    if (scno != undefined) {
      var sc_no = scno.replace(/ /g, '');
      scnos = sc_no.split(',');

      if (scnos != '') {
        $.each(scnos, function (i, x) {});
      }
    }

    $.each(data, function (i, x) {
      if (scnos.indexOf(x.sc_no) == -1) {
        options = "<option value=' " + x.sc_no + "'>" + x.sc_no + " </option>";
        $('#sc_no').append(options);
      } else {
        options = "<option value=' " + x.sc_no + "' selected>" + x.sc_no + " </option>";
        $('#sc_no').append(options);
      }
    });
    $('#sc_no').select2();
  }).fail(function (xhr, textStatus, errorThrown) {
    console.log("error");
  });
}

function getMaterialDetails(material_heat_no, issued_qty, inv_id) {
  $('.loadingOverlay').show();
  $.ajax({
    url: getMaterialDetailsURL,
    type: 'GET',
    dataType: 'JSON',
    data: {
      material_heat_no: material_heat_no,
      issued_qty: issued_qty,
      inv_id: inv_id
    }
  }).done(function (data, textStatus, xhr) {
    if (data.length > 0) {
      if (data.length > 1) {
        inventoryTable(data);
        $('#modal_inventory').modal('show');
      } else {
        var material = data[0];
        $('#item').val("");
        $('#alloy').val("");
        $('#schedule').val("");
        $('#size').val("");
        $('#length').val("");
        $('#mat_code').val("");
        $('#inv_qty').val("");
        $('#qty_weight').val("");
        $('#inv_id').val("");

        if (material.item_code == undefined) {
          $('#issued_qty').prop('readonly', true);
          msg("Material Heat Number is not in the list.", "failed");
        } else if (material.quantity == 0 && $('#item_id').val() == '') {
          $('#issued_qty').prop('readonly', true);
          msg("Material Heat Number have no qty inventory.", "failed");
        } else {
          $('#item').val(material.item);
          $('#alloy').val(material.alloy);
          $('#size').val(material.size);
          $('#length').val(material.length);
          $('#mat_code').val(material.item_code);
          $('#inv_qty').val(material.current_stock);
          $('#qty_weight').val(material.qty_weight);
          $('#issued_qty').prop('readonly', false);

          if ($('#hide_schedule').val() == "") {
            $('#schedule').val(material.schedule);
          } else {
            $('#hide_schedule').val("");
          }

          $('#inv_id').val(material.inv_id);
        }
      }
    } else {
      msg("No Materials found for this Heat Number.", 'warning');
    }
  }).fail(function (xhr, textStatus, errorThrown) {
    //msg(errorThrown, textStatus);
    var response = jQuery.parseJSON(xhr.responseText);
    ErrorMsg(response);
  }).always(function () {
    $('.loadingOverlay').hide();
  });
}

function inventoryTable(arr) {
  $('.loadingOverlay-modal').show();
  $('#tbl_inventory').dataTable().fnClearTable();
  $('#tbl_inventory').dataTable().fnDestroy();
  $('#tbl_inventory').dataTable({
    data: arr,
    order: [[13, 'asc']],
    scrollX: true,
    columns: [{
      data: function data(x) {
        var trimqty = x.quantity;
        var quantity = trimqty.toString().replace(/\s/g, '');
        return "<button class='btn btn-sm bg-blue btn_pick_item' type='button'" + "data-receiving_no='" + x.receiving_no + "' " + "data-materials_type='" + x.materials_type + "' " + "data-materials_code='" + x.materials_code + "' " + "data-item='" + x.item + "' " + "data-alloy='" + x.alloy + "' " + "data-size='" + x.size + "' " + "data-length='" + x.length + "' " + "data-qty_weight='" + x.qty_weight + "' " + "data-qty_pcs='" + x.qty_pcs + "' " + "data-current_stock='" + x.current_stock + "' " + "data-heat_no='" + x.heat_no + "' " + "data-quantity='" + quantity + "'" + "data-inv_id='" + x.inv_id + "' " + ">" + "<i class='fa fa-edit'></i>" + "</button>";
      },
      orderable: false,
      searchable: false
    }, {
      data: 'receiving_no'
    }, {
      data: 'materials_type'
    }, {
      data: 'materials_code'
    }, {
      data: 'item'
    }, {
      data: 'alloy'
    }, {
      data: 'size'
    }, {
      data: 'length'
    }, {
      data: 'qty_weight'
    }, {
      data: 'qty_pcs'
    }, {
      data: 'current_stock'
    }, {
      data: 'heat_no'
    }, {
      data: 'invoice_no'
    }, {
      data: 'received_date'
    }, {
      data: 'supplier'
    }],
    initComplete: function initComplete() {
      $('.loadingOverlay-modal').hide();
    }
  });
}

function addDetails() {
  var limit = $('#item_id').val() != '' ? 21 : 20;
  var current_stock = parseInt($('#inv_qty').val());
  var issued_qty = parseInt($('#issued_qty').val());
  var save_issued_qty = parseInt($('#save_issued_qty').val());
  var ExistHeat_NO = 1;

  if (rawMaterial.length == limit) {
    msg('Material already reach the limit of 20 item', 'failed');
  } else if (issued_qty > current_stock + save_issued_qty) {
    msg('Issued Quantity is greater than stocks.', 'failed');
  } else if ($('#issued_uom').val() == "") {
    msg('Please provide an Unit of Measurement.', 'failed');
  } else if (issued_qty == 0 || issued_qty == "") {
    msg('Please provide an Issued Quantity.', 'failed');
  } else {
    if ($('#material_heat_no').val() == '' || $('#mat_code').val() == '') {
      msg('Please comply some other fields.', 'failed');
    } else if ($('#issued_qty').val() < 0 || $('#needed_qty').val() < 0 || $('#returned_qty').val() < 0) {
      msg('Please Input valid Number.', 'failed');
    } else {
      if ($('#item_id').val() == '') {
        $.each(rawMaterial, function (i, x) {
          if (x.lot_no == $('#lot_no').val()) {
            ExistHeat_NO = 0;
          }
        }); // if (ExistHeat_NO == 0){
        // 	msg('Lot No Already Exist in this table','failed');
        // 	return false;
        // }else{		

        var count = rawMaterial.length + 1;
        rawMaterial.push({
          count: count,
          id: 'NONE',
          detail_id: 0,
          trans_no: $('#trans_no').val(),
          mat_code: $('#mat_code').val(),
          alloy: $('#alloy').val(),
          item: $('#item').val(),
          size: $('#size').val(),
          schedule: $('#schedule').val(),
          // lot_no: $('#lot_no').val(),
          material_heat_no: $('#material_heat_no').val(),
          // sc_no: $('#sc_no').val(),
          remarks: $('#remarks').val(),
          issued_qty: $('#issued_qty').val(),
          issued_uom: $('#issued_uom').val(),
          saveIssued_qty: $('#save_issued_qty').val(),
          old_issued_qtys: 0,
          inv_id: $('#inv_id').val(),
          // needed_qty: $('#needed_qty').val(),
          // returned_uom: $('#returned_uom').val(),
          // needed_uom: $('#needed_uom').val(),
          // returned_qty: $('#returned_qty').val(),
          create_user: $('#create_user').val(),
          update_user: $('#update_user').val(),
          created_at: $('#created_at').val(),
          updated_at: $('#updated_at').val(),
          deleted: 0
        }); // }
      } else {
        var count = parseInt($('#item_id').val()) + 1; // $.each(rawMaterial, function(i, x) {
        // 	if(x.material_heat_no == $('#material_heat_no').val() && x.count != count ){ 
        // 		ExistHeat_NO = 0; 
        // 	}
        // });
        // if (ExistHeat_NO == 0){
        // 	msg('Material Heat No Already Exist in this table','failed');
        // 	return false;
        // }else{

        var id = $('#item_id').val();
        var count = parseInt($('#item_id').val()) + 1;
        rawMaterial.splice(id, 1, {
          count: count,
          id: id,
          detail_id: $('#detail_id').val(),
          trans_no: $('#trans_no').val(),
          mat_code: $('#mat_code').val(),
          alloy: $('#alloy').val(),
          item: $('#item').val(),
          size: $('#size').val(),
          schedule: $('#schedule').val(),
          // lot_no: $('#lot_no').val(),
          material_heat_no: $('#material_heat_no').val(),
          // sc_no: $('#sc_no').val(),
          remarks: $('#remarks').val(),
          issued_qty: $('#issued_qty').val(),
          issued_uom: $('#issued_uom').val(),
          saveIssued_qty: $('#save_issued_qty').val(),
          old_issued_qtys: $('#old_issued_qty').val(),
          inv_id: $('#inv_id').val(),
          // needed_qty: $('#needed_qty').val(),
          // returned_qty: $('#returned_qty').val(),
          // needed_uom: $('#needed_uom').val(),
          // returned_uom: $('#returned_uom').val(),
          create_user: $('#create_user').val(),
          update_user: $('#update_user').val(),
          created_at: $('#created_at').val(),
          updated_at: $('#updated_at').val(),
          deleted: 0
        }); // }
      }

      RawMaterialList(rawMaterial);
      clear();
      $('#needed_qty').val(0);
      $('#issued_qty').val(0);
      $('#returned_qty').val(0);
      $('#needed_uom').val('');
      $('#issued_uom').val('');
      $('#returned_uom').val('');
      $('#btn_add').removeClass('bg-blue');
      $('#btn_add').addClass('bg-green');
      $('#btn_add').html('<i class="fa fa-plus"></i> Add');
      $('.btn_edit_item').prop('disabled', false);
    }
  }
}

function searchDataTable(arr) {
  $('.loadingOverlay-modal').show();
  $('#tbl_search').dataTable().fnClearTable();
  $('#tbl_search').dataTable().fnDestroy();
  $('#tbl_search').dataTable({
    data: arr,
    order: [[10, 'asc']],
    columns: [{
      data: 'trans_no'
    }, {
      data: 'mat_code'
    }, {
      data: 'heat_no'
    }, {
      data: 'alloy'
    }, {
      data: 'item'
    }, {
      data: 'size'
    }, {
      data: 'schedule'
    }, {
      data: 'length'
    }, {
      data: 'issued_qty'
    }, {
      data: 'create_user'
    }, {
      data: 'created_at'
    }],
    initComplete: function initComplete() {
      $('.loadingOverlay-modal').hide();
    }
  });
}

/***/ }),

/***/ 18:
/*!*************************************************************************************************************!*\
  !*** multi ./resources/assets/js/pages/ppc/transactions/raw-material-withdrawal/raw-material-withdrawal.js ***!
  \*************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\laragon\www\en-pms\resources\assets\js\pages\ppc\transactions\raw-material-withdrawal\raw-material-withdrawal.js */"./resources/assets/js/pages/ppc/transactions/raw-material-withdrawal/raw-material-withdrawal.js");


/***/ })

/******/ });