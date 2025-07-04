let navbar = document.querySelector('.header .flex .navbar'); // Select the navbar element inside .header .flex
let menuBtn = document.querySelector('.header .flex #menu-btn'); // Select the menu button (hamburger icon)

/* Toggle the menu icon and navbar when menu button is clicked */
menuBtn.onclick = () => {
   menuBtn.classList.toggle('fa-times'); // Toggle the 'fa-times' class to change icon (e.g., X icon)
   navbar.classList.toggle('active'); // Toggle the 'active' class to show/hide the navbar
}

/* When scrolling the page */
window.onscroll = () => {
   menuBtn.classList.remove('fa-times'); // Remove 'fa-times' class (reset icon to menu)
   navbar.classList.remove('active'); // Remove 'active' class (hide the navbar if open)
}

/* Limit the number of digits in number inputs based on their maxLength */
document.querySelectorAll('input[type="number"]').forEach(inputNumbmer => {
   inputNumbmer.oninput = () => {
      // If input exceeds maxLength, trim it to allowed length
      if(inputNumbmer.value.length > inputNumbmer.maxLength) 
         inputNumbmer.value = inputNumbmer.value.slice(0, inputNumbmer.maxLength);
   }
});
