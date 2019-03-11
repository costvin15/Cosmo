import "../../../../main";
import swal from "sweetalert";

class AdicionarAlunos {
    constructor(){
        (<HTMLButtonElement> document.getElementById("btn-adicionar-alunos")).addEventListener("click", () => {
            swal({
                title: "Adicionar alunos na turma",
                content: document.getElementById("alunos-lista-modal")
            }).then((content: any) => {
                this.save();
            });
        });
    }

    createObject(){
        let rows = (<HTMLTableElement> document.getElementById("alunos-lista-table")).rows;
        let result = [];
        for (let i = 1; i < rows.length; i++)
            if ((<HTMLInputElement> rows[i].cells[0].childNodes[0]).checked)
                result.push(rows[i].cells[2].innerHTML);
        return {
            "id": (<HTMLInputElement> document.getElementById("inputclass-id")).value,
            "students": result
        };
    }

    save(){
        let formObject = this.createObject();
        let success = (content: any) => {
            window.cosmo.dialog.success("Adicionar alunos na turma", content.message, () => {
                window.location.href = content.callback;
            });
        };
        let fail = (content: any) => {
            window.cosmo.dialog.error("Oops", content.responseJSON[0], () => {});
        };
        let ajax = window.cosmo.ajax.getDefaults();
        ajax.url = window.base_url + window.cosmo.routes_name.administrator_insert_students;
        ajax.method = "POST";
        ajax.data = formObject;
        ajax.type = "json";
        ajax.success = success;
        ajax.error = fail;
        window.cosmo.ajax.send(ajax);
    }
}

class AdicionarHabilidades {
    constructor(){
        (<HTMLButtonElement> document.getElementById("btn-adicionar-habilidade")).addEventListener("click", () => {
            swal({
                title: "Adicionar grupos de atividades na turma",
                content: document.getElementById("adicionar-habilidade-modal")
            }).then((content: any) => {
                this.save();
            });
        });
    }

    createObject(){
        let rows = (<HTMLTableElement> document.getElementById("adicionar-habilidade-table")).rows;
        let result = [];
        for (let i = 1; i < rows.length; i++)
            if ((<HTMLInputElement> rows[i].cells[0].childNodes[0]).checked)
                result.push(rows[i].cells[2].innerHTML);
        return {
            "id": (<HTMLInputElement> document.getElementById("inputclass-id")).value,
            "skills": result
        };
    }

    save(){
        let formObject = this.createObject();
        let success = (content: any) => {
            window.cosmo.dialog.success("Adicionar habilidades na turma", content.message, () => {
                window.location.href = content.callback;
            });
        };
        let fail = (content: any) => {
            console.log(content);
            window.cosmo.dialog.error("Oops", content.responseJSON[0], () => {});
        };
        let ajax = window.cosmo.ajax.getDefaults();
        ajax.url = window.base_url + window.cosmo.routes_name.administrator_insert_skills;
        ajax.method = "POST";
        ajax.data = formObject;
        ajax.type = "json";
        ajax.success = success;
        ajax.error = fail;
        window.cosmo.ajax.send(ajax);
    }
}

let adicionar_alunos = new AdicionarAlunos();
let adicionar_habilidades = new AdicionarHabilidades();