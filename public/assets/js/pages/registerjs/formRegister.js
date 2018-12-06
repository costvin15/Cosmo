/**
 * formRegister
 * @constructor
 */
function formRegister() {
}

formRegister.prototype.initialize = function() {
    $("#btnCreateAcount").click(function(){
        if (cosmo.register.validate()) {
            cosmo.register.save();
        }
    });

    $("#frmregister-email").blur(function(){
        cosmo.register.validateUsername($(this).val());
    });
};

formRegister.prototype.save = function() {
    var varFormRegister = this.createObject();

    var returnSuccess = function(data) {
        cosmo.dialog.success('Seja bem vindo!', data.message, function() { window.location.href = data.callback; });
    };
    var errorHandler = function(jqXHR) {
        cosmo.dialog.error('Erro', jqXHR.responseJSON[0]);
    };

    var configurationAjaxSend = cosmo.ajax.getDefaults();
    configurationAjaxSend.url = cosmo.urlbase + cosmo.routes_name.REGISTER_SAVE;
    configurationAjaxSend.method = 'POST';
    configurationAjaxSend.data = varFormRegister;
    configurationAjaxSend.type = 'json';
    configurationAjaxSend.sucess = returnSuccess;
    configurationAjaxSend.error = errorHandler;

    cosmo.ajax.send(configurationAjaxSend);

    return true;
};

formRegister.prototype.validateUsername = function(email) {

    if (!this.validateEmail(email))
        return false;

    var data = "username=" + email;

    var returnSuccess = function() {};
    var errorHandler = function(jqXHR) {
        cosmo.dialog.error('Erro', jqXHR.responseJSON[0]);
    };

    var configurationAjaxSend = cosmo.ajax.getDefaults();
    configurationAjaxSend.url = cosmo.urlbase + cosmo.routes_name.AVALIABLE_USERNAME_COSMO;
    configurationAjaxSend.method = 'GET';
    configurationAjaxSend.data = data;
    configurationAjaxSend.type = 'json';
    configurationAjaxSend.sucess = returnSuccess;
    configurationAjaxSend.error = errorHandler;

    cosmo.ajax.send(configurationAjaxSend);

    return true;
};

formRegister.prototype.validateEmail = function(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
};

formRegister.prototype.createObject = function() {
    var varFormRegister = {};

    varFormRegister.username = $('#frmregister-email').val();
    varFormRegister.fullname = $('#frmregister-fullname').val();
    varFormRegister.password = $('#frmregister-password').val();

    return varFormRegister;
};

formRegister.prototype.validate = function() {
    var varFormRegister = this.createObject();

    if (varFormRegister.username.trim() === '') {
        cosmo.dialog.error('Erro', 'O campo Email é obrigatório!');
        return false;
    }

    if (!this.validateEmail(varFormRegister.username)) {
        cosmo.dialog.error('Erro', 'O campo Email informado é inválido!');
        return false;
    }

    if (varFormRegister.fullname.trim() === '') {
        cosmo.dialog.error('Erro', 'O campo Nome Completo é obrigatório!');
        return false;
    }

    if (varFormRegister.password.trim() === '') {
        cosmo.dialog.error('Erro', 'O campo Senha é obrigatório!');
        return false;
    }

    if (varFormRegister.password.length <= 3) {
        cosmo.dialog.error('Erro', 'O campo Senha deve possuir mais do que 3 caracteres!');
        return false;
    }


    return true;
};

window.cosmo = window.cosmo || {};
window.cosmo.register = window.cosmo.register || new formRegister();

$('document').ready(function(){
    window.cosmo.register.initialize();
});

