import "../../../main";

interface Activity {
    title: string;
    question: string;
    fullquestion: string;
    group: string;
    input_description: string;
    input: string;
    output_description: string;
    output: string;
    input_example: string;
    output_example: string;
}

abstract class AbstractActivity {
    createObject() : Activity {
        return {
            title: (<HTMLInputElement> document.getElementById("input-frmactivity-title")).value,
            question: (<HTMLInputElement> document.getElementById("input-frmactivity-question")).value,
            fullquestion: (<HTMLTextAreaElement> document.getElementById("input-frmactivity-fullquestion")).value,
            group: (<HTMLSelectElement> document.getElementById("input-frmactivity-group")).options[(<HTMLSelectElement> document.getElementById("input-frmactivity-group")).selectedIndex].text,
            input_description: (<HTMLInputElement> document.getElementById("input-frmactivity-description-input")).value,
            input: (<HTMLTextAreaElement> document.getElementById("input-frmactivity-input")).value,
            output_description: (<HTMLInputElement> document.getElementById("input-frmactivity-description-output")).value,
            output: (<HTMLTextAreaElement> document.getElementById("input-frmactivity-output")).value,
            input_example: (<HTMLTextAreaElement> document.getElementById("input-frmactivity-example-input")).value,
            output_example: (<HTMLTextAreaElement> document.getElementById("input-frmactivity-example-output")).value
        };
    }

    validate() : boolean {
        let formObject = this.createObject();

        if (formObject.title.trim() === ""){
            window.cosmo.dialog.error("Oops", "O campo Título não pode ficar vazio", () => {});
            return false;
        } else if (formObject.question.trim() === ""){
            window.cosmo.dialog.error("Oops", "O campo Descrição Curta não pode ficar vazio", () => {});
            return false;
        } else if (formObject.fullquestion.trim() === ""){
            window.cosmo.dialog.error("Oops", "O campo Descrição Completa não pode ficar vazio", () => {});
            return false;
        } else if (formObject.group.trim() === ""){
            window.cosmo.dialog.error("Oops", "O campo Grupo é obrigatório", () => {});
            return false;
        } else if (formObject.input_description.trim() === ""){
            window.cosmo.dialog.error("Oops", "O campo Descrição da Entrada não pode ficar vazio", () => {});
            return false;
        } else if (formObject.input.trim() === ""){
            window.cosmo.dialog.error("Oops", "O campo Entrada não pode ficar vazio", () => {});
            return false;
        } else if (formObject.output_description.trim() === ""){
            window.cosmo.dialog.error("Oops", "O campo Descrição da Saída não pode ficar vazio", () => {});
            return false;
        } else if (formObject.output.trim() === ""){
            window.cosmo.dialog.error("Oops", "O campo Saída não pode ficar vazio", () => {});
            return false;
        } else if (formObject.input_example.trim() === ""){
            window.cosmo.dialog.error("Oops", "O campo Exemplo de Entrada não pode ficar vazio", () => {});
            return false;
        } else if (formObject.output_example.trim() === ""){
            window.cosmo.dialog.error("Oops", "O campo Exemplo de Saída não pode ficar vazio", () => {});
            return false;
        }

        return true;
    }
}

class CreateActivity extends AbstractActivity {
    initialize(){
        if (<HTMLButtonElement> document.getElementById("btn-create-activity"))
            (<HTMLButtonElement> document.getElementById("btn-create-activity")).addEventListener("click", () => {
                if (this.validate())
                    this.save();
            });
    }

    save(){
        let formObject = this.createObject();
        let success = function(content: any){
            window.cosmo.dialog.success("Atividade", content.message, () => {
                window.location.href = content.callback;
            });
        };
        let fail = function(content: any){
            window.cosmo.dialog.error("Erro", content.responseJSON[0], () => {});
        };

        let ajax = window.cosmo.ajax.getDefaults();
        ajax.url = window.base_url + window.cosmo.routes_name.administrator_save_activity;
        ajax.method = "POST";
        ajax.data = formObject;
        ajax.type = "json";
        ajax.success = success;
        ajax.error = fail;
        window.cosmo.ajax.send(ajax);
    }
}

class UpdateActivity extends AbstractActivity {
    initialize(){
        if (<HTMLButtonElement> document.getElementById("btn-update-activity"))
            (<HTMLButtonElement> document.getElementById("btn-update-activity")).addEventListener("click", () => {
                if (this.validate())
                    this.save();
            });
    }

    save(){
        let formObject : any = this.createObject();
        formObject.id = (<HTMLInputElement> document.getElementById("input-frmactivity-id")).value;
        let success = function(content: any){
            window.cosmo.dialog.success("Atividade", content.message, () => {
                window.location.href = content.callback;
            });
        };
        let fail = function(content: any){
            window.cosmo.dialog.error("Erro", content.responseJSON[0], () => {});
        };

        let ajax = window.cosmo.ajax.getDefaults();
        ajax.url = window.base_url + window.cosmo.routes_name.administrator_save_activity;
        ajax.method = "POST";
        ajax.data = formObject;
        ajax.type = "json";
        ajax.success = success;
        ajax.error = fail;
        window.cosmo.ajax.send(ajax);
    }
}

let create_activity = new CreateActivity();
create_activity.initialize();

let update_activity = new UpdateActivity();
update_activity.initialize();