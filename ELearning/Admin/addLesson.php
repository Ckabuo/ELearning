<!-- ?php
if(!isset($_SESSION)){ 
  session_start(); 
}
define('TITLE', 'Add Lesson');
define('PAGE', 'lessons');
include('./adminInclude/header.php'); 
include('../dbConnection.php');

if(isset($_SESSION['is_admin_login'])){
  $adminEmail = $_SESSION['adminLogEmail'];
} else {
  echo "<script> location.href='../index.php'; </script>";
}

if(isset($_REQUEST['lessonSubmitBtn'])){
  // Checking for Empty Fields
  if(($_REQUEST['lesson_name'] == "") || ($_REQUEST['lesson_desc'] == "") || ($_REQUEST['course_name'] == "") || ($_FILES['lesson_link']['error'] !== UPLOAD_ERR_OK)){
    $msg = '<div class="alert alert-warning col-sm-6 ml-5 mt-2" role="alert"> Fill All Fields and select a valid course </div>';
  } else {
    // Assigning User Values to Variable
    $lesson_name = trim($_REQUEST['lesson_name']);
    $lesson_desc = trim($_REQUEST['lesson_desc']);
    $course_name = trim($_REQUEST['course_name']);
    $course_id = trim($_REQUEST['course_id']);

    // Get course ID from database based on selected course name
    $sql = "SELECT course_id FROM course WHERE course_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $course_name);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $course_id = $row['course_id'];
    } else {
      $msg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2" role="alert"> Invalid Course Selected </div>';
    }
    $stmt->close();

    // File upload handling
    $lesson_link = $_FILES['lesson_link']['name']; 
    $lesson_link_temp = $_FILES['lesson_link']['tmp_name'];
    $link_folder = '../lessonvid/'.$lesson_link; 

    if(move_uploaded_file($lesson_link_temp, $link_folder)){
      try {
        // Prepare the query using prepared statements
        $stmt = $conn->prepare("INSERT INTO lesson (lesson_name, lesson_desc, lesson_link, course_id, course_name) VALUES (?, ?, ?, ?, ?)");
        
        // Bind parameters
        $stmt->bind_param("sssss", $lesson_name, $lesson_desc, $link_folder, $course_id, $course_name);
        
        // Execute the query
        if ($stmt->execute()) {
          $msg = '<div class="alert alert-success col-sm-6 ml-5 mt-2" role="alert"> Lesson Added Successfully </div>';
        } else {
          $msg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2" role="alert"> Unable to Add Lesson: ' . $stmt->error . '</div>';
        }
        
        $stmt->close();
      } catch (Exception $e) {
        $msg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2" role="alert"> An error occurred: ' . $e->getMessage() . '</div>';
      }
    } else {
      $msg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2" role="alert"> Failed to upload file </div>';
    }
  }
}

// Fetch courses from the database
$courses = [];
$sql = "SELECT course_id, course_name FROM course";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
  }
}
?> -->
<?php
if(!isset($_SESSION)){ 
  session_start(); 
}
define('TITLE', 'Add Lesson');
define('PAGE', 'lessons');
include('./adminInclude/header.php'); 
include('../dbConnection.php');

 if(isset($_SESSION['is_admin_login'])){
  $adminEmail = $_SESSION['adminLogEmail'];
 } else {
  echo "<script> location.href='../index.php'; </script>";
 }

 if(isset($_REQUEST['lessonSubmitBtn'])){
  // Checking for Empty Fields
  if(($_REQUEST['lesson_name'] == "") || ($_REQUEST['lesson_desc'] == "") || ($_REQUEST['course_id'] == "") || ($_REQUEST['course_name'] == "")){
   // msg displayed if required field missing
   $msg = '<div class="alert alert-warning col-sm-6 ml-5 mt-2" role="alert"> Fill All Fileds </div>';
  } else {
   // Assigning User Values to Variable
   $lesson_name = trim($_REQUEST['lesson_name']);
   $lesson_desc = trim($_REQUEST['lesson_desc']);
   $course_id = trim($_REQUEST['course_id']);
   $course_name = trim($_REQUEST['course_name']);
   
   // File upload handling
   if(isset($_FILES['lesson_link']) && $_FILES['lesson_link']['error'] === UPLOAD_ERR_OK){
    $lesson_link = $_FILES['lesson_link']['name']; 
    $lesson_link_temp = $_FILES['lesson_link']['tmp_name'];
    $link_folder = '../lessonvid/'.$lesson_link; 

    if(move_uploaded_file($lesson_link_temp, $link_folder)){
      try {
        // Prepare the query using prepared statements
        $stmt = $conn->prepare("INSERT INTO lesson (lesson_name, lesson_desc, lesson_link, course_id, course_name) VALUES (?, ?, ?, ?, ?)");
        
        // Bind parameters
        $stmt->bind_param("sssss", $lesson_name, $lesson_desc, $link_folder, $course_id, $course_name);
        
        // Execute the query
        if ($stmt->execute()) {
          $msg = '<div class="alert alert-success col-sm-6 ml-5 mt-2" role="alert"> Lesson Added Successfully </div>';
        } else {
          $msg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2" role="alert"> Unable to Add Lesson: ' . $stmt->error . '</div>';
        }
        
        $stmt->close();
      } catch (Exception $e) {
        $msg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2" role="alert"> An error occurred: ' . $e->getMessage() . '</div>';
      }
    } else {
      $msg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2" role="alert"> Failed to upload file </div>';
    }
  } else {
    $msg = '<div class="alert alert-warning col-sm-6 ml-5 mt-2" role="alert"> Please select a valid file </div>';
  }
}
}

  // Fetch courses from the database
  $courses = [];
  $sql = "SELECT course_id, course_name FROM course";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $courses[] = $row;
    }
  }

  // Convert $courses to a JSON string
$jsonCourses = json_encode($courses);

// Close the database connection
$conn->close();
?>

<div class="col-sm-6 mt-5  mx-3 jumbotron">
  <h3 class="text-center">Add New Lesson</h3>
  <form action="" method="POST" enctype="multipart/form-data">
    <div class="form-group">
      <label for="course_name">Course Name</label>
      <select class="form-control" id="course_name" name="course_name" onchange="updateCourseId(this)">
        <option value="">Select Course</option>
        <?php
        foreach ($courses as $course) {
          echo '<option value="' . $course['course_name'] . '">' . $course['course_name'] . '</option>';
        }
        ?>
      </select>
    </div>
    <div class="form-group">
      <label for="course_id">Course ID</label>
       <input type="text" class="form-control" id="course_id" name="course_id" readonly>
    </div>
    <div class="form-group">
      <label for="lesson_name">Lesson Name</label>
      <input type="text" class="form-control" id="lesson_name" name="lesson_name">
    </div>
    <div class="form-group">
      <label for="lesson_desc">Lesson Description</label>
      <textarea class="form-control" id="lesson_desc" name="lesson_desc" row=2></textarea>
    </div>
    <div class="form-group">
      <label for="lesson_link">Lesson Video Link</label>
      <input type="file" class="form-control-file" id="lesson_link" name="lesson_link">
    </div>
    <div class="text-center">
      <button type="submit" class="btn btn-danger" id="lessonSubmitBtn" name="lessonSubmitBtn">Submit</button>
      <a href="lessons.php" class="btn btn-secondary">Close</a>
    </div>
    <?php if(isset($msg)) {echo $msg; } ?>
  </form>
</div>
<!-- Only Number for input fields -->
<script>
  //  function updateCourseId() {
  //   let select = document.getElementById('course_name');
  //   let courseId = select.options[select.selectedIndex].value;
  //   document.getElementById('course_id').value = courseId;
  // }
  // Define the courses array in JavaScript
  const courses = <?= $jsonCourses; ?>;

  function updateCourseId(selectElement) {
    let selectedOption = selectElement.selectedOptions[0];
    let courseName = selectedOption.value;
    
    // Find the matching course in the courses array
    let matchingCourse = courses.find(course => course.course_name === courseName);
    
    if (matchingCourse) {
      let courseId = matchingCourse.course_id;
      document.getElementById('course_id').value = courseId;
    }
  }

  function isInputNumber(evt) {
    var ch = String.fromCharCode(evt.which);
    if (!(/[0-9]/.test(ch))) {
      evt.preventDefault();
    }
  }
</script>

<?php
include('./adminInclude/footer.php'); 
?>