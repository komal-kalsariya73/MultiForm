<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submitted Data (AJAX)</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="container mt-5">
    <h2 class="text-center">Submitted Form Data</h2>
  
  
    <div class="d-flex mb-3">
    <input type="text" id="search" class="form-control w-50 me-2" placeholder="Search by Name or Email" />
    <select id="gender-filter" class="form-select w-25">
        <option value="">All Genders</option>
        <option value="male">Male</option>
        <option value="female">Female</option>
        
    </select>

   </div>
    <div class="float-end">
  

        <a href="/multistep"><button class="bg-dark text-light p-3 mb-2">Add User</button></a>
    </div>
    <table class="table table-striped table-bordered" id="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Gender</th>
                <th>Hobbies</th>
                <th>City</th>
                <th>Bio</th>
                <th>Resume</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            
        </tbody>
    </table>
    <div id="pagination-controls" class="d-flex justify-content-center mt-3">
    <button id="prev-page" class="btn btn-primary mx-2" disabled>Previous</button>
    <button id="next-page" class="btn btn-primary mx-2">Next</button>
</div>

<script>
    
    let currentPage = 1; 
    const itemsPerPage = 3; 

    $('#gender-filter').on('change', function () {
    const gender = $(this).val(); 
    const searchQuery = $('#search').val(); 
    currentPage = 1; 
    loadTableData(currentPage, searchQuery, gender);
});

    function loadTableData(page = 1, search = '',gender = '') {
        const offset = (page - 1) * itemsPerPage;

        $.ajax({
            url: '<?= base_url("/fetch-data"); ?>',
            type: 'GET',
            dataType: 'json',
            data: {
                limit: itemsPerPage,
                offset: offset,
                search: search,
                gender: gender
            },
            success: function (response) {
                if (response.status) {
                    const rows = response.data.map((user, index) => `
                        <tr>
                            <td>${offset + index + 1}</td>
                            <td>${user.name}</td>
                            <td>${user.email}</td>
                            <td>${user.gender}</td>
                            <td>${user.hobbies}</td>
                            <td>${user.city}</td>
                            <td>${user.bio}</td>
                            <td>
                                ${user.resume
                                    ? `<a href="<?= base_url('uploads/'); ?>/${user.resume}" target="_blank">Download</a>`
                                    : 'No Resume'}
                            </td>
                            <td>
                                <a href="#" class="delete-icon" data-id="${user.id}"><i class="fas fa-trash-alt"></i></a>
                                <a href="<?= base_url()?>multistep/${user.id}" class="update-icon" data-id="${user.id}"><i class="fas fa-edit"></i></a>
                            </td>
                        </tr>
                    `);
                    $('#data-table tbody').html(rows.join(''));
                } else {
                    $('#data-table tbody').html('<tr><td colspan="9" class="text-center">No Data Found</td></tr>');
                }

            
                const totalRows = response.total_rows;
                const totalPages = Math.ceil(totalRows / itemsPerPage);

                $('#prev-page').prop('disabled', currentPage <= 1);
                $('#next-page').prop('disabled', currentPage >= totalPages);
            },
            error: function () {
                alert('Failed to load data.');
            }
        });
    }

    $(document).ready(function () {
        loadTableData(currentPage);

        $('#next-page').on('click', function () {
            currentPage++;
            loadTableData(currentPage);
        });

        $('#prev-page').on('click', function () {
            currentPage--;
            loadTableData(currentPage);
        });

        $(document).on('click', '.delete-icon', function (e) {
            e.preventDefault(); 
            const userId = $(this).data('id');

            if (confirm('Are you sure you want to delete this user?')) {
                $.ajax({
                    url: '<?= base_url("/delete/"); ?>' + userId, 
                    type: 'DELETE', 
                    success: function (response) {
                        alert(response.message); 
                        loadTableData(currentPage); 
                    },
                    error: function (xhr, status, error) {
                        alert('Error occurred while deleting the record.');
                        console.log(xhr.responseText); 
                    }
                });
            }
        });
    });


            $(document).ready(function () {
            loadTableData();
        });
    </script>
</body>

</html>
