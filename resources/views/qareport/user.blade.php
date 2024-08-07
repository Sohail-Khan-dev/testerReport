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
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    @include('modals.add-user')
</x-app-layout>
<script>
    $(document).ready(function() {
        console.log('this is js  calleing ');
        var token = $('input[name="_token"]').val();
        $('#user-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("users.data") }}',
            columns: [
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'role', name: 'role' }
            ]
        });

        $('#name,#password,#email,#password_confirmation').on('blur', function(){
            let inputFieled =  $(this).attr('id');
            validateInput(inputFieled);
        });

        function validateInput(inputfield){
            let inputValue = $('#'+inputfield).val();
            console.log('INput value is : ' + inputValue);
            $.ajax({
                url: "{{ route('validate.field')}}",
                method: 'POST',
                data:{
                    _token : token,
                    field:inputfield,
                    value
                }

            })
            
        }
    });
</script>