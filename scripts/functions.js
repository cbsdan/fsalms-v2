export function changeActiveNav(navId) {
    let activeNav = document.getElementById(`${navId}`);
    let navs = document.querySelectorAll('.navigation-container .nav'); //User Section Navigations
    if (navs.length === 0) {
        navs = document.querySelectorAll('.navbar-container .nav') //Admin Section Navigations
    }
    navs.forEach(nav=>{
        nav.classList.remove('active');
    }) 
    activeNav.classList.add('active');
}