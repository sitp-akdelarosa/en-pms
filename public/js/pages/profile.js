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
/******/ 	return __webpack_require__(__webpack_require__.s = 1);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/assets/js/pages/profile.js":
/*!**********************************************!*\
  !*** ./resources/assets/js/pages/profile.js ***!
  \**********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

var get_count = 5;
var timeline;
$(function () {
  getTimeline($('#user_id').val());
  $('#show-more').on('click', function () {
    getTimeline($('#user_id').val());
  });
});

function getTimeline(user_id) {
  $('#timeline-loading').show();
  $('#show-more-row').hide();
  setTimeout(function () {
    $.ajax({
      url: '../timeline',
      type: 'GET',
      dataType: 'JSON',
      data: {
        _token: token,
        user_id: user_id,
        take: get_count
      }
    }).done(function (data, textStatus, xhr) {
      $('#timeline-loading').hide();
      $('#show-more-row').show();
      $.each(data, function (i, x) {
        timeline = '<li class="time-label">' + '<span class="bg-red">' + i + '</span>' + '</li>';
        $.each(x, function (ii, xx) {
          timeline += '<li>' + // '<i class="ion ion-email bg-blue"></i>'+
          '<div class="timeline-item">' + '<span class="time"><i class="fa fa-clock-o"></i> ' + xx.time + '</span>' + '<h3 class="timeline-header">' + xx.module + '</h3>' + '<div class="timeline-body">' + xx.action;
          '</div>' + '</div>' + '</li>';
        });
        $('#timeline').append(timeline);
      });
      get_count = get_count + 5;
      console.log(get_count);
    }).fail(function (xhr, textStatus, errorThrown) {
      console.log("error");
    });
  }, 1000);
}

/***/ }),

/***/ 1:
/*!****************************************************!*\
  !*** multi ./resources/assets/js/pages/profile.js ***!
  \****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\xampp\htdocs\en-pms\resources\assets\js\pages\profile.js */"./resources/assets/js/pages/profile.js");


/***/ })

/******/ });