<style>
    .users-button-div a{
        margin-left: .5rem ;
        margin-right: .5rem;
        max-width: 7rem;
    }
</style>
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
                                <th> Action </th>
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
        $(document).on('click', '.deleteUser', function (e) {
    e.preventDefault();
    let userId = $(this).data('id');
    let url = '/user/' + userId;

    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to delete this user?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "No, cancel!",
        reverseButtons: true,
        customClass: {
            confirmButton: 'btn btn-danger ml-3', // Custom class for confirm button
            cancelButton: 'btn btn-secondary mr-3' // Custom class for cancel button
        },
        buttonsStyling: false // Disable SweetAlert2 default styles for buttons
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    $('#user-table').DataTable().ajax.reload(); // Reload DataTable after deletion
                    Swal.fire(
                        'Deleted!',
                        response.message,
                        'success'
                    );
                },
                error: function (xhr) {
                    Swal.fire(
                        'Error!',
                        'There was an issue deleting the user.',
                        'error'
                    );
                }
            });
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            Swal.fire(
                'Cancelled',
                'The user is safe :)',
                'error'
            );
        }
    });
});

        $(document).on('click', '.loginUser', function (){
            let userId = $(this).data('id');
            console.log('User id is : ' + userId);
            if(confirm('Are you sure to login this user?')){
                window.location.href = '/login-direct/'+userId;
            }
        });

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
                        $('#add-user-form')[0].reset();
                        $('#user-table').DataTable().ajax.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error Response is:', error);
                    }
                });
            }else {
                alert('Password does not matched');
                console.log("confirm Password is not correct ")
            }

        })
        $(document).on('click','#edit-user',function (e){
           e.preventDefault();
          let id = $(this).data('id');
            // alert(id);
          $.ajax({
              url : '/edit-user',
              method : 'GET',
              data : {id : id},
              success : function (response){
                //   console.log("Response is : " , response.user['name'],response.user['email'],response.user['role']);
                  $('#id').val(response.user['id']);
                  $('#name').val(response.user['name']);
                  $('#email').val(response.user['email']);
                  $('#role').val(response.user['role']);
                  $('#name').text(response.user['name']);
                  $('#email').text(response.user['email']);
                 $('#modal-center').modal('show')

              }
          });
        });
        loadUsers();
        function loadUsers(){
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
                  { data: 'role', name: 'role' },
                  { data: 'action', name: 'action'}
              ]
          });
      }
    });
</script>
