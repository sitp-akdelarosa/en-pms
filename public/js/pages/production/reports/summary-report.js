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
/******/ 	return __webpack_require__(__webpack_require__.s = 28);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/assets/js/pages/production/reports/summary-report.js":
/*!************************************************************************!*\
  !*** ./resources/assets/js/pages/production/reports/summary-report.js ***!
  \************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(function () {
  init();
  $("#frm_summary").on('submit', function (e) {
    e.preventDefault();
    $('.loadingOverlay').show();
    $.ajax({
      dataType: 'json',
      type: 'POST',
      url: $(this).attr("action"),
      data: $(this).serialize()
    }).done(function (data, textStatus, xhr) {
      if (data.status == 'failed') {
        msg(data.msg, data.status);
        $("#btnDownload").attr("disabled", true);
      } else {
        $("#btnDownload").attr("disabled", false);
        makeSummaryTable(data.ppo);
      }
    }).fail(function (xhr, textStatus, errorThrown) {
      ErrorMsg(xhr);
    }).always(function () {
      $('.loadingOverlay').hide();
    });
  });
  $('#btnDownload').on('click', function () {
    window.location.href = downloadExcel + "?date_from=" + $('#date_from').val() + "&date_to=" + $('#date_to').val();
  });
});

function init() {
  check_permission(code_permission, function (output) {
    if (output == 1) {}

    makeSummaryTable();
  });
}

function makeSummaryTable(arr) {
  $('#tbl_summary').dataTable().fnClearTable();
  $('#tbl_summary').dataTable().fnDestroy();
  $('#tbl_summary').dataTable({
    data: arr,
    lengthMenu: [[5, 10, 15, 20, -1], [5, 10, 15, 20, "All"]],
    pageLength: 10,
    order: [[9, 'desc']],
    columns: [{
      data: 'date_upload',
      name: 'date_upload'
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
      data: 'alloy',
      name: 'alloy'
    }, {
      data: 'size',
      name: 'size'
    }, {
      data: 'class',
      name: 'class'
    }, {
      data: 'heatno',
      name: 'heatno'
    }, {
      data: 'quantity',
      name: 'quantity'
    }, {
      data: 'good',
      name: 'good'
    }, {
      data: 'rework',
      name: 'rework'
    }, {
      data: 'scrap',
      name: 'scrap'
    }, {
      data: 'finish_weight',
      name: 'finish_weight'
    }, {
      data: 'wgood',
      name: 'wgood'
    }, {
      data: 'wrework',
      name: 'wrework'
    }, {
      data: 'wscrap',
      name: 'wscrap'
    }, {
      data: 'rrework',
      name: 'rrework'
    }, {
      data: 'rscrap',
      name: 'rscrap'
    }, {
      data: 'jono',
      name: 'jono'
    }],
    fnDrawCallback: function fnDrawCallback() {
      $("#tbl_summary").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
    },
    initComplete: function initComplete() {
      $('.loadingOverlay').hide();
    }
  });
}

/***/ }),

/***/ 28:
/*!******************************************************************************!*\
  !*** multi ./resources/assets/js/pages/production/reports/summary-report.js ***!
  \******************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\laragon\www\en-pms\resources\assets\js\pages\production\reports\summary-report.js */"./resources/assets/js/pages/production/reports/summary-report.js");


/***/ })

/******/ });