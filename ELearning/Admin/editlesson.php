<?php 
if(!isset($_SESSION)){ 
  session_start(); 
}
define('TITLE', 'Edit Lesson');
define('PAGE', 'lessons');
include('./adminInclude/header.php'); 
include('../dbConnection.php');

 if(isset($_SESSION['is_admin_login'])){
  $adminEmail = $_SESSION['adminLogEmail'];
 } else {
  echo "<script> location.href='../index.php'; </script>";
 }
 // Update
 if(isset($_REQUEST['requpdate'])){

  // Checking for Empty Fields
  if(($_REQUEST['lesson_id'] == "") || ($_REQUEST['lesson_name'] == "") || ($_REQUEST['lesson_desc'] == "") || ($_REQUEST['course_id'] == "") || ($_REQUEST['course_name'] == "")){

   // msg displayed if required field missing
   $msg = '<div class="alert alert-warning col-sm-6 ml-5 mt-2" role="alert"> Fill All Fileds </div>';
  } else {
    
    // Assigning User Values to Variable
    $lid = trim($_REQUEST['lesson_id']);
    $lname = trim($_REQUEST['lesson_name']);
    $ldesc = htmlspecialchars(trim($_REQUEST['lesson_desc']));
    $cid = trim($_REQUEST['course_id']);
    $cname = trim($_REQUEST['course_name']);

    // Check if a file was uploaded
    if(isset($_FILES['lesson_link']) && $_FILES['lesson_link']['tmp_name'] != '') {

      // Delete old file if it exists
      if (isset($row['lesson_link']) && file_exists($upload_dir . basename($row['lesson_link']))) {
        unlink($upload_dir . basename($row['lesson_link']));
      }

      // Get the original filename
      $original_filename = basename($_FILES['lesson_link']['name']);

      // Move the uploaded file to the correct directory
      $upload_dir = '../lessonvid/';
      $new_filename = uniqid() . '_' . $original_filename;
      $move_result = move_uploaded_file($_FILES['lesson_link']['tmp_name'], $upload_dir . $new_filename);

      if ($move_result) {
        $llink = $upload_dir . $new_filename;
        
        // Update the database with the new file path
        // $sql = "UPDATE lesson SET lesson_id = '$lid', lesson_name = '$lname', lesson_desc = '$ldesc', course_id='$cid', course_name='$cname', lesson_link='$llink' WHERE lesson_id = '$lid'";
        $sql = "UPDATE lesson SET lesson_id = '$lid', lesson_name = '$lname', lesson_desc = '" . mysqli_real_escape_string($conn, $ldesc) . "', course_id='$cid', course_name='$cname', lesson_link='$llink' WHERE lesson_id = '$lid'";
        if($conn->query($sql) == TRUE){
          // below msg display on form submit success
          $msg = '<div class="alert alert-success col-sm-6 ml-5 mt-2" role="alert"> Updated Successfully </div>';
        } else {
          // below msg display on form submit failed
          $msg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2" role="alert"> Unable to Update </div>';
        }
      } else {
        // Handle move_uploaded_file failure
        $msg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2" role="alert"> Failed to move uploaded file </div>';
      }
    } else {
      // No file was uploaded, use existing file path
      $llink = isset($row['lesson_link']) ? $row['lesson_link'] : '';
      
      $sql = "UPDATE lesson SET lesson_id = '$lid', lesson_name = '$lname', lesson_desc = '$ldesc', course_id='$cid', course_name='$cname', lesson_link='$llink' WHERE lesson_id = '$lid'";
      if($conn->query($sql) == TRUE){
        // below msg display on form submit success
        $msg = '<div class="alert alert-success col-sm-6 ml-5 mt-2" role="alert"> Updated Successfully </div>';
      } else {
        // below msg display on form submit failed
        $msg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2" role="alert"> Unable to Update </div>';
      }
    }
  }
}

 ?>
<div class="col-sm-6 mt-5  mx-3 jumbotron">
  <h3 class="text-center">Update Lesson Details</h3>
  <?php
 if(isset($_REQUEST['view'])){
  $sql = "SELECT * FROM lesson WHERE lesson_id = {$_REQUEST['id']}";
 $result = $conn->query($sql);
 $row = $result->fetch_assoc();
 }
 ?>
  <form action="" method="POST" enctype="multipart/form-data">
    <div class="form-group">
      <label for="lesson_id">Lesson ID</label>
      <input type="text" class="form-control" id="lesson_id" name="lesson_id" value="<?php if(isset($row['lesson_id'])) {echo $row['lesson_id']; }?>" readonly>
    </div>
    <div class="form-group">
      <label for="lesson_name">Lesson Name</label>
      <input type="text" class="form-control" id="lesson_name" name="lesson_name" value="<?php if(isset($row['lesson_name'])) {echo $row['lesson_name']; }?>">
    </div>

    <div class="form-group">
      <label for="lesson_desc">Lesson Description</label>
      <textarea class="form-control" id="lesson_desc" name="lesson_desc" row=2><?php if(isset($row['lesson_desc'])) {echo $row['lesson_desc']; }?></textarea>
    </div>
    <div class="form-group">
      <label for="course_id">Course ID</label>
      <input type="text" class="form-control" id="course_id" name="course_id" value="<?php if(isset($row['course_id'])) {echo $row['course_id']; }?>" readonly>
    </div>
    <div class="form-group">
      <label for="course_name">Course Name</label>
      <input type="text" class="form-control" id="course_name" name="course_name" onkeypress="isInputNumber(event)" value="<?php if(isset($row['course_name'])) {echo $row['course_name']; }?>" readonly>
    </div>
    <div class="form-group">
      <label for="lesson_link">Lesson Link</label>
      <div class="embed-responsive embed-responsive-16by9">
       <!-- <iframe class="embed-responsive-item" src="?php if(isset($row['lesson_link'])) {echo $row['lesson_link']; }?>" allowfullscreen></iframe> -->
       <?php if(isset($row['lesson_link'])): ?>
      <video class="embed-responsive-item" controls>
        <source src="<?php echo htmlspecialchars($row['lesson_link']); ?>" type="video/mp4">
        Your browser does not support the video tag.
      </video>
    <?php endif; ?>
      </div>     
      <input type="file" class="form-control-file" id="lesson_link" name="lesson_link">
    </div>

    <div class="text-center">
      <button type="submit" class="btn btn-danger" id="requpdate" name="requpdate">Update</button>
      <a href="lessons.php" class="btn btn-secondary">Close</a>
    </div>
    <?php if(isset($msg)) {echo $msg; } ?>
  </form>
</div>
<!-- </div>  div Row close from header -->
<!-- </div>  div Conatiner-fluid close from header -->

<?php
include('./adminInclude/footer.php'); 
?>