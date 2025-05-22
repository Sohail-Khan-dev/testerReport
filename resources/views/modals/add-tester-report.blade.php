<!-- Add Tester Report Modal -->
<div class="modal fade" id="report-input-modal" tabindex="-1" role="dialog" aria-labelledby="report-input-modalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header d-flex justify-between h5">
                <h5 class="modal-title w-100 text-center text-2xl" id="longTitle">Daily Tasks Done</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closeModalbtn"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <form id="reportForm">
                    @csrf
                    <input type="hidden" id="id" value="" name="id">

                    <!-- User and Date Information -->
                    <div class="row row-cols-2">
                        <!-- User Selection -->
                        @if(auth()->user()->role != 'admin' && auth()->user()->role != 'manager')
                            <div class="form-group">
                                <label for="user_id">Employee Name</label>
                                <input id="user_id" type="text" class="form-control" readonly value="{{ auth()->user()->name}}">
                            </div>
                        @else
                            <div class="form-group">
                                <label for="user_id">Select Employee</label>
                                <select class="form-control" id="user_id" name="user_id" required>
                                    <option value="">Choose Employee</option>
                                    @foreach ($users as $user)
                                        <option value="{{$user->id}}">{{$user->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <!-- Date Selection -->
                        <div class="form-group">
                            <label for="date">Date</label>
                            <input type="date" class="form-control" id="date" name="date" value="{{ date('Y-m-d') }}">
                        </div>

                        <!-- Project Selection -->
                        <div class="form-group">
                            <label for="project_id">Select Project</label>
                            <select class="form-control" id="project_id" name="project_id" required>
                                <option value="" selected>Select Project</option>
                                @foreach($projects as $project)
                                    <option value="{{$project->id}}">{{$project->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Task and Bug Information -->
                        <div class="form-group">
                            <label for="task_tested">Task Tested</label>
                            <input type="number" class="form-control" id="task_tested" name="task_tested" min="0">
                        </div>

                        <div class="form-group">
                            <label for="bug_reported">Bug Reported</label>
                            <input type="number" class="form-control" id="bug_reported" name="bug_reported" min="0">
                        </div>

                        <div class="form-group">
                            <label for="other">Other</label>
                            <input type="text" class="form-control" id="other" name="other">
                        </div>
                    </div>

                    <!-- Testing Activities -->
                    <div class="d-flex justify-between align-items-center flex-row flex-wrap">
                        <div class="form-group-checkbox">
                            <label for="regression">Regression testing</label>
                            <input type="checkbox" id="regression" name="regression" value="1" class="form-check-input">
                        </div>

                        <div class="form-group-checkbox">
                            <label for="smoke_testing">Smoke Testing</label>
                            <input type="checkbox" id="smoke_testing" name="smoke_testing" value="1" class="form-check-input">
                        </div>

                        <div class="form-group-checkbox">
                            <label for="client_meeting">Client Meeting</label>
                            <input type="checkbox" id="client_meeting" name="client_meeting" value="1" class="form-check-input">
                        </div>

                        <div class="form-group-checkbox">
                            <label for="daily_meeting">Daily Meeting</label>
                            <input type="checkbox" id="daily_meeting" name="daily_meeting" value="1" class="form-check-input">
                        </div>

                        <div class="form-group-checkbox">
                            <label for="mobile_testing">Mobile Testing</label>
                            <input type="checkbox" id="mobile_testing" name="mobile_testing" value="1" class="form-check-input">
                        </div>

                        <div class="form-group-checkbox">
                            <label for="automation">Automation Testing</label>
                            <input type="checkbox" id="automation" name="automation" value="1" class="form-check-input">
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mt-3">
                        <div class="form-group w-100">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter your task and reported bug ID"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary report-form-submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
