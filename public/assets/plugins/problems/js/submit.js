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
                        'A resposta está correta<br/>' +
                        '<div style="text-align: center">' +
                        '       <select class="rating-bar-type-a">' +
                        '           <option value="1">1</option><option value="2">2</option><option value="3">3</option>' +
                        '       </select>' +
                        '       <br />' +
                        '       <small>Dificuldade</small>' +
                        '</div><br/>' +
                        '<a href="#" class="btn btn-lg btn-like btn-primary"><span class="glyphicon glyphicon-thumbs-up"></span></a>' +
                        '<a href="#" class="btn btn-lg btn-deslike btn-danger"><span class="glyphicon glyphicon-thumbs-down"></span></a>',
                        type: "success",
                        showCancelButton: false,
                        confirmButtonText: "Voltar",
                        allowEscapeKey: false,
                        allowOutsideClick: false,
                        onOpen: function() {
                            $('.rating-bar-type-a').barrating({
                                theme: 'bars-1to10'
                            });

                            $(".rating-bar-type-a").on("change", function(){
                                var urldestiny = $('input[ name="destiny" ]').val();
                                var submitAction = {};

                                submitAction.id_activity = $('input[ name="id-activity" ]').val();
                                submitAction.source_coude = editor.getValue();
                                submitAction.difficulty = $(this).val();

                                successFunction = function(retorno) {
                                    console.log('Salvou historico indicando dificuldade.');
                                };

                                sendAjax('POST', urldestiny, submitAction, 'json', successFunction);
                            });

                            $('.btn-like').click(function(e) {
                                e.preventDefault();
                                var urldestiny = $('input[ name="destiny" ]').val();
                                var submitAction = {};

                                submitAction.id_activity = $('input[ name="id-activity" ]').val();
                                submitAction.source_coude = editor.getValue();
                                submitAction.classification = 1;

                                successFunction = function(retorno) {
                                    console.log('Salvou historico indicando like.');
                                };

                                sendAjax('POST', urldestiny, submitAction, 'json', successFunction);
                            });

                            $('.btn-deslike').click(function(e) {
                                e.preventDefault();
                                var urldestiny = $('input[ name="destiny" ]').val();
                                var submitAction = {};

                                submitAction.id_activity = $('input[ name="id-activity" ]').val();
                                submitAction.source_coude = editor.getValue();
                                submitAction.classification = 0;

                                successFunction = function(retorno) {
                                    console.log('Salvou historico indicando deslike.');
                                };

                                sendAjax('POST', urldestiny, submitAction, 'json', successFunction);
                            });
                        }
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
