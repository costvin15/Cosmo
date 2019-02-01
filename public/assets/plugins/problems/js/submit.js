$(document).ready(function () {

    var dateIni, dateFim;;


    $('.btn-submit-activity').click(function () {
        var urldestiny = $('input[ name="destiny" ]').val();
        var urlreturnsucess = $('input[ name="urlreturnsucess" ]').val();
        var submitAction = {};

        // dateIni = new Date().getTime()

        submitAction.id_activity = $('input[ name="id-activity" ]').val();
        submitAction.source_coude = editor.getValue();
        submitAction.language = $('#editor-program-language option:selected').val();
        // submitAction.dateini = dateIni;
        // dateFim = new Date().getTime();
        // submitAction.datefim = dateFim;                    

        successFunction = function(retorno) {
            if (retorno.return) {
                swal({
                        title: "Meus Parabéns!",
                        html:   '<style>.br-wrapper{width: 43px; margin: 0 auto;}</style>' +
                        'A resposta está correta<br/>',
                        type: "success",
                        showCancelButton: false,
                        confirmButtonText: "Voltar",
                        allowEscapeKey: false,
                        allowOutsideClick: false,
                    }).then(function () {
                        window.location.href = urlreturnsucess;
                    });

                return;
            }

            swal({
                title: "Tente novamente!",
                text: retorno.message,
                type: "error",
                showCancelButton: false,
                confirmButtonText: "Voltar"
            });
        };

        var returnSuccess = successFunction;
        var errorHandler = function(jqXHR) {
            cosmo.dialog.error('Erro', jqXHR.responseJSON[0]);
        };

        var configurationAjaxSend = cosmo.ajax.getDefaults();
        configurationAjaxSend.url = urldestiny
        configurationAjaxSend.method = 'POST';
        configurationAjaxSend.data = submitAction;
        configurationAjaxSend.type = 'json';
        configurationAjaxSend.sucess = returnSuccess;
        configurationAjaxSend.error = errorHandler;

        cosmo.ajax.send(configurationAjaxSend);
    });
});
