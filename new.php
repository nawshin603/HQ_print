<?php
include('config.php');

if(isset($_POST['get_barcode'])){
    $barcode = $_POST['get_barcode'];
                $barcodeInfoResult = mysqli_query($conn,"SELECT * FROM barcode_factory where barcodeNumber='$barcode'");
                if(mysqli_num_rows($barcodeInfoResult) > 0){
                    $barcodeInfoRow = mysqli_fetch_array($barcodeInfoResult);
                    echo '<script>';
                        echo '$("#pickedMerchant").html("'.$barcodeInfoRow['merchantName'].'"); ';
                        echo '$("#pickedBy").html("'.$barcodeInfoRow['updated_by'].'"); ';
                        echo '$("#merchantCode").val("'.$barcodeInfoRow['merchant_code'].'");';
                    echo '</script>';
                } else {
                    echo 'Error: no such pick up record found';
                }
            }

?>