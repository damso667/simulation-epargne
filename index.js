const btn = document.querySelector('.btn1')
btn.addEventListener('click',presentation)
function presentation(){
   btn.classList.toggle('active') 
}
const btnHamburger = document.querySelector(".btn1")
const sectionHamburger = document.querySelector(".section-hamburger")

btnHamburger.addEventListener("click",function(){
    sectionHamburger.classList.toggle("active")
})