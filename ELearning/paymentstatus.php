<?php
  include('./dbConnection.php');

  // Header Include from mainInclude 
  include('./mainInclude/header.php'); 
  header("Pragma: no-cache");
  header("Cache-Control: no-cache");
  header("Expires: 0");
  
	$ORDER_ID = "";
	$requestParamList = array();

  if (isset($_POST["ORDER_ID"]) && $_POST["ORDER_ID"] != "") {
    $ORDER_ID = $_POST["ORDER_ID"];
    
    // Fetching order status directly from the database
    $sql = "SELECT * FROM courseorder WHERE order_id = '$ORDER_ID'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {

      $responseParamList = $result->fetch_assoc();

    } else {
      $error_message = "<div class='alert alert-danger text-center'>No order found with the given Order ID.</div>";
    }
  }

?>  

   <div class="container-fluid bg-dark"> <!-- Start Course Page Banner -->
     <div class="row">
       <img src="./image/coursebanner.jpg" alt="courses" style="height:300px; width:100%; object-fit:cover; box-shadow:10px;"/>
     </div> 
   </div> <!-- End Course Page Banner -->
   <div class="container">
     <h2 class="text-center my-4">Payment Status </h2>
     <form method="post" action="">
     <div class="form-group row">
        <label class="offset-sm-3 col-form-label">Order ID: </label>
        <div>
          <input class="form-control mx-3" id="ORDER_ID" tabindex="1" maxlength="20" size="20" name="ORDER_ID" autocomplete="off" value="<?php echo $ORDER_ID ?>">
        </div>
        <div>
          <input class="btn btn-primary mx-4" value="View" type="submit"	onclick="">
        </div>
      </div>
     </form>
    </div>
    <div class="container">
    <?php
      if (!empty($error_message)) {
        echo $error_message;
      }
    ?>

    <?php
      if (isset($responseParamList) && count($responseParamList) > 0) {
        ?>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h2 class="text-center">Payment Receipt</h2>
                <table class="table table-bordered table-striped">
                    <tbody>
                    <?php
                    foreach ($responseParamList as $paramName => $paramValue) {
                        ?>
                        <tr>
                            <td><strong><?php echo $paramName ?></strong></td>
                            <td><?php echo $paramValue ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <td></td>
                        <td><button class="btn btn-primary" onclick="javascript:window.print();">Print Receipt</button></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
      }
    ?>

  </div>  
<div class="mt-5">
<?php 
  // Contact Us
  include('./contact.php'); 
?> 
</div>

<?php 
  // Footer Include from mainInclude 
  include('./mainInclude/footer.php'); 
?>  
