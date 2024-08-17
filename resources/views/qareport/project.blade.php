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

        $('#projectForm').on('submit', function(e) {
           ray("Form is submitted ");
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
</script>