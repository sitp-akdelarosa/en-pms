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
/******/ 	return __webpack_require__(__webpack_require__.s = 3);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/assets/js/pages/ppc/for-approval.js":
/*!*******************************************************!*\
  !*** ./resources/assets/js/pages/ppc/for-approval.js ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(function () {
  getTransferItems($('meta[name=user_id]').attr('content'));
  $('#transfer_item_approval').on('click', '.approve', function () {
    var id = $(this).attr('data-id');
    var status = $(this).attr('data-status');
    confirmAnswer(id, status);
  });
  $('#transfer_item_approval').on('click', '.disapprove', function () {
    var id = $(this).attr('data-id');
    var status = $(this).attr('data-status');
    confirmAnswer(id, status);
  });
});

function getTransferItems(user_id) {
  var items = '';
  $('#transfer_item_approval').html(items);
  $('#timeline-loading').show();
  $.ajax({
    url: getTransferItemsURL,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token,
      user_id: user_id
    }
  }).done(function (data, textStatus, xhr) {
    $('#timeline-loading').hide();
    $.each(data, function (i, x) {
      items = '<form >' + '<div class="from-group row">' + '<div class="col-md-6">' + '<div class="table-responsive">' + '<table class="table">' + '<thead>' + '<tr>' + '<th colspan="2"><h5>From</h5></th>' + '</tr>' + '</thead>' + '<tbody>' + '<tr>' + '<th>Division Code:</th>' + '<td>' + x.current_div_code + '</td>' + '</tr>' + '<tr>' + '<th>Process:</th>' + '<td>' + x.current_process + '</td>' + '</tr>' + '<tr>' + '<th>Line Leader:</th>' + '<td>' + x.from_user + '</td>' + '</tr>' + '</tbody>' + '</table>' + '</div>' + '</div>' + '<div class="col-md-6">' + '<div class="table-responsive">' + '<table class="table">' + '<thead>' + '<tr>' + '<th colspan="2"><h5>To</h5></th>' + '</tr>' + '</thead>' + '<tbody>' + '<tr>' + '<th>Division Code</th>' + '<td>' + x.div_code + '</td>' + '</tr>' + '<tr>' + '<th>Process</th>' + '<td>' + x.process + '</td>' + '</tr>' + '</tbody>' + '</table>' + '</div>' + '</div>' + '</div>' + '<div class="form-group">' + '<div class="col-md-12">' + '<button type="button" class="btn btn-sm bg-green approve pull-right" data-id="' + x.id + '" data-status="1">Approve</button>' + '<button type="button" class="btn btn-sm bg-red disapprove pull-right" data-id="' + x.id + '" data-status="3">Disapprove</button>' + '</div>' + '</div>' + '</form>' + '<br><hr>';
      $('#transfer_item_approval').append(items);
    });
  }).fail(function (xhr, textStatus, errorThrown) {
    msg(errorThrown, textStatus);
  });
}

function confirmAnswer(id, status) {
  $.ajax({
    url: answerRequestURL,
    type: 'POST',
    dataType: 'JSON',
    data: {
      _token: token,
      id: id,
      status: status
    }
  }).done(function (data, textStatus, xhr) {
    getTransferItems($('meta[name=user_id]').attr('content'));

    if (data == 1) {
      msg('Successfully Approve', 'success');
    } else {
      msg('Successfully Disapprove', 'success');
    }
  }).fail(function (xhr, textStatus, errorThrown) {
    msg(errorThrown, textStatus);
  });
}

/***/ }),

/***/ 3:
/*!*************************************************************!*\
  !*** multi ./resources/assets/js/pages/ppc/for-approval.js ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\xampp\htdocs\en-pms\resources\assets\js\pages\ppc\for-approval.js */"./resources/assets/js/pages/ppc/for-approval.js");


/***/ })

/******/ });