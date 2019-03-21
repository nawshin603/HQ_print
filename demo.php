<?php
include('session.php');
include('config.php');
if(isset($_POST['data'])){
    $pickPointEmp = $_POST['data'];
    $orderid = $_POST['order'];
    $flag = $_POST['flag'];
    if ($flag == 'assignPic'){
        $updatesql="update tbl_order_details set pickPointEmp = '$pickPointEmp' where orderid='$orderid'";
            //$result = mysqli_query($conn, $updatesql);
        if (!mysqli_query($conn,$updatesql)){
            $error ="Update Error : " . mysqli_error($conn);
            echo $error;
        } else {
            echo "success";

        }
        mysqli_close($conn);
        exit;            
    }
    if ($flag == 'Pick'){
        $picupdatesql="update tbl_order_details set Pick = 'Y', PickTime= NOW() + INTERVAL 6 HOUR, PickBy= '$user_check' where orderid='$orderid'";
            //$result = mysqli_query($conn, $updatesql);
        if (!mysqli_query($conn, $picupdatesql)){
            $error ="Update Error : " . mysqli_error($conn);
            echo $error;
        } else {
            echo "success";
            $merOrderRefRow = mysqli_fetch_array(mysqli_query($conn, "select merchantCode, merOrderRef,demo from tbl_order_details where orderid='$orderid'"));
            $merOrderRef = $merOrderRefRow['merOrderRef'];
            $merchantCode = $merOrderRefRow['merchantCode'];
            $merchantType = $merOrderRefRow['demo'];
            if($merchantCode == 'M-1-0484' AND $merchantType == 'robishop'){
                $curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => "https://robishop.com.bd/rest/V1/integration/admin/token",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 90,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "POST",
                  CURLOPT_POSTFIELDS => "{\n\t\"username\" : \"paperfly\",\n\t\"password\" : \"PaperFly123@\"\n}",
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
                $curl = curl_init();
                curl_setopt_array($curl, array(
                  CURLOPT_URL => "https://robishop.com.bd/rest/V1/deliverypartner",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 90,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "POST",
                  CURLOPT_POSTFIELDS => "{\n\t\"order_id\" : \"".$merOrderRef."\",\n\t\"status\" : \"processing\",\n\t\"comment\" : \"The order has been picked\"\n}",
                  CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer $token",
                    "Content-Type: application/json"

                ),
              ));

                $response = curl_exec($curl);
                $err = curl_error($curl);
                if ($err) {
                  //echo "cURL Error #:" . $err;
              } else {
                  //echo $response;
              }
              curl_close($curl);
          }
      }
       if($merchantCode == 'M-1-0484' AND $merchantType == 'digired'){
                $curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => "https://digired.shop/rest/V1/integration/admin/token",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 90,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "POST",
                  CURLOPT_POSTFIELDS => "{\n\t\"username\" : \"paperfly\",\n\t\"password\" : \"PaperFly123@\"\n}",
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
                $curl = curl_init();
                curl_setopt_array($curl, array(
                  CURLOPT_URL => "https://digired.shop/rest/V1/deliverypartner",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 90,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "POST",
                  CURLOPT_POSTFIELDS => "{\n\t\"order_id\" : \"".$merOrderRef."\",\n\t\"status\" : \"processing\",\n\t\"comment\" : \"The order has been picked\"\n}",
                  CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer $token",
                    "Content-Type: application/json"

                ),
              ));

                $response = curl_exec($curl);
                $err = curl_error($curl);
                if ($err) {
                  //echo "cURL Error #:" . $err;
              } else {
                  //echo $response;
              }
              curl_close($curl);
          }
      }
  }
  mysqli_close($conn);
  exit;            
}
if ($flag == 'DP1'){
    $updatesql="update tbl_order_details set DP1 = 'Y', DP1Time= NOW() + INTERVAL 6 HOUR, DP1By= '$user_check' where orderid='$orderid'";
            //$result = mysqli_query($conn, $updatesql);
    if (!mysqli_query($conn,$updatesql)){
        $error ="Update Error : " . mysqli_error($conn);
        echo $error;
    } else {
        echo "success";
    }
    mysqli_close($conn);
    exit;            
}
if ($flag == 'Shtl'){
    $updatesql="update tbl_order_details set Shtl = 'Y', ShtlTime= NOW() + INTERVAL 6 HOUR, ShtlBy= '$user_check' where orderid='$orderid'";
            //$result = mysqli_query($conn, $updatesql);
    if (!mysqli_query($conn,$updatesql)){
        $error ="Update Error : " . mysqli_error($conn);
        echo $error;
    } else {
        echo "success";
    }
    mysqli_close($conn);
    exit;            
}
if ($flag == 'cp1'){
    $centralPoint = substr($orderid,12,1)."0";
    $userCodeSQL = "select merchEmpCode from tbl_user_info where userName = '$user_check'";
    $userCodeResult = mysqli_query($conn, $userCodeSQL);
    $userCodeRow = mysqli_fetch_array($userCodeResult);
    $userCode = $userCodeRow['merchEmpCode'];
    $cpPermissionSQL = "select empCode from tbl_employee_point where empCode = '$userCode' and pointCode = '$centralPoint'";
    $cpPermissionResult = mysqli_query($conn, $cpPermissionSQL);
    if (mysqli_num_rows($cpPermissionResult) > 0) {
        $updatesql="update tbl_order_details set cp1 = 'Y', cp1Time= NOW() + INTERVAL 6 HOUR, cp1By= '$user_check' where orderid='$orderid'";
        if (!mysqli_query($conn,$updatesql)){
            $error ="Update Error : " . mysqli_error($conn);
            echo $error;
        } else {
            echo "success";
        }
        mysqli_close($conn);
    }
    exit;            
}
if ($flag == 'cp1Shuttle'){
    $centralPoint = substr($orderid,12,1)."0";
    $userCodeSQL = "select merchEmpCode from tbl_user_info where userName = '$user_check'";
    $userCodeResult = mysqli_query($conn, $userCodeSQL);
    $userCodeRow = mysqli_fetch_array($userCodeResult);
    $userCode = $userCodeRow['merchEmpCode'];
    $cpPermissionSQL = "select empCode from tbl_employee_point where empCode = '$userCode' and pointCode = '$centralPoint'";
    $cpPermissionResult = mysqli_query($conn, $cpPermissionSQL);
    if (mysqli_num_rows($cpPermissionResult) > 0) {
        $updatesql="update tbl_order_details set cp1Shuttle = 'Y', cp1ShuttleTime= NOW() + INTERVAL 6 HOUR, cp1ShuttleBy= '$user_check' where orderid='$orderid'";
        if (!mysqli_query($conn,$updatesql)){
            $error ="Update Error : " . mysqli_error($conn);
            echo $error;
        } else {
            echo "success";
        }
        mysqli_close($conn);
    }
    exit;            
}
if ($flag == 'cp2'){
    $centralPoint = substr($orderid,15,1)."0";
    $userCodeSQL = "select merchEmpCode from tbl_user_info where userName = '$user_check'";
    $userCodeResult = mysqli_query($conn, $userCodeSQL);
    $userCodeRow = mysqli_fetch_array($userCodeResult);
    $userCode = $userCodeRow['merchEmpCode'];
    $cpPermissionSQL = "select empCode from tbl_employee_point where empCode = '$userCode' and pointCode = '$centralPoint'";
    $cpPermissionResult = mysqli_query($conn, $cpPermissionSQL);
    if (mysqli_num_rows($cpPermissionResult) > 0) {
        $updatesql="update tbl_order_details set cp2 = 'Y', cp2Time= NOW() + INTERVAL 6 HOUR, cp2By= '$user_check' where orderid='$orderid'";
        if (!mysqli_query($conn,$updatesql)){
            $error ="Update Error : " . mysqli_error($conn);
            echo $error;
        } else {
            echo "success";
        }
        mysqli_close($conn);
    }
    exit;            
}
if ($flag == 'cp2Shuttle'){
    $centralPoint = substr($orderid,15,1)."0";
    $userCodeSQL = "select merchEmpCode from tbl_user_info where userName = '$user_check'";
    $userCodeResult = mysqli_query($conn, $userCodeSQL);
    $userCodeRow = mysqli_fetch_array($userCodeResult);
    $userCode = $userCodeRow['merchEmpCode'];
    $cpPermissionSQL = "select empCode from tbl_employee_point where empCode = '$userCode' and pointCode = '$centralPoint'";
    $cpPermissionResult = mysqli_query($conn, $cpPermissionSQL);
    if (mysqli_num_rows($cpPermissionResult) > 0) {
        $updatesql="update tbl_order_details set cp2Shuttle = 'Y', cp2ShuttleTime= NOW() + INTERVAL 6 HOUR, cp2ShuttleBy= '$user_check' where orderid='$orderid'";
        if (!mysqli_query($conn,$updatesql)){
            $error ="Update Error : " . mysqli_error($conn);
            echo $error;
        } else {
            echo "success";
        }
        mysqli_close($conn);
    }
    exit;            
}
if ($flag == 'retcp1'){
    $centralPoint = "A0";
    $userCodeSQL = "select merchEmpCode from tbl_user_info where userName = '$user_check'";
    $userCodeResult = mysqli_query($conn, $userCodeSQL);
    $userCodeRow = mysqli_fetch_array($userCodeResult);
    $userCode = $userCodeRow['merchEmpCode'];
    $cpPermissionSQL = "select empCode from tbl_employee_point where empCode = '$userCode' and pointCode = '$centralPoint'";
    $cpPermissionResult = mysqli_query($conn, $cpPermissionSQL);
    if (mysqli_num_rows($cpPermissionResult) > 0) {
        $updatesql="update tbl_order_details set retcp1 = 'Y', retcp1Time= NOW() + INTERVAL 6 HOUR, retcp1By= '$user_check' where orderid='$orderid'";
        if (!mysqli_query($conn,$updatesql)){
            $error ="Update Error : " . mysqli_error($conn);
            echo $error;
        } else {
            echo "success";
        }
        mysqli_close($conn);
    }
    exit;            
}
if ($flag == 'DP2'){
    $updatesql="update tbl_order_details set DP2 = 'Y', DP2Time= NOW() + INTERVAL 6 HOUR, DP2By= '$user_check' where orderid='$orderid'";
            //$result = mysqli_query($conn, $updatesql);
    if (!mysqli_query($conn,$updatesql)){
        $error ="Update Error : " . mysqli_error($conn);
        echo $error;
    } else {
        echo "success";
    }
    mysqli_close($conn);
    exit;            
}
if ($flag == 'assignDrop'){
    $updatesql="update tbl_order_details set dropPointEmp = '$pickPointEmp', dropAssignTime = NOW() + INTERVAL 6 HOUR, dropAssignBy = '$user_check' where orderid='$orderid'";
            //$result = mysqli_query($conn, $updatesql);
    if (!mysqli_query($conn,$updatesql)){
        $error ="Update Error : " . mysqli_error($conn);
        echo $error;
    } else {
        $merOrderRefRow = mysqli_fetch_array(mysqli_query($conn, "select tbl_order_details.merchantCode, tbl_merchant_info.merchantName, merOrderRef, dropPointEmp, tbl_employee_info.empName, tbl_employee_info.contactNumber, custphone from tbl_order_details left join tbl_employee_info on tbl_order_details.dropPointEmp = tbl_employee_info.empCode left join tbl_merchant_info on tbl_order_details.merchantCode = tbl_merchant_info.merchantCode where orderid='$orderid'"));
        $merOrderRef = $merOrderRefRow['merOrderRef'];
        $merchantCode = $merOrderRefRow['merchantCode'];
        $merchantName = $merOrderRefRow['merchantName'];
        $empName = $merOrderRefRow['empName'];
        $empContact = $merOrderRefRow['contactNumber'];
        $phone = substr($merOrderRefRow['custphone'], -10);

        if($merchantCode == 'M-1-0262' || $merchantCode == 'M-1-0411' || $merchantCode == 'M-1-0435' || $merchantCode == 'M-1-0441'){
            $url='http://api.rankstelecom.com/api/v3/sendsms/json';

            $ch=curl_init($url);

            $mobile = '880'.$phone;

            $message = 'Your order '.$merOrderRef.' from Ajker Deal is ready for delivery. You can contact '.$empName.', '.$empContact.' for more information. Paperfly.';



            $data=array(
                'authentication'=>array('username'=>'Paperfly','password'=>'TbikjHrN'),
                'messages'=>array(array('sender'=>'7777','text'=>$message,'recipients'=>array(array('gsm'=>$mobile))
            ))
            );
            $jsondataencode=json_encode($data);
            CURL_SETOPT($ch,CURLOPT_POST,1);
            CURL_SETOPT($ch,CURLOPT_POSTFIELDS,$jsondataencode);
            CURL_SETOPT($ch,CURLOPT_HTTPHEADER,array('content-type:application/json'));
            $result=CURL_EXEC($ch);
            curl_close($ch);                         
        } else {
            $url='http://api.rankstelecom.com/api/v3/sendsms/json';

            $ch=curl_init($url);

            $mobile = '880'.$phone;

            $message = 'Your order '.$merOrderRef.' from '.$merchantName.' is ready for delivery. Our team will contact you. Thank You! Paperfly';



            $data=array(
                'authentication'=>array('username'=>'Paperfly','password'=>'TbikjHrN'),
                'messages'=>array(array('sender'=>'7777','text'=>$message,'recipients'=>array(array('gsm'=>$mobile))
            ))
            );
            $jsondataencode=json_encode($data);
            CURL_SETOPT($ch,CURLOPT_POST,1);
            CURL_SETOPT($ch,CURLOPT_POSTFIELDS,$jsondataencode);
            CURL_SETOPT($ch,CURLOPT_HTTPHEADER,array('content-type:application/json'));
            $result=CURL_EXEC($ch);
            curl_close($ch);                    
        }
        $pointCodeRow = mysqli_fetch_array(mysqli_query($conn, "select dropPointCode, custphone from tbl_order_details where orderid = '$orderid'"));
        $dropPointCode = $pointCodeRow['dropPointCode'];
       /* if($dropPointCode == 'A1'|| $dropPointCode == 'A2' || $dropPointCode == 'A3' || $dropPointCode == 'A4'|| $dropPointCode == 'A5'|| $dropPointCode == 'A6'){
            $recipientsNo = $pointCodeRow['custphone'];
            $message = "Dear Customer, we also provide bKash account opening service - FREE! Please keep your NID copy and 1 passport size photos ready if interested. Paperfly.";
            $mobile = '880'.substr($recipientsNo, -10);            
            $curl = curl_init();
            $messageText = urlencode($message);
            curl_setopt_array($curl, array(
              CURLOPT_URL => "http://api.rankstelecom.com/api/v3/sendsms/plain?user=Paperfly&password=TbikjHrN&sender=7777&SMSText=$messageText&GSM=$mobile&type=longSMS",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "GET",
              CURLOPT_HTTPHEADER => array(
                "Cache-Control: no-cache",
                "Postman-Token: 071758b8-5817-4787-86df-a3623844fd90"
            ),
          ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            $xml=simplexml_load_string($response) or die("Error: Cannot create object");
            $status = (string)$xml->result->status;

            curl_close($curl);                    
        }*/                
    }
    mysqli_close($conn);
    exit;            
} 
if ($flag == 'PickDrop'){
    $updatesql="update tbl_order_details set PickDrop = 'Y', PickDropTime= NOW() + INTERVAL 6 HOUR, PickDropBy= '$user_check' where orderid='$orderid'";
            //$result = mysqli_query($conn, $updatesql);
    if (!mysqli_query($conn,$updatesql)){
        $error ="Update Error : " . mysqli_error($conn);
        echo $error;
    } else {
        echo "success";
        $merOrderRefRow = mysqli_fetch_array(mysqli_query($conn, "select merchantCode, merOrderRef from tbl_order_details where orderid='$orderid'"));
        $merOrderRef = '['.$merOrderRefRow['merOrderRef'].']';
        $merchantCode = $merOrderRefRow['merchantCode'];
        if($merchantCode == 'M-1-0262' || $merchantCode == 'M-1-0411' || $merchantCode == 'M-1-0435' || $merchantCode == 'M-1-0441'){
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => "http://bridge.ajkerdeal.com/ThirdPartyOrderAction/UpdateStatusByCourier",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => "\t{\r\n\t\t\"OrderIds\":".$merOrderRef.",\r\n\t\t\"StatusId\": 1011,\r\n    \t\"ThirdPartyId\":30,\r\n    \t\"Comments\":\"Last HUB\"\r\n\t}\r\n",
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
    mysqli_close($conn);
    exit;            
}
if ($flag == 'Cust'){
    $updatesql="update tbl_order_details set Cust = 'Y', CustTime= NOW() + INTERVAL 6 HOUR, CustBy= '$user_check' where orderid='$orderid'";
            //$result = mysqli_query($conn, $updatesql);
    if (!mysqli_query($conn,$updatesql)){
        $error ="Update Error : " . mysqli_error($conn);
        echo $error;
    } else {
        echo "success";
    }
    mysqli_close($conn);
    exit;            
}
if ($flag == 'Cash'){
    $CashAmt = trim($_POST['CashAmt']);
    $cashComment = trim($_POST['cashComment']);
    $cashComment = mysqli_real_escape_string($conn, $cashComment);
    $cashType = trim($_POST['cashType']);

    $cashOrgAmountRow = mysqli_fetch_array(mysqli_query($conn, "select packagePrice from tbl_order_details where orderid='$orderid'"));

    $cashOrgAmount = $cashOrgAmountRow['packagePrice'];

    if($CashAmt != $cashOrgAmount){
        if(!$cashComment){
            echo 'Error : Cash comment require';
            exit;                    
        } else {
            $updatesql="update tbl_order_details set CashAmt='$CashAmt', cashComment='$cashComment', cashType='$cashType', Cash = 'Y', CashTime= NOW() + INTERVAL 6 HOUR, CashBy= '$user_check' where orderid='$orderid'";
                    //$result = mysqli_query($conn, $updatesql);
            if (!mysqli_query($conn,$updatesql)){
                $error ="Update Error : " . mysqli_error($conn);
                echo $error;
            } else {
                echo "success";
            }

            $merOrderRefRow = mysqli_fetch_array(mysqli_query($conn, "select merchantCode, merOrderRef,demo from tbl_order_details where orderid='$orderid'"));
            $merOrderRef = '['.$merOrderRefRow['merOrderRef'].']';
            $merchantCode = $merOrderRefRow['merchantCode'];
            $merOrderRef1 = $merOrderRefRow['merOrderRef'];
            $merchantType = $merOrderRefRow['demo'];

            if($merchantCode == 'M-1-0262' || $merchantCode == 'M-1-0411' || $merchantCode == 'M-1-0435' || $merchantCode == 'M-1-0441'){
                $curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => "http://bridge.ajkerdeal.com/ThirdPartyOrderAction/UpdateStatusByCourier",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 30,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "POST",
                  CURLOPT_POSTFIELDS => "\t{\r\n\t\t\"OrderIds\":".$merOrderRef.",\r\n\t\t\"StatusId\": 9,\r\n    \t\"ThirdPartyId\":30,\r\n    \t\"Comments\":\"Delivered\"\r\n\t}\r\n",
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
            if($merchantCode == 'M-1-0484' AND $merchantType='robishop'){
                $curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => "https://robishop.com.bd/rest/V1/integration/admin/token",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 60,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "POST",
                  CURLOPT_POSTFIELDS => "{\n\t\"username\" : \"paperfly\",\n\t\"password\" : \"PaperFly123@\"\n}",
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
           /* $merOrderRefRow = mysqli_fetch_array(mysqli_query($conn, "select merchantCode, merOrderRef from tbl_order_details where orderid='$orderid'"));
            $merOrderRef = $merOrderRefRow['merOrderRef'];
            $merchantCode = $merOrderRefRow['merchantCode'];*/
            
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://robishop.com.bd/rest/V1/deliverypartner",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 60,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => "{\n\t\"order_id\" : \"".$merOrderRef1."\",\n\t\"status\" : \"complete\",\n\t\"comment\" : \"The order has been delivered\"\n}",
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

   if($merchantCode == 'M-1-0484' AND $merchantType='digired'){
                $curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => "https://digired.shop/rest/V1/integration/admin/token",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 60,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "POST",
                  CURLOPT_POSTFIELDS => "{\n\t\"username\" : \"paperfly\",\n\t\"password\" : \"PaperFly123@\"\n}",
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
           /* $merOrderRefRow = mysqli_fetch_array(mysqli_query($conn, "select merchantCode, merOrderRef from tbl_order_details where orderid='$orderid'"));
            $merOrderRef = $merOrderRefRow['merOrderRef'];
            $merchantCode = $merOrderRefRow['merchantCode'];*/
            
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://digired.shop/rest/V1/deliverypartner",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 60,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => "{\n\t\"order_id\" : \"".$merOrderRef1."\",\n\t\"status\" : \"complete\",\n\t\"comment\" : \"The order has been delivered\"\n}",
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



  mysqli_close($conn);
  exit;                    
}
} else {
    $updatesql="update tbl_order_details set CashAmt='$CashAmt', cashComment='$cashComment', cashType='$cashType', Cash = 'Y', CashTime= NOW() + INTERVAL 6 HOUR, CashBy= '$user_check' where orderid='$orderid'";
                //$result = mysqli_query($conn, $updatesql);
    if (!mysqli_query($conn,$updatesql)){
        $error ="Update Error : " . mysqli_error($conn);
        echo $error;
    } else {
        echo "success";
    }

    $merOrderRefRow = mysqli_fetch_array(mysqli_query($conn, "select merchantCode, merOrderRef,demo from tbl_order_details where orderid='$orderid'"));
    $merOrderRef = '['.$merOrderRefRow['merOrderRef'].']';
    $merchantCode = $merOrderRefRow['merchantCode'];
    $merOrderRef1 = $merOrderRefRow['merOrderRef'];
    $merchantType = $merOrderRefRow['demo'];

    if($merchantCode == 'M-1-0262' || $merchantCode == 'M-1-0411' || $merchantCode == 'M-1-0435' || $merchantCode == 'M-1-0441'){
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "http://bridge.ajkerdeal.com/ThirdPartyOrderAction/UpdateStatusByCourier",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => "\t{\r\n\t\t\"OrderIds\":".$merOrderRef.",\r\n\t\t\"StatusId\": 9,\r\n    \t\"ThirdPartyId\":30,\r\n    \t\"Comments\":\"Delivered\"\r\n\t}\r\n",
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
    if($merchantCode == 'M-1-0484' AND $merchantType == 'robishop'){
      $curl = curl_init();

      curl_setopt_array($curl, array(
          CURLOPT_URL => "https://robishop.com.bd/rest/V1/integration/admin/token",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => "{\n\t\"username\" : \"paperfly\",\n\t\"password\" : \"PaperFly123@\"\n}",
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
            /*$merOrderRefRow = mysqli_fetch_array(mysqli_query($conn, "select merchantCode, merOrderRef from tbl_order_details where orderid='$orderid'"));
            $merOrderRef = $merOrderRefRow['merOrderRef'];
            $merchantCode = $merOrderRefRow['merchantCode'];*/
            
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://robishop.com.bd/rest/V1/deliverypartner",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => "{\n\t\"order_id\" : \"".$merOrderRef1."\",\n\t\"status\" : \"complete\",\n\t\"comment\" : \"The order has been delivered\"\n}",
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

   if($merchantCode == 'M-1-0484' AND $merchantType == 'digired'){
      $curl = curl_init();

      curl_setopt_array($curl, array(
          CURLOPT_URL => "https://digired.shop/rest/V1/integration/admin/token",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => "{\n\t\"username\" : \"paperfly\",\n\t\"password\" : \"PaperFly123@\"\n}",
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
            /*$merOrderRefRow = mysqli_fetch_array(mysqli_query($conn, "select merchantCode, merOrderRef from tbl_order_details where orderid='$orderid'"));
            $merOrderRef = $merOrderRefRow['merOrderRef'];
            $merchantCode = $merOrderRefRow['merchantCode'];*/
            
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://digired.shop/rest/V1/deliverypartner",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => "{\n\t\"order_id\" : \"".$merOrderRef1."\",\n\t\"status\" : \"complete\",\n\t\"comment\" : \"The order has been delivered\"\n}",
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

  mysqli_close($conn);
  exit;                
}
} 
if ($flag == 'partial'){
    $partialReceive = trim($_POST['deliveredQty']);
    $partialReturn = trim($_POST['returnedQty']);
    $CashAmt = trim($_POST['partialAmt']);
    $partialReason = trim($_POST['partialReason']);
    $partialReason = mysqli_real_escape_string($conn, $partialReason);
    $cashType = trim($_POST['cashType']);
    $updatesql="update tbl_order_details set partialReceive='$partialReceive', partialReturn='$partialReturn', CashAmt='$CashAmt', partialReason='$partialReason', cashType='$cashType', partial = 'Y', partialTime= NOW() + INTERVAL 6 HOUR, partialBy= '$user_check' where orderid='$orderid'";
            //$result = mysqli_query($conn, $updatesql);
    if (!mysqli_query($conn,$updatesql)){
        $error ="Update Error : " . mysqli_error($conn);
        echo $error;
    } else {
        echo "success";
        $merOrderRefRow = mysqli_fetch_array(mysqli_query($conn, "select merchantCode, merOrderRef,demo from tbl_order_details where orderid='$orderid'"));
        $merOrderRef = $merOrderRefRow['merOrderRef'];
        $merchantCode = $merOrderRefRow['merchantCode'];
        $merchantType = $merOrderRefRow['demo'];
        if($merchantCode == 'M-1-0484' AND $merchantType == 'robishop'){
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://robishop.com.bd/rest/V1/integration/admin/token",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => "{\n\t\"username\" : \"paperfly\",\n\t\"password\" : \"PaperFly123@\"\n}",
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
            
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://robishop.com.bd/rest/V1/deliverypartner",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => "{\n\t\"order_id\" : \"".$merOrderRef."\",\n\t\"status\" : \"complete\",\n\t\"comment\" : \"The order has been delivered\"\n}",
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

  if($merchantCode == 'M-1-0484' AND $merchantType == 'digired'){
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://digired.shop/rest/V1/integration/admin/token",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => "{\n\t\"username\" : \"paperfly\",\n\t\"password\" : \"PaperFly123@\"\n}",
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
            
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://digired.shop/rest/V1/deliverypartner",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => "{\n\t\"order_id\" : \"".$merOrderRef."\",\n\t\"status\" : \"complete\",\n\t\"comment\" : \"The order has been delivered\"\n}",
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
mysqli_close($conn);
exit;            
} 
if ($flag == 'DropDP2'){
    $updatesql="update tbl_order_details set DropDP2 = 'Y', DropDP2Time= NOW() + INTERVAL 6 HOUR, DropDP2By= '$user_check' where orderid='$orderid'";
            //$result = mysqli_query($conn, $updatesql);
    if (!mysqli_query($conn,$updatesql)){
        $error ="Update Error : " . mysqli_error($conn);
        echo $error;
    } else {
        echo "success";

    }
    mysqli_close($conn);
    exit;            
}
if ($flag == 'bank'){
    $updatesql="update tbl_order_details set bank = 'Y', bankTime= NOW() + INTERVAL 6 HOUR, bankBy= '$user_check' where orderid='$orderid'";
            //$result = mysqli_query($conn, $updatesql);
    if (!mysqli_query($conn,$updatesql)){
        $error ="Update Error : " . mysqli_error($conn);
        echo $error;
    } else {
        echo "success";
    }
    mysqli_close($conn);
    exit;            
}
if ($flag == 'close'){
    $updatesql="update tbl_order_details set close = 'Y', closeTime= NOW() + INTERVAL 6 HOUR, closeBy= '$user_check' where orderid='$orderid'";
            //$result = mysqli_query($conn, $updatesql);
    if (!mysqli_query($conn,$updatesql)){
        $error ="Update Error : " . mysqli_error($conn);
        echo $error;
    } else {
        echo "success";
    }
    mysqli_close($conn);
    exit;            
}
if ($flag == 'accclose'){
    $accRem = $_POST['accRem'];
    $accRem = mysqli_real_escape_string($conn, $accRem);
    $updatesql="update tbl_order_details set close = 'Y', accRem = '$accRem', closeTime= NOW() + INTERVAL 6 HOUR, closeBy= '$user_check' where orderid='$orderid'";
            //$result = mysqli_query($conn, $updatesql);
    if (!mysqli_query($conn,$updatesql)){
        $error ="Update Error : " . mysqli_error($conn);
        echo $error;
    } else {
        echo "success";
    }
    mysqli_close($conn);
    exit;            
}
if ($flag == 'Ret'){
    $retReason = $_POST['retReason'];
    $retRem = $_POST['retRemarks'];
    $merOrderRefRow = mysqli_fetch_array(mysqli_query($conn, "select merchantCode, merOrderRef, ratechartId,demo from tbl_order_details where orderid='$orderid'"));
    $ratechartID = $merOrderRefRow['ratechartId'];
    $merchantCode = $merOrderRefRow['merchantCode'];

    $merchantInfoRow = mysqli_fetch_array(mysqli_query($conn, "select * from tbl_merchant_info where merchantCode = '$merchantCode'"));
    $returnExtra = $merchantInfoRow['returnExtra'];

    $returnExtraCharge = (1 + ($returnExtra/100));

    if($ratechartID == 23){
        $updatesql="update tbl_order_details set retRem='$retRem', Ret = 'Y', retReason='$retReason', charge = charge * $returnExtraCharge, RetTime= NOW() + INTERVAL 6 HOUR, RetBy= '$user_check' where orderid='$orderid'";
    } else {
        $updatesql="update tbl_order_details set retRem='$retRem', Ret = 'Y', retReason='$retReason', RetTime= NOW() + INTERVAL 6 HOUR, RetBy= '$user_check' where orderid='$orderid'";
    }

            //$result = mysqli_query($conn, $updatesql);
    if (!mysqli_query($conn,$updatesql)){
        $error ="Update Error : " . mysqli_error($conn);
        echo $error;
    } else {
        echo "success";

        $merOrderRef = '['.$merOrderRefRow['merOrderRef'].']';
        $merOrderRef1 = $merOrderRefRow['merOrderRef'];
        $merchantType = $merOrderRefRow['demo'];
        $returnReason = $retRem.' | '.$retReason;
        if($merchantCode == 'M-1-0262' || $merchantCode == 'M-1-0411' || $merchantCode == 'M-1-0435' || $merchantCode == 'M-1-0441'){
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => "http://bridge.ajkerdeal.com/ThirdPartyOrderAction/UpdateStatusByCourier",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => "\t{\r\n\t\t\"OrderIds\":".$merOrderRef.",\r\n\t\t\"StatusId\": 1010,\r\n    \t\"ThirdPartyId\":30,\r\n    \t\"Comments\":\"".$returnReason."\"\r\n\t}\r\n",
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
        if($merchantCode == 'M-1-0484' AND $merchantType == 'robishop'){
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://robishop.com.bd/rest/V1/integration/admin/token",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => "{\n\t\"username\" : \"paperfly\",\n\t\"password\" : \"PaperFly123@\"\n}",
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
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://robishop.com.bd/rest/V1/deliverypartner",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => "{\n\t\"order_id\" : \"".$merOrderRef1."\",\n\t\"status\" : \"closed\",\n\t\"comment\" : \"".$returnReason."\"\n}",
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
   if($merchantCode == 'M-1-0484' AND $merchantType == 'digired'){
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://digired.shop/rest/V1/integration/admin/token",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => "{\n\t\"username\" : \"paperfly\",\n\t\"password\" : \"PaperFly123@\"\n}",
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
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://digired.shop/rest/V1/deliverypartner",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => "{\n\t\"order_id\" : \"".$merOrderRef1."\",\n\t\"status\" : \"closed\",\n\t\"comment\" : \"".$returnReason."\"\n}",
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
mysqli_close($conn);
exit;            
}
if ($flag == 'NoShow'){
    $updatesql="update tbl_order_details set NoShow = 'Y', NoShowTime= NOW() + INTERVAL 6 HOUR, NoShowBy= '$user_check' where orderid='$orderid'";
            //$result = mysqli_query($conn, $updatesql);
    if (!mysqli_query($conn,$updatesql)){
        $error ="Update Error : " . mysqli_error($conn);
        echo $error;
    } else {
        echo "success";
    }
    mysqli_close($conn);
    exit;            
}
if ($flag == 'Hold'){
    $onHoldSchedule = trim($_POST['onHoldSchedule']);
    $updatesql="update tbl_order_details set onHoldSchedule='$onHoldSchedule', Rea = 'Y', ReaTime= NOW() + INTERVAL 6 HOUR, ReaBy= '$user_check' where orderid='$orderid'";
            //$result = mysqli_query($conn, $updatesql);
    if (!mysqli_query($conn,$updatesql)){
        $error ="Update Error : " . mysqli_error($conn);
        echo $error;
    } else {
        echo "success";
      
}
mysqli_close($conn);
exit;            
}
if ($flag == 'checkOnSchedule'){
    $onHoldDateSQL = "select onHoldSchedule from tbl_order_details where orderid = '$orderid'";
    $onHoldDateResult = mysqli_query($conn, $onHoldDateSQL);
    $onHoldDateRow = mysqli_fetch_array($onHoldDateResult);
    if($onHoldDateRow['onHoldSchedule'] !=''){
        echo 'scheduled';
    } else {
        echo 'unscheduled';
    }
    mysqli_close($conn);
    exit;
}
if($flag == 'requestForOnHoldDate'){
    $onHoldDateSQL = "select DATE_FORMAT(onHoldSchedule, '%d-%m-%Y') as onHoldSchedule from tbl_order_details where orderid = '$orderid'";
    $onHoldDateResult = mysqli_query($conn, $onHoldDateSQL);
    $onHoldDateRow = mysqli_fetch_array($onHoldDateResult);
            //echo '<option value="'.$onHoldDateRow['onHoldSchedule'].'">'.$onHoldDateRow['onHoldScheduleshow'].'</option>';
    echo $onHoldDateRow['onHoldSchedule'];
    mysqli_close($conn);
    exit;            
}
if($flag == 'requestForOnHoldReason'){
    $onHoldReasonSQL = "select onHoldReason  from tbl_order_details where orderid = '$orderid'";
    $onHoldReasonResult = mysqli_query($conn, $onHoldReasonSQL);
    $onHoldReasonRow = mysqli_fetch_array($onHoldReasonResult);
    if($onHoldReasonRow['onHoldReason'] == 'Customer unreachable'){
        echo '<option value="Customer unreachable" selected>Customer unreachable</option>';    
        echo '<option value="Customer request">Customer request</option>';    
    } else {
        echo '<option value="Customer unreachable">Customer unreachable</option>';    
        echo '<option value="Customer request" selected>Customer request</option>';                    
    }

    mysqli_close($conn);
    exit;              
}
if($flag == 'requestForOnHoldSchedule'){
    $date1 = new DateTime('+1 day');
    echo '<option value="'.$date1->format('Y-m-d').'">'.$date1->format('d-M-Y').'</option>';
    $date2 = new DateTime('+2 day');
    echo '<option value="'.$date2->format('Y-m-d').'">'.$date2->format('d-M-Y').'</option>';
    $date3 = new DateTime('+3 day');
    echo '<option value="'.$date3->format('Y-m-d').'">'.$date3->format('d-M-Y').'</option>';
    $date4 = new DateTime('+4 day');
    echo '<option value="'.$date4->format('Y-m-d').'">'.$date4->format('d-M-Y').'</option>';
    $date5 = new DateTime('+5 day');
    echo '<option value="'.$date5->format('Y-m-d').'">'.$date5->format('d-M-Y').'</option>';
    $date6 = new DateTime('+6 day');
    echo '<option value="'.$date6->format('Y-m-d').'">'.$date6->format('d-M-Y').'</option>';
    $date7 = new DateTime('+7 day');
    echo '<option value="'.$date7->format('Y-m-d').'">'.$date7->format('d-M-Y').'</option>';
}
if($flag == 'updateOnHold'){
    $onHoldDate = strtotime($_POST['onHoldDate']);
    $onHoldDate = date("Y-m-d", $onHoldDate);
    $onReason = $_POST['onReason'];
    $updateOnHoldSQL = "update tbl_order_details set Rea = 'Y', ReaTime = NOW() + INTERVAL 6 HOUR, ReaBy = '$user_check', onHoldSchedule = '$onHoldDate', onHoldReason = '$onReason' where orderid = '$orderid'";
    if(!mysqli_query($conn, $updateOnHoldSQL)){
        echo 'Error unable to update onHold Status'.mysqli_error($conn);
    } else {
        echo $orderid.' on hold';
          $merOrderRefRow = mysqli_fetch_array(mysqli_query($conn, "select merchantCode, merOrderRef,demo from tbl_order_details where orderid='$orderid'"));
        $merOrderRef = $merOrderRefRow['merOrderRef'];
        $merchantCode = $merOrderRefRow['merchantCode'];
        $merchantType = $merOrderRefRow['demo'];
        if($merchantCode == 'M-1-0484' AND $merchantType == 'robishop'){
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://robishop.com.bd/rest/V1/integration/admin/token",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => "{\n\t\"username\" : \"paperfly\",\n\t\"password\" : \"PaperFly123@\"\n}",
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
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://robishop.com.bd/rest/V1/deliverypartner",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => "{\n\t\"order_id\" : \"".$merOrderRef."\",\n\t\"status\" : \"holded\",\n\t\"comment\" : \"The order is on hold\"\n}",
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
    if($merchantCode == 'M-1-0484' AND $merchantType == 'digired'){
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://digired.shop/rest/V1/integration/admin/token",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => "{\n\t\"username\" : \"paperfly\",\n\t\"password\" : \"PaperFly123@\"\n}",
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
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://digired.shop/rest/V1/deliverypartner",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => "{\n\t\"order_id\" : \"".$merOrderRef."\",\n\t\"status\" : \"holded\",\n\t\"comment\" : \"The order is on hold\"\n}",
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
    mysqli_close($conn);
    exit;
}
if($flag == 'onHoldMessage'){
    $onHoldInfoSQL = "select DATE_FORMAT(onHoldSchedule, '%d-%M-%Y') as onHoldSchedule, onHoldReason from tbl_order_details where orderid = '$orderid'";
    $onHoldInfoResult = mysqli_query($conn, $onHoldInfoSQL);
    $onHoldInfoRow = mysqli_fetch_array($onHoldInfoResult);

    echo 'On Hold Date : '.$onHoldInfoRow['onHoldSchedule'].'  On Hold Reason : '.$onHoldInfoRow['onHoldReason'];

    mysqli_close($conn);
    exit;
}



}
?>