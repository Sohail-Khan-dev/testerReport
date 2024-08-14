<x-app-layout>
    <div class="py-12">
        <div class="px-4">
            <div class="bg-white ">
                <div class="outer d-flex justify-between align-items-center px-4 border-bottom">
                    <div class="p-6 text-gray-900 d-flex justify-center w-75 font-semibold text-2xl">
                        {{ __("All QA's Report") }}
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary"  data-bs-toggle="modal" data-bs-target="#modal-center">
                            Add User
                        </button>
                    </div>
                </div>
                <div class="py-2 px-2">
                    <table id="user-table" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th> Name</th>
                                <th> Email</th>
                                <th> Role </th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    @include('modals.add-user')
</x-app-layout>
<script>
    $(document).ready(function() {

    loadUserData();
      function loadUserData(){
          if($.fn.dataTable.isDataTable('#user-table')){
              $('#user-table').DataTable().clear().destroy();
          }
          $('#user-table').DataTable({
              processing: true,
              serverSide: true,
              dom: '<"top"f> rt<"bottom"ip><"clear">',
              ajax: '{{ route("users.data") }}',
              columns: [
                  { data: 'name', name: 'name' },
                  { data: 'email', name: 'email' },
                  { data: 'role', name: 'role' }
              ]
          });
      }
    $("#add-user-form").on('submit',function (e){
        e.preventDefault();
        console.log("Submit is Called ");
        let passwordVal = $("#password").val();
        let confirmVal = $("#password_confirmation").val();
        console.log(passwordVal , confirmVal );
        if(passwordVal == confirmVal){
            let formData = new FormData(this);
            console.log('passwords matches', formData);
            $.ajax({
                url : '{{ route('register.new')}}' ,
                method : 'POST',
                data: new FormData(this),
                processData: false, // Prevent jQuery from automatically transforming the data into a query string
                contentType: false,
                success : function (response){
                    console.log('Success Response is : ' , response);
                    $('#close-modal').click();
                    loadUserData();
                },
                error: function(xhr, status, error) {
                    console.error('Error Response is:', error);
                }
            });
        }else {
            console.log("confirm Password is not correct ")
        }

    })
    });
</script>
