/**
 * Reporting Module - Handles all reporting functionality
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialize variables
    let from_date = '';
    let to_date = '';
    
    // Initialize the reporting functionality
    initReportingFunctionality();
    
    /**
     * Initialize all reporting functionality
     */
    function initReportingFunctionality() {
        // Form submission handler
        initFormSubmission();
        
        // Delete report handler
        initDeleteReport();
        
        // Edit report handler
        initEditReport();
        
        // Input validation
        initInputValidation();
        
        // Load report data
        loadReportData();
        
        // Date filter handlers
        initDateFilters();
        
        // Modal reset handler
        initModalReset();
    }
    
    /**
     * Initialize form submission
     */
    function initFormSubmission() {
        $('#reportForm').on('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission
            showLoading();
            let formData = new FormData(this);
            $('.report-form-submit').prop('disabled', true);
            
            fetch(reportStoreRoute, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
            })
            .then(response => {
                // Check if the response is in JSON format
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    // Handle non-JSON response (e.g., HTML error page)
                    return response.text().then(text => { throw new Error('Invalid JSON: ' + text); });
                }
            })
            .then(data => {
                if (data.success) {
                    $('#report-input-modal').modal('hide');
                    $('#closeModalbtn').click();
                    $('#reportForm')[0].reset();
                    $('.report-form-submit').prop('disabled', false);
                    hideLoading();
                    $('#reports-table').DataTable().ajax.reload();
                    
                    // Show success message
                    Swal.fire({
                        title: 'Success!',
                        text: 'Report has been saved successfully.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                } else {
                    // Handle validation errors
                    console.log(data.errors);
                    $('.report-form-submit').prop('disabled', false);
                    hideLoading();
                    
                    // Show error message
                    Swal.fire({
                        title: 'Error!',
                        text: 'There was an error saving the report.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error: ', error);
                hideLoading();
                $('.report-form-submit').prop('disabled', false);
                
                // Show error message
                Swal.fire({
                    title: 'Error!',
                    text: 'There was an error processing your request.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        });
    }
    
    /**
     * Initialize delete report functionality
     */
    function initDeleteReport() {
        $(document).on('click', '.deleteReport', function (e) {
            e.preventDefault();
            let reportId = $(this).data('id');
            let url = '/report/' + reportId;
            
            Swal.fire({
                title: "Want to Delete?",
                text: "This will delete the selected Report!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!",
                reverseButtons: true,
                customClass: {
                    confirmButton: 'btn btn-danger ml-3',
                    cancelButton: 'btn btn-secondary mr-2'
                },
                buttonsStyling: false
            })
            .then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            _token: csrfToken
                        },
                        beforeSend: function(){
                            showLoading();
                        },
                        success: function (response) {
                            $('#reports-table').DataTable().ajax.reload();
                            hideLoading();
                            
                            // Show success message
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Report has been deleted successfully.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            });
                        },
                        error: function (xhr) {
                            hideLoading();
                            Swal.fire({
                                title: 'Error!',
                                text: 'There was an issue deleting the report.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: 'Cancelled',
                        text: 'Your report is safe :)',
                        icon: 'info',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    }
    
    /**
     * Initialize edit report functionality
     */
    function initEditReport() {
        $(document).on("click", ".editReport", function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            var userRole = userRoleGlobal;
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                url: reportUpdateRoute,
                method: 'patch',
                data: {id: id},
                beforeSend: function(){
                    showLoading();
                },
                success: function (response){
                    hideLoading();
                    var report = response[1];
                    $("#report-input-modal").modal('show');
                    
                    // Set values using .val() for input fields
                    $("#id").val(report['id']);
                    if(userRole != 'user')
                        $('#user_id').val(report['user_id']);
                    $("#project_id").val(report['project_id']);
                    $("#task_tested").val(report['task_tested']);
                    $("#bug_reported").val(report['bug_reported']);
                    $("#other").val(report['other']);
                    $("#description").val(report['description']);
                    
                    // Set values for checkboxes using .prop()
                    $("#regression").prop('checked', report['regression']);
                    $("#smoke_testing").prop('checked', report['smoke_testing']);
                    $("#client_meeting").prop('checked', report['client_meeting']);
                    $("#daily_meeting").prop('checked', report['daily_meeting']);
                    $("#mobile_testing").prop('checked', report['mobile_testing']);
                    $("#automation").prop('checked', report['automation']);
                },
                error: function(xhr, status){
                    console.log("Error " + status);
                    hideLoading();
                    
                    // Show error message
                    Swal.fire({
                        title: 'Error!',
                        text: 'There was an error loading the report data.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    }
    
    /**
     * Initialize input validation
     */
    function initInputValidation() {
        $('#task_tested, #bug_reported').on('input', function() {
            let value = $(this).val();
            // Allow only positive integers
            if (value < 0) {
                $(this).val('');
            } else {
                $(this).val(Math.floor(value)); // Ensure it's an integer
            }
        });
    }
    
    /**
     * Load report data into DataTable
     */
    function loadReportData() {
        if ($.fn.DataTable.isDataTable('#reports-table')) {
            $('#reports-table').DataTable().clear().destroy();
        }
        
        $('#reports-table').DataTable({
            serverSide: true,
            dom: '<"top" f> rtlp',
            scrollX: true,
            scrollY: '60vh',
            scrollCollapse: true,
            paging: true,
            ajax: {
                url: reportsDataRoute,
                data: function (d) {
                    d.from_date = from_date;
                    d.to_date = to_date;
                    d.user_id = $('#user-name').val();
                    d.project_id = $('#project-name').val();
                },
                beforeSend: function(){
                    showLoading(true);
                },
                complete: function(){
                    hideLoading();
                }
            },
            columns: [
                { data: 'date', name: 'date' },
                { data: 'user_name', name: 'user_name', orderable: false, searchable: true },
                { data: 'project_name', name: 'project_name', orderable: false, searchable: true },
                { data: 'task_tested', name: 'task_tested' },
                { data: 'bug_reported', name: 'bug_reported' },
                { data: 'regression', name: 'regression' },
                { data: 'smoke_testing', name: 'smoke_testing' },
                { data: 'client_meeting', name: 'client_meeting' },
                { data: 'daily_meeting', name: 'daily_meeting' },
                { data: 'mobile_testing', name: 'mobile_testing' },
                { data: 'automation', name: 'automation' },
                { data: 'other', name: 'other' },
                { data: 'description', name: 'description' },
                { data: 'action', name: 'action' },
            ],
            columnDefs: [
                {
                    targets: [0, 12],
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).addClass('column-width-height');
                    }
                },
            ],
            drawCallback: function() {
                var api = this.api();
                
                function sumColumn(columnIndex) {
                    var total = 0;
                    if(api.column(columnIndex).data().length > 0){
                        api.column(columnIndex).data().each(function(value) {
                            var numericValue = parseFloat(value) || 0;
                            total += numericValue;
                        });
                        return total;
                    }
                }
                
                const totalTasks = sumColumn(3);
                const totalBugs = sumColumn(4);
                const totalRegression = sumColumn(5);
                const totalSmoke = sumColumn(6);
                const totalClientMeeting = sumColumn(7);
                const totalDailyMeeting = sumColumn(8);
                const totalMobile = sumColumn(9);
                
                $(api.column(3).footer()).html(totalTasks !== undefined ? totalTasks : '0');
                $(api.column(4).footer()).html(totalBugs !== undefined ? totalBugs : '0');
                $(api.column(5).footer()).html(totalRegression !== undefined ? totalRegression : '0');
                $(api.column(6).footer()).html(totalSmoke !== undefined ? totalSmoke : '0');
                $(api.column(7).footer()).html(totalClientMeeting !== undefined ? totalClientMeeting : '0');
                $(api.column(8).footer()).html(totalDailyMeeting !== undefined ? totalDailyMeeting : '0');
                $(api.column(9).footer()).html(totalMobile !== undefined ? totalMobile : '0');
            },
            initComplete: function (settings, json) {
                // Copy the width of the header columns to the footer columns
                let api = this.api();
                $(api.columns().footer()).each(function(i) {
                    $(this).css('width', $(api.column(i).header()).width());
                });
            },
        });
    }
    
    /**
     * Initialize date filters
     */
    function initDateFilters() {
        // Handle change event of the date filter
        $('#date-filter').on('change', function() {
            if($(this).val() == undefined || $(this).val() === '') return;
            
            $('#user-name').val('');
            $('#project-name').val(''); 
            $('#from-date').val('');
            $('#to-date').val('');
            
            const date = new Date();
            const todayDate = date.toISOString().split('T')[0]; // Format: YYYY-MM-DD
            date.setDate(date.getDate() - 1); // Subtract 1 day
            const yesterdayDate = date.toISOString().split('T')[0]; // Format: YYYY-MM-DD
            
            from_date = $(this).val();
            if (yesterdayDate === from_date)
                to_date = yesterdayDate;  // Same dates 1 day
            else
                to_date = todayDate; // Set current date to the from Date field 
                
            loadReportData();
        });
        
        // Date search button handler
        $('#date-search-btn').click(function() {
            $('#date-filter').val('');
            from_date = $('#from-date').val();
            to_date = $('#to-date').val();
            loadReportData();
        });
        
        // Date reset button handler
        $('#date-reset-btn').click(function() {
            $('#user-name').val('');
            $('#project-name').val(''); 
            $('#date-filter').val('');
            $('#from-date').val('');
            $('#to-date').val('');
            from_date = to_date = '';
            loadReportData();
        });
    }
    
    /**
     * Initialize modal reset
     */
    function initModalReset() {
        $('#report-input-modal').on('hidden.bs.modal', function () {
            $('#reportForm')[0].reset();
            $('.report-form-submit').removeClass('disabled');
        });
    }
});
