<?php

// Display success messages using SweetAlert
if(isset($success_msg)){                                   // Check if success_msg is set
   foreach($success_msg as $success_msg){                  // Loop through each success message
      echo '<script>swal("'.$success_msg.'", "" ,"success");</script>';  // Show success alert
   }
}

// Display warning messages using SweetAlert
if(isset($warning_msg)){                                   // Check if warning_msg is set
   foreach($warning_msg as $warning_msg){                  // Loop through each warning message
      echo '<script>swal("'.$warning_msg.'", "" ,"warning");</script>'; // Show warning alert
   }
}

// Display info messages using SweetAlert
if(isset($info_msg)){                                      // Check if info_msg is set
   foreach($info_msg as $success_msg){                     // Loop through each info message
      echo '<script>swal("'.$info_msg.'", "" ,"info");</script>';       // Show info alert
   }
}

// Display error messages using SweetAlert
if(isset($error_msg)){                                     // Check if error_msg is set
   foreach($error_msg as $error_msg){                      // Loop through each error message
      echo '<script>swal("'.$error_msg.'", "" ,"error");</script>';     // Show error alert
   }
}

?>
