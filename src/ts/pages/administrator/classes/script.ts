import "../../../main";

interface Turma {
    title: string;
    code: string; 
}

abstract class AbstractTurma {
    createObject() : Turma {
        return {
            title: (<HTMLInputElement> document.getElementById("frmclass-title")).value,
            code: (<HTMLInputElement> document.getElementById("frmclass-code")).value
        };
    }

    validate() : boolean {
        let formObject = this.createObject();

        if (formObject.title.trim() === ""){
            window.cosmo.dialog.error("Oops", "O campo Título da Turma não pode ficar vazio.", () => {});
            return false;
        }

        if (formObject.code.trim() === ""){
            window.cosmo.dialog.error("Oops", "O campo Código da Turma não pode ficar vazio.", () => {});
            return false;
        }

        return true;
    }
}

class CreateTurma extends AbstractTurma {
    constructor(){
        super();

        if (<HTMLButtonElement> document.getElementById("frmclass-btn-create"))
            (<HTMLButtonElement> document.getElementById("frmclass-btn-create")).addEventListener("click", () => {
                if (this.validate())
                    this.save();
            });
        if (<HTMLButtonElement> document.getElementById("frmclass-btn-update"))
            (<HTMLButtonElement> document.getElementById("frmclass-btn-update")).addEventListener("click", () => {
                if (this.validate())
                    this.save();
            });
    }

    save(){
        let formObject : any = this.createObject();
        if (<HTMLButtonElement> document.getElementById("frmclass-btn-update"))
            formObject.id = (<HTMLInputElement> document.getElementById("frmclass-id")).value;

        let success = (content: any) => {
            window.cosmo.dialog.success("Turma", content.message, () => {
                window.location.href = content.callback;
            });
        };
        let fail = (content: any) => {
            console.error(content);
            window.cosmo.dialog.error("Oops", content.responseJSON[0], () => {});
        };

        let ajax = window.cosmo.ajax.getDefaults();
        ajax.url = window.base_url + window.cosmo.routes_name.administrator_save_class;
        ajax.method = "POST";
        ajax.data = formObject;
        ajax.type = "json";
        ajax.success = success;
        ajax.error = fail;
        window.cosmo.ajax.send(ajax);
    }
}

let create_turma = new CreateTurma();
