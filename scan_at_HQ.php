<?php
    
    include('header.php');
    include('config.php');
    $merchantsql = "select merchantCode, merchantName from tbl_merchant_info";
    $merchantresult = mysqli_query($conn,$merchantsql);

    //$merchantrow = mysqli_fetch_array($merchantresult);
    $employeesql = "SELECT userName as usrname, userType as type, tbl_employee_info.empName, userRole from tbl_user_info left join tbl_employee_info on tbl_user_info.merchEmpCode = tbl_employee_info.empCode";

    $employeeresult = mysqli_query($conn,$employeesql);
    //$employeerow = mysqli_fetch_array($employeeresult);
   

?>


        <div style="margin-left: 15px; width: 98%; clear: both">
            <p style="background-color: #16469E; border-radius: 5px; width: 100%; height: 25px; color: #fff; font: 15px 'paperfly roman'">Scan to Pick Orders</p>
            <div class="container-fluid" style="margin-left: 15px; font: 15px 'paperfly roman'">
                <div class="col-sm-6">
                    <div class="row">
                    <div class="col-sm-6">
                    <label for="merchants" id="merchantslabel">Merchant Name</label>  
                           <select class="js-example-basic-single" id="merchants" name="merchants" style="width: 100%" required>
                                <option></option>

                                 <?php 
                                    while($row = mysqli_fetch_array($merchantresult))
                                    {       
                                          echo "<option value=\"".$row["merchantCode"]."\"";
                                          if($_POST['merchants'] == $row['merchantCode'])
                                                echo 'selected';
                                          echo ">".$row["merchantName"]."</option>";        
                                    }  
                                  ?>  
                               
                            </select>      
                          </div>
                          </div>
                   <div class="row">
                    <div class="col-sm-6">
                    <label for="employees" id="employeeslabel">Employee Name</label>  
                            <select class="js-example-basic-single-1" id="employees"  name="employees" style="width: 100%" required>
                                <option></option>
                                 <?php 
                                    while($row = mysqli_fetch_array($employeeresult))
                                    {       
                                          echo "<option value=\"".$row["usrname"]."\"";
                                          if($_POST['employees'] == $row['usrname'])
                                                echo 'selected';
                                          echo ">".$row["usrname"]."</option>";        
                                    }  
                                  ?>  
                            </select>  
                          </div>
                          </div>
                        
                    <div class="row">
                        <div class="col-sm-6">
                            <label><b><u>Scan Barcode</u></b></label>
                            <input type="text" id="pickedBarcode" class="form-control input-sm" onchange="validateBarcode() " autofocus>
                            <input type="hidden" id="pickedBarcodeVal">
                        </div>
                    </div>
                    <div class="row" id="pickScanResult1" hidden>
                        <div class="col-sm-4">
                            <label style="font-weight: 800">Merchant Name</label>
                            <input type="hidden" id="merchantCode">
                        </div>
                        <div class="col-sm-8">
                            <label id="pickedMerchant1"></label>
                        </div>
                    </div>
                    <div class="row" id="pickScanResult2" hidden>
                        <div class="col-sm-4">
                            <label style="font-weight: 800">Picked By</label>
                        </div>
                        <div class="col-sm-8">
                            <label id="pickedBy1"></label>
                        </div>
                    </div>
                    <div class="row" id="pickScanResult3" hidden>
                        <div class="col-sm-4">
                            <label style="font-weight: 800">Barcode</label>
                        </div>
                        <div class="col-sm-8">
                            <label style="color: blue; font-weight: bold;" id="barcodeonscreen"></label>
                        </div>
                    </div>
                     <div class="row" id="pickScanResult3" hidden>
                        <div class="col-sm-4">
                            <label style="font-weight: 800">MerchantCode</label>
                        </div>
                        <div class="col-sm-8">
                            <label id="m_code"></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <p id="barcodeAlrt"></p>
                        </div>
                    </div>
                </div>
                 
                    
            </div>

        </div>
        <!--Modal -->
        <!-- The Modal for smart Pick input and message -->
       <div  id="add_data_Modal" class="modal" style="width: auto">
           <!-- Modal content -->

           <div id="modal-content"  class="modal-content modal-dialog" style="width: 25%">
               <div class="modal-header" style="height: 60px; background-color: #16469E">
                   <span class="close">&times;</span>
                   <h3 style="font: 25px 'paperfly roman';text-align:center;" id="headerID">Create New</h3>
               </div>
               <div class="modal-body" style="text-align: left;">
                    <form method="post" id="insert_form">  

                      <div class="row" style="margin-top: 15px !important;">
                       
                        <div class="col-md-6" style="text-align: center; font-weight: bold;">
                            Logistics&nbsp;<input id="op1" type="radio" name="orderType" style="margin-top: -2px" value="Merchant" onclick="return showHide()" checked>
                        </div>
                      <div class="col-md-6" style="text-align: center; font-weight: bold;">
                            Fullfillment&nbsp;<input id="op2" type="radio" style="margin-top: -2px" name="orderType" value="Other_Merchant" onclick="return showHide()">
                        </div>
                      </div>
                      &nbsp;
                       <p class="animated infinite pulse delay-2s slow" style="font-weight: bold;text-align: center;" id="heading"></p>
                          <label for="barcode" id="barcodelabel">Barcode Number</label>  
                          <input type="text" name="barcode" id="barcode" class="form-control" readonly/>  
                          <br />  
                          <input type="hidden" name="flag" id="flag" value="insertbarcode" /> 
                          <input type="hidden" name="get_orderid" id="get_orderid" value="demo" /> 
                          <input type="hidden" name="fl" id="fl" value="demo">

                           <label for="merchants" id="merchantslabel">Merchant Name</label>  
                           <select class="js-example-basic-single" id="merchants" name="merchants" style="width: 100%" required>
                                <option></option>

                                 <?php 
                                    while($row = mysqli_fetch_array($merchantresult))
                                    {       
                                          echo "<option value=\"".$row["merchantCode"]."\"";
                                          if($_POST['merchants'] == $row['merchantCode'])
                                                echo 'selected';
                                          echo ">".$row["merchantName"]."</option>";        
                                    }  
                                  ?>  
                               
                            </select>                           
                             <br/> 
                             <br>
                             <label for="employees" id="employeeslabel">Employee Name</label>  
                            <select class="js-example-basic-single-1" id="employees"  name="employees" style="width: 100%" required>
                                <option></option>
                                 <?php 
                                    while($row = mysqli_fetch_array($employeeresult))
                                    {       
                                          echo "<option value=\"".$row["usrname"]."\"";
                                          if($_POST['employees'] == $row['usrname'])
                                                echo 'selected';
                                          echo ">".$row["usrname"]."</option>";        
                                    }  
                                  ?>  
                            </select>  
                           
                            <br>
                            <br>
                          <input autofocus style="background-color: #16469E"  type="submit" name="insert" id="insert" value="Insert" class="btn btn-success" />  
                     </form>  
               </div>
               
           </div>
       </div> 
        <div class="row">
            <div class="col-sm-6">
            </div>
            <div class="col-sm-6" id="barcodeDiv" hidden>
                <!--<div class="row" style="margin-top: 15px" >
                    <div class="col-sm-12">-->
                        <br>
                        <iframe id="barcodeView" src="" style="width: 100%; height:450px"></iframe>
                    <!--</div>
                </div>-->
            </div>
        </div>
        <div style="float: right;" id="validationResult">
        </div>
        <br>
        <br>
        <div class="container">
          <div  style="margin-top: -137px !important; padding-left: 73px !important; float: right;margin-top: -80px;" class="col-sm-6">
                    <div class="row" id="tpick" hidden>
                        <div class="col-sm-4">
                            <label style="font-size: 19px !important; color: green;">Total Picked</label>
                            <input type="hidden" id="totalpicked">
                        </div>
                        <div class="col-sm-8">
                            <label style="font-size: 50px; color: green;" id="totalpicked1"></label>
                        </div>
                    </div>
                </div>
        <div id="validationResult2" style="float: right; margin-top: -5%;height: 100%;"class="col-sm-6 ">
        </div>
    </div>

        <!-- <button id="myBtn">Open Modal</button> -->
        <!-- The Modal for Update Records -->
        
        <script type='text/javascript'>
            $('#pickedBarcode').focus();
             document.getElementById("heading").innerHTML = "Logistic Orders";
             document.getElementById("fl").value = "l";
             document.getElementById("heading").style.color ="green";

                $(document).ready(function() {
                $('.js-example-basic-single').select2();
                $('.js-example-basic-single-1').select2();
                
                 

                });

            
           
            function validateBarcode()
            {   if($('#merchants').val()=='' || $('#employees').val()=='' )
                      { alert("please select employee and merchant");
                       document.getElementById('pickedBarcode').value = '';  
                      }
                      else{
                if ($('#pickedBarcode').val().length >= 11)
                { 
                    var barcodeVal = $('#pickedBarcode').val();
                    var merchantCode = $('#merchants').val();
                    var employeeCode = $('#employees').val();
                   // alert(employeeCode);
                    var barcodeString = barcodeVal.trim().substring(0, 11);
                    $('#mobileSearchAlrt').css('color', 'green');
                    $('#mobileSearchAlrt').html('Please wait.......');
                    $.ajax({
                        type: 'post',
                        url: 'orders_update1.php',
                        data: {
                            get_orderid: barcodeString,
                            merchantCode : merchantCode,
                            employeeCode : employeeCode,
                            flagreq: 'validateBarcode2'
                        },
                        success: function (response)
                        {
                            //alert(response);
                            var str = response;
                            var n = str.search("Error");


                            if (n < 0)
                            {
                                $('#validationResult').html('');
                                $('#validationResult').append(response);
                                 $('#pickScanResult1').prop('hidden', false);
                                $('#pickScanResult2').prop('hidden', false);
                                $('#pickScanResult3').prop('hidden', false);
                                $('#mobileSearch').prop('hidden', false);
                                $('#pickedBarcodeVal').val(barcodeVal);
                                m_code = $("#m_code").text();
                                

                               
                                
                                  var res = str.search("logistics");
                                  var res1 = str.search("fulfillment");
                                  if(res>0)
                                  {
                                    table=1;//table 1 means barcode factory
                                  }
                                  if(res1>0)
                                  {
                                    table=2;//table 2 means fulfillment barcode
                                  }
                               
                                  $.ajax({
                                    type: "post",
                                    url: "orders_update1.php",
                                    data: {
                                      merchantCode : m_code,
                                      table : table,
                                      get_orderid: barcodeString,
                                      flagreq: 'update_barcode_factory'
                                  },
                                    success: function (response2) {
                                       //$.notify("Product records created successfully", "success"); 
                                        $("#tpick").prop("hidden", false);
                                        $('#validationResult2').html('');
                                        $('#validationResult2').append(response2);  
                                        document.getElementById('pickedBarcode').value = ''; 
                                    }
                                });
                               
                              
                               /* $('#pickScanResult1').prop('hidden', false);
                                $('#pickScanResult2').prop('hidden', false);*/
                              

                            } else
                            {
                                $('#barcode').val(barcodeVal);  
                                /*$('#barcodeAlrt').css('color', 'red');
                                $('#barcodeAlrt').html(response);
                                $('#pickScanResult1').prop('hidden', true);
                                $('#pickScanResult2').prop('hidden', true);
                                $('#mobileSearch').prop('hidden', true);
                                $('#pickedBarcodeVal').val('');
                                $('#mobileSearchAlrt').html('');
                                setTimeout(function () { $('#barcodeAlrt').html(''); }, 5000);*/
                               // var a= document.getElementById("add_data_Modal");
                               // a.style.display = 'block';
                               /* $("#barcode").prop( "disabled", true );
                                $("#merchants").prop( "disabled", true );
                                $("#employees").prop( "disabled", true );*/
                               // document.getElementById("insert").focus();
                                
                            }
                        }
                    })
                } else
                {
                    /*$('#barcodeAlrt').css('color', 'red');
                    $('#barcodeAlrt').html('Error: no such pick up record found');
                    $('#pickScanResult1').prop('hidden', true);
                    $('#pickScanResult2').prop('hidden', true);
                    $('#mobileSearch').prop('hidden', true);
                    $('#pickedBarcodeVal').val('');
                    $('#mobileSearchAlrt').html('');
                    setTimeout(function () { $('#barcodeAlrt').html(''); }, 5000);*/
                }
              }
                /*$('#mobileSearch1').prop('hidden', true);
                $('#mobileSearch2').prop('hidden', true);
                $('#mobileSearch3').prop('hidden', true);
                $('#mobileSearch4').prop('hidden', true);
                $('#mobileSearch5').prop('hidden', true);
                $('#mobileSearch6').prop('hidden', true);
                $('#mobileSearch7').prop('hidden', true);
                $('#mobileSearch8').prop('hidden', true);
                $('#acceptEdit').prop('hidden', true);
                $('#barcodeDiv').attr('hidden', true);
                $('#mobileSearchAlrt').html('');*/
            }

            
           
       function showHide(){

           if (document.getElementById("op1").checked == true)
                  {
                    document.getElementById("fl").value = "l";
                     $("#barcode").prop( "disabled", false );
                    document.getElementById("heading").innerHTML = "Logistics Orders";
                    document.getElementById("heading").style.color ="green";
                     $("#merchants").prop( "disabled", false );
                     $("#employees").prop( "disabled", false );
                  }
            if (document.getElementById("op2").checked == true)
                  {
                     document.getElementById("fl").value = "f";
                    document.getElementById("heading").innerHTML = "Fullfillment Orders";
                    document.getElementById("heading").style.color ="blue";
                     $("#barcode").prop( "disabled", false );
                     $("#merchants").prop( "disabled", false );
                     $("#employees").prop( "disabled", false );
                  }
                 } 
        </script>
     </body>
</html>
