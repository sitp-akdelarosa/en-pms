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
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
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
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 51);
/******/ })
/************************************************************************/
/******/ ({

/***/ 51:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(52);


/***/ }),

/***/ 52:
/***/ (function(module, exports) {

var dataColumn = [{ data: function data(_data) {
		return '<input type="checkbox" class="table-checkbox check_item" value="' + _data.id + '">';
	}, name: 'a.id', orderable: false, searchable: false }, { data: 'action', name: 'action', orderable: false, searchable: false }, { data: function data(_data2) {
		return _data2.firstname + ' ' + _data2.lastname;
	}, name: 'u.firstname' }, { data: 'product_line', name: 'a.product_line' }, { data: 'updated_at', name: 'a.updated_at' }];

$(function () {
	get_dropdown_items_by_id(1, '#product_line');
	get_users();
	checkAllCheckboxesInTable('.check_all', '.check_item');
	getDatatable('tbl_assign_productline', prodLineListURL, dataColumn, [], 0);

	$('.select-validate').on('change', function (e) {
		var no_error = $(this).attr('id');
		hideErrors(no_error);
	});

	$('#frm_assign_productline').on('submit', function (e) {
		e.preventDefault();
		$.ajax({
			url: $(this).attr('action'),
			type: 'POST',
			dataType: 'JSON',
			data: $(this).serialize()
		}).done(function (data, textStatus, xhr) {
			if (textStatus) {
				msg("User was successfully assigned.", textStatus);
				getDatatable('tbl_assign_productline', prodLineListURL, dataColumn, [], 0);
			}
			clear();
			$('#btn_save').html('<i class="fa fa-plus"></i> Add');
		}).fail(function (xhr, textStatus, errorThrown) {
			var errors = xhr.responseJSON.errors;
			showErrors(errors);
		}).always(function (xhr, textStatus) {
			console.log("complete");
		});
	});

	$('#tbl_assign_productline_body').on('click', '.btn_edit_prodline', function (e) {
		e.preventDefault();
		$('#id').val($(this).attr('data-id'));
		$('#user_id').val($(this).attr('data-user_id'));
		$('#product_line').val($(this).attr('data-product_line'));
		$('#btn_save').html('<i class="fa fa-check"></i> Update');
	});

	$('#btn_clear').on('click', function (e) {
		clear();
	});

	$('#btn_delete').on('click', function (e) {
		delete_items('.check_item', prodLineDeleteURL);
	});
});

function get_users() {
	var opt = '<option value=""></option>';
	$('#user_id').html(opt);
	$.ajax({
		url: getUserURL,
		type: 'GET',
		dataType: 'JSON',
		data: { _token: token }
	}).done(function (data, textStatus, xhr) {
		$.each(data, function (i, x) {
			opt = '<option value="' + x.id + '">' + x.name + '</option>';
			$('#user_id').append(opt);
		});
	}).fail(function (xhr, textStatus, errorThrown) {
		console.log("error");
	});
}

function clear() {
	$('.clear').val('');
}

function delete_items(checkboxClass, deleteURL) {
	var chkArray = [];
	$(checkboxClass + ":checked").each(function () {
		chkArray.push($(this).val());
	});

	if (chkArray.length > 0) {
		confirm_delete(chkArray, token, deleteURL, true, 'tbl_assign_productline', prodLineListURL, dataColumn);
	} else {
		msg("Please select at least 1 item.");
	}

	$('.check_all').prop('checked', false);
	clear();
	$('#btn_save').html('<i class="fa fa-plus"></i> Add');
}

/***/ })

/******/ });