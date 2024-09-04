<x-app-layout>
<style> 
    .column-width-height{
        text-wrap: nowrap;
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .column-width-height:hover{
        text-wrap: wrap;
        max-width: 300px;
    }
</style>
    <div class="py-12">
        <div class="px-4">
            <div class="bg-white ">
                <div class="outer d-flex justify-between align-items-center px-4 border-bottom">
                    <div class="p-6 text-gray-900 d-flex justify-center w-75 font-semibold text-2xl">
                       @can('is-admin')  {{ __("All QA's Report") }}@endcan
                        @can('is-user') {{ __("Today Report") }}@endcan
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary addTask" data-bs-toggle="modal" data-bs-target="#report-input-modal">
                            Add Task
                        </button>
                    </div>
                </div>
                <div class="table-container py-2 px-2">
                    <table id="reports-table" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr class="text-center">
                                <th> Date</th>
                                <th> Name</th>
                                <th> Project</th>
                                <th> Tasks<br>Tested</th>
                                <th> Bugs<br>Reported</th>
                                <th> Regression<br>Testing</th>
                                <th> Smoke<br>Testing</th>
                                <th> Client<br> Meeting</th>
                                <th> Daily <br>Meeting</th>
                                <th> Mobile <br>Testing</th>
                                <th> Automation <br>Testing</th>
                                <th> Other</th>
                                <th> Description</th>
                                <th> Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">

                        </tbody>
                        <tfoot>
                            <tr class="text-center">
                                <th></th>
                                <th> </th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th> </th>
                                <th> </th>
                                <th> </th>
                                <th> </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
   @include('modals.add-tester-report')
</x-app-layout>
<script>
    $(document).ready(function() {
        loadReportData();
        $('#reportForm').on('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission
            let formData = new FormData(this);
            $('.report-form-submit').addClass('disabled');
            fetch("{{ route('user-reports.store') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
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
                    $('.report-form-submit').removeClass('disabled');
                    loadReportData();
                } else {
                    // Handle validation errors
                    console.log(data.errors);
                }
            })
            .catch(error => console.error('Error: ', error));
            $('.report-form-submit').removeClass('disabled'); 
        });
   
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
                    confirmButton: 'btn btn-danger ml-3', // Custom class for confirm button
                    cancelButton: 'btn btn-secondary mr-2' // Custom class for cancel button
                },
                buttonsStyling: false // Disable SweetAlert2 default styles for buttons
            })
            .then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            $('#reports-table').DataTable().ajax.reload();
                            Swal.fire(
                                'Deleted!',
                                response.message,
                                'success'
                            );
                        },
                        error: function (xhr) {
                            Swal.fire(
                                'Error!',
                                'There was an issue deleting the report.',
                                'error'
                            );
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire(
                        'Cancelled',
                        'Your report is safe :)',
                        'error'
                    );
                }
            });
        });
        $(document).on("click",".editReport", function(e){
            e.preventDefault();
            let id = $(this).data('id');
            var userRole = "{{ auth()->user()->role }}";
            console.log('user role ' , userRole);
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url : "{{route('user-reports.update')}}",
                method : 'patch',
                data : {id:id},
                success : function (response){
                    // console.log(response[1]);
                    var report = response[1];
                    $("#report-input-modal").modal('show'); 
                    // console.log('user Id ' + report['id']);     
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
                }
            })
        });

        function loadReportData(){
            if ($.fn.DataTable.isDataTable('#reports-table')) {
                $('#reports-table').DataTable().clear().destroy();
            }
            $('#reports-table').DataTable({
                processing: true,
                serverSide: true,
                dom : '<"top" f> rtlp',
                scrollX:true,
                scrollY: '60vh',  // We can fix the height of the dataTable.
                scrollCollapse: true,
                paging: true,
                ajax: '{{ route("reports.data") }}',
                columns: [
                    {  data: 'date', name: 'date' },
                    { data: 'user_name', name: 'user_name', orderable: false, searchable: true },
                    { data: 'project_name', name: 'project_name', orderable: false, searchable: true },
                    {  data: 'task_tested', name: 'task_tested' },
                    {  data: 'bug_reported', name: 'bug_reported'  },
                    {  data: 'regression', name: 'regression'  },
                    {  data: 'smoke_testing',name: 'smoke_testing'  },
                    {  data: 'client_meeting', name: 'client_meeting' },
                    {   data: 'daily_meeting', name: 'daily_meeting' },
                    {   data: 'mobile_testing', name: 'mobile_testing' },
                    {   data: 'automation', name: 'automation' },
                    {   data: 'other', name: 'other' },
                    {   data: 'description', name: 'description' },
                    {   data: 'action', name: 'action' },

                ],
                columnDefs: [
                    {
                        targets: [0,12], // The index of the 'description' column
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).addClass('column-width-height'); // Add your class here
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


                    $(api.column(3).footer()).html(totalTasks);
                    $(api.column(4).footer()).html(totalBugs);
                    $(api.column(5).footer()).html(totalRegression);
                    $(api.column(6).footer()).html(totalSmoke);
                    $(api.column(7).footer()).html( totalClientMeeting);
                    $(api.column(8).footer()).html( totalDailyMeeting);
                    $(api.column(9).footer()).html(totalMobile);
                },
                initComplete: function (settings, json) {
                    // Copy the width of the header columns to the footer columns
                    let api = this.api();
                    $(api.columns().footer()).each(function(i) {
                        let c =  $(this).width($(api.column(i).header()).width());
                        // debugger
                        //  console.log(c)
                    });
                }
            });
        }
        $('#report-input-modal').on('hidden.bs.modal', function () {
            $('#reportForm')[0].reset();
            $('.report-form-submit').removeClass('disabled');
        });
    });
</script>
