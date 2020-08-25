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
/******/ 	return __webpack_require__(__webpack_require__.s = 15);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/assets/js/pages/ppc/transactions/update-inventory/update-inventory.js":
/*!*****************************************************************************************!*\
  !*** ./resources/assets/js/pages/ppc/transactions/update-inventory/update-inventory.js ***!
  \*****************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

var _with_zero = 0;
$(function () {
  getMaterials();
  getInventory(_with_zero);
  init();
  $("#materials_type").on('change', function () {
    getMaterialCode();
  });
  $("#materials_code").on('change', function () {
    GetMaterialCodeDetails();
  });
  $('.custom-file-input').on('change', function () {
    var fileName = $(this).val().split('\\').pop();
    $(this).next('.custom-file-label').addClass("selected").html(fileName);
  });
  $('#btn_add').on('click', function () {
    var d = new Date();
    var month = d.getMonth() + 1;
    var day = d.getDate();
    var date = d.getFullYear() + '-' + (month < 10 ? '0' : '') + month + '-' + (day < 10 ? '0' : '') + day;
    $('#received_date').val(date);
    $('#modal_material_inventory').modal('show');
    clear();
    getMaterialCode();
  });
  $('#btn_zero').on('click', function () {
    if (_with_zero == 0) {
      _with_zero = 1;
      getInventory(_with_zero);
      $(this).removeClass('bg-blue');
      $(this).addClass('bg-red');
      $(this).html('Exclude 0 quantity');
    } else {
      _with_zero = 0;
      getInventory(_with_zero);
      $(this).removeClass('bg-red');
      $(this).addClass('bg-blue');
      $(this).html('Include 0 quantity');
    }
  });
  $('#tbl_materials_body').on('click', '.edit-mainEdit', function (e) {
    e.preventDefault();
    $('#material_id').val($(this).attr('data-id'));
    $('#materials_type').val($(this).attr('data-materials_type'));

    if ($('#materials_type').val() != '') {
      getMaterialCode($(this).attr('data-materials_code'));
    }

    $('#description').val($(this).attr('data-description'));
    $('#item').val($(this).attr('data-item'));
    $('#alloy').val($(this).attr('data-alloy'));
    $('#schedule').val($(this).attr('data-schedule'));
    $('#size').val($(this).attr('data-size'));
    $('#width').val($(this).attr('data-width'));
    $('#length').val($(this).attr('data-length'));
    $('#quantity').val($(this).attr('data-quantity'));
    $('#uom').val($(this).attr('data-uom'));
    $('#heat_no').val($(this).attr('data-heat_no'));
    $('#invoice_no').val($(this).attr('data-invoice_no'));
    $('#received_date').val($(this).attr('data-received_date'));
    $('#supplier').val($(this).attr('data-supplier'));
    $('#supplier_heat_no').val($(this).attr('data-supplier_heat_no'));
    $('#modal_material_inventory').modal('show');
  });
  $('#frm_update_inventory').on('submit', function (e) {
    var formObj = $('#frm_update_inventory');
    var formURL = formObj.attr("action");
    var formData = new FormData(this);
    var fileName = $("#file_inventory").val();
    var ext = fileName.split('.').pop();
    var pros = $('#file_inventory').val().replace("C:\fakepath", "");
    var fileN = pros.substring(12, pros.length);
    e.preventDefault(); //Prevent Default action.

    if ($("#file_inventory").val() == '') {
      msg("No File", "failed");
    } else {
      $('.loadingOverlay').show();

      if (fileName != '') {
        if (ext == 'xls' || ext == 'xlsx' || ext == 'XLS' || ext == 'XLSX') {
          $('.myprogress').css('width', '0%');
          $('#progress-msg').html('Uploading in progress...');
          var percent = 0;
          $.ajax({
            url: checkfile,
            type: 'POST',
            mimeType: "multipart/form-data",
            contentType: false,
            cache: false,
            processData: false,
            data: formData,
            success: function success(returns) {
              var return_datas = jQuery.parseJSON(returns);

              if (return_datas.status == "success") {
                $.ajax({
                  url: uploadInventory,
                  type: 'POST',
                  data: formData,
                  mimeType: "multipart/form-data",
                  contentType: false,
                  cache: false,
                  processData: false,
                  success: function success(returnData) {
                    $('.loadingOverlay').hide();
                    var return_data = jQuery.parseJSON(returnData);
                    msg(return_data.msg, return_data.status);
                    document.getElementById('file_inventory_label').innerHTML = fileN;
                    getInventory(_with_zero);
                    var not_registedred = return_data.Material;

                    if (not_registedred.length > 0) {
                      GetMateriialTypeNotExisting(not_registedred);
                    }
                  }
                });
              } else if (return_datas.status == "validateRequired") {
                $('.loadingOverlay').hide();
                msg("Fill up correctly the record in line " + return_datas.line, "warning");
                document.getElementById('file_inventory_label').innerHTML = "Select file...";
              } else if (return_datas.status == "heatnumber error") {
                $('.loadingOverlay').hide();
                msg(return_datas.msg, "warning");
                document.getElementById('file_inventory_label').innerHTML = "Select file...";
              } else if (return_datas.status == "not num") {
                $('.loadingOverlay').hide();
                msg("Invalid input of Quantity", "warning");
                document.getElementById('file_inventory_label').innerHTML = "Select file...";
              } else if (return_datas.status == "failed") {
                $('.loadingOverlay').hide();
                console.log(return_datas.fields);
                msg("Please maintain data as 1 sheet only.", "warning");
                document.getElementById('file_inventory_label').innerHTML = "Select file...";
              } else {
                $('.loadingOverlay').hide();
                msg("Upload failed", "warning");
                document.getElementById('file_inventory_label').innerHTML = "Select file...";
              }
            }
          });
        } else {
          $('.loadingOverlay').hide();
          msg("File Format not supported.", "warning");
        }
      }
    }
  });
  $("#frm_material_inventory").on('submit', function (e) {
    e.preventDefault();

    if ($('#quantity').val() < 0 || $('#quantity').val() == 0) {
      msg('Please Input valid number', 'warning');
    } else {
      $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        dataType: 'JSON',
        data: $(this).serialize()
      }).done(function (data, textStatus, xhr) {
        msg(data.msg, data.status);
        getInventory(_with_zero);
      }).fail(function (xhr, textStatus, errorThrown) {
        var errors = xhr.responseJSON.errors;
        showErrors(errors);
      });
    }
  });
  $('#btn_excel').on('click', function () {
    window.location.href = downloadNonexistingURL;
  });
  $('#btn_check_unregistered').on('click', function (event) {
    $.ajax({
      url: getNonexistingURL,
      type: 'GET',
      dataType: 'JSON'
    }).done(function (data, textStatus, xhr) {
      GetMateriialTypeNotExisting(data);
    }).fail(function (xhr, textStatus, errorThrown) {
      msg('Unregistered Products: ' + errorThrown);
    });
  });
});

function init() {
  check_permission(code_permission, function (output) {
    if (output == 1) {}
  });
}

function getMaterials() {
  $.ajax({
    url: materialTypeURL,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token
    }
  }).done(function (data, textStatus, xhr) {
    var code = '<option></option>';
    $('#materials_type').html(code);
    $.each(data.type, function (i, x) {
      code = '<option value="' + x.material_type + '">' + x.material_type + '</option>';
      $('#materials_type').append(code);
    });
  }).fail(function (xhr, textStatus, errorThrown) {
    msg(errorThrown, textStatus);
  });
}

function getMaterialCode(mat_code) {
  var code = "<option value=''></option>";
  $('#materials_code').html(code);
  $.ajax({
    url: GetMaterialCode,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token,
      mat_type: $('#materials_type').val()
    }
  }).done(function (data, textStatus, xhr) {
    $.each(data.code, function (i, x) {
      code = "<option value='" + x.material_code + "'>" + x.material_code + "</option>";
      $('#materials_code').append(code);
    });
    $('#materials_code').val(mat_code);
  }).fail(function (xhr, textStatus, errorThrown) {
    msg(errorThrown, textStatus);
  });
}

function GetMaterialCodeDetails() {
  $.ajax({
    url: GetMaterialCodeDetailsurl,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token,
      mat_code: $('#materials_code').val()
    }
  }).done(function (data, textStatus, xhr) {
    // if (data.length > 0) {
    $('#description').val(data.code_description);
    $('#item').val(data.item);
    $('#alloy').val(data.alloy);
    $('#schedule').val(data.schedule);
    $('#size').val(data.size); // } else {
    // 	msg("Material Code is not registered");
    // }
  }).fail(function (xhr, textStatus, errorThrown) {
    msg(errorThrown, textStatus);
  });
}

function GetMateriialTypeNotExisting(arr) {
  $('#modal_material_not_existing').modal('show');
  $('#tbl_material_not_existing').dataTable().fnClearTable();
  $('#tbl_material_not_existing').dataTable().fnDestroy();
  $('#tbl_material_not_existing').dataTable({
    data: arr,
    bLengthChange: false,
    paging: true,
    columns: [{
      data: 'materials_code',
      name: 'materials_code'
    }, {
      data: 'quantity',
      name: 'quantity'
    }, {
      data: 'uom',
      name: 'uom'
    }, {
      data: 'heat_no',
      name: 'heat_no'
    }, {
      data: 'invoice_no',
      name: 'invoice_no'
    }, {
      data: 'received_date',
      name: 'received_date'
    }, {
      data: 'supplier',
      name: 'supplier'
    }]
  });
}

function getInventory(with_zero) {
  $.ajax({
    url: materialDataTable,
    type: 'GET',
    dataType: 'JSON',
    data: {
      with_zero: with_zero
    }
  }).done(function (data, textStatus, xhr) {
    InventoryTable(data);
  }).fail(function (xhr, textStatus, errorThrown) {
    console.log("error");
  });
}

function clear() {
  $('.clear').val('');
}

function InventoryTable(arr) {
  $('#tbl_materials').dataTable().fnClearTable();
  $('#tbl_materials').dataTable().fnDestroy();
  $('#tbl_materials').dataTable({
    data: arr,
    columns: [{
      data: function data(_data) {
        return "<button type='button' name='edit-mainEdit' class='btn btn-sm btn-primary edit-mainEdit'" + "id='editinventory'" + "data-id= '" + _data.id + "' " + "data-materials_type='" + _data.materials_type + "' " + "data-materials_code='" + _data.materials_code + "'" + "data-description='" + _data.description + "'" + "data-item='" + _data.item + "'" + "data-alloy='" + _data.alloy + "'" + "data-schedule='" + _data.schedule + "'" + "data-size='" + _data.size + "'" + "data-quantity='" + _data.quantity + "'" + "data-uom='" + _data.uom + "'" + "data-heat_no='" + _data.heat_no + "' " + "data-invoice_no='" + _data.invoice_no + "'" + "data-received_date='" + _data.received_date + "'" + "data-width='" + _data.width + "' " + "data-length='" + _data.length + "' " + "data-supplier_heat_no='" + _data.supplier_heat_no + "' " + "data-supplier='" + _data.supplier + "'>" + "<i class='fa fa-edit'></i> " + "</button>";
      },
      searchable: false,
      orderable: false
    }, {
      data: 'materials_type'
    }, {
      data: 'materials_code'
    }, {
      data: 'description'
    }, {
      data: 'item'
    }, {
      data: 'alloy'
    }, {
      data: 'schedule'
    }, {
      data: 'size'
    }, {
      data: 'width'
    }, {
      data: 'length'
    }, // {
    // 	data: function (data) {
    // 		var wxl = $.trim(data.wxl);
    // 		var w = $.trim(data.width);
    // 		var l = $.trim(data.length);
    // 		if (w != "" && l == "") {
    // 			return data.width
    // 		} else if (w == "" && l != "") {
    // 			return data.length
    // 		} else {
    // 			if (wxl == "x") {
    // 				return "";
    // 			} else {
    // 				return data.wxl;
    // 			}
    // 		}
    // 	}
    // },
    {
      data: 'quantity'
    }, {
      data: 'uom'
    }, {
      data: 'heat_no'
    }, {
      data: 'invoice_no'
    }, {
      data: 'received_date'
    }, {
      data: 'supplier'
    }, {
      data: 'supplier_heat_no'
    }],
    createdRow: function createdRow(row, data, dataIndex) {
      if (data.description == "N/A") {
        $(row).css('background-color', '#ff6266');
        $(row).css('color', '#fff');
      }
    }
  });
}

/***/ }),

/***/ 15:
/*!***********************************************************************************************!*\
  !*** multi ./resources/assets/js/pages/ppc/transactions/update-inventory/update-inventory.js ***!
  \***********************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\laragon\www\en-pms\resources\assets\js\pages\ppc\transactions\update-inventory\update-inventory.js */"./resources/assets/js/pages/ppc/transactions/update-inventory/update-inventory.js");


/***/ })

/******/ });