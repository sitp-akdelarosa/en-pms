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
/******/ 	return __webpack_require__(__webpack_require__.s = 5);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/assets/js/pages/admin/user-master/user-master.js":
/*!********************************************************************!*\
  !*** ./resources/assets/js/pages/admin/user-master/user-master.js ***!
  \********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(function () {
  "use strict";

  userList(); //getDivisionCode('#div_code');

  get_user_type('#user_type');
  modules(1, '');
  check_permission(code_module);
  $('.custom-file-input').on('change', function () {
    var fileName = $(this).val().split('\\').pop();
    $(this).next('.custom-file-label').addClass("selected").html(fileName);
    readPhotoURL(this);
  });
  $('#tbl_user_body').on('click', '.btn_delete_user', function (e) {
    var id = $(this).attr('data-id');
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
        $('.loadingOverlay').show();
        $.ajax({
          url: userDeleteURL,
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

          userList(); // in here, the loading will close 

          return data.status;
        }).fail(function (xhr, textStatus, errorThrown) {
          msg(errorThrown, 'error');
        });
      } else {
        swal("Cancelled", "Your data is safe and not deleted.");
      }
    });
  });
  $('#tbl_user_body').on('click', '.btn_edit_user', function (e) {
    clear();
    show_user($(this).attr('data-id'));
    $('#modal_user_access').modal('show');
  }); //$("#div_code").on('keyup', getdivisionsuggest);

  $('#btn_add_user').on('click', function () {
    clear();
    modules();
    $('#modal_user_access').modal('show');
  });
  $('#btn_upd_usertype').on('click', function () {
    $.ajax({
      url: userUpdTypeURL,
      type: 'POST',
      dataType: 'JSON'
    }).done(function (data, textStatus, xhr) {
      msg(data.msg, data.status);
    }).fail(function (xhr, textStatus, errorThrown) {
      console.log("error");
    });
  });
  $('#user_type').on('change', function () {
    modules($(this).val());
  });
  $('#frm_user').on('submit', function (e) {
    $('.loadingOverlay-modal').show();
    e.preventDefault();
    var data = new FormData(this);
    $.ajax({
      url: $(this).attr('action'),
      type: 'POST',
      dataType: 'JSON',
      data: data,
      mimeType: "multipart/form-data",
      contentType: false,
      cache: false,
      processData: false
    }).done(function (data, textStatus, xhr) {
      $('.loadingOverlay-modal').hide();

      if (textStatus) {
        if (data.status == "failed") {
          msg(data.msg, data.status);
        } else {
          msg("User data was successfully saved.", textStatus);
        }

        userList();
      }
    }).fail(function (xhr, textStatus, errorThrown) {
      var errors = xhr.responseJSON.errors;
      showErrors(errors);

      if (errorThrown == "Internal Server Error") {
        msg(errorThrown, textStatus);
      }

      $('.loadingOverlay-modal').hide();
    });
  });
});

function show_user(id) {
  $.ajax({
    url: '/admin/user-master/' + id,
    type: 'GET',
    dataType: 'JSON'
  }).done(function (data) {
    $('#photo_profile').attr("src", '../../../../' + data.photo);
    $('#id').val(data.id);
    $('#user_id').val(data.user_id);
    $('#firstname').val(data.firstname);
    $('#lastname').val(data.lastname);
    $('#user_type').val(data.user_type);
    $('#password').val(data.actual_password);
    $('#email').val(data.email);
    var checked = false;

    if (data.is_admin) {
      checked = true;
    }

    $('#is_admin').prop('checked', checked);
    modules(data.user_type, data.id);
  }).fail(function () {
    msg(errorThrown, textStatus);
  });
}

function clear() {
  $('.clear').val('');
  $('#photo_profile').attr('src', defaultPhoto);
  $('#photo_label').html("Select a photo...");
}

function modules(user_type) {
  var id = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
  $('.loadingOverlay-modal').show();
  tbl = '';
  $('#tbl_modules_body').html(tbl);
  var d = {
    user_type: user_type,
    id: id
  };
  $.ajax({
    url: '/admin/user-mod',
    type: 'GET',
    dataType: 'JSON',
    data: d
  }).done(function (data, textStatus, xhr) {
    $('.loadingOverlay-modal').hide();

    if (data.length < 1) {
      tbl = '<tr>' + '<td colspan="4">No data displayed.</td>' + '</tr>';
      $('#tbl_modules_body').append(tbl);
    } else {
      $.each(data, function (i, x) {
        if (x.access == 1) {
          var checked_rw = 'checked';
        }

        if (x.access == 2) {
          var checked_ro = 'checked';
        }

        tbl = '<tr>' + '<td>' + x.code + '<input type="hidden" name="code[]" value="' + x.code + '">' + '</td>' + '<td>' + x.title + '<input type="hidden" name="title[]" value="' + x.title + '">' + '</td>' + '<td>' + '<input type="checkbox" class="table-checkbox access" name="rw[]" value="' + x.id + '" ' + checked_rw + '>' + '</td>' + '<td>' + '<input type="checkbox" class="table-checkbox access" name="ro[]" value="' + x.id + '" ' + checked_ro + '>' + '</td>' + '</tr>';
        $('#tbl_modules_body').append(tbl);
      });
    }
  }).fail(function (xhr, textStatus, errorThrown) {
    msg(errorThrown, textStatus);
  });
}

function userList() {
  $('.loadingOverlay').show();
  $.ajax({
    url: userListURL,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token
    }
  }).done(function (data, textStatus, xhr) {
    var table = $('#tbl_user');
    table.dataTable().fnClearTable();
    table.dataTable().fnDestroy();
    table.dataTable({
      data: data,
      processing: true,
      deferRender: true,
      responsive: true,
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
      pageLength: 10,
      columnDefs: [{
        orderable: false,
        targets: [7]
      }, {
        searchable: false,
        targets: [7]
      }],
      order: [[6, "desc"]],
      columns: [{
        data: 'user_id',
        name: 'user_id'
      }, {
        data: 'firstname',
        name: 'firstname'
      }, {
        data: 'lastname',
        name: 'lastname'
      }, {
        data: 'email',
        name: 'email'
      }, {
        data: 'user_type',
        name: 'user_type'
      }, {
        data: 'actual_password',
        name: 'actual_password'
      }, {
        data: 'created_at',
        name: 'created_at'
      }, {
        data: function data(x) {
          return '<button type="button" class="btn btn-sm bg-blue btn_edit_user" data-id="' + x.id + '">' + '<i class="fa fa-edit"></i>' + '</button>' + '<button type="button" class="btn btn-sm bg-red btn_delete_user permission-button" data-id="' + x.id + '">' + '<i class="fa fa-trash"></i>' + '</button>';
        },
        name: 'action',
        orderable: false,
        searchable: false
      }],
      "initComplete": function initComplete() {
        $('.loadingOverlay').hide();
      },
      "fnDrawCallback": function fnDrawCallback() {}
    });
  }).fail(function (xhr, textStatus, ErrMsg) {
    var msgErr = xhr.responseJSON.message;
    msg(msgErr, textStatus);
  }).always(function () {
    console.log("complete");
  });
} // function getdivisionsuggest(){
//     var options = '';
//     var datas = $("#div_code").val();
//     $.ajax({
//         url: divCodeURL,
//         type: 'POST',
//         datatype: "json",
//         loadonce: true,
//         data: {_token: token, data:datas},
//         rowNum: 1000,
//         success: function (returnData) {
//             options = "";
//             if (returnData.length > 20) {
//                 l = 10;
//             }
//             else {
//                 l = returnData.length;
//             }
//             for (var i = 0; i < l; i++) {
//                 options += '<option value="' + returnData[i].div_code + '" />';
//             }
//             $("#divcode").empty().append(options);
//             document.getElementById('divcode').innerHTML = options;
//         },
//         error: function (xhr, ajaxOptions, thrownError) {
//             alert(xhr.status);
//             alert(thrownError);
//         }
//     });
// }

/***/ }),

/***/ 5:
/*!**************************************************************************!*\
  !*** multi ./resources/assets/js/pages/admin/user-master/user-master.js ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\xampp\htdocs\en-pms\resources\assets\js\pages\admin\user-master\user-master.js */"./resources/assets/js/pages/admin/user-master/user-master.js");


/***/ })

/******/ });