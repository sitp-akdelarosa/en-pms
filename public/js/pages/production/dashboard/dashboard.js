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
  var tbl_prod_dashboard = $('#tbl_prod_dashboard').DataTable();
  tbl_prod_dashboard.clear();
  tbl_prod_dashboard.destroy();
  tbl_prod_dashboard = $('#tbl_prod_dashboard').DataTable({
    ajax: {
      url: getDashBoardURL,
      data: {
        _token: token
      },
      error: function error(xhr, textStatus, errorThrown) {
        ErrorMsg(xhr);
      }
    },
    processing: true,
    order: [[0, 'desc']],
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
      data: 'lot_no',
      name: 'ts.lot_no'
    }, {
      data: 'issued_qty',
      name: 'ts.issued_qty'
    }, {
      data: 'process',
      name: 'p.process'
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
        switch (x.status) {
          case 1:
          case '1':
            return 'DONE PROCESS';
            break;

          case 2:
          case '2':
            return 'ON-GOING';
            break;

          case 3:
          case '31':
            return 'CANCELLED';
            break;

          case 4:
          case '4':
            return 'TRANSFER ITEM';
            break;

          case 5:
          case '5':
            return 'ALL PROCESS DONE';
            break;

          case 7:
          case '7':
            return 'RECEIVED';
            break;

          case 0:
          case '0':
            return 'WAITING';
            break;
        } // var status = '';
        // if (x.status == 1) {
        //     status = 'DONE'; //READY FOR FG
        // } else if (x.status == 2){
        //     status = 'FINISHED';
        // } else if (x.status == 3){
        //     status = 'CANCELLED';
        // } else if (x.status == 4){
        //     status = 'TRANSFER ITEM';
        // }
        // return status;

      }
    }, {
      data: 'end_date',
      name: 'p.end_date'
    }],
    fnDrawCallback: function fnDrawCallback() {
      $("#tbl_prod_dashboard").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
    },
    initComplete: function initComplete() {
      $('.loadingOverlay').hide();
    },
    createdRow: function createdRow(row, data, dataIndex) {
      if (data.status == 2 || data.status == '2') {
        $(row).css('background-color', '#001F3F'); // NAVY

        $(row).css('color', '#fff');
      }

      if (data.status == 3 || data.status == '3') {
        $(row).css('background-color', '#ff6266'); // RED

        $(row).css('color', '#fff');
      }

      if (data.status == 4 || data.status == '4') {
        $(row).css('background-color', '#7460ee'); // PURPLE

        $(row).css('color', '#fff');
      }

      if (data.status == 5 || data.status == '1') {
        $(row).css('background-color', 'rgb(139 241 191)'); // GREEN

        $(row).css('color', '#000000');
      }

      if (data.status == 6 || data.status == '5') {
        $(row).css('background-color', 'rgb(121 204 241)'); // BLUE

        $(row).css('color', '#000000');
      }
    }
  });
}

/***/ }),

/***/ 23:
/*!***************************************************************************!*\
  !*** multi ./resources/assets/js/pages/production/dashboard/dashboard.js ***!
  \***************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\laragon\www\en-pms\resources\assets\js\pages\production\dashboard\dashboard.js */"./resources/assets/js/pages/production/dashboard/dashboard.js");


/***/ })

/******/ });