<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['alogin']) == 0) {   
    header('location:index.php');
    exit();
}

$msg = '';
$error = '';
$formData = [
    'empcode' => '',
    'firstName' => '',
    'lastName' => '',
    'email' => '',
    'gender' => '',
    'employeetype' => '',
    'faculty' => '',
    'department' => '',
    'mobileno' => ''
];

if(isset($_POST['add'])) {
    // Sanitize and validate input
    $formData = array_map('trim', $_POST);
    $formData = array_map('htmlspecialchars', $formData);
    
    $empid = $formData['empcode'];
    $fname = $formData['firstName'];
    $lname = $formData['lastName'];
    $email = $formData['email'];
    $password = $formData['password'];
    $confirmPassword = $formData['confirmpassword'];
    $gender = $formData['gender'];
    $emptype = $formData['employeetype'];
    $faculty = $formData['faculty'];
    $department = $formData['department'];
    $mobileno = $formData['mobileno'];
    $status = 1;
    
    // Validation
    $errors = [];
    
    if(empty($empid)) $errors[] = "Employee Code is required";
    if(empty($fname)) $errors[] = "First Name is required";
    if(empty($lname)) $errors[] = "Last Name is required";
    if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid Email is required";
    if(empty($password)) $errors[] = "Password is required";
    if($password !== $confirmPassword) $errors[] = "Passwords do not match";
    if(empty($gender)) $errors[] = "Gender is required";
    if(empty($emptype)) $errors[] = "Employee Type is required";
    if(empty($faculty)) $errors[] = "Faculty is required";
    if(empty($department)) $errors[] = "Department is required";
    if(empty($mobileno) || !preg_match('/^[0-9]{10}$/', $mobileno)) $errors[] = "Valid 10-digit Mobile Number is required";
    
    if(empty($errors)) {
        // Check if employee ID already exists
        $check_sql = "SELECT id FROM tblemployees WHERE EmpId = :empid OR EmailId = :email";
        $check_query = $dbh->prepare($check_sql);
        $check_query->bindParam(':empid', $empid, PDO::PARAM_STR);
        $check_query->bindParam(':email', $email, PDO::PARAM_STR);
        $check_query->execute();
        
        if($check_query->rowCount() > 0) {
            $error = "Employee with this ID or Email already exists";
        } else {
            $hashedPassword = md5($password);
            $sql = "INSERT INTO tblemployees(EmpId, FirstName, LastName, EmailId, Password, Gender, DepartmentCode, Phonenumber, Status, RegDate, Faculty, employeetype) 
                    VALUES(:empid, :fname, :lname, :email, :password, :gender, :department, :mobileno, :status, NOW(), :faculty, :emptype)";
            
            $query = $dbh->prepare($sql);
            $query->bindParam(':empid', $empid, PDO::PARAM_STR);
            $query->bindParam(':fname', $fname, PDO::PARAM_STR);
            $query->bindParam(':lname', $lname, PDO::PARAM_STR);
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
            $query->bindParam(':gender', $gender, PDO::PARAM_STR);
            $query->bindParam(':emptype', $emptype, PDO::PARAM_STR);
            $query->bindParam(':faculty', $faculty, PDO::PARAM_STR);
            $query->bindParam(':department', $department, PDO::PARAM_STR);
            $query->bindParam(':mobileno', $mobileno, PDO::PARAM_STR);
            $query->bindParam(':status', $status, PDO::PARAM_INT);
            
            if($query->execute()) {
                $msg = "Employee record added successfully";
                // Clear form on success
                $formData = array_fill_keys(array_keys($formData), '');
                echo "<script>setTimeout(function(){window.location.href='manage-employees.php'},2000);</script>";
            } else {
                $error = "Something went wrong. Please try again";
            }
        }
    } else {
        $error = implode("<br>", $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Add Employee | Employee Leave Management System</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="../assets/images/favicon.ico" type="image/x-icon">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons|Material+Icons+Outlined" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link type="text/css" rel="stylesheet" href="../assets/plugins/materialize/css/materialize.min.css"/>
    <link href="../assets/plugins/material-preloader/css/materialPreloader.min.css" rel="stylesheet">
    <link href="../assets/css/alpha.min.css" rel="stylesheet" type="text/css"/>
    <link href="../assets/css/custom.css" rel="stylesheet" type="text/css"/>
    
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f5f5;
        }
        
        .card {
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            border: none;
        }
        
        .card .card-content {
            padding: 30px;
        }
        
        .page-title {
            font-size: 24px;
            margin-bottom: 30px;
            color: #444;
            font-weight: 500;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .input-field label {
            color: #666;
            font-size: 14px;
        }
        
        .input-field input[type=text]:focus + label,
        .input-field input[type=email]:focus + label,
        .input-field input[type=password]:focus + label,
        .input-field input[type=tel]:focus + label,
        .input-field select:focus + label {
            color: #800000;
        }
        
        .input-field input[type=text]:focus,
        .input-field input[type=email]:focus,
        .input-field input[type=password]:focus,
        .input-field input[type=tel]:focus,
        .input-field select:focus {
            border-bottom: 1px solid #800000;
            box-shadow: 0 1px 0 0 #800000;
        }
        
        .btn-custom {
            background-color: #800000;
            text-transform: capitalize;
            padding: 0 30px;
            height: 42px;
            line-height: 42px;
            font-size: 15px;
            border-radius: 4px;
            transition: all 0.3s;
        }
        
        .btn-custom:hover {
            background-color: #6a0a0a;
        }
        
        .errorWrap {
            padding: 15px;
            margin: 0 0 20px 0;
            background: #ffebee;
            border-left: 4px solid #f44336;
            border-radius: 0 4px 4px 0;
            color: #c62828;
            font-size: 14px;
            display: flex;
            align-items: center;
        }
        
        .succWrap {
            padding: 15px;
            margin: 0 0 20px 0;
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
            border-radius: 0 4px 4px 0;
            color: #2e7d32;
            font-size: 14px;
            display: flex;
            align-items: center;
        }
        
        .errorWrap i, .succWrap i {
            margin-right: 10px;
            font-size: 20px;
        }
        
        .btn-cancel {
            margin-left: 10px;
            background-color: #9e9e9e;
        }
        
        .btn-cancel:hover {
            background-color: #757575;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 500;
            margin: 20px 0;
            color: #444;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .radio-group label {
            margin-right: 20px;
            color: #666;
        }
        
        .radio-group input[type="radio"]:checked + span {
            color: #800000;
        }
        
        @media (max-width: 600px) {
            .btn-custom, .btn-cancel {
                width: 100%;
                margin: 5px 0;
            }
            
            .btn-cancel {
                margin-left: 0;
            }
        }
    </style>
    <script type="text/javascript">
function valid()
{
if(document.addemp.password.value!= document.addemp.confirmpassword.value)
{
alert("New Password and Confirm Password Field do not match  !!");
document.addemp.confirmpassword.focus();
return false;
}
return true;
}
</script>

<script>
function checkAvailabilityEmpid() {
$("#loaderIcon").show();
jQuery.ajax({
url: "check_availability.php",
data:'empcode='+$("#empcode").val(),
type: "POST",
success:function(data){
$("#empid-availability").html(data);
$("#loaderIcon").hide();
},
error:function (){}
});
}
</script>

<script>
function checkAvailabilityEmailid() {
$("#loaderIcon").show();
jQuery.ajax({
url: "check_availability.php",
data:'emailid='+$("#email").val(),
type: "POST",
success:function(data){
$("#emailid-availability").html(data);
$("#loaderIcon").hide();
},
error:function (){}
});
}
</script>





    </head>
    <body>
  <?php include('includes/header.php');?>
            
       <?php include('includes/sidebar.php');?>
   <main class="mn-inner">
                <div class="row">
                    <div class="col s12">
                        <div class="page-title">Add employee</div>
                    </div>
                    <div class="col s12 m12 l12">
                        <div class="card">
                            <div class="card-content">
                                <form id="example-form" method="post" action="addemployee.php" name="addemp">
                                    <div>
                                        <h3>Employee Info</h3>
                                        <section>
                                            <div class="wizard-content">
                                                <div class="row">
                                                    <div class="col m6">
                                                        <div class="row">
     <?php if($error){?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php } 
                else if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php }?>


 <div class="input-field col  s12">
<label for="empcode">Employee Code(Must be unique)</label>
<input  name="empcode" id="empcode" onBlur="checkAvailabilityEmpid()" type="text" autocomplete="off" required>
<span id="empid-availability" style="font-size:12px;"></span> 
</div>


<div class="input-field col m6 s12">
<label for="firstName">First name</label>
<input id="firstName" name="firstName" type="text" required>
</div>

<div class="input-field col m6 s12">
<label for="lastName">Last name</label>
<input id="lastName" name="lastName" type="text" autocomplete="off" required>
</div>

<div class="input-field col s12">
<label for="email">Email</label>
<input  name="email" type="email" id="email" onBlur="checkAvailabilityEmailid()" autocomplete="off" required>
<span id="emailid-availability" style="font-size:12px;"></span> 
</div>

<div class="input-field col s12">
<label for="password">Password</label>
<input id="password" name="password" type="password" autocomplete="off" required>
</div>

<div class="input-field col s12">
<label for="confirm">Confirm password</label>
<input id="confirm" name="confirmpassword" type="password" autocomplete="off" required>
</div>
</div>
</div>

<div class="col m6">
<div class="row">
<div class="input-field col m12 s12">
<select  name="employeetype" autocomplete="off">
<option value="">Employee Type</option>     
<option value="assistantlecturer">Assistant Lecturer</option>                                     
<option value="professors">Professors</option>
<option value="associateprofessors">Associate Professors</option>
<option value="lectures">Lectures</option>
<option value="temporaryassistentlecture">Temporary Assistent Lecture</option>
<option value="demostrators">Demostrators</option>
<option value="acadamicsupport">Acadamic Supoort</option>
<option value="juniorexecutive">Junior Executive</option>
<option value="medicalofficer">Medical Officer</option>
<option value="associateofficer">Associate Officer</option>
<option value="managementassistent">Management Assistent</option>
<option value="primarylevelskilled">Primary Level Skilled</option>



<option value="nonacademic">Non Academic</option>
</select>
</div>
                                                    
<div class="col m6">
<div class="row">
<div class="input-field col m12 s12">
<select  name="gender" autocomplete="off">
<option value="">Gender...</option>                                          
<option value="Male">Male</option>
<option value="Female">Female</option>
<option value="Other">Other</option>
</select>
</div>





<!-- Faculty Dropdown -->
<div class="input-field col m12 s12">
<select name="faculty" id="faculty" autocomplete="off">
  <option value="">Faculty</option>
  <?php
  $sql = "SELECT FacultyName, FacultyCode FROM tblfaculty";
  $query = $dbh->prepare($sql);
  $query->execute();
  $results = $query->fetchAll(PDO::FETCH_OBJ);
  foreach ($results as $result) {
    echo '<option value="' . htmlentities($result->FacultyCode) . '">' . htmlentities($result->FacultyName) . '</option>';
  }
  ?>
</select>
</div>


<div class="input-field col m12 s12">
<select name="department" id="department" autocomplete="off">
  <option value="">Department</option>
  <?php
  $sql = "SELECT DepartmentName, DepartmentCode FROM tbldepartments";
  $query = $dbh->prepare($sql);
  $query->execute();
  $results = $query->fetchAll(PDO::FETCH_OBJ);
  foreach ($results as $result) {
    echo '<option value="' . htmlentities($result->DepartmentCode) . '">' . htmlentities($result->DepartmentName) . '</option>';
  }
  ?>
</select>
</div>

<!-- Department Dropdown 
<div class="input-field col m12 s12">
<select name="department" id="department" autocomplete="off">
  <option value="">Department...</option>
</select>
</div>

-->


                                                            
<div class="input-field col s12">
<label for="phone">Mobile number</label>
<input id="phone" name="mobileno" type="tel" maxlength="10" autocomplete="off" required>
 </div>

                                                        
<div class="input-field col s12">
<button type="submit" name="add" onclick="return valid();" id="add" class="waves-effect waves-light btn indigo m-b-xs">ADD</button>

</div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>
                                     
                                    
                                        </section>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div class="left-sidebar-hover"></div>

        <script>
/*

document.addEventListener("DOMContentLoaded", function () {
    const facultySelect = document.getElementById("faculty");
    const departmentSelect = document.getElementById("department");

    facultySelect.addEventListener("change", function () {
        const facultyCode = this.value;

        if (facultyCode !== "") {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "getdepartment.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onload = function () {
                if (xhr.status === 200) {
                    departmentSelect.innerHTML = xhr.responseText;
                } else {
                    departmentSelect.innerHTML = '<option value="">Error loading departments</option>';
                }
            };

            xhr.send("faculty_code=" + encodeURIComponent(facultyCode));
        } else {
            departmentSelect.innerHTML = '<option value="">Department...</option>';
        }
    });
});

*/
</script>

        
        <!-- Javascripts -->
        <script src="../assets/plugins/jquery/jquery-2.2.0.min.js"></script>
        <script src="../assets/plugins/materialize/js/materialize.min.js"></script>
        <script src="../assets/plugins/material-preloader/js/materialPreloader.min.js"></script>
        <script src="../assets/plugins/jquery-blockui/jquery.blockui.js"></script>
        <script src="../assets/js/alpha.min.js"></script>
        <script src="../assets/js/pages/form_elements.js"></script>
        
    </body>
</html>
<?php ?> 