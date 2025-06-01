/**
 * Reporting Module - Handles all reporting functionality
 */

/**
 * Helper function to truncate text with ellipsis and tooltip
 * @param {string} text - The text to truncate
 * @param {number} maxLength - Maximum length before truncation
 * @returns {string} - Truncated text with tooltip or original text
 */
function truncateText(text, maxLength) {
    if (!text) return text;

    // Convert to string if not already
    text = String(text);

    if (text.length > maxLength) {
        // Escape HTML characters in the title attribute
        const escapedText = text.replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        return '<span title="' + escapedText + '" class="truncated-text" data-full-text="' + escapedText + '">' +
               text.substring(0, maxLength) + '...</span>';
    }

    // Even for non-truncated text, add tooltip for consistency
    const escapedText = text.replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    return '<span title="' + escapedText + '" class="full-text" data-full-text="' + escapedText + '">' + text + '</span>';
}

document.addEventListener('DOMContentLoaded', function () {
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
        $('#reportForm').on('submit', function (e) {
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
                            beforeSend: function () {
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
        $(document).on("click", ".editReport", function (e) {
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
                data: { id: id },
                beforeSend: function () {
                    showLoading();
                },
                success: function (response) {
                    hideLoading();
                    var report = response[1];
                    $("#report-input-modal").modal('show');

                    // Set values using .val() for input fields
                    $("#id").val(report['id']);
                    if (userRole != 'user')
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
                error: function (xhr, status) {
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
        $('#task_tested, #bug_reported').on('input', function () {
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
            responsive: false,
            paging: true,
            scrollX: true,  // Enable horizontal scrolling
            scrollY: "400px", // Add vertical scrolling to stabilize layout
            scrollCollapse: true,
            autoWidth: false,
            fixedColumns: false, // Disable fixed columns to prevent layout shifts
            ajax: {
                url: reportsDataRoute,
                data: function (d) {
                    d.from_date = from_date;
                    d.to_date = to_date;
                    d.user_id = $('#user-name').val();
                    d.project_id = $('#project-name').val();
                },
                // beforeSend: function () {
                //     showLoading(true);
                // },
                // complete: function () {
                //     hideLoading();
                // }
            },
            columns: [
                { data: 'date', name: 'date', width: '150px', orderable: false, searchable: false    },
                { data: 'user_name', name: 'user_name', searchable: true, width: '120px' },
                { data: 'project_name', name: 'project_name', searchable: true, width: '120px' },
                { data: 'task_tested', name: 'task_tested', width: '80px' },
                { data: 'bug_reported', name: 'bug_reported', orderable: false, searchable: false, width: '80px' },
                { data: 'regression', name: 'regression', orderable: false, searchable: false, width: '80px' },
                { data: 'smoke_testing', name: 'smoke_testing', orderable: false, searchable: false, width: '80px' },
                { data: 'client_meeting', name: 'client_meeting', orderable: false, searchable: false, width: '80px' },
                { data: 'daily_meeting', name: 'daily_meeting', orderable: false, searchable: false, width: '80px' },
                { data: 'mobile_testing', name: 'mobile_testing', orderable: false, searchable: false, width: '80px' },
                { data: 'automation', name: 'automation', orderable: false, searchable: false, width: '80px' },
                { data: 'other', name: 'other', orderable: false, searchable: false, width: '80px' },
                { data: 'description', name: 'description', width: '200px' },
                { data: 'action', name: 'action', orderable: false, searchable: false, width: '100px' }
            ],
            columnDefs: [
                {
                    targets: '_all',
                    className: 'dt-head-center dt-body-center',
                    createdCell: function (td) {
                        $(td).css({
                            'text-align': 'center',
                            'vertical-align': 'middle',
                            'white-space': 'nowrap',
                            'overflow': 'hidden',
                            'text-overflow': 'ellipsis'
                        });
                    }
                },
                {
                    // Target Project column (3rd column, index 2)
                    targets: [2],
                    render: function(data, type, row) {
                        if (type === 'display' || type === 'type') {
                            return truncateText(data, 20);
                        }
                        return data;
                    }
                },
                {
                    // Target Description column (13th column, index 12)
                    targets: [12],
                    render: function(data, type, row) {
                        if (type === 'display' || type === 'type') {
                            return truncateText(data, 20);
                        }
                        return data;
                    }
                }
            ],
            drawCallback: function () {
                var api = this.api();
                function sumColumn(columnIndex) {
                    var total = 0;
                    if (api.column(columnIndex).data().length > 0) {
                        api.column(columnIndex).data().each(function (value) {
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
            initComplete: function () {
                var api = this.api();

                // Function to fix alignment issues
                function fixTableAlignment() {
                    // Force column adjustment
                    api.columns.adjust();

                    // Get the actual table width
                    var tableWidth = $('#reports-table').outerWidth();

                    // // Ensure all scroll containers have consistent styling
                    $('.dataTables_scrollHead, .dataTables_scrollFoot').css({
                        'width': '100%',
                        'margin': '0',
                        'padding': '0',
                        'position': 'relative',
                        'left': '0',
                        'transform': 'translateX(0)'
                    });

                    // Ensure inner containers match and force remove padding
                    $('.dataTables_scrollHeadInner, .dataTables_scrollFootInner').css({
                        'width': tableWidth + 'px',
                        'margin': '0',
                        'padding': '0',
                        'padding-right': '0',
                        'padding-left': '0',
                        'box-sizing': 'border-box'
                    });

                    // Force remove padding with direct style manipulation
                    $('.dataTables_scrollFootInner, .dataTables_scrollHeadInner').each(function() {
                        this.style.setProperty('padding-right', '0', 'important');
                        this.style.setProperty('padding-left', '0', 'important');
                        this.style.setProperty('padding', '0', 'important');
                    });

                    // // Force all tables to have the same width and layout
                    $('.dataTables_scrollHead table, table, .dataTables_scrollFoot table').css({
                        'width': tableWidth + 'px',
                        'table-layout': 'fixed',
                        'margin': '0',
                        'position': 'relative',
                        'left': '0'
                    });

                    // Synchronize column widths between header, body, and footer
                    // api.columns().every(function (index) {
                    //     var headerCell = $(this.header());
                    //     var footerCell = $(this.footer());
                    //     var headerWidth = headerCell.outerWidth();

                    //     if (footerCell.length) {
                    //         footerCell.css({
                    //             'width': headerWidth + 'px',
                    //             'min-width': headerWidth + 'px',
                    //             'max-width': headerWidth + 'px'
                    //         });
                    //     }
                    // });
                }

                // Initial fix
                setTimeout(fixTableAlignment, 100);

                // Store the function for later use
                window.fixDataTableAlignment = fixTableAlignment;

                // Add immediate resize listener for dimension changes
                $(window).on('resize.dataTableFix', function () {
                    clearTimeout(window.resizeTimer);
                    window.resizeTimer = setTimeout(function () {
                        if ($.fn.DataTable.isDataTable('#reports-table')) {
                            fixTableAlignment();
                        }
                    }, 100);
                });
            }
        });
    }
    /**
     * Initialize date filters
     */
    function initDateFilters() {
        // Handle change event of the date filter
        $('#date-filter').on('change', function () {
            if ($(this).val() == undefined || $(this).val() === '') return;

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
        $('#date-search-btn').click(function () {
            $('#date-filter').val('');
            from_date = $('#from-date').val();
            to_date = $('#to-date').val();
            loadReportData();
        });

        // Date reset button handler
        $('#date-reset-btn').click(function () {
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

    /**
     * Initialize enhanced tooltips for Project and Description columns
     */
    function initEnhancedTooltips() {
        // Add event listeners for better tooltip positioning and visual feedback
        $(document).on('mouseenter', '#reports-table td:nth-child(3) span[title], #reports-table td:nth-child(13) span[title]', function(e) {
            const $this = $(this);
            const fullText = $this.attr('title') || $this.data('full-text');

            if (fullText && fullText.length > 20) {
                // Add visual indicator that there's more content
                $this.addClass('has-tooltip');

                // Add a subtle animation to indicate interactivity
                $this.css({
                    'transform': 'scale(1.02)',
                    'transition': 'transform 0.2s ease'
                });
            }
        });

        $(document).on('mouseleave', '#reports-table td:nth-child(3) span[title], #reports-table td:nth-child(13) span[title]', function() {
            $(this).removeClass('has-tooltip').css({
                'transform': 'scale(1)',
                'transition': 'transform 0.2s ease'
            });
        });

        // Add double-click functionality to show content in a modal for very long text
        $(document).on('dblclick', '#reports-table td:nth-child(3) .truncated-text, #reports-table td:nth-child(13) .truncated-text', function(e) {
            e.preventDefault();
            const fullText = $(this).attr('title') || $(this).data('full-text');
            const columnName = $(this).closest('td').is(':nth-child(3)') ? 'Project' : 'Description';

            if (fullText && fullText.length > 50) {
                showContentModal(columnName, fullText);
            }
        });
    }

    /**
     * Show content in a modal for very long text
     */
    function showContentModal(columnName, content) {
        // Create modal HTML with better styling
        const modalHtml = `
            <div class="modal fade" id="contentModal" tabindex="-1" role="dialog" aria-labelledby="contentModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="contentModalLabel">
                                <i class="fas fa-info-circle mr-2"></i>${columnName} Content
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="content-display p-3" style="background-color: #f8f9fa; border-radius: 8px; border-left: 4px solid #007bff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6;">
                                ${content}
                            </div>
                            <div class="mt-3 text-muted small">
                                <i class="fas fa-lightbulb mr-1"></i>
                                Tip: You can copy this text by selecting it.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times mr-1"></i>Close
                            </button>
                            <button type="button" class="btn btn-primary" onclick="copyToClipboard('${content.replace(/'/g, "\\'")}')">
                                <i class="fas fa-copy mr-1"></i>Copy Text
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remove existing modal if any
        $('#contentModal').remove();

        // Add modal to body and show
        $('body').append(modalHtml);
        $('#contentModal').modal('show');

        // Remove modal from DOM when hidden
        $('#contentModal').on('hidden.bs.modal', function() {
            $(this).remove();
        });
    }

    /**
     * Copy text to clipboard
     */
    function copyToClipboard(text) {
        if (navigator.clipboard && window.isSecureContext) {
            // Use the modern clipboard API
            navigator.clipboard.writeText(text).then(function() {
                showCopySuccess();
            }).catch(function(err) {
                console.error('Failed to copy text: ', err);
                fallbackCopyTextToClipboard(text);
            });
        } else {
            // Fallback for older browsers
            fallbackCopyTextToClipboard(text);
        }
    }

    /**
     * Fallback copy method for older browsers
     */
    function fallbackCopyTextToClipboard(text) {
        const textArea = document.createElement("textarea");
        textArea.value = text;
        textArea.style.top = "0";
        textArea.style.left = "0";
        textArea.style.position = "fixed";

        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();

        try {
            const successful = document.execCommand('copy');
            if (successful) {
                showCopySuccess();
            } else {
                console.error('Fallback: Copying text command was unsuccessful');
            }
        } catch (err) {
            console.error('Fallback: Oops, unable to copy', err);
        }

        document.body.removeChild(textArea);
    }

    /**
     * Show copy success message
     */
    function showCopySuccess() {
        // Create a temporary success message
        const successMsg = $('<div class="alert alert-success alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 250px;">' +
            '<i class="fas fa-check-circle mr-2"></i>Text copied to clipboard!' +
            '<button type="button" class="close" data-dismiss="alert">' +
            '<span>&times;</span>' +
            '</button>' +
            '</div>');

        $('body').append(successMsg);

        // Auto-remove after 3 seconds
        setTimeout(function() {
            successMsg.fadeOut(function() {
                $(this).remove();
            });
        }, 3000);
    }

    // Initialize enhanced tooltips when document is ready
    $(document).ready(function() {
        initEnhancedTooltips();
    });
});
