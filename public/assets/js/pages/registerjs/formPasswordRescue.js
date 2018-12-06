/**
 * formPasswordRescue
 * @constructor
 */
function formPasswordRescue() {
}

formPasswordRescue.prototype.initialize = function() {
    $('#btnRescuePassword').click(function(){
        if (cosmo.rescue.validate()) {
            cosmo.rescue.sendMail();
        }
    });

};

formPasswordRescue.prototype.validate = function() {
    var varFormPasswordRescue = this.createObject();

    if (varFormPasswordRescue.username.trim() === '') {
        cosmo.dialog.error('Erro', 'O campo Email é obrigatório!');
        return false;
    }

    if (!this.validateEmail(varFormPasswordRescue.username)) {
        cosmo.dialog.error('Erro', 'O campo Email informado é inválido!');
        return false;
    }

    return true;
};

formPasswordRescue.prototype.createObject = function() {
    var varFormPasswordRescue = {};
    varFormPasswordRescue.username = $('#frmrescue-email').val();
    return varFormPasswordRescue;
};

formPasswordRescue.prototype.sendMail = function() {
    var varFormPasswordRescue = this.createObject();

    var returnSuccess = function(data) {
        cosmo.dialog.success('Recuperar Senha!', data.message, function() { window.location.href = data.callback; });
    };
    var errorHandler = function(jqXHR) {
        cosmo.dialog.error('Erro', jqXHR.responseJSON);
    };

    var configurationAjaxSend = cosmo.ajax.getDefaults();
    configurationAjaxSend.url = cosmo.urlbase + cosmo.routes_name.REGISTER_RESCUE_PASSWORD;
    configurationAjaxSend.method = 'POST';
    configurationAjaxSend.data = varFormPasswordRescue;
    configurationAjaxSend.type = 'json';
    configurationAjaxSend.sucess = returnSuccess;
    configurationAjaxSend.error = errorHandler;

    cosmo.ajax.send(configurationAjaxSend);

    return true;
};

formPasswordRescue.prototype.validateEmail = function(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
};

window.cosmo = window.cosmo || {};
window.cosmo.rescue = window.cosmo.rescue || new formPasswordRescue();

$('document').ready(function(){
    window.cosmo.rescue.initialize();
});