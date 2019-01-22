function frmProfileUser(){}

frmProfileUser.prototype.initialize = function(){
    $("#btn-update-profile").on("click", function(){
        return cosmo.frmprofileuser.validate() && cosmo.frmprofileuser.save(), !1;
    });

    $("#btn-alter-image").click(function(){
        cosmo.frmprofileuser.wrapImage();
    });
    
    $("#input-frmuser-fileupload").on("change", function(){
        cosmo.frmprofileuser.loadAvatar();
    });

    $("#btn-close-profile").click(function(){
        cosmo.dialog.confirm("Encerar sua conta", "Você tem certeza que deseja encerrar sua conta? Essa ação é irreversível.", (result) => {
            cosmo.frmprofileuser.close_account();
        });
    });
};

frmProfileUser.prototype.createObject = function(){
    var e = {};
    e.id = $("#input-hidden-id").val();
    e.nickname = $("#input-frmuser-nickname").val();
    e.avatar = $("#img-frmuser-avatar").attr("src");
    e.fullname = $("#input-frmuser-name").val();    
    return e;
};

frmProfileUser.prototype.wrapImage = function(){
    $("#input-frmuser-fileupload").click();
};

frmProfileUser.prototype.validate = function(){
    var element = this.createObject();
    console.log(JSON.stringify(element));
    return "" === element.fullname.trim() ? (cosmo.dialog.error("Erro", "O campo Nome Completo é obrigatório"), !1) : "" === element.nickname.trim() ? (cosmo.dialog.error("Erro", "O campo Nome de Usuário é obrigatório"), !1) : this.isDataUrl(element.avatar) || (cosmo.dialog.error("Erro", "O campo Imagem é obrigatório!"), !1);
};

frmProfileUser.prototype.loadAvatar = function(){
    var element = new FileReader;
    var expression = /^(?:image\/bmp|image\/cis\-cod|image\/gif|image\/ief|image\/jpeg|image\/jpeg|image\/jpeg|image\/pipeg|image\/png|image\/svg\+xml|image\/tiff|image\/x\-cmu\-raster|image\/x\-cmx|image\/x\-icon|image\/x\-portable\-anymap|image\/x\-portable\-bitmap|image\/x\-portable\-graymap|image\/x\-portable\-pixmap|image\/x\-rgb|image\/x\-xbitmap|image\/x\-xpixmap|image\/x\-xwindowdump)$/i;
    var image_uploaded = $("#input-frmuser-fileupload").get(0).files[0];
    
    if (!expression.test(image_uploaded.type))
        return void cosmo.dialog.error("Erro", "O arquivo selecionado é inválido!");
    element.onload = function(object){
        var image = new Image;
        image.onload = function(){
            var canvas = document.createElement("canvas");
            canvas.width = 96;
            canvas.height = 96;
            canvas.getContext("2d").drawImage(this, 0, 0, 100, 100);
            $("#img-frmuser-avatar").attr("src", canvas.toDataURL());
        };
        image.src = object.target.result;
    };
    $("#input-frmuser-fileupload").get(0).files.length !== 0 && element.readAsDataURL(image_uploaded);
};

frmProfileUser.prototype.isDataUrl = function(object){
    var expression = /^\s*data:([a-z]+\/[a-z0-9\-\+]+(;[a-z\-]+\=[a-z0-9\-]+)?)?(;base64)?,[a-z0-9\!\$\&\'\,\(\)\*\+\,\;\=\-\.\_\~\:\@\/\?\%\s]*\s*$/i;
    return object.match(expression);
};

frmProfileUser.prototype.save = function(){
    var element = this.createObject();
    var success = function(content){
        cosmo.dialog.success("Atualização de usuário!", content.message, function(){
            window.location.href = content.callback;
        });
    };
    var error = function(content){
        cosmo.dialog.error("Erro", content.responseJSON[0]);
    };
    var ajax_defaults = cosmo.ajax.getDefaults();
    return ajax_defaults.url = cosmo.urlbase + cosmo.routes_name.USER_UPDATE_PROFILE, ajax_defaults.method="POST", ajax_defaults.data = element, ajax_defaults.type = "json", ajax_defaults.sucess = success, ajax_defaults.error = error, cosmo.ajax.send(ajax_defaults), !0;
};

frmProfileUser.prototype.close_account = function(){
    var success = function(content){
        cosmo.dialog.success("Sentiremos sua falta!", content.message, function(){
            window.location.href = content.callback;
        });  
    };

    var error = function(content){
        cosmo.dialog.error("Erro", content.responseJSON[0]);
    };

    var ajax = cosmo.ajax.getDefaults();
    ajax.url = cosmo.urlbase + cosmo.routes_name.USER_CLOSE_PROFILE;
    ajax.method = "POST";
    ajax.type= "json";
    ajax.sucess = success;
    ajax.error = error;
    cosmo.ajax.send(ajax);
};

window.cosmo = window.cosmo || {};
window.cosmo.frmprofileuser = window.cosmo.frmprofileuser || new frmProfileUser;
$("document").ready(function(){
   window.cosmo.frmprofileuser.initialize();
});