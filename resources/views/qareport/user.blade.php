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
                        <button type="button" class="btn btn-primary" id="add-user-button"  data-bs-toggle="modal" data-bs-target="#modal-center">
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
                buttonsStyling: false, // Disable SweetAlert2 default styles for buttons
                
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        beforeSend :function(){
                            showLoading();
                        },
                        success: function (response) {
                            hideLoading();
                            $('#user-table').DataTable().ajax.reload(); // Reload DataTable after deletion
                        },
                        error: function (xhr) {
                            hideLoading();
                            Swal.fire(
                                'Error!', 'There was an issue deleting the user.', 'error'
                            );
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire(
                        'Cancelled','The user is safe :)','error'
                    );
                }
            });
        });

        $(document).on('click', '.loginUser', function (){
            let userId = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "Are you sure you want to log in as this user?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Log in!",
                cancelButtonText: "No, cancel!",
                reverseButtons: true,
                customClass: {
                    confirmButton: 'btn btn-danger ml-3', // Custom class for confirm button
                    cancelButton: 'btn btn-secondary mr-3' // Custom class for cancel button
                },
                buttonsStyling: false, // Disable SweetAlert2 default styles for buttons
                
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '/login-direct/'+userId;
                }
            });
        });
        $("#add-user-button").on('click',function(){
            $('#add-user-form')[0].reset();
            $('#projects').next('.mult-select-tag').remove(); // Assumes the container is added after the select element
                // Reset the project select field
                $('#projects').val([]);  // Clear existing selections
                // Assign the new values
                $("#projects").val();
                new MultiSelectTag('projects');
            $("#add-user-title").text('Add New User');
            $('#add-user-submit').text('Register');
        });
        $("#add-user-form").on('submit',function (e){
            e.preventDefault();
            let passwordVal = $("#password").val();
            let confirmVal = $("#password_confirmation").val();
            if(passwordVal == confirmVal){
                let formData = new FormData(this);
                $.ajax({
                    url : "{{ route('register.new')}}" ,
                    method : 'POST',
                    data: new FormData(this),
                    processData: false, // Prevent jQuery from automatically transforming the data into a query string
                    contentType: false,
                    beforeSend:function(){
                        showLoading();
                    },
                    success : function (response){
                        hideLoading();
                        $('#close-modal').click();
                        $('#add-user-form')[0].reset();
                        $('#user-table').DataTable().ajax.reload();
                    },
                    error: function(xhr, status, error) {
                        hideLoading();
                        console.error('Error Response is:', error);
                    }
                });
            }else {
                Swal.fire(
                    'Error!', 'The password does not match!', 'error'
                );
                // alert('Password does not matched');
                console.log("confirm Password is not correct ")
            }

        }); 
        $('.togglePassword').on('click',function(){
            let passwordInput =  $('#password');
            if(passwordInput.attr('type') === 'password'){
                passwordInput.attr('type',"text");
                $(this).text('Hide');
            }
            else if(passwordInput.attr('type') === 'text') {
                passwordInput.attr('type',"password");
                $(this).text('Show');
            }

        });
        $(document).on('click','#edit-user',function (e){
           e.preventDefault();
          let id = $(this).data('id');
          $.ajax({
              url : '/edit-user',
              method : 'GET',
              data : {id : id},
              beforeSend : function(){
                showLoading();
              },
              success : function (response){ 
                hideLoading();
                // $('#projects').val([]);
                $('#id').val(response.user['id']);
                $('#name').val(response.user['name']);
                $('#email').val(response.user['email']);
                $('#role').val(response.user['role']);
                $('#name').text(response.user['name']);
                $('#email').text(response.user['email']);
        
                $('#projects').next('.mult-select-tag').remove(); // Assumes the container is added after the select element
                // Reset the project select field
                $('#projects').val([]);  // Clear existing selections
                // Assign the new values
                $("#projects").val(response.projects);

                // Reinitialize the multi-select plugin
                new MultiSelectTag('projects');  // Assuming you already initialized it somewhere

                $("#add-user-title").text('Update User');
                $('#add-user-submit').text('Update');
                $('#modal-center').modal('show')

              },
              error: function() {
                hideLoading();
                // Hide loading SVG even on error
                $('#loadingModal').hide();
            }
          });
        });
        loadUsers();
        function loadUsers(){
            showLoading(true);
          if($.fn.dataTable.isDataTable('#user-table')){
              $('#user-table').DataTable().clear().destroy();
          }
          $('#user-table').DataTable({
                processing: true,
                serverSide: true,
                dom: '<"top"f> rt<"bottom"ip><"clear">',
                ajax: {
                    url:'{{ route("users.data") }}',
                    complete : function(){
                        hideLoading();
                    }
                },
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'role', name: 'role' },
                    { data: 'action', name: 'action'}
                ]
            });
        }
    });

    new MultiSelectTag('projects', {
        rounded: true,    // default true
        shadow: true,      // default false
        placeholder: 'Search',  // default Search...
        tagColor: {
            textColor: '#327b2c',
            borderColor: '#92e681',
            bgColor: '#eaffe6',
        },
        onChange: function(values) {
            console.log(values)
       }
    });
</script>
