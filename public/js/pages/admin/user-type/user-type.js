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
/******/ 	return __webpack_require__(__webpack_require__.s = 7);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/assets/js/pages/admin/user-type/user-type.js":
/*!****************************************************************!*\
  !*** ./resources/assets/js/pages/admin/user-type/user-type.js ***!
  \****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

var modules_id = [];
$(function () {
  checkAllCheckboxesInTable('#tbl_type', '.check_all', '.check_item', '#btn_delete');
  checkAllCheckboxesInTable('#tbl_modules', '.check_all_modules', '.check_module');
  getUserType();
  init();
  $('#btn_save').on('click', function (e) {
    e.preventDefault();
    $('.loadingOverlay').show();
    $.ajax({
      url: saveUrl,
      type: 'POST',
      dataType: 'JSON',
      data: {
        _token: token,
        description: $('#description').val(),
        category: $('#category').val(),
        modules: modules_id,
        id: $('#id').val()
      }
    }).done(function (data, textStatus, xhr) {
      if (textStatus == 'success') {
        msg("User Type was successfully added.", textStatus);
      }

      getUserType();
      GuiState('view');
    }).fail(function (xhr, textStatus, errorThrown) {
      var errors = xhr.responseJSON.errors;
      showErrors(errors);
    }).always(function () {
      $('.loadingOverlay').hide();
    });
  });
  $('#tbl_type_body').on('click', '.btn_edit', function (e) {
    e.preventDefault();
    $('#id').val($(this).attr('data-id'));
    $('#description').val($(this).attr('data-description'));
    $('#category').val($(this).attr('data-category')).trigger("chosen:updated");
    GuiState('update');
  });
  $('#tbl_type_body').on('change', '.check_item', function (e) {
    if ($(this).is(':checked')) {
      $('#btn_delete').prop('disabled', false);
    } else {
      $('#btn_delete').prop('disabled', true);
    }
  });
  $('#btn_modules').on('click', function () {
    getModules($('#id').val());
    $('#modal_user_type_modules').modal('show');
    $('.check_all_modules').prop('checked', false);
  });
  $('#btn_delete').on('click', function (e) {
    delete_items('.check_item', typeDeleteURL);
  });
  $('#btn_save_module').on('click', function () {
    var chkArray = [];
    var table = $('#tbl_modules').DataTable();

    for (var x = 0; x < table.context[0].aoData.length; x++) {
      var DataRow = table.context[0].aoData[x];

      if (DataRow.anCells !== null && DataRow.anCells[0].firstChild.checked == true) {
        chkArray.push(table.context[0].aoData[x].anCells[0].firstChild.value);
      }
    }

    if (chkArray.length > 0) {
      modules_id = chkArray;
    }

    $('#modal_user_type_modules').modal('hide');
  });
  $('#btn_cancel').on('click', function () {
    GuiState('view');
  });
});

function init() {
  check_permission(code_permission, function (output) {
    if (output == 1) {
      $('#btn_delete').prop('disabled', true);
    }

    GuiState('view');
  });
}

function GuiState(state) {
  switch (state) {
    case 'view':
      clear();
      $('#btn_save').removeClass('bg-green');
      $('#btn_save').addClass('bg-blue');
      $('#btn_save').html('<i class="fa fa-floppy-o"></i> Save');
      $('#btn_cancel').hide();
      break;

    case 'update':
      $('#btn_save').removeClass('bg-blue');
      $('#btn_save').addClass('bg-green');
      $('#btn_save').html('<i class="fa fa-check"></i> Update');
      $('#btn_cancel').show();
      break;
  }
}

function clear() {
  $('.clear').val('');
}

function getUserType() {
  $('.loadingOverlay').show();
  $.ajax({
    url: typeListURL,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token
    }
  }).done(function (data, textStatus, xhr) {
    UserTypeDataTable(data);
  }).fail(function (xhr, textStatus, errorThrown) {
    var errors = xhr.responseJSON.errors;
    showErrors(errors);
  });
}

function UserTypeDataTable(dataArr) {
  var table = $('#tbl_type');
  table.dataTable().fnClearTable();
  table.dataTable().fnDestroy();
  table.dataTable({
    data: dataArr,
    processing: true,
    deferRender: true,
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
    searching: false,
    pageLength: 10,
    columns: [{
      data: function data(x) {
        return "<input type='checkbox' class='table-checkbox check_item' value='" + x.id + "'>";
      },
      name: 'id',
      orderable: false,
      searchable: false
    }, {
      data: function data(x) {
        return '<button type="button" class="btn btn-sm bg-blue btn_edit" data-id="' + x.id + '" ' + 'data-description="' + x.description + '" ' + 'data-category="' + x.category + '"> ' + '<i class="fa fa-edit"></i>' + '</button>';
      },
      orderable: false,
      searchable: false
    }, {
      data: 'description',
      name: 'description'
    }, {
      data: 'category',
      name: 'category'
    }, {
      data: 'created_at',
      name: 'created_at'
    }],
    order: [[4, "desc"]],
    initComplete: function initComplete() {
      $('.loadingOverlay').hide();
    },
    fnDrawCallback: function fnDrawCallback() {}
  });
}

function delete_items(checkboxClass, deleteURL) {
  var chkArray = [];
  var table = $('#tbl_type').DataTable();

  for (var x = 0; x < table.context[0].aoData.length; x++) {
    var DataRow = table.context[0].aoData[x];

    if (DataRow.anCells !== null && DataRow.anCells[0].firstChild.checked == true) {
      chkArray.push(table.context[0].aoData[x].anCells[0].firstChild.value);
    }
  }

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
          url: deleteURL,
          type: 'POST',
          dataType: 'JSON',
          data: {
            _token: token,
            id: chkArray
          }
        }).done(function (data, textStatus, xhr) {
          msg(data.msg, data.status);
          getUserType();
        }).fail(function (xhr, textStatus, errorThrown) {
          msg(errorThrown, 'error');
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
  $('#btn_save').removeClass('bg-green');
  $('#btn_save').addClass('bg-blue');
  $('#btn_save').html('<i class="fa fa-floppy-o"></i> Save');
}

function getModules(id) {
  $('.loadingOverlay-modal').show();
  $.ajax({
    url: moduleListURL,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token,
      id: id,
      category: $('#category').val()
    }
  }).done(function (data, textStatus, xhr) {
    modulesDataTable(data);
  }).fail(function (xhr, textStatus, errorThrown) {
    msg(errorThrown, textStatus);
  });
}

function modulesDataTable(dataArr) {
  var table = $('#tbl_modules');
  table.dataTable().fnClearTable();
  table.dataTable().fnDestroy();
  table.dataTable({
    data: dataArr,
    processing: true,
    deferRender: true,
    language: {
      aria: {
        sortAscending: ": activate to sort column ascending",
        sortDescending: ": activate to sort column descending"
      },
      emptyTable: "No pages were selected.",
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
    order: [[1, "asc"]],
    searching: false,
    lengthChange: false,
    pageLength: 50,
    columns: [{
      data: function data(x) {
        var checked = 'checked';

        if (x.module_id == null || x.module_id == '') {
          checked = '';
        }

        return "<input type='checkbox' class='table-checkbox check_module' value='" + x.id + "' data-mod_id='" + x.module_id + "' " + checked + ">";
      },
      orderable: false,
      searchable: false,
      width: '5%'
    }, {
      data: 'code',
      width: '25%'
    }, {
      data: 'title',
      width: '70%'
    }],
    initComplete: function initComplete() {
      $('.loadingOverlay-modal').hide();
    },
    fnDrawCallback: function fnDrawCallback() {}
  });
}

/***/ }),

/***/ 7:
/*!**********************************************************************!*\
  !*** multi ./resources/assets/js/pages/admin/user-type/user-type.js ***!
  \**********************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\laragon\www\en-pms\resources\assets\js\pages\admin\user-type\user-type.js */"./resources/assets/js/pages/admin/user-type/user-type.js");


/***/ })

/******/ });