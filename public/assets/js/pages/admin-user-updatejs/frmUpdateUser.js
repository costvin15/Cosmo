function frmUpdateUser() {
}

frmUpdateUser.prototype.initialize = function() {
    $("#btn-create-user").on('click', function() {
        if (cosmo.frmupdateuser.validate()) {
            cosmo.frmupdateuser.save();
        }
        return false;
    });

    $('#btn-alter-image').click(function() {
       cosmo.frmupdateuser.wrapImage();
    });

    $('#input-frmuser-fileupload').on('change', function() {
        cosmo.frmupdateuser.loadAvatar();
    });
};

frmUpdateUser.prototype.createObject = function() {
    var varFormRegister = {};

    varFormRegister.id = $('#input-hidden-id').val();
    varFormRegister.avatar = $('#img-frmuser-avatar').attr('src');
    varFormRegister.fullname = $('#input-frmuser-name').val();
    varFormRegister.admin = $("input#input-frmuser-administrator:checked").length;

    return varFormRegister;
};

frmUpdateUser.prototype.wrapImage = function() {
    $('#input-frmuser-fileupload').click();
};

frmUpdateUser.prototype.validate = function() {
    var varFormRegister = this.createObject();

    if (varFormRegister.fullname.trim() === '') {
        cosmo.dialog.error('Erro', 'O campo Nome Completo é obrigatório!');
        return false;
    }

    if (!this.isDataURL(varFormRegister.avatar)) {
        cosmo.dialog.error('Erro', 'O campo Imagem é obrigatório!');
        return false;
    }

    return true;
};

frmUpdateUser.prototype.loadAvatar = function() {
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

frmUpdateUser.prototype.isDataURL = function(s) {
    var sRegex = /^\s*data:([a-z]+\/[a-z0-9\-\+]+(;[a-z\-]+\=[a-z0-9\-]+)?)?(;base64)?,[a-z0-9\!\$\&\'\,\(\)\*\+\,\;\=\-\.\_\~\:\@\/\?\%\s]*\s*$/i;
    return !!s.match(sRegex);
};

frmUpdateUser.prototype.save = function() {
    var varFormRegister = this.createObject();

    var returnSuccess = function(data) {
        cosmo.dialog.success('Cadastro de usuário!', data.message, function() { window.location.href = data.callback; });
    };
    var errorHandler = function(jqXHR) {
        cosmo.dialog.error('Erro', jqXHR.responseJSON[0]);
    };

    var configurationAjaxSend = cosmo.ajax.getDefaults();
    configurationAjaxSend.url = cosmo.urlbase + cosmo.routes_name.ADM_REGISTER_USER_UPDATE;
    configurationAjaxSend.method = 'POST';
    configurationAjaxSend.data = varFormRegister;
    configurationAjaxSend.type = 'json';
    configurationAjaxSend.sucess = returnSuccess;
    configurationAjaxSend.error = errorHandler;

    cosmo.ajax.send(configurationAjaxSend);

    return true;
};

window.cosmo = window.cosmo || {};
window.cosmo.frmupdateuser = window.cosmo.frmupdateuser || new frmUpdateUser();

$('document').ready(function(){
    window.cosmo.frmupdateuser.initialize();
});