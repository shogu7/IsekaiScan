console.log("searchToggle.js chargé !");

document.addEventListener("DOMContentLoaded", function() {
  const searchButton = document.getElementById("search-button");
  const searchBar = document.getElementById("search-bar");
  const searchField = document.getElementById("search-field");
  
  if (searchButton && searchBar && searchField) {
    searchButton.addEventListener("click", function(event) {
      event.preventDefault();
      
      if (searchBar.classList.contains("show") && searchField.value.trim() !== "") {
        submitSearch();
      } else {
        toggleSearchBar();
      }
    });
    
    searchField.addEventListener("keypress", function(event) {
      if (event.key === "Enter") {
        submitSearch();
      }
    });
    
    document.addEventListener("click", function(event) {
      if (!searchBar.contains(event.target) && event.target !== searchButton && !searchButton.contains(event.target)) {
        searchBar.classList.remove("show");
      }
    });
    
    searchBar.addEventListener("transitionend", function() {
      if (searchBar.classList.contains("show")) {
        searchField.focus();
      }
    });
  }
  
  function toggleSearchBar() {
    searchBar.classList.toggle("show");
    console.log("Barre de recherche toggled, maintenant visible :", searchBar.classList.contains("show"));
    
    if (searchBar.classList.contains("show")) {
      setTimeout(() => {
        searchField.focus();
      }, 100);
    }
  }
  
  function submitSearch() {
    if (searchField.value.trim() !== "") {
      console.log("Recherche soumise :", searchField.value);
      
      alert("Recherche de : " + searchField.value);
      
      searchBar.classList.remove("show");
      searchField.value = "";
    }
  }
});
