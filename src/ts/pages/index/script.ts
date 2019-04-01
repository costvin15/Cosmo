function extraInformationsAlert(){
    let success = (content: any) => {
        if (content.sexo == "" || content.sexo == null )
            window.cosmo.dialog.info("Alerta", "Complete seu perfil", () => {
                window.location.href = window.base_url + window.cosmo.routes_name.user_update_profile_view
            });
        };
    let fail = (content: any) => {};
    let ajax = window.cosmo.ajax.getDefaults();
    ajax.url = window.base_url + window.cosmo.routes_name.get_informations;
    ajax.method = "POST";
    ajax.type = "json";
    ajax.success = success;
    ajax.error = fail;
    window.cosmo.ajax.send(ajax);
}

extraInformationsAlert();