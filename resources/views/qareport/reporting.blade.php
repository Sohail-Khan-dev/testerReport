<x-app-layout>
    <div class="py-12">
        <div class="px-4">
            <div class="bg-white ">
                <div class="outer d-flex justify-between align-items-center px-4 border-bottom">
                    <div class="p-6 text-gray-900 d-flex justify-center w-75 font-semibold text-2xl">
                        {{ __("All QA's Report") }}
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#report-input-modal">
                            Add Task
                        </button>
                    </div>
                </div>
                <div class="table-container py-2 px-2">
                    <table id="reports-table" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th> Date</th>
                                <th> Name</th>
                                <th> Project</th>
                                <th> Tasks Tested</th>
                                <th> Bugs Testing</th>
                                <th> Regression Testing</th>
                                <th> Smoke Testing</th>
                                <th> Client Meeting</th>
                                <th> Daily Meeting</th>
                                <th> Mobile Testing</th>
                                <th> Other</th>
                                <th> Description</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Total</th>
                                <th>User </th>
                                <th>Project</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>No sum </th>
                                <th>No Summation </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="report-input-modal" tabindex="-1" role="dialog" aria-labelledby="report-input-modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex justify-between h5">
                    <h5 class="modal-title" id="longTitle">Daily Tasks Done</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" id="closeModalbtn">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="reportForm"> {{-- action="{{ route('user-reports.store') }}" method="POST">--}}
                        @csrf
                        <div class="row row-cols-2">
                            @if(auth()->user()->role != 'admin' && auth()->user()->role != 'manager')
                            <div class="form-group ">
                                <label for="user_id">Employee Name sdf </label>
                                <input id="user_id" type="text" readonly value="{{ auth()->user()->name}}">
                            @else
                            <div class="form-group ">
                                <label for="user_id">Select Employee</label>
                                <select class="form-control" id="user_id" name="user_id">
                                    <option value="None">Choose Employee</option>
                                    @foreach ($users as $user)
                                    <option value="{{$user->id}}">{{$user->name}}</option>
                                    @endforeach
                                </select>
                            @endif
                            </div>
                            <div class="form-group">
                                <label for="date">Date</label>
                                <input type="date" class="form-control" id="date" name="date" value="{{ date('Y-m-d') }}" readonly>
                            </div>
                            <div class="form-group">
                                <label for="project_id">Select Project</label>
                                <select class="form-control" id="project_id" name="project_id" required>
                                    <option value="" selected> Select Project</option>
                                    @foreach($projects as $project)
                                    <option value="{{$project->id}}">{{$project->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="task_tested">Task Tested</label>
                                <input type="number" class="form-control" id="task_tested" name="task_tested" required>
                            </div>
                            <div class="form-group">
                                <label for="bug_reported">Bug Reported</label>
                                <input type="number" class="form-control" id="bug_reported" name="bug_reported">
                            </div>
                            <div class="form-group">
                                <label for="other">Other</label>
                                <input type="text" class="form-control" id="other" name="other">
                            </div>

                        </div>
                        <div class="d-flex justify-between align-items-center flex-row">
                            <div class="form-group-checkbox">
                                <label for="regression">Regression testing</label>
                                <input type="checkbox" id="regression" name="regression" value="1">
                            </div>
                            <div class="form-group-checkbox">
                                <label for="smoke_testing">Smoke Testing</label>
                                <input type="checkbox" id="smoke_testing" name="smoke_testing" value="1">
                            </div>
                            <div class="form-group-checkbox">
                                <label for="client_meeting">Client Meeting</label>
                                <input type="checkbox" id="client_meeting" name="client_meeting" value="1">
                            </div>

                            <div class="form-group-checkbox">
                                <label for="daily_meeting">Daily Meeting</label>
                                <input type="checkbox" id="daily_meeting" name="daily_meeting" value="1">
                            </div>
                            <div class="form-group-checkbox">
                                <label for="mobile_testing">Mobile Testing</label>
                                <input type="checkbox" id="mobile_testing" name="mobile_testing" value="1">
                            </div>
                        </div>
                        <div class="">
                            <div class="form-group w-100">
                                <label for="description">Description</label>
                                <textarea type="text" class="form-control" id="description" name='description' rows="3"> </textarea>
                            </div>

                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    </div>
</x-app-layout>
<script>
    $(document).ready(function() {
        loadReportData();
        $('#reportForm').on('submit', function(e) {
           console.log("Form is submitted ")
            e.preventDefault(); // Prevent the default form submission
            let formData = new FormData(this);

            fetch('{{ route('user-reports.store') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {

                    $('#report-input-modal').modal('hide');
                    $('#closeModalbtn').click();
                    // $('#reportForm')[0].reset();
                    loadReportData();
                } else {
                    // Handle validation errors
                    console.log(data.errors);
                }
            })
            .catch(error => console.error('Error: ', error));
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
                    {   data: 'other', name: 'other' },
                    {   data: 'description', name: 'description' },

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
    });
</script>