let adminSection = document.querySelector('.section.admin');
let userSection = document.querySelector('.section.user');
let mainElement = document.querySelector('main');

function loadContent(page, button) {
    if (adminSection) {
        fetch(page) //PHP File
            .then(response => response.text())
            .then(data => {
                adminSection.innerHTML = data;
                executeInjectedScripts(adminSection);
            });
           
    } else {
        fetch(page) //PHP File
            .then(response => response.text())
            .then(data => {
                userSection.innerHTML = data;
                executeInjectedScripts(userSection);
            });
        
    }
    let navs = document.querySelectorAll('.navigation-container .nav'); //User Section Navigations
    if (navs.length === 0) {
        navs = document.querySelectorAll('.navbar-container .nav') //Admin Section Navigations
    }
    navs.forEach(nav=>{
        nav.classList.remove('active');
    }) 
    button.classList.add('active');
}
function executeInjectedScripts(section) {
    const scripts = section.getElementsByTagName('script');
    for (let i = 0; i < scripts.length; i++) {
        eval(scripts[i].innerText);
    }
}

let lastScrollTop = 0;
let headerElement = document.querySelector("header");
let scrollTopElement = document.querySelector('.scroll-top');

window.addEventListener("scroll", () => {
  const currentScroll = window.pageYOffset || document.documentElement.scrollTop;

  //if the user click to make the window full screen, the following code will not work
  if (!isFullScreen) {
      if (currentScroll > lastScrollTop) {
        // Scrolling down
        headerElement.classList.add("hidden");
        mainElement.style.paddingTop = 0;
        adminSection ? adminSection.style.minHeight = '100vh' : 0;
        userSection ? userSection.style.minHeight = '100vh' : 0;
    } else {
        // Scrolling up
        headerElement.classList.remove("hidden");
        headerElement.style.position = "fixed";
        mainElement.style.paddingTop = '7rem';
        adminSection ? adminSection.style.minHeight = 'calc(100vh - 7rem)' : 0;
        userSection ? userSection.style.minHeight = 'calc(100vh - 7rem)' : 0;
      }
    
      lastScrollTop = currentScroll <= 0 ? 0 : currentScroll; // For Mobile or negative scrolling
  }
  if (currentScroll === 0) {
    scrollTopElement ? scrollTopElement.style.display = 'none' : 0;
  } else {
    scrollTopElement ? scrollTopElement.style.display = 'block' : 0;
  }
});

const showPw = document.querySelector('.password-container .show-pw');
showPw.addEventListener('click', ()=>{
    const showPwImg = showPw.querySelector('img');
    const inputPw = document.querySelector('#input-password');

    if (showPw.classList.contains('show')) {
        showPwImg.src = './img/hide.png';
        showPw.classList.remove('show')
        inputPw.type = 'text';
        
    } else {
        showPwImg.src = './img/show.png';
        showPw.classList.add('show')
        inputPw.type = 'password';
    }
   
})

//toggling the full screen or exit full screen button
let isFullScreen = false;
let screenToggle = document.querySelector('.screen-toggle');
let footerElement = document.querySelector('footer');
let screenToggleLabel = document.querySelector('.screen-toggle span')
if (screenToggle) {
    screenToggle.addEventListener('click', ()=>{
        if (!headerElement.classList.contains('hidden')) {
            headerElement.classList.add('hidden');
            mainElement.style.paddingTop = 0;
            screenToggle.style.top = '1rem';
            isFullScreen = true;
            screenToggleLabel.innerHTML = 'Exit Full Screen';
            footerElement.style.display = 'none';
            adminSection.style.minHeight = '100vh';
        } else {
            headerElement.classList.remove('hidden');
            mainElement.style.paddingTop = '7rem';
            screenToggle.style.top = '8rem';
            isFullScreen = false;
            screenToggleLabel.innerHTML = 'Make Full Screen';
            footerElement.style.display = 'block';
            adminSection.style.minHeight = 'calc(100vh - 7rem)';
        }
    })

}

