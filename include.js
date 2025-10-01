// Function to load header or footer dynamically
function loadInclude(id, url) {
  return fetch(url)
    .then(response => {
      if (!response.ok) {
        throw new Error(`Failed to load ${url}: ${response.statusText}`);
      }
      return response.text();
    })
    .then(data => {
      const element = document.getElementById(id);
      if (element) {
        element.innerHTML = data;
      }
    })
    .catch(error => {
      console.error(error);
      const element = document.getElementById(id);
      if (element) {
        element.innerHTML = `<p>Could not load ${url}</p>`;
      }
    });
}

// DOM ready
document.addEventListener('DOMContentLoaded', () => {
  // Load header and footer
  Promise.all([
    loadInclude('header', 'header.php'),
    loadInclude('footer', 'footer.php')
  ]).then(() => {
    // Replace <h2> in hero section if it exists
    const heroHeading = document.querySelector('.hero h2');
    if (heroHeading) {
      heroHeading.textContent = "Welcome to Chandusoft";
    }
  });

  // Back to Top Button functionality
  const backToTopBtn = document.getElementById('backToTop');

  if (backToTopBtn) {
    // Show/hide button on scroll
    window.addEventListener('scroll', () => {
      backToTopBtn.style.display = window.scrollY > 300 ? 'block' : 'none';
    });

    // Smooth scroll to top when clicked
    backToTopBtn.addEventListener('click', () => {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
  }
});
