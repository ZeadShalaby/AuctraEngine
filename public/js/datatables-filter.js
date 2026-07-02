$(document).on('click', '#filter', function () {

    $.each(window.LaravelDataTables, function (key, table) {

        table.ajax.reload();

    });

});

$(document).on('click', '#reset', function () {

    $('#from_date').val('');
    $('#to_date').val('');

    $.each(window.LaravelDataTables, function (key, table) {

        table.ajax.reload();

    });

});

$(document).on('preXhr.dt', '.dataTable', function (e, settings, data) {

    data.from_date = $('#from_date').val();
    data.to_date = $('#to_date').val();

});