
<?php
    
    include('header.php');
    include('config.php');

if (isset($_POST['submit'])) {
   $from = trim($_POST['from']);
   $from = date("Y-m-d H:i:s", strtotime($from));
   $to = trim($_POST['to']);
   $to = date("Y-m-d H:i:s", strtotime($to));

   $sql = "SELECT count(*) as total FROM `barcode_factory` WHERE scanned_at BETWEEN '$from' AND '$to' and state = 1";
   $sql1 = "SELECT count(*) as total FROM `barcode_factory` WHERE scanned_at BETWEEN '$from' AND '$to' and state = 1 and accepted = 'Y' ";

     $sql2 = "SELECT count(*) as total FROM `barcode_factory` WHERE scanned_at BETWEEN '$from' AND '$to' and state = 1 and accepted = 'N' ";
  
    $result = mysqli_query($conn, $sql) or die("Error in Selecting " . mysqli_error($conn));
    $results = mysqli_query($conn, $sql1) or die("Error in Selecting " . mysqli_error($conn));
    $resultss = mysqli_query($conn, $sql2) or die("Error in Selecting " . mysqli_error($conn));
    $result2 = mysqli_fetch_assoc($result);
    $result3 = mysqli_fetch_assoc($results);
    $result4 = mysqli_fetch_assoc($resultss);
    $picked = $result2['total'];
    $accepted = $result3['total'];
    $pending = $result4['total'];
    // echo $picked;
   

  }
 

 ?>

 <div class="container">
<div class="col-lg-6">
 
    
      <form action ="scan_report.php" method="POST">
      <label>From</label>
      <input size="16" type="text" value="" class="form_datetime" name="from">
      <label>To</label>
     <input size="16" type="text" value="" class="form_datetime" name="to">
      <br>
      <input name="submit" type="submit"> 
      </form>
  
  
  
</div>
<div class="col-lg-6">
 <h1>Total Received : <?php echo $picked?></h1>
 <h1>Total Accepted : <?php echo $accepted?></h1>
 <h1>Total Pending : <?php echo $pending?></h1>
 </div>
 </div>
  <script type="text/javascript">
    $(".form_datetime").datetimepicker({format: 'yyyy-mm-dd hh:ii'});
</script> 
     </body>
</html>
