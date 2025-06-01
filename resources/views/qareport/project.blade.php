<x-app-layout>
    <div class="py-12">
        <div class="px-4">
            <div class="bg-white ">
                <div class="outer d-flex justify-between align-items-center px-4 border-bottom">
                    <div class="p-6 text-gray-900 d-flex justify-center w-75 font-semibold text-2xl">
                        {{ __("All QA's Report") }}
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary"  data-bs-toggle="modal" data-bs-target="#add-project">
                            Add Project
                        </button>
                    </div>
                </div>
                <div class="py-2 px-2">
                    <table id="project-table" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th> Name</th>
                                <th> Description</th>
                                <th> Actions</th>
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
    @include('modals.add-project')
</x-app-layout>
<script>
    $(document).ready(function() {
        loadProject();
        $('#projectForm').on('submit', function (e) {
            console.log("Form is submitted ");
            showLoading();
            e.preventDefault(); // Prevent the default form submission
            let formData = new FormData(this);
            // if there is any value  project_id then call the update else call the store 
            let url = "{{ route('project.store') }}";
            const projectId = $('#project_id').val();
            if (projectId) {
                url = `/project/${projectId}`;
                formData.append('_method', 'PUT');
            }
            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        hideLoading();
                        $('#close-modal').click();
                        $('#projectForm')[0].reset();
                        loadProject();
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: projectId ? 'Project updated successfully!' : 'Project created successfully!',
                            timer: 1500
                        });
                    } else {
                        hideLoading();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Please check the form for errors',
                            footer: Object.values(data.errors).join('<br>')
                        });
                        console.log(data.errors);
                    }
                })
                .catch(error => {
                    hideLoading();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Something went wrong!'
                    });
                    console.error('Error: ', error)
                });
        });
        function loadProject(){
            if($.fn.dataTable.isDataTable('#project-table')){
                $('#project-table').DataTable().clear().destroy();
            }
            $('#project-table').DataTable({
                serverSide: true,
                dom: '<"top"f> rt<"bottom"ip><"clear">',
                ajax: {
                    url: '{{ route("project.data") }}',
                    // beforeSend: function(){
                    //     showLoading(true);
                    // },
                    // complete: function(){
                    //     hideLoading();
                    // }
                },
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'description', name: 'description' },
                    { 
                        data: 'id',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `
                                <button class="btn btn-sm btn-primary edit-project" data-id="${data}">
                                    Edit
                                </button>
                                <button class="btn btn-sm btn-danger delete-project" data-id="${data}">
                                    Delete
                                </button>
                            `;
                        }
                    }
                ]
            });
        }
        $(document).on('click', '.edit-project', function() {
            const projectId = $(this).data('id');
            fetch(`/project/${projectId}/edit`)
                .then(response => response.json())
                .then(data => {
                    $('#project_id').val(data.id);
                    $('#name').val(data.name);
                    $('#description').val(data.description);
                    $("#add-project-title").text("Edit Project");
                    $('#add-project-submit').text('Update');
                    $('#add-project').modal('show');
                })
                .catch(error => console.error('Error:', error));
        });

        // Replace the delete confirmation with SweetAlert2
        $(document).on('click', '.delete-project', function() {
            const projectId = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/project/${projectId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire(
                                'Deleted!',
                                'Project has been deleted.',
                                'success'
                            );
                            loadProject();
                        } else {
                            Swal.fire(
                                'Error!',
                                data.message,
                                'error'
                            );
                        }
                    })
                    .catch(error => {
                        Swal.fire(
                            'Error!',
                            'Something went wrong.',
                            'error'
                        );
                        console.error('Error:', error)
                    });
                }
            });
        });

        function resetForm(){
            $('#project_id').val('');
            $('#name').val('');
            $('#description').val('');
            $("#add-project-title").text("Add Project");
            $('#add-project-submit').text('Add');
        }
    });
</script>
