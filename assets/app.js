//import './styles/app.css';
//import './styles/fonts.css';

//~ Main libs
import bootstrap from 'bootstrap/dist/js/bootstrap.bundle.min.js';
//import '@fortawesome/fontawesome-free';
import 'admin-lte';
import './theme.js';
import './menu.js';


document.addEventListener('DOMContentLoaded', () => {

    const toastTriggerList = document.querySelectorAll('[data-bs-toggle="toast"]');
    toastTriggerList.forEach((btn) => {
        btn.addEventListener('click', (event) => {
            event.preventDefault();
            const toastEle = document.getElementById(btn.getAttribute('data-bs-target'));
            const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastEle);
            toastBootstrap.show();
        });
    });



    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipTriggerList.forEach((tooltipTriggerEl) => {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });

    //$('.select2').select2({theme: 'bootstrap4', tags: true});

    //$('.dataTable').DataTable();

});
