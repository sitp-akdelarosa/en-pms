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
/******/ 	return __webpack_require__(__webpack_require__.s = 13);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/assets/js/pages/ppc/masters/operator-master/operator-master.js":
/*!**********************************************************************************!*\
  !*** ./resources/assets/js/pages/ppc/masters/operator-master/operator-master.js ***!
  \**********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

var dataColumn = [{
  data: function data(_data) {
    return '<input type="checkbox" class="table-checkbox check_item" value="' + _data.id + '">';
  },
  name: 'id',
  orderable: false,
  searchable: false
}, {
  data: 'action',
  name: 'action',
  orderable: false,
  searchable: false
}, {
  data: 'operator_id',
  name: 'operator_id'
}, {
  data: 'firstname',
  name: 'firstname'
}, {
  data: 'lastname',
  name: 'lastname'
}];
$(function () {
  getDatatable('tbl_operator', getOutputsURL, dataColumn, [], 0);
  check_permission(code_permission);
  checkAllCheckboxesInTable('.check_all', '.check_item');
  $('#btn_clear').on('click', function () {
    clear();
    $('#btn_save').removeClass('bg-green');
    $('#btn_save').addClass('bg-blue');
    $('#btn_save').html('<i class="fa fa-floppy-o"></i> Save');
  }); //Add and save update

  $(function () {
    $("#frm_operator").on('submit', function (e) {
      e.preventDefault();
      var form_action = $(this).attr("action");
      $.ajax({
        dataType: 'json',
        type: 'POST',
        url: form_action,
        data: $(this).serialize()
      }).done(function (data, textStatus, xhr) {
        if (data.status == 'success') {
          getDatatable('tbl_operator', getOutputsURL, dataColumn, [], 0);
          $('#btn_save').removeClass('bg-green');
          $('#btn_save').addClass('bg-blue');
          $('#btn_save').html('<i class="fa fa-floppy-o"></i> Save');
          clear();
        }

        msg(data.msg, data.status);
      }).fail(function (xhr, textStatus, errorThrown) {
        var errors = xhr.responseJSON.errors;
        showErrors(errors);
      });
    });
  }); //Edit table

  $('#tbl_operator').on('click', '.btn_edit', function (e) {
    $('#operator_id').val($(this).attr('data-operator_id'));
    $('#id').val($(this).attr('data-id'));
    $('#firstname').val($(this).attr('data-firstname'));
    $('#lastname').val($(this).attr('data-lastname'));
    $('#btn_save').removeClass('bg-blue');
    $('#btn_save').addClass('bg-green');
    $('#btn_save').html('<i class="fa fa-check"></i> Update');
  }); //Delete Multiple data

  $('#btn_delete').on('click', function () {
    delete_set('.check_item', deleteOM);
  });
}); //Multiple Delete 

function delete_set(checkboxClass, deleteOM) {
  var chkArray = [];
  $(checkboxClass + ":checked").each(function () {
    chkArray.push($(this).val());
  });

  if (chkArray.length > 0) {
    confirm_delete(chkArray, token, deleteOM, true, 'tbl_operator', getOutputsURL, dataColumn);
  } else {
    msg("Please select at least 1 item.", "failed");
  }

  $('.check_all').prop('checked', false);
  clear();
  $('#btn_save').removeClass('bg-green');
  $('#btn_save').addClass('bg-blue');
  $('#btn_save').html('<i class="fa fa-plus"></i> Add');
} //Clear Textbox


function clear() {
  $('.clear').val('');
}

/***/ }),

/***/ 13:
/*!****************************************************************************************!*\
  !*** multi ./resources/assets/js/pages/ppc/masters/operator-master/operator-master.js ***!
  \****************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\xampp\htdocs\en-pms\resources\assets\js\pages\ppc\masters\operator-master\operator-master.js */"./resources/assets/js/pages/ppc/masters/operator-master/operator-master.js");


/***/ })

/******/ });