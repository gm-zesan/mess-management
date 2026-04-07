import "./bootstrap";

import Alpine from "alpinejs";
import toastr from "toastr";

// Configure Toastr
toastr.options = {
    closeButton: true,
    debug: false,
    newestOnTop: true,
    progressBar: false,
    positionClass: "toast-top-center",
    preventDuplicates: false,
    onclick: null,
    showDuration: 300,
    hideDuration: 500,
    timeOut: 2000,
    extendedTimeOut: 500,
    showEasing: "swing",
    hideEasing: "linear",
    showMethod: "fadeIn",
    hideMethod: "fadeOut",
};

// Make Toastr globally available
window.toastr = toastr;

window.Alpine = Alpine;

Alpine.start();
