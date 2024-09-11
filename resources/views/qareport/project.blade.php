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

            fetch("{{ route('project.store') }}", {
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
                    } else {
                        // Handle validation errors
                        console.log(data.errors);
                    }
                })
                .catch(error => console.error('Error: ', error));
        });
        function loadProject(){
            if($.fn.dataTable.isDataTable('#project-table')){
                $('#project-table').DataTable().clear().destroy();
            }
            $('#project-table').DataTable({
                // processing: true,
                serverSide: true,
                dom: '<"top"f> rt<"bottom"ip><"clear">',
                ajax: {
                   url: '{{ route("project.data") }}',
                   beforeSend :function(){
                    showLoading(true);
                   },
                   complete : function(){
                    hideLoading();
                   }
                
                },
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'description', name: 'description' }
                ]
            });
        }
    });
</script>
