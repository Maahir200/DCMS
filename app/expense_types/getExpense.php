<?php 
    include_once('../database/conn.php');

    if(isset($_POST['updateid'])){
        $id = $_POST['updateid'];
        $sql = "SELECT * FROM expense_types WHERE expense_type_id='$id'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        echo json_encode($row);
      }
?>