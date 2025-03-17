const scrollToTopBtn = document.getElementById("scrollToTopBtn");

scrollToTopBtn.onclick = function() {
  window.scrollTo({
    top: 0,
    behavior: "smooth" 
  });
};