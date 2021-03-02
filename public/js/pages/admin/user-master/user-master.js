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
  userList(); //modules(1,'');

  init();
  checkAllCheckboxesInTable('#tbl_user', '.check_all_users', '.check_user');
  getUserType();
  getDivCode();
  $('.custom-file-input').on('change', function () {
    var fileName = $(this).val().split('\\').pop();
    $(this).next('.custom-file-label').addClass("selected").html(fileName);
    readPhotoURL(this);
  });
  $('#btn_delete').on('click', function () {
    var chkArray = [];
    var table = $('#tbl_user').DataTable();

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
          $('.loadingOverlay').show();
          $.ajax({
            url: userDeleteURL,
            type: 'POST',
            dataType: 'JSON',
            data: {
              _token: token,
              ids: chkArray
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
    } else {
      msg('Please select at least 1 user.', 'failed');
    }
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
          clear();
          userList();
          msg("User data was successfully saved.", textStatus);
        } //userList();

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
  $('#tbl_user').on('page.dt', function () {
    $('.check_all_users').prop('checked', false);
  });
});

function init() {
  if (permission_access == '2' || permission_access == 2) {
    $('.permission').prop('readonly', true);
    $('.permission-button').prop('disabled', true);
  } else {
    $('.permission').prop('readonly', false);
    $('.permission-button').prop('disabled', false);
  }
}

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
    $('#nickname').val(data.nickname);
    $('#user_type').val(data.user_type).trigger('change');
    $('#div_code').val(data.div_code).trigger('change');
    $('#password').val(data.actual_password);
    $('#password_confirmation').val(data.actual_password);
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
  hideErrors('user_id');
  hideErrors('firstname');
  hideErrors('lastname');
  hideErrors('user_type');
  hideErrors('email');
  hideErrors('password');
  $('#user_type').val(null).trigger('change');
  $('#div_code').val(null).trigger('change');
  $('#photo_profile').attr('src', defaultPhoto);
  $('#photo_label').html("Select a photo...");
}

function modules(user_type) {
  var id = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
  $('.loadingOverlay-modal').show();
  tbl = ''; //$('#tbl_modules_body').html(tbl);

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
    console.log(data);
    var table = $('#tbl_modules');
    table.dataTable().fnClearTable();
    table.dataTable().fnDestroy();
    table.dataTable({
      data: data,
      processing: true,
      deferRender: true,
      responsive: true,
      pageLength: 10,
      scrollX: '400px',
      paging: false,
      searching: false,
      bLengthChange: false,
      columns: [{
        data: function data(x) {
          return x.code + '<input type="hidden" name="code[]" value="' + x.code + '">';
        },
        searchable: false,
        orderable: false,
        width: '10%',
        visible: true
      }, {
        data: function data(x) {
          return x.title + '<input type="hidden" name="title[]" value="' + x.title + '">';
        },
        searchable: false,
        orderable: false,
        width: '40%',
        visible: true
      }, {
        data: function data(x) {
          var checked_rw = "";

          if (x.access == 1) {
            checked_rw = 'checked';
          }

          return '<input type="checkbox" class="table-checkbox access" name="rw[]" value="' + x.id + '" ' + checked_rw + '>';
        },
        searchable: false,
        orderable: false,
        width: '25%',
        visible: true
      }, {
        data: function data(x) {
          var checked_ro = "";

          if (x.access == 2) {
            checked_ro = 'checked';
          }

          return '<input type="checkbox" class="table-checkbox access" name="ro[]" value="' + x.id + '" ' + checked_ro + '>';
        },
        searchable: false,
        orderable: false,
        width: '25%',
        visible: true
      }],
      "initComplete": function initComplete() {
        $('.loadingOverlay-modal').hide();
        $('.dataTables_scrollBody').slimscroll();
      },
      "fnDrawCallback": function fnDrawCallback() {}
    }); // if (data.length < 1) {
    // 	tbl = 	'<tr>'+
    // 				'<td colspan="4">No data displayed.</td>'+
    // 			'</tr>';
    // 	$('#tbl_modules_body').append(tbl);
    // } else {
    // $.each(data, function(i, x) {
    // 	if (x.access == 1) {
    // 		var checked_rw = 'checked';
    // 	}
    // 	if (x.access == 2) {
    // 		var checked_ro = 'checked';
    // 	}
    // 	tbl = 	'<tr>'+
    // 				'<td>'+x.code+
    // 					'<input type="hidden" name="code[]" value="'+x.code+'">'+
    // 				'</td>'+
    // 				'<td>'+x.title+
    // 					'<input type="hidden" name="title[]" value="'+x.title+'">'+
    // 				'</td>'+
    // 				'<td>'+
    // 					'<input type="checkbox" class="table-checkbox access" name="rw[]" value="'+x.id+'" '+checked_rw+'>'+
    // 				'</td>'+
    // 				'<td>'+
    // 					'<input type="checkbox" class="table-checkbox access" name="ro[]" value="'+x.id+'" '+checked_ro+'>'+
    // 				'</td>'+
    // 			'</tr>';
    // 	$('#tbl_modules_body').append(tbl);
    // });
    // }
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
      order: [[10, "desc"]],
      columns: [{
        data: function data(x) {
          return '<input type="checkbox" class="table-checkbox check_user" value="' + x.id + '">';
        },
        name: 'id',
        searchable: false,
        orderable: false,
        width: '3.09%'
      }, {
        data: function data(x) {
          return '<button type="button" class="btn btn-sm bg-blue btn_edit_user" data-id="' + x.id + '">' + '<i class="fa fa-edit"></i>' + '</button>'; // '<button type="button" class="btn btn-sm bg-red btn_delete_user permission-button" data-id="'+x.id+'">'+
          // 	'<i class="fa fa-trash"></i>'+
          // '</button>';
        },
        name: 'action',
        orderable: false,
        searchable: false,
        width: '3.09%'
      }, {
        data: 'user_id',
        name: 'user_id',
        width: '9.09%'
      }, {
        data: 'firstname',
        name: 'firstname',
        width: '12.09%'
      }, {
        data: 'nickname',
        name: 'nickname',
        width: '12.09%'
      }, {
        data: 'lastname',
        name: 'lastname',
        width: '12.09%'
      }, {
        data: 'email',
        name: 'email',
        width: '9.09%'
      }, {
        data: 'div_code',
        name: 'div_code',
        width: '12.09%'
      }, {
        data: 'user_type',
        name: 'user_type',
        width: '9.09%'
      }, {
        data: 'actual_password',
        name: 'actual_password',
        width: '9.09%'
      }, {
        data: 'created_at',
        name: 'created_at',
        width: '9.09%'
      }],
      createdRow: function createdRow(row, data, dataIndex) {
        if (data.del_flag === 1) {
          $(row).css('background-color', '#ff6266');
          $(row).css('color', '#fff');
        }
      },
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
}

function getUserType() {
  $.ajax({
    url: UserTypeURL,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token
    }
  }).done(function (data, textStatus, xhr) {
    $('#user_type').select2({
      //dropdownParent: "#modal_user_access",
      allowClear: true,
      placeholder: 'Select User Type',
      data: data
    }).val(null).trigger('change');
  }).fail(function (xhr, textStatus, errorThrown) {
    msg(errorThrown, textStatus);
  });
}

function getDivCode() {
  $.ajax({
    url: DivCodeURL,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token
    }
  }).done(function (data, textStatus, xhr) {
    $('#div_code').select2({
      //dropdownParent: "#modal_user_access",
      allowClear: true,
      placeholder: 'Select Division Code',
      data: data
    }).val(null).trigger('change');
  }).fail(function (xhr, textStatus, errorThrown) {
    msg(errorThrown, textStatus);
  });
}

/***/ }),

/***/ 5:
/*!**************************************************************************!*\
  !*** multi ./resources/assets/js/pages/admin/user-master/user-master.js ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\laragon\www\en-pms\resources\assets\js\pages\admin\user-master\user-master.js */"./resources/assets/js/pages/admin/user-master/user-master.js");


/***/ })

/******/ });