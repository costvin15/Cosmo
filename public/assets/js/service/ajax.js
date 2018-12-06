/**
 * Object send ajax
 * @returns {boolean}
 * @constructor
 */
function AjaxSend() {
    return true;
}


/**
 * Get Attributes Default Ajax 2.0
 * @returns {{method: string, url: string, data: {}, type: string, sucess: undefined, show: undefined, hide: undefined, error: undefined, block: boolean, processData: boolean, cache: boolean, target: undefined, contentType: string}}
 */
AjaxSend.prototype.getDefaults = function() {
    return {
        method: "POST",     url: "",
        data: {},           type: "json",
        sucess: undefined,  show: undefined,
        hide: undefined,    error: undefined,
        block: false,       processData: true,
        cache: true,        target: undefined,
        contentType: "application/x-www-form-urlencoded; charset=UTF-8",
        async: true
    };
};

/**
 * Send Ajax
 * @param configuration
 */
AjaxSend.prototype.send = function(configuration) {
    var option_default = this.getDefaults();
    var settings = $.extend(option_default, configuration);

    $.ajax({
        type: settings.method,
        url: settings.url,
        data: settings.data,
        dataType: settings.type,
        contentType: settings.contentType,
        processData: settings.processData,
        async: settings.async,
        cache: settings.cache,
        beforeSend: function () {
            if (settings.show === undefined) {
                if (!settings.block) {
                    if (settings.target !== undefined) {
                        settings.target.block({message: $('#sigu-loader-modal').html(), baseZ: 9999});
                    } else {
                        $.blockUI({message: $('#sigu-loader-modal').html(), baseZ: 9999});
                    }
                }
            } else {
                settings.show();
            }
        }
    }).done(function(retorno) {
        if (settings.show === undefined) {
            if (settings.target !== undefined) {
                settings.target.unblock();
            } else {
                $.unblockUI();
            }
        } else {
            settings.hide();
        }
        settings.sucess(retorno);
    }).fail(function (jqXHR, textStatus, errorMessage) {
        console.log("Erro de Requisição", "error", errorMessage, "");
        console.log(textStatus);
        console.log(jqXHR);
        if (settings.show === undefined) {
            if (settings.target !== undefined) {
                settings.target.unblock();
            } else {
                $.unblockUI();
            }
        } else {
            settings.hide();
        }

        if (settings.error !== undefined)  {
            settings.error(jqXHR);
        } else {
            cosmo.dialog.error('Erro', jqXHR.responseJSON);
        }
    });

    return true;
};

window.cosmo = window.cosmo || {};
window.cosmo.ajax = window.cosmo.ajax || new AjaxSend();