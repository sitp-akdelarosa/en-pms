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
/******/ 	return __webpack_require__(__webpack_require__.s = 6);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/assets/js/pages/admin/assign-production-line/assign-production-line.js":
/*!******************************************************************************************!*\
  !*** ./resources/assets/js/pages/admin/assign-production-line/assign-production-line.js ***!
  \******************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(function () {
  $('.loadingOverlay').show();
  get_select_prodline();
  get_users();
  get_assigned_prodline([0]);
  init();
  $('.select-validate').on('change', function (e) {
    var no_error = $(this).attr('id');
    hideErrors(no_error);
  });
  $('#btn_save').on('click', function () {
    var user_id = [];
    var product_line = [];
    var utable = $('#tbl_users').DataTable();

    for (var x = 0; x < utable.context[0].aoData.length; x++) {
      var cells = utable.context[0].aoData[x].anCells;

      if (cells !== null && cells[0].firstChild.checked == true) {
        user_id.push(cells[0].firstChild.value);
      }
    }

    var ptable = $('#tbl_productline').DataTable();

    for (var x = 0; x < ptable.context[0].aoData.length; x++) {
      var cells = ptable.context[0].aoData[x].anCells;

      if (cells !== null && cells[0].firstChild.checked == true) {
        product_line.push(cells[0].firstChild.value);
      }
    }

    if (user_id.length == 0 && product_line.length == 0) {
      msg('Please select User and Product Lines to assign.', 'warning');
    }

    if (user_id.length > 0 && product_line.length == 0) {
      msg('Please select Product Lines to assign to user.', 'warning');
    }

    if (user_id.length == 0 && product_line.length > 0) {
      msg('Please select User to whom to assign the Product Line.', 'warning');
    }

    if (user_id.length > 0 && product_line.length > 0) {
      $('.loadingOverlay').show();
      $.ajax({
        url: SaveURL,
        type: 'POST',
        dataType: 'JSON',
        data: {
          _token: token,
          user_id: user_id,
          product_line: product_line
        }
      }).done(function (data, textStatus, xhr) {
        msg(data.msg, data.status);
        get_assigned_prodline(data.user_id);
        clear();
        $('#btn_save').html('<i class="fa fa-floppy-o"></i> Save');
      }).fail(function (xhr, textStatus, errorThrown) {
        var errors = xhr.responseJSON.errors;
        showErrors(errors);
      });
    }
  });
  $('#tbl_assign_productline_body').on('click', '.btn_edit_prodline', function (e) {
    e.preventDefault();
    $('#id').val($(this).attr('data-id'));
    $('#user_id').val($(this).attr('data-user_id'));
    $('#product_line').val($(this).attr('data-product_line'));
    $('#btn_save').html('<i class="fa fa-check"></i> Update');
  });
  $('#btn_clear').on('click', function (e) {
    clear();
    get_assigned_prodline([0]);
    $('#btn_save').html('<i class="fa fa-floppy-o"></i> Save');
  });
  $('#btn_delete').on('click', function (e) {
    delete_items('.check_item', prodLineDeleteURL);
  });
  $('#tbl_users').on('click', '.btn_view_prod', function () {
    get_assigned_prodline([$(this).attr('data-user_id')]);
  });
  $('#tbl_users').on('change', '.check_all_users', function () {
    $('input:checkbox.check_user').not(this).prop('checked', this.checked);
    var table = $('#tbl_users').DataTable();

    for (var x = 0; x < table.context[0].aoData.length; x++) {
      var aoData = table.context[0].aoData[x];
      var tr = aoData.nTr;

      if (aoData.anCells !== null && aoData.anCells[0].firstChild.checked == true) {
        console.log(tr);
        $(tr).addClass('selected');
      } else {
        $(tr).removeClass('selected');
      }
    }
  });
  $('#tbl_users_body').on('change', '.check_user', function () {
    var tr = $(this).parent().parent()[0];

    if ($(this).is(':checked')) {
      $(tr).addClass('selected');
    } else {
      $(tr).removeClass('selected');
    }
  });
  $('#tbl_productline').on('change', '.check_all_prods', function () {
    $('input:checkbox.check_prod').not(this).prop('checked', this.checked);
    var table = $('#tbl_productline').DataTable();

    for (var x = 0; x < table.context[0].aoData.length; x++) {
      var aoData = table.context[0].aoData[x];
      var tr = aoData.nTr;

      if (aoData.anCells !== null && aoData.anCells[0].firstChild.checked == true) {
        console.log(tr);
        $(tr).addClass('selected');
      } else {
        $(tr).removeClass('selected');
      }
    }
  });
  $('#tbl_productline_body').on('change', '.check_prod', function () {
    var tr = $(this).parent().parent()[0];

    if ($(this).is(':checked')) {
      $(tr).addClass('selected');
    } else {
      $(tr).removeClass('selected');
    }
  });
  $('#tbl_assign_productline').on('change', '.check_all', function () {
    $('input:checkbox.check_item').not(this).prop('checked', this.checked);
    var table = $('#tbl_assign_productline').DataTable();

    for (var x = 0; x < table.context[0].aoData.length; x++) {
      var aoData = table.context[0].aoData[x];
      var tr = aoData.nTr;

      if (aoData.anCells !== null && aoData.anCells[0].firstChild.checked == true) {
        console.log(tr);
        $(tr).addClass('selected');
      } else {
        $(tr).removeClass('selected');
      }
    }
  });
  $('#tbl_assign_productline_body').on('change', '.check_item', function () {
    var tr = $(this).parent().parent()[0];

    if ($(this).is(':checked')) {
      $(tr).addClass('selected');
      $('#btn_delete').prop('disabled', false);
    } else {
      $(tr).removeClass('selected');
      $('#btn_delete').prop('disabled', true);
    }
  });
});

function init() {
  check_permission(code_permission, function (output) {
    if (output == 1) {
      $("#btn_delete").prop('disabled', true);
    }
  });
}

function get_assigned_prodline(user_id) {
  $.ajax({
    url: prodLineListURL,
    type: 'GET',
    dataType: 'JSON',
    data: {
      user_id: user_id
    }
  }).done(function (data, textStatus, xhr) {
    assignedProdlineTale(data);
  }).fail(function (xhr, textStatus, errorThrown) {
    console.log("error");
  });
}

function assignedProdlineTale(arr) {
  $('#tbl_assign_productline').dataTable().fnClearTable();
  $('#tbl_assign_productline').dataTable().fnDestroy();
  $('#tbl_assign_productline').dataTable({
    data: arr,
    processing: true,
    deferRender: true,
    bLengthChange: false,
    paging: true,
    pageLength: 10,
    // searching: false,
    order: [[2, 'desc']],
    columns: [{
      data: function data(_data) {
        return '<input type="checkbox" class="table-checkbox check_item" value="' + _data.id + '" data-id="' + _data.id + '">';
      },
      orderable: false,
      searchable: false
    }, {
      data: 'product_line'
    }, {
      data: 'fullname'
    }, {
      data: 'updated_at'
    }],
    initComplete: function initComplete() {
      $('.loadingOverlay').hide();
      $('.check_all_items').prop('checked', false);
    },
    fnDrawCallback: function fnDrawCallback() {
      checkAllCheckboxesInTable("#tbl_assign_productline", ".check_all", ".check_item", "#btn_delete");
    }
  });
}

function get_select_prodline() {
  var opt = "<option value=''></option>";
  $("#product_line").html(opt);
  $.ajax({
    url: dropdownProduct,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token
    }
  }).done(function (data, textStatus, xhr) {
    selectProdlineTable(data); // $.each(data, function(i, x) {
    //     opt = "<option value='"+x.dropdown_item+"'>"+x.dropdown_item+"</option>";
    //     $("#product_line").append(opt);
    // });
  }).fail(function (xhr, textStatus, errorThrown) {
    msg(errorThrown, textStatus);
  });
}

function selectProdlineTable(arr) {
  $('#tbl_productline').dataTable().fnClearTable();
  $('#tbl_productline').dataTable().fnDestroy();
  $('#tbl_productline').dataTable({
    data: arr,
    processing: true,
    deferRender: true,
    bLengthChange: false,
    pageLength: 10,
    // searching: false,
    order: [[1, 'asc']],
    columns: [{
      data: function data(_data2) {
        return '<input type="checkbox" class="table-checkbox check_prod" value="' + _data2.dropdown_item + '">';
      },
      orderable: false,
      searchable: false
    }, {
      data: 'dropdown_item'
    }],
    initComplete: function initComplete() {
      $('.loadingOverlay').hide();
      $('.check_all_prods').prop('checked', false);
    },
    fnDrawCallback: function fnDrawCallback() {
      checkAllCheckboxesInTable('#tbl_productline', '.check_all_prods', '.check_prod');
    }
  });
}

function get_users() {
  var opt = '<option value=""></option>';
  $('#user_id').html(opt);
  $.ajax({
    url: getUserURL,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token
    }
  }).done(function (data, textStatus, xhr) {
    usersTable(data);
  }).fail(function (xhr, textStatus, errorThrown) {
    msg(errorThrown, textStatus);
  });
}

function usersTable(arr) {
  $('#tbl_users').dataTable().fnClearTable();
  $('#tbl_users').dataTable().fnDestroy();
  $('#tbl_users').dataTable({
    data: arr,
    processing: true,
    deferRender: true,
    bLengthChange: false,
    pageLength: 10,
    // searching: false,
    order: [[1, 'asc']],
    columns: [{
      data: function data(_data3) {
        return '<input type="checkbox" class="table-checkbox check_user" value="' + _data3.id + '" data-id="' + _data3.id + '">';
      },
      orderable: false,
      searchable: false
    }, {
      data: 'user_id'
    }, {
      data: 'fullname'
    }, {
      data: function data(_data4) {
        return '<button class="btn btn-blue btn-flat btn-sm btn_view_prod" data-user_id="' + _data4.id + '">' + '<i class="fa fa-laptop"></i>' + '</button>';
      },
      orderable: false,
      searchable: false
    }],
    initComplete: function initComplete() {
      $('.loadingOverlay').hide();
      $('.check_all_users').prop('checked', false);
    },
    fnDrawCallback: function fnDrawCallback() {
      checkAllCheckboxesInTable('#tbl_users', '.check_all_users', '.check_user');
    }
  });
}

function delete_items(checkboxClass, deleteURL) {
  var chkArray = [];
  var table = $('#tbl_assign_productline').DataTable();

  for (var x = 0; x < table.context[0].aoData.length; x++) {
    var aoData = table.context[0].aoData[x];

    if (aoData.anCells !== null && aoData.anCells[0].firstChild.checked == true) {
      chkArray.push(table.context[0].aoData[x].anCells[0].firstChild.attributes['data-id'].value);
    }
  }

  if (chkArray.length > 0) {
    swal({
      title: "Are you sure to delete this data?",
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
        $('.loadingOverlay').show();
        $.ajax({
          url: deleteURL,
          type: 'POST',
          dataType: 'JSON',
          data: {
            _token: token,
            id: chkArray
          }
        }).done(function (data, textStatus, xhr) {
          get_assigned_prodline([0]);

          if (data.status == 'success') {
            msg(data.msg, data.status);
            is_confirmed_deleted = true;
          } else {
            msg(data.msg, data.status);
          }

          return data.status;
        }).fail(function (xhr, textStatus, errorThrown) {
          msg(errorThrown, textStatus);
        });
      } else {
        swal("Cancelled", "Your data is safe and not deleted.");
      }
    });
  } else {
    msg("Please select at least 1 item.", "warning");
  }

  $('.check_all').prop('checked', false);
  clear();
  $('#btn_save').html('<i class="fa fa-plus"></i> Add');
}

function clear() {
  $('.clear').val('');
  get_select_prodline();
  get_users();
}

/***/ }),

/***/ 6:
/*!************************************************************************************************!*\
  !*** multi ./resources/assets/js/pages/admin/assign-production-line/assign-production-line.js ***!
  \************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\xampp\htdocs\en-pms\resources\assets\js\pages\admin\assign-production-line\assign-production-line.js */"./resources/assets/js/pages/admin/assign-production-line/assign-production-line.js");


/***/ })

/******/ });