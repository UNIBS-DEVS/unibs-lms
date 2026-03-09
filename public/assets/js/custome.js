$(document).ready(function () {

    let table = $('#listTable').DataTable({
        pageLength: 5,
        lengthMenu: [5, 10, 25, 50],
        pagingType: "simple_numbers",
        columnDefs: [{
            orderable: false,
            searchable: false,
        }],
        language: {
            emptyTable: "No Users found!",
            search: "",
            searchPlaceholder: "Search...",
            lengthMenu: "Show _MENU_",
            paginate: {
                first: '<i class="fa fa-angle-double-left"></i>',
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>',
                last: '<i class="fa fa-angle-double-right"></i>'
            }
        },
        drawCallback: function () {
            let api = this.api();

            if (api.data().count() === 0) {
                $('.dataTables_paginate').hide();
                $('.dataTables_info').hide();
            } else {
                $('.dataTables_paginate').show();
                $('.dataTables_info').show();
            }
        }
    });

    // Column search + clear button logic
    $('#listTable thead tr.filter-row th').each(function (index) {

        let input = $('input', this);
        let clearBtn = $('.clear-input', this);

        input.on('keyup change', function () {
            table.column(index).search(this.value).draw();

            if (this.value.length > 0) {
                clearBtn.show();
            } else {
                clearBtn.hide();
            }
        });

        clearBtn.on('click', function (e) {
            e.preventDefault();
            input.val('');
            clearBtn.hide();
            table.column(index).search('').draw();
            input.focus();
        });
    });

});