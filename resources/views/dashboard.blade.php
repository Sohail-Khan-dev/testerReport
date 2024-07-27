<x-app-layout>
  <style>
    .form-group {
      align-items: center;
      justify-content: end;
      display: flex;
      flex-flow: column;
      padding: .5rem;
      flex: 0 0 auto;
      width: 50%;
      margin-bottom: .5rem !important;
    }

    .form-group-checkbox {
      align-items: center;
      justify-content: end;
      display: flex;
      flex-flow: column;
      padding: .5rem;
      /* flex: 0 0 auto; */
      width: 50%;
      margin-bottom: .5rem !important;
    }

    label {
      font-weight: 600;
      text-align: center;
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
              {{ __("Your's reports will be here!") }}
            </div>
            <div>
              <button type="button" class="btn btn-primary" onclick="window.location.href='/register/new'">
                Add Tester
              </button> 
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="modal-center" tabindex="-1" role="dialog" aria-labelledby="modal-centerTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header d-flex justify-between h5">
            <h5 class="modal-title" id="exampleModalLongTitle">Daily Tasks Done</h5>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form action="{{ route('user-reports.store') }}" method="POST">
              @csrf
              <div class="d-flex justify-between align-items-center">
                <div class="form-group">
                  <label for="name">Name</label>
                  <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group">
                  <label for="email">Email</label>
                  <input type="number" class="form-control" id="email" name="task_tested">
                </div>
                <div class="form-group">
                  <label for="password">Password</label>
                  <input type="number" class="form-control" id="password" name="task_tested">
                </div>
              </div>
              <div class="">
                <div class="form-group w-100">
                  <label for="description">Description</label>
                  <textarea type="text" class="form-control" id="description" rows="3"> </textarea>
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
</x-app-layout>