<?php
include '/var/www/backend_components/connect.php'; // Connect to the database

// Check if the user_id cookie exists
if (!isset($_COOKIE['user_id'])) {
   setcookie('user_id', create_unique_id(), time() + 60*60*24*30, '/'); // Create user_id cookie for 30 days
   header('location:index.php'); // Refresh the page after setting cookie
   exit;
}

$user_id = intval($_COOKIE['user_id']); // Get user_id from cookie

// Handle check availability request
if (isset($_POST['check'])) {
   $check_in = trim(strip_tags($_POST['check_in'] ?? '')); // Sanitize check-in input

   if ($check_in) {
      // Count total bookings for selected check-in date
      $check_bookings = $conn->prepare("SELECT COUNT(*) FROM `bookings` WHERE check_in = ?");
      $check_bookings->bind_param("s", $check_in);
      $check_bookings->execute();
      $check_bookings->bind_result($total_rooms);
      $check_bookings->fetch();
      $check_bookings->close();

      // Max 30 rooms available
      if ($total_rooms >= 30) {
         $warning_msg[] = 'Rooms are not available';
      } else {
         $success_msg[] = 'Rooms are available';
      }
   } else {
      $warning_msg[] = 'Invalid check-in date!';
   }
}

// Handle booking request
if (isset($_POST['book'])) {
   // Sanitize and validate form input
   $name = trim(strip_tags($_POST['name'] ?? ''));
   $email = trim(strip_tags($_POST['email'] ?? ''));
   $number = trim(strip_tags($_POST['number'] ?? ''));
   $check_in = trim(strip_tags($_POST['check_in'] ?? ''));
   $check_out = trim(strip_tags($_POST['check_out'] ?? ''));
   $adults = intval($_POST['adults'] ?? 1);
   $childs = intval($_POST['childs'] ?? 0);
   $total_rooms = intval($_POST['rooms'] ?? 1);

   if ($check_in && $check_out) {
      // Count how many rooms are already booked for selected check-in date
      $check_bookings = $conn->prepare("SELECT COUNT(*) FROM `bookings` WHERE check_in = ?");
      $check_bookings->bind_param("s", $check_in);
      $check_bookings->execute();
      $check_bookings->bind_result($booked_rooms);
      $check_bookings->fetch();
      $check_bookings->close();

      // Check if there are enough rooms available
      if ($booked_rooms + $total_rooms > 30) {
         $warning_msg[] = 'Not enough rooms available!';
      } else {
         // Prevent duplicate bookings for same date by the same user
         $verify = $conn->prepare("SELECT * FROM `bookings` WHERE user_id = ? AND check_in = ? AND check_out = ?");
         $verify->bind_param("iss", $user_id, $check_in, $check_out);
         $verify->execute();
         $result = $verify->get_result();

         if ($result->num_rows > 0) {
            $warning_msg[] = 'You have already booked for the selected date!';
         } else {
            // Generate random room number (R-xxx)
            $room_number = 'R-' . rand(100, 999);

            // Insert booking into database
            $insert = $conn->prepare("INSERT INTO `bookings` (user_id, name, email, number, check_in, check_out, adults, childs, total_rooms, room_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insert->bind_param("isssssssis", $user_id, $name, $email, $number, $check_in, $check_out, $adults, $childs, $total_rooms, $room_number);
            $insert->execute();
            $insert->close();

            $success_msg[] = 'Room booked successfully!';
         }
         $verify->close();
      }
   } else {
      $warning_msg[] = 'Invalid check-in/check-out dates!';
   }
}

// Handle contact message
if (isset($_POST['send'])) {
   $id = create_unique_id(); // Generate unique ID for the message
   $name = trim(strip_tags($_POST['name'] ?? ''));
   $email = trim(strip_tags($_POST['email'] ?? ''));
   $number = trim(strip_tags($_POST['number'] ?? ''));
   $message = trim(strip_tags($_POST['message'] ?? ''));

   // Prevent duplicate messages with same content
   $verify_message = $conn->prepare("SELECT * FROM `messages` WHERE name = ? AND email = ? AND number = ? AND message = ?");
   $verify_message->bind_param("ssss", $name, $email, $number, $message);
   $verify_message->execute();
   $result = $verify_message->get_result();

   if ($result->num_rows > 0) {
      $warning_msg[] = 'Message has already been sent!';
   } else {
      // Insert new message
      $insert_message = $conn->prepare("INSERT INTO `messages` (id, name, email, number, message) VALUES (?, ?, ?, ?, ?)");
      $insert_message->bind_param("sssss", $id, $name, $email, $number, $message);
      $insert_message->execute();
      $insert_message->close();
      $success_msg[] = 'Message sent successfully!';
   }
   $verify_message->close();
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>The Palace Hotel</title>

   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">


</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- home section starts  -->

<section class="home" id="home">

   <div class="swiper home-slider">

      <div class="swiper-wrapper">

         <div class="box swiper-slide">
            <img src="images/home-img-1.jpg" alt="">
            <div class="flex">
               <h3>luxurious rooms</h3>
               <a href="#availability" class="btn">check availability</a>
            </div>
         </div>

         <div class="box swiper-slide">
            <img src="images/home-img-2.jpg" alt="">
            <div class="flex">
               <h3>foods and drinks</h3>
               <a href="#reservation" class="btn">make a reservation</a>
            </div>
         </div>

         <div class="box swiper-slide">
            <img src="images/home-img-3.jpg" alt="">
            <div class="flex">
               <h3>luxurious halls</h3>
               <a href="#contact" class="btn">contact us</a>
            </div>
         </div>

      </div>

      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>

   </div>

</section>

<!-- home section ends -->

<!-- availability section starts  -->

<section class="availability" id="availability">

   <form action="" method="post">
      <div class="flex">
         <div class="box">
            <p>Check in <span>*</span></p>
            <input type="date" name="check_in" class="input" required>
         </div>
         <div class="box">
            <p>Check out <span>*</span></p>
            <input type="date" name="check_out" class="input" required>
         </div>
         <div class="box">
            <p>Adults <span>*</span></p>
            <select name="adults" class="input" required>
               <option value="1">1 Adult</option>
               <option value="2">2 Adults</option>
               <option value="3">3 Adults</option>
               <option value="4">4 Adults</option>
               <option value="5">5 Adults</option>
               <option value="6">6 Adults</option>
            </select>
         </div>
         <div class="box">
            <p>Childs <span>*</span></p>
            <select name="childs" class="input" required>
               <option value="-">0 Child</option>
               <option value="1">1 Child</option>
               <option value="2">2 Childs</option>
               <option value="3">3 Childs</option>
               <option value="4">4 Childs</option>
               <option value="5">5 Childs</option>
               <option value="6">6 Childs</option>
            </select>
         </div>
         <div class="box">
            <p>Rooms <span>*</span></p>
            <select name="rooms" class="input" required>
               <option value="1">1 Room</option>
               <option value="2">2 Rooms</option>
               <option value="3">3 Rooms</option>
               <option value="4">4 Rooms</option>
               <option value="5">5 Rooms</option>
               <option value="6">6 Rooms</option>
            </select>
         </div>
      </div>
      <input type="submit" value="check availability" name="check" class="btn">
   </form>

</section>

<!-- availability section ends -->

<!-- about section starts  -->

<section class="about" id="about">

   <div class="row">
      <div class="image">
         <img src="images/about-img-1.jpg" alt="">
      </div>
      <div class="content">
         <h3>Best Staff</h3>
         <p>We are proud to have professional and friendly staff ready to provide you with the best service.
            Enjoy a comfortable and memorable stay with our dedicated team.</p>
         <a href="#reservation" class="btn">make a reservation</a>
      </div>
   </div>

   <div class="row revers">
      <div class="image">
         <img src="images/about-img-2.jpg" alt="">
      </div>
      <div class="content">
         <h3>Best Foods</h3>
         <p>Enjoy a variety of delicious dishes prepared with fresh ingredients and served with passion.
Our culinary creations are here to satisfy every taste.</p>
         <a href="#contact" class="btn">contact us</a>
      </div>
   </div>

   <div class="row">
      <div class="image">
         <img src="images/about-img-3.jpg" alt="">
      </div>
      <div class="content">
         <h3>Swimming Pool</h3>
         <p>Relax and refresh in our crystal-clear pool, surrounded by tropical vibes and serene views.
A perfect place to unwind, whether day or night.</p>
         <a href="#availability" class="btn">check availability</a>
      </div>
   </div>

</section>

<!-- about section ends -->

<!-- services section starts  -->

<section class="services">

   <div class="box-container">

      <div class="box">
         <img src="images/icon-1.png" alt="">
         <h3>Food & Drinks</h3>
         <p>Enjoy a wide selection of delicious dishes and refreshing beverages, all crafted to satisfy your cravings.</p>
      </div>

      <div class="box">
         <img src="images/icon-2.png" alt="">
         <h3>Outdoor Dining</h3>
         <p>Savor your meals in a cozy open-air setting surrounded by nature and fresh air.</p>
      </div>

      <div class="box">
         <img src="images/icon-3.png" alt="">
         <h3>Beach View</h3>
         <p>Relax with stunning views of the beach from the comfort of your room or dining area.</p>
      </div>

      <div class="box">
         <img src="images/icon-4.png" alt="">
         <h3>decorations</h3>
         <p>Beautifully designed spaces with elegant decorations that add charm to every corner.</p>
      </div>

      <div class="box">
         <img src="images/icon-5.png" alt="">
         <h3>swimming pool</h3>
         <p>Take a dip in our clean, spacious pool — perfect for a refreshing break any time of day.</p>
      </div>

      <div class="box">
         <img src="images/icon-6.png" alt="">
         <h3>Resort Beach</h3>
         <p>Step onto soft sands and feel the breeze at our exclusive private beach area.</p>
      </div>

   </div>

</section>

<!-- services section ends -->

<!-- reservation section starts  -->

<section class="reservation" id="reservation">

   <form action="" method="post">
      <h3>make a reservation</h3>
      <div class="flex">
         <div class="box">
            <p>your name <span>*</span></p>
            <input type="text" name="name" maxlength="50" required placeholder="enter your name" class="input">
         </div>
         <div class="box">
            <p>your email <span>*</span></p>
            <input type="email" name="email" maxlength="50" required placeholder="enter your email" class="input">
         </div>
         <div class="box">
            <p>your number <span>*</span></p>
            <input type="number" name="number" maxlength="10" min="0" max="9999999999" required placeholder="enter your number" class="input">
         </div>
         <div class="box">
            <p>rooms <span>*</span></p>
            <select name="rooms" class="input" required>
               <option value="1" selected>1 room</option>
               <option value="2">2 rooms</option>
               <option value="3">3 rooms</option>
               <option value="4">4 rooms</option>
               <option value="5">5 rooms</option>
               <option value="6">6 rooms</option>
            </select>
         </div>
         <div class="box">
            <p>check in <span>*</span></p>
            <input type="date" name="check_in" class="input" required>
         </div>
         <div class="box">
            <p>check out <span>*</span></p>
            <input type="date" name="check_out" class="input" required>
         </div>
         <div class="box">
            <p>adults <span>*</span></p>
            <select name="adults" class="input" required>
               <option value="1" selected>1 adult</option>
               <option value="2">2 adults</option>
               <option value="3">3 adults</option>
               <option value="4">4 adults</option>
               <option value="5">5 adults</option>
               <option value="6">6 adults</option>
            </select>
         </div>
         <div class="box">
            <p>childs <span>*</span></p>
            <select name="childs" class="input" required>
               <option value="0" selected>0 child</option>
               <option value="1">1 child</option>
               <option value="2">2 childs</option>
               <option value="3">3 childs</option>
               <option value="4">4 childs</option>
               <option value="5">5 childs</option>
               <option value="6">6 childs</option>
            </select>
         </div>
      </div>
      <input type="submit" value="book now" name="book" class="btn">
   </form>

</section>

<!-- reservation section ends -->

<!-- gallery section starts  -->

<section class="gallery" id="gallery">

   <div class="swiper gallery-slider">
      <div class="swiper-wrapper">
         <img src="images/gallery-img-1.jpg" class="swiper-slide" alt="">
         <img src="images/gallery-img-2.webp" class="swiper-slide" alt="">
         <img src="images/gallery-img-3.webp" class="swiper-slide" alt="">
         <img src="images/gallery-img-4.webp" class="swiper-slide" alt="">
         <img src="images/gallery-img-5.webp" class="swiper-slide" alt="">
         <img src="images/gallery-img-6.webp" class="swiper-slide" alt="">
      </div>
      <div class="swiper-pagination"></div>
   </div>

</section>

<!-- gallery section ends -->

<!-- contact section starts  -->

<section class="contact" id="contact">

   <div class="row">

      <form action="" method="post">
         <h3>send us message</h3>
         <input type="text" name="name" required maxlength="50" placeholder="enter your name" class="box">
         <input type="email" name="email" required maxlength="50" placeholder="enter your email" class="box">
         <input type="number" name="number" required maxlength="10" min="0" max="9999999999" placeholder="enter your number" class="box">
         <textarea name="message" class="box" required maxlength="1000" placeholder="enter your message" cols="30" rows="10"></textarea>
         <input type="submit" value="send message" name="send" class="btn">
      </form>

      <div class="faq">
         <h3 class="title">frequently asked questions</h3>
         <div class="box active">
            <h3>how to cancel?</h3>
            <p>You can cancel your reservation by contacting our customer service at least 24 hours in advance. Refund policies may apply.</p>
         </div>
         <div class="box">
            <h3>is there any vacancy?</h3>
            <p>Yes, we regularly update room availability. Please check our booking page or contact us directly for real-time info.</p>
         </div>
         <div class="box">
            <h3>what are payment methods?</h3>
            <p>We accept payments via credit card, debit card, bank transfer, and selected digital wallets like PayPal and GoPay.</p>
         </div>
         <div class="box">
            <h3>how to claim coupons codes?</h3>
            <p>Enter your coupon code during checkout. If valid, the discount will be applied automatically to your total.</p>
         </div>
         <div class="box">
            <h3>what are the age requirements?</h3>
            <p>Guests must be at least 18 years old to make a reservation. Children under 12 must be accompanied by an adult.</p>
         </div>
      </div>

   </div>

</section>

<!-- contact section ends -->

<!-- reviews section starts  -->

<section class="reviews" id="reviews">


   <div class="swiper reviews-slider">

      <div class="swiper-wrapper">
         <div class="swiper-slide box">
            <img src="images/pic-1.png" alt="">
            <h3>joao deo</h3>
            <p>"I really enjoyed my time here. This hotel has a very comfortable environment and a welcoming atmosphere. The staff truly pays attention to every detail to ensure a pleasant stay."</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-2.png" alt="">
            <h3>lara pez</h3>
            <p>"My experience staying at this hotel was exceptional. The rooms are clean and well-maintained, and the service is highly professional, making my visit unforgettable."</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-3.png" alt="">
            <h3>roy ado</h3>
            <p>"From the very helpful receptionists to the convenient amenities available, this hotel truly met my expectations. Safety and comfort are also their top priorities."</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-4.png" alt="">
            <h3>lopez diaz</h3>
            <p>"This hotel offers a unique experience with a very peaceful atmosphere and excellent facilities. I will definitely return here on my next trip."</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-5.png" alt="">
            <h3>tony leo</h3>
            <p>"My stay at this hotel was very satisfying. The staff was very professional, the rooms were very comfortable, and the atmosphere was very relaxing. I felt highly valued as a guest and will definitely return the next time."</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-6.png" alt="">
            <h3>laura zee</h3>
            <p>"I had an outstanding stay at this hotel. The accommodations were comfortable and thoughtfully furnished, the team was courteous and responsive, and everything went smoothly from check-in to check-out. It’s a top choice for those who value excellent service and a relaxing atmosphere."</p>
         </div>
      </div>

      <div class="swiper-pagination"></div>
   </div>

</section>

<!-- reviews section ends  -->





<?php include 'components/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<!-- custom js file link  -->
<script src="js/script.js"></script>

<?php include 'components/message.php'; ?>

</body>
</html>