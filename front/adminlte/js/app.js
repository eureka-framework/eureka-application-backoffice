import '../scss/app.scss';

//~ Main libs
import $ from 'jquery';
import 'bootstrap/dist/js/bootstrap.bundle';
import 'select2';

const imagesContext = require.context('../images', true, /\.(png|jpg|jpeg|gif|ico|svg|webp)$/);
imagesContext.keys().forEach(imagesContext);


function setCookie(name, value, expireDays) {
    const expireDate = new Date();
    expireDate.setDate(expireDate.getDate() + expireDays);
    document.cookie = name + '=' + encodeURI(value) + '; path=/' + (!expireDays ? '' : ';expires=' + expireDate.toString());
}

function getCookie(name) {
    if (document.cookie.length > 0) {
        let start = document.cookie.indexOf(name + '=');
        if (start !== -1) {
            start = start + name.length + 1;
            let end = document.cookie.indexOf(';', start);
            if (end === -1) {
                end = document.cookie.length;
            }
            return unescape(document.cookie.substring(start, end));
        }
    }
    return '';
}

function ajaxToastError(error)
{
    console.log(error);
    $(document).Toasts('create', {
        class: 'bg-danger',
        title: 'Toast Title',
        autohide: true,
        delay: 750,
        body: error.detail
    });
}

document.addEventListener('DOMContentLoaded', () => {

    $('[data-toggle="minimize"]').on('click', () => {
        //~ inverse condition, this event is captured before the update of the body class
        setCookie('bo_menu_state', $('body').hasClass('sidebar-icon-only') ? 'open' : 'closed', 30);
    });

    $('.select2').select2();
});
