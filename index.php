<?php
$conn = new mysqli('url', 'user', 'password', 'db');
$customer = $_GET['c'];
$sql = "SELECT * FROM projects WHERE customer='$customer' ORDER BY creation DESC";
echo "<html lang='en'>
  <head>
    <title>Cogwork Consulting Portal</title>
    <link href='css/bootstrap.min.css' rel='stylesheet'>
  </head>
  <body>
    <div class='col-md-12'><table class='table'><tr class='info'><td>&nbsp;</td></tr></table></div>
  
    <div class='container'>
    <h1>Cogwork Consulting Project Dashboard<br><small><small>$customer</small></small></h1>
    <p class='text-right'><button type='button' class='btn btn-info btn-lg' data-toggle='modal' data-target='#new'>New Project</button></p>
    <hr>
    
    <table class='table table-striped'>
    <thead><tr>
    <th>ID</th>
    <th>Project Name</th>
    <th>Started</th>
    <th>Due</th>
    <th>Status</th>
    </tr></thead>
    <tbody>
    ";
$results = $conn->query($sql);
while ($row = $results->fetch_assoc()){
    $date = new DateTime("now", new DateTimeZone('America/New_York'));
    $date->setTimestamp($row['creation']);
    $dateEnd = new DateTime();
    $dateEnd->setTimestamp($row['due']);
    $dateNow = new DateTime();
    $dateNow->setTimeStamp(time());
    $interval = $dateEnd->diff($dateNow);
    $time = time();
    $dir = 'files/'.$row['id'].'/';
    echo "<tr>
            <td>".$row['id']."</td>
            <td><a href='#' data-toggle='modal' data-target='#".$row['id']."'>".$row['title']."</a></td>
            <td>".$date->format('j F Y')."</td>
            <td>";
            if ($row['status'] != 3){
                if ($row['due']-$time > 86400){
                    echo "in " . $interval->days . " days";
                 } else if($row['due']-$time < 86400 && $row['due']>$time){
                    echo "Today";
                 } else {
                    echo "<button class='btn btn-danger btn-xs'>";
                    echo $interval->days . " days ago";
                    echo "</button>";
                }
            } else{
                echo "-";
            }
            echo"</td><td>";
            
            if ($row['status'] == 0){
                echo "<button class='btn btn-warning btn-xs'><span class='glyphicon glyphicon-exclamation-sign'></span> Not Viewed</button>";
            }
            if ($row['status'] == 1){
                echo "<button class='btn btn-info btn-xs'><span class='glyphicon glyphicon-eye-open'></span> Confirmed</button>";
            }
            if ($row['status'] == 2){
                echo "<button class='btn btn-primary btn-xs'><span class='glyphicon glyphicon-cog'></span> Working</button>";
            }
            if ($row['status'] == 3){
                echo "<button class='btn btn-success btn-xs'><span class='glyphicon glyphicon-ok-circle'></span> Completed</button>";
            }
            
            echo"</td>
        </tr>
        
        
<div class='modal fade' id='".$row['id']."' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
  <div class='modal-dialog modal-lg'>
    <div class='modal-content'>
      <div class='modal-header'>
        <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
        <h3 class='modal-title' id='myModalLabel'>".$row['title'];
              
        echo "</h3>
        
      </div>
      <div class='modal-body'>";
      if ($row['status'] != 0){
            $mod0 = "btn-xs' disabled";
        } else {
            $mod0 = "btn-lg'";
        }
        if ($row['status'] != 1){
            $mod1 = "btn-xs' disabled";
        }else {
            $mod1 = "btn-lg'";
        }
        if ($row['status'] != 2){
            $mod2 = "btn-xs' disabled";
        }else {
            $mod2 = "btn-lg'";
        }
        if ($row['status'] != 3){
            $mod3 = "btn-xs' disabled";
        }else {
            $mod3 = "btn-lg'";
        }
        
        echo "<p class='text-center'>
        <button class='btn btn-warning $mod0><span class='glyphicon glyphicon-exclamation-sign'></span> Not Viewed</button>
        <span class='glyphicon glyphicon-chevron-right'></span>
        <button class='btn btn-info $mod1><span class='glyphicon glyphicon-eye-open'></span> Confirmed</button>
        <span class='glyphicon glyphicon-chevron-right'></span>
        <button class='btn btn-primary $mod2><span class='glyphicon glyphicon-cog'></span> Working</button>
        <span class='glyphicon glyphicon-chevron-right'></span>
        <button class='btn btn-success $mod3><span class='glyphicon glyphicon-ok-circle'></span> Completed</button></p>";
        
        
      ##BODY
      echo $row['description'] . '<hr>';
      echo "<textarea class='form-control' rows='3'>".$row['notes']."</textarea><p class='text-right'><button class='btn btn-xs'>Update Notes</button></p>";
      
      $fromCCG = scandir($dir."to");
      $toCCG = scandir($dir."from");
    echo "<hr>";
    
    echo "<div class='row'><div class='col-md-6'><strong>FILES SENT TO YOU:<br></strong><small>(opens in new tab/window)</small><br>";
      for ($i = 2; $i < count($fromCCG); $i++){
        $size = number_format(filesize($dir."to/".$fromCCG[$i]) / 1024,2);
        echo "<a href='".$dir."to/".$fromCCG[$i]."' target='_blank'>".$fromCCG[$i]."</a> - <em>$size kb</em> - ".date("F d Y H:i",filemtime($dir."to/".$fromCCG[$i]))."<br>";
      }
      echo "</div><div class='col-md-6'><strong>FILES YOU SENT:<br></strong><small>(opens in new tab/window)</small><br>";
      for ($i = 2; $i < count($toCCG); $i++){
        $size = number_format(filesize($dir."from/".$toCCG[$i]) / 1024,2);
        echo "<a href='".$dir."from/".$toCCG[$i]."' target='_blank'>".$toCCG[$i]."</a> - <em>$size kb</em> - ".date("F d Y H:i",filemtime($dir."from/".$toCCG[$i]))."<br>";
      }
      echo "</div>";
      
      echo "<div class='col-md-12'><hr><strong>MESSAGES:</strong><p class='text-right'>New Message</p><br>";
      $msg = explode(",",$row['messages']);

      for ($i = count($msg)-1; $i >= 0; $i-=3){
        echo $msg[$i];
        echo ' - ';
        if  ($msg[$i-1]){
            echo '<em>From CCG</em>';
        } else {
            echo '<em>From You</em>';
        }
        echo ' <sup>(';
        echo date("F d,Y H:i",$msg[$i-2]);
        echo ')</sup><br>';
      }
      
      echo "</div></div>
      <div class='modal-footer'>
        <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
      </div>
    </div>
  </div>";
        
}
echo "</tbody></table></div><!-- /.container -->";
$dueTime = time()+432000;
$dueDate = date("Y-m-d",$dueTime);
$minDate = date("Y-m-d",time());
echo "
<div class='modal fade' id='new' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
  <div class='modal-dialog'>
    <div class='modal-content'>
      <div class='modal-header'>
        <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
        <h3 class='modal-title' id='myModalLabel'>New Project</h3>
      </div>
    <div class='modal-body'>
    
<form role='form'>
  <div class='form-group'>
    <label>Email Address</label>
    <input type='email' class='form-control' name='email' value='".$customer."'>
  </div>
  <div class='form-group'>
    <label>Project Title</label>
    <input type='text' class='form-control' name='title' placeholder='Unique name for this project'>
  </div>
  <div class='form-group'>
    <label>Project Description</label>
    <textarea class='form-control' rows='3' name='description'></textarea><small>*Please be as descriptive as possible, include any formulas if applicable or ensure they are emailed to me at howard@cogworkconsulting.com
  </div>
  <div class='form-group'>
    <label>Requested Due Date</label>
    <input type='date' class='form-control' name='due' value='$dueDate' min=$minDate><small>*Default is 5 days from order submission but can be moved earlier if needed. No explicit guarantee that order will be completed by due date but I will try my best</small>
  </div>
  <div class='form-group'>
    <label for='exampleInputFile'>File Upload (optional):</label>
    <input type='file' id='exampleInputFile'>
  </div><p class='text-center'>
  <button type='submit' class='btn btn-primary'>Submit</button>
  </p>
</form>
    
    </div>
  </div>
</div>

";


echo "
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src='https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js'></script>
    <script src='js/bootstrap.min.js'></script>
  </body>
</html>";
?>
