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

$(function () {
  checkAllCheckboxesInTable('.check_all', '.check_item');
  check_permission(code_permission);
  getUserType();
  $('#frm_user_type').on('submit', function (e) {
    e.preventDefault();
    $.ajax({
      url: $(this).attr('action'),
      type: 'POST',
      dataType: 'JSON',
      data: $(this).serialize()
    }).done(function (data, textStatus, xhr) {
      if (textStatus) {
        msg("User Type was successfully added.", textStatus);
        getUserType();
      }

      clear();
      $('#btn_save').removeClass('bg-green');
      $('#btn_save').addClass('bg-blue');
      $('#btn_save').html('<i class="fa fa-floppy-o"></i> Save');
    }).fail(function (xhr, textStatus, errorThrown) {
      var errors = xhr.responseJSON.errors;
      showErrors(errors);
    });
  });
  $('#tbl_type_body').on('click', '.btn_edit', function (e) {
    e.preventDefault();
    $('#id').val($(this).attr('data-id'));
    $('#description').val($(this).attr('data-description'));
    $('#category').val($(this).attr('data-category'));
    $('#btn_save').removeClass('bg-blue');
    $('#btn_save').addClass('bg-green');
    $('#btn_save').html('<i class="fa fa-check"></i> Update');
  });
  $('#btn_delete').on('click', function (e) {
    delete_items('.check_item', typeDeleteURL);
  });
});

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
        return '<button type="type" class="btn btn-sm bg-blue btn_edit" data-id="' + x.id + '" ' + 'data-description="' + x.description + '" ' + 'data-category="' + x.categor + '"> ' + '<i class="fa fa-edit"></i>' + '</button>';
      },
      orderable: false,
      searchable: false
    }, {
      data: 'description',
      name: 'description'
    }, {
      data: 'category',
      name: 'category'
    }],
    // createdRow: function (row, data, dataIndex) {
    //     if (data.del_flag === 1) {
    //         $(row).css('background-color', '#ff6266');
    //         $(row).css('color', '#fff');
    //     }
    // },
    "initComplete": function initComplete() {
      $('.loadingOverlay').hide();
    },
    "fnDrawCallback": function fnDrawCallback() {}
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
  } // $(checkboxClass+":checked").each(function() {
  // 	chkArray.push($(this).val());
  // });


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

/***/ }),

/***/ 7:
/*!**********************************************************************!*\
  !*** multi ./resources/assets/js/pages/admin/user-type/user-type.js ***!
  \**********************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\xampp\htdocs\en-pms\resources\assets\js\pages\admin\user-type\user-type.js */"./resources/assets/js/pages/admin/user-type/user-type.js");


/***/ })

/******/ });