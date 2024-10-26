 <?php 
include('./dbConnection.php');
session_start(); 

if (!isset($_SESSION['stuLogEmail'])) {  
    echo "<script> location.href='loginorsignup.php'; </script>"; 
    exit();
}

header("Pragma: no-cache");  
header("Cache-Control: no-cache");  
header("Expires: 0");   

$stuEmail = $_SESSION['stuLogEmail'];  
$conn = new mysqli($db_host, $db_user, $db_password, $db_name);    

if ($conn->connect_error) {    
    die("Connection failed: ". $conn->connect_error);  
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buy'])) {
    try {    
        // Start transaction    
        mysqli_autocommit($conn, false);    
        mysqli_query($conn, "BEGIN");        

        // Create order      
        $orderId = $_POST['ORDER_ID'];    
        $amount = $_POST['COURSE_PRICE'];
        $course_id = $_POST['COURSE_ID']; 
        $status = "TXN_SUCCESS";    
        $respmsg = "Payment successful";    
        $orderDate = date('Y-m-d');    
        $sql = "INSERT INTO `courseorder` (`co_id`, `order_id`, `stu_email`, `course_id`, `status`, `respmsg`, `amount`, `order_date`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";    
        $stmt = mysqli_prepare($conn, $sql);    

        mysqli_stmt_bind_param($stmt, "ississis", $co_id, $orderId, $stuEmail, $course_id, $status, $respmsg, $amount, $orderDate);    

        if (mysqli_stmt_execute($stmt)) {      
            echo "<div class='alert alert-success'>Order created successfully!</div>";            
            // Commit the transaction      
            mysqli_commit($conn);    
                            
        } else {      
            echo "<div class='alert alert-danger'>Failed to create order.</div>";            
            // Rollback the transaction      
            mysqli_rollback($conn);    
        }    

        mysqli_close($conn);  
    } catch (Exception $e) {    
        echo "<div class='alert alert-danger'>An error occurred: " . $e->getMessage() . "</div>";  
    }
}

?>

<!DOCTYPE html>  
<html lang="en">  
<head>   
    <meta charset="UTF-8">   
    <meta name="GENERATOR" content="Evrsoft First Page">   
    <meta name="viewport" content="width=device-width, initial-scale=1.0">   
    <meta http-equiv="X-UA-Compatible" content="ie=edge">    
    <!-- Bootstrap CSS -->    
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">    
    <!-- Font Awesome CSS -->    
    <link rel="stylesheet" type="text/css" href="css/all.min.css">    
    <!-- Google Font -->    
    <link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">    
    <!-- Custom Style CSS -->    
    <link rel="stylesheet" type="text/css" href="./css/style.css" />        
    <title>ELearning</title>   
    <title>Checkout</title>  
</head>  
<body>  
    <div class="container mt-5">    
        <div class="row">    
            <div class="col-sm-6 offset-sm-3 jumbotron">    
                <h3 class="mb-5">Welcome to E-Learning Payment Page</h3>     
                <form method="post" action="./checkout.php">   
                    <input type="hidden" name="form_id" value="checkout_form">   
                    <div class="form-group row">       
                        <label for="ORDER_ID" class="col-sm-4 col-form-label">Order ID</label>       
                        <div class="col-sm-8">         
                            <input id="ORDER_ID" class="form-control" tabindex="1" maxlength="20" size="20" name="ORDER_ID" autocomplete="off" value="<?php echo "ORDS" . rand(10000,99999999)?>" readonly>       
                        </div>      
                    </div>      
                    <div class="form-group row">       
                        <label for="CUST_ID" class="col-sm-4 col-form-label">Student Email</label>       
                        <div class="col-sm-8">         
                            <input id="CUST_ID" class="form-control" tabindex="2" maxlength="12" size="12" name="CUST_ID" autocomplete="off" value="<?php if(isset($stuEmail)){echo $stuEmail; }?>" readonly>       
                        </div>      
                    </div>      
                    <div class="form-group row">       
                        <label for="COURSE_PRICE" class="col-sm-4 col-form-label">Amount</label>       
                        <div class="col-sm-8">        
                             <input title="COURSE_PRICE" class="form-control" tabindex="10" type="text" name="COURSE_PRICE" value="<?php if(isset($_POST['COURSE_PRICE'])){echo $_POST['COURSE_PRICE']; }?>" readonly required>     
                        </div>      
                    </div>      
                    <div class="form-group row">        
                        <div class="col-sm-8">          
                            <!-- <input type="hidden" id="courseId" class="form-control" tabindex="4" maxlength="12" size="12" name="COURSE_ID" autocomplete="off" value="?php echo isset($course_id) ?>"> -->
                            <input type="hidden" id="courseId" title="COURSE_ID" class="form-control" tabindex="4" maxlength="12" size="12" name="COURSE_ID" autocomplete="off" value="<?php if(isset($_POST['COURSE_ID'])){echo $_POST['COURSE_ID']; } ?>">                
                        </div>      
                    </div>      
                    <div class="text-center">       
                        <input value="Check out" type="submit" class="btn btn-primary" name="buy">       
                        <a href="./courses.php" class="btn btn-secondary">Cancel</a>      
                    </div>     
                </form>     
                <small class="form-text text-muted text-center"><strong>Note:</strong> Complete Payment by Clicking Checkout Button</small>     
            </div>    
        </div>  
    </div>    

    <!-- Jquery and Boostrap JavaScript -->    
    <script type="text/javascript" src="js/jquery.min.js"></script>    
    <script type="text/javascript" src="js/popper.min.js"></script>    
    <script type="text/javascript" src="js/bootstrap.min.js"></script>    
    <!-- Font Awesome JS -->    
    <script type="text/javascript" src="js/all.min.js"></script>    
    <!-- Custom JavaScript -->    
    <script type="text/javascript" src="js/custom.js"></script>  
</body>  
</html>
