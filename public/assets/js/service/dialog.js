/**
 * Method dialog view
 * @returns {boolean}
 * @constructor
 */
function Dialog() {
}

Dialog.prototype.error = function(title, message) {
    return swal({
        title: title,
        html: message,
        type: "error",
        timer: 5000
    });
};

Dialog.prototype.success = function(title, message, callback) {
    if (callback === undefined) {
        return swal({
            title: title,
            html: message,
            type: "success",
            timer: 5000
        });
    } else {
        return swal({
            title: title,
            html: message,
            type: "success",
            timer: 5000
        }).then(callback, callback);
    }
};

Dialog.prototype.warning = function(title, message) {
    return swal({
        title: title,
        html: message,
        type: "warning",
        timer: 5000
    });
};

Dialog.prototype.info = function(title, message) {
    return swal({
        title: title,
        html: message,
        type: "info",
        timer: 5000
    });
};

Dialog.prototype.question = function(title, message) {
    return swal({
        title: title,
        html: message,
        type: "question",
        timer: 5000
    });
};

window.cosmo = window.cosmo || {};
window.cosmo.dialog = window.cosmo.dialog || new Dialog();
