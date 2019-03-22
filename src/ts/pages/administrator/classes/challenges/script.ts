import swal from "sweetalert";
import "../../../../main";

class CreateChallenge {
    count: number;

    constructor(){
        this.count = 0;
        if ((<HTMLButtonElement> document.getElementById("adicionar-questao-btn"))){
            (<HTMLButtonElement> document.getElementById("adicionar-questao-btn")).addEventListener("click", () => {
                swal({
                    title: "Adicionar questão",
                    content: document.getElementById("adicionar-questao-modal")
                }).then(() => {
                    let question: any = {
                        level: {
                            title: (<HTMLSelectElement> document.getElementById("frm-challenge-level")).options[(<HTMLSelectElement> document.getElementById("frm-challenge-level")).selectedIndex].text,
                            number: (<HTMLSelectElement> document.getElementById("frm-challenge-level")).value
                        },
                        question: {
                            title: (<HTMLSelectElement> document.getElementById("frm-challenge-question")).options[(<HTMLSelectElement> document.getElementById("frm-challenge-question")).selectedIndex].text,
                            id: (<HTMLSelectElement> document.getElementById("frm-challenge-question")).value
                        }
                    };
                    this.appendQuestion(question);
                });
            });
            (<HTMLButtonElement> document.getElementById("create-challenge-btn")).addEventListener("click", () => {
                if (this.validate())
                    this.save();
            });
        }
    }

    createObject(){
        let questions: Array<any> = new Array<any>();
        let questions_container = (<HTMLDivElement> document.getElementById("questions-container")).children;
        for (let i = 0; i < questions_container.length; i++)
            questions.push({
                id: (<HTMLInputElement> document.getElementById(questions_container[i].id + "-id")).value,
                level: (<HTMLInputElement> document.getElementById(questions_container[i].id + "-level")).value
            });
        
        return {
            class: (<HTMLInputElement> document.getElementById("frm-class-id")).value,
            title: (<HTMLInputElement> document.getElementById("frm-challenge-title")).value,
            opening: (<HTMLInputElement> document.getElementById("frm-challenge-opening")).value,
            validity: (<HTMLInputElement> document.getElementById("frm-challenge-validity")).value,
            type: (<HTMLSelectElement> document.getElementById("frm-challenge-type")).value,
            questions: questions
        };
    }

    validate() : boolean {
        let formObject = this.createObject();

        if (formObject.title.trim() === ""){
            window.cosmo.dialog.error("Oops", "O campo Título é obrigatório.", () => {});
            return false;
        }

        if (formObject.questions.length == 0){
            window.cosmo.dialog.error("Oops", "É necessário pelo menos uma questão.", () => {});
            return false;
        }
        
        return true;
    }

    appendQuestion(question: any){
        let question_element = new DOMParser().parseFromString(`<div id="question-${this.count}" class="col-md-4">
            <div class="card">
                <input id="question-${this.count}-id" type="hidden" value="${question.question.id}"></input>
                <input id="question-${this.count}-level" type="hidden" value="${question.level.number}"></input>

                <div class="card-header">
                    <h5 class="card-title mb-0">${question.question.title}</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">Nível: ${question.level.title}</p>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-12">
                            <button id="${this.count}" type="button" class="btn-question-remove btn btn-danger w-100">Remover</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>`, "text/html");
        (<HTMLDivElement> document.getElementById("questions-container")).append(question_element.firstChild.lastChild.firstChild);
        (<HTMLButtonElement> document.getElementById(this.count.toString())).addEventListener("click", function(event: any){
            (<HTMLDivElement> document.getElementById("question-" + event.target.id)).remove();
        });
        this.count++;
    }

    save(){
        let formObject = this.createObject();
        let success = (content: any) => {
            window.cosmo.dialog.success("Questão desafio", content.message, () => {
                window.location.href = content.callback;
            });
        };
        let fail = (content: any) => {
            window.cosmo.dialog.error("Questão desafio", content.message, () => {});
        };
        let ajax = window.cosmo.ajax.getDefaults();
        ajax.url = window.base_url + window.cosmo.routes_name.administrator_save_challenge_class;
        ajax.method = "POST";
        ajax.data = formObject;
        ajax.type = "json";
        ajax.success = success;
        ajax.error = fail;
        window.cosmo.ajax.send(ajax);
    }
}

class UpdateChallenge {
    constructor(){
        
    }
}

let create_challenge = new CreateChallenge();
let update_challenge = new UpdateChallenge();