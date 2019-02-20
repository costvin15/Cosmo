import "../../../main";

var casos_count = 1;

interface Activity {
    title: string;
    question: string;
    fullquestion: string;
    group: string;
    input_description: string;
    output_description: string;
    input_example: string;
    output_example: string;
    casos_testes: Array<Object>;
}

abstract class AbstractActivity {
    createObject() : Activity {
        let casos_testes = [];
        let i = 1;
        while (true){
            let caso: any = {};

            if ((<HTMLInputElement> document.getElementById("input-frmactivity-input-" + i)))
                caso.in = (<HTMLInputElement> document.getElementById("input-frmactivity-input-" + i)).value;
            else
                break;
            
            if ((<HTMLInputElement> document.getElementById("input-frmactivity-output-" + i)))
                caso.out = (<HTMLInputElement> document.getElementById("input-frmactivity-output-" + i)).value;
            else
                break;
            
            casos_testes[i - 1] = caso;
            i++;
        }

        return {
            title: (<HTMLInputElement> document.getElementById("input-frmactivity-title")).value,
            question: (<HTMLInputElement> document.getElementById("input-frmactivity-question")).value,
            fullquestion: (<HTMLTextAreaElement> document.getElementById("input-frmactivity-fullquestion")).value,
            group: (<HTMLSelectElement> document.getElementById("input-frmactivity-group")).options[(<HTMLSelectElement> document.getElementById("input-frmactivity-group")).selectedIndex].text,
            input_description: (<HTMLInputElement> document.getElementById("input-frmactivity-description-input")).value,
            output_description: (<HTMLInputElement> document.getElementById("input-frmactivity-description-output")).value,
            input_example: (<HTMLTextAreaElement> document.getElementById("input-frmactivity-example-input")).value,
            output_example: (<HTMLTextAreaElement> document.getElementById("input-frmactivity-example-output")).value,
            casos_testes: casos_testes
        };
    }

    validate() : boolean {
        let formObject = this.createObject();
        console.log(formObject.casos_testes.length);

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
        } else if (formObject.output_description.trim() === ""){
            window.cosmo.dialog.error("Oops", "O campo Descrição da Saída não pode ficar vazio", () => {});
            return false;
        } else if (formObject.input_example.trim() === ""){
            window.cosmo.dialog.error("Oops", "O campo Exemplo de Entrada não pode ficar vazio", () => {});
            return false;
        } else if (formObject.output_example.trim() === ""){
            window.cosmo.dialog.error("Oops", "O campo Exemplo de Saída não pode ficar vazio", () => {});
            return false;
        } else if (formObject.casos_testes.length == 0){
            window.cosmo.dialog.error("Oops", "Sua atividade precisa ter pelo menos um caso de teste", () => {});
            return false;
        }

        return true;
    }
}

class CreateActivity extends AbstractActivity {
    initialize(){
        if (<HTMLButtonElement> document.getElementById("btn-create-activity")){
            (<HTMLButtonElement> document.getElementById("btn-create-activity")).addEventListener("click", () => {
                if (this.validate())
                    this.save();
            });
        
            if (<HTMLButtonElement> document.getElementById("adicionar-caso-de-teste"))
                (<HTMLButtonElement> document.getElementById("adicionar-caso-de-teste")).addEventListener("click", () => {
                    let dom_parser = new DOMParser();
                    let caso_element = dom_parser.parseFromString(`<div class="caso-de-teste">
                                            <p class="caso-de-teste-count text-muted">Caso de teste ${casos_count}</p>
                                            <div class="form-group">
                                                <label for="input-frmactivity-input-${casos_count}">Entrada</label>
                                                <textarea id="input-frmactivity-input-${casos_count}" class="form-control" rows="3"></textarea>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="input-frmactivity-output-${casos_count}">Saída</label>
                                                <textarea id="input-frmactivity-output-${casos_count}" class="form-control" rows="3"></textarea>
                                            </div>
                                        </div>`, "text/html");
                    (<HTMLDivElement> document.getElementById("casos-container")).append(caso_element.body.firstChild);
                    casos_count++;
                });
        }
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
        if (<HTMLButtonElement> document.getElementById("btn-update-activity")){
            (<HTMLButtonElement> document.getElementById("btn-update-activity")).addEventListener("click", () => {
                if (this.validate())
                    this.save();
            });

            while ((<HTMLInputElement> document.getElementById("input-frmactivity-input-" + casos_count)))
                casos_count++;

            if (<HTMLButtonElement> document.getElementById("adicionar-caso-de-teste"))
                (<HTMLButtonElement> document.getElementById("adicionar-caso-de-teste")).addEventListener("click", () => {
                    let dom_parser = new DOMParser();
                    let caso_element = dom_parser.parseFromString(`<div class="caso-de-teste">
                                            <p class="caso-de-teste-count text-muted">Caso de teste ${casos_count}</p>
                                            <div class="form-group">
                                                <label for="input-frmactivity-input-${casos_count}">Entrada</label>
                                                <textarea id="input-frmactivity-input-${casos_count}" class="form-control" rows="3"></textarea>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="input-frmactivity-output-${casos_count}">Saída</label>
                                                <textarea id="input-frmactivity-output-${casos_count}" class="form-control" rows="3"></textarea>
                                            </div>
                                        </div>`, "text/html");
                    (<HTMLDivElement> document.getElementById("casos-container")).append(caso_element.body.firstChild);
                    casos_count++;
                });
        }
    }

    save(){
        let formObject : any = this.createObject();
        console.log(formObject);

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