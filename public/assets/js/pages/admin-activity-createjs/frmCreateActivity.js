function frmCreateActivity(){}

frmCreateActivity.prototype.initialize = function(){
    $("#btn-create-activity").on("click", function(){
        if (cosmo.frmcreateactivity.validate())
            cosmo.frmcreateactivity.save();
        return false;
    });
};

frmCreateActivity.prototype.createObject = function(){
    var formRegister = {};
    formRegister.title = $("#input-frmactivity-title").val();
    formRegister.question = $("#input-frmactivity-question").val();
    formRegister.fullquestion = $("#input-frmactivity-fullquestion").val();
    formRegister.group = $("#input-frmactivity-group").children("option:selected").val();
    formRegister.input_description = $("#input-frmactivity-description-input").val();
    formRegister.input_example = $("#input-frmactivity-example-input").val();
    formRegister.output_description = $("#input-frmactivity-description-output").val();
    formRegister.output_example = $("#input-frmactivity-example-output").val();
    formRegister.input = $("#input-frmactivity-input").val();
    formRegister.output = $("#input-frmactivity-output").val();

    return formRegister;
};

frmCreateActivity.prototype.validate = function(){
    var formRegister = this.createObject();

    if (formRegister.input_description === "")
        formRegister.input_description = "Não há entrada.";
    if (formRegister.input_example === "")
        formRegister.input_example = "Não há entrada.";

    if (formRegister.title.trim() === ""){
        cosmo.dialog.error("Erro", "O campo Título é obrigatório");
        return false;
    } else if (formRegister.question.trim() === ""){
        cosmo.dialog.error("Erro", "O campo Descrição Curta é obrigatório");
        return false;
    } else if (formRegister.fullquestion.trim() === ""){
        cosmo.dialog.error("Erro", "O campo Descrição é obrigatório");
        return false;
    } else if (formRegister.group.trim() === ""){
        cosmo.dialog.error("Erro", "O campo Categoria é obrigatório");
        return false;
    } else if (formRegister.output_description === ""){
        cosmo.dialog.error("Erro", "O campo Descrição de saída é obrigatório");
        return false;
    } else if (formRegister.output_example === ""){
        cosmo.dialog.error("Erro", "O campo Saída de Exemplo é obrigatório");
        return false;
    } else if (formRegister.output === ""){
        cosmo.dialog.error("Erro", "O campo Saída é obrigatório");
        return false;
    }

    return true;
};

frmCreateActivity.prototype.save = function(){
    var formRegister = this.createObject();

    var success = function(data){
        cosmo.dialog.success("Cadastro de Atividade!", data.message, function(){
            window.location.href = data.callback;
        });
    };

    var error = function(data){
        cosmo.dialog.error("Erro", data.responseJSON[0]);
    };

    var configuration_ajax = cosmo.ajax.getDefaults();
    configuration_ajax.url = cosmo.urlbase + cosmo.routes_name.ADM_REGISTER_ACTIVITY_SAVE;
    configuration_ajax.method = "POST";
    configuration_ajax.data = formRegister;
    configuration_ajax.type=  "json";
    configuration_ajax.sucess = success;
    configuration_ajax.error = error;
    cosmo.ajax.send(configuration_ajax);
    return true;
};

window.cosmo = window.cosmo || {};
window.cosmo.frmcreateactivity = window.cosmo.frmcreateactivity || new frmCreateActivity();

$("document").ready(function(){
    window.cosmo.frmcreateactivity.initialize();
});