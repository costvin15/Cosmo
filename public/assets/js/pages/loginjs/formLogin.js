/**
 * formLogin
 * @constructor
 */
function formLogin() {
}

formLogin.prototype.initialize = function() {
    $('#btnLogin').click(function(){
        if (cosmo.login.validate())
            cosmo.login.auth();
    });
};

formLogin.prototype.validateEmail = function(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
};

formLogin.prototype.createObject = function() {
    var varFormRegister = {};

    varFormRegister.username = $('#frmlogin-email').val();
    varFormRegister.password = $('#frmlogin-password').val();

    return varFormRegister;
};

formLogin.prototype.validate = function() {
    var varFormRegister = this.createObject();

    if (varFormRegister.username.trim() === '') {
        cosmo.dialog.error('Erro', 'O campo Email é obrigatório!');
        return false;
    }

    if (!this.validateEmail(varFormRegister.username)) {
        cosmo.dialog.error('Erro', 'O campo Email informado é inválido!');
        return false;
    }

    if (varFormRegister.password.trim() === '') {
        cosmo.dialog.error('Erro', 'O campo Senha é obrigatório!');
        return false;
    }

    return true;
};

formLogin.prototype.auth = function() {
    var varFormRegister = this.createObject();

    var returnSuccess = function(data) {
        window.location.href = data.callback;
    };

    var errorHandler = function(jqXHR) {
        // console.log(jqXHR.responseJSON);
        cosmo.dialog.error('Erro', jqXHR.responseJSON[0]);
    };

    var configurationAjaxSend = cosmo.ajax.getDefaults();
    configurationAjaxSend.url = cosmo.urlbase + cosmo.routes_name.AUTH;
    configurationAjaxSend.method = 'POST';
    configurationAjaxSend.data = varFormRegister;
    configurationAjaxSend.type = 'json';
    configurationAjaxSend.sucess = returnSuccess;
    configurationAjaxSend.error = errorHandler;

    cosmo.ajax.send(configurationAjaxSend);

    return true;
};


window.cosmo = window.cosmo || {};
window.cosmo.login = window.cosmo.login || new formLogin();

$('document').ready(function(){
    window.cosmo.login.initialize();
});