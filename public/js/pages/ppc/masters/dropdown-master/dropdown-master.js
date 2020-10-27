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

/***/ "./resources/assets/js/pages/ppc/masters/dropdown-master/dropdown-master.js":
/*!**********************************************************************************!*\
  !*** ./resources/assets/js/pages/ppc/masters/dropdown-master/dropdown-master.js ***!
  \**********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

var dataColumn = [{
  data: 'action',
  name: 'action',
  orderable: false,
  searchable: false,
  width: '5%'
}, {
  data: 'dropdown_name',
  name: 'dropdown_name',
  width: '95%'
}];
var dropdown_item_arr = [];
$(function () {
  dropdown_names_options(true);
  checkAllCheckboxesInTable('.check_all', '.check_item');
  init();
  $('#tbl_dropdown_body').on('click', '.btn_edit_dropdown_name', function (e) {
    e.preventDefault();
    getDropdownItems($(this).attr('data-id'));
    $('#selected_dropdown_name_id').val($(this).attr('data-id'));
    $('#selected_dropdown_name').val($(this).attr('data-dropdown_name'));
    $('#modal_dropdown_option_title').html('Edit Item/Option for ' + $(this).attr('data-dropdown_name'));
    $('#modal_dropdown_option').modal('show');
  });
  $("#frm_dropdown_items_value").on('submit', function (e) {
    e.preventDefault();
    $('#btn_add_dropdown_item').click();
  });
  $('#frm_dropdown_items').on('submit', function (e) {
    $('.loadingOverlay').show();
    e.preventDefault();
    $.ajax({
      url: $(this).attr('action'),
      type: 'POST',
      dataType: 'JSON',
      data: $(this).serialize()
    }).done(function (data, textStatus, xhr) {
      if (textStatus) {
        msg("Data was successfully saved.", textStatus);
        $('#dropdown_item').val('');
        getDatatable('tbl_dropdown', dropdownListURL, dataColumn, [], 0);
        getDropdownItems($('#selected_dropdown_name_id').val());
        $('#dropdown_item_id').val('');
      }
    }).fail(function (xhr, textStatus, errorThrown) {
      var errors = xhr.responseJSON.errors;
      showErrors(errors);
    }).always(function (xhr, textStatus) {
      $('.loadingOverlay').hide();
    });
  });
  $('#btn_add_dropdown_item').on('click', function () {
    if ($('#new_item').val() == "") {
      msg("The Item field is required.", "failed");
    } else if ($('#new_item').val().length >= 50) {
      msg("The Item may not be greater than 50 characters.", "failed");
    } else {
      if (dropdown_item_arr.indexOf($('#new_item').val()) != -1) {
        msg("Item already existing.", "failed");
      } else {
        dropdown_item_arr.push($('#new_item').val());
        makeDropdownTable(dropdown_item_arr);
      }
    }
  });
  $('#tbl_item_list_body').on('click', '.btn_remove_dropdown_item', function () {
    var count = $(this).attr('data-count');
    $('#' + count).remove();
    count--;
    dropdown_item_arr.splice(count, 1);
    makeDropdownTable(dropdown_item_arr);

    if ($('#tbl_item_list_body > tr').length < 1) {
      $('#tbl_item_list_body').html('<tr>' + '<td colspan="3" class="text-center">No data displayed.</td>' + '</tr>');
    }
  }); // $('#btn_delete_dropdown_name').on('click', function() {
  // 	var id = $('#select_dropdown_name option:selected').val();
  // 	swal({
  //         title: "Are you sure?",
  //         text: "You will not be able to recover your data!",
  //         type: "warning",
  //         showCancelButton: true,
  //         confirmButtonColor: "#f95454",
  //         confirmButtonText: "Yes",
  //         cancelButtonText: "No",
  //         closeOnConfirm: true,
  //         closeOnCancel: false
  //     }, function(isConfirm){
  //         if (isConfirm) {
  //         	$.ajax({
  //         		url: dropdownNamesDeleteURL,
  //         		type: 'POST',
  //         		dataType: 'JSON',
  //         		data: {
  //         			_token:token,
  //         			id: id
  //         		},
  //         	}).done(function(data, textStatus, xhr) {
  //         		if (data.status == 'success') {
  //         			msg(data.msg,data.status)
  //         		} else {
  //                     msg(data.msg,data.status)
  //                 }
  //                	dropdown_names_options(true);
  //         	}).fail(function(xhr, textStatus, errorThrown) {
  //         		msg(errorThrown,'error');
  //         	});
  //         } else {
  //             swal("Cancelled", "Your data is safe and not deleted.");
  //         }
  //     });
  // });
  // $('#btn_delete_dropdown_option').on('click', function() {
  // 	delete_items('.check_item',dropdownItemsDeleteURL);
  // });
  // $('#select_dropdown_name').on('change', function() {
  // 	getDatatable('tbl_dropdown',dropdownListURL,dataColumn,[],0);
  // });
  // $('#btn_add_dropdown_name').on('click', function() {
  // 	$('#dropdown_name_id').val('');
  // 	$('#modal_dropdown_name').modal('show');
  // });
  // $('#btn_edit_dropdown_name').on('click', function() {
  // 	$('#dropdown_name').val($('#select_dropdown_name option:selected').text());
  // 	$('#dropdown_name_id').val($('#select_dropdown_name option:selected').val());
  // 	$('#modal_dropdown_name').modal('show');
  // });
  // $('#btn_add_dropdown_option').on('click', function() {
  // 	$('#modal_dropdown_option_title').html('Item/Option for '+$('#select_dropdown_name option:selected').text());
  // 	$('#selected_dropdown_name_id').val($('#select_dropdown_name option:selected').val());
  // 	$('#selected_dropdown_name').val($('#select_dropdown_name option:selected').text());
  // 	$('#modal_dropdown_option').modal('show');
  // });
  // $('#frm_dropdown_name').on('submit', function(e) {
  // 	e.preventDefault();
  //   		$.ajax({
  // 		url: $(this).attr('action'),
  // 		type: 'POST',
  // 		dataType: 'JSON',
  // 		data: $(this).serialize(),
  // 	}).done(function(data, textStatus, xhr) {
  // 		if (textStatus) {
  // 			msg("Data was successfully saved.",textStatus);
  // 			$('#dropdown_name').val('');
  // 			dropdown_names_options(true,data.id);
  // 		}
  // 	}).fail(function(xhr, textStatus, errorThrown) {
  // 		var errors = xhr.responseJSON.errors;
  // 		showErrors(errors);
  // 	}).always(function(xhr, textStatus) {
  // 		console.log("complete");
  // 	});
  // });
});

function init() {
  check_permission(code_permission, function (output) {
    if (output == 1) {}
  });
}

function dropdown_names_options(refresh_table) {
  var id = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 1;
  var options = "";
  $('#select_dropdown_name').html(options);
  $.ajax({
    url: dropdownNamesURL,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token
    }
  }).done(function (data, textStatus, xhr) {
    if (data.length < 1) {
      options = "<option value=''>No data available</option>";
      $('#select_dropdown_name').append(options);
    } else {
      $.each(data, function (i, x) {
        options = "<option value='" + x.id + "'>" + x.dropdown_name + "</option>";
        $('#select_dropdown_name').append(options);
      });
      $('#select_dropdown_name').val(id);
    }

    if (refresh_table == true) {
      getDatatable('tbl_dropdown', dropdownListURL, dataColumn, [], 1);
    }
  }).fail(function (xhr, textStatus, errorThrown) {
    msg(errorThrown, textStatus);
  });
}

function makeDropdownTable(arr) {
  var tbl = '';
  $('#tbl_item_list_body').html(tbl);
  var cnt = 1;
  $.each(arr, function (i, x) {
    tbl = '<tr id="' + cnt + '">' + '<td>' + cnt + '</td>' + '<td>' + x + '<input type="hidden" name="dropdown_item[]" value="' + x + '">' + '</td>' + '<td>' + '<span class="btn_remove_dropdown_item" data-count="' + cnt + '">' + '<i class="text-red fa fa-times"></i>' + '</span>' + '</td>' + '</tr>';
    $('#tbl_item_list_body').append(tbl);
    cnt++;
  });
  $('#new_item').val('');
}

function getDropdownItems(id) {
  dropdown_item_arr = [];
  $.ajax({
    url: getDropdownItemURL,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token,
      id: id
    }
  }).done(function (data, textStatus, xhr) {
    $.each(data, function (i, x) {
      dropdown_item_arr.push(x.dropdown_item);
    });
    makeDropdownTable(dropdown_item_arr);
  }).fail(function (xhr, textStatus, errorThrown) {
    msg(errorThrown, textStatus);
  });
} // function delete_items(checkboxClass,deleteURL) {
// 	var chkArray = [];
// 	$(checkboxClass+":checked").each(function() {
// 		chkArray.push($(this).val());
// 	});
// 	if (chkArray.length > 0) {
// 		var listURL = dropdownListURL;
// 		confirm_delete(chkArray,token,deleteURL,true,'tbl_dropdown',listURL,dataColumn);
// 	} else {
// 		msg("Please select at least 1 item.");
// 	}
// 	$('.check_all').prop('checked',false);
// }

/***/ }),

/***/ 13:
/*!****************************************************************************************!*\
  !*** multi ./resources/assets/js/pages/ppc/masters/dropdown-master/dropdown-master.js ***!
  \****************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\laragon\www\en-pms\resources\assets\js\pages\ppc\masters\dropdown-master\dropdown-master.js */"./resources/assets/js/pages/ppc/masters/dropdown-master/dropdown-master.js");


/***/ })

/******/ });