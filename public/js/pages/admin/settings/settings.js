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
/******/ 	return __webpack_require__(__webpack_require__.s = 9);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/assets/js/pages/admin/settings/settings.js":
/*!**************************************************************!*\
  !*** ./resources/assets/js/pages/admin/settings/settings.js ***!
  \**************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(function () {
  check_permission(code_permission);
  getISO();
  checkAllCheckboxesInTable('.check_all', '.check_item');
  $('.custom-file-input').on('change', function () {
    var fileName = $(this).val().split('\\').pop();
    $(this).next('.custom-file-label').addClass("selected").html(fileName);
    readPhotoURL(this);
  });
  $('#btn_clear').on('click', function () {
    clear();
  });
  $("#frm_iso").on('submit', function (e) {
    $('.loadingOverlay').show();
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
      readPhotoURL("");
      msg("Successful", "success");
      clear();
      getISO();
      $('#btn_save').removeClass('bg-green');
      $('#btn_save').addClass('bg-blue');
      $('#btn_save').html('<i class="fa fa-floppy-o"></i> Save');
    }).fail(function (xhr, textStatus, errorThrown) {
      var errors = xhr.responseJSON.errors;
      showErrors(errors);
    }).always(function () {
      $('.loadingOverlay').hide();
    });
  }); //Edit table

  $('#tbl_iso').on('click', '.btn_edit', function (e) {
    $('#id').val($(this).attr('data-id'));
    $('#iso_name').val($(this).attr('data-iso_name'));
    $('#iso_code').val($(this).attr('data-iso_code'));
    $('#whs_photo').attr("src", '../../../' + $(this).attr('data-photo'));
    $('#btn_save').removeClass('bg-blue');
    $('#btn_save').addClass('bg-green');
    $('#btn_save').html('<i class="fa fa-check"></i> Update');
  }); //Delete Multiple data

  $('#btn_delete').on('click', function () {
    delete_set('.check_item', deleteISO);
  });
});

function getISO() {
  $.ajax({
    url: getISOTable,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token
    }
  }).done(function (data, textStatus, xhr) {
    makeISOdatatable(data);
  }).fail(function (xhr, textStatus, errorThrown) {
    msg(errorThrown, textStatus);
  });
}

function makeISOdatatable(arr) {
  $('#tbl_iso').dataTable().fnClearTable();
  $('#tbl_iso').dataTable().fnDestroy();
  $('#tbl_iso').dataTable({
    data: arr,
    columns: [{
      data: function data(x) {
        return '<input type="checkbox" class="table-checkbox check_item" value="' + x.id + '">';
      }
    }, {
      data: function data(x) {
        return '<button class="btn btn-sm bg-blue btn_edit permission-button" data-id="' + x.id + '" ' + 'data-iso_code="' + x.iso_code + '" data-iso_name="' + x.iso_name + '" ' + 'data-photo="' + x.photo + '">' + '<i class="fa fa-edit"></i>' + '</button>';
      }
    }, {
      data: 'iso_name'
    }, {
      data: 'iso_code'
    }]
  });
} //Multiple Delete 


function delete_set(checkboxClass, deleteOM) {
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
          url: deleteISO,
          type: 'POST',
          dataType: 'JSON',
          data: {
            _token: token,
            id: chkArray
          }
        }).done(function (data, textStatus, xhr) {
          msg(data.msg, data.status);
          getISO();
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
  $('#btn_save').html('<i class="fa fa-plus"></i> Add');
  getISO();
}

function clear() {
  $('.clear').val('');
  $('.custom-file-input').next('.custom-file-label').addClass("selected").html("Select a photo...");
  $('.photo').attr('src', '../images/default_upload_photo.jpg');
  $('#btn_save').removeClass('bg-green');
  $('#btn_save').addClass('bg-blue');
  $('#btn_save').html('<i class="fa fa-plus"></i> Add');
}

/***/ }),

/***/ 9:
/*!********************************************************************!*\
  !*** multi ./resources/assets/js/pages/admin/settings/settings.js ***!
  \********************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\xampp\htdocs\en-pms\resources\assets\js\pages\admin\settings\settings.js */"./resources/assets/js/pages/admin/settings/settings.js");


/***/ })

/******/ });