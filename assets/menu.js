"use strict";

const storedMenuState = localStorage.getItem("menu-state");

const getPreferredMenuState = () => {
    if (storedMenuState) {
        return storedMenuState;
    }

    return "opened";
};

const setMenuState = function (menuState) {
    const body = document.querySelector('body');
    if (menuState === 'opened') {
        body.classList.remove('sidebar-collapse');
    } else {
        body.classList.add('sidebar-collapse');
    }
};

setMenuState(getPreferredMenuState());

const sidebar = document.querySelector('[data-lte-toggle="sidebar"]');
sidebar.addEventListener('click', (event) => {
    const body = document.querySelector('body');
    localStorage.setItem("menu-state", body.classList.contains('sidebar-collapse') ? 'closed' : 'opened');
});
