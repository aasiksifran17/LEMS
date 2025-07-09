<?php
include('includes/config.php');

if (isset($_POST['faculty_code'])) {
    $faculty_code = $_POST['faculty_code'];

    $sql = "SELECT DepartmentName, DepartmentCode FROM tbldepartments WHERE FacultyCode = :faculty";
    $query = $dbh->prepare($sql);
    $query->bindParam(':faculty', $faculty_code, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    echo '<option value="">Select Department...</option>';
    if ($results) {
        foreach ($results as $row) {
            echo '<option value="' . htmlentities($row->DepartmentCode) . '">' . htmlentities($row->DepartmentName) . '</option>';
        }
    }
}
?>
