class Login {
    constructor(){
        (<HTMLInputElement> document.getElementById("btnLogin")).addEventListener("click", () => {
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

    validateEmail(email: string) : boolean {
        let expression = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return expression.test(email);
    }

    validate() : boolean {
        let formObject = this.createObject();

        if (formObject.username.trim() === ""){
            window.cosmo.dialog.error("Oops", "O campo Email não pode ficar vazio.", () => {});
            return false;
        }

        if (!this.validateEmail(formObject.username)){
            window.cosmo.dialog.error("Oops", "O Email informado é inválido.", () => {});
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
            if (content.responseJSON["callback"])
                window.cosmo.dialog.error("Oops", content.responseJSON[0], () => {
                    window.location.href = content.responseJSON["callback"];
                });
            else
                window.cosmo.dialog.error("Oops", content.responseJSON[0], () => {});
        };
        let ajax = window.cosmo.ajax.getDefaults();
        ajax.url = window.cosmo.routes_name.auth;
        ajax.method = "POST";
        ajax.type = "json";
        ajax.data = formObject;
        ajax.success = success;
        ajax.error = fail;
        window.cosmo.ajax.send(ajax);
    }
}

let login = new Login();