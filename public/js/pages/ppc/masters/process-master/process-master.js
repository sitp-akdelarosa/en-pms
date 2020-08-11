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
/******/ 	return __webpack_require__(__webpack_require__.s = 12);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/assets/js/pages/ppc/masters/process-master/process-master.js":
/*!********************************************************************************!*\
  !*** ./resources/assets/js/pages/ppc/masters/process-master/process-master.js ***!
  \********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

var process_select_arr = [];
var selected_process_arr = [];
var setArray = [];
$(function () {
  //$('.select2').select2();
  // $('#set_list').slimscroll({ height: '100px',width: '100%' });
  get_set();
  processSelection(); // get_dropdown_items_by_id(2,'#set');

  checkAllCheckboxesInTable('#tbl_added_sets', '.check_all_sets', '.check_set', '#btn_delete_set');
  checkAllCheckboxesInTable('#tbl_selected_process', '.check_all', '.check_item');
  init(); //selectedProcess('Default');

  $('#btn_add_process').on('click', function () {
    var setTable = $('#tbl_added_sets').DataTable();
    setArray = [];

    for (var x = 0; x < setTable.context[0].aoData.length; x++) {
      var cell = setTable.context[0].aoData[x].anCells;

      if (cell !== null && cell[0].firstChild.checked == true) {
        setArray.push(parseInt(cell[0].firstChild.value));
      }
    }

    var processTable = $('#tbl_select_process').DataTable();
    var chkArray = [];

    for (var x = 0; x < processTable.context[0].aoData.length; x++) {
      var cell = processTable.context[0].aoData[x].anCells;

      if (cell !== null && cell[0].firstChild.checked == true) {
        chkArray.push(cell[0].firstChild.value);
      }
    } // var chkArray = [];
    // $('.check_item:checked').each(function() {
    // 	chkArray.push($(this).val());
    // });


    var ExistProcess = 1;

    if (chkArray.length > 0) {
      $.each(chkArray, function (i, x) {
        $.each(selected_process_arr, function (ii, xx) {
          if (xx.process == x) {
            ExistProcess = 0;
          }
        });
      });

      if (ExistProcess == 1) {
        if ($('#state').val() !== 'edit') {
          $('#selected_set').val(setArray).trigger('change');

          if ($('#state').val() == 'add') {
            $('#selected_set').prop('readonly', true);
          }
        } else {}

        var cnt = selected_process_arr.length;
        $.each(chkArray, function (i, x) {
          cnt++;
          selected_process_arr.push({
            count: cnt,
            sequence: cnt,
            process: x
          });
        });
        selectedProcessTable(selected_process_arr);
        $('#selected_set_feedback').html("Reminder: If one of the selected set has saved processes, the saved processes will be overight with selected processes below.");
      } else {
        msg("The Process already existing on the Process Table.", "failed");
      }
    } else {
      msg("Please select at least 1 item.", "failed");
    }

    $('.check_all').prop('checked', false);
  });
  $('#btn_save_process').on('click', function () {
    var selectedSet = $('#selected_set').val();

    if (selectedSet.length > 0 && selected_process_arr.length > 0) {
      $('.loadingOverlay').show();
      $.ajax({
        url: saveProcessURL,
        type: 'POST',
        dataType: 'JSON',
        data: {
          _token: token,
          set_id: selectedSet,
          //sets: getSelectedText('selected_set'),
          processes: selected_process_arr
        }
      }).done(function (data, textStatus, xhr) {
        msg(data.msg, data.status); // $('.check_item').prop('checked', false);
        // $('.check_all').prop('checked', false);

        $('#btn_cancel').click();
      }).fail(function (xhr, textStatus, errorThrown) {
        msg(errorThrown, textStatus);
        $('.check_item').prop('checked', false);
        selected_process_arr = [];
        $('#tbl_selected_process_body').html('<tr>' + '<td colspan="3" class="text-center">No data available.</td>' + '</tr>');
      }).always(function () {
        $('.loadingOverlay').hide();
      });
    } else {
      console.log($('#selected_set').val());
      msg("Please select at least 1 process and select your desired set.", 'failed');
    }
  }); // $('#selected_set').on('click', function() {
  // 	selectedProcess($(this).val());
  // });

  $('#tbl_selected_process_body').on('click', '.delete', function () {
    var id = $(this).attr('data-count');
    id--;
    selected_process_arr.splice(id, 1);
    var data = [];
    var cnt = 1;
    $.each(selected_process_arr, function (i, x) {
      data.push({
        count: cnt,
        sequence: cnt,
        process: x.process
      });
      cnt++;
    });
    selected_process_arr = [];
    selected_process_arr = data;
    selectedProcessTable(selected_process_arr);

    if ($('#tbl_selected_process_body > tr').length < 1) {
      $('#tbl_selected_process_body').html('<tr id="no_data">' + '<td colspan="4" class="text-center">No data available.</td>' + '</tr>');
    }
  });
  $('#frm_add_set').on('submit', function (e) {
    e.preventDefault();
    $('.loadingOverlay').show();
    $.ajax({
      url: $(this).attr('action'),
      type: 'POST',
      dataType: 'JSON',
      data: $(this).serialize()
    }).done(function (data, textStatus, xhr) {
      msg(data.msg, data.status);
      $('#set').val('');
      get_set();
    }).fail(function (xhr, textStatus, errorThrown) {
      var errors = xhr.responseJSON.errors;
      showErrors(errors);
    }).always(function () {
      $('.loadingOverlay').hide();
    });
  });
  $('#btn_delete_set').on('click', function () {
    delete_set();
  });
  $('#tbl_added_sets').on('change', '.check_all_sets', function () {
    $('input:checkbox.check_set').not(this).prop('checked', this.checked);
    var table = $('#tbl_added_sets').DataTable();

    for (var x = 0; x < table.context[0].aoData.length; x++) {
      var aoData = table.context[0].aoData[x];
      var tr = aoData.nTr;

      if (aoData.anCells !== null && aoData.anCells[0].firstChild.checked == true) {
        console.log(aoData.anCells[0].firstChild.attributes);
        $(tr).addClass('selected');
        GUIState('add');
      } else {
        $(tr).removeClass('selected');
        GUIState('view');
      }
    }
  });
  $('#tbl_added_sets_body').on('change', '.check_set', function () {
    var tr = $(this).parent().parent()[0];

    if ($(this).is(':checked')) {
      $(tr).addClass('selected');
      $('#btn_delete_set').prop('disabled', false);
      GUIState('add');
    } else {
      $(tr).removeClass('selected');
      $('#btn_delete_set').prop('disabled', true);
      GUIState('view');
    }
  });
  $('#tbl_added_sets_body').on('click', '.btn_edit_set', function () {
    GUIState('edit');
    selectedProcess($(this).attr('data-id'));
    $('#selected_set').val([$(this).attr('data-id')]).trigger('change');
  });
  $('#btn_cancel').on('click', function () {
    GUIState('view');
    $('.check_all_sets').prop('checked', false).trigger('change');
    $('.check_set').prop('checked', false).trigger('change');
    $('.check_all').prop('checked', false).trigger('change');
    $('.check_item').prop('checked', false).trigger('change');
    $('#selected_set').val([]).trigger('change');
    selected_process_arr = [];
    selectedProcessTable(selected_process_arr);
  });
});

function init() {
  check_permission(code_permission, function (output) {
    if (output == 1) {
      $('#btn_delete_set').prop('disabled', true);
    }
  });
  GUIState('view');
}

function GUIState(state) {
  switch (state) {
    case 'view':
      $('.check_all_sets').prop('disabled', false);
      $('#tbl_added_sets').DataTable().$('.check_set').prop('disabled', false);
      $('.page-link').prop('disabled', true);
      $('.check_all').prop('disabled', true);
      $('#tbl_select_process').DataTable().$('.check_item').prop('disabled', true); //$('#tbl_select_process .check_item').prop('disabled', true);

      $('#btn_cancel').hide();
      $('#state').val(state);
      $("#selected_set").select2({
        disabled: 'readonly'
      });
      break;

    case 'add':
      $('.check_all').prop('disabled', false);
      $('#tbl_select_process').DataTable().$('.check_item').prop('disabled', false); //$('#tbl_select_process .check_item').prop('disabled', false);

      $('#btn_cancel').show();
      $('#state').val(state);
      break;

    case 'edit':
      $('.check_all_sets').prop('disabled', true);
      $('#tbl_added_sets').DataTable().$('.check_set').prop('disabled', true); //$('.check_set').prop('disabled', true);

      $('.check_all').prop('disabled', false);
      $('#tbl_select_process').DataTable().$('.check_item').prop('disabled', false); //$('#tbl_select_process .check_item').prop('disabled', true);

      $('#btn_cancel').show();
      $('#state').val(state);
      break;
  }
}

function processSelection() {
  process_select_arr = [];
  $.ajax({
    url: processListURL,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token
    }
  }).done(function (data, textStatus, xhr) {
    process_select_arr = data;
    makeProcessesTable(process_select_arr);
  }).fail(function (xhr, textStatus, errorThrown) {
    msg(errorThrown, textStatus);
  });
}

function makeProcessesTable(arr) {
  $('#tbl_select_process').dataTable().fnClearTable();
  $('#tbl_select_process').dataTable().fnDestroy();
  $('#tbl_select_process').dataTable({
    data: arr,
    bLengthChange: false,
    //scrollY: "300px",
    searching: false,
    pageLength: 10,
    //paging: false,
    order: [[1, 'asc']],
    columns: [{
      data: function data(x) {
        return '<input type="checkbox" class="table-checkbox check_item" value="' + x.process + '">';
      },
      searchable: false,
      orderable: false
    }, {
      data: function data(x) {
        return x.process + "<input type='hidden' name='process[]' value='" + x.process + "'>";
      }
    }],
    fnInitComplete: function fnInitComplete() {
      GUIState('view');
      $('.dataTables_scrollBody').slimscroll();
    }
  });
}

function selectedProcess(set_id) {
  selected_process_arr = [];
  $.ajax({
    url: selectedProcessListURL,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token,
      set_id: set_id
    }
  }).done(function (data, textStatus, xhr) {
    var cnt = 1;
    $.each(data, function (i, x) {
      selected_process_arr.push({
        count: cnt,
        sequence: x.sequence,
        process: x.process
      });
      cnt++;
    });
    $('#selected_set').val(set_id);
    selectedProcessTable(selected_process_arr);
  }).fail(function (xhr, textStatus, errorThrown) {
    msg(errorThrown, textStatus);
  });
}

function selectedProcessTable(arr) {
  $('#tbl_selected_process_body').html('');
  $('#tbl_selected_process').dataTable().fnClearTable();
  $('#tbl_selected_process').dataTable().fnDestroy();
  $('#tbl_selected_process').dataTable({
    data: arr,
    bLengthChange: false,
    searching: false,
    paging: false,
    order: [[1, 'asc']],
    columns: [{
      data: function data() {
        return '<i class="text-blue fa fa-arrows"></i>';
      }
    }, {
      data: 'sequence'
    }, {
      data: 'process'
    }, {
      data: function data(x) {
        return '<i class="text-red fa fa-times delete" data-count="' + x.count + '"></i>';
      }
    }],
    rowReorder: {
      dataSrc: 'process'
    }
  });
}

function get_set() {
  var set = '<option value=""></option>'; // var set_list = '';

  $('#selected_set').html(set); // $('#set_list').html(set_list);

  $.ajax({
    url: getSetURL,
    type: 'GET',
    dataType: 'JSON',
    data: {
      _token: token
    }
  }).done(function (data, textStatus, xhr) {
    console.log(data);
    $('#selected_set').select2({
      data: data
    });
    setTable(data); // $.each(data, function(i, x) {
    // 	set = '<option value="'+x.id+'">'+x.set+'</option>';
    // 	$('#selected_set').append(set);
    // 	set_list = '<h6>'+
    // 					'<input type="checkbox" class="set_item" value="'+x.id+'">'+x.set+
    // 				'</h6>';
    // 	$('#set_list').append(set_list);
    // });
  }).fail(function (xhr, textStatus, errorThrown) {
    msg(errorThrown, textStatus);
  });
}

function setTable(dataArr) {
  var table = $('#tbl_added_sets');
  table.dataTable().fnClearTable();
  table.dataTable().fnDestroy();
  table.dataTable({
    data: dataArr,
    processing: true,
    deferRender: true,
    searching: false,
    pageLength: 10,
    bLengthChange: false,
    order: [[1, 'desc']],
    columns: [{
      data: function data(x) {
        return "<input type='checkbox' class='table-checkbox check_set' value='" + x.id + "'>";
      },
      name: 'id',
      orderable: false,
      searchable: false
    }, {
      data: 'text',
      name: 'text'
    }, //set
    {
      data: function data(x) {
        return '<button type="button" class="btn btn-sm btn-blue btn_edit_set" data-id="' + x.id + '">' + '<i class="fa fa-edit"></i>' + '</button>';
      },
      orderable: false,
      searchable: false
    }],
    initComplete: function initComplete() {
      $('.loadingOverlay').hide();
    },
    fnDrawCallback: function fnDrawCallback() {}
  });
}

function delete_set() {
  var chkArray = [];
  var table = $('#tbl_added_sets').DataTable();

  for (var x = 0; x < table.context[0].aoData.length; x++) {
    var aoData = table.context[0].aoData[x];

    if (aoData.anCells !== null && aoData.anCells[0].firstChild.checked == true) {
      chkArray.push(table.context[0].aoData[x].anCells[0].firstChild.value);
    }
  } // $(".set_item:checked").each(function() {
  // 	chkArray.push($(this).val());
  // });


  if (chkArray.length > 0) {
    swal({
      title: "Are you sure?",
      text: "You will not be able to recover your data!",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#f95454",
      confirmButtonText: "Yes",
      cancelButtonText: "No",
      closeOnConfirm: true,
      closeOnCancel: false
    }, function (isConfirm) {
      if (isConfirm) {
        $.ajax({
          url: deleteSetURL,
          type: 'POST',
          dataType: 'JSON',
          data: {
            _token: token,
            id: chkArray
          }
        }).done(function (data, textStatus, xhr) {
          msg(data.msg, data.status);
          selected_process_arr = [];
          selectedProcessTable(selected_process_arr);
          get_set();
        }).fail(function (xhr, textStatus, errorThrown) {
          msg(errorThrown, textStatus);
        });
        $('.check_all_product').prop('checked', false);
      } else {
        swal("Cancelled", "Your data is safe and not deleted.");
      }
    });
  } else {
    msg("Please select at least 1 Set.", "failed");
  }
}

/***/ }),

/***/ 12:
/*!**************************************************************************************!*\
  !*** multi ./resources/assets/js/pages/ppc/masters/process-master/process-master.js ***!
  \**************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\laragon\www\en-pms\resources\assets\js\pages\ppc\masters\process-master\process-master.js */"./resources/assets/js/pages/ppc/masters/process-master/process-master.js");


/***/ })

/******/ });