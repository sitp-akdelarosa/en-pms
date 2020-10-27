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
/******/ 	return __webpack_require__(__webpack_require__.s = 23);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/assets/js/pages/ppc/reports/transfer-item-report.js":
/*!***********************************************************************!*\
  !*** ./resources/assets/js/pages/ppc/reports/transfer-item-report.js ***!
  \***********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

var transfer_item_arr = [];
$(function () {
  getTransferEntry();
  check_permission(code_permission);
});

function getTransferEntry() {
  transfer_item_arr = [];
  $.ajax({
    url: getTransferEntryURL,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token
    }
  }).done(function (data, textStatus, xhr) {
    transfer_item_arr = data;
    makeTransferItemTable(transfer_item_arr);
  }).fail(function (xhr, textStatus, errorThrown) {
    msg(errorThrown, textStatus);
  });
}

function makeTransferItemTable(arr) {
  $('#tbl_transfer_item').dataTable().fnClearTable();
  $('#tbl_transfer_item').dataTable().fnDestroy();
  $('#tbl_transfer_item').dataTable({
    data: arr,
    bLengthChange: false,
    searching: true,
    paging: true,
    columns: [{
      data: 'jo_no'
    }, {
      data: 'prod_order_no'
    }, {
      data: 'prod_code'
    }, {
      data: 'current_div_code'
    }, {
      data: 'current_process_name'
    }, {
      data: 'div_code_code'
    }, {
      data: 'process'
    }, {
      data: 'qty'
    }, {
      data: 'status'
    }, {
      data: 'remarks'
    }, {
      data: function data(x) {
        if (x.item_status == 1) {
          return "READY FOR RECEIVE";
        } else if (x.item_status == 2) {
          return "RECEIVED";
        } else if (x.item_status == 3) {
          return "DISAPPROVED";
        } else {
          return "NOT YET APPROVED";
        }
      }
    }, {
      data: function data(x) {
        if (x.item_status == 2) {
          return x.updated_at;
        } else {
          return "";
        }
      }
    }, {
      data: function data(x) {
        if (x.item_status == 2) {
          return x.receive_qty;
        } else {
          return "";
        }
      }
    }, {
      data: function data(x) {
        if (x.item_status == 2) {
          return x.receive_remarks;
        } else {
          return "";
        }
      }
    }],
    fnInitComplete: function fnInitComplete() {
      $('.dataTables_scrollBody').slimscroll();
    }
  });
}

/***/ }),

/***/ 23:
/*!*****************************************************************************!*\
  !*** multi ./resources/assets/js/pages/ppc/reports/transfer-item-report.js ***!
  \*****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\laragon\www\en-pms\resources\assets\js\pages\ppc\reports\transfer-item-report.js */"./resources/assets/js/pages/ppc/reports/transfer-item-report.js");


/***/ })

/******/ });