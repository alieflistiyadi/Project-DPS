let navbar = document.querySelector('.header .navbar'); // Select the navbar element inside the header

// Toggle the 'active' class when the menu button is clicked (to show/hide the navbar)
document.querySelector('#menu-btn').onclick = () => {
   navbar.classList.toggle('active'); // Add or remove 'active' class
}

// Remove the 'active' class from navbar when the user scrolls the page
window.onscroll = () => {
   navbar.classList.remove('active'); // Hide the navbar on scroll
}

// Toggle FAQ boxes when the question title (h3) is clicked
document.querySelectorAll('.contact .row .faq .box h3').forEach(faqBox => {
   faqBox.onclick = () => {
      faqBox.parentElement.classList.toggle('active'); // Add or remove 'active' class on the parent box
   }
});

// Limit the length of number inputs to their defined maxLength
document.querySelectorAll('input[type="number"]').forEach(inputNumber => {
   inputNumber.oninput = () => {
      if(inputNumber.value.length > inputNumber.maxLength)
         inputNumber.value = inputNumber.value.slice(0, inputNumber.maxLength); // Truncate input to maxLength
   }
});

// Initialize the home slider with coverflow effect
var swiper = new Swiper(".home-slider", {
   loop: true, // Enable infinite loop
   effect: "coverflow", // Use coverflow effect
   spaceBetween: 30, // Set space between slides
   grabCursor: true, // Change cursor to grab style
   coverflowEffect: {
      rotate: 50, // Rotate slides by 50 degrees
      stretch: 0, // No stretch
      depth: 100, // Depth for 3D effect
      modifier: 1, // Modifier for coverflow intensity
      slideShadows: false, // Disable slide shadows
   },
   navigation: {
     nextEl: ".swiper-button-next", // Next button element
     prevEl: ".swiper-button-prev", // Previous button element
   },
});

// Initialize the gallery slider with coverflow effect
var swiper = new Swiper(".gallery-slider", {
   loop: true, // Enable infinite loop
   effect: "coverflow", // Use coverflow effect
   slidesPerView: "auto", // Automatic number of slides in view
   centeredSlides: true, // Center the active slide
   grabCursor: true, // Cursor looks like grabbing hand
   coverflowEffect: {
      rotate: 0, // No rotation
      stretch: 0, // No stretch
      depth: 100, // Depth for 3D effect
      modifier: 2, // Higher modifier for stronger effect
      slideShadows: true, // Enable shadows on slides
   },
   pagination: {
      el: ".swiper-pagination", // Enable pagination bullets
   },
});

// Initialize the reviews slider
var swiper = new Swiper(".reviews-slider", {
   loop: true, // Enable infinite loop
   slidesPerView: "auto", // Number of slides adjusts automatically
   grabCursor: true, // Cursor looks like grabbing hand
   spaceBetween: 30, // Space between slides
   pagination: {
      el: ".swiper-pagination", // Enable pagination bullets
   },
   breakpoints: {
      768: {
        slidesPerView: 1, // Show 1 slide on medium screens
      },
      991: {
        slidesPerView: 2, // Show 2 slides on large screens
      },
   },
});
