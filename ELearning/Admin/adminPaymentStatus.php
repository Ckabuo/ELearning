<?php
define('TITLE', 'Payment Status');
define('PAGE', 'paymentstatus');
include('./adminInclude/header.php');

header("Pragma: no-cache");
header("Cache-Control: no-cache");
header("Expires: 0");

include('../dbConnection.php');

$ORDER_ID = "";
$responseParamList = array();

if (isset($_POST["ORDER_ID"]) && $_POST["ORDER_ID"] != "") {
    $ORDER_ID = $_POST["ORDER_ID"];
    // Fetching order status directly from the database
    $sql = "SELECT * FROM courseorder WHERE order_id = '$ORDER_ID'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $responseParamList = $result->fetch_assoc();
    } else {
        echo "<div class='alert alert-danger text-center'>No order found with the given Order ID.</div>";
    }
}
?>

<div class="container">
    <h2 class="text-center my-4">Payment Status</h2>
    <form method="post" action="">
        <div class="form-group row justify-content-center">
            <label class="col-form-label">Order ID:</label>
            <div class="col-md-4">
                <input class="form-control" id="ORDER_ID" tabindex="1" maxlength="20" size="20" name="ORDER_ID" autocomplete="off" value="<?php echo $ORDER_ID ?>">
            </div>
            <div>
                <input class="btn btn-primary ml-2" value="View" type="submit">
            </div>
        </div>
    </form>
</div>

<div class="container">
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

<?php include('./adminInclude/footer.php'); ?>
