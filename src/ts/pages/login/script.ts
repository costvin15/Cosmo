class Login {
    constructor(){
        (<HTMLInputElement> document.getElementById("login-form")).addEventListener("submit", event => {
            event.preventDefault();
            if (this.validate())
                this.auth();
        });
    }

    createObject(){
        return {
            username: (<HTMLInputElement> document.getElementById("frmlogin-email")).value,
            password: (<HTMLInputElement> document.getElementById("frmlogin-password")).value
        }
    }

    validate() : boolean {
        let formObject = this.createObject();

        if (formObject.username.trim() === ""){
            window.cosmo.dialog.error("Oops", "O campo Email ou Nome de Usuário não pode ficar vazio.", () => {});
            return false;
        }

        if (formObject.password.trim() === ""){
            window.cosmo.dialog.error("Oops", "O campo Senha não pode ficar vazio.", () => {});
            return false;
        }

        if (formObject.password.length <= 3){
            window.cosmo.dialog.error("Oops", "A senha deve ter mais de 3 caracteres.", () => {});
            return false;
        }

        return true;
    }

    auth(){
        let formObject = this.createObject();
        let success = (content: any) => {
            window.location.href = content.callback;
        };
        let fail = (content: any) => {
            console.log(content);
            //if (content.responseJSON)
            //    if (content.responseJSON[0] && content.responseJSON["callback"])
            //        window.cosmo.dialog.error("Oops", content.responseJSON[0], () => {
            //            window.location.href = content.responseJSON["callback"];
            //        });
            //    else if(content.responseJSON[0])
            //        window.cosmo.dialog.error("Oops", content.responseJSON[0], () => {});
            //else
            //    window.cosmo.dialog.error("Oops", "Erro desconhecido", () => {});    
        };
        let ajax = window.cosmo.ajax.getDefaults();
        ajax.url = window.base_url + window.cosmo.routes_name.auth;
        ajax.method = "POST";
        ajax.type = "json";
        ajax.data = formObject;
        ajax.success = success;
        ajax.error = fail;
        window.cosmo.ajax.send(ajax);
    }
}

let login = new Login();