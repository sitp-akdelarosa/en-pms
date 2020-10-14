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
/******/ 	return __webpack_require__(__webpack_require__.s = 21);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/assets/js/pages/production/dashboard/dashboard.js":
/*!*********************************************************************!*\
  !*** ./resources/assets/js/pages/production/dashboard/dashboard.js ***!
  \*********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

var dashboard_arr = [];
$(function () {
  getDashboard();
});

function getDashboard() {
  $.ajax({
    url: getDashBoardURL,
    type: 'GET',
    dataType: 'JSON'
  }).done(function (data, textStatus, xhr) {
    dashboard_arr = data;
    makeDashTable(dashboard_arr);
  }).fail(function () {
    console.log("error");
  });
}

function makeDashTable(arr) {
  $('#tbl_prod_dashboard').dataTable().fnClearTable();
  $('#tbl_prod_dashboard').dataTable().fnDestroy();
  $('#tbl_prod_dashboard').dataTable({
    data: arr,
    bLengthChange: false,
    paging: true,
    searching: true,
    columns: [{
      data: 'jo_sequence',
      name: 'ts.jo_sequence'
    }, {
      data: 'prod_code',
      name: 'ts.prod_code'
    }, {
      data: 'description',
      name: 'ts.description'
    }, {
      data: 'process',
      name: 'p.process'
    }, {
      data: 'div_code',
      name: 'p.div_code'
    }, {
      data: 'plant',
      name: 'd.plant'
    }, {
      data: 'material_used',
      name: 'ts.material_used'
    }, {
      data: 'material_heat_no',
      name: 'ts.material_heat_no'
    }, {
      data: 'lot_no',
      name: 'ts.lot_no'
    }, {
      data: 'order_qty',
      name: 'ts.order_qty'
    }, {
      data: 'issued_qty',
      name: 'ts.issued_qty'
    }, {
      data: 'unprocessed',
      name: 'p.unprocessed'
    }, {
      data: 'good',
      name: 'p.good'
    }, {
      data: 'rework',
      name: 'p.rework'
    }, {
      data: 'scrap',
      name: 'p.scrap'
    }, {
      data: function data(x) {
        var status = 'ON PROCESS';

        if (x.status == 1) {
          status = 'READY FOR FG';
        } else if (x.status == 2) {
          status = 'FINISHED';
        } else if (x.status == 3) {
          status = 'CANCELLED';
        } else if (x.status == 4) {
          status = 'TRANSFER ITEM';
        }

        return status;
      }
    }]
  });
}

/***/ }),

/***/ 21:
/*!***************************************************************************!*\
  !*** multi ./resources/assets/js/pages/production/dashboard/dashboard.js ***!
  \***************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\laragon\www\en-pms\resources\assets\js\pages\production\dashboard\dashboard.js */"./resources/assets/js/pages/production/dashboard/dashboard.js");


/***/ })

/******/ });