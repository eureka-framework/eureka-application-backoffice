import '../scss/app.scss';

//~ Main libs
import 'jquery';
import 'popper.js';
import 'bootstrap';
import 'perfect-scrollbar';

//~ Vendors Libs
import 'chart.js';
import 'progressbar.js';
import 'typeahead.js';
import 'select2';
import 'jvectormap';
import 'jvectormap/tests/assets/jquery-jvectormap-world-mill-en.js';
import 'owl.carousel';
import 'codemirror';
import 'codemirror/mode/javascript/javascript.js';
import 'codemirror/mode/shell/shell.js';
import 'pwstabs/assets/jquery.pwstabs';
import 'jquery-toast-plugin';

//~ Libs Config
import './lib.js';


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
    $.toast({
        heading: 'Danger',
        text: error.detail,
        showHideTransition: 'slide',
        icon: 'error',
        loaderBg: '#f2a654',
        position: 'top-right'
    });
}

document.addEventListener('DOMContentLoaded', () => {

    $('[data-toggle="minimize"]').on('click', () => {
        //~ inverse condition, this event is captured before the update of the body class
        setCookie('bo_menu_state', $('body').hasClass('sidebar-icon-only') ? 'open' : 'closed', 30);
    });

    let $formLogin = $('#form-login');
    $formLogin.on('submit', () => {
        let email    = $formLogin.find('input[type=text]').val();
        let password = $formLogin.find('input[type=password]').val();

        $.ajax('/api/auth/token/get', {
            method: "POST",
            accepts: {
                "json": "application/json"
            },
            //contentType: "application/json",
            dataType: "json",
            //processData: false,
            data: {"email": email, "password": password},
        })
            .done((data, textStatus, jqXHR) => {
                document.location.href = "/";
            })
            .fail((jqXHR, textStatus, errorThrown) => {
                let response = JSON.parse(jqXHR.responseText);
                if (!response.errors || response.errors.length === 0) {
                    ajaxToastError({detail: "Unknown error!"});
                } else {
                    ajaxToastError(response.errors.shift());
                }
            });

        return false;
    });
});