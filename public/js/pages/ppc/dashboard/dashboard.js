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
/******/ 	return __webpack_require__(__webpack_require__.s = 4);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/assets/js/pages/ppc/dashboard/dashboard.js":
/*!**************************************************************!*\
  !*** ./resources/assets/js/pages/ppc/dashboard/dashboard.js ***!
  \**************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

var dashboard_arr = [];
var dataColumn = [{
  data: 'jo_sequence',
  name: 'ts.jo_sequence'
}, {
  data: 'prod_code',
  name: 'ts.prod_code'
}, {
  data: 'description',
  name: 'ts.description'
}, {
  data: 'div_code',
  name: 'p.div_code'
}, {
  data: 'plant',
  name: 'd.plant'
}, {
  data: 'process',
  name: 'p.process'
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
  data: function data(e) {
    return e.status;
  },
  name: 'p.status' // { data: 'status', name: 'p.status'}

}];
$(function () {
  getDatatable('tbl_dashboard', get_dashboard, dataColumn, [], 0);
  get_chart();
  get_jono('');
  $('#jo_no').on('change', function (e) {
    e.preventDefault();
    get_chart($(this).val());
  });
  $("#search").on('click', function (e) {
    if ($('#date_from').val() != '' || $('#date_from').val() != '') {
      getDatatable('tbl_dashboard', get_dashboard + '?date_from=' + $('#date_from').val() + '&date_to=' + $('#date_to').val(), dataColumn, [], 0);
    } else {
      msg('Please Input date', 'warning');
    }
  });
});

function get_chart(jo_no) {
  $('#chart').html('');
  var count = 0;
  $.ajax({
    url: get_chartURl,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token,
      jo_no: jo_no
    }
  }).done(function (data, textStatus, xhr) {
    $.each(data, function (i, x) {
      count++;
      chart = '<div class="col-md-6">' + '<div class="box box-solid">' + '<div class="box-body text-center">' + '<div class="row">' + '<div class="col-md-12">' + '<div id="' + count + '" style="height: 370px; max-width: 920px; margin: 0px auto;"></div>' + '</div>' + '</div>' + '</div>' + '</div>' + '</div>';
      $('#chart').append(chart);
      var options = {
        title: {
          text: x.process,
          fontSize: 20
        },
        theme: "light2",
        exportEnabled: true,
        animationEnabled: true,
        legend: {
          cursor: "pointer",
          itemclick: explodePie
        },
        data: [{
          type: "pie",
          toolTipContent: "{label}: <strong>{y}%</strong>",
          showInLegend: "true",
          legendText: "{label}",
          yValueFormatString: "##0.00\"%\"",
          indexLabel: "{label} {y}",
          dataPoints: x.records
        }]
      };
      $('#' + count).CanvasJSChart(options);

      function explodePie(e) {
        if (typeof e.dataSeries.dataPoints[e.dataPointIndex].exploded === "undefined" || !e.dataSeries.dataPoints[e.dataPointIndex].exploded) {
          e.dataSeries.dataPoints[e.dataPointIndex].exploded = true;
        } else {
          e.dataSeries.dataPoints[e.dataPointIndex].exploded = false;
        }

        e.chart.render();
      }
    });
  }).fail(function (xhr, textStatus, errorThrown) {
    msg(errorThrown, textStatus);
  });
}

function get_jono() {
  $('#jo_no').html("<option value=''></option>");
  $.ajax({
    url: get_jonoURL,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token
    }
  }).done(function (data, textStatus, xhr) {
    $.each(data, function (i, x) {
      $('#jo_no').append("<option value='" + x.jo_sequence + "'>" + x.jo_sequence + "</option>");
    });
  }).fail(function (xhr, textStatus, errorThrown) {
    msg(errorThrown, textStatus);
  });
}

/***/ }),

/***/ 4:
/*!********************************************************************!*\
  !*** multi ./resources/assets/js/pages/ppc/dashboard/dashboard.js ***!
  \********************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\xampp\htdocs\en-pms\resources\assets\js\pages\ppc\dashboard\dashboard.js */"./resources/assets/js/pages/ppc/dashboard/dashboard.js");


/***/ })

/******/ });