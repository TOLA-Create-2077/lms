<script>
    $(document).ready(function() {
        // Initialize DataTables with only pagination and Khmer language
        var table = $('#reportTable').DataTable({
            "paging": true, // Enable pagination
            "searching": false, // Disable search
            "ordering": false, // Disable column sorting
            "info": true, // Disable table information
            "lengthChange": false, // Disable option to change number of rows displayed
            "pageLength": 10, // Default number of rows per page
            "language": {
                "decimal": "",
                "emptyTable": "ពុំមានទិន្នន័យ",
                "info": "បង្ហាញ _START_ ដល់ _END_ ក្នុងចំណោម _TOTAL_ ទិន្នន័យ",
                "infoEmpty": "បង្ហាញ 0 ដល់ 0 ក្នុងចំណោម 0 ទិន្នន័យ",
                "infoFiltered": "(ទាញចេញពីទិន្នន័យសរុបចំនួន_MAX_)",
                "lengthMenu": "បង្ហាញ _MENU_ ទិន្នន័យ",
                "loadingRecords": "កំពុងផ្ទុក...",
                "processing": "",
                "zeroRecords": "ពុំមានទិន្នន័យ",
                "paginate": {
                    "first": "ដំបូង",
                    "last": "ចុងក្រោយ",
                    "next": "បន្ទាប់",
                    "previous": "ពីមុន"
                },
                "aria": {
                    "sortAscending": ": ក្រុមតាមលំដាប់កើនឡើង",
                    "sortDescending": ": ក្រុមតាមលំដាប់បន្ថយ"
                }
            }
        });
    });
</script>