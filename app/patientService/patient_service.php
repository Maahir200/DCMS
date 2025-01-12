<!DOCTYPE html>
<html>

<head>
    <title>Staff Page</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
    include_once('./app/database/conn.php');
    ?>
</head>

<body>

    <div class="container p-3 rounded shadow bg-white overflow-auto">
        <form id="formPS" class="m-2">
            <div>
                <h3 class="text-center text-white bg-primary p-2 rounded shadow">Patient Service</h3>
            </div>
            <div class="row m-5">
                <!-- hidden id input -->
                <input type="hidden" name="id" id="id">
                <label for="patient_id" class="form-label ">Patient Name</label>
                <select class="form-control select2" id="patient_id" name="patient_id" REQUIRED>
                    <option value="">Select Patient</option>
                    <?php
                    $query = 'SELECT * FROM `patients`';
                    $result = mysqli_query($conn, $query);
                    while ($row = mysqli_fetch_array($result)) {
                        echo '<option value=' . $row['patient_id'] . '>' . $row['first_name'] . ' ' . $row['last_name'] . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="input-field">
                <table class="table table-bordered" id="servicetable">
                    <thead>
                        <tr>
                            <th scope="col">Service</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Cost</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select class="form-control select2" id="service_id" name="service_id[]" REQUIRED>
                                    <option value="">Select Service</option>
                                    <?php
                                    $query = 'SELECT * FROM `services`';
                                    $result = mysqli_query($conn, $query);
                                    while ($row = mysqli_fetch_array($result)) {
                                        echo '<option value=' . $row['service_id'] . '>' . $row['name'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                            <td><input type="number" class="form-control" id="quantity" name="quantity[]" REQUIRED></td>
                            <td><input type="number" class="form-control" id="cost" name="cost[]" REQUIRED></td>
                            <td><button class="btn btn-warning" name="addNewRec" id="addNewRec"><i class="fas fa-plus"></i></button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <CENTER>
                <button type="submit" class="btn btn-primary" name="submit" id="submit">Submit</button>
            </CENTER>
        </form>

        <!-- display area -->
        <div id="display">
            <table class="table table-bordered" id="dataTable">
                <thead>
                    <tr>
                        <th scope="col">Patient Name</th>
                        <th scope="col">Service</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Cost</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = 'SELECT * FROM `patient_service_view`';
                    $result = mysqli_query($conn, $query);
                    while ($row = mysqli_fetch_array($result)) {
                    ?>
                        <tr>
                            <td> <?php echo  htmlspecialchars ($row['first_name']) . ' ' . htmlspecialchars ( $row['last_name']) ?> </td>
                            <td> <?php echo  htmlspecialchars ($row['Services']) ?></td>
                            <td> <?php echo  htmlspecialchars ($row['Quantity']) ?></td>
                            <td> <?php echo  htmlspecialchars ($row['Total']) ?></td>
                            <td class="text-center">
                                <button class="btn btn-primary" onclick="ViewpatientService(<?php echo $row['patient_id']; ?>)" name='view' id='view'>
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-primary " onclick="EditpatientService(<?php echo $row['patientService_id']; ?>)" name="edit" id="edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger" onclick="DeletepatientService(<?php echo $row['patientService_id']; ?>)" name="delete" id="delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>



    <script>
        //   
        $(document).ready(function() {
            hideLoader();
            $('.select2').select2();
            $('#dataTable').DataTable({
                pagingType: 'full_numbers',
                "aLengthMenu": [
                    [5, 10, , 20, 50, 75, -1],
                    [5, 10, 20, 50, 75, "All"]
                ],
                "iDisplayLength": 5,
                "bDestroy": true
            });

            var html = '';
            // When someone clicks on the add button, create a new col and append it to the row and change the add icon to remove icon  
            html += '<tr>';
            html += '<td><select class="form-control select2" id="service_id" name="service_id[]" REQUIRED><option value="">Select Service</option><?php $query = "SELECT * FROM `services`";
                                                                                                                                                    $result = mysqli_query($conn, $query);
                                                                                                                                                    while ($row = mysqli_fetch_array($result)) {
                                                                                                                                                        echo "<option value=" . $row["service_id"] . ">" . $row["name"] . "</option>";
                                                                                                                                                    } ?></select></td>';
            html += '<td><input type="number" class="form-control" id="quantity" name="quantity[]" REQUIRED></td>';
            html += '<td><input type="number" class="form-control" id="cost" name="cost[]" REQUIRED></td>';
            html += '<td><button class="btn btn-danger" name="removeRec" id="removeRec"><i class="fas fa-minus"></i></button></td>';
            html += '</tr>';
            $('#addNewRec').click(function() {

                // append the new row to the table
                $('#servicetable').append(html);
                $('.select2').select2();
                // $('.select2').removeAttr('select2-hidden-accessible');
            });

            // When someone clicks on the remove button, remove the col and change the remove icon to add icon
            $("#servicetable").on('click', '#removeRec', function() {
                $(this).closest('tr').remove();
            });

            //on submit
            $('#formPS').on('submit', function(event) {
                event.preventDefault();
                showLoader();
                $.ajax({
                    url: "./app/patientService/Save.php",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function(data) {
                        alert(data);
                        location.reload();
                        hideLoader();
                    },
                    error: function(data) {
                        alert(data);
                        hideLoader();
                    },
                    complete: function(data) {
                        hideLoader();
                    }
                });
            });


        });
        //on delete
        function DeletepatientService(id) {
            id = id;
            showLoader();
            $.ajax({
                url: "./app/patientService/delete.php",
                method: "POST",
                data: {
                    id: id
                },
                success: function(data) {
                    // alert(data);
                    obj = JSON.parse(data);
                    if (obj.status == 200) {
                        // alert(obj.message);
                        location.reload();
                        hideLoader();
                    } else {
                        alert(obj.message);
                        hideLoader();
                    }
                },
                error: function(data) {
                    alert(data);
                    hideLoader();
                },
                complete: function(data) {
                    hideLoader();
                }
            });
        }

        //on edit
        function EditpatientService(id) {
            id = id;
            showLoader();
            $.ajax({
                url: "./app/patientService/edit.php",
                method: "POST",
                data: {
                    id: id
                },
                success: function(data) {
                    // alert(data);
                    var obj = JSON.parse(data);
                    $('#id').val(obj.patientService_id);
                    $('#formPS  select[name="patient_id"]').val(obj.patient_id).trigger('change');
                    $('#formPS  select[name="service_id[]"]').val(obj.service_id).trigger('change');
                    $('#quantity').val(obj.quantity);
                    $('#cost').val(obj.cost);
                    $('#submit').html('Update');
                    // hide view modal
                    $('#viewModal').modal('hide');
                    hideLoader();
                },
                error: function(data) {
                    alert(data);
                    hideLoader();
                },
                complete: function(data) {
                    hideLoader();
                }
            });

        }
        //on view
        function ViewpatientService(id) {
            id = id;
            // create a small pop up modal to show the details of the patientService
            showLoader();
            $.ajax({
                url: "./app/patientService/view.php",
                method: "POST",
                data: {
                    id: id
                },
                success: function(data) {
                    // alert(data);
                    // to show the modal
                    $('#modalDisplay').html(data);
                    $('#viewModal').modal('show');
                    hideLoader();
                },
                error: function(data) {
                    alert(data);
                    hideLoader();
                },
                complete: function(data) {
                    hideLoader();
                }
            });

        }
    </script>

    <div id='modalDisplay'>

    </div>

    <!-- Q: Do you see any unclosed brackets? -->