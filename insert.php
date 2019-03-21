<?php
include('config.php');
 if(isset($_POST['barcode'])){
                $barcode = mysqli_real_escape_string($conn, $_POST["barcode"]); 
                $merchant = mysqli_real_escape_string($conn, $_POST["merchants"]); 
                $emp = mysqli_real_escape_string($conn, $_POST["employees"]); 

               
               $sql = "INSERT INTO barcode_factory (barcodeNumber, merchant_code, updated_by,state)
               VALUES ('demo', 'demo', 'demo',1)";

                if ($conn->query($sql) === TRUE) {

                    echo "New record created successfully";
                } else {
                   
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }            
 }

?>