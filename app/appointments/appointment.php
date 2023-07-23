<?php
    //connect to database
    include_once('./app/database/conn.php');
    // include_once('../../includes/header.php')
?>
<head>
    <style>
        :root {
            --bs-success-rgb: 71, 222, 152 !important;
        }

        html,
        body {
            height: 100%;
            width: 100%;
        }

        .btn-info.text-light:hover,
        .btn-info.text-light:focus {
            background: #000;
        }

        table,
        tbody,
        td,
        tfoot,
        th,
        thead,
        tr {
            border-color: #ededed !important;
            border-style: solid;
            border-width: 1px !important;
        }
        #gg{
            color: green;
            background-color: chocolate;

        }
    </style>
</head>

<body>
    <div class="container py-1 mx-auto" id="page-container">
        <div class="row overflow-auto shadow p-3 rounded">
            <div class="col-md-8">
                <div id="calendar"></div>
            </div>
            <div class="col-md-4">
                <div class="cardt rounded shadow">
                    <div class="card-header bg-gradient bg-primary text-light">
                        <h5 class="card-title p-2">appointments Form</h5>
                    </div>
                    <div class="card-body">
                        <div class="container-fluid">
                            <form action="./app/appointments/save.php" method="post" id="schedule-form">
                                <input type="hidden" name="id" value="">

                                <div class="form-group mb-2">
                                    <label for="title" class="control-label">Status</label> <br>
                                    <!-- select appointment status  -->
                                    <select class="form-control select2"  name="status" id="status" REQUIRED>
                                        <option value="">Select Status</option>
                                        <option value="Arrived">Arrived</option>
                                        <option value="In Room">In Room</option>
                                        <option value="Finished">Finished</option>
                                        <option value="Pending">Pending</option>
                                        <option value="No Show">No Show</option>
                                        <option value="LWBS">Left Without Being Seen</option>
                                        <option value="Cancelled">Cancelled</option>
                                    </select>
                                </div>
                                <div class="form-group mt-2 mb-3">
                                    <label for="patients" class="control-label">Patients</label><br>
                                    <select class="form-control select2 " id="patients" name="patients" REQUIRED>
                                        <option   value="">Select Patients</option>
                                        <?php
                                        $query = "SELECT * FROM `patients`";
                                        $result = mysqli_query($conn, $query);
                                        while ($row = mysqli_fetch_array($result)) {
                                            echo "<option value='" . $row['patient_id'] . "'>" . $row['first_name'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group mt-2 mb-3">
                                    <label for="dentist" class="control-label">Dentist:</label><br>
                                    <select class="form-control select2 " id="dentist" name="dentist" REQUIRED>
                                        <option  value="">Select Dentist</option>
                                        <?php
                                        $query = "SELECT * FROM `addresses_employees_view` WHERE role_name = 'Dentist';                                        ";
                                        $result = mysqli_query($conn, $query);
                                        while ($row = mysqli_fetch_array($result)) {
                                            echo "<option value='" . $row['employee_id'] . "'>" . $row['first_name'] . ' '. $row['last_name'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group mb-2">
                                    <label for="service" class="control-label">Service </label><br>
                                    <select class="form-control select2" id="service" name="service" REQUIRED>
                                        <option value="">Select service </option>
                                        <?php
                                        $query = "SELECT * FROM `services`";
                                        $result = mysqli_query($conn, $query);
                                        while ($row = mysqli_fetch_array($result)) {
                                            echo "<option value='" . $row['service_id'] . "'>" . $row['name'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group mb-2">
                                    <label for="start_datetime" class="control-label">Start</label>
                                    <input type="datetime-local" class="form-control form-control-sm rounded-0" name="start_datetime" id="start_datetime" required>
                                </div>
                                <div class="form-group mb-2">
                                    <label for="end_datetime" class="control-label">End</label>
                                    <input type="datetime-local" class="form-control form-control-sm rounded-0" name="end_datetime" id="end_datetime" required>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-footer p-2">
                        <div class="text-center">
                            <button class="btn btn-primary btn-sm rounded" type="submit" form="schedule-form"><i class="fa fa-save"></i> Save</button>
                            <button class="btn btn-default border btn-sm rounded" type="reset" form="schedule-form"><i class="fa fa-reset"></i> Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Event Details Modal -->
    <div class="modal fade" tabindex="-1" data-bs-backdrop="static" id="event-details-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded">
                <div class="modal-header rounded">
                    <h5 class="modal-title">appointments Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body rounded">
                    <div class="container-fluid">
                        <dl>
                            <dt class="text-muted">Patient</dt>
                            <dd id="patient" class=""></dd>
                            <dt class="text-muted">Dentist</dt>
                            <dd id="e_dentist" class=""></dd>
                            <dt class="text-muted">Start</dt>
                            <dd id="start" class=""></dd>
                            <dt class="text-muted">End</dt>
                            <dd id="end" class=""></dd>
                        </dl>
                    </div>
                </div>
                <div class="modal-footer rounded">
                    <div class="text-end">
                        <button type="button" class="btn btn-primary btn-sm rounded" id="edit" data-id="">Edit</button>
                        <button type="button" class="btn btn-danger btn-sm rounded" id="delete" data-id="">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Event Details Modal -->


    <?php
    // Get all appointments
    $schedules = $conn->query("SELECT * FROM `appointmentdetails`");
    if (!$schedules) {
        // echo "Error: " . $conn->error;
        echo "no appointment found";
    } else {
        foreach ($schedules->fetch_all(MYSQLI_ASSOC) as $row) {
            // Format the start and end dates
            $row['sdate'] = date("F d, Y h:i A", strtotime($row['start_date']));
            $row['edate'] = date("F d, Y h:i A", strtotime($row['end_date']));

            // Add the appointment to the array
            $sched_res[$row['appointment_id']] = $row;
        }
    }
    ?>
    <?php
    ?>

<script>
    var scheds = $.parseJSON('<?= json_encode($sched_res) ?>');
    if (scheds == null) {
        alert('no appointment found');
    }
    //document ready
    $(document).ready(function() {
        $('.select2').select2();
        console.log('JSON.stringify(scheds)'); 

        $('#schedule-form').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                success: function(resp) {
                    // alert(resp);
                    var obj = jQuery.parseJSON(resp);
                    if (obj.status == 200) {
                        //reload calendar
                        calendar.refetchEvents();
                        //reload page
                        location.reload();
                    }
                    if (obj.status == 404) {
                        alert(obj.message);
                    }
                }
            });
        });

        $("#edit").click(function() {
            var id = $(this).attr('data-id');
            var sched = scheds[id];
            // alert(JSON.stringify(sched));
            $('#schedule-form input[name="id"]').val(sched.appointment_id);
            $('#schedule-form select[name="status"]').val(sched.status).trigger('change');
            $('#schedule-form select[name="patients"]').val(sched.patient_id).trigger('change');
            $('#schedule-form select[name="dentist"]').val(sched.employee_id).trigger('change');
            $('#schedule-form select[name="service"]').val(sched.service_id).trigger('change');
            $('#schedule-form input[name="start_datetime"]').val(sched.start_date);
            $('#schedule-form input[name="end_datetime"]').val(sched.end_date);
            $('#schedule-form').attr('action', './app/appointments/save.php');
            $('#event-details-modal').modal('hide');
        });

        $("#delete").click(function() {
            var id = $(this).attr('data-id')
            if (!!scheds[id]) {
                var _conf = confirm("Are you sure to delete this scheduled event?");
                if (_conf === true) {
                    //location.href = "appointments/delete.php?id=" + scheds[id].appointment_id;

                    $.ajax({
                        url: "./app/appointments/delete.php",
                        type: "post",
                        data: {
                            id: scheds[id].appointment_id
                        },
                        success: function(data) {
                            var obj = jQuery.parseJSON(data);
                            if (obj.status == 200) {
                                //close modal 
                                $('#event-details-modal').modal('hide');
                                //reload calendar
                                calendar.refetchEvents();
                                //reload page
                                location.reload();
                            }
                            if (obj.status == 404) {
                                // $("#state").text(obj.message);
                                alert(obj.message);
                            }
                        }
                    });

                }
            } else {
                alert("Event is undefined");
            }
        });

        // on reset
        $('#schedule-form').on('reset', function() {
            $('#schedule-form').attr('action', './app/appointments/save.php');
            $('#schedule-form input[name="id"]').val('');
            $('#schedule-form select[name="status"]').val('').trigger('change');
            $('#schedule-form select[name="patients"]').val('').trigger('change');
            $('#schedule-form select[name="dentist"]').val('').trigger('change');
            $('#schedule-form select[name="service"]').val('').trigger('change');
            $('#schedule-form input[name="start_datetime"]').val('');
            $('#schedule-form input[name="end_datetime"]').val('');
        });

    });
    //onsubmit schedule-form 
</script>
<script src="./app/appointments/app.js"></script>
</body>