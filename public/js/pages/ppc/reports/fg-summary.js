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
/******/ 	return __webpack_require__(__webpack_require__.s = 20);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/assets/js/pages/ppc/reports/fg-summary.js":
/*!*************************************************************!*\
  !*** ./resources/assets/js/pages/ppc/reports/fg-summary.js ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

var FG_arr = [];
$(function () {
  getFG(0);
  $('#tbl_fg_summary').on('click', '.btn_edit_fg', function () {
    $('#id').val($(this).attr('data-id'));
    $('#current_sc_no').val($(this).attr('data-sc_no'));
    $('#prod_code').val($(this).attr('data-prod_code'));
    $('#description').val($(this).attr('data-description'));
    $('#order_qty').val($(this).attr('data-order_qty'));
    $('#qty').val($(this).attr('data-qty'));
    get_sc_no($(this).attr('data-sc_no'), $(this).attr('data-prod_code'));
    $('#modal_fg_summary').modal('show');
  });
  $("#frm_fg_summary").on('submit', function (e) {
    e.preventDefault();

    if (parseInt($('#qty').val()) > parseInt($('#order_qty').val())) {
      msg('Qty need to be less than order qty', 'warning');
    } else if (parseInt($('#qty').val()) > parseInt($('#total_order_qty').val())) {
      msg('Qty need to be less than order qty of SC # selected', 'warning');
    } else if (parseInt($('#qty').val()) < 0 || parseInt($('#qty').val()) == 0) {
      msg('Please input valid number', 'warning');
    } else {
      var form_action = $(this).attr("action");
      $.ajax({
        dataType: 'json',
        type: 'POST',
        url: form_action,
        data: $(this).serialize()
      }).done(function (data, textStatus, xhr) {
        msg(data.msg, data.status);
        getFG($('#status').val());

        if (data.status == 'success') {
          $('#modal_fg_summary').modal('hide');
        }
      }).fail(function (xhr, textStatus, errorThrown) {
        var errors = xhr.responseJSON.errors;
        showErrors(errors);
      });
    }
  });
  $('#qty').on('change', function (e) {
    e.preventDefault();

    if (parseInt($(this).val()) > parseInt($('#order_qty').val())) {
      msg('Qty need to be less than order qty', 'warning');
    } else if (parseInt($(this).val()) > parseInt($('#total_order_qty').val())) {
      msg('Qty need to be less than order qty of SC # selected', 'warning');
    }
  });
  $('#sc_no').on('change', function (e) {
    e.preventDefault();
    $('#total_order_qty').val($(this).find("option:selected").attr('data-order_qty'));
  });
  $('#status').on('change', function (e) {
    getFG($(this).val());
  });
});

function getFG(status) {
  transfer_item_arr = [];
  $.ajax({
    url: getFGURL,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token,
      status: status
    }
  }).done(function (data, textStatus, xhr) {
    FG_arr = data;
    makeFGTable(FG_arr);
  }).fail(function (xhr, textStatus, errorThrown) {
    msg(errorThrown, textStatus);
  });
}

function makeFGTable(arr) {
  $('#tbl_fg_summary').dataTable().fnClearTable();
  $('#tbl_fg_summary').dataTable().fnDestroy();
  $('#tbl_fg_summary').dataTable({
    data: arr,
    bLengthChange: false,
    searching: true,
    paging: true,
    columns: [{
      data: function data(x) {
        return '<button class="btn btn-sm bg-blue btn_edit_fg" ' + 'data-id="' + x.id + '" ' + 'data-sc_no="' + x.sc_no + '" ' + 'data-prod_code="' + x.prod_code + '" ' + 'data-description="' + x.description + '" ' + 'data-order_qty="' + x.order_qty + '" ' + 'data-qty="' + x.qty + '">' + '<i class="fa fa-edit"></i>' + '</button>';
      },
      searchable: false,
      orderable: false
    }, {
      data: 'sc_no',
      name: 'sc_no'
    }, {
      data: 'prod_code',
      name: 'prod_code'
    }, {
      data: 'description',
      name: 'description'
    }, {
      data: 'order_qty',
      name: 'order_qty'
    }, {
      data: 'qty',
      name: 'qty'
    }]
  });
}

function get_sc_no(sc_nos, prod_code) {
  var sc_no = '<option></option>';
  $('#sc_no').html(sc_no);
  $.ajax({
    url: getSc_noURL,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token,
      prod_code: prod_code
    }
  }).done(function (data, textStatus, xhr) {
    $.each(data, function (i, x) {
      if (x.sc_no != sc_nos) {
        sc_no = '<option value="' + x.sc_no + '" data-order_qty="' + x.order_qty + '">' + x.sc_no + '</option>';
        $('#sc_no').append(sc_no);
      }
    });
  }).fail(function (xhr, textStatus, errorThrown) {
    msg(errorThrown, textStatus);
  });
}

function clear() {
  $('.clear').val('');
}

/***/ }),

/***/ 20:
/*!*******************************************************************!*\
  !*** multi ./resources/assets/js/pages/ppc/reports/fg-summary.js ***!
  \*******************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\laragon\www\en-pms\resources\assets\js\pages\ppc\reports\fg-summary.js */"./resources/assets/js/pages/ppc/reports/fg-summary.js");


/***/ })

/******/ });