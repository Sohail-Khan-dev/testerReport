<x-app-layout>
    @vite(['resources/css/reporting.css'])
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="flex-grow-1 text-center">
                <div class=" h2 card-title">
                    @can('is-admin')
                        {{ __("All QA's Reports") }}
                    @endcan
                    @can('is-user')
                        {{ __('Today Report') }}
                    @endcan
                </div>
            </div>
            <div>
                <button type="button" class="btn btn-primary addTask" data-bs-toggle="modal"
                    data-bs-target="#report-input-modal">
                    <i class="fas fa-plus me-2"></i> Add Task
                </button>
            </div>
        </div>
        <div class="card-body p-0">
                <div class="table-responsive">
                    <div class="d-flex flex-column flex-md-row justify-content-between mb-3 mx-4">
                        <div class="order-2 order-md-1">
                            @can('is-admin')
                                <div class="dates d-flex gap-2">
                                    <div class="dates-container row g-2">
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                                            <label for="date-filter">Date</label>
                                            <select class="form-select rounded-3" id="date-filter">
                                                <option value="" selected disabled>Select</option>
                                                <option value="{{ $dateOptions['today'] }}">Today </option>
                                                <option value="{{ $dateOptions['yesterday'] }}">Yesterday</option>
                                                <option value="{{ $dateOptions['last3Days'] }}">Last 3 days</option>
                                                <option value="{{ $dateOptions['last7Days'] }}">Last 7 days</option>
                                                <option value="{{ $dateOptions['last15Days'] }}">Last 15 days </option>
                                                <option value="{{ $dateOptions['last30Days'] }}">Last 30 days </option>
                                            </select>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                                            <label for="user-name">Users</label>
                                            <select class="form-select rounded-3" id="user-name" name="user-name">
                                                <option value="">Choose</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                                            <label for="project-name">Projects</label>
                                            <select class="form-select rounded-3" id="project-name" name="project-name">
                                                <option value="">Choose</option>
                                                @foreach ($allprojects as $project)
                                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <label for="from-date">Date From:</label>
                                                    <input type="date" id="from-date" name="from-date"
                                                        class="form-control rounded-3">
                                                </div>
                                                <div class="col-6">
                                                    <label for="to-date">Date To:</label>
                                                    <input type="date" id="to-date" name="to-date"
                                                        class="form-control rounded-3">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-2 d-flex align-items-end gap-2">
                                            <button type="button" class="btn btn-success rounded-3 flex-grow-1"
                                                id='date-search-btn'>Search</button>
                                            <button type="button" class="btn btn-primary rounded-3 flex-grow-1"
                                                id='date-reset-btn'>Reset</button>
                                        </div>
                                    </div>
                                </div>
                            @endcan
                        </div>
                    </div>
                    <div class="px-4 py-3">
                        <h2 class="text-center h3 " style="margin-bottom: -2rem">Latest Reports Record</h2>
                        <table id="reports-table" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr class="text-center">
                                <th> Date&Time</th>
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
    </div>
    <!-- Modal -->
    @include('modals.add-tester-report')
</x-app-layout>
<script>
    // Define global variables for use in the reporting.js file
    const reportStoreRoute = "{{ route('user-reports.store') }}";
    const reportUpdateRoute = "{{ route('user-reports.update') }}";
    const reportsDataRoute = "{{ route('reports.data') }}";
    const csrfToken = "{{ csrf_token() }}";
    const userRoleGlobal = "{{ auth()->user()->role }}";
</script>
@vite(['resources/js/reporting.js'])
