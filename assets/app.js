//import './styles/app.css';
//import './styles/fonts.css';

//~ Main libs
import $ from 'jquery';

import 'bootstrap';
import 'popper.js';
import '@fortawesome/fontawesome-free';
import 'admin-lte';
import 'select2';

const APP_MENU_STATE_NAME = 'menu_state';
const APP_DARK_MODE_NAME = 'dark_mode';


//~ Cookie management
window.setCookie = function (name, value, expireDays) {
    const expireDate = new Date();
    expireDate.setDate(expireDate.getDate() + expireDays);
    document.cookie = name + '=' + encodeURI(value) + '; path=/' + (!expireDays ? '' : '; expires=' + expireDate.toGMTString() + '; SameSite=Strict');
    console.log(document.cookie);
};

window.getCookie = function (name) {
    if (document.cookie.length > 0) {
        let c_start = document.cookie.indexOf(name + '=');
        if (c_start !== -1) {
            c_start = c_start + name.length + 1;
            let c_end = document.cookie.indexOf(';', c_start);
            if (c_end === -1) {
                c_end = document.cookie.length;
            }
            return decodeURI(document.cookie.substring(c_start, c_end));
        }
    }
    return '';
};

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

    $('[data-widget="pushmenu"]').on('click', function () {
        window.setCookie(APP_MENU_STATE_NAME, $('body').hasClass('sidebar-collapse') ? 'opened' : 'closed', 30);
    });

    $('[data-toggle="dark-mode"]').on('click', function (e) {
        let $body = $('body');
        if (e.target.checked) {
            $body.addClass('dark-mode');
        } else {
            $body.removeClass('dark-mode');
        }

        window.setCookie(APP_DARK_MODE_NAME, $body.hasClass('dark-mode'), 30);
    });

    $('.select2').select2({theme: 'bootstrap4', tags: true});

    //$('.dataTable').DataTable();

});
