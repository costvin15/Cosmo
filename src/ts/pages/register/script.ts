import "../../main";

class Register {
    constructor(){
        (<HTMLInputElement> document.getElementById("frmregister-email")).addEventListener("blur", (event: any) => {
            this.validateUsername(event.target.value);
        });

        (<HTMLInputElement> document.getElementById("btnCreateAccount")).addEventListener("click", () => {
            if (this.validate())
                this.save();
        });
    }

    createObject(){
        return {
            username: (<HTMLInputElement> document.getElementById("frmregister-email")).value,
            fullname: (<HTMLInputElement> document.getElementById("frmregister-fullname")).value,
            password: (<HTMLInputElement> document.getElementById("frmregister-password")).value,
            nickname: (<HTMLInputElement> document.getElementById("frmregister-nickname")).value,
            code: (<HTMLInputElement> document.getElementById("frmregister-class")).value,
            
        };
    }

    validateUsername(username: string) : boolean {
        (<HTMLInputElement> document.activeElement).blur();

        if (!this.validateEmail(username))
            return false;
        let data = {
            username: username
        };
        let fail = (content: any) => {
            window.cosmo.dialog.error("Erro", content.responseJSON[0], () => {});
            return false;
        }
        
        let ajax = window.cosmo.ajax.getDefaults();
        ajax.url = window.base_url + window.cosmo.routes_name.available_username;
        ajax.method = "GET";
        ajax.type = "json";
        ajax.success = () => {};
        ajax.error = fail;
        ajax.data = data;

        window.cosmo.ajax.send(ajax);

        return true;
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

        if (formObject.fullname.trim() === ""){
            window.cosmo.dialog.error("Oops", "O campo Nome Completo não pode ficar vazio.", () => {});
            return false;
        }

        if (formObject.nickname.trim() === ""){
            window.cosmo.dialog.error("Oops", "O campo Nome de Usuário não pode ficar vazio.", () => {});
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

    save(){
        let formObject = this.createObject();
        let success = (content: any) => {
            window.cosmo.dialog.success("Seja bem vindo!", content.message, () => {
                window.location.href = content.callback;
            });
        };
        let fail = (content: any) => {
            window.cosmo.dialog.error("Erro", content.responseJSON[0], () => {});
        };

        let ajax = window.cosmo.ajax.getDefaults();
        ajax.url = window.base_url + window.cosmo.routes_name.register_user;
        ajax.method = "POST";
        ajax.type = "json";
        ajax.data = formObject;
        ajax.success = success;
        ajax.error = fail;

        window.cosmo.ajax.send(ajax);
    }
}

let register = new Register();