$('a.sidebar-toggle').click(function(e){
    changeMenuState();
});

setStoredMenuState();

function setStoredMenuState()
{
    var minimenu = localStorage.getItem('minimenu');
    if (minimenu == 1) {
        $('body').addClass('sidebar-collapse');
    } else {
        $('body').removeClass('sidebar-collapse');
    }
}

function changeMenuState()
{
    var minimenu = $('body').hasClass('sidebar-collapse');
    if (minimenu) {
        localStorage.setItem('minimenu', 0);
    } else {
        localStorage.setItem('minimenu', 1);
    }
}