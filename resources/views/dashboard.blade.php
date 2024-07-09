<x-app-layout>
    <style>
        .form-group{
            align-items: center;
            justify-content: end;
            display: flex;
            flex-flow: column;
            padding: .5rem;
            flex: 0 0 auto;
            width: 50%;
        }
    </style>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="outer d-flex justify-between align-items-center px-4">
                    <div class="p-6 text-gray-900">
                        {{ __("You're logged in!") }}
                    </div>
                    <div>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter">
                        Add Task 
                    </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header d-flex justify-between h5">
        <h5 class="modal-title" id="exampleModalLongTitle">Daily Tasks Done</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <form action="{{ route('user-reports.store') }}" method="POST">
            @csrf
            <div class="d-flex justify-between align-items-center">
                <div class="form-group ">
                    <label for="user_id">Select Employee</label>
                    <select class="form-control" id="user_id" name="user_id">
                            <option value="None">Choose Employee</option>
                            <option value="user1">Employee 1</option>
                            <option value="user2">Employee 2</option>
                            <option value="user3">Employee 3</option>
                            <option value="user4">Employee 4</option>
                            <option value="user5">Employee 5</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="project_id">Select Project</label>
                    <select class="form-control" id="project_id" name="project_id">
                        <option value="none" selected> Select Project</option>
                        <option value="project1"> Project 1</option>
                        <option value="project1"> Project 2</option>
                        <option value="project1"> Project 3</option>
                        <option value="project1"> Project 4</option>
                    </select>
                </div>
            </div>
            <div class="d-flex justify-between align-items-center">
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" class="form-control" id="date" name="date" required>
                </div>
                <div class="form-group">
                    <label for="task_tested">Task Tested</label>
                    <input type="number" class="form-control" id="task_tested" name="task_tested">
                </div>
            </div>
            <div class="d-flex justify-between align-items-center">
                <div class="form-group">
                    <label for="bug_reported">Bug Reported</label>
                    <input type="number" class="form-control" id="bug_reported" name="bug_reported">
                </div>
                <div class="form-group">
                    <label for="regression">Regression</label>
                    <input type="checkbox" id="regression" name="regression" value="1">
                </div>
            </div>
            <div class="d-flex justify-between align-items-center">
                <div class="form-group">
                    <label for="smoke_testing">Smoke Testing</label>
                    <input type="checkbox" id="smoke_testing" name="smoke_testing" value="1">
                </div>
                <div class="form-group">
                    <label for="client_meeting">Client Meeting</label>
                    <input type="checkbox" id="client_meeting" name="client_meeting" value="1">
                </div>
            </div>
            <div class="d-flex justify-between align-items-center">
                <div class="form-group">
                    <label for="daily_meeting">Daily Meeting</label>
                    <input type="checkbox" id="daily_meeting" name="daily_meeting" value="1">
                </div>
                <div class="form-group">
                    <label for="mobile_testing">Mobile Testing</label>
                    <input type="checkbox" id="mobile_testing" name="mobile_testing" value="1">
                </div>
            </div>
            <div class="d-flex justify-between align-items-center">
                <div class="form-group">
                    <label for="description">Description</label>
                    <input type="text" class="form-control" id="description" name="description">
                </div>
                <div class="form-group">
                    <label for="other">Other</label>
                    <input type="text" class="form-control" id="other" name="other">
                </div>
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Submit</button>
      </div>
      </form>
    </div>
  </div>
</div>
</x-app-layout>