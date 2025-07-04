<?php
include '/var/www/backend_components/connect.php';

// Set user_id from cookie or create a new one
if (isset($_COOKIE['user_id'])) {
    $user_id = intval($_COOKIE['user_id']);
} else {
    setcookie('user_id', create_unique_id(), time() + 60 * 60 * 24 * 30, '/');
    header('location:index.php');
    exit;
}

// Handle booking cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel'], $_POST['booking_id'])) {
    $booking_id = filter_var($_POST['booking_id'], FILTER_SANITIZE_NUMBER_INT);

    // Verify booking and user
    $verify_booking = $conn->prepare("SELECT * FROM bookings WHERE id = ? AND user_id = ?");
    $verify_booking->bind_param("ii", $booking_id, $user_id);
    $verify_booking->execute();
    $result = $verify_booking->get_result();

    if ($result->num_rows > 0) {
        $delete_booking = $conn->prepare("DELETE FROM bookings WHERE id = ?");
        $delete_booking->bind_param("i", $booking_id);
        $delete_booking->execute();
        $success_msg[] = 'Booking cancelled successfully!';
    } else {
        $warning_msg[] = 'Booking not found or you do not have permission to cancel this booking!';
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Bookings</title>

   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<body>

<section class="bookings">
   <h1 class="heading">My Bookings</h1>
   <div class="box-container">
      <?php
         $select_bookings = $conn->prepare("SELECT * FROM bookings WHERE user_id = ?");
         $select_bookings->bind_param("i", $user_id);
         $select_bookings->execute();
         $result = $select_bookings->get_result();

         if ($result->num_rows > 0) {
            while ($fetch_booking = $result->fetch_assoc()) {
                $room_number = !empty($fetch_booking['room_number']) ? htmlspecialchars($fetch_booking['room_number']) : 'N/A';
                $check_in    = htmlspecialchars($fetch_booking['check_in'] ?? 'N/A');
                $check_out   = htmlspecialchars($fetch_booking['check_out'] ?? 'N/A');
                $created_at  = htmlspecialchars($fetch_booking['created_at'] ?? 'N/A');
      ?>
      <div class="box">
         <p>name : <span><?= htmlspecialchars($fetch_booking['name']); ?></span></p>
         <p>email : <span><?= htmlspecialchars($fetch_booking['email']); ?></span></p>
         <p>number : <span><?= htmlspecialchars($fetch_booking['number']); ?></span></p>
         <p>Booking ID : <span><?= htmlspecialchars($fetch_booking['id'] ?? 'N/A') ?></span></p>
         <p>User ID : <span><?= htmlspecialchars($fetch_booking['user_id'] ?? 'N/A') ?></span></p>
         <p>Room Number : <span><?= $room_number ?></span></p>
         <p>Check In : <span><?= $check_in ?></span></p>
         <p>Check Out : <span><?= $check_out ?></span></p>
         <p>Created At : <span><?= $created_at ?></span></p>
         <form action="" method="POST">
            <input type="hidden" name="booking_id" value="<?= htmlspecialchars($fetch_booking['id'] ?? '') ?>">
            <input type="submit" value="Cancel Booking" name="cancel" class="btn" onclick="return confirm('Are you sure you want to cancel this booking?');">
         </form>
      </div>
      <?php
            }
         } else {
      ?>   
      <div class="box" style="text-align: center;">
         <p style="padding-bottom: .5rem; text-transform:capitalize;">No bookings found!</p>
         <a href="index.php#reservation" class="btn">Book New</a>
      </div>
      <?php
         }
      ?>
   </div>
</section>

<?php include 'components/footer.php'; ?>

<!-- Script -->
<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script src="js/script.js"></script>
<?php include 'components/message.php'; ?>

</body>
</html>
