const burger = $('.burger');
const menuBurger = $('.sidebar__show');
const body = $('body');
burger.click(function () {
    menuBurger.toggleClass('active');
    menuBurger.css("top", $('.pacient-aside').innerHeight + 'px');
    body.toggleClass('ovhidden');
})