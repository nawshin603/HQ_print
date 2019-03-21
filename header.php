<!DOCTYPE html>
<html lang="en">
    <head>
        <title>PaperFly</title>
         <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="cache-control" content="no-cache, must-revalidate">
        <meta http-equiv="expires" content="0">
        <meta http-equiv="pragma" content="no-cache">
        <link rel="icon" type="image/jpg" href="image/favicon.jpg">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link href="css/bootstrap-combinedv1.min.css" rel="stylesheet">
        <link type="text/css" rel="stylesheet" href="css/calender.css" media="screen">
        <link rel="stylesheet" href="css/styles.css">
        <link rel="stylesheet" href="css/sristi-styles.css">
        <script src="js/jquery-2.2.0.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/ie-emulation-modes-warning.js"></script>
        <script type="text/javascript" src="js/calendar.js"></script>

        <link rel="stylesheet" type="text/css" href="css/jquery-ui.css">
        <link rel="stylesheet" type="text/css" href="css/jquery-ui-timepicker-addon.css">
        <link rel="stylesheet" type="text/css" href="css/select2.css">
        <link href="css/jquery.bdt.css" type="text/css" rel="stylesheet">
        <link rel="stylesheet" href="css/dataTables.bootstrap.min.css" />
          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.0/animate.min.css">

        <script type="text/javascript" src="js/jquery.maskedinput.js"></script>
        <script src="js/jquery.dataTables.min.js"></script>
        <script src="js/dataTables.bootstrap.min.js"></script>            

        <script type="text/javascript" src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/jquery-ui-sliderAccess.js"></script>
        <script type="text/javascript" src="js/jquery-ui-timepicker-addon.js"></script>
        <script src="js/jquery.ui.touch-punch.min.js"></script>
        <script src="js/select2.min.js"></script>

        <script src="js/jquery.sortelements.js" type="text/javascript"></script>
        <script src="js/notify.js" type="text/javascript"></script>
        <script src="js/jquery.bdt.js" type="text/javascript"></script>

        <script type="text/javascript" src="js/canvasjs.min.js"></script>

        
        <style>
            .navbar-custom {
                background-color:#fff;
                color:#16469E;
                border-color: #fff;
                border-radius:0;
            }
            .navbar-custom .navbar-nav > li > a {
                font: 13px 'paperfly roman';            
  	            color:#00AEEF;
            }
            
            .navbar-custom .navbar-nav > ul > a {
  	            color:#16469E;
            }

            .dropdown-menu > li > a {
                font: 13px 'paperfly roman';
  	            color:#16469E;
            }

            .icon-bar {
               background-color:#16469E !important;
            }            
          .dropdown-menu > li > a:hover,
           .dropdown-menu > li > a:focus {
            color: #fff;
            text-decoration: none;
            background-color: #66CCFF;  /*change color of links in drop down here*/
           }
	        div.combobox	{font: 15px 'paperfly roman'}
	        div.combobox	{position: relative;zoom: 1}
	        div.combobox	div.dropdownlist	{display: none; width: 200px;
		        border: solid 1px #000;background-color: #fff;
		        height: 200px;overflow: auto;position: absolute;
		        top: 45px; left: 0px;}
	        div.combobox	.dropdownlist	a	{display: block;text-decoration: none;
		        color: #000;padding: 1px;height: 1em;cursor: default}
	        div.combobox	.dropdownlist	a.light	{color: #fff;
		        background-color: #16469E; height: 23px}
	        div.combobox	.dropdownlist, input {font: 15px 'paperfly roman';}
	        div.combobox	input {float: left;width: 182px;
		        border: solid 1px #ccc;height: 25px}
	        div.combobox	span	{border: solid 1px #ccc;background: #eee;
		        width: 16px;height: 25px;
		        float: left;text-align: center;border-left: none;cursor: default}
                              
        </style>
    </head>
    <body>
        <?php
            $groupSQL = "Select * from tbl_permission_group where user_id = $user_id_chk";
            $groupResult = mysqli_query($conn, $groupSQL);
            $groupRow = mysqli_fetch_array($groupResult);

        ?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <br>
                    <a style="margin-left: 15px; color: #16469E; font: 15px 'paperfly roman'" href="#">Welcome <u><?php echo $uName ; ?></u></a>
                </div>
                <div class="col-sm-6">
                    <img style="height: 25%; width: 25%; float: right" src="image/Wings.jpg" />
                </div>
            </div>
            <div class="row">
            <nav style ="width: 100%; float: left" role="navigation" class="navbar navbar-default navbar-custom">
                <div class="col-sm-8">
                    <div class="navbar-header">
                       <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navigationbar">
                           <span class="sr-only">Toggle navigation</span>
                           <span class="icon-bar"></span>
                           <span class="icon-bar"></span>
                           <span class="icon-bar"></span>
                        </button>
                    </div>
                            <!--
                            <a class="navbar-brand" style="color: #00AEEF; text-align: left; font: 13px 'paperfly roman'" href="home">Home</a>
                            -->
                        <div style="color: #00AEEF" class="collapse navbar-collapse" id="navigationbar">
                            <ul style="font: 13px 'paperfly roman'" class="nav navbar-nav">
                                <li>
                                    <a style="color: #00AEEF; text-align: left; font: 13px 'paperfly roman'" href="Home-Page">Home</a>
                                </li>
                                <?php if ($groupRow['Order_Management'] == 'Y') {
                                    //Check Order menu permission
                                    $menuSQL = "Select * from tbl_menu_ordermgt where user_id = $user_id_chk";
                                    $menuResult = mysqli_query($conn, $menuSQL);
                                    $menuRow = mysqli_fetch_array($menuResult);
                                ?>
                                <li class="dropdown">
                                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">Order Management<span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <?php if ($menuRow['new_order'] == 'Y') {?>
                                        <li class="dropdown-submenu">
                                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">New Order</a>
                                            <ul class="dropdown-menu">
                                                <li class="dropdown">
                                                    <a tabindex="0" href="placeorders">Single Order</a>
                                                </li>
                                                <li class="dropdown">
                                                    <a tabindex="0" href="batchuploads">Batch Upload</a>
                                                </li>
                                                <?php if($user_type == 'Administrator' or $user_type != 'Merchant'){?>
                                                <li class="dropdown">
                                                    <a tabindex="0" href="Pull-Orders">Pull Orders</a>
                                                </li>
                                                <?php }?>
                                            </ul>
                                        </li>
                                        <?php } if ($menuRow['smart_pick'] == 'Y') {?>
                                        <li class="dropdown-submenu">
                                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">Fulfillment Pick</a>
                                            <ul class="dropdown-menu">
                                                <li class="dropdown">
                                                    <a tabindex="0" href="#">Single Order</a>
                                                </li>
                                                <li class="dropdown">
                                                    <a tabindex="0" href="Fulfillment-Pick-New">Batch Upload</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <?php } if ($menuRow['3p_order_service'] == 'Y') {?>
                                        <li class="dropdown-submenu">
                                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">3P Order Service</a>
                                            <ul class="dropdown-menu">
                                                <li class="dropdown">
                                                    <a tabindex="0" href="Third-Party-Pick">Single Order</a>
                                                </li>
                                                <li class="dropdown">
                                                    <a tabindex="0" href="#">Batch Upload</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <?php } if ($menuRow['close_order'] == 'Y') {?>
                                        <li class="dropdown-submenu">
                                            <a href="#">Close Order</a>
                                            <ul class="dropdown-menu">
                                                <li class="dropdown">
                                                    <a tabindex="0" href="General-Close">General Close</a>
                                                </li>
                                                <li class="dropdown">
                                                    <a tabindex="0" href="Accidental-Close">Accidental Close</a>
                                                </li>
                                                <li class="dropdown">
                                                    <a tabindex="0" href="Batch-Close">Batch Close</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <?php } if ($menuRow['assign_order'] == 'Y') {?>
                                        <li class="dropdown-submenu">
                                            <a href="#">Assign Orders</a>
                                            <ul class="dropdown-menu">
                                                <li class="dropdown">
                                                    <a tabindex="0" href="Assign-Orders">One By One</a>
                                                </li>
                                                <li class="dropdown">
                                                    <a tabindex="0" href="Pick-Assign">Pick Assignment in Batch</a>
                                                </li>
                                                <li class="dropdown">
                                                    <a tabindex="0" href="#">Drop Assignment in Batch</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <?php } if ($menuRow['edit_order'] == 'Y') {?>
                                        <li>
                                            <a href="Edit-Delete-Orders">Edit/Delete Orders</a>
                                        </li>
                                        <?php } if ($menuRow['exportToExcel'] == 'Y') {?>
                                        <li>
                                            <a href="Order-Export-Close">Export Orders to Excel</a>
                                        </li>
                                        <?php } if ($menuRow['bankCollection'] == 'Y') {?>
                                        <li class="dropdown-submenu">
                                            <a href="#">Collection at Bank</a>
                                            <ul class="dropdown-menu">
                                                <li class="dropdown">
                                                    <a tabindex="0" href="Bank-Collection">Collection Entry</a>
                                                </li>
                                                <li class="dropdown">
                                                    <a tabindex="0" href="#">Find Deposit</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <?php } if ($menuRow['scanOrders'] == 'Y') {?>
                                        <li>
                                            <a href="Scan-Orders">Scan Orders</a>
                                        </li>
                                        <?php }if ($menuRow['retOrder'] == 'Y') {?>
                                        <li>
                                            <a href="Return-Orders">Return Orders</a>
                                        </li>
                                        <?php }if ($menuRow['DP2_mgt'] == 'Y') {?>
                                        <li>
                                            <a href="DP2_mgt">DP2 Management</a>
                                        </li>
                                        <?php }if ($menuRow['barcodeWarehouse'] == 'Y') {?>
                                        <li>
                                            <a href="Request-For-Barcode">Barcode Warehouse</a>
                                        </li>
                                        <?php } if ($menuRow['pickOrders'] == 'Y') {?>
                                        <li>
                                            <a href="Pick-Orders">Pick Orders</a>
                                        </li>
                                        <?php }?>      
                                                                                 
                                    </ul>
                                </li>
                                <?php } if ($groupRow['Database_Management'] == 'Y') {
                                    //Check Order menu permission
                                    $menuSQL = "Select * from tbl_menu_dbmgt where user_id = $user_id_chk";
                                    $menuResult = mysqli_query($conn, $menuSQL);
                                    $menuRow = mysqli_fetch_array($menuResult);
                                ?>
                                <li class = "dropdown">
                                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">Database Management<span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <?php if ($menuRow['merchant'] == 'Y') {?>
                                        <li class="dropdown-submenu">
                                            <a href="#">Merchant</a>
                                            <ul class="dropdown-menu">
                                                <li class="dropdown">
                                                    <a tabindex="0" href="newmerchant">New Registration</a>
                                                </li>
                                                <li class="dropdown">
                                                    <a tabindex="0" href="editMerchant">Edit Existing</a>
                                                </li>
                                                <li class="dropdown">
                                                    <a tabindex="1" href="merchantExport">Export to Excel</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <?php } if ($menuRow['point'] == 'Y') {?>
                                        <li class="dropdown-submenu">
                                            <a href="#">Point</a>
                                            <ul class="dropdown-menu">
                                                <li class="dropdown">
                                                    <a tabindex="0" href="newpoint">New Point</a>
                                                </li>
                                                <li class="dropdown">
                                                    <a tabindex="0" href="editPoint">Edit Existing</a>
                                                </li>
                                                <li class="dropdown">
                                                    <a tabindex="1" href="pointExport">Export to Excel</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <?php } if ($menuRow['employee'] == 'Y') {?>
                                        <li class="dropdown-submenu">
                                            <a href="#">Employee</a>
                                            <ul class="dropdown-menu">
                                                <li class="dropdown">
                                                    <a tabindex="0" href="newemployee">New joinee</a>
                                                </li>
                                                <li class="dropdown">
                                                    <a tabindex="0" href="editEmployee">Edit Existing</a>
                                                </li>
                                                <li class="dropdown">
                                                    <a tabindex="1" href="empExport">Export to Excel</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <?php } if ($menuRow['designation'] == 'Y') {?>
                                        <li class="dropdown-submenu">
                                            <a href="#">Designation</a>
                                            <ul class="dropdown-menu">
                                                <li class="dropdown">
                                                    <a tabindex="0" href="newDesignation">New Designation</a>
                                                </li>
                                                <li class="dropdown">
                                                    <a tabindex="0" href="editDesignation">Edit/Delete Designation</a>
                                                </li>
                                                <li class="dropdown">
                                                    <a tabindex="0" href="designationExport">Export to Excel</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <?php } if ($menuRow['RateType'] == 'Y') {?>
                                        <li class="dropdown-submenu">
                                            <a href="#">Rate Chart ID</a>
                                            <ul class="dropdown-menu">
                                                <li class="dropdown">
                                                    <a tabindex="0" href="RateTypeNew">Create New</a>
                                                </li>
                                                <li class="dropdown">
                                                    <a tabindex="0" href="ratetypeExport">Export to Excel</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <?php } if ($menuRow['RateChart'] == 'Y') {?>
                                        <li class="dropdown-submenu">
                                            <a href="#">Rate Chart</a>
                                            <ul class="dropdown-menu">
                                                <li class="dropdown">
                                                    <a tabindex="0" href="Edit-Rate-Chart">Edit/Delete</a>
                                                </li>
                                                <li class="dropdown">
                                                    <a tabindex="0" href="ChartExport">Export to Excel</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <?php } if ($menuRow['thana'] == 'Y') {?>
                                        <li class="dropdown-submenu">
                                            <a href="#">Thana Assignment</a>
                                            <ul class="dropdown-menu">
                                                <li class="dropdown">
                                                    <a tabindex="0" href="#">New Assignment</a>
                                                </li>
                                                <li class="dropdown">
                                                    <a tabindex="0" href="#">Edit/Delete</a>
                                                </li>
                                                <li class="dropdown">
                                                    <a tabindex="0" href="#">Export to Excel</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <?php } if ($menuRow['atmLocation'] == 'Y') {?>
                                        <li>
                                            <a href="ATM-Location">ATM Locations</a>
                                        </li>
                                        <?php }if ($menuRow['orderMark'] == 'Y') {?>
                                        <li>
                                            <a href="Mark-Order">Order Marker</a>
                                        </li>
                                        <?php }?>
                                    </ul>
                                </li>
                                <?php } if ($groupRow['Capacity_Relocation'] == 'Y') {
                                    $menuSQL = "Select * from tbl_menu_capacityrel where user_id = $user_id_chk";
                                    $menuResult = mysqli_query($conn, $menuSQL);
                                    $menuRow = mysqli_fetch_array($menuResult);
                                ?>
                                <li class = "dropdown">
                                    <a style="class="dropdown-toggle" data-toggle="dropdown" href="#">Relocation<span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <?php if ($menuRow['pickupSystem'] == 'Y') {?>
                                        <li>
                                            <a href="Pickup-System">Pick-up System</a>
                                        </li>
                                        <?php } if ($menuRow['deliveryCapacity'] == 'Y') {?>
                                        <li>
                                            <a href="Capacity-Relocation">Delivery Capacity</a>
                                        </li>
                                        <?php }?>
                                    </ul>
                                </li>
                                <?php } if ($groupRow['User_Setting'] == 'Y') {
                                    //Check Order menu permission
                                    $menuSQL = "Select * from tbl_menu_usersetting where user_id = $user_id_chk";
                                    $menuResult = mysqli_query($conn, $menuSQL);
                                    $menuRow = mysqli_fetch_array($menuResult);
                                ?>
                                <li class = "dropdown">
                                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">User Settings<span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="ChangePass">Change Password</a>
                                        </li>
                                        <?php if ($menuRow['new_user'] == 'Y') {?>
                                        <li>
                                            <a href="newUser">Create New User</a>
                                        </li>
                                        <?php } if ($menuRow['edit_user'] == 'Y') {?>
                                        <li>
                                            <a href="editUser">Edit User</a>
                                        </li>
                                        <?php } if ($menuRow['permission'] == 'Y') {?>
                                        <li class="dropdown-submenu">
                                            <a href="#">Permission</a>
                                            <ul class="dropdown-menu">
                                                <li class="dropdown">
                                                    <a tabindex="0" href="groupPermission">Group Level Permission</a>
                                                </li>
                                                <li class="dropdown">
                                                    <a tabindex="0" href="MenuLevel">Menu Lavel Permission</a>
                                                </li>
                                                <li class="dropdown">
                                                    <a tabindex="0" href="orderTracker">Order Tracker Permission</a>
                                                </li>
                                                <li class="dropdown">
                                                    <a href="Scan-Privilege">Order Scan Privilege</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <?php }?>
                                    </ul>
                                </li>
                                <?php } if ($groupRow['Report'] == 'Y') {
                                    //Check Order menu permission
                                    $menuSQL = "Select * from tbl_menu_report where user_id = $user_id_chk";
                                    $menuResult = mysqli_query($conn, $menuSQL);
                                    $menuRow = mysqli_fetch_array($menuResult);
                                ?>
                                <li class = "dropdown">
                                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">Reports<span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <?php if ($menuRow['hitCounter'] == 'Y') {?>
                                        <li>
                                            <a href="hitcounter">Hit Counter</a>
                                        </li>
                                        <?php } if ($menuRow['closedOrders'] == 'Y') {?>
                                        <li>
                                            <a href="Close-Orders">All Orders</a>
                                        </li>
                                        <?php } if ($menuRow['invoices'] == 'Y') {?>
                                        <li>
                                            <a href="invoices">Create Invoice</a>
                                        </li>
                                        <?php } if ($menuRow['printInvoices'] == 'Y') {?>
                                        <li>
                                            <a href="Invoice-Print-Search">Print Invoices</a>
                                        </li>
                                        <?php } if ($menuRow['bankDeposit'] == 'Y') {?>
                                        <li>
                                            <a href="Bank-Deposit">Bank Deposit</a>
                                        </li>
                                        <?php } if ($menuRow['revSummary'] == 'Y') {?>
                                        <li>
                                            <a href="Revenue-Summary">Revenue Summary</a>
                                        </li>
                                        <?php } if ($menuRow['shuttleReport'] == 'Y') {?>
                                        <li>
                                            <a href="Shuttle-Report">Shuttle Report</a>
                                        </li>
                                        <?php } if ($menuRow['beftn'] == 'Y') {?>
                                        <li>
                                            <a href="BEFTN">BEFTN</a>
                                        </li>
                                        <?php } if ($menuRow['chequePrint'] == 'Y') {?>
                                        <li>
                                            <a href="Print-Cheque">Cheque Print</a>
                                        </li>
                                        <?php } if($menuRow['barcodePrint'] == 'Y') {?>
                                        <li>
                                            <a href="Generate-Barcode">Barcode Print</a>
                                        </li>
                                        <?php } if($menuRow['dashboard'] == 'Y') {?>
                                        <li>
                                            <a href="Dashboard">Dashboard</a>
                                        </li>
                                        <?php } if($menuRow['point_executive'] == 'Y') {?>
                                        <li>
                                            <a href="Point-Executive">Daily  Delivery tracker</a>
                                        </li>
                                        <?php } if($menuRow['delivery_performance'] == 'Y') {?>
                                        <li>
                                            <a href="Delivery-Performance">Daily point and officers performance</a>
                                        </li>
                                        <?php } if($menuRow['revenuePerformance'] == 'Y') {?>
                                        <li>
                                            <a href="Revenue-Performance">Merchant performance</a>
                                        </li>
                                        <?php } if($menuRow['cashPending'] == 'Y') {?>
                                        <li>
                                            <a href="Pending-Cash-Collection">Pending Cash Collection</a>
                                        </li>
                                        <?php }if($menuRow['slaMissed'] == 'Y') {?>
                                        <li>
                                            <a href="Missed-SLA-Report">5 Days SLA Missed Orders</a>
                                        </li>
                                        <?php }if($menuRow['barcodeOne'] == 'Y') {?>
                                        <li>
                                            <a href="Single-Barcode">Barcode Print (Single)</a>
                                        </li>
                                        <?php }if($menuRow['returnReport'] == 'Y') {?>
                                        <li>
                                            <a href="Return-Report">Return Report</a>
                                        </li>
                                        <?php }?>
                                    </ul>
                                </li>
                                <?php }?>
                                <li>
                                    <a style="color: #00AEEF; font: 13px 'paperfly roman'" href="logout">Logout</a>
                                </li>
                            </ul>
                        </div>
                       </div>
                    </nav>
                </div>
            </div>
        </div>



<script type="text/javascript">
    $(".dropdown-menu > li > a").click(function () {
    x = $(this).attr("href");
    if(x=="#") { return false; }
    }); 
</script>