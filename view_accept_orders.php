<?php
    include('header.php');
    $userPrivCheckRow = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `tbl_menu_ordermgt` WHERE user_id = $user_id_chk and pickOrders = 'Y'"));
    if ($userPrivCheckRow['pickOrders'] != 'Y'){
        exit();
    }
    $districtsql = "select districtId, districtName from tbl_district_info where districtId in (select distinct districtId from tbl_thana_info)";
    $districtresult = mysqli_query($conn,$districtsql);

    $thanaResult = mysqli_query($conn, "select * from tbl_thana_info");
?>
        <div style="margin-left: 15px; width: 98%; clear: both">
            <p style="background-color: #16469E; border-radius: 5px; width: 100%; height: 25px; color: #fff; font: 15px 'paperfly roman'">Scan to Pick Orders</p>
            <div class="container-fluid" style="margin-left: 15px; font: 15px 'paperfly roman'">
                <div class="col-sm-6">
                    <div class="row">
                        <div class="col-sm-6">
                            <label><b><u>Scan Barcode</u></b></label>
                            <input type="text" id="pickedBarcode" class="form-control input-sm" onchange="validateBarcode()">
                            <input type="hidden" id="pickedBarcodeVal">
                        </div>
                    </div>
                    <div class="row" id="pickScanResult1" hidden>
                        <div class="col-sm-4">
                            <label style="font-weight: 800">Merchant Name</label>
                            <input type="hidden" id="merchantCode">
                        </div>
                        <div class="col-sm-8">
                            <label id="pickedMerchant"></label>
                        </div>
                    </div>
                    <div class="row" id="pickScanResult2" hidden>
                        <div class="col-sm-4">
                            <label style="font-weight: 800">Picked By</label>
                        </div>
                        <div class="col-sm-8">
                            <label id="pickedBy"></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <p id="barcodeAlrt"></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12" id="barcodeDiv" hidden>
                            <!--<div class="row" style="margin-top: 15px" >
                                <div class="col-sm-12">-->
                                    <br>
                                    <iframe id="barcodeView" src="" style="width: 100%; height:450px"></iframe>
                                <!--</div>
                            </div>-->
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="row">
                        <div class="col-sm-6" id="mobileSearch" hidden>
                            <div class="row">
                                <div class="col-sm-6">
                                    <input type="radio" name="searchOption" value="mobNo" checked> By Mobile No
                                </div>
                                <div class="col-sm-6">
                                    <input type="radio" name="searchOption" value="merRef"> By Merchant ID
                                </div>
                            </div>
                            <hr>
                            <label><b><u>Search By Mobile No (Last 3 Digits Only)</u></b></label>
                            <input type="text" id="searchMobileNo" class="form-control input-sm" autocomplete="off">
                        </div>
                    </div>
                    <div class="row" id="mobileSearch1" hidden>
                        <div class="col-sm-4">
                            <label style="font-weight: 800">Merchant Name</label>
                        </div>
                        <div class="col-sm-8">
                            <label id="searchedMerchant"></label>
                        </div>
                    </div>
                    <div class="row" id="mobileSearch2" hidden>
                        <div class="col-sm-4">
                            <label style="font-weight: 800">Customer Name</label>
                        </div>
                        <div class="col-sm-8">
                            <label id="customerName"></label>
                        </div>
                    </div>
                    <div class="row" id="mobileSearch3" hidden>
                        <div class="col-sm-4">
                            <label style="font-weight: 800">Customer Address</label>
                        </div>
                        <div class="col-sm-8">
                            <!--<label id="customerAddress"></label>-->
                            <input type="text" id="customerAddress" class="form-control">
                        </div>
                    </div>
                    <div class="row" id="mobileSearch4" hidden>
                        <div class="col-sm-4">
                            <label style="font-weight: 800">Customer Phone</label>
                        </div>
                        <div class="col-sm-8">
                            <label id="customerPhone"></label>
                        </div>
                    </div>
                    <div class="row" id="mobileSearch9" hidden>
                        <div class="col-sm-4">
                            <label style="font-weight: 800">Price</label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" id="productPrice" class="form-control">
                        </div>
                    </div>
                    <div class="row" id="mobSearch10" hidden>
                        <div class="col-sm-4">
                            <label style="font-weight: 800">Package Option</label>
                        </div>
                        <div class="col-sm-8">
                            <select id="productSizeWeight" style="width: 100%">
                                <option value="standard">Standard</option>
                                <option value="large">Large</option>
                                <option value="special">Special</option>
                                <option value="specialplus">Special Plus</option>
                            </select>
                        </div>
                    </div>
                    <div class="row" id="mobileSearch6" hidden>
                        <div class="col-sm-4">
                            <label style="font-weight: 800">Customer District</label>
                        </div>
                        <div class="col-sm-8">
                            <select id="customerDistrict" style="width: 100%" onchange="fetch_customerThana(this.value);">
                                <?php
                                    foreach ($districtresult as $districtrow){
                                        echo "<option value=".$districtrow['districtId'].">".$districtrow['districtName']."</option>";
                                    }
                                ?>
                            </select>                            
                        </div>
                    </div>
                    <div class="row" id="mobileSearch5" style="margin-top: 10px" hidden>
                        <div class="col-sm-4">
                            <label style="font-weight: 800">Customer Thana</label>
                        </div>
                        <div class="col-sm-8">
                            <select id="customerThana" name="customerThana" style="width: 100%" required>
                                <option></option>
                            </select>
                        </div>
                    </div>
                    <div class="row" id="mobileSearch7" hidden>
                        <div class="col-sm-4">
                            <label style="font-weight: 800">Order ID</label>
                        </div>
                        <div class="col-sm-8">
                            <label id="orderID"></label>
                            <input type="hidden" id="paperflyID">
                         </div>
                    </div>
                    <div class="row" id="mobileSearch8" hidden>
                        <div class="col-sm-4">
                            <label style="font-weight: 800">Merchant Ref.</label>
                        </div>
                        <div class="col-sm-8">
                            <label id="merchantRef"></label>
                        </div>
                    </div>
                    <div class="row" id="acceptEdit" hidden>
                        <div class="col-sm-2">
                            <button type="button" class="btn btn-primary" id="btnAccept">Accept</button>
                        </div>
                        <div class="col-sm-2">
                            <button type="button" class="btn btn-warning" id="btnEdit">Save & Accept</button>
                        </div>
                        <div class="col-sm-2">
                            <button type="button" class="btn btn-danger" id="btnCancel">Close</button>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 15px; margin-bottom: 15px">
                        <div class="col-sm-12">
                            <p id="mobileSearchAlrt"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="validationResult">
        </div>

        <script type='text/javascript'>
            $('#pickedBarcode').focus();
            $('#customerDistrict').select2();
            $('#btnSave').click(function ()
            {

            })
            $('#btnEdit').click(function ()
            {
                var paperflyID = $('#paperflyID').val();
                var customerAddress = $('#customerAddress').val();
                var productPrice = $('#productPrice').val();
                var customerThana = $('#customerThana').val();
                var barcodeVal = $('#pickedBarcodeVal').val();
                var pickedBy = $('#pickedBy').html();
                var packageOption = $('#productSizeWeight').val();
                $.ajax(
                {
                    type: 'post',
                    url: 'orders_update1.php',
                    data:
                    {
                        get_orderid: paperflyID,
                        customerAddress: customerAddress,
                        productPrice: productPrice,
                        packageOption: packageOption,
                        customerThana: customerThana,
                        barcodeVal: barcodeVal,
                        pickedBy: pickedBy,
                        flagreq: 'updateAndAccept'

                    },
                    success: function (response)
                    {
                        var str = response;
                        var n = str.search("Error");
                        if (n < 0)
                        {
                            $('#mobileSearchAlrt').css('color', 'green');
                            $('#mobileSearchAlrt').html(response);
                            $('#barcodeDiv').attr('hidden', false);
                            $('#barcodeView').attr('src', 'Print-Without-Barcode?orderid=' + response);
                        } else
                        {
                            $('#mobileSearchAlrt').css('color', 'red');
                            $('#mobileSearchAlrt').html(response);
                            setTimeout(function () { $('#mobileSearchAlrt').html(''); }, 3000);
                        }
                    }
                })

            })
            function fetch_customerThana(val)
            {
                $.ajax({
                    type: 'post',
                    url: 'fetch_thana.php',
                    data: {
                        get_thanaid: val
                    },
                    success: function (response)
                    {
                        document.getElementById("customerThana").innerHTML = response;
                    }
                });
            }
            function fetch_customerThana_forPick(val)
            {
                $.ajax({
                    type: 'post',
                    url: 'fetch_thana_pick.php',
                    data: {
                        get_thanaid: val
                    },
                    success: function (response)
                    {
                        document.getElementById("customerThana").innerHTML = response;
                    }
                });
            }
            $('#btnCancel').click(function ()
            {
                $('#mobileSearch1').prop('hidden', true);
                $('#mobileSearch2').prop('hidden', true);
                $('#mobileSearch3').prop('hidden', true);
                $('#mobileSearch4').prop('hidden', true);
                $('#mobileSearch5').prop('hidden', true);
                $('#mobileSearch6').prop('hidden', true);
                $('#mobileSearch7').prop('hidden', true);
                $('#mobileSearch8').prop('hidden', true);
                $('#mobileSearch9').prop('hidden', true);
                $('#mobSearch10').prop('hidden', true);
                $('#acceptEdit').prop('hidden', true);
                $('#barcodeDiv').attr('hidden', true);
                $('#mobileSearchAlrt').html('');

                $('#pickScanResult1').prop('hidden', true);
                $('#pickScanResult2').prop('hidden', true);
                $('#mobileSearch').prop('hidden', true);

                $('#pickedBarcode').val('');
                $('#pickedBarcode').focus();
            })
            $('#btnAccept').click(function ()
            {
                var barcodeVal = $('#pickedBarcodeVal').val();
                var paperflyID = $('#paperflyID').val();
                var pickedBy = $('#pickedBy').html();
                if (!barcodeVal || !paperflyID)
                {
                    $('#mobileSearchAlrt').css('color', 'red');
                    $('#mobileSearchAlrt').html('Barcode or Order ID missing');
                    setTimeout(function () { $('#mobileSearchAlrt').html(''); }, 3000);
                } else
                {
                    $('#mobileSearchAlrt').css('color', 'blue');
                    $('#mobileSearchAlrt').html('Please wait.......');
                    $.ajax(
                    {
                        type: 'post',
                        url: 'orders_update1.php',
                        data:
                        {
                            get_orderid: barcodeVal,
                            paperflyID: paperflyID,
                            pickedBy: pickedBy,
                            flagreq: 'acceptOrder'
                        },
                        success: function (response)
                        {
                            var str = response;
                            var n = str.search("Error");
                            if (n < 0)
                            {
                                $('#mobileSearchAlrt').css('color', 'green');
                                $('#mobileSearchAlrt').html(response);
                                $('#barcodeDiv').attr('hidden', false);
                                $('#barcodeView').attr('src', 'Print-Without-Barcode?orderid=' + paperflyID);
                            } else
                            {
                                $('#mobileSearchAlrt').css('color', 'red');
                                $('#mobileSearchAlrt').html(response);
                                setTimeout(function () { $('#mobileSearchAlrt').html(''); }, 3000);
                            }
                        }
                    })
                }
            })
            function validateBarcode()
            {
                if ($('#pickedBarcode').val().length >= 11)
                {
                    var barcodeVal = $('#pickedBarcode').val();
                    var barcodeString = barcodeVal.trim().substring(0, 11);
                    $('#mobileSearchAlrt').css('color', 'blue');
                    $('#mobileSearchAlrt').html('Please wait.......');
                    $.ajax({
                        type: 'post',
                        url: 'orders_update1.php',
                        data: {
                            get_orderid: barcodeString,
                            flagreq: 'validateBarcode'
                        },
                        success: function (response)
                        {
                            var str = response;
                            var n = str.search("Error");
                            if (n < 0)
                            {
                                $('#validationResult').html('');
                                $('#validationResult').append(response);
                                $('#pickScanResult1').prop('hidden', false);
                                $('#pickScanResult2').prop('hidden', false);
                                $('#mobileSearch').prop('hidden', false);
                                $('#pickedBarcodeVal').val(barcodeVal);
                                $('#searchMobileNo').val('');
                                $('#searchMobileNo').focus();
                                $('#mobileSearchAlrt').html('');
                            } else
                            {
                                $('#barcodeAlrt').css('color', 'red');
                                $('#barcodeAlrt').html(response);
                                $('#pickScanResult1').prop('hidden', true);
                                $('#pickScanResult2').prop('hidden', true);
                                $('#mobileSearch').prop('hidden', true);
                                $('#pickedBarcodeVal').val('');
                                $('#mobileSearchAlrt').html('');
                                setTimeout(function () { $('#barcodeAlrt').html(''); }, 5000);
                            }
                        }
                    })
                } else
                {
                    $('#barcodeAlrt').css('color', 'red');
                    $('#barcodeAlrt').html('Error: no such pick up record found');
                    $('#pickScanResult1').prop('hidden', true);
                    $('#pickScanResult2').prop('hidden', true);
                    $('#mobileSearch').prop('hidden', true);
                    $('#pickedBarcodeVal').val('');
                    $('#mobileSearchAlrt').html('');
                    setTimeout(function () { $('#barcodeAlrt').html(''); }, 5000);
                }
                $('#mobileSearch1').prop('hidden', true);
                $('#mobileSearch2').prop('hidden', true);
                $('#mobileSearch3').prop('hidden', true);
                $('#mobileSearch4').prop('hidden', true);
                $('#mobileSearch5').prop('hidden', true);
                $('#mobileSearch6').prop('hidden', true);
                $('#mobileSearch7').prop('hidden', true);
                $('#mobileSearch8').prop('hidden', true);
                $('#mobileSearch9').prop('hidden', true);
                $('#mobSearch10').prop('hidden', true);
                $('#acceptEdit').prop('hidden', true);
                $('#barcodeDiv').attr('hidden', true);
                $('#mobileSearchAlrt').html('');
            }
            $('#searchMobileNo').on("keydown", function search(e)
            {
                if (e.keyCode == 13)
                {
                    var mobileNo = $(this).val();
                    var merchantCode = $('#merchantCode').val();
                    if (!mobileNo)
                    {
                        $('#mobileSearchAlrt').css('color', 'red');
                        $('#mobileSearchAlrt').html('Please enter customer mobile no or Merchant Order ID');
                        setTimeout(function () { $('#mobileSearchAlrt').html(''); }, 3000);
                    } else
                    {
                        if ($("input[name='searchOption']:checked").val() == 'mobNo')
                        {
                            if (mobileNo.length < 3)
                            {
                                $('#mobileSearchAlrt').css('color', 'red');
                                $('#mobileSearchAlrt').html('Last 3 digit of mobile no require');
                                setTimeout(function () { $('#mobileSearchAlrt').html(''); }, 3000);
                            } else
                            {
                                $('#mobileSearchAlrt').css('color', 'blue');
                                $('#mobileSearchAlrt').html('Please wait.......');
                                $.ajax(
                                {
                                    type: 'post',
                                    url: 'orders_update1.php',
                                    data:
                                    {
                                        get_orderid: mobileNo,
                                        merchantCode: merchantCode,
                                        flagreq: 'searchCustMobileNo'
                                    },
                                    success: function (response)
                                    {
                                        var str = response;
                                        var n = str.search("Error");
                                        if (n < 0)
                                        {
                                            $('#validationResult').html('');
                                            $('#validationResult').append(response);
                                            $('#mobileSearch1').prop('hidden', false);
                                            $('#mobileSearch2').prop('hidden', false);
                                            $('#mobileSearch3').prop('hidden', false);
                                            $('#mobileSearch4').prop('hidden', false);
                                            $('#mobileSearch5').prop('hidden', false);
                                            $('#mobileSearch6').prop('hidden', false);
                                            $('#mobileSearch7').prop('hidden', false);
                                            $('#mobileSearch8').prop('hidden', false);
                                            $('#mobileSearch9').prop('hidden', false);
                                            $('#mobSearch10').prop('hidden', false);
                                            $('#acceptEdit').prop('hidden', false);
                                            $('#mobileSearchAlrt').html('');
                                            $('#barcodeDiv').attr('hidden', true);
                                        } else
                                        {
                                            $('#mobileSearch1').prop('hidden', true);
                                            $('#mobileSearch2').prop('hidden', true);
                                            $('#mobileSearch3').prop('hidden', true);
                                            $('#mobileSearch4').prop('hidden', true);
                                            $('#mobileSearch5').prop('hidden', true);
                                            $('#mobileSearch6').prop('hidden', true);
                                            $('#mobileSearch7').prop('hidden', true);
                                            $('#mobileSearch8').prop('hidden', true);
                                            $('#mobileSearch9').prop('hidden', true);
                                            $('#mobSearch10').prop('hidden', true);
                                            $('#acceptEdit').prop('hidden', true);
                                            $('#mobileSearchAlrt').css('color', 'red');
                                            $('#mobileSearchAlrt').html(response);
                                            setTimeout(function () { $('#mobileSearchAlrt').html(''); }, 3000);
                                        }
                                    }
                                })
                            }
                        } else
                        {
                            $('#mobileSearchAlrt').css('color', 'blue');
                            $('#mobileSearchAlrt').html('Please wait.......');
                            $.ajax(
                            {
                                type: 'post',
                                url: 'orders_update1.php',
                                data:
                                {
                                    get_orderid: mobileNo,
                                    merchantCode: merchantCode,
                                    flagreq: 'searchMerchantRef'
                                },
                                success: function (response)
                                {
                                    var str = response;
                                    var n = str.search("Error");
                                    if (n < 0)
                                    {
                                        $('#validationResult').html('');
                                        $('#validationResult').append(response);
                                        $('#mobileSearch1').prop('hidden', false);
                                        $('#mobileSearch2').prop('hidden', false);
                                        $('#mobileSearch3').prop('hidden', false);
                                        $('#mobileSearch4').prop('hidden', false);
                                        $('#mobileSearch5').prop('hidden', false);
                                        $('#mobileSearch6').prop('hidden', false);
                                        $('#mobileSearch7').prop('hidden', false);
                                        $('#mobileSearch8').prop('hidden', false);
                                        $('#mobileSearch9').prop('hidden', false);
                                        $('#mobSearch10').prop('hidden', false);
                                        $('#acceptEdit').prop('hidden', false);
                                        $('#mobileSearchAlrt').html('');
                                        $('#barcodeDiv').attr('hidden', true);
                                    } else
                                    {
                                        $('#mobileSearch1').prop('hidden', true);
                                        $('#mobileSearch2').prop('hidden', true);
                                        $('#mobileSearch3').prop('hidden', true);
                                        $('#mobileSearch4').prop('hidden', true);
                                        $('#mobileSearch5').prop('hidden', true);
                                        $('#mobileSearch6').prop('hidden', true);
                                        $('#mobileSearch7').prop('hidden', true);
                                        $('#mobileSearch8').prop('hidden', true);
                                        $('#mobileSearch9').prop('hidden', true);
                                        $('#mobSearch10').prop('hidden', true);
                                        $('#acceptEdit').prop('hidden', true);
                                        $('#mobileSearchAlrt').css('color', 'red');
                                        $('#mobileSearchAlrt').html(response);
                                        setTimeout(function () { $('#mobileSearchAlrt').html(''); }, 3000);
                                    }
                                }
                            })
                        }
                    }
                }
            });
        </script>
     </body>
</html>
