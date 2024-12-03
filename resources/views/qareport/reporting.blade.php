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
    #reports-table_filter{
        margin: .5rem;
    }
    #reports-table_filter label{
        margin-top: -0.5rem;
    }
    #reports-table_filter input{
        border-radius: .5rem;
    }
    .table-container .dates input{
        border-radius: .5rem;
    }
    .table-container .dates{
        margin-bottom: -2.5rem;
        margin-top: 0.5rem;
    }
    .dataTables_wrapper{
        margin: 0px 0 16px 8px;
    }
    #reports-table_length{
        margin-top: .75rem; 
    }
    #reports-table_length select{
        border-radius: .75rem; 
    }
    #reports-table_paginate .paginate_button.current{
        border-radius: 0.5rem;
    }
    #reports-table_paginate .paginate_button.current{
        margin-top: 0.5rem;
    }
    input, .form-control, textarea{ 
        border-radius: .5rem !important;
        margin-top: .25rem;
    }
    .modal-footer{
        display: flex;
        align-items: center;
        justify-content: space-evenly;
    }
</style>
    <div class="py-12">
        <div class="px-4">
            <div class="bg-white ">
                <div class="outer d-flex justify-between align-items-center px-4 border-bottom">
                    <div class="p-6 text-gray-900 d-flex justify-center w-75 font-semibold text-2xl">
                       @can('is-admin')  {{ __("All QA's Reports") }}@endcan
                        @can('is-user') {{ __("Today Report") }}@endcan
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary addTask" data-bs-toggle="modal" data-bs-target="#report-input-modal">
                            Add Task
                        </button>
                    </div>
                </div>
                {{-- @dump($dateOptions) --}}
                <div class="table-container px-2 " >
                    @can('is-admin')
                    <div class="dates d-flex gap-2">
                        <div class="">
                            <label for="date-filter">Date</label>                    
                            <select class="form-select w-auto rounded-3 z-3 relative" id="date-filter" style="padding: 6px;">
                                <option value="Selected" selected disabled>Select</option>
                                <option value="{{ $dateOptions['today'] }}">Today </option>
                                <option value="{{ $dateOptions['yesterday'] }}">Yesterday</option>
                                <option value="{{ $dateOptions['last3Days'] }}">Last 3 days</option>
                                <option value="{{ $dateOptions['last7Days'] }}">Last 7 days</option>
                                <option value="{{ $dateOptions['last15Days'] }}">Last 15 days </option>
                                <option value="{{ $dateOptions['last30Days'] }}">Last 30 days </option>
                            </select>
                        </div>
                        <div class="z-3">
                            <label for="user-name">Users</label>
                            <select class="form-select w-auto rounded-3 z-3" id="user-name" name="user-name" style="padding: 6px; ">
                                <option value="">Choose</option>
                                @foreach ($users as $user)
                                    <option value="{{$user->id}}">{{$user->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="z-3">
                            <label for="project-name">Projects</label>
                            <select class="form-select w-auto rounded-3 z-3" id="project-name" name="project-name" style="padding: 6px; ">
                                <option value="">Choose</option>
                                @foreach($projects as $project)
                                    <option value="{{$project->id}}">{{$project->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="z-3 d-flex gap-1 w-25">
                            <div class="" style="margin-top: -2px">
                                <label for="from-date">Date From:</label>
                                <input type="date" id="from-date" name="from-date" class="form-control" style="padding: 5px; max-width:8rem;">
                            </div>
                            <div class="" style="margin-top: -2px">
                                <label for="to-date">Date To:</label>
                                <input type="date" id="to-date" name="to-date" class="form-control" style="padding: 5px; max-width:8rem;">
                            </div>
                            <button type="button" class="btn btn-success rounded-3 h-auto align-self-end" id='date-search-btn'>Search</button>
                        </div>
                    </div>
                    @endcan
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
        $('#reportForm').on('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission
            showLoading();
            let formData = new FormData(this);
            $('.report-form-submit').prop('disabled', true);
            fetch("{{ route('user-reports.store') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
                } else {
                    // Handle validation errors
                    console.log(data.errors);
                    $('.report-form-submit').prop('disabled', false);
                }
            })
            .catch(error => console.error('Error: ', error));
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
                        beforeSend: function(){
                            showLoading();
                        },
                        success: function (response) {
                            $('#reports-table').DataTable().ajax.reload();
                            hideLoading();
                            // Swal.fire(
                            //     'Deleted!',
                            //     response.message,
                            //     'success'
                            // );
                        },
                        error: function (xhr) {
                            hideLoading();
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

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url : "{{route('user-reports.update')}}",
                method : 'patch',
                data : {id:id},
                beforeSend: function(){
                    showLoading();
                },
                success : function (response){
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
                error : function(xhr,status){
                    console.log("Error " + status );
                    hideLoading();
                }
            })
        });
        $('#task_tested, #bug_reported').on('input', function() {
            let value = $(this).val();
            // Allow only positive integers
            if (value < 0) {
                $(this).val('');
            } else {
                $(this).val(Math.floor(value)); // Ensure it's an integer
            }
        });
        let from_date = '';
        let to_date = '';
        loadReportData();
        function loadReportData(){
            if ($.fn.DataTable.isDataTable('#reports-table')) {
                $('#reports-table').DataTable().clear().destroy();
            }
            $('#reports-table').DataTable({
                // processing: true,
                serverSide: true,
                dom : '<"top" f> rtlp',
                scrollX:true,
                scrollY: '60vh',  // We can fix the height of the dataTable.
                scrollCollapse: true,
                paging: true,
                ajax: {
                    url : '{{ route("reports.data") }}',
                    data: function (d) {
                        d.from_date = from_date;
                        d.to_date = to_date;
                        d.user_id = $('#user-name').val();
                        d.project_id = $('#project-name').val();
                    },
                    beforeSend: function(){
                        showLoading(true);
                    },
                    complete : function(){
                        hideLoading();
                    }
                },
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

                    $(api.column(3).footer()).html(totalTasks !== undefined ? totalTasks : '0');
                    $(api.column(4).footer()).html(totalBugs !== undefined ? totalBugs : '0');
                    $(api.column(5).footer()).html(totalRegression !== undefined ? totalRegression : '0');
                    $(api.column(6).footer()).html(totalSmoke !== undefined ? totalSmoke : '0');
                    $(api.column(7).footer()).html( totalClientMeeting !== undefined ? totalClientMeeting : '0');
                    $(api.column(8).footer()).html( totalDailyMeeting !== undefined ? totalDailyMeeting : '0');
                    $(api.column(9).footer()).html(totalMobile !== undefined ? totalMobile : '0');
                },
                initComplete: function (settings, json) {
                    // Copy the width of the header columns to the footer columns
                    let api = this.api();
                    $(api.columns().footer()).each(function(i) {
                        $(this).css('width', $(api.column(i).header()).width());
                        // let c =  $(this).width($(api.column(i).header()).width());
                    });
                },
              
            });
        }
        $('#report-input-modal').on('hidden.bs.modal', function () {
            $('#reportForm')[0].reset();
            $('.report-form-submit').removeClass('disabled');
        });

        // Handle change event of the date filter
        $('#date-filter').on('change', function() {  
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
        $('#date-search-btn').click( function() {
            console.log("Clicked on search Btton");
            from_date = $('#from-date').val();
            to_date = $('#to-date').val();
            loadReportData();
        });
        // function datesSearhesHtml(){
        //     const filterHtml = `
        //         <div class="dates row gx-3 gy-2">
        //             <div class="col-md-4 col-12">
        //                 <select class="form-select rounded-3" id="date-filter">
        //                     <option value="Selected" selected disabled>--- Select date ---</option>
        //                     <option value="{{ $dateOptions['today'] }}">Today</option>
        //                     <option value="{{ $dateOptions['yesterday'] }}">Yesterday</option>
        //                     <option value="{{ $dateOptions['last3Days'] }}">Last 3 days</option>
        //                     <option value="{{ $dateOptions['last7Days'] }}">Last 7 days</option>
        //                     <option value="{{ $dateOptions['last15Days'] }}">Last 15 days</option>
        //                     <option value="{{ $dateOptions['last30Days'] }}">Last 30 days</option>
        //                 </select>
        //             </div>
        //             <div class="col-md-8 col-12">
        //                 <div class="d-flex flex-wrap align-items-center">
        //                     <div class="me-2">
        //                         <label for="from-date" class="form-label mb-0">Date From:</label>
        //                         <input type="date" id="from-date" name="from-date" class="form-control">
        //                     </div>
        //                     <div class="me-2">
        //                         <label for="to-date" class="form-label mb-0">Date To:</label>
        //                         <input type="date" id="to-date" name="to-date" class="form-control">
        //                     </div>
        //                     <button type="button" class="btn btn-success rounded-3" id="date-search-btn">Search</button>
        //                 </div>
        //             </div>
        //         </div>
        //     `;
        //     return filterHtml;
        // }
    });
</script>
