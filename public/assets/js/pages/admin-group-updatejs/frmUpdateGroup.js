function frmCreateGroup(){}

frmCreateGroup.prototype.initialize = function(){
    $("#btn-update-group").on("click", function(){
        if (cosmo.frmcreategroup.validate())
            cosmo.frmcreategroup.save();
        return false;
    });
};

frmCreateGroup.prototype.createObject = function(){
    var formRegister = {};
    formRegister.id = $("#input-frmactivity-id").val();
    formRegister.name = $("#input-frmgroup-title").val();
    formRegister.visible = $("#input-frmgroup-visible:checked").lenght;
    formRegister.tags = $("#input-frmgroup-tags").val();
    return formRegister;
};

frmCreateGroup.prototype.validate = function(){
    var formRegister = this.createObject();

    if (formRegister.name.trim() === ""){
        cosmo.dialog.error("Erro", "O campo Título é obrigatório");
        return false;
    } else if(formRegister.tags.trim() === ""){
        cosmo.dialog.error("Erro", "O campo Tags é obrigatório");
        return false;
    }

    return true;
};

frmCreateGroup.prototype.save = function(){
    var formRegister = this.createObject();

    var success = function(data){
        cosmo.dialog.success("Atualização de Grupo de Atividades!", data.message, function(){
            window.location.href = data.callback;
        });
    };

    var error = function(data){
        cosmo.dialog.error("Erro", data.responseJSON[0]);
    };

    var configuration_ajax = cosmo.ajax.getDefaults();
    configuration_ajax.url = cosmo.urlbase + cosmo.routes_name.ADM_REGISTER_GROUP_ACTIVITY_SAVE;
    configuration_ajax.method = "POST";
    configuration_ajax.data = formRegister;
    configuration_ajax.type = "json";
    configuration_ajax.sucess = success;
    configuration_ajax.error = error;
    cosmo.ajax.send(configuration_ajax);
};

window.cosmo = window.cosmo || {};
window.cosmo.frmcreategroup = window.cosmo.frmcreategroup || new frmCreateGroup();

$("document").ready(function(){
    window.cosmo.frmcreategroup.initialize();
});