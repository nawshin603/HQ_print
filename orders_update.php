<?php
   
    include('config.php');
    include('num_format.php');    
    /*if ($user_check!=''){*/
        if(isset($_POST['get_orderid'])){
            $orderid = trim($_POST['get_orderid']);
            $packageprice = $_POST['prices'];
            $packageprice = mysqli_real_escape_string($conn, $packageprice);
            $packageOption = $_POST['packageOption'];
            $packageOption = mysqli_real_escape_string($conn, $packageOption);
            $deliveryOption = $_POST['deliveryOption'];
            $deliveryOption = mysqli_real_escape_string($conn, $deliveryOption);
            $CashAmt = trim($_POST['collection']);
            $customerDistrict = trim($_POST['districts']);
            $customerThana = trim($_POST['thanas']);
            $customerPhone = trim($_POST['phones']);
            $bankID = trim($_POST['bank']);
            $locationName = trim($_POST['atmLocation']);
            $locationName = mysqli_real_escape_string($conn, $locationName);
            $address = trim($_POST['atmAddress']);
            $address = mysqli_real_escape_string($conn, $address);
            $districtID = trim($_POST['atmDistrict']);
            $empCode = trim($_POST['employee']);
            $flag = $_POST['flagreq'];

            //Re-generation of orders

            //Identify Merchant Code
            $merchantCodeSQL = "select merchantCode, thanaId, districtId, dropPointCode from tbl_order_details where orderid = '$orderid'";
            $merchantCodeResult = mysqli_query($conn, $merchantCodeSQL);
            $merchantCodeRow = mysqli_fetch_array($merchantCodeResult);
            $merchantCode = $merchantCodeRow['merchantCode'];
            $pickMerchantThanaID = $merchantCodeRow['thanaId'];
            $pickMerchantDistrictID = $merchantCodeRow['districtId'];
            $oldDropPointCode = $merchantCodeRow['dropPointCode'];

            //Fetch Merchant District, Thana, RateChart ID, COD Information
            $merchantInfoSQL = "select districtid, thanaid, rateChartId, cod from tbl_merchant_info where merchantCode='$merchantCode'";
            $merchantInfoResult = mysqli_query($conn, $merchantInfoSQL);
            $merchantInfoRow = mysqli_fetch_array($merchantInfoResult);
            $merchantDistrict = $merchantInfoRow['districtid'];
            $merchantThana = $merchantInfoRow['thanaid'];
            $merchantRateChart = $merchantInfoRow['rateChartId'];
            $merchantCOD = $merchantInfoRow['cod'];

            //Identify if there is pickup merchant
            if ($pickMerchantDistrictID == 0 ){
                //Identify whether order is local/interDistrict
                if ($customerDistrict == $merchantDistrict){
                    //Local delivery
                    $chargeSQL = "select charge from tbl_rate_type where rateChartId = '$merchantRateChart' and packageOption = '$packageOption' and deliveryOption='$deliveryOption' and destination = 'local' ";
                    $chargeResult = mysqli_query($conn,$chargeSQL);
                    $chargeRow = mysqli_fetch_array($chargeResult);
                    $charge = $chargeRow['charge'];
                    $destination ='local';
                } else{
                    //Inter-District Delivery
                    $chargeSQL = "select charge from tbl_rate_type where rateChartId = '$merchantRateChart' and packageOption = '$packageOption' and deliveryOption='$deliveryOption' and destination = 'interDistrict' ";
                    $chargeResult = mysqli_query($conn,$chargeSQL);
                    $chargeRow = mysqli_fetch_array($chargeResult);
                    $charge = $chargeRow['charge'];
                    $destination ='interDistrict';
                }
            } else {
                if ($customerDistrict == $pickMerchantDistrictID){
                    //Local delivery
                    $chargeSQL = "select charge from tbl_rate_type where rateChartId = '$merchantRateChart' and packageOption = '$packageOption' and deliveryOption='$deliveryOption' and destination = 'local' ";
                    $chargeResult = mysqli_query($conn,$chargeSQL);
                    $chargeRow = mysqli_fetch_array($chargeResult);
                    $charge = $chargeRow['charge'];
                    $destination ='local';
                } else{
                    //Inter-District Delivery
                    $chargeSQL = "select charge from tbl_rate_type where rateChartId = '$merchantRateChart' and packageOption = '$packageOption' and deliveryOption='$deliveryOption' and destination = 'interDistrict' ";
                    $chargeResult = mysqli_query($conn,$chargeSQL);
                    $chargeRow = mysqli_fetch_array($chargeResult);
                    $charge = $chargeRow['charge'];
                    $destination ='interDistrict';
                }
            }

            //Identify the drop point
            $dropPointSQL = "select pointCode from tbl_point_coverage where thanaId = '$customerThana'";
            $dropPointResult = mysqli_query($conn, $dropPointSQL);
            $dropPointRow = mysqli_fetch_array($dropPointResult);
            $dropPointCode = $dropPointRow['pointCode'];

            $newOrderID = substr($orderid,0, 15).$dropPointCode;

            if ($flag == 'update'){
                if($oldDropPointCode != $dropPointCode){
                    $updateorders="update tbl_order_details set orderid = '$newOrderID', dropPointCode='$dropPointCode', dropPointEmp=NULL, packagePrice='$packageprice', productSizeWeight='$packageOption', deliveryOption='$deliveryOption', CashAmt='$CashAmt', customerDistrict='$customerDistrict', customerThana='$customerThana', custphone='$customerPhone', ratechartId = '$merchantRateChart',destination='$destination', charge='$charge', cod='$merchantCOD', update_date =  NOW() + INTERVAL 6 HOUR , updated_by = '$user_check' where orderid='$orderid'";
                } else {
                    $updateorders="update tbl_order_details set orderid = '$newOrderID', dropPointCode='$dropPointCode', packagePrice='$packageprice', productSizeWeight='$packageOption', deliveryOption='$deliveryOption', CashAmt='$CashAmt', customerDistrict='$customerDistrict', customerThana='$customerThana', custphone='$customerPhone', ratechartId = '$merchantRateChart',destination='$destination', charge='$charge', cod='$merchantCOD', update_date =  NOW() + INTERVAL 6 HOUR , updated_by = '$user_check' where orderid='$orderid'";
                }
                if (!mysqli_query($conn,$updateorders)){
                    $error ="Update Error : " . mysqli_error($conn);
                    echo $error;
                } else {
                    echo "success";
                }
                mysqli_close($conn);
                exit;
            }
            if ($flag == 'delete'){
                $backupSQL = "insert into tbl_deleted_orders select * from tbl_order_details where orderid in ($orderid)";
                //$backupResult = mysqli_query($conn, $backupSQL);
                if (!mysqli_query($conn,$backupSQL)){
                    $error ="Delete Error : " . mysqli_error($conn);
                    echo $error;                
                } else {
                    $updateorders="delete from  tbl_order_details where orderid in ($orderid)";
                    if (!mysqli_query($conn,$updateorders)){
                        $error ="Delete Error : " . mysqli_error($conn);
                        echo $error;
                    } else {
                        echo "success";
                    }
                }
                mysqli_close($conn);
                exit;
            }
            if ($flag == 'reset'){
                $updateorders="update tbl_order_details set Cash=NULL, CashTime=NULL, CashBy=NULL, CashAmt=NULL, cashComment=NULL, Ret=NULL, RetTime=NULL, RetBy=NULL, retRem=NULL, partial=NULL, partialTime=NULL, partialBy=NULL, DropDP2=NULL, DropDP2Time=NULL, DropDP2By=NULL, bank=NULL, bankTime=NULL, bankBy=NULL, retcp1=NULL, retcp1Time=NULL, retcp1By=NULL, update_date =  NOW() + INTERVAL 6 HOUR , updated_by = '$user_check'  where orderid='$orderid'";
                if (!mysqli_query($conn,$updateorders)){
                    $error ="Delete Error : " . mysqli_error($conn);
                    echo $error;
                } else {
                    echo "success";
                }
                mysqli_close($conn);
                exit;
            }
            if ($flag == 'pickassign'){
                $updateorders="update tbl_order_details set pickPointEmp='$empCode', pickAssignTime=NOW() + INTERVAL 6 HOUR, pickAssignBy='$user_check' where merchantCode='$orderid' and pickPointEmp is null and close is null";
                if (!mysqli_query($conn,$updateorders)){
                    $error ="Assignment Error : " . mysqli_error($conn);
                    echo $error;
                } else {
                    echo "success";
                }
                mysqli_close($conn);
                exit;
            }
            if ($flag == 'dropassign'){
                $updateorders="update tbl_order_details set dropPointEmp='$empCode', dropAssignTime=NOW() + INTERVAL 6 HOUR, dropAssignBy='$user_check' where merchantCode='$orderid' and dropPointEmp is null and close is null";
                if (!mysqli_query($conn,$updateorders)){
                    $error ="Assignment Error : " . mysqli_error($conn);
                    echo $error;
                } else {
                    echo "success";
                }
                mysqli_close($conn);
                exit;
            }
           if($flag == 'smartPickComment'){
               $smartPickComment = $_POST['smartPickComment'];
               $smartPickComment = mysqli_real_escape_string($conn, $smartPickComment);
               $nothingtopick = $_POST['nothingtopick'];
               if ($nothingtopick == 'N'){
                   $updateorders = "update tbl_order_details set Pick = 'Y', smartPickComment='$smartPickComment', nothingtopick='$nothingtopick', close = 'Y', closeTime = NOW() + INTERVAL 6 HOUR, closeBy = '$user_check', PickTime= NOW() + INTERVAL 6 HOUR, PickBy= '$user_check' where orderid='$orderid'";
               } else {
                   $updateorders = "update tbl_order_details set Pick = 'Y', smartPickComment='$smartPickComment', PickTime= NOW() + INTERVAL 6 HOUR, PickBy= '$user_check' where orderid='$orderid'";               
               }
                if (!mysqli_query($conn,$updateorders)){
                    $error ="Delete Error : " . mysqli_error($conn);
                    echo $error;
                } else {
                    echo "success";
                }
                mysqli_close($conn);
                exit;
           } 
            if ($flag == 'ATMupdate'){
                $updateorders="update tbl_atm_locations set bankID = '$bankID', locationName='$locationName', address='$address', districtID='$districtID', updateDate =  NOW() + INTERVAL 6 HOUR , updateBy = '$user_check' where atmLocationID='$orderid'";
                if (!mysqli_query($conn,$updateorders)){
                    $error ="Update Error : " . mysqli_error($conn);
                    echo $error;
                } else {
                    echo "success";
                }
                mysqli_close($conn);
                exit;
            }
            if ($flag == 'merchantList'){
                $startDate = date("Y-m-d", strtotime($orderid));
                $merchantListSQL="select tbl_order_details.merchantCode, tbl_merchant_info.merchantName, count(1) as orders from tbl_order_details left join tbl_merchant_info on tbl_order_details.merchantCode = tbl_merchant_info.merchantCode where orderDate = '$startDate' group by tbl_order_details.merchantCode order by count(1) desc";
                $merchantListResult = mysqli_query($conn, $merchantListSQL);
                echo "<option></option>";
                foreach ($merchantListResult as $merchantListRow){
                    echo "<option value=".$merchantListRow['merchantCode'].">".$merchantListRow['merchantName']." (".$merchantListRow['orders'].")</option>";
                }
                mysqli_close($conn);
                exit;
            }
            if ($flag == 'beftnInfo'){
                $collectionVal = $_POST['collectionVal'];
                $insertBeftnSQL = "insert into tbl_beftn_info (beftnID, accountName, accountNumber, bankName, branch, routeNumber, paidAmt, deliveryCharge, collection, cashCOD, Discount, bankID, invNum, invSeq, beftnFor, creationDate, createdBy) select (select (max(beftnID)+1) as beftnID from tbl_beftn_info) as beftnID, tbl_merchant_info.accountName, tbl_merchant_info.accountNumber, tbl_merchant_info.bankName, tbl_merchant_info.branch, tbl_merchant_info.routeNumber, (sum(cashCollection) - (sum(deliveryCharge) + sum(CashCoD) - tbl_invoice.Discount)) as paidAmt, sum(deliveryCharge) as deliveryCharge, sum(cashCollection) as collection, sum(CashCoD) as cashCOD, tbl_invoice.Discount, '1' as bankID, tbl_invoice_details.invNum, tbl_invoice.invSeq, 'general' as beftnFor, NOW() + INTERVAL 5 HOUR as creationDate, '$user_check' as createdBy from tbl_invoice_details left join tbl_invoice left join tbl_merchant_info on tbl_invoice.merchantCode = tbl_merchant_info.merchantCode on tbl_invoice_details.invNum = tbl_invoice.invNum where tbl_invoice_details.invNum in ($orderid) group by tbl_invoice_details.invNum";
                if (!mysqli_query($conn,$insertBeftnSQL)){
                    $error ="BEFTN Error : " . mysqli_error($conn);
                    echo $error;
                } else {
                    $transID = mysqli_insert_id($conn);
                    $updateBeftnSQL = "update tbl_invoice set chequeStatus = 'Y', beftn = 'Y' where invNum in ($orderid)";
                    if (!mysqli_query($conn,$updateBeftnSQL)){
                        $error ="BEFTN Status Error : " . mysqli_error($conn);
                        echo $error;
                    } else {
                        if ($collectionVal !=''){
                            $updateCollectionSQL = "update tbl_beftn_info set paidAmt = collection, beftnFor = 'collection' where invNum in ($collectionVal)";
                            if (!mysqli_query($conn,$updateCollectionSQL)){
                                $error ="BEFTN Collection Error : " . mysqli_error($conn);
                                echo $error;
                            } else {
                                $beftnSQL = "select beftnID from tbl_beftn_info where transID = $transID";
                                $beftnResult = mysqli_query($conn, $beftnSQL);
                                $beftnRow = mysqli_fetch_array($beftnResult);
                                $beftnID = $beftnRow['beftnID'];
                                echo $beftnID;                        
                            }
                        } else {
                            $beftnSQL = "select beftnID from tbl_beftn_info where transID = $transID";
                            $beftnResult = mysqli_query($conn, $beftnSQL);
                            $beftnRow = mysqli_fetch_array($beftnResult);
                            $beftnID = $beftnRow['beftnID'];
                            echo $beftnID;                            
                        }
                    }
                }
                mysqli_close($conn);
                exit;
            }
            if ($flag == 'beftnRemove'){
                $beftnRemoveSQL = "update tbl_invoice set chequeStatus = 'Y', beftn = 'Y', beftnComment = 'Excluded' where invNum = '$orderid'";
                if(!mysqli_query($conn, $beftnRemoveSQL)){
                    $error = "BEFTN Error : ".mysqli_error($conn);
                    echo $error;
                } else {
                    echo "Excluded from the list";
                }
            }
            if($flag == 'beftnList'){
                $beftnListSQL = "SELECT distinct beftnID, DATE_FORMAT(creationDate, '%d-%M-%Y') as beftnDate FROM tbl_beftn_info where beftnID != 0 order by beftnID desc";
                $beftnListResult = mysqli_query($conn, $beftnListSQL);
                echo "<option></option>";
                foreach($beftnListResult as $beftnListRow){
                    echo "<option value=".$beftnListRow['beftnID']."> BEFTN ID :- ".$beftnListRow['beftnID']."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;BEFTN Date :-".$beftnListRow['beftnDate']."</option>";
                }
            }
            if($flag == 'shuttleScan'){
                $findOrderSQL = "SELECT ordId, orderid, pickPointEmp, Pick, PickTime, PickBy, DP1, DP1Time, DP1By, Shtl, ShtlTime, ShtlBy,retcp1, retcp1Time, retcp1By FROM `tbl_order_details` WHERE barcode = SUBSTRING('$orderid',1,11)";
                $findOrderResult = mysqli_query($conn, $findOrderSQL);
                if(mysqli_num_rows($findOrderResult) > 0){
                    $findOrderRow = mysqli_fetch_array($findOrderResult);
                    $scannedOrder = $findOrderRow['orderid'];
                    if($findOrderRow['Shtl'] !='Y'){
                        if($findOrderRow['pickPointEmp'] != ''){
                            if($findOrderRow['Pick'] != 'Y'){
                                $updateOrdersSQL = "update tbl_order_details set Pick = 'Y', PickTime = NOW() + INTERVAL 6 HOUR, PickBy = '$user_check', DP1 = 'Y', DP1Time = NOW() + INTERVAL 6 HOUR, DP1By = '$user_check', Shtl = 'Y', ShtlTime = NOW() + INTERVAL 6 HOUR, ShtlBy = '$user_check', cp1 = 'Y', cp1Time = NOW() + INTERVAL 6 HOUR, cp1By = '$user_check', cp1Shuttle = 'Y', cp1ShuttleTime = NOW() + INTERVAL 6 HOUR, cp1ShuttleBy = '$user_check', cp2 = 'Y', cp2Time = NOW() + INTERVAL 6 HOUR, cp2By = '$user_check', cp2Shuttle = 'Y', cp2ShuttleTime = NOW() + INTERVAL 6 HOUR, cp2ShuttleBy = '$user_check' where orderid = '$scannedOrder'";
                                if(!mysqli_query($conn, $updateOrdersSQL)){
                                    echo "Update Error : " . mysqli_error($conn);
                                } else {
                                    echo $scannedOrder." ----shuttle---ok";
                                    $curl = curl_init();

                  curl_setopt_array($curl, array(
                  CURLOPT_URL => "https://sandbox.robishop.com.bd/rest/V1/integration/admin/token",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 30,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "POST",
                  CURLOPT_POSTFIELDS => "{\n\t\"username\" : \"paperfly\",\n\t\"password\" : \"Robi123@\"\n}",
                  CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json"
                  ),
                ));

                $result = curl_exec($curl);
                $err = curl_error($curl);

                if ($err) {
                  echo "cURL Error #:" . $err;
                } else {
                    $token = json_decode($result, true);
                    curl_close($curl);
                    $merOrderRefRow = mysqli_fetch_array(mysqli_query($conn, "select merchantCode, merOrderRef from tbl_order_details where orderid='$scannedOrder'"));
                    $merOrderRef = $merOrderRefRow['merOrderRef'];
                    $merchantCode = $merOrderRefRow['merchantCode'];
                    if($merchantCode == 'M-1-0484'){
                     $curl = curl_init();

                    curl_setopt_array($curl, array(
                      CURLOPT_URL => "https://sandbox.robishop.com.bd/rest/V1/deliverypartner",
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => "",
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 30,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => "POST",
                      CURLOPT_POSTFIELDS => "{\n\t\"order_id\" : \"".$merOrderRef."\",\n\t\"status\" : \"in_transit\",\n\t\"comment\" : \"The delivery has started\"\n}",
                      CURLOPT_HTTPHEADER => array(
                        "Authorization: Bearer $token",
                        "Content-Type: application/json"
                        
                      ),
                    ));

                    $response = curl_exec($curl);
                    $err = curl_error($curl);

                   

                    if ($err) {
                      echo "cURL Error #:" . $err;
                    } else {
                      echo $response;
                    }
                    curl_close($curl);
                  }
                }
                                }
                            }
                            if($findOrderRow['Pick'] == 'Y' && $findOrderRow['DP1'] != 'Y'){
                                $updateOrdersSQL = "update tbl_order_details set DP1 = 'Y', DP1Time = NOW() + INTERVAL 6 HOUR, DP1By = '$user_check', Shtl = 'Y', ShtlTime = NOW() + INTERVAL 6 HOUR, ShtlBy = '$user_check', cp1 = 'Y', cp1Time = NOW() + INTERVAL 6 HOUR, cp1By = '$user_check', cp1Shuttle = 'Y', cp1ShuttleTime = NOW() + INTERVAL 6 HOUR, cp1ShuttleBy = '$user_check', cp2 = 'Y', cp2Time = NOW() + INTERVAL 6 HOUR, cp2By = '$user_check', cp2Shuttle = 'Y', cp2ShuttleTime = NOW() + INTERVAL 6 HOUR, cp2ShuttleBy = '$user_check' where orderid = '$scannedOrder'";
                                if(!mysqli_query($conn, $updateOrdersSQL)){
                                    echo "Update Error : " . mysqli_error($conn);
                                } else {
                                    echo $scannedOrder." ----shuttle---ok";
                                }                            
                            }
                            if($findOrderRow['Pick'] == 'Y' && $findOrderRow['DP1'] == 'Y' && $findOrderRow['Shtl'] !='Y'){
                                $updateOrdersSQL = "update tbl_order_details set DP1 = 'Y', DP1Time = NOW() + INTERVAL 6 HOUR, DP1By = '$user_check', Shtl = 'Y', ShtlTime = NOW() + INTERVAL 6 HOUR, ShtlBy = '$user_check', cp1 = 'Y', cp1Time = NOW() + INTERVAL 6 HOUR, cp1By = '$user_check', cp1Shuttle = 'Y', cp1ShuttleTime = NOW() + INTERVAL 6 HOUR, cp1ShuttleBy = '$user_check', cp2 = 'Y', cp2Time = NOW() + INTERVAL 6 HOUR, cp2By = '$user_check', cp2Shuttle = 'Y', cp2ShuttleTime = NOW() + INTERVAL 6 HOUR, cp2ShuttleBy = '$user_check' where orderid = '$scannedOrder'";
                                if(!mysqli_query($conn, $updateOrdersSQL)){
                                    echo "Update Error : " . mysqli_error($conn);
                                } else {
                                    echo $scannedOrder." ----shuttle---ok";
                                }                            
                            }
                        } else {
                            //echo "Error: Order not assigned yet!!";
                            $updateOrdersSQL = "update tbl_order_details set pickPointEmp = '$user_code', Pick = 'Y', PickTime = NOW() + INTERVAL 6 HOUR, PickBy = '$user_check', DP1 = 'Y', DP1Time = NOW() + INTERVAL 6 HOUR, DP1By = '$user_check', Shtl = 'Y', ShtlTime = NOW() + INTERVAL 6 HOUR, ShtlBy = '$user_check', cp1 = 'Y', cp1Time = NOW() + INTERVAL 6 HOUR, cp1By = '$user_check', cp1Shuttle = 'Y', cp1ShuttleTime = NOW() + INTERVAL 6 HOUR, cp1ShuttleBy = '$user_check', cp2 = 'Y', cp2Time = NOW() + INTERVAL 6 HOUR, cp2By = '$user_check', cp2Shuttle = 'Y', cp2ShuttleTime = NOW() + INTERVAL 6 HOUR, cp2ShuttleBy = '$user_check' where orderid = '$scannedOrder'";
                            if(!mysqli_query($conn, $updateOrdersSQL)){
                                echo "Update Error : " . mysqli_error($conn);
                            } else {
                                echo $scannedOrder." ----shuttle---ok";
                            }
                        }                    
                    } else {
                        echo $scannedOrder." Error: Already scanned!!";
                    }
                } else {
                    echo "Error: No Order Found!!!";
                }
            }
            if($flag == 'retCPScan'){
                $findOrderSQL = "SELECT ordId, orderid, Ret, partial, DropDP2, bank, retcp1, retcp1Time, retcp1By FROM `tbl_order_details` WHERE barcode = SUBSTRING('$orderid',1,11)";
                $findOrderResult = mysqli_query($conn, $findOrderSQL);
                if(mysqli_num_rows($findOrderResult) > 0){
                    $findOrderRow = mysqli_fetch_array($findOrderResult);
                    $scannedOrder = $findOrderRow['orderid'];
                    if($findOrderRow['retcp1'] !='Y'){
                        if($findOrderRow['Ret'] == 'Y'){
                            if ($findOrderRow['DropDP2'] =='Y'){
                                $updateOrdersSQL = "update tbl_order_details set retcp1 = 'Y', retcp1Time = NOW() + INTERVAL 6 HOUR, retcp1By = '$user_check', cpRetStatus = 'R' where orderid = '$scannedOrder'";
                                if(!mysqli_query($conn, $updateOrdersSQL)){
                                    echo "Update Error : " . mysqli_error($conn);
                                } else {
                                    echo $scannedOrder." ----CP Return---ok";
                                } 
                            }else {
                                $updateOrdersSQL = "update tbl_order_details set DropDP2 = 'Y', DropDP2Time = NOW() + INTERVAL 6 HOUR, DropDP2By = '$user_check',  retcp1 = 'Y', retcp1Time = NOW() + INTERVAL 6 HOUR, retcp1By = '$user_check', cpRetStatus = 'R' where orderid = '$scannedOrder'";
                                if(!mysqli_query($conn, $updateOrdersSQL)){
                                    echo "Update Error : " . mysqli_error($conn);
                                } else {
                                    echo $scannedOrder." ----CP Return---ok";
                                }
                                //echo $scannedOrder." Error: DP2 Status Not Found!!";
                            }
                        } 
                        if($findOrderRow['partial'] == 'Y'){
                            if($findOrderRow['DropDP2'] =='Y'){
                                $updateOrdersSQL = "update tbl_order_details set retcp1 = 'Y', retcp1Time = NOW() + INTERVAL 6 HOUR, retcp1By = '$user_check', cpRetStatus = 'R' where orderid = '$scannedOrder'";
                                if(!mysqli_query($conn, $updateOrdersSQL)){
                                    echo "Update Error : " . mysqli_error($conn);
                                } else {
                                    echo $scannedOrder." ----CP Return---ok";
                                }                            
                            } else {
                            echo $scannedOrder." Error: DP2 and Bank Status Not Found!!";
                            }
                        }
                        if($findOrderRow['Ret'] != 'Y' && $findOrderRow['partial'] != 'Y') {
                            echo $scannedOrder." Error: DP2 Status Not Found!!";
                        }
                    }else{
                        echo $scannedOrder." Error: Already scanned!!";
                    }
                } else {
                    echo "Error: No Order Found!!";
                }            
            }
            if($flag == 'dp2Scan'){
                $findOrderSQL = "SELECT ordId, orderid, Shtl, cp2Shuttle, DP2 FROM `tbl_order_details` WHERE barcode = SUBSTRING('$orderid',1,11)";
                $findOrderResult = mysqli_query($conn, $findOrderSQL);
                if(mysqli_num_rows($findOrderResult) > 0){
                    $findOrderRow = mysqli_fetch_array($findOrderResult);
                    $scannedOrder = $findOrderRow['orderid'];
                    if($findOrderRow['DP2'] !='Y'){
                        if($findOrderRow['Shtl'] == 'Y' || $findOrderRow['cp2Shuttle'] == 'Y'){
                            $updateOrdersSQL = "update tbl_order_details set DP2 = 'Y', DP2Time = NOW() + INTERVAL 6 HOUR, DP2By = '$user_check' where orderid = '$scannedOrder'";
                            if(!mysqli_query($conn, $updateOrdersSQL)){
                                echo "Update Error : " . mysqli_error($conn);
                            } else {
                                echo $scannedOrder." ----DP2---ok";
                            }
                            //echo $scannedOrder." Error: DP2 Status Not Found!!";
                        } else {
                            echo $scannedOrder." Error: Shuttle Status Not Found!!";
                        } 
                    }else{
                        echo $scannedOrder." Error: Already scanned!!";
                    }
                } else {
                    echo "Error: No Order Found!!";
                }            
            }
            if($flag == 'dp2pickScan'){
                $findOrderSQL = "SELECT ordId, orderid, dropPointEmp, DP2, PickDrop FROM `tbl_order_details` WHERE barcode = SUBSTRING('$orderid',1,11)";
                $findOrderResult = mysqli_query($conn, $findOrderSQL);
                if(mysqli_num_rows($findOrderResult) > 0){
                    $findOrderRow = mysqli_fetch_array($findOrderResult);
                    $scannedOrder = $findOrderRow['orderid'];
                    if($findOrderRow['PickDrop'] !='Y'){
                        if($findOrderRow['DP2'] == 'Y'){
                            $updateOrdersSQL = "update tbl_order_details set dropPointEmp = '$user_code', dropAssignTime = NOW() + INTERVAL 6 HOUR, dropAssignBy = '$user_check', PickDrop = 'Y', PickDropTime = NOW() + INTERVAL 6 HOUR, PickDropBy = '$user_check' where orderid = '$scannedOrder'";
                            if(!mysqli_query($conn, $updateOrdersSQL)){
                                echo "Update Error : " . mysqli_error($conn);
                            } else {
                                echo $scannedOrder." ----Pick Product---ok";
                            }
                            //echo $scannedOrder." Error: DP2 Status Not Found!!";
                        } else {
                            echo $scannedOrder." Error: DP2 Status Not Found!!";
                        } 
                    }else{
                        echo $scannedOrder." Error: Already scanned!!";
                    }
                } else {
                    echo "Error: No Order Found!!";
                }            
            }
            if($flag == 'merchantWiseShuttle'){
                $shuttleDate = date("Y-m-d H:i", strtotime($orderid));
                $shuttleEndTime = strtotime($_POST['shuttleEndTime']);
                $shuttleEndTime = date("Y-m-d H:i", $shuttleEndTime);
                $merchantWiseSQL = "Select tbl_merchant_info.merchantName, count(1) as orderCount from tbl_order_details left join tbl_merchant_info on tbl_order_details.merchantCode = tbl_merchant_info.merchantCode where DATE_FORMAT(ShtlTime, '%Y-%m-%d %H:%i') >= '$shuttleDate' and DATE_FORMAT(ShtlTime, '%Y-%m-%d %H:%i') <= '$shuttleEndTime' and Shtl = 'Y' group by tbl_merchant_info.merchantName  order by orderCount desc";
                $merchantWiseResult = mysqli_query($conn, $merchantWiseSQL) or die ("Error: unable to execute query ".mysqli_error($conn));
                $orderCount = 0;
                foreach($merchantWiseResult as $merchantWiseRow){
                    $orderCount = $orderCount + $merchantWiseRow['orderCount'];
                }
                if(mysqli_num_rows($merchantWiseResult) > 0){
                    echo '<thead>';
                    echo '<tr><th colspan = "2">No of Merchant ( <b style="color: blue; font-size: 120%">'.mysqli_num_rows($merchantWiseResult).'</b> )&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No of Orders ( <b style="color: blue; font-size: 120%">'.$orderCount.'</b>) &nbsp;&nbsp;&nbsp; Date:'.date("d-M-Y", strtotime($orderid)).'</th></tr>';
                    echo '<tr>';
                    echo '<th>Merchant Name</th>';
                    echo '<th>Order Count</th>';
                    echo '</tr></thead>';

                    echo '<tbody>';
                    foreach($merchantWiseResult as $merchantWiseRow){
                        echo '<tr>';
                        echo '<td>'.$merchantWiseRow['merchantName'].'</td>';
                        echo '<td>'.$merchantWiseRow['orderCount'].'</td>';
                        echo '</tr>';
                    }
                    echo '</tbody>';
                }
            }
            if($flag == 'pointWiseShuttle'){
                $shuttleDate = date("Y-m-d H:i", strtotime($orderid));
                $shuttleEndTime = strtotime($_POST['shuttleEndTime']);
                $shuttleEndTime = date("Y-m-d H:i", $shuttleEndTime);
                $merchantWiseSQL = "Select dropPointCode,  tbl_point_info.pointName, count(1) as orderCount from tbl_order_details left join tbl_point_info on tbl_order_details.dropPointCode = tbl_point_info.pointCode where DATE_FORMAT(ShtlTime, '%Y-%m-%d %H:%i') >= '$shuttleDate' and DATE_FORMAT(ShtlTime, '%Y-%m-%d %H:%i') <= '$shuttleEndTime' and Shtl = 'Y' group by dropPointCode order by tbl_point_info.pointCode";
                $merchantWiseResult = mysqli_query($conn, $merchantWiseSQL) or die ("Error: unable to execute query ".mysqli_error($conn));
                if(mysqli_num_rows($merchantWiseResult) > 0){
                    echo '<thead>';
                    echo '<tr><th colspan = "2">No of Point ( <b style="color: blue; font-size: 120%">'.mysqli_num_rows($merchantWiseResult).'</b> ) &nbsp;&nbsp;&nbsp; Date:'.date("d-M-Y", strtotime($orderid)).'</th></tr>';
                    echo '<tr>';
                    echo '<th>Point Name</th>';
                    echo '<th>Order Count</th>';
                    echo '</tr></thead>';

                    echo '<tbody>';
                    foreach($merchantWiseResult as $merchantWiseRow){
                        echo '<tr>';
                        echo '<td>'.$merchantWiseRow['dropPointCode'].' - '. $merchantWiseRow['pointName'].'</td>';
                        echo '<td>'.$merchantWiseRow['orderCount'].'</td>';
                        echo '</tr>';
                    }
                    echo '</tbody>';
                }            
            }
            if($flag == 'cpReturnInvoice'){
                $distinctMerchantSQL = "select distinct merchantCode from tbl_order_details where orderid in ($orderid)";
                $distinctMerchantResult = mysqli_query($conn, $distinctMerchantSQL) or die ('Error : unable find merchant code'.mysqli_error($conn));
                foreach($distinctMerchantResult as $distinctMerchantRow){
                    $merchantCode = $distinctMerchantRow['merchantCode'];
                    $maxCPSeqSQL = "select max(retChallanSeq) as maxSeq from tbl_order_details where merchantCode = '$merchantCode'"; 
                    $maxCPSeqResult = mysqli_query($conn, $maxCPSeqSQL) or die('Error : unable find max sequence for invoice'.mysqli_error($conn));
                    $maxCPSeqRow = mysqli_fetch_array($maxCPSeqResult);
                    $maxSeq = $maxCPSeqRow['maxSeq']+1;
                
                    $retInv = date('dmy').'-'.$merchantCode.'-'.$maxSeq;

                    $genChallanSQL = "update tbl_order_details set cpRetStatus = 'S', retInvDate = NOW() + INTERVAL 6 HOUR, retInv = '$retInv', retChallanSeq = $maxSeq, retChallanBy = '$user_check' where merchantCode = '$merchantCode' and orderid in ($orderid)"; 
                    $genChallanResult = mysqli_query($conn, $genChallanSQL) or die('Error : unable to generate return invoice'.mysqli_error($conn));
                    //echo $genChallanSQL;
                }
            }
            if($flag == 'invoiceCount'){
                $merchantsql = "SELECT distinct tbl_order_details.merchantCode, tbl_merchant_info.merchantName FROM tbl_order_details left join tbl_merchant_info on tbl_order_details.merchantCode = tbl_merchant_info.merchantCode WHERE invNum IS NULL and close ='Y' and orderType in ('Merchant', 'Other_Merchant')";
                $merchantresult = mysqli_query($conn,$merchantsql);
                $merchantCount = mysqli_num_rows($merchantresult);
                echo $merchantCount;            
            }
            if($flag == 'merchantInvoiceList'){
                $merchantsql = "SELECT distinct tbl_order_details.merchantCode, tbl_merchant_info.merchantName, tbl_merchant_info.paymentMode FROM tbl_order_details left join tbl_merchant_info on tbl_order_details.merchantCode = tbl_merchant_info.merchantCode WHERE invNum IS NULL and close ='Y' and orderType in ('Merchant', 'Other_Merchant')";
                $merchantresult = mysqli_query($conn,$merchantsql);
                foreach($merchantresult as $merchantRow){
                    echo '<option value="'.$merchantRow['merchantCode'].'">'.$merchantRow['merchantName'].' ('.$merchantRow['paymentMode'].')</option>';
                }            
            }
            if($flag == 'challanList'){
                $challanListSQL = "select distinct tbl_order_details.merchantCode, tbl_merchant_info.merchantName, retInv, DATE_FORMAT(retInvDate, '%d-%b-%Y') as retInvDate from tbl_order_details left join tbl_merchant_info on tbl_order_details.merchantCode = tbl_merchant_info.merchantCode where cpRetStatus = 'A' and tbl_order_details.merchantCode = '$orderid' order by retInvDate";
                $challanListResult = mysqli_query($conn, $challanListSQL) or die("Error : unable to generate challan list".mysqli_error($conn));
                echo '<option></option>';
                foreach($challanListResult as $challanListRow){
                    echo '<option value="'.$challanListRow['retInv'].'">'.$challanListRow['merchantName'].' : '.$challanListRow['retInv'].'</option>';
                }
            }
            if($flag == 'acceptedChallanList'){
                $challanNo = $_POST['challanNo'];
                $challanNameSQL = "select distinct retInvFileName from tbl_order_details where retInv = '$challanNo'";
                $challanNameResult = mysqli_query($conn, $challanNameSQL) or die('Error : unable to find accepted challan document'.mysqli_error($conn));
                $challanNameRow = mysqli_fetch_array($challanNameResult);

                echo $challanNameRow['retInvFileName'];
            }
            if($flag == 'cpReturnOrderList'){
                $merchantCode = $_POST['merchantCode'];
                $cpReturnListSQL = "select orderid, merOrderRef, tbl_merchant_info.merchantName, DATE_FORMAT(retcp1Time, '%d-%M-%Y') as retcp1Date from tbl_order_details left join tbl_merchant_info on tbl_order_details.merchantCode = tbl_merchant_info.merchantCode where retcp1 = 'Y' and cpRetStatus = 'R' and tbl_order_details.merchantCode = '$merchantCode' order by tbl_merchant_info.merchantName, retcp1Time";
                $cpReturnListResult = mysqli_query($conn, $cpReturnListSQL) or die("Error: unable select retrun list".mysqli_error($conn));

                foreach($cpReturnListResult as $cpReturnListRow){
                    echo '<tr>';
                    echo '<td>'.$cpReturnListRow['orderid'].'</td>';
                    echo '<td>'.$cpReturnListRow['merOrderRef'].'</td>';
                    echo '<td>'.$cpReturnListRow['merchantName'].'</td>';                                    
                    echo '<td>'.$cpReturnListRow['retcp1Date'].'</td>';
                    echo '<td><input type="checkbox" style="text-align: center" id="chk'.$cpReturnListRow['orderid'].'" value="'.$cpReturnListRow['orderid'].'" name="retCheck"></td>';
                    echo '</tr>';                    
                }
            }
            if($flag == 'searchReturn'){
                $searchID = $_POST['searchID'];
                $orderSQL = "select orderid, merOrderRef, retInv, retInvFileName from tbl_order_details where (orderid = '$searchID'  and (Ret = 'Y' or partial = 'Y')) or (merOrderRef = '$searchID' and (Ret = 'Y' or partial = 'Y')) ";
                $orderResult = mysqli_query($conn, $orderSQL) or die('Error : unable to search return order'.mysqli_error($conn));
                if(mysqli_num_rows($orderResult) > 0){
                    $orderRow = mysqli_fetch_array($orderResult);
                    echo '<thead><tr><th>Order ID</th><th>Merchant Ref.</th><th>Original Challan</th><th>Accepted Challan</th>';
                    echo '</tr></thead>';
                    echo '<tbody><tr><td>'.$orderRow['orderid'].'</td><td>'.$orderRow['merOrderRef'].'</td>';
                    if($orderRow['retInv'] !=''){
                        $retInv = $orderRow['retInv'];
                        echo '<td> <a href="Return-Challan?xxCode='.$orderRow['retInv'].'" target="_blank">Original Challan</a></td>';
                        if($orderRow['retInvFileName'] != ''){
                            echo '<td><a href="/returnOrder/'.$orderRow['retInvFileName'].'" target="_blank">Accepted Challan</a></td>';
                        } else {
                            echo '<td></td>';
                        }
                    } else {
                        echo '<td></td><td></td>';
                    }
                    echo '</tr></tbody>'; 
                } else {
                    echo "No such return orders found";
                }
            }
            if($flag == 'dp2-return'){
                $returnOrders = $_POST['returnOrders'];
                $obj = json_decode($returnOrders, true);
                foreach ($obj as $item){
                    $orderid = $item['orderid'];
                    $comments = $item['comments'];
                    $comments = mysqli_real_escape_string($conn, $comments);

                    $updateReturnOrdersSQL = "update tbl_order_details set DropDP2 = 'Y', DropDP2Time = NOW() + INTERVAL 6 HOUR, DropDP2By = '$user_check', dropDP2Comments = '$comments' where orderid = '$orderid'";
                    if(!mysqli_query($conn, $updateReturnOrdersSQL)){
                        echo "Error : unable to return orders ".$orderid;
                        exit;
                    } 
                }
            }
            if($flag == 'dp2-cash-partial'){
                $batchNoSQL = "select (max(dropDP2Batch) + 1) as dropDP2Batch from tbl_order_details where DropDP2By = '$user_check'";
                if(!mysqli_query($conn, $batchNoSQL)){
                    echo "Error : unable generate batch no".mysqli_error($conn);
                    exit;
                } else {
                    $batchNoResult = mysqli_query($conn, $batchNoSQL);
                    $batchNoRow = mysqli_fetch_array($batchNoResult);
                    $batchNo = $batchNoRow['dropDP2Batch'];
                }
                $cashOrders = $_POST['cashOrders'];
                $partialOrders = $_POST['partialOrders'];
                $depositSlip = $_POST['depositSlip'];
                $depositSlip = mysqli_real_escape_string($conn, $depositSlip);
                $depositedBy = $_POST['depositedBy'];
                $depositDate = date('Y-m-d', strtotime($_POST['depositDate']));
                $depositComment = $_POST['depositComment'];
                $depositComment = mysqli_real_escape_string($conn, $depositComment);
                if($cashOrders != ''){
                    $cashObj = json_decode($cashOrders, true);
                    foreach($cashObj as $cashItem){
                        $orderid = $cashItem['orderid'];
                        $CashAmt = $cashItem['CashAmt'];
                        $comments = $cashItem['comments'];
                        $comments = mysqli_real_escape_string($conn, $comments);
                        $updateCashOrdersSQL = "update tbl_order_details set CashAmt = '$CashAmt', DropDP2 = 'Y', DropDP2Time = NOW() + INTERVAL 6 HOUR, DropDP2By = '$user_check', depositDate = '$depositDate', depositComment = '$depositComment', dropDP2Comments = '$comments', dropDP2depositSlip = '$depositSlip', dropDP2Batch = '$batchNo', depositedBy = '$depositedBy' where orderid = '$orderid'";
                        if(!mysqli_query($conn, $updateCashOrdersSQL)){
                            echo "Error : unable to return orders ".$orderid;
                            exit;
                        } 
                    }
                }
                if($partialOrders != ''){
                    $partialObj = json_decode($partialOrders, true);
                    foreach($partialObj as $partialItem){
                        $orderid = $partialItem['orderid'];
                        $CashAmt = $partialItem['CashAmt'];
                        $comments = $partialItem['comments'];
                        $comments = mysqli_real_escape_string($conn, $comments);
                        $updatePartialOrdersSQL = "update tbl_order_details set CashAmt = '$CashAmt', DropDP2 = 'Y', DropDP2Time = NOW() + INTERVAL 6 HOUR, DropDP2By = '$user_check', depositDate = '$depositDate', depositComment = '$depositComment', dropDP2Comments = '$comments', dropDP2depositSlip = '$depositSlip', dropDP2Batch = '$batchNo', depositedBy = '$depositedBy' where orderid = '$orderid'";
                        if(!mysqli_query($conn, $updatePartialOrdersSQL)){
                            echo "Error : unable to return orders ".$orderid;
                            exit;
                        } 
                    }                
                }
            }
            if($flag == 'orderStatusReset'){
                $orderStatusSQL = "update tbl_order_details set Cash = NULL, CashAmt = NULL, cashComment = NULL,  Ret = NULL, retReason = NULL, retRem = NULL, partial= NULL, partialReceive = NULL, partialReason = NULL, update_date = NOW() + INTERVAL 6 HOUR, updated_by = '$user_check' where orderid = '$orderid'";
                $orderStatusResult = mysqli_query($conn, $orderStatusSQL) or die ('Error : unable to change order status'.mysqli_error($conn));
            }
            if($flag == 'monthInvoice'){
                $merchantCode = trim($_POST['merchantCode']);
                $merchantCode = mysqli_real_escape_string($conn, $merchantCode);
                $findInvoicesql="select distinct monthInvNum from tbl_invoice where merchantCode='$merchantCode' order by inv_date desc";
                $findInvoiceresult = mysqli_query($conn, $findInvoicesql);
                echo "<option></option>";
                foreach ($findInvoiceresult as $row){
                    echo "<option value=".$row['monthInvNum'].">".$row['monthInvNum']."</option>";
                }
                exit;               
            }
            if($flag == 'addPrivilege'){
                $privilege = $_POST['privilege'];

                $inserSQL = mysqli_query($conn, "insert into tbl_scan_priv (userName, privilegeOption, creationDate, createdBy) values('$orderid', '$privilege', NOW() + INTERVAL 6 HOUR, '$user_check')") or die ("Error : unable to add privilege ".mysqli_error($conn));

                $previlegeResult = mysqli_query($conn, "select privilegeID, userName, privilegeOption from tbl_scan_priv where userName != 'admin' order by privilegeID desc");

                echo '
                    <table class="table table-hover" id="privilegeTable">
                        <thead>
                            <tr>
                                <th>User Name</th>
                                <th>Privilege</th>
                                <th style="text-align: right">Remove</th>
                            </tr>
                        </thead>
                        <tbody>';
                            foreach($previlegeResult as $previlegeRow){
                            echo '<tr>';
                                echo '<td>'.$previlegeRow['userName'].'</td>';
                                echo '<td>'.$previlegeRow['privilegeOption'].'</td>';
                                echo '<td style="text-align: right"><button type="button" class="btn btn-warning" onclick="removePrivilege('.$previlegeRow['privilegeID'].')">Remove</button> </td>';
                            echo '</tr>';
                            }
                        echo '</tbody>
                    </table>
                ';
            }
            if($flag == 'removePrivilege'){
                $deleteSQL = mysqli_query($conn, "delete from tbl_scan_priv where privilegeID = '$orderid'") or die ("Error : unable to remove privilege ".mysqli_error($conn));

                $previlegeResult = mysqli_query($conn, "select privilegeID, userName, privilegeOption from tbl_scan_priv where userName != 'admin' order by privilegeID desc");

                echo '
                    <table class="table table-hover" id="privilegeTable">
                        <thead>
                            <tr>
                                <th>User Name</th>
                                <th>Privilege</th>
                                <th style="text-align: right">Remove</th>
                            </tr>
                        </thead>
                        <tbody>';
                            foreach($previlegeResult as $previlegeRow){
                            echo '<tr>';
                                echo '<td>'.$previlegeRow['userName'].'</td>';
                                echo '<td>'.$previlegeRow['privilegeOption'].'</td>';
                                echo '<td style="text-align: right"><button type="button" class="btn btn-warning" onclick="removePrivilege('.$previlegeRow['privilegeID'].')">Remove</button> </td>';
                            echo '</tr>';
                            }
                        echo '</tbody>
                    </table>
                ';
            }
            if($flag == 'pointManagerPerf'){
                $pointManager = $_POST['pointManager'];
                $startDate = date("Y-m-d", strtotime($_POST['startDate']));
                $endDate = date("Y-m-d", strtotime($_POST['startDate']));

                $shuttleStartDate = date('Y-m-d', strtotime('-1 day', strtotime($startDate)));

                $shuttleStartTime = $shuttleStartDate.' 11:00';
                $shuttleEndTime = $endDate.' 11:00';
            
                $pointListResult = mysqli_query($conn, "select * from tbl_employee_point where empCode = '$pointManager'") or die("Error : failed to get point info".mysqli_error($conn));
                $pointRegion = '';

                $pointSumResult = mysqli_query($conn, "select pointid, pointCode, pointName, region, regionSort, shtlOrders.shuttle, dp2pick.pickDate, dp2pick.pick, slaMissed.slaCnt, employeeCount.empCount, cashOrders.cash, retOrders.ret, partialOrders.partial, onHoldOrders.onHold, onHoldSum.onHoldPending, pendingOrders.pending from tbl_point_info 
    left join (SELECT * from tbl_sla_missed where DATE_FORMAT(creationDate, '%Y-%m-%d') = '$startDate') as slaMissed on tbl_point_info.pointCode = slaMissed.dropPointCode 
    left join (SELECT dropPointCode, count(distinct dropPointEmp) as empCount FROM tbl_order_details WHERE DATE_FORMAT(tbl_order_details.dropAssignTime,'%Y-%m-%d') = '$startDate' group by dropPointCode) as employeeCount on tbl_point_info.pointCode = employeeCount.dropPointCode 
    left join (SELECT tbl_order_details.dropPointCode, DATE_FORMAT(tbl_order_details.ShtlTime,'%Y-%m-%d') as shuttleDate, count(1) as shuttle from tbl_order_details where DATE_FORMAT(tbl_order_details.ShtlTime,'%Y-%m-%d %H:%i') between '$shuttleStartTime' and '$shuttleEndTime' and Shtl = 'Y' group by  dropPointCode) as shtlOrders on tbl_point_info.pointCode = shtlOrders.dropPointCode
    left join (SELECT * from tbl_pending_orders where DATE_FORMAT(creationDate, '%Y-%m-%d') = '$startDate') as pendingOrders on tbl_point_info.pointCode = pendingOrders.dropPointCode
    left join (SELECT tbl_order_details.dropPointCode, DATE_FORMAT(tbl_order_details.DP2Time,'%Y-%m-%d') as pickDate, count(1) as pick from tbl_order_details where DATE_FORMAT(tbl_order_details.DP2Time,'%Y-%m-%d') between '$startDate' and '$endDate' and DP2 = 'Y' group by  dropPointCode, DATE_FORMAT(tbl_order_details.DP2Time,'%Y-%m-%d')) as dp2pick on tbl_point_info.pointCode = dp2pick.dropPointCode
    left join (SELECT tbl_order_details.dropPointCode, DATE_FORMAT(tbl_order_details.CashTime,'%Y-%m-%d') as cashDate, count(1) as cash from tbl_order_details where DATE_FORMAT(tbl_order_details.CashTime,'%Y-%m-%d') between '$startDate' and '$endDate' and cash = 'Y' group by  dropPointCode, DATE_FORMAT(tbl_order_details.CashTime,'%Y-%m-%d')) as cashOrders on tbl_point_info.pointCode = cashOrders.dropPointCode
    left join (SELECT tbl_order_details.dropPointCode, DATE_FORMAT(tbl_order_details.RetTime,'%Y-%m-%d') as retDate, count(1) as ret from tbl_order_details where DATE_FORMAT(tbl_order_details.RetTime,'%Y-%m-%d') between '$startDate' and '$endDate' and Ret = 'Y' group by dropPointCode, DATE_FORMAT(tbl_order_details.RetTime,'%Y-%m-%d')) as retOrders on tbl_point_info.pointCode = retOrders.dropPointCode
    left join (SELECT tbl_order_details.dropPointCode, DATE_FORMAT(tbl_order_details.partialTime,'%Y-%m-%d') as partialDate, count(1) as partial from tbl_order_details where DATE_FORMAT(tbl_order_details.partialTime,'%Y-%m-%d') between '$startDate' and '$endDate' and partial = 'Y' group by dropPointCode, DATE_FORMAT(tbl_order_details.partialTime,'%Y-%m-%d')) as partialOrders on tbl_point_info.pointCode = partialOrders.dropPointCode
    left join (SELECT tbl_order_details.dropPointCode, DATE_FORMAT(tbl_order_details.ReaTime,'%Y-%m-%d') as reaDate, count(1) as onHold from tbl_order_details where DATE_FORMAT(tbl_order_details.ReaTime,'%Y-%m-%d') between '$startDate' and '$endDate' and Rea = 'Y' group by dropPointCode, DATE_FORMAT(tbl_order_details.ReaTime,'%Y-%m-%d')) as onHoldOrders on tbl_point_info.pointCode = onHoldOrders.dropPointCode
    left join (SELECT * from tbl_onHoldPending_orders where DATE_FORMAT(creationDate, '%Y-%m-%d') = '$startDate') as onHoldSum on tbl_point_info.pointCode = onHoldSum.dropPointCode order by tbl_point_info.regionSort asc, tbl_point_info.pointCode asc, tbl_point_info.pointid desc ") or die("Error : failed to get point summary".mysqli_error($conn));           
 // echo "select pointid, pointCode, pointName, region, regionSort, shtlOrders.shuttle, dp2pick.pickDate, dp2pick.pick, slaMissed.slaCnt, employeeCount.empCount, cashOrders.cash, retOrders.ret, partialOrders.partial, onHoldOrders.onHold, onHoldSum.onHoldPending, pendingOrders.pending from tbl_point_info 
    //left join (SELECT dropPointCode, count(1) as slaCnt FROM `tbl_order_details` WHERE Cash is null and Ret is null and partial is null and close is null and Shtl='Y' and IF(destination='local',orderDate < (DATE_SUB(curdate(), INTERVAL 1 DAY)), orderDate < (DATE_SUB(curdate(), INTERVAL 3 DAY))) group by dropPointCode) as slaMissed on tbl_point_info.pointCode = slaMissed.dropPointCode 
    //left join (SELECT dropPointCode, count(distinct dropPointEmp) as empCount FROM tbl_order_details WHERE DATE_FORMAT(tbl_order_details.dropAssignTime,'%Y-%m-%d') = '$startDate' group by dropPointCode) as employeeCount on tbl_point_info.pointCode = employeeCount.dropPointCode 
    //left join (SELECT tbl_order_details.dropPointCode, DATE_FORMAT(tbl_order_details.ShtlTime,'%Y-%m-%d') as shuttleDate, count(1) as shuttle from tbl_order_details where DATE_FORMAT(tbl_order_details.ShtlTime,'%Y-%m-%d %H:%i') between '$shuttleStartTime' and '$shuttleEndTime' and Shtl = 'Y' group by  dropPointCode) as shtlOrders on tbl_point_info.pointCode = shtlOrders.dropPointCode
    //left join (SELECT * from tbl_pending_orders) as pendingOrders on tbl_point_info.pointCode = pendingOrders.dropPointCode
    //left join (SELECT tbl_order_details.dropPointCode, DATE_FORMAT(tbl_order_details.DP2Time,'%Y-%m-%d') as pickDate, count(1) as pick from tbl_order_details where DATE_FORMAT(tbl_order_details.DP2Time,'%Y-%m-%d') between '$startDate' and '$endDate' and DP2 = 'Y' group by  dropPointCode, DATE_FORMAT(tbl_order_details.DP2Time,'%Y-%m-%d')) as dp2pick on tbl_point_info.pointCode = dp2pick.dropPointCode
    //left join (SELECT tbl_order_details.dropPointCode, DATE_FORMAT(tbl_order_details.CashTime,'%Y-%m-%d') as cashDate, count(1) as cash from tbl_order_details where DATE_FORMAT(tbl_order_details.CashTime,'%Y-%m-%d') between '$startDate' and '$endDate' and cash = 'Y' group by  dropPointCode, DATE_FORMAT(tbl_order_details.CashTime,'%Y-%m-%d')) as cashOrders on tbl_point_info.pointCode = cashOrders.dropPointCode
    //left join (SELECT tbl_order_details.dropPointCode, DATE_FORMAT(tbl_order_details.RetTime,'%Y-%m-%d') as retDate, count(1) as ret from tbl_order_details where DATE_FORMAT(tbl_order_details.RetTime,'%Y-%m-%d') between '$startDate' and '$endDate' and Ret = 'Y' group by dropPointCode, DATE_FORMAT(tbl_order_details.RetTime,'%Y-%m-%d')) as retOrders on tbl_point_info.pointCode = retOrders.dropPointCode
    //left join (SELECT tbl_order_details.dropPointCode, DATE_FORMAT(tbl_order_details.partialTime,'%Y-%m-%d') as partialDate, count(1) as partial from tbl_order_details where DATE_FORMAT(tbl_order_details.partialTime,'%Y-%m-%d') between '$startDate' and '$endDate' and partial = 'Y' group by dropPointCode, DATE_FORMAT(tbl_order_details.partialTime,'%Y-%m-%d')) as partialOrders on tbl_point_info.pointCode = partialOrders.dropPointCode
    //left join (SELECT tbl_order_details.dropPointCode, DATE_FORMAT(tbl_order_details.ReaTime,'%Y-%m-%d') as reaDate, count(1) as onHold from tbl_order_details where DATE_FORMAT(tbl_order_details.ReaTime,'%Y-%m-%d') between '$startDate' and '$endDate' and Rea = 'Y' group by dropPointCode, DATE_FORMAT(tbl_order_details.ReaTime,'%Y-%m-%d')) as onHoldOrders on tbl_point_info.pointCode = onHoldOrders.dropPointCode
    //left join (SELECT * from tbl_onHoldPending_orders) as onHoldSum on tbl_point_info.pointCode = onHoldSum.dropPointCode order by tbl_point_info.regionSort asc, tbl_point_info.pointCode asc, tbl_point_info.pointid desc ";              
                $statusDateRow = mysqli_fetch_array(mysqli_query($conn, "select * from tbl_pending_orders  where DATE_FORMAT(creationDate, '%Y-%m-%d') = '$startDate' order by creationDate desc limit 1"));
                //echo "<br>";

                $statusDate = date('d-M-Y h:i A', strtotime($statusDateRow['creationDate']));
                
                echo '<thead><tr><th colspan=5></th><th colspan=4 style="text-align: center; background-color: #F5F5F5">Status at '.$statusDate.'</th><th colspan=6></th></tr></thead>';
                echo '<thead><tr><th>Point Code</th><th>Point Name</th><th>Pick Date</th><th>Shuttled Orders</th><th>Picked Orders</th><th style="text-align: center; background-color: #F5F5F5">Without Status</th><th style="text-align: center; background-color: #F5F5F5">on Hold Pendings</th><th style="text-align: center; background-color: #F5F5F5">Opening Balance</th><th style="color: red; background-color: #F5F5F5">SLA Missed</th><th>Capacity</th><th>Cash</th><th>Ret</th><th>Partial</th><th>on Hold</th><th>% Delivered</th></tr></thead><tbody>';
                function encryptor($action, $string) {
                    $output = false;

                    $encrypt_method = "AES-256-CBC";
                    //pls set your unique hashing key
                    $secret_key = 'shLitu';
                    $secret_iv = '12Litu34';

                    // hash
                    $key = hash('sha256', $secret_key);

                    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
                    $iv = substr(hash('sha256', $secret_iv), 0, 16);

                    //do the encyption given text/string/number
                    if( $action == 'encrypt' ) {
                        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
                        $output = base64_encode($output);
                    }
                    else if( $action == 'decrypt' ){
    	                //decrypt the given text/string/number
                        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
                    }

                    return $output;
                }

                $pointOrderCountResult = mysqli_query($conn, "SELECT dropPointCode, count(1) as ordCnt FROM tbl_order_details WHERE  Shtl ='Y' and dropDP2 is null and close is null group by dropPointCode");
                $pointOrder = 0;
                $regionOrderCount = 0;
                $shuttleCount = 0; 
                $dp2PickCount = 0 ;
                $onHoldCount = 0;
                $openingCount = 0;
                $cashCount = 0;
                $retCount = 0 ;
                $partialCount = 0; 
                $curOnHoldCount = 0;
                $withoutStatusCount = 0;
                $executiveCount = 0;
                $gtExecutiveCount = 0;
                $gtPointOrder = 0;
                $gtShuttle = 0 ;
                $slaMissedCount = 0;
                $gtPickCount = 0;
                $gtOnHoldCount =  0;
                $gtWithoutStatus = 0;
                $gtCashCount = 0;
                $gtReturnCount = 0;
                $gtPartialCount = 0;
                $gtOnHoldToday = 0;
                if($pointManager == '0'){
                    foreach($pointSumResult as $pointSumRow){
                        $pointCode = urlencode(encryptor('encrypt', $pointSumRow['pointCode']));
                        $openingBalance = $pointSumRow['onHoldPending'] + $pointSumRow['pending'];
                        $gtExecutiveCount = $gtExecutiveCount + $pointSumRow['empCount'];
                        $gtShuttle = $gtShuttle + $pointSumRow['shuttle'];
                        $gtPickCount = $gtPickCount + $pointSumRow['pick'];
                        $gtOnHoldCount = $gtOnHoldCount + $pointSumRow['onHoldPending'];
                        $gtslaMissedCount = $gtslaMissedCount + $pointSumRow['slaCnt'];
                        $gtWithoutStatus = $gtWithoutStatus + $pointSumRow['pending'];
                        $gtCashCount = $gtCashCount + $pointSumRow['cash'];
                        $gtReturnCount = $gtReturnCount + $pointSumRow['ret'];
                        $gtPartialCount = $gtPartialCount + $pointSumRow['partial'];
                        $gtOnHoldToday = $gtOnHoldToday + $pointSumRow['onHold'];
                        if($pointSumRow['pickDate'] == NULL){
                            $pickDate = '';
                        } else {
                            $pickDate = date('d-M-Y', strtotime($pointSumRow['pickDate']));
                        }
                        foreach($pointOrderCountResult as $pointOrderCount){
                            if($pointSumRow['pointCode'] == $pointOrderCount['dropPointCode']){
                                $pointOrder = $pointOrderCount['ordCnt'];
                                $regionOrderCount = $regionOrderCount + $pointOrder;
                                $gtPointOrder = $gtPointOrder + $pointOrder;
                            }
                        }
                        $success = round(($pointSumRow['cash']/($pointSumRow['pick'] + $pointSumRow['onHoldPending']))*100,0);
                        if($pointRegion == $pointSumRow['region']){
                            $openingCount = $openingCount + $openingBalance;
                            $executiveCount = $executiveCount + $pointSumRow['empCount'];
                            $shuttleCount = $shuttleCount + $pointSumRow['shuttle'];
                            $dp2PickCount = $dp2PickCount + $pointSumRow['pick'];
                            $slaMissedCount = $slaMissedCount + $pointSumRow['slaCnt'];
                            $onHoldCount = $onHoldCount + $pointSumRow['onHoldPending'];
                            $withoutStatusCount = $withoutStatusCount + $pointSumRow['pending'];
                            $cashCount = $cashCount + $pointSumRow['cash'];
                            $retCount = $retCount + $pointSumRow['ret'];
                            $partialCount = $partialCount + $pointSumRow['partial'];
                            $curOnHoldCount = $curOnHoldCount + $pointSumRow['onHold'];
                            if($pointRegion == 'Dhaka Central'){
                              //$deliveryPercent = round(((($pointSumRow['cash'])/$openingBalance) * 100),0); 
                              $deliveryPercent = round(((($pointSumRow['cash'])/$pointSumRow['slaCnt']) * 100),0); 
                            } else {
                              $deliveryPercent = round(((($pointSumRow['cash'])/$pointSumRow['slaCnt']) * 100),0);    
                            }
                            echo '<tr class="'.$pointSumRow['regionSort'].'" hidden><td><a href="Point-Status?xxCode='.$pointCode.'" target="_blank">'.$pointSumRow['pointCode'].' ('.$pointOrder.')</a></td><td>'.$pointSumRow['pointName'].'</td><td>'.$pickDate.'</td><td>'.$pointSumRow['shuttle'].'</td><td>'.$pointSumRow['pick'].'</td><td style="text-align: center; background-color: #F5F5F5"><a href="without-Status?xxCode='.$pointCode.'" target="_blank">'.$pointSumRow['pending'].'</a></td><td style="text-align: center; background-color: #F5F5F5">'.$pointSumRow['onHoldPending'].'</td><td style="text-align: center; background-color: #F5F5F5">'.$openingBalance.'</td><td style="background-color: #F5F5F5"><a style="color: red" href="Sla-Point-Status?xxCode='.$pointCode.'" target="_blank">'.$pointSumRow['slaCnt'].'</a></td><td><a href="Emp-Point-Status?xxCode='.$pointCode.'&startDate='.$startDate.'" target="_blank">'.$pointSumRow['empCount'].'</a></td><td>'.$pointSumRow['cash'].'</td><td>'.$pointSumRow['ret'].'</td><td>'.$pointSumRow['partial'].'</td><td>'.$pointSumRow['onHold'].'</td><td style="text-align: center">'.$deliveryPercent.'</td></tr>';
                        } else {
                            $pointRegion = $pointSumRow['region'];
                            if($regionOrderCount != 0){
                                $totalSuccess = round(($cashCount/($dp2PickCount+$onHoldCount))*100,0);
                                if($pointRegion == 'Dhaka Central'){
                                    //$deliveryPercent = round(((($cashCount)/$openingCount) * 100),0);
                                    $deliveryPercent = round(((($cashCount)/$slaMissedCount) * 100),0);
                                } else {
                                    $deliveryPercent = round(((($cashCount)/$slaMissedCount) * 100),0);
                                }
                                echo '<tr style="font-weight: 800"><td colspan = 3>Total : '.$regionOrderCount.'</td><td>'.$shuttleCount.'</td><td>'.$dp2PickCount.'</td><td style="text-align: center; background-color: #F5F5F5">'.$withoutStatusCount.'</td><td style="text-align: center; background-color: #F5F5F5">'.$onHoldCount.'</td><td style="text-align: center; background-color: #F5F5F5">'.$openingCount.'</td><td style="color: red; background-color: #F5F5F5">'.$slaMissedCount.'</td><td>'.$executiveCount.'</td><td>'.$cashCount.'</td><td>'.$retCount.'</td><td>'.$partialCount.'</td><td>'.$curOnHoldCount.'</td><td style="text-align: center">'.$deliveryPercent.'</td></tr>';    
                            }
                            $regionOrderCount = 0;
                            $shuttleCount = $pointSumRow['shuttle'];
                            $dp2PickCount = $pointSumRow['pick'];
                            $onHoldCount = $pointSumRow['onHoldPending'];
                            $openingCount = $openingBalance;
                            $slaMissedCount = $pointSumRow['slaCnt'];
                            $executiveCount = $pointSumRow['empCount'];
                            $cashCount = $pointSumRow['cash'];
                            $retCount = $pointSumRow['ret'];
                            $partialCount = $pointSumRow['partial'];
                            $curOnHoldCount = $pointSumRow['onHold'];
                            $withoutStatusCount = $pointSumRow['pending'];
                            echo '<tr><td colspan=2 style="color: #16469E; cursor: pointer; font-weight: 800" onclick="regionDetail('.$pointSumRow['regionSort'].')">'.$pointSumRow['region'].'<p style="font-size: 8px; display: inline-block">&nbsp;&nbsp;(Click for Detail)</p></td></tr>';
                            if($pointRegion == 'Dhaka Central'){
                                //$deliveryPercent = round(((($pointSumRow['cash'])/$openingBalance) * 100),0);
                                $deliveryPercent = round(((($pointSumRow['cash'])/$pointSumRow['slaCnt']) * 100),0);
                            } else {
                                $deliveryPercent = round(((($pointSumRow['cash'])/$pointSumRow['slaCnt']) * 100),0);
                            }
                            echo '<tr class="'.$pointSumRow['regionSort'].'" hidden><td><a href="Point-Status?xxCode='.$pointCode.'" target="_blank">'.$pointSumRow['pointCode'].' ('.$pointOrder.')</a></td><td>'.$pointSumRow['pointName'].'</td><td>'.$pickDate.'</td><td>'.$pointSumRow['shuttle'].'</td><td>'.$pointSumRow['pick'].'</td><td style="text-align: center; background-color: #F5F5F5">'.$pointSumRow['pending'].'</td><td style="text-align: center; background-color: #F5F5F5">'.$pointSumRow['onHoldPending'].'</td><td style="text-align: center; background-color: #F5F5F5">'.$openingBalance.'</td><td style="color: red; background-color: #F5F5F5"><a style="color: red" href="Sla-Point-Status?xxCode='.$pointCode.'" target="_blank">'.$pointSumRow['slaCnt'].'</a></td><td><a href="Emp-Point-Status?xxCode='.$pointCode.'&startDate='.$startDate.'" target="_blank">'.$pointSumRow['empCount'].'</a></td><td>'.$pointSumRow['cash'].'</td><td>'.$pointSumRow['ret'].'</td><td>'.$pointSumRow['partial'].'</td><td>'.$pointSumRow['onHold'].'</td><td style="text-align: center">'.$deliveryPercent.'</td></tr>';
                        }
                        $pointOrder = 0;   
                    }
                    if($regionOrderCount != 0){
                        $totalSuccess = round(($cashCount/($dp2PickCount+$onHoldCount))*100,0);
                        if($pointRegion == 'Dhaka Central'){
                            //$deliveryPercent = round(((($cashCount)/$openingCount) * 100),0);
                            $deliveryPercent = round(((($cashCount)/$slaMissedCount) * 100),0);
                        } else {
                            $deliveryPercent = round(((($cashCount)/$slaMissedCount) * 100),0);
                        }
                        echo '<tr style="font-weight: 800"><td colspan = 3>Total : '.$regionOrderCount.'</td><td>'.$shuttleCount.'</td><td>'.$dp2PickCount.'</td><td style="text-align: center; background-color: #F5F5F5">'.$withoutStatusCount.'</td><td style="text-align: center; background-color: #F5F5F5">'.$onHoldCount.'</td><td style="text-align: center; background-color: #F5F5F5">'.$openingCount.'</td><td style="color: red; background-color: #F5F5F5">'.$slaMissedCount.'</td><td>'.$executiveCount.'</td><td>'.$cashCount.'</td><td>'.$retCount.'</td><td>'.$partialCount.'</td><td>'.$curOnHoldCount.'</td><td style="text-align: center">'.$deliveryPercent.'</td></tr>';    
                    }
                    $regionOrderCount = 0;
                    $shuttleCount = 0;
                    $dp2PickCount = 0;
                    $onHoldCount = 0;
                    $openingCount = 0;
                    $slaMissedCount = 0;
                    $executiveCount = 0;
                    $cashCount = 0;
                    $retCount = 0;
                    $partialCount = 0;
                    $curOnHoldCount = 0;
                    $withoutStatusCount = 0;                                
                    $gtOPeningCount =  $gtOnHoldCount + $gtWithoutStatus;
                    $gtSuccess = round(($gtCashCount/($gtPickCount + $gtOnHoldCount))*100,0);
                    if($pointRegion == 'Dhaka Central'){
                        //$deliveryPercent = round(((($gtCashCount)/$gtOPeningCount) * 100),0);
                        $deliveryPercent = round(((($gtCashCount)/$gtslaMissedCount) * 100),0);
                    } else {
                        $deliveryPercent = round(((($gtCashCount)/$gtslaMissedCount) * 100),0);
                    }
                    echo '<tr style="font-weight: 800"><td>Grand Total : '.$gtPointOrder.'</td><td></td><td></td><td>'.$gtShuttle.'</td><td>'.$gtPickCount.'</td><td style="text-align: center; background-color: #F5F5F5">'.$gtWithoutStatus.'</td><td style="text-align: center; background-color: #F5F5F5">'.$gtOnHoldCount.'</td><td style="text-align: center; background-color: #F5F5F5">'.$gtOPeningCount.'</td><td style="color: red; background-color: #F5F5F5">'.$gtslaMissedCount.'</td><td>'.$gtExecutiveCount.'</td><td>'.$gtCashCount.'</td><td>'.$gtReturnCount.'</td><td>'.$gtPartialCount.'</td><td>'.$gtOnHoldToday.'</td><td style="text-align: center">'.$deliveryPercent.'</td></tr>';
                } else {
                    $regionOrderCount = 0;
                    foreach($pointSumResult as $pointSumRow){
                        $pointCode = urlencode(encryptor('encrypt', $pointSumRow['pointCode']));
                        $empPointCode = urlencode(encryptor('encrypt', $pointManager));
                        foreach($pointOrderCountResult as $pointOrderCount){
                            if($pointSumRow['pointCode'] == $pointOrderCount['dropPointCode']){
                                $pointOrder = $pointOrderCount['ordCnt'];
                            }
                        }
                        foreach($pointListResult as $pointListRow){
                            $openingBalance = $pointSumRow['onHoldPending'] + $pointSumRow['pending'];
                            if($pointSumRow['pickDate'] == NULL){
                                $pickDate = '';
                            } else {
                                $pickDate = date('d-M-Y', strtotime($pointSumRow['pickDate']));
                            }
                            if($pointSumRow['pointCode'] == $pointListRow['pointCode']){
                                $regionOrderCount = $regionOrderCount + $pointOrder;
                                $shuttleCount = $shuttleCount + $pointSumRow['shuttle'];
                                $dp2PickCount = $dp2PickCount + $pointSumRow['pick'];
                                $onHoldCount = $onHoldCount + $pointSumRow['onHoldPending'];
                                $openingCount = $openingCount + $openingBalance;
                                $slaMissedCount = $slaMissedCount + $pointSumRow['slaCnt'];
                                $executiveCount = $executiveCount + $pointSumRow['empCount'];
                                $gtExecutiveCount = $gtExecutiveCount + $pointSumRow['empCount'];
                                $cashCount = $cashCount + $pointSumRow['cash'];
                                $retCount = $retCount + $pointSumRow['ret'];
                                $partialCount = $partialCount + $pointSumRow['partial'];
                                $curOnHoldCount = $curOnHoldCount + $pointSumRow['onHold'];
                                $withoutStatusCount = $withoutStatusCount + $pointSumRow['pending'];
                                $gtShuttle = $gtShuttle + $pointSumRow['shuttle'];
                                $gtPickCount = $gtPickCount + $pointSumRow['pick'];
                                $gtOnHoldCount = $gtOnHoldCount + $pointSumRow['onHoldPending'];
                                $gtslaMissedCount = $gtslaMissedCount + $pointSumRow['slaCnt'];
                                $gtWithoutStatus = $gtWithoutStatus + $pointSumRow['pending'];
                                $gtCashCount = $gtCashCount + $pointSumRow['cash'];
                                $gtReturnCount = $gtReturnCount + $pointSumRow['ret'];
                                $gtPartialCount = $gtPartialCount + $pointSumRow['partial'];
                                $gtOnHoldToday = $gtOnHoldToday + $pointSumRow['onHold'];
                                $gtPointOrder = $gtPointOrder + $pointOrder;
                                $success = round(($pointSumRow['cash']/($pointSumRow['pick'] + $pointSumRow['onHoldPending']))*100,0);
                                if($pointRegion == 'Dhaka Central'){
                                    //$deliveryPercent = round(((($pointSumRow['cash'])/$openingBalance) * 100),0);
                                    $deliveryPercent = round(((($pointSumRow['cash'])/$pointSumRow['slaCnt']) * 100),0);
                                } else {
                                    $deliveryPercent = round(((($pointSumRow['cash'])/$pointSumRow['slaCnt']) * 100),0);
                                }
                                if($pointRegion == $pointSumRow['region']){
                                    echo '<tr class="'.$pointSumRow['regionSort'].'" hidden><td><a href="Point-Status?xxCode='.$pointCode.'" target="_blank">'.$pointSumRow['pointCode'].' ('.$pointOrder.')</a></td><td>'.$pointSumRow['pointName'].'</td><td>'.$pickDate.'</td><td>'.$pointSumRow['shuttle'].'</td><td>'.$pointSumRow['pick'].'</td><td style="text-align: center; background-color: #F5F5F5"><a href="without-Status?xxCode='.$pointCode.'&zzCode='.$empPointCode.'" target="_blank">'.$pointSumRow['pending'].'</a></td><td style="text-align: center; background-color: #F5F5F5">'.$pointSumRow['onHoldPending'].'</td><td style="text-align: center; background-color: #F5F5F5">'.$openingBalance.'</td><td style="color: red; background-color: #F5F5F5"><a style="color: red" href="Sla-Point-Status?xxCode='.$pointCode.'" target="_blank">'.$pointSumRow['slaCnt'].'</a></td><td><a href="Emp-Point-Status?xxCode='.$pointCode.'&startDate='.$startDate.'" target="_blank">'.$pointSumRow['empCount'].'</a></td><td>'.$pointSumRow['cash'].'</td><td>'.$pointSumRow['ret'].'</td><td>'.$pointSumRow['partial'].'</td><td>'.$pointSumRow['onHold'].'</td><td style="text-align: center">'.$deliveryPercent.'</td></tr>';    
                                } else {
                                    $pointRegion = $pointSumRow['region'];
                                    echo '<tr><td <td colspan=2 style="color: #16469E; cursor: pointer" onclick="regionDetail('.$pointSumRow['regionSort'].')"><b>'.$pointRegion.'</b><p style="font-size: 8px; display: inline-block">&nbsp;&nbsp;(Click for Detail)</p></td></tr>';
                                    echo '<tr class="'.$pointSumRow['regionSort'].'" hidden><td><a href="Point-Status?xxCode='.$pointCode.'" target="_blank">'.$pointSumRow['pointCode'].' ('.$pointOrder.')</a></td><td>'.$pointSumRow['pointName'].'</td><td>'.$pickDate.'</td><td>'.$pointSumRow['shuttle'].'</td><td>'.$pointSumRow['pick'].'</td><td style="text-align: center; background-color: #F5F5F5"><a href="without-Status?xxCode='.$pointCode.'&zzCode='.$empPointCode.'" target="_blank">'.$withoutStatusCount.'</a></td><td style="text-align: center; background-color: #F5F5F5">'.$pointSumRow['onHoldPending'].'</td><td style="text-align: center; background-color: #F5F5F5">'.$openingBalance.'</td><td style="color: red; background-color: #F5F5F5"><a style="color: red" href="Sla-Point-Status?xxCode='.$pointCode.'" target="_blank">'.$pointSumRow['slaCnt'].'</a></td><td><a href="Emp-Point-Status?xxCode='.$pointCode.'&startDate='.$startDate.'" target="_blank">'.$pointSumRow['empCount'].'</a></td><td>'.$pointSumRow['cash'].'</td><td>'.$pointSumRow['ret'].'</td><td>'.$pointSumRow['partial'].'</td><td>'.$pointSumRow['onHold'].'</td><td style="text-align: center">'.$deliveryPercent.'</td></tr>';    

                                }
                            }
                            if($pointRegion != $pointSumRow['region']){
                                if($regionOrderCount != 0){
                                    $totalSuccess = round(($cashCount/($dp2PickCount+$onHoldCount))*100,0);
                                    if($pointRegion == 'Dhaka Central'){
                                        //$deliveryPercent = round((($cashCount/$openingCount) * 100),0);
                                        $deliveryPercent = round((($cashCount/$slaMissedCount) * 100),0);
                                    } else {
                                        $deliveryPercent = round((($cashCount/$slaMissedCount) * 100),0);
                                    }
                                    echo '<tr style="font-weight: 800"><td colspan = 3>Total : '.$regionOrderCount.'</td><td>'.$shuttleCount.'</td><td>'.$dp2PickCount.'</td><td style="text-align: center; background-color: #F5F5F5">'.$withoutStatusCount.'</td><td style="text-align: center; background-color: #F5F5F5">'.$onHoldCount.'</td><td style="text-align: center; background-color: #F5F5F5">'.$openingCount.'</td><td style="color: red; background-color: #F5F5F5">'.$slaMissedCount.'</td><td>'.$executiveCount.'</td><td>'.$cashCount.'</td><td>'.$retCount.'</td><td>'.$partialCount.'</td><td>'.$curOnHoldCount.'</td><td style="text-align: center">'.$deliveryPercent.'</td></tr>';    
                                }
                                $regionOrderCount = 0;
                                $shuttleCount = 0;
                                $dp2PickCount = 0;
                                $onHoldCount = 0;
                                $openingCount = 0;
                                $slaMissedCount = 0;
                                $executiveCount = 0;
                                $cashCount = 0;
                                $retCount = 0;
                                $partialCount = 0;
                                $curOnHoldCount = 0;
                                $withoutStatusCount = 0;
                            }
                        }
                        $pointOrder = 0;
                    }                
                    $gtOPeningCount =  $gtOnHoldCount + $gtWithoutStatus;
                    $gtSuccess = round(($gtCashCount/($gtPickCount + $gtOnHoldCount))*100,0);
                    if($pointRegion == 'Dhaka Central'){
                        //$deliveryPercent = round((($gtCashCount/$gtOPeningCount) * 100),0);
                        $deliveryPercent = round((($gtCashCount/$gtslaMissedCount) * 100),0);
                    } else {
                        $deliveryPercent = round((($gtCashCount/$gtslaMissedCount) * 100),0);
                    }
                    echo '<tr style="font-weight: 800"><td>Grand Total : '.$gtPointOrder.'</td><td></td><td></td><td>'.$gtShuttle.'</td><td>'.$gtPickCount.'</td><td style="text-align: center; background-color: #F5F5F5">'.$gtWithoutStatus.'</td><td style="text-align: center; background-color: #F5F5F5">'.$gtOnHoldCount.'</td><td style="text-align: center; background-color: #F5F5F5">'.$gtOPeningCount.'</td><td style="color: red; background-color: #F5F5F5">'.$gtslaMissedCount.'</td><td>'.$gtExecutiveCount.'</td><td>'.$gtCashCount.'</td><td>'.$gtReturnCount.'</td><td>'.$gtPartialCount.'</td><td>'.$gtOnHoldToday.'</td><td style="text-align: center">'.$deliveryPercent.'</td></tr>';
                }
                echo '</tbody>';


            }
            if($flag == 'pointManagerPerfMerchant'){
                $pointManager = $_POST['pointManager'];
                $merchant = $_POST['merchant'];
                $startDate = date("Y-m-d", strtotime($_POST['startDate']));
                $endDate = date("Y-m-d", strtotime($_POST['startDate']));

                $shuttleStartDate = date('Y-m-d', strtotime('-1 day', strtotime($startDate)));

                $shuttleStartTime = $shuttleStartDate.' 11:00';
                $shuttleEndTime = $endDate.' 11:00';
            
                $pointListResult = mysqli_query($conn, "select * from tbl_employee_point where empCode = '$pointManager'") or die("Error : failed to get point info".mysqli_error($conn));
                $pointRegion = '';

                $pointSumResult = mysqli_query($conn, "select pointid, pointCode, pointName, region, regionSort, shtlOrders.shuttle, dp2pick.pickDate, dp2pick.pick, slaMissed.slaCnt, employeeCount.empCount, cashOrders.cash, retOrders.ret, partialOrders.partial, onHoldOrders.onHold, onHoldSum.onHoldPending, pendingOrders.pending from tbl_point_info 
    left join (SELECT dropPointCode, count(1) as slaCnt FROM `tbl_order_details` WHERE Cash is null and Ret is null and partial is null and close is null and Shtl='Y' and IF(destination='local',orderDate < (DATE_SUB(curdate(), INTERVAL 1 DAY)), orderDate < (DATE_SUB(curdate(), INTERVAL 3 DAY))) and merchantCode = '$merchant' group by dropPointCode) as slaMissed on tbl_point_info.pointCode = slaMissed.dropPointCode 
    left join (SELECT dropPointCode, count(distinct dropPointEmp) as empCount FROM tbl_order_details WHERE tbl_order_details.merchantCode = '$merchant' and DATE_FORMAT(tbl_order_details.dropAssignTime,'%Y-%m-%d') = '$startDate' group by dropPointCode) as employeeCount on tbl_point_info.pointCode = employeeCount.dropPointCode  
    left join (SELECT tbl_order_details.dropPointCode, DATE_FORMAT(tbl_order_details.ShtlTime,'%Y-%m-%d') as shuttleDate, count(1) as shuttle from tbl_order_details where tbl_order_details.merchantCode = '$merchant' and DATE_FORMAT(tbl_order_details.ShtlTime,'%Y-%m-%d %H:%i') between '$shuttleStartTime' and '$shuttleEndTime' and Shtl = 'Y' group by  dropPointCode) as shtlOrders on tbl_point_info.pointCode = shtlOrders.dropPointCode
    left join (SELECT dropPointCode, count(1) as pending FROM `tbl_order_details` WHERE tbl_order_details.merchantCode = '$merchant' and Shtl = 'Y' and Cash is null and Ret is null and partial is null and Rea is null and close is null group by dropPointCode) as pendingOrders on tbl_point_info.pointCode = pendingOrders.dropPointCode
    left join (SELECT tbl_order_details.dropPointCode, DATE_FORMAT(tbl_order_details.DP2Time,'%Y-%m-%d') as pickDate, count(1) as pick from tbl_order_details where tbl_order_details.merchantCode = '$merchant' and DATE_FORMAT(tbl_order_details.DP2Time,'%Y-%m-%d') between '$startDate' and '$endDate' and DP2 = 'Y' group by  dropPointCode, DATE_FORMAT(tbl_order_details.DP2Time,'%Y-%m-%d')) as dp2pick on tbl_point_info.pointCode = dp2pick.dropPointCode
    left join (SELECT tbl_order_details.dropPointCode, DATE_FORMAT(tbl_order_details.CashTime,'%Y-%m-%d') as cashDate, count(1) as cash from tbl_order_details where tbl_order_details.merchantCode = '$merchant' and DATE_FORMAT(tbl_order_details.CashTime,'%Y-%m-%d') between '$startDate' and '$endDate' and cash = 'Y' group by  dropPointCode, DATE_FORMAT(tbl_order_details.CashTime,'%Y-%m-%d')) as cashOrders on tbl_point_info.pointCode = cashOrders.dropPointCode
    left join (SELECT tbl_order_details.dropPointCode, DATE_FORMAT(tbl_order_details.RetTime,'%Y-%m-%d') as retDate, count(1) as ret from tbl_order_details where tbl_order_details.merchantCode = '$merchant' and DATE_FORMAT(tbl_order_details.RetTime,'%Y-%m-%d') between '$startDate' and '$endDate' and Ret = 'Y' group by dropPointCode, DATE_FORMAT(tbl_order_details.RetTime,'%Y-%m-%d')) as retOrders on tbl_point_info.pointCode = retOrders.dropPointCode
    left join (SELECT tbl_order_details.dropPointCode, DATE_FORMAT(tbl_order_details.partialTime,'%Y-%m-%d') as partialDate, count(1) as partial from tbl_order_details where tbl_order_details.merchantCode = '$merchant' and DATE_FORMAT(tbl_order_details.partialTime,'%Y-%m-%d') between '$startDate' and '$endDate' and partial = 'Y' group by dropPointCode, DATE_FORMAT(tbl_order_details.partialTime,'%Y-%m-%d')) as partialOrders on tbl_point_info.pointCode = partialOrders.dropPointCode
    left join (SELECT tbl_order_details.dropPointCode, DATE_FORMAT(tbl_order_details.ReaTime,'%Y-%m-%d') as reaDate, count(1) as onHold from tbl_order_details where tbl_order_details.merchantCode = '$merchant' and DATE_FORMAT(tbl_order_details.ReaTime,'%Y-%m-%d') between '$startDate' and '$endDate' and Rea = 'Y' group by dropPointCode, DATE_FORMAT(tbl_order_details.ReaTime,'%Y-%m-%d')) as onHoldOrders on tbl_point_info.pointCode = onHoldOrders.dropPointCode
    left join (SELECT tbl_order_details.dropPointCode, count(1) as onHoldPending from tbl_order_details where tbl_order_details.merchantCode = '$merchant' and DATE_FORMAT(tbl_order_details.orderDate,'%Y-%m-%d') < '$startDate' and (Rea = 'Y' and close is null and Cash is null and Ret is null and partial is null) group by  dropPointCode) as onHoldSum on tbl_point_info.pointCode = onHoldSum.dropPointCode order by tbl_point_info.regionSort asc, tbl_point_info.pointCode asc, tbl_point_info.pointid desc ") or die("Error : failed to get point summary".mysqli_error($conn));           
            
                echo '<thead><tr><th>Point Code</th><th>Point Name</th><th>Pick Date</th><th>Shuttled Orders</th><th>Picked Orders</th><th>Without Status</th><th>on Hold Pendings</th><th>Opening Balance</th><th style="color: red">SLA Missed</th><th>Capacity</th><th>Cash</th><th>Ret</th><th>Partial</th><th>on Hold</th></tr></thead><tbody>';
                function encryptor($action, $string) {
                    $output = false;

                    $encrypt_method = "AES-256-CBC";
                    //pls set your unique hashing key
                    $secret_key = 'shLitu';
                    $secret_iv = '12Litu34';

                    // hash
                    $key = hash('sha256', $secret_key);

                    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
                    $iv = substr(hash('sha256', $secret_iv), 0, 16);

                    //do the encyption given text/string/number
                    if( $action == 'encrypt' ) {
                        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
                        $output = base64_encode($output);
                    }
                    else if( $action == 'decrypt' ){
    	                //decrypt the given text/string/number
                        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
                    }

                    return $output;
                }

                $pointOrderCountResult = mysqli_query($conn, "SELECT dropPointCode, count(1) as ordCnt FROM tbl_order_details WHERE merchantCode = '$merchant' and Shtl ='Y' and dropDP2 is null and close is null group by dropPointCode");
                $pointOrder = 0;
                $regionOrderCount = 0;
                $shuttleCount = 0; 
                $dp2PickCount = 0 ;
                $onHoldCount = 0;
                $openingCount = 0;
                $slaMissedCount = 0;
                $executiveCount = 0;
                $gtExecutiveCount = 0;
                $cashCount = 0;
                $retCount = 0 ;
                $partialCount = 0; 
                $curOnHoldCount = 0;
                $withoutStatusCount = 0;
                $gtPointOrder = 0;
                $gtShuttle = 0 ;
                $gtPickCount = 0;
                $gtOnHoldCount =  0;
                $gtWithoutStatus = 0;
                $gtCashCount = 0;
                $gtReturnCount = 0;
                $gtPartialCount = 0;
                $gtOnHoldToday = 0;
                $merchant = urlencode(encryptor('encrypt', $merchant));
                if($pointManager == '0'){
                    foreach($pointSumResult as $pointSumRow){
                        $pointCode = urlencode(encryptor('encrypt', $pointSumRow['pointCode']));
                        $openingBalance = $pointSumRow['onHoldPending'] + $pointSumRow['pending'];
                        $shuttleCount = $shuttleCount + $pointSumRow['shuttle'];
                        $dp2PickCount = $dp2PickCount + $pointSumRow['pick'];
                        $onHoldCount = $onHoldCount + $pointSumRow['onHoldPending'];
                        $openingCount = $openingCount + $openingBalance;
                        $slaMissedCount = $slaMissedCount + $pointSumRow['slaCnt'];
                        $executiveCount = $executiveCount + $pointSumRow['empCount'];
                        $gtExecutiveCount = $gtExecutiveCount + $pointSumRow['empCount'];
                        $cashCount = $cashCount + $pointSumRow['cash'];
                        $retCount = $retCount + $pointSumRow['ret'];
                        $partialCount = $partialCount + $pointSumRow['partial'];
                        $curOnHoldCount = $curOnHoldCount + $pointSumRow['onHold'];
                        $withoutStatusCount = $withoutStatusCount + $pointSumRow['pending'];
                        $gtShuttle = $gtShuttle + $pointSumRow['shuttle'];
                        $gtPickCount = $gtPickCount + $pointSumRow['pick'];
                        $gtOnHoldCount = $gtOnHoldCount + $pointSumRow['onHoldPending'];
                        $gtslaMissedCount = $gtslaMissedCount + $pointSumRow['slaCnt'];
                        $gtWithoutStatus = $gtWithoutStatus + $pointSumRow['pending'];
                        $gtCashCount = $gtCashCount + $pointSumRow['cash'];
                        $gtReturnCount = $gtReturnCount + $pointSumRow['ret'];
                        $gtPartialCount = $gtPartialCount + $pointSumRow['partial'];
                        $gtOnHoldToday = $gtOnHoldToday + $pointSumRow['onHold'];
                        if($pointSumRow['pickDate'] == NULL){
                            $pickDate = '';
                        } else {
                            $pickDate = date('d-M-Y', strtotime($pointSumRow['pickDate']));
                        }
                        foreach($pointOrderCountResult as $pointOrderCount){
                            if($pointSumRow['pointCode'] == $pointOrderCount['dropPointCode']){
                                $pointOrder = $pointOrderCount['ordCnt'];
                                $regionOrderCount = $regionOrderCount + $pointOrder;
                                $gtPointOrder = $gtPointOrder + $pointOrder;
                            }
                        }
                        $success = round(($pointSumRow['cash']/($pointSumRow['pick'] + $pointSumRow['onHoldPending']))*100,0);
                        if($pointRegion == $pointSumRow['region']){
                            echo '<tr class="'.$pointSumRow['regionSort'].'" hidden><td><a href="Point-Status?xxCode='.$pointCode.'&yyCode='.$merchant.'" target="_blank">'.$pointSumRow['pointCode'].' ('.$pointOrder.')</a></td><td>'.$pointSumRow['pointName'].'</td><td>'.$pickDate.'</td><td>'.$pointSumRow['shuttle'].'</td><td>'.$pointSumRow['pick'].'</td><td><a href="without-Status?xxCode='.$pointCode.'&yyCode='.$merchant.'" target="_blank">'.$pointSumRow['pending'].'</a></td><td>'.$pointSumRow['onHoldPending'].'</td><td>'.$openingBalance.'</td><td style="color: red"><a style="color: red" href="Sla-Point-Status?xxCode='.$pointCode.'&yyCode='.$merchant.'" target="_blank">'.$pointSumRow['slaCnt'].'</a></td><td><a href="Emp-Point-Status?xxCode='.$pointCode.'&yyCode='.$merchant.'&startDate='.$startDate.'" target="_blank">'.$pointSumRow['empCount'].'</a></td><td>'.$pointSumRow['cash'].'</td><td>'.$pointSumRow['ret'].'</td><td>'.$pointSumRow['partial'].'</td><td>'.$pointSumRow['onHold'].'</td></tr>';
                        } else {
                            $pointRegion = $pointSumRow['region'];
                            if($regionOrderCount != 0){
                                $totalSuccess = round(($cashCount/($dp2PickCount+$onHoldCount))*100,0);
                                echo '<tr style="font-weight: 800"><td colspan = 3>Total : '.$regionOrderCount.'</td><td>'.$shuttleCount.'</td><td>'.$dp2PickCount.'</td><td>'.$withoutStatusCount.'</td><td>'.$onHoldCount.'</td><td>'.$openingCount.'</td><td style="color: red">'.$slaMissedCount.'</td><td>'.$executiveCount.'</td><td>'.$cashCount.'</td><td>'.$retCount.'</td><td>'.$partialCount.'</td><td>'.$curOnHoldCount.'</td></tr>';    
                            }
                            $regionOrderCount = 0;
                            $shuttleCount = 0;
                            $dp2PickCount = 0;
                            $onHoldCount = 0;
                            $openingCount = 0;
                            $slaMissedCount = 0;
                            $executiveCount = 0;
                            $cashCount = 0;
                            $retCount = 0;
                            $partialCount = 0;
                            $curOnHoldCount = 0;
                            $withoutStatusCount = 0;
                            echo '<tr><td colspan=2 style="color: #16469E; cursor: pointer"  onclick="regionDetail('.$pointSumRow['regionSort'].')"><b>'.$pointSumRow['region'].'</b><p style="font-size: 8px; display: inline-block">&nbsp;&nbsp;(Click for Detail)</p></td></tr>';
                            echo '<tr class="'.$pointSumRow['regionSort'].'" hidden><td><a href="Point-Status?xxCode='.$pointCode.'&yyCode='.$merchant.'" target="_blank">'.$pointSumRow['pointCode'].' ('.$pointOrder.')</a></td><td>'.$pointSumRow['pointName'].'</td><td>'.$pickDate.'</td><td>'.$pointSumRow['shuttle'].'</td><td>'.$pointSumRow['pick'].'</td><td><a href="without-Status?xxCode='.$pointCode.'&yyCode='.$merchant.'" target="_blank">'.$pointSumRow['pending'].'</a></td><td>'.$pointSumRow['onHoldPending'].'</td><td>'.$openingBalance.'</td><td style="color: red"><a style="color: red" href="Sla-Point-Status?xxCode='.$pointCode.'&yyCode='.$merchant.'" target="_blank">'.$pointSumRow['slaCnt'].'</a></td><td><a href="Emp-Point-Status?xxCode='.$pointCode.'&yyCode='.$merchant.'&startDate='.$startDate.'" target="_blank">'.$pointSumRow['empCount'].'</a></td><td>'.$pointSumRow['cash'].'</td><td>'.$pointSumRow['ret'].'</td><td>'.$pointSumRow['partial'].'</td><td>'.$pointSumRow['onHold'].'</td></tr>';
                        }
                        $pointOrder = 0;   
                    }                
                    if($regionOrderCount != 0){
                        $totalSuccess = round(($cashCount/($dp2PickCount+$onHoldCount))*100,0);
                        echo '<tr style="font-weight: 800"><td colspan = 3>Total : '.$regionOrderCount.'</td><td>'.$shuttleCount.'</td><td>'.$dp2PickCount.'</td><td>'.$withoutStatusCount.'</td><td>'.$onHoldCount.'</td><td>'.$openingCount.'</td><td style="color: red">'.$slaMissedCount.'</td><td>'.$executiveCount.'</td><td>'.$cashCount.'</td><td>'.$retCount.'</td><td>'.$partialCount.'</td><td>'.$curOnHoldCount.'</td></tr>';    
                    }
                    $regionOrderCount = 0;
                    $shuttleCount = 0;
                    $dp2PickCount = 0;
                    $onHoldCount = 0;
                    $openingCount = 0;
                    $slaMissedCount = 0;
                    $executiveCount = 0;
                    $cashCount = 0;
                    $retCount = 0;
                    $partialCount = 0;
                    $curOnHoldCount = 0;
                    $withoutStatusCount = 0;
                    $gtOPeningCount =  $gtOnHoldCount + $gtWithoutStatus;
                    $gtSuccess = round(($gtCashCount/($gtPickCount + $gtOnHoldCount))*100,0);
                    echo '<tr style="font-weight: 800"><td>Grand Total : '.$gtPointOrder.'</td><td></td><td></td><td>'.$gtShuttle.'</td><td>'.$gtPickCount.'</td><td>'.$gtWithoutStatus.'</td><td>'.$gtOnHoldCount.'</td><td>'.$gtOPeningCount.'</td><td style="color: red">'.$gtslaMissedCount.'</td><td>'.$gtExecutiveCount.'</td><td>'.$gtCashCount.'</td><td>'.$gtReturnCount.'</td><td>'.$gtPartialCount.'</td><td>'.$gtOnHoldToday.'</td></tr>';
                } else {
                    $regionOrderCount = 0;
                    foreach($pointSumResult as $pointSumRow){
                        $pointCode = urlencode(encryptor('encrypt', $pointSumRow['pointCode']));
                        foreach($pointOrderCountResult as $pointOrderCount){
                            if($pointSumRow['pointCode'] == $pointOrderCount['dropPointCode']){
                                $pointOrder = $pointOrderCount['ordCnt'];
                            }
                        }
                        foreach($pointListResult as $pointListRow){
                            $openingBalance = $pointSumRow['onHoldPending'] + $pointSumRow['pending'];
                            if($pointSumRow['pickDate'] == NULL){
                                $pickDate = '';
                            } else {
                                $pickDate = date('d-M-Y', strtotime($pointSumRow['pickDate']));
                            }
                            if($pointSumRow['pointCode'] == $pointListRow['pointCode']){
                                $regionOrderCount = $regionOrderCount + $pointOrder;
                                $shuttleCount = $shuttleCount + $pointSumRow['shuttle'];
                                $dp2PickCount = $dp2PickCount + $pointSumRow['pick'];
                                $onHoldCount = $onHoldCount + $pointSumRow['onHoldPending'];
                                $openingCount = $openingCount + $openingBalance;
                                $slaMissedCount = $slaMissedCount + $pointSumRow['slaCnt'];
                                $executiveCount = $executiveCount + $pointSumRow['empCount'];
                                $gtExecutiveCount = $gtExecutiveCount + $pointSumRow['empCount'];
                                $cashCount = $cashCount + $pointSumRow['cash'];
                                $retCount = $retCount + $pointSumRow['ret'];
                                $partialCount = $partialCount + $pointSumRow['partial'];
                                $curOnHoldCount = $curOnHoldCount + $pointSumRow['onHold'];
                                $withoutStatusCount = $withoutStatusCount + $pointSumRow['pending'];
                                $gtShuttle = $gtShuttle + $pointSumRow['shuttle'];
                                $gtPickCount = $gtPickCount + $pointSumRow['pick'];
                                $gtOnHoldCount = $gtOnHoldCount + $pointSumRow['onHoldPending'];
                                $gtWithoutStatus = $gtWithoutStatus + $pointSumRow['pending'];
                                $gtCashCount = $gtCashCount + $pointSumRow['cash'];
                                $gtReturnCount = $gtReturnCount + $pointSumRow['ret'];
                                $gtPartialCount = $gtPartialCount + $pointSumRow['partial'];
                                $gtOnHoldToday = $gtOnHoldToday + $pointSumRow['onHold'];
                                $gtPointOrder = $gtPointOrder + $pointOrder;
                                $success = round(($pointSumRow['cash']/($pointSumRow['pick'] + $pointSumRow['onHoldPending']))*100,0);
                                if($pointRegion == $pointSumRow['region']){
                                    echo '<tr class="'.$pointSumRow['regionSort'].'" hidden><td><a href="Point-Status?xxCode='.$pointCode.'&yyCode='.$merchant.'" target="_blank">'.$pointSumRow['pointCode'].' ('.$pointOrder.')</a></td><td>'.$pointSumRow['pointName'].'</td><td>'.$pickDate.'</td><td>'.$pointSumRow['shuttle'].'</td><td>'.$pointSumRow['pick'].'</td><td><a href="without-Status?xxCode='.$pointCode.'&yyCode='.$merchant.'" target="_blank">'.$pointSumRow['pending'].'</a></td><td>'.$pointSumRow['onHoldPending'].'</td><td>'.$openingBalance.'</td><td style="color: red"><a style="color: red" href="Sla-Point-Status?xxCode='.$pointCode.'&yyCode='.$merchant.'" target="_blank">'.$pointSumRow['slaCnt'].'</a></td><td><a href="Emp-Point-Status?xxCode='.$pointCode.'&yyCode='.$merchant.'&startDate='.$startDate.'" target="_blank">'.$pointSumRow['empCount'].'</a></td><td>'.$pointSumRow['cash'].'</td><td>'.$pointSumRow['ret'].'</td><td>'.$pointSumRow['partial'].'</td><td>'.$pointSumRow['onHold'].'</td></tr>';    
                                } else {
                                    $pointRegion = $pointSumRow['region'];
                                    echo '<tr><td colspan=2 style="color: #16469E; cursor: pointer"  onclick="regionDetail('.$pointSumRow['regionSort'].')"><b>'.$pointRegion.'</b><p style="font-size: 8px; display: inline-block">&nbsp;&nbsp;(Click for Detail)</p></td></tr>';
                                    echo '<tr class="'.$pointSumRow['regionSort'].'" hidden><td><a href="Point-Status?xxCode='.$pointCode.'&yyCode='.$merchant.'" target="_blank">'.$pointSumRow['pointCode'].' ('.$pointOrder.')</a></td><td>'.$pointSumRow['pointName'].'</td><td>'.$pickDate.'</td><td>'.$pointSumRow['shuttle'].'</td><td>'.$pointSumRow['pick'].'</td><td><a href="without-Status?xxCode='.$pointCode.'&yyCode='.$merchant.'" target="_blank">'.$pointSumRow['pending'].'</a></td><td>'.$pointSumRow['onHoldPending'].'</td><td>'.$openingBalance.'</td><td style="color: red"><a style="color: red" href="Sla-Point-Status?xxCode='.$pointCode.'&yyCode='.$merchant.'" target="_blank">'.$pointSumRow['slaCnt'].'</a></td><td><a href="Emp-Point-Status?xxCode='.$pointCode.'&yyCode='.$merchant.'&startDate='.$startDate.'" target="_blank">'.$pointSumRow['empCount'].'</a></td><td>'.$pointSumRow['cash'].'</td><td>'.$pointSumRow['ret'].'</td><td>'.$pointSumRow['partial'].'</td><td>'.$pointSumRow['onHold'].'</td></tr>';    
                                }
                            }
                            if($pointRegion != $pointSumRow['region']){
                                if($regionOrderCount != 0){
                                    $totalSuccess = round(($cashCount/($dp2PickCount+$onHoldCount))*100,0);
                                    echo '<tr style="font-weight: 800"><td colspan = 3>Total : '.$regionOrderCount.'</td><td>'.$shuttleCount.'</td><td>'.$dp2PickCount.'</td><td>'.$withoutStatusCount.'</td><td>'.$onHoldCount.'</td><td>'.$openingCount.'</td><td style="color: red">'.$slaMissedCount.'</td><td>'.$executiveCount.'</td><td>'.$cashCount.'</td><td>'.$retCount.'</td><td>'.$partialCount.'</td><td>'.$curOnHoldCount.'</td></tr>';    
                                }
                                $regionOrderCount = 0;
                                $shuttleCount = 0;
                                $dp2PickCount = 0;
                                $onHoldCount = 0;
                                $openingCount = 0;
                                $slaMissedCount = 0;
                                $executiveCount = 0;
                                $cashCount = 0;
                                $retCount = 0;
                                $partialCount = 0;
                                $curOnHoldCount = 0;
                                $withoutStatusCount = 0;
                            }
                        }
                        $pointOrder = 0;
                    }                
                    $gtOPeningCount =  $gtOnHoldCount + $gtWithoutStatus;
                    $gtSuccess = round(($gtCashCount/($gtPickCount + $gtOnHoldCount))*100,0);
                    echo '<tr style="font-weight: 800"><td>Grand Total : '.$gtPointOrder.'</td><td></td><td></td><td>'.$gtShuttle.'</td><td>'.$gtPickCount.'</td><td>'.$gtWithoutStatus.'</td><td>'.$gtOnHoldCount.'</td><td>'.$gtOPeningCount.'</td><td style="color: red">'.$gtslaMissedCount.'</td><td>'.$gtExecutiveCount.'</td><td>'.$gtCashCount.'</td><td>'.$gtReturnCount.'</td><td>'.$gtPartialCount.'</td><td>'.$gtOnHoldToday.'</td></tr>';
                }
                echo '</tbody>';


            }
            if($flag == 'merchantStatus'){
                $merchantRow = mysqli_fetch_array(mysqli_query($conn, "select * from tbl_merchant_info where merchantCode = '$orderid'"));
                if($merchantRow['isActive'] == 'Y'){
                    $updateMerchant = mysqli_query($conn, "update tbl_merchant_info set isActive = 'N', update_date = NOW() + INTERVAL 6 HOUR, update_by = '$user_check' where merchantCode = '$orderid'");
                    echo 'Inactive';
                } else {
                    $updateMerchant = mysqli_query($conn, "update tbl_merchant_info set isActive = 'Y', update_date = NOW() + INTERVAL 6 HOUR, update_by = '$user_check' where merchantCode = '$orderid'");
                    echo 'Active';                
                }
            }
            if($flag == 'merchantWiseSLA'){
                $slaYear = $_POST['slaYear'];
                $slaMonth = $_POST['slaMonth'];
                $merchantCode = $_POST['merchantCode'];
            
                $slaPeriod = $slaYear.'-'.$slaMonth;
            
                $totalOrderCount = 0;
                $totalLocalOrderCount = 0;
                $totalInterDistrictOrderCount = 0;

                $localSLAMissedCount = 0;
                $interDistrictSLAMissedCount = 0;


                $totaOrdersSQL = "SELECT orderDate, destination, case when Cash = 'Y' then CashTime when Ret = 'Y' then RetTime when partial then partialTime end as deliveryTime FROM `tbl_order_details` WHERE merchantCode = '$merchantCode' and DATE_FORMAT(orderDate, '%Y-%m') = '$slaPeriod'";            

                if(!mysqli_query($conn, $totaOrdersSQL)){
                    echo "Error: unable execute query";
                } else {
                    $totaOrdersResult = mysqli_query($conn, $totaOrdersSQL);
                    foreach($totaOrdersResult as $totaOrdersRow){
                        if($totaOrdersRow['destination'] == 'local'){
                            $totalLocalOrderCount++;
                            $orderDate = strtotime($totaOrdersRow['orderDate']);
                            $deliveryDate = strtotime(date('Y-m-d', strtotime($totaOrdersRow['deliveryTime'])));
                            $days = ($deliveryDate - $orderDate)/ 86400;
                            if($days > 1){
                                $localSLAMissedCount++;
                            }
                        } else {
                            $orderDate = strtotime($totaOrdersRow['orderDate']);
                            $deliveryDate = strtotime(date('Y-m-d', strtotime($totaOrdersRow['deliveryTime'])));
                            $days = ($deliveryDate - $orderDate)/ 86400;
                            if($days > 3){
                                $interDistrictSLAMissedCount++;
                            }
                            $totalInterDistrictOrderCount++;
                        }                  
                        $totalOrderCount++;
                    }
                    $localMissedPercent = round((($localSLAMissedCount/$totalLocalOrderCount)*100),0);
                    $interDistrictMissedPercent = round((($interDistrictSLAMissedCount/$totalInterDistrictOrderCount)*100),0);
                    $overallSLAmissedPercent = round((($localSLAMissedCount + $interDistrictSLAMissedCount)/$totalOrderCount)*100,0);
                    echo '<thead><tr><th colspan=8 style="text-align: center;font-size: 25px">Merchant-wise SLA Performance</th></tr></thead>';
                    echo '<thead><tr><th style="text-align: center">Total Order</th><th style="text-align: center">Local Orders</th><th style="text-align: center">Inter District Orders</th><th style="text-align: center">Local SLA Missed</th><th style="text-align: center">Inter District SLA Missed</th><th style="text-align: center">Local Missed (%)</th><th style="text-align: center">Inter District Missed (%)</th><th>Overall Missed (%)</th></tr></thead>';
                    echo '<tbody><tr><td style="text-align: center">'.$totalOrderCount.'</td><td style="text-align: center">'.$totalLocalOrderCount.'</td><td style="text-align: center">'.$totalInterDistrictOrderCount.'</td><td style="text-align: center">'.$localSLAMissedCount.'</td><td style="text-align: center">'.$interDistrictSLAMissedCount.'</td><td style="text-align: center">'.$localMissedPercent.'</td><td style="text-align: center">'.$interDistrictMissedPercent.'</td><td style="text-align: center">'.$overallSLAmissedPercent.'</td></tr></tbody>';
                }
            }
            if($flag == 'overAllSLA'){
                $slaYear = $_POST['slaYear'];
                $slaMonth = $_POST['slaMonth'];
            
                $slaPeriod = $slaYear.'-'.$slaMonth;
            
                $totalOrderCount = 0;
                $totalLocalOrderCount = 0;
                $totalInterDistrictOrderCount = 0;

                $localSLAMissedCount = 0;
                $interDistrictSLAMissedCount = 0;


                $totaOrdersSQL = "SELECT orderDate, destination, case when Cash = 'Y' then CashTime when Ret = 'Y' then RetTime when partial then partialTime end as deliveryTime FROM `tbl_order_details` WHERE DATE_FORMAT(orderDate, '%Y-%m') = '$slaPeriod'";            

                if(!mysqli_query($conn, $totaOrdersSQL)){
                    echo "Error: unable execute query";
                } else {
                    $totaOrdersResult = mysqli_query($conn, $totaOrdersSQL);
                    foreach($totaOrdersResult as $totaOrdersRow){
                        if($totaOrdersRow['destination'] == 'local'){
                            $totalLocalOrderCount++;
                            $orderDate = strtotime($totaOrdersRow['orderDate']);
                            $deliveryDate = strtotime(date('Y-m-d', strtotime($totaOrdersRow['deliveryTime'])));
                            $days = ($deliveryDate - $orderDate)/ 86400;
                            if($days > 1){
                                $localSLAMissedCount++;
                            }
                        } else {
                            $orderDate = strtotime($totaOrdersRow['orderDate']);
                            $deliveryDate = strtotime(date('Y-m-d', strtotime($totaOrdersRow['deliveryTime'])));
                            $days = ($deliveryDate - $orderDate)/ 86400;
                            if($days > 3){
                                $interDistrictSLAMissedCount++;
                            }
                            $totalInterDistrictOrderCount++;
                        }                  
                        $totalOrderCount++;
                    }
                    $localMissedPercent = round((($localSLAMissedCount/$totalLocalOrderCount)*100),0);
                    $interDistrictMissedPercent = round((($interDistrictSLAMissedCount/$totalInterDistrictOrderCount)*100),0);
                    $overallSLAmissedPercent = round((($localSLAMissedCount + $interDistrictSLAMissedCount)/$totalOrderCount)*100,0);
                    echo '<thead><tr><th colspan=8 style="text-align: center;font-size: 25px">Overall SLA Performance</th></tr></thead>';
                    echo '<thead><tr><th style="text-align: center">Total Order</th><th style="text-align: center">Local Orders</th><th style="text-align: center">Inter District Orders</th><th style="text-align: center">Local SLA Missed</th><th style="text-align: center">Inter District SLA Missed</th><th style="text-align: center">Local Missed (%)</th><th style="text-align: center">Inter District Missed (%)</th><th>Overall Missed (%)</th></tr></thead>';
                    echo '<tbody><tr><td style="text-align: center">'.$totalOrderCount.'</td><td style="text-align: center">'.$totalLocalOrderCount.'</td><td style="text-align: center">'.$totalInterDistrictOrderCount.'</td><td style="text-align: center">'.$localSLAMissedCount.'</td><td style="text-align: center">'.$interDistrictSLAMissedCount.'</td><td style="text-align: center">'.$localMissedPercent.'</td><td style="text-align: center">'.$interDistrictMissedPercent.'</td><td style="text-align: center">'.$overallSLAmissedPercent.'</td></tr></tbody>';
                }
            }
            if($flag == 'orderDeliverPerf'){
                $startDate = date('Y-m-d', strtotime($_POST['startDate']));
                $endDate = date('Y-m-d', strtotime($_POST['endDate']));

                function encryptor($action, $string) {
                    $output = false;

                    $encrypt_method = "AES-256-CBC";
                    //pls set your unique hashing key
                    $secret_key = 'shLitu';
                    $secret_iv = '12Litu34';

                    // hash
                    $key = hash('sha256', $secret_key);

                    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
                    $iv = substr(hash('sha256', $secret_iv), 0, 16);

                    //do the encyption given text/string/number
                    if( $action == 'encrypt' ) {
                        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
                        $output = base64_encode($output);
                    }
                    else if( $action == 'decrypt' ){
    	                //decrypt the given text/string/number
                        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
                    }

                    return $output;
                }

                $regionResult = mysqli_query($conn, "select distinct regionSort, region from tbl_point_info");


                $deliveryCountSQL = "select tbl_order_details.dropPointCode, tbl_point_info.pointName, tbl_point_info.regionSort, count(1) as shuttleCount, cashInfo.cashCount, retInfo.retCount, retCpInfo.retCpCount from tbl_order_details
     left join tbl_point_info on tbl_order_details.dropPointCode = tbl_point_info.pointCode 
    left join (select tbl_order_details.dropPointCode, count(1) as cashCount from tbl_order_details where Cash = 'Y' and DATE_FORMAT(CashTime, '%Y-%m-%d') between '$startDate' and '$endDate' group by tbl_order_details.dropPointCode) as cashInfo on tbl_order_details.dropPointCode = cashInfo.dropPointCode 
    left join (select tbl_order_details.dropPointCode, count(1) as retCount from tbl_order_details where Ret = 'Y' and DATE_FORMAT(RetTime, '%Y-%m-%d') between '$startDate' and '$endDate' group by tbl_order_details.dropPointCode) as retInfo on tbl_order_details.dropPointCode = retInfo.dropPointCode 
    left join (select tbl_order_details.dropPointCode, count(1) as retCpCount from tbl_order_details where retcp1 = 'Y' and DATE_FORMAT(retcp1Time, '%Y-%m-%d') between '$startDate' and '$endDate' group by tbl_order_details.dropPointCode) as retCpInfo on tbl_order_details.dropPointCode = retCpInfo.dropPointCode
    where DATE_FORMAT(ShtlTime, '%Y-%m-%d') between '$startDate' and '$endDate' group by tbl_order_details.dropPointCode";

                $deliveryCountResult = mysqli_query($conn, $deliveryCountSQL) or die("Error : unable execute query");

                $totalShuttleOrder = 0;
                $totalCashOrder = 0;
                $totalRetOrder = 0;
                $totalCpCount = 0;

                $totalRegionShuttleOrder = 0;
                $totalRegionCashOrder = 0;
                $totalRegionRetOrder = 0;
                $totalRegionCpCount = 0;

                echo '<thead><tr><th>Point Name</th><th>Total orders (shuttle orders)</th><th>Total cash</th><th>Total Return</th><th>Total Return Close</th><th>Return%</th><th>Cash%</th></tr></thead><tbody>';
                foreach($regionResult as $regionRow){
                    $regionSort = $regionRow['regionSort'];
                    echo '<tr><td style="color: #16469E; cursor: pointer"  onclick="regionDetail('.$regionRow['regionSort'].')">'.$regionRow['region'].'<p style="font-size: 8px; display: inline-block">&nbsp;&nbsp;(Click for Detail)</p></td></tr>';
                    foreach($deliveryCountResult as $deliveryCountRow){
                        if($deliveryCountRow['regionSort'] == $regionSort){
                            echo '<tr class="'.$regionRow['regionSort'].'" hidden>';
                                $pointCode = urlencode(encryptor('encrypt', $deliveryCountRow['dropPointCode']));
                                echo '<td><a href="Point-Performance?xxCode='.$pointCode.'&startDate='.$startDate.'&endDate='.$endDate.'" target="_BLANK">'.$deliveryCountRow['dropPointCode'].' - '.$deliveryCountRow['pointName'].'</a></td>';
                                echo '<td style="text-align: center">'.$deliveryCountRow['shuttleCount'].'</td>';
                                echo '<td style="text-align: center">'.$deliveryCountRow['cashCount'].'</td>';
                                echo '<td style="text-align: center">'.$deliveryCountRow['retCount'].'</td>';
                                echo '<td style="text-align: center">'.$deliveryCountRow['retCpCount'].'</td>';
                                $retPerchent = round(($deliveryCountRow['retCount']/$deliveryCountRow['shuttleCount'])*100,0);
                                echo '<td style="text-align: center">'.$retPerchent.'</td>';
                                $success = round(($deliveryCountRow['cashCount']/$deliveryCountRow['shuttleCount'])*100,0);
                                echo '<td style="text-align: center">'.$success.'</td>';
                            echo '</tr>';
                            $totalShuttleOrder = $totalShuttleOrder + $deliveryCountRow['shuttleCount'];
                            $totalCashOrder = $totalCashOrder + $deliveryCountRow['cashCount'];
                            $totalRetOrder = $totalRetOrder + $deliveryCountRow['retCount'];
                            $totalCpCount = $totalCpCount + $deliveryCountRow['retCpCount'];

                            $totalRegionShuttleOrder = $totalRegionShuttleOrder + $deliveryCountRow['shuttleCount'];
                            $totalRegionCashOrder = $totalRegionCashOrder + $deliveryCountRow['cashCount'];
                            $totalRegionRetOrder = $totalRegionRetOrder + $deliveryCountRow['retCount'];
                            $totalRegionCpCount = $totalRegionCpCount + $deliveryCountRow['retCpCount'];
                        }
                    }
                    $cashSuccess = round(($totalRegionCashOrder/$totalRegionShuttleOrder)*100,0);
                    $RetPercent = round(($totalRegionRetOrder/$totalRegionShuttleOrder)*100,0);
                    echo '<tr><td><b>Total</b></td><td style="text-align: center"><b>'.$totalRegionShuttleOrder.'</b></td><td style="text-align: center"><b>'.$totalRegionCashOrder.'</b></td><td style="text-align: center"><b>'.$totalRegionRetOrder.'</b></td><td style="text-align: center"><b>'.$totalRegionCpCount.'</b></td><td style="text-align: center"><b>'.$RetPercent.'</b></td><td style="text-align: center"><b>'.$cashSuccess.'</b></td></tr>';
                    $totalRegionShuttleOrder = 0;
                    $totalRegionCashOrder = 0;
                    $totalRegionRetOrder = 0;
                    $totalRegionCpCount = 0;
                }
                echo '<tr><td><b>Grand Total</b></td><td style="text-align: center"><b>'.$totalShuttleOrder.'</b></td><td style="text-align: center"><b>'.$totalCashOrder.'</b></td><td style="text-align: center"><b>'.$totalRetOrder.'</b></td><td style="text-align: center"><b>'.$totalCpCount.'</b></td><td></td><td></td></tr>';
                echo '</tbody>';
            
            }
            if($flag == 'orderDeliverPerfMerchant'){
                $merchantCode = $_POST['merchantCode'];
                $startDate = date('Y-m-d', strtotime($_POST['startDate']));
                $endDate = date('Y-m-d', strtotime($_POST['endDate']));

                function encryptor($action, $string) {
                    $output = false;

                    $encrypt_method = "AES-256-CBC";
                    //pls set your unique hashing key
                    $secret_key = 'shLitu';
                    $secret_iv = '12Litu34';

                    // hash
                    $key = hash('sha256', $secret_key);

                    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
                    $iv = substr(hash('sha256', $secret_iv), 0, 16);

                    //do the encyption given text/string/number
                    if( $action == 'encrypt' ) {
                        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
                        $output = base64_encode($output);
                    }
                    else if( $action == 'decrypt' ){
    	                //decrypt the given text/string/number
                        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
                    }

                    return $output;
                }

                $regionResult = mysqli_query($conn, "select distinct regionSort, region from tbl_point_info");


                $deliveryCountSQL = "select tbl_order_details.dropPointCode, tbl_point_info.pointName, tbl_point_info.regionSort, count(1) as shuttleCount, cashInfo.cashCount, retInfo.retCount, retCpInfo.retCpCount from tbl_order_details
     left join tbl_point_info on tbl_order_details.dropPointCode = tbl_point_info.pointCode 
    left join (select tbl_order_details.dropPointCode, count(1) as cashCount from tbl_order_details where merchantCode = '$merchantCode' and  Cash = 'Y' and DATE_FORMAT(CashTime, '%Y-%m-%d') between '$startDate' and '$endDate' group by tbl_order_details.dropPointCode) as cashInfo on tbl_order_details.dropPointCode = cashInfo.dropPointCode 
    left join (select tbl_order_details.dropPointCode, count(1) as retCount from tbl_order_details where merchantCode = '$merchantCode' and  Ret = 'Y' and DATE_FORMAT(RetTime, '%Y-%m-%d') between '$startDate' and '$endDate' group by tbl_order_details.dropPointCode) as retInfo on tbl_order_details.dropPointCode = retInfo.dropPointCode 
    left join (select tbl_order_details.dropPointCode, count(1) as retCpCount from tbl_order_details where merchantCode = '$merchantCode' and  retcp1 = 'Y' and DATE_FORMAT(retcp1Time, '%Y-%m-%d') between '$startDate' and '$endDate' group by tbl_order_details.dropPointCode) as retCpInfo on tbl_order_details.dropPointCode = retCpInfo.dropPointCode
    where merchantCode = '$merchantCode' and DATE_FORMAT(ShtlTime, '%Y-%m-%d') between '$startDate' and '$endDate' group by tbl_order_details.dropPointCode";

                $deliveryCountResult = mysqli_query($conn, $deliveryCountSQL) or die("Error : unable execute query");

                $totalShuttleOrder = 0;
                $totalCashOrder = 0;
                $totalRetOrder = 0;
                $totalCpCount = 0;

                $totalRegionShuttleOrder = 0;
                $totalRegionCashOrder = 0;
                $totalRegionRetOrder = 0;
                $totalRegionCpCount = 0;

                echo '<thead><tr><th>Point Name</th><th>Total orders (shuttle orders)</th><th>Total cash</th><th>Total Return</th><th>Total Return Close</th><th>Return%</th><th>Cash%</th></tr></thead><tbody>';
                foreach($regionResult as $regionRow){
                    $regionSort = $regionRow['regionSort'];
                    echo '<tr><td style="color: #16469E; cursor: pointer"  onclick="regionDetail('.$regionRow['regionSort'].')">'.$regionRow['region'].'<p style="font-size: 8px; display: inline-block">&nbsp;&nbsp;(Click for Detail)</p></td></tr>';
                    foreach($deliveryCountResult as $deliveryCountRow){
                        if($deliveryCountRow['regionSort'] == $regionSort){
                            echo '<tr class="'.$regionRow['regionSort'].'" hidden>';
                                $pointCode = urlencode(encryptor('encrypt', $deliveryCountRow['dropPointCode']));
                                echo '<td><a href="Point-Performance?xxCode='.$pointCode.'&startDate='.$startDate.'&endDate='.$endDate.'" target="_BLANK">'.$deliveryCountRow['dropPointCode'].' - '.$deliveryCountRow['pointName'].'</a></td>';
                                echo '<td style="text-align: center">'.$deliveryCountRow['shuttleCount'].'</td>';
                                echo '<td style="text-align: center">'.$deliveryCountRow['cashCount'].'</td>';
                                echo '<td style="text-align: center">'.$deliveryCountRow['retCount'].'</td>';
                                echo '<td style="text-align: center">'.$deliveryCountRow['retCpCount'].'</td>';
                                $retPerchent = round(($deliveryCountRow['retCount']/$deliveryCountRow['shuttleCount'])*100,0);
                                echo '<td style="text-align: center">'.$retPerchent.'</td>';
                                $success = round(($deliveryCountRow['cashCount']/$deliveryCountRow['shuttleCount'])*100,0);
                                echo '<td style="text-align: center">'.$success.'</td>';
                            echo '</tr>';
                            $totalShuttleOrder = $totalShuttleOrder + $deliveryCountRow['shuttleCount'];
                            $totalCashOrder = $totalCashOrder + $deliveryCountRow['cashCount'];
                            $totalRetOrder = $totalRetOrder + $deliveryCountRow['retCount'];
                            $totalCpCount = $totalCpCount + $deliveryCountRow['retCpCount'];

                            $totalRegionShuttleOrder = $totalRegionShuttleOrder + $deliveryCountRow['shuttleCount'];
                            $totalRegionCashOrder = $totalRegionCashOrder + $deliveryCountRow['cashCount'];
                            $totalRegionRetOrder = $totalRegionRetOrder + $deliveryCountRow['retCount'];
                            $totalRegionCpCount = $totalRegionCpCount + $deliveryCountRow['retCpCount'];
                        }
                    }
                    $cashSuccess = round(($totalRegionCashOrder/$totalRegionShuttleOrder)*100,0);
                    $RetPercent = round(($totalRegionRetOrder/$totalRegionShuttleOrder)*100,0);
                    echo '<tr><td><b>Total</b></td><td style="text-align: center"><b>'.$totalRegionShuttleOrder.'</b></td><td style="text-align: center"><b>'.$totalRegionCashOrder.'</b></td><td style="text-align: center"><b>'.$totalRegionRetOrder.'</b></td><td style="text-align: center"><b>'.$totalRegionCpCount.'</b></td><td style="text-align: center"><b>'.$RetPercent.'</b></td><td style="text-align: center"><b>'.$cashSuccess.'</b></td></tr>';
                    $totalRegionShuttleOrder = 0;
                    $totalRegionCashOrder = 0;
                    $totalRegionRetOrder = 0;
                    $totalRegionCpCount = 0;
                }
                echo '<tr><td><b>Grand Total</b></td><td style="text-align: center"><b>'.$totalShuttleOrder.'</b></td><td style="text-align: center"><b>'.$totalCashOrder.'</b></td><td style="text-align: center"><b>'.$totalRetOrder.'</b></td><td style="text-align: center"><b>'.$totalCpCount.'</b></td><td></td><td></td></tr>';
                echo '</tbody>';                
            }
            if($flag == 'unInvoiceReveune'){
                $startDate = date('Y-m-d', strtotime($_POST['startDate']));
                $endDate = date('Y-m-d', strtotime($_POST['endDate']));
                $rowCount = 0;
                $revenueSQL = "select tbl_order_details.merchantCode, tbl_merchant_info.merchantName,  count(1) as orderCount, cashInfo.cashCount, retInfo.retCount, pendingInfo.pendingCount, revenueInfo.cashAmt, revenueInfo.COD, revenueInfo.deliveryCharge from tbl_order_details 

left join tbl_merchant_info on tbl_order_details.merchantCode = tbl_merchant_info.merchantCode

left join (select tbl_order_details.merchantCode, tbl_merchant_info.merchantName,  count(1) as cashCount from tbl_order_details left join tbl_merchant_info on tbl_order_details.merchantCode = tbl_merchant_info.merchantCode where Cash = 'Y' and orderDate between '$startDate' and '$endDate' group by merchantCode) as cashInfo on tbl_order_details.merchantCode = cashInfo.merchantCode

left join (select tbl_order_details.merchantCode, tbl_merchant_info.merchantName,  count(1) as retCount from tbl_order_details left join tbl_merchant_info on tbl_order_details.merchantCode = tbl_merchant_info.merchantCode where Ret = 'Y' and orderDate between '$startDate' and '$endDate' group by merchantCode) as retInfo on tbl_order_details.merchantCode = retInfo.merchantCode

left join (select tbl_order_details.merchantCode, tbl_merchant_info.merchantName,  count(1) as pendingCount from tbl_order_details left join tbl_merchant_info on tbl_order_details.merchantCode = tbl_merchant_info.merchantCode where Shtl = 'Y' and Cash is null and Ret is null and partial is null and close is null and orderDate < '$endDate' group by merchantCode) as pendingInfo on tbl_order_details.merchantCode = pendingInfo.merchantCode

left join (select tbl_order_details.merchantCode, tbl_merchant_info.merchantName, sum(CashAmt) as cashAmt,  sum((CashAmt*tbl_order_details.cod)/100) as COD, sum(charge) as deliveryCharge from tbl_order_details left join tbl_merchant_info on tbl_order_details.merchantCode = tbl_merchant_info.merchantCode where Shtl = 'Y' and orderDate between '$startDate' and '$endDate' group by merchantCode) as revenueInfo on tbl_order_details.merchantCode = revenueInfo.merchantCode

where Shtl = 'Y' and orderDate between '$startDate' and '$endDate' group by merchantCode order by orderCount desc

";
                $totalOrderCount = 0;
                $totalCashOrder = 0;
                $totalCashAmout = 0;
                $returnOrders = 0;
                $totalPendingCount = 0;
                $totalCOD = 0;
                $totalDeliveryChare = 0;
                $totalRevenue = 0;

                $revenueResult = mysqli_query($conn, $revenueSQL) or die ("Error: unable execute query");
                echo '<thead><tr><th>SL</th><th>Merchant</th><th style="text-align: right">Total Order</th><th style="text-align: right">Cash</th><th style="text-align: right">Return</th><th style="text-align: right">Pending</th><th style="text-align: right">Collection</th><th style="text-align: right">COD</th><th style="text-align: right">Charge</th><th style="text-align: right">Revenue</th><th style="text-align: right">Cash %</th><th style="text-align: right">Return %</th></tr></thead>';
                echo '<tbody>';
                foreach($revenueResult as $revenueRow){
                    $rowCount++;
                    echo '<tr>';
                    echo '<td>'.$rowCount.'</td>';
                    echo '<td>'.$revenueRow['merchantName'].'</td>';
                    echo '<td style="text-align: right">'.$revenueRow['orderCount'].'</td>';
                    echo '<td style="text-align: right">'.$revenueRow['cashCount'].'</td>';
                    echo '<td style="text-align: right">'.$revenueRow['retCount'].'</td>';
                    echo '<td style="text-align: right">'.$revenueRow['pendingCount'].'</td>';
                    echo '<td style="text-align: right">'.num_to_format(round($revenueRow['cashAmt'],0)).'</td>';
                    echo '<td style="text-align: right">'.num_to_format(round($revenueRow['COD'],0)).'</td>';
                    echo '<td style="text-align: right">'.num_to_format(round($revenueRow['deliveryCharge'],0)).'</td>';
                    echo '<td style="text-align: right">'.num_to_format(round(($revenueRow['COD'] + $revenueRow['deliveryCharge']),0)).'</td>';
                    echo '<td style="text-align: right">'.num_to_format(round((($revenueRow['cashCount']/$revenueRow['orderCount'])*100),0)).'</td>';
                    echo '<td style="text-align: right">'.num_to_format(round((($revenueRow['retCount']/$revenueRow['orderCount'])*100),0)).'</td>';
                    echo '</tr>';

                    $totalOrderCount = $totalOrderCount + $revenueRow['orderCount'];
                    $totalCashOrder = $totalCashOrder + $revenueRow['cashCount'];
                    $totalCashAmout = $totalCashAmout + $revenueRow['cashAmt'];
                    $returnOrders = $returnOrders + $revenueRow['retCount'];
                    $totalPendingCount = $totalPendingCount + $revenueRow['pendingCount'];
                    $totalCOD = $totalCOD + $revenueRow['COD'];
                    $totalDeliveryChare = $totalDeliveryChare + $revenueRow['deliveryCharge'];
                    $totalRevenue = $totalRevenue + $revenueRow['COD'] + $revenueRow['deliveryCharge'];
                }
                $totalCashPercent = ($totalCashOrder/$totalOrderCount) * 100;
                $returnOrdersPercent = ($returnOrders/$totalOrderCount) * 100;
                echo '<tr style="font-weight: 800"><td colspan=2>Total</td><td style="text-align: right">'.num_to_format(round($totalOrderCount,0)).'</td><td style="text-align: right">'.num_to_format(round($totalCashOrder,0)).'</td><td style="text-align: right">'.num_to_format(round($returnOrders,0)).'</td><td style="text-align: right">'.num_to_format(round($totalPendingCount,0)).'</td><td style="text-align: right">'.num_to_format(round($totalCashAmout,0)).'</td><td style="text-align: right">'.num_to_format(round($totalCOD,0)).'</td><td style="text-align: right">'.num_to_format(round($totalDeliveryChare,0)).'</td><td style="text-align: right">'.num_to_format(round($totalRevenue,0)).'</td><td style="text-align: right">'.num_to_format(round($totalCashPercent,0)).'</td><td style="text-align: right">'.num_to_format(round($returnOrdersPercent,0)).'</td></tr>';
                echo '</tbody>';
            }
            if($flag == 'InvoiceReveune'){
                $startDate = date('Y-m-d', strtotime($_POST['startDate']));
                $endDate = date('Y-m-d', strtotime($_POST['endDate']));
                $rowCount = 0;
                $revenueSQL = "select invoice_info.merchantName, sum(TotalOrder) as totalorder, sum(cash+partial) as cash, sum(Ret) as ret, sum(deliveryCharge) as deliveryCharge, sum(cashCollection) as cashcollection, sum(CashCoD) as cashcod from tbl_invoice_details left join (select tbl_invoice.merchantCode, tbl_merchant_info.merchantName, invNum from tbl_invoice left join tbl_merchant_info on tbl_invoice.merchantCode = tbl_merchant_info.merchantCode) as invoice_info on tbl_invoice_details.invNum = invoice_info.invNum where tbl_invoice_details.invNum in (select invNum from tbl_invoice where (inv_date between '$startDate' and '$endDate')) group by invoice_info.merchantName order by sum(deliveryCharge + CashCoD) desc";
                $totalOrderCount = 0;
                $totalCashOrder = 0;
                $totalCashAmout = 0;
                $returnOrders = 0;
                $totalCOD = 0;
                $totalDeliveryChare = 0;
                $totalRevenue = 0;

                $revenueResult = mysqli_query($conn, $revenueSQL) or die ("Error: unable execute query");
                echo '<thead><tr><th>SL</th><th>Merchant</th><th style="text-align: right">Total Order</th><th style="text-align: right">Cash</th><th style="text-align: right">Return</th><th style="text-align: right">Collection</th><th style="text-align: right">COD</th><th style="text-align: right">Charge</th><th style="text-align: right">Revenue</th><th style="text-align: right">Cash %</th><th style="text-align: right">Return %</th></tr></thead>';
                echo '<tbody>';
                foreach($revenueResult as $revenueRow){
                    $rowCount++;
                    echo '<tr>';
                    echo '<td>'.$rowCount.'</td>';
                    echo '<td>'.$revenueRow['merchantName'].'</td>';
                    echo '<td style="text-align: right">'.$revenueRow['totalorder'].'</td>';
                    echo '<td style="text-align: right">'.$revenueRow['cash'].'</td>';
                    echo '<td style="text-align: right">'.$revenueRow['ret'].'</td>';
                    echo '<td style="text-align: right">'.num_to_format(round($revenueRow['cashcollection'],0)).'</td>';
                    echo '<td style="text-align: right">'.num_to_format(round($revenueRow['cashcod'],0)).'</td>';
                    echo '<td style="text-align: right">'.num_to_format(round($revenueRow['deliveryCharge'],0)).'</td>';
                    echo '<td style="text-align: right">'.num_to_format(round(($revenueRow['cashcod'] + $revenueRow['deliveryCharge']),0)).'</td>';
                    echo '<td style="text-align: right">'.num_to_format(round((($revenueRow['cash']/$revenueRow['totalorder'])*100),0)).'</td>';
                    echo '<td style="text-align: right">'.num_to_format(round((($revenueRow['ret']/$revenueRow['totalorder'])*100),0)).'</td>';
                    echo '</tr>';

                    $totalOrderCount = $totalOrderCount + $revenueRow['totalorder'];
                    $totalCashOrder = $totalCashOrder + $revenueRow['cash'];
                    $totalCashAmout = $totalCashAmout + $revenueRow['cashcollection'];
                    $returnOrders = $returnOrders + $revenueRow['ret'];
                    $totalCOD = $totalCOD + $revenueRow['cashcod'];
                    $totalDeliveryChare = $totalDeliveryChare + $revenueRow['deliveryCharge'];
                    $totalRevenue = $totalRevenue + $revenueRow['cashcod'] + $revenueRow['deliveryCharge'];
                }
                $totalCashPercent = ($totalCashOrder/$totalOrderCount) * 100;
                $returnOrdersPercent = ($returnOrders/$totalOrderCount) * 100;
                echo '<tr style="font-weight: 800"><td colspan=2>Total</td><td style="text-align: right">'.num_to_format(round($totalOrderCount,0)).'</td><td style="text-align: right">'.num_to_format(round($totalCashOrder,0)).'</td><td style="text-align: right">'.num_to_format(round($returnOrders,0)).'</td><td style="text-align: right">'.num_to_format(round($totalCashAmout,0)).'</td><td style="text-align: right">'.num_to_format(round($totalCOD,0)).'</td><td style="text-align: right">'.num_to_format(round($totalDeliveryChare,0)).'</td><td style="text-align: right">'.num_to_format(round($totalRevenue,0)).'</td><td style="text-align: right">'.num_to_format(round($totalCashPercent,0)).'</td><td style="text-align: right">'.num_to_format(round($returnOrdersPercent,0)).'</td></tr>';
                echo '</tbody>';                
            }
            if($flag == 'showSlaMissedOrdersNS'){
                function encryptor($action, $string) {
                    $output = false;

                    $encrypt_method = "AES-256-CBC";
                    //pls set your unique hashing key
                    $secret_key = 'shLitu';
                    $secret_iv = '12Litu34';

                    // hash
                    $key = hash('sha256', $secret_key);

                    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
                    $iv = substr(hash('sha256', $secret_iv), 0, 16);

                    //do the encyption given text/string/number
                    if( $action == 'encrypt' ) {
                        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
                        $output = base64_encode($output);
                    }
                    else if( $action == 'decrypt' ){
    	                //decrypt the given text/string/number
                        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
                    }

                    return $output;
                }

                $slaMissedListResult = mysqli_query($conn, "SELECT dropPointCode, tbl_point_info.pointName, count(1) as orderCount FROM tbl_order_details left join tbl_point_info on tbl_order_details.dropPointCode = tbl_point_info.pointCode WHERE orderDate < DATE_ADD(CURDATE(), INTERVAL -$orderid DAY) and Pick is null and close is null group by dropPointCode");

                $orderCount = 0;

                if(!$slaMissedListResult){
                    echo "Error: unable to retrive information ";
                } else {
                    echo '<table class="table table-hover"><thead><tr><th>Point Name</th><th style="text-align: right">Order Count</th></tr></thead><tbody>';
                        foreach($slaMissedListResult as $slaMissedListRow){
                            $pointCode = urlencode(encryptor('encrypt', $slaMissedListRow['dropPointCode']));
                            echo '<tr>';
                                echo '<td><a href="List-SLA-Status?xxCode='.$pointCode.'&yyCode='.$orderid.'" target="_blank">'.$slaMissedListRow['dropPointCode'].'-'.$slaMissedListRow['pointName'].'</a></td>';
                                echo '<td style="text-align: right">'.$slaMissedListRow['orderCount'].'</td>';
                            echo '</tr>';
                            $orderCount = $orderCount + $slaMissedListRow['orderCount'];
                        }
                        echo '<tr><td>Total</td><td style="text-align: right">'.$orderCount.'</td></tr>';
                    echo '</tbody></table>';
                }                
            }
            if($flag == 'showSlaMissedOrders'){

                function encryptor($action, $string) {
                    $output = false;

                    $encrypt_method = "AES-256-CBC";
                    //pls set your unique hashing key
                    $secret_key = 'shLitu';
                    $secret_iv = '12Litu34';

                    // hash
                    $key = hash('sha256', $secret_key);

                    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
                    $iv = substr(hash('sha256', $secret_iv), 0, 16);

                    //do the encyption given text/string/number
                    if( $action == 'encrypt' ) {
                        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
                        $output = base64_encode($output);
                    }
                    else if( $action == 'decrypt' ){
    	                //decrypt the given text/string/number
                        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
                    }

                    return $output;
                }

                $slaMissedListResult = mysqli_query($conn, "SELECT dropPointCode, tbl_point_info.pointName, count(1) as orderCount FROM tbl_order_details left join tbl_point_info on tbl_order_details.dropPointCode = tbl_point_info.pointCode WHERE orderDate < DATE_ADD(CURDATE(), INTERVAL -$orderid DAY) and Shtl = 'Y' and Cash is null and Ret is Null and partial is null and close is null group by dropPointCode");

                $orderCount = 0;

                if(!$slaMissedListResult){
                    echo "Error: unable to retrive information ";
                } else {
                    echo '<table class="table table-hover"><thead><tr><th>Point Name</th><th style="text-align: right">Order Count</th></tr></thead><tbody>';
                        foreach($slaMissedListResult as $slaMissedListRow){
                            $pointCode = urlencode(encryptor('encrypt', $slaMissedListRow['dropPointCode']));
                            echo '<tr>';
                                echo '<td><a href="List-SLA-Status?xxCode='.$pointCode.'&yyCode='.$orderid.'" target="_blank">'.$slaMissedListRow['dropPointCode'].'-'.$slaMissedListRow['pointName'].'</a></td>';
                                echo '<td style="text-align: right">'.$slaMissedListRow['orderCount'].'</td>';
                            echo '</tr>';
                            $orderCount = $orderCount + $slaMissedListRow['orderCount'];
                        }
                        echo '<tr><td>Total</td><td style="text-align: right">'.$orderCount.'</td></tr>';
                    echo '</tbody></table>';
                }
            }
            if($flag == 'showMerchantSlaMissedOrdersSN'){
                function encryptor($action, $string) {
                    $output = false;

                    $encrypt_method = "AES-256-CBC";
                    //pls set your unique hashing key
                    $secret_key = 'shLitu';
                    $secret_iv = '12Litu34';

                    // hash
                    $key = hash('sha256', $secret_key);

                    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
                    $iv = substr(hash('sha256', $secret_iv), 0, 16);

                    //do the encyption given text/string/number
                    if( $action == 'encrypt' ) {
                        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
                        $output = base64_encode($output);
                    }
                    else if( $action == 'decrypt' ){
    	                //decrypt the given text/string/number
                        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
                    }

                    return $output;
                }

                $merchantCode = $_POST['merchantCode'];

                $slaMissedListResult = mysqli_query($conn, "SELECT dropPointCode, tbl_point_info.pointName, count(1) as orderCount FROM tbl_order_details left join tbl_point_info on tbl_order_details.dropPointCode = tbl_point_info.pointCode WHERE orderDate < DATE_ADD(CURDATE(), INTERVAL -$orderid DAY) and merchantCode = '$merchantCode' and Pick is null and close is null group by dropPointCode");

                $orderCount = 0;
                $SN = 1;
                if(!$slaMissedListResult){
                    echo "Error: unable to retrive information ";
                } else {
                    echo '<table class="table table-hover"><thead><tr><th>Point Name</th><th style="text-align: right">Order Count</th></tr></thead><tbody>';
                        foreach($slaMissedListResult as $slaMissedListRow){
                            $pointCode = urlencode(encryptor('encrypt', $slaMissedListRow['dropPointCode']));
                            echo '<tr>';
                                echo '<td><a href="List-SLA-Status?xxCode='.$pointCode.'&yyCode='.$orderid.'&mc='.$merchantCode.'&zzCode='.$SN.'" target="_blank">'.$slaMissedListRow['dropPointCode'].'-'.$slaMissedListRow['pointName'].'</a></td>';
                                echo '<td style="text-align: right">'.$slaMissedListRow['orderCount'].'</td>';
                            echo '</tr>';
                            $orderCount = $orderCount + $slaMissedListRow['orderCount'];
                        }
                        echo '<tr><td>Total</td><td style="text-align: right">'.$orderCount.'</td></tr>';
                    echo '</tbody></table>';
                }                
            }
            if($flag == 'showMerchantSlaMissedOrders'){

                function encryptor($action, $string) {
                    $output = false;

                    $encrypt_method = "AES-256-CBC";
                    //pls set your unique hashing key
                    $secret_key = 'shLitu';
                    $secret_iv = '12Litu34';

                    // hash
                    $key = hash('sha256', $secret_key);

                    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
                    $iv = substr(hash('sha256', $secret_iv), 0, 16);

                    //do the encyption given text/string/number
                    if( $action == 'encrypt' ) {
                        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
                        $output = base64_encode($output);
                    }
                    else if( $action == 'decrypt' ){
    	                //decrypt the given text/string/number
                        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
                    }

                    return $output;
                }

                $merchantCode = $_POST['merchantCode'];

                $slaMissedListResult = mysqli_query($conn, "SELECT dropPointCode, tbl_point_info.pointName, count(1) as orderCount FROM tbl_order_details left join tbl_point_info on tbl_order_details.dropPointCode = tbl_point_info.pointCode WHERE orderDate < DATE_ADD(CURDATE(), INTERVAL -$orderid DAY) and merchantCode = '$merchantCode' and Shtl = 'Y' and Cash is null and Ret is Null and partial is null and close is null group by dropPointCode");

                $orderCount = 0;
                $SN = 0;

                if(!$slaMissedListResult){
                    echo "Error: unable to retrive information ";
                } else {
                    echo '<table class="table table-hover"><thead><tr><th>Point Name</th><th style="text-align: right">Order Count</th></tr></thead><tbody>';
                        foreach($slaMissedListResult as $slaMissedListRow){
                            $pointCode = urlencode(encryptor('encrypt', $slaMissedListRow['dropPointCode']));
                            echo '<tr>';
                                echo '<td><a href="List-SLA-Status?xxCode='.$pointCode.'&yyCode='.$orderid.'&mc='.$merchantCode.'&zzCode='.$SN.'" target="_blank">'.$slaMissedListRow['dropPointCode'].'-'.$slaMissedListRow['pointName'].'</a></td>';
                                echo '<td style="text-align: right">'.$slaMissedListRow['orderCount'].'</td>';
                            echo '</tr>';
                            $orderCount = $orderCount + $slaMissedListRow['orderCount'];
                        }
                        echo '<tr><td>Total</td><td style="text-align: right">'.$orderCount.'</td></tr>';
                    echo '</tbody></table>';
                }
            }
            if($flag == 'signleBarcodeID'){
                $orderSearchResult = mysqli_query($conn, "select * from tbl_order_details where orderid like '$orderid%' or merOrderRef = '$orderid' and close is null limit 1");

                if(mysqli_num_rows($orderSearchResult) > 0){
                    $orderSearchRow = mysqli_fetch_array($orderSearchResult);
                    $merchant = $orderSearchRow['merchantCode'];
                    $orderID = $orderSearchRow['orderid']; 
                    echo $orderID;
                } else {
                    echo 'Error: No such order found';
                }
            }
            if($flag == 'orderForEdit'){
                $orderSQL = "select tbl_order_details.*, tbl_merchant_info.merchantName from tbl_order_details left join tbl_merchant_info on tbl_order_details.merchantCode = tbl_merchant_info.merchantCode where orderid ='$orderid' and close is null";
                $orderResult = mysqli_query($conn, $orderSQL);
                if(mysqli_num_rows($orderResult) > 0){
                    $orderRow = mysqli_fetch_array($orderResult);
                    echo '
                    <table class="table table-hover" style="font-size: 0.7em">
                        <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Merchant Name</th>
                            <th>Merchant Ref</th>
                            <th>Price</th>
                            <th>Package Option</th>
                            <th>Delivery Option</th>
                            <th>Collection</th>
		                    <th>Edit</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr id="tr'.$orderRow['orderid'].'">
                                <td>'.$orderRow['orderid'].'</td>
                                <td>'.$orderRow['merchantName'].'</td>
                                <td id="merRef'.$orderRow['orderid'].'">'.$orderRow['merOrderRef'].'</td>
                                <td id="price'.$orderRow['orderid'].'">'.$orderRow['packagePrice'].'</td>
                                <td id="packOps'.$orderRow['orderid'].'">'.$orderRow['productSizeWeight'].'</td>
                                <td id="deliveryOps'.$orderRow['orderid'].'">'.$orderRow['deliveryOption'].'</td>
                                <td id="collection'.$orderRow['orderid'].'">'.$orderRow['CashAmt'].'</td>
                                <td id="customerThana'.$orderRow['orderid'].'" hidden>'.$orderRow['customerThana'].'</td>
                                <td id="customerPhone'.$orderRow['orderid'].'" hidden>'.$orderRow['custphone'].'</td>
                                <td><button class="btn btn-primary" onclick="return ordEdit('.$orderRow['ordId'].')">Edit</button></td>
                                <td id="customerDistrict'.$orderRow['orderid'].'" hidden>'.$orderRow['customerDistrict'].'</td>
                            </tr>
                        </tbody>
                    </table>';                    
                } else {
                    echo 'No such order found for edit or order may be closed';
                }                
            }
            if($flag == 'getOrderID'){
                $orderIDRow = mysqli_fetch_array(mysqli_query($conn, "select orderid from tbl_order_details where ordId = '$orderid'"));
                echo $orderIDRow['orderid'];
            }
            if($flag == 'pullMerchantOrders'){
                
                $merchantAgreementResult = mysqli_query($conn, "select * from tbl_api_client where merchantCode = '$orderid' and orderPull = 'Y'");
                if(mysqli_num_rows($merchantAgreementResult) > 0){
                    $apiRow = mysqli_fetch_array($merchantAgreementResult);

                    $url = $apiRow['apiUrl'];


                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                      CURLOPT_URL => $url,
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => "",
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 30,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => "GET",
                      CURLOPT_HTTPHEADER => array(
                        "API_KEY: Ajkerdeal_~La?Rj73FcLm",
                        "Authorization: Basic UGFwZXJGbHk6SGpGZTVWNWY=",
                        "Cache-Control: no-cache",
                        "Postman-Token: 196ee106-a3d6-41df-86da-d62884315e69"
                      ),
                    ));

                    $response = curl_exec($curl);

                    $err = curl_error($curl);
                    curl_close($curl);
                    
                    $orderLine = json_decode($response, TRUE);

                    $insertSQL = "insert IGNORE into tbl_api_orders (merchantCode, pickPointCode, pickUpMerchantID, dropPointCode, customerName, customerAddress, customerNumber, customerThana, customerDistrict, packagePrice, productType, deliveryType, PackageType, merOrderRef, barcode, status, dataValidity, destination, creationDate, createdBy) values ";

                    $lineCount = 0;
                    $merchantCode = $orderid;
                    $merchantRow = mysqli_fetch_array(mysqli_query($conn, "select * from tbl_merchant_info where merchantCode = '$orderid'"));

                    foreach($orderLine as $orderItem){
                        $DelivaryDistrictId = $orderItem['DelivaryDistrictId'];
                        $DelivaryThanaId = $orderItem['DelivaryThanaId'];
                        $customerName = $orderItem['CustomerName'];
                        $customerName = mysqli_real_escape_string($conn, $customerName);
                        $CustomerAddress = $orderItem['CustomerAddress'];
                        $CustomerAddress = mysqli_real_escape_string($conn, $CustomerAddress);
                        $DelivaryDistrict = $orderItem['DelivaryDistrict'];
                        $DelivaryThana = $orderItem['DelivaryThana'];
                        $DelivaryArea = $orderItem['DelivaryArea'];
                        $CustomerNumber = $orderItem['CustomerNumber'];
                        $CollectionAddress = $orderItem['CollectionAddress'];
                        $CollectionNumber = $orderItem['CollectionNumber'];
                        $ProductType = $orderItem['ProductType'];
                        $BarCodeNo = $orderItem['BarCodeNo'];
                        $OrderId = $orderItem['OrderId'];
                        $OrderId = mysqli_real_escape_string($conn, $OrderId);
                        $Pickuplocation = strtoupper($orderItem['Pickuplocation']);
                        $DeliveryType = $orderItem['DeliveryType'];
                        $ProductPrice = $orderItem['ProductPrice'];
                        $PackageType = $orderItem['PackageType'];
                        $pickUpMerchantID = $orderItem['MerchentId'];

                        $customerTDRow = mysqli_fetch_array(mysqli_query($conn, "select * from tbl_ajker_deal_map where districtID = '$DelivaryDistrictId' and thanaID = '$DelivaryThanaId'"));
                        
                        $customerThana = $customerTDRow['pThanaID'];
                        $customerDistrict = $customerTDRow['pDistrictID'];
                        
                        if(ucfirst($Pickuplocation) == "DHAKA"){
                            $pickPointCode = $merchantRow['pointCode'];    
                        } else {
                            $pickupPointNameResult = mysqli_query($conn, "select pointCode from tbl_point_info where UPPER(pointName) = '$Pickuplocation'");
                            if(mysqli_num_rows($pickupPointNameResult) > 0){
                                $pickupPointNameRow = mysqli_fetch_array($pickupPointNameResult) or die("Error: unable to fetch point information ".mysqli_error($conn));    
                                $pickPointCode = $pickupPointNameRow['pointCode'];
                            } else {
                                $pickPointCode = '';
                            } 
                            
                            
                        }

                        $dropPointRow = mysqli_fetch_array(mysqli_query($conn, "select pointCode from tbl_ajker_deal_map where districtID = '$DelivaryDistrictId' and thanaID = '$DelivaryThanaId'"));
                        $dropPointCode = $dropPointRow['pointCode'];

                        if(ucfirst($DelivaryDistrict) == ucfirst($Pickuplocation)){
                            $destination = "local";    
                        } else {
                            $destination = "interDistrict";
                        }
    
                        $status = '';
                        if($dropPointCode == '' || $Pickuplocation == '' || $CustomerNumber == '' || $DeliveryType == ''|| $pickPointCode == ''){
                            $dataValidity = 'N';
                            if($dropPointCode == ''){
                                $status .= "Delivery district or thana not found";
                            }
                            if($Pickuplocation == ''){
                                $status .= " || Pickup location missing";
                            }
                            if($CustomerNumber == ''){
                                $status .= " || Customer phone not found";
                            }
                            if($DeliveryType == ''){
                                $status .= " || Delivery type not found";
                            }
                            if($pickPointCode == ''){
                                $status .= " || Pick up point missing";
                            }
                        } else {
                            $dataValidity = 'Y';
                        }

                        if($lineCount == 0 ){
                            $insertSQL .= " ('".$merchantCode."','".$pickPointCode."','".$pickUpMerchantID."','".$dropPointCode."','".$customerName."','".$CustomerAddress."','".$CustomerNumber."','".$customerThana."','".$customerDistrict."','".$ProductPrice."','".$ProductType."','".$DeliveryType."','".$PackageType."','".$OrderId."','".$BarCodeNo."','".$status."','".$dataValidity."','".$destination."', NOW() + INTERVAL 6 HOUR, '".$user_check."')";    
                        } else {
                            $insertSQL .= " ,('".$merchantCode."','".$pickPointCode."','".$pickUpMerchantID."','".$dropPointCode."','".$customerName."','".$CustomerAddress."','".$CustomerNumber."','".$customerThana."','".$customerDistrict."','".$ProductPrice."','".$ProductType."','".$DeliveryType."','".$PackageType."','".$OrderId."','".$BarCodeNo."','".$status."','".$dataValidity."','".$destination."', NOW() + INTERVAL 6 HOUR, '".$user_check."')";    
                        }
                        $lineCount++;
                    }
                    mysqli_set_charset( $conn, 'utf8' );
                    if($lineCount == 0){
                        $apiOrdersResult = mysqli_query($conn, "select tbl_api_orders.*, tbl_pickup_merchant_info.pickMerchantName from tbl_api_orders left join tbl_pickup_merchant_info on tbl_pickup_merchant_info.pickMerchantID = tbl_api_orders.pickUpMerchantID where tbl_api_orders.merchantCode = '$merchantCode' and processed = 'N'");
                        echo '<hr>';
                        echo '<div class="row">';
                            echo '<div class="col-sm-4">';
                                echo '<label style="margin-left: 10px"><span style="font-weight: 800">Orders pulled :</span>  '.$lineCount.'</label>';
                            echo '</div>';
                            echo '<div class="col-sm-4" style="text-align: right">';
                                echo '<button type="button" class="btn btn-default" onclick="exportOrders()"> Export Orders</button>';
                            echo '</div>';
                            echo '<div class="col-sm-4" style="text-align: right">';
                                echo '<button type="button" class="btn btn-default" onclick="acceptAllOrders()"> Accept All Orders</button>';
                            echo '</div>';
                        echo '</div>';
                        echo '<hr>';
                        echo '<table id="orderPullTable" class="table table-hover" style="font-size: 0.7em">';
                            echo '<thead><tr><th>Date</th><th>Pick Point</th><th>Pickup Merchant</th><th>Drop Point</th><th>Destination Type</th><th>Name</th><th>Address</th><th>Phone</th><th>Package Price</th><th>Package Type</th><th>Delivery Type</th><th>Product Type</th><th>Order ID</th><th>Status</th><th>Accept</th><th>Notify</th><th>Delete</th></tr></thead></tbody>';
                            foreach($apiOrdersResult as $apiOrderRow){
                                $pullDate = date('d-M-Y', strtotime($apiOrderRow['apiOrderID']));
                                echo '<tr id="'.$apiOrderRow['apiOrderID'].'">';
                                    echo '<td>'.$pullDate.'</td>';
                                    echo '<td>'.$apiOrderRow['pickPointCode'].'</td>';
                                    echo '<td>'.$apiOrderRow['pickMerchantName'].'</td>';
                                    echo '<td>'.$apiOrderRow['dropPointCode'].'</td>';
                                    echo '<td>'.$apiOrderRow['destination'].'</td>';
                                    echo '<td>'.$apiOrderRow['customerName'].'</td>';
                                    echo '<td>'.$apiOrderRow['customerAddress'].'</td>';
                                    echo '<td>'.$apiOrderRow['customerNumber'].'</td>';
                                    echo '<td>'.$apiOrderRow['packagePrice'].'</td>';   
                                    echo '<td>'.$apiOrderRow['packageType'].'</td>';
                                    echo '<td>'.$apiOrderRow['deliveryType'].'</td>';
                                    echo '<td>'.$apiOrderRow['productType'].'</td>';
                                    echo '<td>'.$apiOrderRow['merOrderRef'].'</td>';
                                    echo '<td>'.$apiOrderRow['status'].'</td>';
                                    //echo '<td><button type="button" class="btn btn-default" onclick="updateOrder('.$apiOrderRow['apiOrderID'].')"> Update</button></td>';
                                    if($apiOrderRow['dataValidity'] == 'Y'){
                                        echo '<td><button type="button" class="btn btn-primary" onclick="acceptOrder('.$apiOrderRow['apiOrderID'].')"> Accept</button></td>';    
                                    } else {
                                        echo '<td></td>';
                                    }
                                    echo '<td><button type="button" class="btn btn-warning" onclick="notifyOrder('.$apiOrderRow['apiOrderID'].')"> Notify</button></td>';
                                    echo '<td><button type="button" class="btn btn-danger" onclick="removeOrder('.$apiOrderRow['apiOrderID'].')"> Remove</button></td>';
                                echo '</tr>';
                            }
                        echo '</tbody></table>';                        
                    } else {
                        if(!mysqli_query($conn, $insertSQL)){
                            echo "Error: unable to process data ".mysqli_error($conn);
                            $err = "Error: unable to process data ".mysqli_error($conn);
                            $logInsertResult = mysqli_query($conn, "insert into tbl_api_log (merchantCode, logType, logDescription, creationDate, createdBy) values ('M-1-0262', 'pull', '$err', NOW() + INTERVAL 6 HOUR, 'admin')");    
                        } else {
                        
                            $apiOrdersResult = mysqli_query($conn, "select distinct tbl_api_orders.*, tbl_pickup_merchant_info.pickMerchantName from tbl_api_orders left join tbl_pickup_merchant_info on tbl_pickup_merchant_info.pickMerchantID = tbl_api_orders.pickUpMerchantID where tbl_api_orders.merchantCode = '$merchantCode' and processed = 'N'");
                            echo '<hr>';
                            echo '<div class="row">';
                                echo '<div class="col-sm-4">';
                                    echo '<label style="margin-left: 10px"><span style="font-weight: 800">Orders pulled :</span>  '.$lineCount.'</label>';
                                echo '</div>';
                                echo '<div class="col-sm-4" style="text-align: right">';
                                    echo '<button type="button" class="btn btn-default" onclick="exportOrders()"> Export Orders</button>';
                                echo '</div>';
                                echo '<div class="col-sm-4" style="text-align: right">';
                                    echo '<button type="button" class="btn btn-default" onclick="acceptAllOrders()"> Accept All Orders</button>';
                                echo '</div>';
                            echo '</div>';
                            echo '<hr>';
                            echo '<table id="orderPullTable" class="table table-hover" style="font-size: 0.7em">';
                                echo '<thead><tr><th>Date</th><th>Pick Point</th><th>Pickup Merchant</th><th>Drop Point</th><th>Destination Type</th><th>Name</th><th>Address</th><th>Phone</th><th>Package Price</th><th>Package Type</th><th>Delivery Type</th><th>Product Type</th><th>Order ID</th><th>Status</th><th>Accept</th><th>Notify</th><th>Delete</th></tr></thead></tbody>';
                                foreach($apiOrdersResult as $apiOrderRow){
                                    $pullDate = date('d-M-Y', strtotime($apiOrderRow['creationDate']));
                                    echo '<tr id="'.$apiOrderRow['apiOrderID'].'">';
                                        echo '<td>'.$pullDate.'</td>';
                                        echo '<td>'.$apiOrderRow['pickPointCode'].'</td>';
                                        echo '<td>'.$apiOrderRow['pickMerchantName'].'</td>';
                                        echo '<td>'.$apiOrderRow['dropPointCode'].'</td>';
                                        echo '<td>'.$apiOrderRow['destination'].'</td>';
                                        echo '<td>'.$apiOrderRow['customerName'].'</td>';
                                        echo '<td>'.$apiOrderRow['customerAddress'].'</td>';
                                        echo '<td>'.$apiOrderRow['customerNumber'].'</td>';
                                        echo '<td>'.$apiOrderRow['packagePrice'].'</td>';
                                        echo '<td>'.$apiOrderRow['packageType'].'</td>';
                                        echo '<td>'.$apiOrderRow['deliveryType'].'</td>';
                                        echo '<td>'.$apiOrderRow['productType'].'</td>';
                                        echo '<td>'.$apiOrderRow['merOrderRef'].'</td>';
                                        echo '<td>'.$apiOrderRow['status'].'</td>';
                                        //echo '<td><button type="button" class="btn btn-default" onclick="updateOrder('.$apiOrderRow['apiOrderID'].')"> Update</button></td>';
                                        if($apiOrderRow['dataValidity'] == 'Y'){
                                            echo '<td><button type="button" class="btn btn-primary" onclick="acceptOrder('.$apiOrderRow['apiOrderID'].')"> Accept</button></td>';    
                                        } else {
                                            echo '<td></td>';
                                        }
                                        echo '<td><button type="button" class="btn btn-warning" onclick="notifyOrder('.$apiOrderRow['apiOrderID'].')"> Notify</button></td>';
                                        echo '<td><button type="button" class="btn btn-danger" onclick="removeOrder('.$apiOrderRow['apiOrderID'].')"> Remove</button></td>';
                                    echo '</tr>';
                                }
                            echo '</tbody></table>';
                            
                            $success = 'Successfully pulled '.$lineCount;

                            $logInsertResult = mysqli_query($conn, "insert into tbl_api_log (merchantCode, logType, logDescription, creationDate, createdBy) values ('M-1-0262', 'pull', '$success', NOW() + INTERVAL 6 HOUR, 'admin')");
                        }                         
                    }
                } else {
                    echo "Error: there is no integration with this merchant";
                }
            }
            if($flag == 'acceptSingleApiOrder'){
                $comments = $_POST['comments'];
                $apiOrdersRow = mysqli_fetch_array(mysqli_query($conn, "select * from tbl_api_orders where apiOrderID = '$orderid'")) or die("Error: unable to fetch pulled orders ".mysqli_error($conn));
                $rootData = $orderid;
                $merchantCode = $apiOrdersRow['merchantCode'];
                $pickUpMerchantID = $apiOrdersRow['pickUpMerchantID'];
                $pickPointCode = $apiOrdersRow['pickPointCode'];
                $dropPointCode = $apiOrdersRow['dropPointCode'];
                $customerName = $apiOrdersRow['customerName'];
                $customerAddress = $apiOrdersRow['customerAddress'];
                $customerNumber = $apiOrdersRow['customerNumber'];
                $productType  = $apiOrdersRow['productType'];
                $deliveryType = strtoupper($apiOrdersRow['deliveryType']);
                $packageType = strtoupper($apiOrdersRow['packageType']);
                $merOrderRef = $apiOrdersRow['merOrderRef'];
                
                
                //$barCodeNo = substr($apiOrdersRow['barcode'],0,10);
                

                $destination = $apiOrdersRow['destination'];
                $packagePrice = $apiOrdersRow['packagePrice'];
                $customerDistrict = $apiOrdersRow['customerDistrict'];
                $customerThana = $apiOrdersRow['customerThana'];

                $pickUpMerchantRow = mysqli_fetch_array(mysqli_query($conn, "select * from tbl_pickup_merchant_info where pickMerchantID = '$pickUpMerchantID' and merchantCode = '$merchantCode'"));
                $pickMerchantName = $pickUpMerchantRow['pickMerchantName'];
                $pickMerchantAddress = $pickUpMerchantRow['pickMerchantAddress'];
                $pickupMerchantPhone = $pickUpMerchantRow['phone1'].','.$pickUpMerchantRow['phone2'];

                if($merchantCode == 'M-1-0262' && $pickUpMerchantID > 0){
                    $merchantCode = 'M-1-0441';
                }


                $merchantRateChartRow = mysqli_fetch_array(mysqli_query($conn, "select ratechartId, cod from tbl_merchant_info where merchantCode = '$merchantCode'")) or die("Error: unable to fertch merchant info ".mysqli_error($conn));
                $merchantRateChart = $merchantRateChartRow['ratechartId'];
                $cod = $merchantRateChartRow['cod'];
                
                $rateChartRow = mysqli_fetch_array(mysqli_query($conn, "select * from tbl_rate_type where UPPER(packageOption) = '$packageType' and UPPER(deliveryOption) = '$deliveryType' and destination = '$destination' and ratechartId = '$merchantRateChart'")) or die("Error: unable to fetch rate information ".mysqli_error($conn)); 
                $charge = $rateChartRow['charge'];

                $orderDate = date('Y-m-d');
                $maxemordid ="Select max(ordSeq) as ordSeq from tbl_order_details where orderDate='$orderDate'";
                $maxresult = mysqli_query($conn, $maxemordid);
                foreach ($maxresult as $maxrow){
                    $orderid = $maxrow['ordSeq']+1;
                    $ordSeq = $maxrow['ordSeq']+1; 
                }
                switch (strlen($orderid)){
                    case 1: $orderid = "000".$orderid;
                    break;
                    case 2: $orderid = "00".$orderid;
                    break;
                    case 3: $orderid = "0".$orderid;
                    break;
                    default:
                        echo "";
                }

                $packageType = strtolower($packageType);
                $deliveryType = strtolower($deliveryType);

                $barCodeNo = date("dmy", strtotime($orderDate)).$orderid."0";

                $orderid = date("dmy", strtotime($orderDate))."-".$orderid."-".$pickPointCode."-".$dropPointCode;

                $insertSQL = "insert into tbl_order_details (ordSeq, orderid, barcode, orderType, pickPointCode, dropPointCode, merchantCode, merOrderRef, orderDate, pickMerchantName, pickMerchantAddress, pickupMerchantPhone, productSizeWeight, deliveryOption,";
                $insertSQL .= " packagePrice, custname, custaddress, customerThana, customerDistrict, custphone, ratechartId, destination, charge, cod, api , creation_date, created_by)";
                $insertSQL .= " values ('$ordSeq', '$orderid', '$barCodeNo', 'Merchant', '$pickPointCode', '$dropPointCode', '$merchantCode', '$merOrderRef', '$orderDate', '$pickMerchantName', '$pickMerchantAddress', '$pickupMerchantPhone', '$packageType', '$deliveryType',";
                $insertSQL .= " '$packagePrice', '$customerName', '$customerAddress', '$customerThana', '$customerDistrict', '$customerNumber', '$merchantRateChart', '$destination', '$charge', '$cod', 'Y', NOW() + INTERVAL 6 HOUR, '$user_check')";

                if(!mysqli_query($conn, $insertSQL)){
                    echo "Error: unable accept order ".mysqli_error($conn);
                } else {
                    echo $orderid;
                    $updatePullStatusResult = mysqli_query($conn, "update tbl_api_orders set processed = 'Y' where apiOrderID = '$rootData'");
                    if($pickUpMerchantID > 0){
                        $curl = curl_init();

                        curl_setopt_array($curl, array(
                          CURLOPT_URL => "http://bridge.ajkerdeal.com/ThirdPartyOrderAction/UpdateStatusByCourier",
                          CURLOPT_RETURNTRANSFER => true,
                          CURLOPT_ENCODING => "",
                          CURLOPT_MAXREDIRS => 10,
                          CURLOPT_TIMEOUT => 30,
                          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                          CURLOPT_CUSTOMREQUEST => "POST",
                          CURLOPT_POSTFIELDS => "\t{\r\n\t\t\"OrderIds\":[".$merOrderRef."],\r\n\t\t\"StatusId\": 1001,\r\n    \t\"ThirdPartyId\":30,\r\n    \t\"Comments\":\"".$comments."\"\r\n\t}\r\n",
                          CURLOPT_HTTPHEADER => array(
                            "API_KEY: Ajkerdeal_~La?Rj73FcLm",
                            "Authorization: Basic UGFwZXJGbHk6SGpGZTVWNWY=", 
                            "Content-Type: application/json",
                            "Postman-Token: 1fd890aa-67a2-4d10-ae6c-d1da93a6f4ca",
                            "cache-control: no-cache"
                          ),
                        ));

                        $response = curl_exec($curl);
                        $err = curl_error($curl);

                        curl_close($curl);                        
                    }
                }
            }
            if($flag == 'acceptAllApiOrder'){
                $errorText ='';
                $errorCode = 0;
                $orderCount = 0;
                $apiOrdersResult = mysqli_query($conn, "select * from tbl_api_orders where dataValidity = 'Y' and processed = 'N'") or die("Error: unable to fetch pulled orders ".mysqli_error($conn));
                foreach($apiOrdersResult as $apiOrdersRow){
                    $rootData = $apiOrdersRow['apiOrderID'];
                    $merchantCode = $apiOrdersRow['merchantCode'];
                    $pickPointCode = $apiOrdersRow['pickPointCode'];
                    $dropPointCode = $apiOrdersRow['dropPointCode'];
                    $customerName = $apiOrdersRow['customerName'];
                    $customerAddress = $apiOrdersRow['customerAddress'];
                    $customerNumber = $apiOrdersRow['customerNumber'];
                    $productType  = $apiOrdersRow['productType'];
                    $deliveryType = strtoupper($apiOrdersRow['deliveryType']);
                    $packageType = strtoupper($apiOrdersRow['packageType']);
                    $merOrderRef = $apiOrdersRow['merOrderRef'];
                    $barCodeNo = substr($apiOrdersRow['barcode'],0,10);
                    $destination = $apiOrdersRow['destination'];
                    $packagePrice = $apiOrdersRow['packagePrice'];
                    $customerDistrict = $apiOrdersRow['customerDistrict'];
                    $customerThana = $apiOrdersRow['customerThana'];

                    $pickUpMerchantRow = mysqli_fetch_array(mysqli_query($conn, "select * from tbl_pickup_merchant_info where pickMerchantID = '$pickUpMerchantID' and merchantCode = '$merchantCode'"));
                    $pickMerchantName = $pickUpMerchantRow['pickMerchantName'];
                    $pickMerchantAddress = $pickUpMerchantRow['pickMerchantAddress'];
                    $pickupMerchantPhone = $pickUpMerchantRow['phone1'].','.$pickUpMerchantRow['phone2'];
                
                    $merchantRateChartRow = mysqli_fetch_array(mysqli_query($conn, "select ratechartId, cod from tbl_merchant_info where merchantCode = '$merchantCode'")) or die("Error: unable to fertch merchant info ".mysqli_error($conn));
                    $merchantRateChart = $merchantRateChartRow['ratechartId'];
                    $cod = $merchantRateChartRow['cod'];
                
                    $rateChartRow = mysqli_fetch_array(mysqli_query($conn, "select * from tbl_rate_type where UPPER(packageOption) = '$packageType' and UPPER(deliveryOption) = '$deliveryType' and destination = '$destination' and ratechartId = '$merchantRateChart'")) or die("Error: unable to fetch rate information ".mysqli_error($conn)); 
                    $charge = $rateChartRow['charge'];

                    if($charge == 0){
                        echo $orderCount.' order processed successfully';
                        exit();
                    }

                    $orderDate = date('Y-m-d');
                    $maxemordid ="Select max(ordSeq) as ordSeq from tbl_order_details where orderDate='$orderDate'";
                    $maxresult = mysqli_query($conn, $maxemordid);
                    foreach ($maxresult as $maxrow){
                        $orderid = $maxrow['ordSeq']+1;
                        $ordSeq = $maxrow['ordSeq']+1; 
                    }
                    switch (strlen($orderid)){
                        case 1: $orderid = "000".$orderid;
                        break;
                        case 2: $orderid = "00".$orderid;
                        break;
                        case 3: $orderid = "0".$orderid;
                        break;
                        default:
                            echo "Null";
                    }

                    $packageType = strtolower($packageType);
                    $deliveryType = strtolower($deliveryType);

                    $orderid = date("dmy", strtotime($orderDate))."-".$orderid."-".$pickPointCode."-".$dropPointCode;

                    $insertSQL = "insert into tbl_order_details (ordSeq, orderid, barcode, orderType, pickPointCode, dropPointCode, merchantCode, merOrderRef, orderDate, productSizeWeight, deliveryOption,";
                    $insertSQL .= " packagePrice, custname, custaddress, customerThana, customerDistrict, custphone, ratechartId, destination, charge, cod, api , creation_date, created_by)";
                    $insertSQL .= " values ('$ordSeq', '$orderid', '$barCodeNo', 'Merchant', '$pickPointCode', '$dropPointCode', '$merchantCode', '$merOrderRef', '$orderDate', '$packageType', '$deliveryType',";
                    $insertSQL .= " '$packagePrice', '$customerName', '$customerAddress', '$customerThana', '$customerDistrict', '$customerNumber', '$merchantRateChart', '$destination', '$charge', '$cod', 'Y', NOW() + INTERVAL 6 HOUR, '$user_check')";

                    if(!mysqli_query($conn, $insertSQL)){
                        $errorText = "Error: unable accept order ".mysqli_error($conn);
                        $errorCode++;
                        exit();
                    } else {
                        $updatePullStatusResult = mysqli_query($conn, "update tbl_api_orders set processed = 'Y' where apiOrderID = '$rootData'");
                        $orderCount++;
                    }                    
                }
                if($errorCode == 0){
                    echo $orderCount.' order processed successfully';
                } else {
                    echo $errorText;
                }
                
            }
            if($flag == 'deleteSingleApiOrder'){
                $reason = $_POST['reason'];

                $merOrderRefRow = mysqli_fetch_array(mysqli_query($conn, "select merOrderRef, pickUpMerchantID from tbl_api_orders where apiOrderID = '$orderid'"));
                $merOrderRef = $merOrderRefRow['merOrderRef'];
                $pickUpMerchantID = $merOrderRefRow['pickUpMerchantID'];

                $deleteSQL = "delete from tbl_api_orders where apiOrderID = '$orderid'";

                if(!mysqli_query($conn, $deleteSQL)){
                    echo "Error: unable to delete order ".mysqli_error($conn);
                } else {
                    echo 'Deleted successfully';
                    if($pickUpMerchantID > 0){
                        $curl = curl_init();

                        curl_setopt_array($curl, array(
                          CURLOPT_URL => "http://bridge.ajkerdeal.com/ThirdPartyOrderAction/UpdateStatusByCourier",
                          CURLOPT_RETURNTRANSFER => true,
                          CURLOPT_ENCODING => "",
                          CURLOPT_MAXREDIRS => 10,
                          CURLOPT_TIMEOUT => 30,
                          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                          CURLOPT_CUSTOMREQUEST => "POST",
                          CURLOPT_POSTFIELDS => "\t{\r\n\t\t\"OrderIds\":[".$merOrderRef."],\r\n\t\t\"StatusId\": 1002,\r\n    \t\"ThirdPartyId\":30,\r\n    \t\"Comments\":\"".$reason."\"\r\n\t}\r\n",
                          CURLOPT_HTTPHEADER => array(
                            "API_KEY: Ajkerdeal_~La?Rj73FcLm",
                            "Authorization: Basic UGFwZXJGbHk6SGpGZTVWNWY=",
                            "Content-Type: application/json",
                            "Postman-Token: 1fd890aa-67a2-4d10-ae6c-d1da93a6f4ca",
                            "cache-control: no-cache"
                          ),
                        ));

                        $response = curl_exec($curl);
                        $err = curl_error($curl);

                        curl_close($curl);                        
                    }
                }
            }
            if($flag == 'notifySingleApiOrder'){
                $reason = $_POST['reason'];

                $merOrderRefRow = mysqli_fetch_array(mysqli_query($conn, "select merOrderRef, pickUpMerchantID from tbl_api_orders where apiOrderID = '$orderid'"));
                $merOrderRef = $merOrderRefRow['merOrderRef'];
                $pickUpMerchantID = $merOrderRefRow['pickUpMerchantID'];

                //$deleteSQL = "delete from tbl_api_orders where apiOrderID = '$orderid'";

                //if(!mysqli_query($conn, $deleteSQL)){
                //    echo "Error: unable to delete order ".mysqli_error($conn);
                //} else {
                //    echo 'Deleted successfully';
                    if($pickUpMerchantID > 0){
                        $curl = curl_init();

                        curl_setopt_array($curl, array(
                          CURLOPT_URL => "http://bridge.ajkerdeal.com/ThirdPartyOrderAction/UpdateStatusByCourier",
                          CURLOPT_RETURNTRANSFER => true,
                          CURLOPT_ENCODING => "",
                          CURLOPT_MAXREDIRS => 10,
                          CURLOPT_TIMEOUT => 30,
                          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                          CURLOPT_CUSTOMREQUEST => "POST",
                          CURLOPT_POSTFIELDS => "\t{\r\n\t\t\"OrderIds\":[".$merOrderRef."],\r\n\t\t\"StatusId\": 1002,\r\n    \t\"ThirdPartyId\":30,\r\n    \t\"Comments\":\"".$reason."\"\r\n\t}\r\n",
                          CURLOPT_HTTPHEADER => array(
                            "API_KEY: Ajkerdeal_~La?Rj73FcLm",
                            "Authorization: Basic UGFwZXJGbHk6SGpGZTVWNWY=",
                            "Content-Type: application/json",
                            "Postman-Token: 1fd890aa-67a2-4d10-ae6c-d1da93a6f4ca",
                            "cache-control: no-cache"
                          ),
                        ));

                        $response = curl_exec($curl);
                        $err = curl_error($curl);

                        curl_close($curl);                        
                    }
                //}
            }
            if($flag == 'ajaxEncryption'){
                function encryptor($action, $string) {
                    $output = false;

                    $encrypt_method = "AES-256-CBC";
                    //pls set your unique hashing key
                    $secret_key = 'shLitu';
                    $secret_iv = '12Litu34';

                    // hash
                    $key = hash('sha256', $secret_key);

                    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
                    $iv = substr(hash('sha256', $secret_iv), 0, 16);

                    //do the encyption given text/string/number
                    if( $action == 'encrypt' ) {
                        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
                        $output = base64_encode($output);
                    }
                    else if( $action == 'decrypt' ){
    	                //decrypt the given text/string/number
                        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
                    }

                    return $output;
                }
                $id = urlencode(encryptor('encrypt', $rootData));
                echo $id;                        
            }
            if($flag == 'returnOrdersTrack'){
                $merchantCode = $_POST['merchantCode'];

                if($orderid == 0 and $merchantCode == '0'){
            
                    $pointListResult = mysqli_query($conn, "select * from tbl_employee_point where empCode = '$pointManager'") or die("Error : failed to get point info".mysqli_error($conn));
                    $pointRegion = 1;

                    $pointSumResult = mysqli_query($conn, "select pointid, pointCode, pointName, region, regionSort, returnOrders.retCount, returnedDP2Orders.retCount as cpOrders from tbl_point_info left join (select dropPOintCode, count(1) as retCount from tbl_order_details where Ret = 'Y' and DropDP2 is null and close is null group by dropPointCode) as returnOrders on tbl_point_info.pointCode = returnOrders.dropPointCode left join (select dropPOintCode, count(1) as retCount from tbl_order_details where Ret = 'Y' and DropDP2 = 'Y' and retcp1 is null and close is null group by dropPointCode) as returnedDP2Orders on tbl_point_info.pointCode = returnedDP2Orders.dropPointCode  order by regionSort, pointCode") or die("Error : failed to get point summary".mysqli_error($conn));           
                    
                    echo '<table class="table table-hover" id="returnTable">';                 
                    echo '<thead><tr><th>Point Code</th><th>Point Name</th><th style="text-align: right">DP2 Pendings</th><th style="text-align: right">CP Pendings</th></tr></thead><tbody>';
                    function encryptor($action, $string) {
                        $output = false;

                        $encrypt_method = "AES-256-CBC";
                        //pls set your unique hashing key
                        $secret_key = 'shLitu';
                        $secret_iv = '12Litu34';

                        // hash
                        $key = hash('sha256', $secret_key);

                        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
                        $iv = substr(hash('sha256', $secret_iv), 0, 16);

                        //do the encyption given text/string/number
                        if( $action == 'encrypt' ) {
                            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
                            $output = base64_encode($output);
                        }
                        else if( $action == 'decrypt' ){
    	                    //decrypt the given text/string/number
                            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
                        }

                        return $output;
                    }

                    $lineCount = 0;
                    $grandReturnTotal = 0;
                        foreach($pointSumResult as $pointSumRow){
                            if($lineCount == 0){
                                echo '<tr><td colspan=2 style="color: #16469E; cursor: pointer; font-weight: 800" onclick="regionDetail('.$pointSumRow['regionSort'].')">'.$pointSumRow['region'].'<p style="font-size: 8px; display: inline-block">&nbsp;&nbsp;(Click for Detail)</p></td><td></td><td></td></tr>';
                            }
                            $pointCode = urlencode(encryptor('encrypt', $pointSumRow['pointCode']));

                            if($pointRegion == $pointSumRow['regionSort']){
                                echo '<tr class="'.$pointSumRow['regionSort'].'" hidden><td>'.$pointSumRow['pointCode'].'</td><td>'.$pointSumRow['pointName'].'</td><td style="text-align: right"><a href="Return-Pendings?xxCode='.$pointCode.'&slCode='.$orderid.'&yyCode='.$merchantCode.'&cpCode=0" target="_blank">'.$pointSumRow['retCount'].'</a></td><td style="text-align: right"><a href="Return-Pendings?xxCode='.$pointCode.'&slCode='.$orderid.'&yyCode='.$merchantCode.'&cpCode=1" target="_blank">'.$pointSumRow['cpOrders'].'</a></td></tr>';
                                $totalReturn = $totalReturn + $pointSumRow['retCount'];
                                $totalCPPendings = $totalCPPendings + $pointSumRow['cpOrders'];
                                $grandReturnTotal = $grandReturnTotal + $pointSumRow['retCount'];
                                $grandCPTotal = $grandCPTotal + $pointSumRow['cpOrders'];
                            } else {
                                $pointRegion = $pointSumRow['regionSort'];
                                echo '<tr style="font-weight: 800"><td colspan=2>Total:</td><td style="text-align: right">'.$totalReturn.'</td><td style="text-align: right">'.$totalCPPendings.'</td></tr>';
                                echo '<tr><td colspan=2 style="color: #16469E; cursor: pointer; font-weight: 800" onclick="regionDetail('.$pointSumRow['regionSort'].')">'.$pointSumRow['region'].'<p style="font-size: 8px; display: inline-block">&nbsp;&nbsp;(Click for Detail)</p></td><td></td><td></td></tr>';
                                $totalReturn = 0;
                                $totalCPPendings = 0;
                            }
                            $pointOrder = 0;
                            $lineCount++; 
                        }
                        echo '<tr style="font-weight: 800"><td colspan=2>Total:</td><td style="text-align: right">'.$totalReturn.'</td><td style="text-align: right">'.$totalCPPendings.'</td></tr>';
                        echo '<tr style="font-weight: 800"><td colspan=2>Grand Total:</td><td style="text-align: right">'.$grandReturnTotal.'</td><td style="text-align: right">'.$grandCPTotal.'</td></tr>';
                    echo '</tbody></table>';                    
                }
                if($orderid == 0 and $merchantCode != '0'){
                    $pointListResult = mysqli_query($conn, "select * from tbl_employee_point where empCode = '$pointManager'") or die("Error : failed to get point info".mysqli_error($conn));
                    $pointRegion = 1;

                    $pointSumResult = mysqli_query($conn, "select pointid, pointCode, pointName, region, regionSort, returnOrders.retCount, returnedDP2Orders.retCount as cpOrders from tbl_point_info left join (select dropPOintCode, count(1) as retCount from tbl_order_details where Ret = 'Y' and DropDP2 is null and close is null and merchantCode = '$merchantCode' group by dropPointCode) as returnOrders on tbl_point_info.pointCode = returnOrders.dropPointCode left join (select dropPOintCode, count(1) as retCount from tbl_order_details where Ret = 'Y' and DropDP2 = 'Y' and retcp1 is null and close is null and merchantCode = '$merchantCode' group by dropPointCode) as returnedDP2Orders on tbl_point_info.pointCode = returnedDP2Orders.dropPointCode  order by regionSort, pointCode") or die("Error : failed to get point summary".mysqli_error($conn));           
                    
                    echo '<table class="table table-hover" id="returnTable">';                 
                    echo '<thead><tr><th>Point Code</th><th>Point Name</th><th style="text-align: right">DP2 Pendings</th><th style="text-align: right">CP Pendings</th></tr></thead><tbody>';
                    function encryptor($action, $string) {
                        $output = false;

                        $encrypt_method = "AES-256-CBC";
                        //pls set your unique hashing key
                        $secret_key = 'shLitu';
                        $secret_iv = '12Litu34';

                        // hash
                        $key = hash('sha256', $secret_key);

                        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
                        $iv = substr(hash('sha256', $secret_iv), 0, 16);

                        //do the encyption given text/string/number
                        if( $action == 'encrypt' ) {
                            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
                            $output = base64_encode($output);
                        }
                        else if( $action == 'decrypt' ){
    	                    //decrypt the given text/string/number
                            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
                        }

                        return $output;
                    }

                    $lineCount = 0;
                    $grandReturnTotal = 0;
                        foreach($pointSumResult as $pointSumRow){
                            if($lineCount == 0){
                                echo '<tr><td colspan=2 style="color: #16469E; cursor: pointer; font-weight: 800" onclick="regionDetail('.$pointSumRow['regionSort'].')">'.$pointSumRow['region'].'<p style="font-size: 8px; display: inline-block">&nbsp;&nbsp;(Click for Detail)</p></td><td></td><td></td></tr>';
                            }
                            $pointCode = urlencode(encryptor('encrypt', $pointSumRow['pointCode']));

                            if($pointRegion == $pointSumRow['regionSort']){
                                echo '<tr class="'.$pointSumRow['regionSort'].'" hidden><td>'.$pointSumRow['pointCode'].'</td><td>'.$pointSumRow['pointName'].'</td><td style="text-align: right"><a href="Return-Pendings?xxCode='.$pointCode.'&slCode='.$orderid.'&yyCode='.$merchantCode.'&cpCode=0" target="_blank">'.$pointSumRow['retCount'].'</a></td><td style="text-align: right"><a href="Return-Pendings?xxCode='.$pointCode.'&slCode='.$orderid.'&yyCode='.$merchantCode.'&cpCode=1" target="_blank">'.$pointSumRow['cpOrders'].'</a></td></tr>';
                                $totalReturn = $totalReturn + $pointSumRow['retCount'];
                                $totalCPPendings = $totalCPPendings + $pointSumRow['cpOrders'];
                                $grandReturnTotal = $grandReturnTotal + $pointSumRow['retCount'];
                                $grandCPTotal = $grandCPTotal + $pointSumRow['cpOrders'];
                            } else {
                                $pointRegion = $pointSumRow['regionSort'];
                                echo '<tr style="font-weight: 800"><td colspan=2>Total:</td><td style="text-align: right">'.$totalReturn.'</td><td style="text-align: right">'.$totalCPPendings.'</td></tr>';
                                echo '<tr><td colspan=2 style="color: #16469E; cursor: pointer; font-weight: 800" onclick="regionDetail('.$pointSumRow['regionSort'].')">'.$pointSumRow['region'].'<p style="font-size: 8px; display: inline-block">&nbsp;&nbsp;(Click for Detail)</p></td><td></td><td></td></tr>';
                                $totalReturn = 0;
                                $totalCPPendings = 0;
                            }
                            $pointOrder = 0;
                            $lineCount++; 
                        }
                        echo '<tr style="font-weight: 800"><td colspan=2>Total:</td><td style="text-align: right">'.$totalReturn.'</td><td style="text-align: right">'.$totalCPPendings.'</td></tr>';
                        echo '<tr style="font-weight: 800"><td colspan=2>Grand Total:</td><td style="text-align: right">'.$grandReturnTotal.'</td><td style="text-align: right">'.$grandCPTotal.'</td></tr>';
                    echo '</tbody></table>';                    
                }
                if($orderid != 0 and $merchantCode != '0'){
                    $pointListResult = mysqli_query($conn, "select * from tbl_employee_point where empCode = '$pointManager'") or die("Error : failed to get point info".mysqli_error($conn));
                    $pointRegion = 1;

                    $pointSumResult = mysqli_query($conn, "select pointid, pointCode, pointName, region, regionSort, returnOrders.retCount, returnedDP2Orders.retCount as cpOrders from tbl_point_info left join (select dropPOintCode, count(1) as retCount from tbl_order_details where Ret = 'Y' and DropDP2 is null and close is null and merchantCode = '$merchantCode' and DATE_FORMAT(retTime, '%Y-%m-%d') < DATE_SUB(CURDATE(), INTERVAL $orderid DAY) group by dropPointCode) as returnOrders on tbl_point_info.pointCode = returnOrders.dropPointCode left join (select dropPOintCode, count(1) as retCount from tbl_order_details where Ret = 'Y' and DropDP2 = 'Y' and retcp1 is null and close is null and merchantCode = '$merchantCode' and DATE_FORMAT(retTime, '%Y-%m-%d') < DATE_SUB(CURDATE(), INTERVAL $orderid DAY) group by dropPointCode) as returnedDP2Orders on tbl_point_info.pointCode = returnedDP2Orders.dropPointCode  order by regionSort, pointCode") or die("Error : failed to get point summary".mysqli_error($conn));           
                    
                    echo '<table class="table table-hover" id="returnTable">';                 
                    echo '<thead><tr><th>Point Code</th><th>Point Name</th><th style="text-align: right">DP2 Pendings</th><th style="text-align: right">CP Pendings</th></tr></thead><tbody>';
                    function encryptor($action, $string) {
                        $output = false;

                        $encrypt_method = "AES-256-CBC";
                        //pls set your unique hashing key
                        $secret_key = 'shLitu';
                        $secret_iv = '12Litu34';

                        // hash
                        $key = hash('sha256', $secret_key);

                        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
                        $iv = substr(hash('sha256', $secret_iv), 0, 16);

                        //do the encyption given text/string/number
                        if( $action == 'encrypt' ) {
                            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
                            $output = base64_encode($output);
                        }
                        else if( $action == 'decrypt' ){
    	                    //decrypt the given text/string/number
                            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
                        }

                        return $output;
                    }

                    $lineCount = 0;
                    $grandReturnTotal = 0;
                        foreach($pointSumResult as $pointSumRow){
                            if($lineCount == 0){
                                echo '<tr><td colspan=2 style="color: #16469E; cursor: pointer; font-weight: 800" onclick="regionDetail('.$pointSumRow['regionSort'].')">'.$pointSumRow['region'].'<p style="font-size: 8px; display: inline-block">&nbsp;&nbsp;(Click for Detail)</p></td><td></td><td></td></tr>';
                            }
                            $pointCode = urlencode(encryptor('encrypt', $pointSumRow['pointCode']));

                            if($pointRegion == $pointSumRow['regionSort']){
                                echo '<tr class="'.$pointSumRow['regionSort'].'" hidden><td>'.$pointSumRow['pointCode'].'</td><td>'.$pointSumRow['pointName'].'</td><td style="text-align: right"><a href="Return-Pendings?xxCode='.$pointCode.'&slCode='.$orderid.'&yyCode='.$merchantCode.'&cpCode=0" target="_blank">'.$pointSumRow['retCount'].'</a></td><td style="text-align: right"><a href="Return-Pendings?xxCode='.$pointCode.'&slCode='.$orderid.'&yyCode='.$merchantCode.'&cpCode=1" target="_blank">'.$pointSumRow['cpOrders'].'<a></td></tr>';
                                $totalReturn = $totalReturn + $pointSumRow['retCount'];
                                $totalCPPendings = $totalCPPendings + $pointSumRow['cpOrders'];
                                $grandReturnTotal = $grandReturnTotal + $pointSumRow['retCount'];
                                $grandCPTotal = $grandCPTotal + $pointSumRow['cpOrders'];
                            } else {
                                $pointRegion = $pointSumRow['regionSort'];
                                echo '<tr style="font-weight: 800"><td colspan=2>Total:</td><td style="text-align: right">'.$totalReturn.'</td><td style="text-align: right">'.$totalCPPendings.'</td></tr>';
                                echo '<tr><td colspan=2 style="color: #16469E; cursor: pointer; font-weight: 800" onclick="regionDetail('.$pointSumRow['regionSort'].')">'.$pointSumRow['region'].'<p style="font-size: 8px; display: inline-block">&nbsp;&nbsp;(Click for Detail)</p></td><td></td><td></td></tr>';
                                $totalReturn = 0;
                                $totalCPPendings = 0;
                            }
                            $pointOrder = 0;
                            $lineCount++; 
                        }
                        echo '<tr style="font-weight: 800"><td colspan=2>Total:</td><td style="text-align: right">'.$totalReturn.'</td><td style="text-align: right">'.$totalCPPendings.'</td></tr>';
                        echo '<tr style="font-weight: 800"><td colspan=2>Grand Total:</td><td style="text-align: right">'.$grandReturnTotal.'</td><td style="text-align: right">'.$grandCPTotal.'</td></tr>';
                    echo '</tbody></table>';                    
                }
                if($orderid != 0 and $merchantCode == '0'){
                    $pointListResult = mysqli_query($conn, "select * from tbl_employee_point where empCode = '$pointManager'") or die("Error : failed to get point info".mysqli_error($conn));
                    $pointRegion = 1;

                    $pointSumResult = mysqli_query($conn, "select pointid, pointCode, pointName, region, regionSort, returnOrders.retCount, returnedDP2Orders.retCount as cpOrders from tbl_point_info left join (select dropPOintCode, count(1) as retCount from tbl_order_details where Ret = 'Y' and DropDP2 is null and close is null and DATE_FORMAT(retTime, '%Y-%m-%d') < DATE_SUB(CURDATE(), INTERVAL $orderid DAY) group by dropPointCode) as returnOrders on tbl_point_info.pointCode = returnOrders.dropPointCode left join (select dropPOintCode, count(1) as retCount from tbl_order_details where Ret = 'Y' and DropDP2 = 'Y' and retcp1 is null and close is null and DATE_FORMAT(retTime, '%Y-%m-%d') < DATE_SUB(CURDATE(), INTERVAL $orderid DAY) group by dropPointCode) as returnedDP2Orders on tbl_point_info.pointCode = returnedDP2Orders.dropPointCode  order by regionSort, pointCode") or die("Error : failed to get point summary".mysqli_error($conn));           
                    
                    echo '<table class="table table-hover" id="returnTable">';                 
                    echo '<thead><tr><th>Point Code</th><th>Point Name</th><th style="text-align: right">DP2 Pendings</th><th style="text-align: right">CP Pendings</th></tr></thead><tbody>';
                    function encryptor($action, $string) {
                        $output = false;

                        $encrypt_method = "AES-256-CBC";
                        //pls set your unique hashing key
                        $secret_key = 'shLitu';
                        $secret_iv = '12Litu34';

                        // hash
                        $key = hash('sha256', $secret_key);

                        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
                        $iv = substr(hash('sha256', $secret_iv), 0, 16);

                        //do the encyption given text/string/number
                        if( $action == 'encrypt' ) {
                            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
                            $output = base64_encode($output);
                        }
                        else if( $action == 'decrypt' ){
    	                    //decrypt the given text/string/number
                            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
                        }

                        return $output;
                    }

                    $lineCount = 0;
                    $grandReturnTotal = 0;
                        foreach($pointSumResult as $pointSumRow){
                            if($lineCount == 0){
                                echo '<tr><td colspan=2 style="color: #16469E; cursor: pointer; font-weight: 800" onclick="regionDetail('.$pointSumRow['regionSort'].')">'.$pointSumRow['region'].'<p style="font-size: 8px; display: inline-block">&nbsp;&nbsp;(Click for Detail)</p></td><td></td><td></td></tr>';
                            }
                            $pointCode = urlencode(encryptor('encrypt', $pointSumRow['pointCode']));

                            if($pointRegion == $pointSumRow['regionSort']){
                                echo '<tr class="'.$pointSumRow['regionSort'].'" hidden><td>'.$pointSumRow['pointCode'].'</td><td>'.$pointSumRow['pointName'].'</td><td style="text-align: right"><a href="Return-Pendings?xxCode='.$pointCode.'&slCode='.$orderid.'&yyCode='.$merchantCode.'&cpCode=0" target="_blank">'.$pointSumRow['retCount'].'</a></td><td style="text-align: right"><a href="Return-Pendings?xxCode='.$pointCode.'&slCode='.$orderid.'&yyCode='.$merchantCode.'&cpCode=1" target="_blank">'.$pointSumRow['cpOrders'].'</a></td></tr>';
                                $totalReturn = $totalReturn + $pointSumRow['retCount'];
                                $totalCPPendings = $totalCPPendings + $pointSumRow['cpOrders'];
                                $grandReturnTotal = $grandReturnTotal + $pointSumRow['retCount'];
                                $grandCPTotal = $grandCPTotal + $pointSumRow['cpOrders'];
                            } else {
                                $pointRegion = $pointSumRow['regionSort'];
                                echo '<tr style="font-weight: 800"><td colspan=2>Total:</td><td style="text-align: right">'.$totalReturn.'</td><td style="text-align: right">'.$totalCPPendings.'</td></tr>';
                                echo '<tr><td colspan=2 style="color: #16469E; cursor: pointer; font-weight: 800" onclick="regionDetail('.$pointSumRow['regionSort'].')">'.$pointSumRow['region'].'<p style="font-size: 8px; display: inline-block">&nbsp;&nbsp;(Click for Detail)</p></td><td></td><td></td></tr>';
                                $totalReturn = 0;
                                $totalCPPendings = 0;
                            }
                            $pointOrder = 0;
                            $lineCount++; 
                        }
                        echo '<tr style="font-weight: 800"><td colspan=2>Total:</td><td style="text-align: right">'.$totalReturn.'</td><td style="text-align: right">'.$totalCPPendings.'</td></tr>';
                        echo '<tr style="font-weight: 800"><td colspan=2>Grand Total:</td><td style="text-align: right">'.$grandReturnTotal.'</td><td style="text-align: right">'.$grandCPTotal.'</td></tr>';
                    echo '</tbody></table>';                    
                }
            }
            if($flag == 'validateBarcode'){
                $barcodeInfoResult = mysqli_query($conn, "SELECT barcode_factory.*, tbl_merchant_info.merchantName FROM barcode_factory left join tbl_merchant_info on barcode_factory.merchant_code = tbl_merchant_info.merchantCode WHERE substring(barcodeNumber, 1, 11) = '$orderid'");
                if(mysqli_num_rows($barcodeInfoResult) > 0){
                    $barcodeInfoRow = mysqli_fetch_array($barcodeInfoResult);
                    echo '<script>';
                        echo '$("#pickedMerchant").html("'.$barcodeInfoRow['merchantName'].'"); ';
                        echo '$("#pickedBy").html("'.$barcodeInfoRow['updated_by'].'"); ';
                        echo '$("#m_code").val("'.$barcodeInfoRow['merchant_code'].'");';
                    echo '</script>';
                } else {
                    echo 'Error: no such pick up record found';

                }
            }
             if($flag == 'validateBarcode2'){
                
                $merchant = mysqli_real_escape_string($conn, $_POST["merchantCode"]); 
                $emp = mysqli_real_escape_string($conn, $_POST["employeeCode"]);


                 $barcodeInfoResult =  mysqli_query($conn, "SELECT barcode_factory.*, tbl_merchant_info.merchantName FROM barcode_factory left join tbl_merchant_info on barcode_factory.merchant_code = tbl_merchant_info.merchantCode WHERE substring(barcodeNumber, 1, 11) = '$orderid'");
                  $barcodeInfoResult1 =  mysqli_query($conn, "SELECT tbl_barcode_factory_fulfillment.*, tbl_merchant_info.merchantName FROM tbl_barcode_factory_fulfillment left join tbl_merchant_info on tbl_barcode_factory_fulfillment.merchant_id = tbl_merchant_info.merchantCode WHERE substring(barcodeNumber, 1, 11) = '$orderid'");
                  if(mysqli_num_rows($barcodeInfoResult)>0)
                  {
                    $result=1;
                  }else
                  {
                     $sql = "INSERT INTO barcode_factory (barcodeNumber, merchant_code, updated_by,state,scanned_by,scanned_at)
                    VALUES ('$orderid', '$merchant', '$emp',1,'nawshin',NOW()+INTERVAL 6 HOUR)";

                if ($conn->query($sql) === TRUE) {
                     $result = 1;
                    
                   // echo "New record created successfully";
                    $barcodeInfoResult =  mysqli_query($conn, "SELECT barcode_factory.*, tbl_merchant_info.merchantName FROM barcode_factory left join tbl_merchant_info on barcode_factory.merchant_code = tbl_merchant_info.merchantCode WHERE substring(barcodeNumber, 1, 11) = '$orderid'");
                    
                }
                else {
                   
                    echo "<div class='alert alert-danger'>";
           
                     echo "could not create order";
                     echo "</div>";
                }
                  
                  }

                  if(mysqli_num_rows($barcodeInfoResult1)>0)
                  {
                    $result=2;
                  }
                  if($result ==1)
                  {
                       $barcodeInfoRow = mysqli_fetch_array($barcodeInfoResult);
                       echo '<script>';
                        echo '$("#pickedMerchant1").html("'.$barcodeInfoRow['merchantName'].'"); ';
                        echo '$("#pickedBy1").html("'.$barcodeInfoRow['updated_by'].'"); ';
                        echo '$("#m_code").html("'.$barcodeInfoRow['merchant_code'].'");';
                      echo '</script>';
                      echo '<div class="col-sm-8">';
                           echo '<label style="color:white;">logistics</label>';
                       echo '</div>';
                  }elseif($result==2){
                     $barcodeInfoRow1 = mysqli_fetch_array($barcodeInfoResult1);
                        echo '<script>';
                        echo '$("#pickedMerchant1").html("'.$barcodeInfoRow1['merchantName'].'"); ';
                        echo '$("#pickedBy1").html("'.$barcodeInfoRow1['updated_by'].'"); ';
                        echo '$("#m_code").html("'.$barcodeInfoRow1['merchant_id'].'");';
                        echo '</script>';
                        echo '<div class="col-sm-8">';
                           echo '<label style="color:white;>fulfillment</label>';
                       echo '</div>';
                    }
                 else{
                     echo 'Error: no such pick up record found';
                    //echo mysqli_error($conn);

                    }
                
            }
            
          /*   if(isset($_POST['flag'])){
                $logOrfull = mysqli_real_escape_string($conn, $_POST["fl"]);
                
                
                if($logOrfull=='l'){

                $barcode = mysqli_real_escape_string($conn, $_POST["barcode"]); 
                $merchant = mysqli_real_escape_string($conn, $_POST["merchants"]); 
                $emp = mysqli_real_escape_string($conn, $_POST["employees"]);



               
               $sql = "INSERT INTO barcode_factory (barcodeNumber, merchant_code, updated_by,state,scanned_by,scanned_at)
               VALUES ('$barcode', '$merchant', '$emp',1,'nawshin',NOW()+INTERVAL 6 HOUR)";

                if ($conn->query($sql) === TRUE) {

                    echo "New record created successfully";
                    
                }
                else {
                   
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }    
                }elseif ($logOrfull=='f') {
                    $barcode = mysqli_real_escape_string($conn, $_POST["barcode"]); 
                $merchant = mysqli_real_escape_string($conn, $_POST["merchants"]); 
                $emp = mysqli_real_escape_string($conn, $_POST["employees"]);
                $pickmerchantname = mysqli_real_escape_string($conn, $_POST["pick_merchant"]);
                $product_quantity = mysqli_real_escape_string($conn, $_POST["product_qty"]);



               
               $sql = "INSERT INTO tbl_barcode_factory_fulfillment (barcodeNumber, merchant_id, updated_by,state,scanned_by,scanned_at,sub_merchant_name,picked_qty)
               VALUES ('$barcode', '$merchant', '$emp',1,'nawshin',NOW()+INTERVAL 6 HOUR,'$pickmerchantname','$product_quantity')";

                if ($conn->query($sql) === TRUE) {

                    echo "New record created successfully";
                    
                }
                else {
                   
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }    
                } 
                else{
                    echo "Error: NOT insert";
                }      
            }*/
             if($flag == 'update_barcode_factory'){
                 $merchant_code = trim($_POST['merchantCode']);
                 $table = trim($_POST['table']);
                 if($table==1)
                 {
                   $barcodeInfoResult =  mysqli_query($conn, "UPDATE barcode_factory SET state=1,scanned_at=NOW()+INTERVAL 6 HOUR,scanned_by='nawshin' WHERE substring(barcodeNumber, 1, 11) = '$orderid'");

                 $totalresult =  mysqli_query($conn, "SELECT updated_by,count(id) as total from barcode_factory where state=1 AND merchant_code ='$merchant_code' group by updated_by");
                 $sumup =  mysqli_query($conn, "SELECT count(id) as sum from barcode_factory where state=1 AND merchant_code='$merchant_code'");
                   if(mysqli_num_rows($totalresult) > 0){
                   $sums = mysqli_fetch_array($sumup);
                   while($row = mysqli_fetch_array($totalresult)){
                      $fields[] = $row;
                   } 
                   echo '<table class="table table-striped">';
                            echo '<thead><tr><th></th><th>Executive Name</th><th>Total Pick</th></tr></thead><tbody>';
                   foreach ($fields as $field) {
                    echo '<tr>';
                    echo '<td> </td>';
                    echo '<td>'.$field['updated_by'].'</td>';
                    echo '<td>'.$field['total'].'</td>';
                    echo '</tr>';
                      /* echo  '<div class="col-sm-12">';
                        
                       echo'<div class="row">';
                        echo '<div class="col-sm-4">';
                           echo '<label style="font-size: 14px; line-height: 20px;">'.$field['updated_by'].'</label>';
                            
                        echo '</div>';
                        echo '<div class="col-sm-8">';
                           echo '<label>'.$field['total'].'</label>';
                       echo '</div>';
                    echo '</div>';
                  echo '</div>';*/
                   } 
                   echo '</tbody></table>';
                   echo '<script>';
                        echo '$("#totalpicked1").html("'.$sums['sum'].'"); ';
                       
                    echo '</script>';
                }
                 
                else {

                    echo 'Error: no such pick up record found';
                }
                 }
                 elseif($table==2)
                 {
                     $barcodeInfoResult =  mysqli_query($conn, "UPDATE tbl_barcode_factory_fulfillment SET state=1,scanned_at=NOW()+INTERVAL 6 HOUR,scanned_by='nawshin' WHERE substring(barcodeNumber, 1, 11) = '$orderid'");

                 $totalresult =  mysqli_query($conn, "SELECT updated_by,count(id) as total from tbl_barcode_factory_fulfillment where state=1 AND merchant_id ='$merchant_code' group by updated_by");
                 $sumup =  mysqli_query($conn, "SELECT count(id) as sum from tbl_barcode_factory_fulfillment where state=1 AND merchant_id='$merchant_code'");
                   if(mysqli_num_rows($totalresult) > 0){
                   $sums = mysqli_fetch_array($sumup);
                   while($row = mysqli_fetch_array($totalresult)){
                      $fields[] = $row;
                   } 
                   echo '<table class="table table-striped">';
                            echo '<thead><tr><th></th><th>Executive Name</th><th>Total Pick</th></tr></thead><tbody>';
                   foreach ($fields as $field) {
                    echo '<tr>';
                    echo '<td> </td>';
                    echo '<td>'.$field['updated_by'].'</td>';
                    echo '<td>'.$field['total'].'</td>';
                    echo '</tr>';
                      
                   } 
                   echo '</tbody></table>';
                   echo '<script>';
                        echo '$("#totalpicked1").html("'.$sums['sum'].'"); ';
                       
                    echo '</script>';
                }
                 
                else {

                    echo 'Error: no such pick up record found';
                }
                 }
                 else {

                    echo 'Error: no such pick up record found';
                }

               
               
            }
            if($flag == 'searchCustMobileNo'){
                $merchantCode = $_POST['merchantCode'];
                $mobileNo = substr($orderid, -10);
                $foundFlag = 'F';
                $searchOrderResult = mysqli_query($conn, "select tbl_order_details.orderid, tbl_order_details.merOrderRef, tbl_order_details.merchantCode, tbl_merchant_info.merchantName, custname, custaddress, custphone, tbl_thana_info.thanaName, tbl_district_info.districtName from tbl_order_details left join tbl_merchant_info on tbl_order_details.merchantCode = tbl_merchant_info.merchantCode left join tbl_thana_info on tbl_order_details.customerThana = tbl_thana_info.thanaId left join tbl_district_info on tbl_order_details.customerDistrict = tbl_district_info.districtId where tbl_order_details.merchantCode = '$merchantCode' and tbl_order_details.Pick is null");

                foreach($searchOrderResult as $searchOrderRow){
                    $custphone = substr($searchOrderRow['custphone'], -10);
                    if($custphone == $mobileNo){
                        echo '<script>';
                            echo '$("#searchedMerchant").html("'.$searchOrderRow['merchantName'].'"); ';
                            echo '$("#customerName").html("'.$searchOrderRow['custname'].'"); ';
                            echo '$("#customerAddress").html("'.$searchOrderRow['custaddress'].'"); ';
                            echo '$("#customerPhone").html("'.$searchOrderRow['custphone'].'"); ';
                            echo '$("#customerThana").html("'.$searchOrderRow['thanaName'].'"); ';
                            echo '$("#customerDistrict").html("'.$searchOrderRow['districtName'].'"); ';
                            echo '$("#orderID").html("'.$searchOrderRow['orderid'].'"); ';
                            echo '$("#merchantRef").html("'.$searchOrderRow['merOrderRef'].'"); ';
                            echo '$("#paperflyID").val("'.$searchOrderRow['orderid'].'");';
                        echo '</script>';
                        $foundFlag = 'T';
                        exit();
                    }
                }
                if($foundFlag == 'F'){
                    echo 'Error: no such order exist';
                }
            }
            if($flag == 'acceptOrder'){
                $paperflyID = $_POST['paperflyID'];
                $pickedBy = $_POST['pickedBy'];

                $barCode = substr($orderid,0,11);

                $userInfoRow = mysqli_fetch_array(mysqli_query($conn, "select * from tbl_user_info where userName = '$pickedBy'"));

                $empCode = $userInfoRow['merchEmpCode'];

                if(empty($empCode)){
                    echo 'Error: user '.$pickedBy.' not found';
                } else {
                    $updateSQL = "update tbl_order_details set barcode = '$barCode', pickPointEmp = '$empCode', Pick = 'Y', PickTime = NOW() + INTERVAL 6 HOUR, PickBy = '$pickedBy' where orderid = '$paperflyID' and Pick is null";
                    if(!mysqli_query($conn, $updateSQL)){
                        echo 'Error: unable to accept product';
                    } else {
                        echo $paperflyID.' accepted for shuttle';
                    }   
                }
            }
            if($flag == 'viewOrderDetail'){
                $orderInfoRow = mysqli_fetch_array(mysqli_query($conn, "select * from tbl_order_details where orderid = '$orderid'"));

                echo '<script>';
                    echo '$("#price").val("'.$orderInfoRow['packagePrice'].'");';
                    echo '$("#packageOption").val("'.$orderInfoRow['productSizeWeight'].'");';
                    echo '$("#deliveryOption").val("'.$orderInfoRow['deliveryOption'].'");';
                    echo '$("#custDistrict").val("'.$orderInfoRow['customerDistrict'].'");';
                    echo '$("#custThana").val("'.$orderInfoRow['customerThana'].'");';
                echo '</script>';
            }







                                              
        }
     else {
        //header("location: http://paperflybd.com/login");
    }
?>