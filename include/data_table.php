<script>
    $(document).ready(function() {
        // Initialize DataTables
        var table = $('#dataTable').DataTable({
            "language": {
                "decimal": "",
                "emptyTable": "ពុំមានទិន្នន័យ",
                "info": "បង្ហាញ _START_ ដល់ _END_ ក្នុងចំណោម _TOTAL_ ទិន្នន័យ",
                "infoEmpty": "បង្ហាញ 0 ដល់ 0 ក្នុងចំណោម 0 ទិន្នន័យ",
                "infoFiltered": "(ទាញចេញពីទិន្នន័យសរុបចំនួន_MAX_)",
                "lengthMenu": "បង្ហាញ _MENU_ ទិន្នន័យ",
                "loadingRecords": "Loading...",
                "processing": "",
                "search": "ស្វែងរក:",
                "zeroRecords": "ពុំមានទិន្នន័យ",
                "paginate": {
                    "first": "ដំបូង",
                    "last": "ចុងក្រោយ",
                    "next": "បន្ទាប់",
                    "previous": "ពីមុន"
                },
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending"
                },
                "columns": [{
                    "name": "សកម្មភាព",
                    "orderable": "false"
                }]
            }
        });
    });
</script>