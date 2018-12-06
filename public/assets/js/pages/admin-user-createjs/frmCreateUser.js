function frmCreateUser() {
}

frmCreateUser.prototype.initialize = function() {
    $("#btn-create-user").on('click', function() {
        if (cosmo.frmcreateuser.validate()) {
            cosmo.frmcreateuser.save();
        }
        return false;
    });

    $('#btn-alter-image').click(function() {
       cosmo.frmcreateuser.wrapImage();
    });

    $('#input-frmuser-fileupload').on('change', function() {
        cosmo.frmcreateuser.loadAvatar();
    });
};

frmCreateUser.prototype.createObject = function() {
    var varFormRegister = {};

    varFormRegister.avatar = $('#img-frmuser-avatar').attr('src');
    varFormRegister.fullname = $('#input-frmuser-name').val();
    varFormRegister.username = $('#input-frmuser-email').val();
    varFormRegister.password = $('#input-frmuser-password').val();
    varFormRegister.admin = $("input#input-frmuser-administrator:checked").length;

    return varFormRegister;
};

frmCreateUser.prototype.wrapImage = function() {
    $('#input-frmuser-fileupload').click();
};

frmCreateUser.prototype.validateEmail = function(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
};

frmCreateUser.prototype.validateUsername = function(email) {

    var bRetorno = false;

    if (!this.validateEmail(email))
        return false;

    var data = "username=" + email;

    var returnSuccess = function() {
        bRetorno = true;
    };
    var errorHandler = function(jqXHR) {
        cosmo.dialog.error('Erro', jqXHR.responseJSON[0]);
        bRetorno = false;
    };

    var configurationAjaxSend = cosmo.ajax.getDefaults();
    configurationAjaxSend.url = cosmo.urlbase + cosmo.routes_name.AVALIABLE_USERNAME_COSMO;
    configurationAjaxSend.method = 'GET';
    configurationAjaxSend.data = data;
    configurationAjaxSend.type = 'json';
    configurationAjaxSend.async = false;
    configurationAjaxSend.sucess = returnSuccess;
    configurationAjaxSend.error = errorHandler;

    cosmo.ajax.send(configurationAjaxSend);

    return bRetorno;
};

frmCreateUser.prototype.validate = function() {
    var varFormRegister = this.createObject();

    if (varFormRegister.username.trim() === '') {
        cosmo.dialog.error('Erro', 'O campo Email é obrigatório!');
        return false;
    }

    if (!this.validateEmail(varFormRegister.username)) {
        cosmo.dialog.error('Erro', 'O campo Email informado é inválido!');
        return false;
    }

    if (!this.validateUsername(varFormRegister.username)) {
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

    if (!this.isDataURL(varFormRegister.avatar)) {
        cosmo.dialog.error('Erro', 'O campo Imagem é obrigatório!');
        return false;
    }

    return true;
};

frmCreateUser.prototype.loadAvatar = function() {
    var oFReader = new FileReader(),
        rFilter = /^(?:image\/bmp|image\/cis\-cod|image\/gif|image\/ief|image\/jpeg|image\/jpeg|image\/jpeg|image\/pipeg|image\/png|image\/svg\+xml|image\/tiff|image\/x\-cmu\-raster|image\/x\-cmx|image\/x\-icon|image\/x\-portable\-anymap|image\/x\-portable\-bitmap|image\/x\-portable\-graymap|image\/x\-portable\-pixmap|image\/x\-rgb|image\/x\-xbitmap|image\/x\-xpixmap|image\/x\-xwindowdump)$/i;

    var oFile = $('#input-frmuser-fileupload').get(0).files[0];

    if (!rFilter.test(oFile.type)) {
        cosmo.dialog.error('Erro', 'O arquivo selecionado é inválido.');
        return;
    }

    oFReader.onload = function (oFREvent) {
        var img = new Image();

        img.src = oFREvent.target.result;

        img.onload = function () {
            var canvas = document.createElement("canvas");

            canvas.width = 96;
            canvas.height = 96;
            var ctx = canvas.getContext("2d");

            ctx.drawImage(this, 0, 0, 100, 100);

            $('#img-frmuser-avatar').attr('src', canvas.toDataURL());
        };

        img.src = oFREvent.target.result;
    };

    if ($('#input-frmuser-fileupload').get(0).files.length === 0) {
        return;
    }

    oFReader.readAsDataURL(oFile);
};

frmCreateUser.prototype.isDataURL = function(s) {
    var sRegex = /^\s*data:([a-z]+\/[a-z0-9\-\+]+(;[a-z\-]+\=[a-z0-9\-]+)?)?(;base64)?,[a-z0-9\!\$\&\'\,\(\)\*\+\,\;\=\-\.\_\~\:\@\/\?\%\s]*\s*$/i;
    return !!s.match(sRegex);
};

frmCreateUser.prototype.save = function() {
    var varFormRegister = this.createObject();

    var returnSuccess = function(data) {
        cosmo.dialog.success('Cadastro de usuário!', data.message, function() { window.location.href = data.callback; });
    };
    var errorHandler = function(jqXHR) {
        cosmo.dialog.error('Erro', jqXHR.responseJSON[0]);
    };

    var configurationAjaxSend = cosmo.ajax.getDefaults();
    configurationAjaxSend.url = cosmo.urlbase + cosmo.routes_name.ADM_REGISTER_USER_SAVE;
    configurationAjaxSend.method = 'POST';
    configurationAjaxSend.data = varFormRegister;
    configurationAjaxSend.type = 'json';
    configurationAjaxSend.sucess = returnSuccess;
    configurationAjaxSend.error = errorHandler;

    cosmo.ajax.send(configurationAjaxSend);

    return true;
};

window.cosmo = window.cosmo || {};
window.cosmo.frmcreateuser = window.cosmo.frmcreateuser || new frmCreateUser();

$('document').ready(function(){
    window.cosmo.frmcreateuser.initialize();
});