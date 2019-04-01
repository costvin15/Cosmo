import "../../main";
import * as ace from "brace";
import "brace/mode/lua";
import "brace/mode/c_cpp";
import "brace/mode/python";
import "brace/mode/java";
import "brace/theme/monokai";

let editor = ace.edit("editor");
editor.getSession().setMode('ace/mode/lua');
editor.setTheme('ace/theme/monokai');

class PluginProblems {
    editor: any;

    constructor(){
        this.editor = ace.edit("editor");
        this.editor.setTheme("ace/theme/monokai");
        this.editor.getSession().setMode("ace/mode/lua");

        (<HTMLInputElement> document.getElementById("language-selector")).addEventListener("change", (event: any) => {
            switch(event.target.value){
                case "lua":
                    this.editor.getSession().setMode("ace/mode/lua");
                    break;
                case "cpp":
                    this.editor.getSession().setMode("ace/mode/c_cpp");
                    break;
                case "python":
                    this.editor.getSession().setMode("ace/mode/python");
                    break;
            }
        });

        (<HTMLInputElement> document.getElementById("submit-activity")).addEventListener("click", () => {
            this.send();
        });
    }

    createObject(){
        return {
            id_activity: (<HTMLInputElement> document.getElementById("id-activity")).value,
            source_code: this.editor.getValue(),
            language: (<HTMLInputElement> document.getElementById("language-selector")).value,
            id_group: (<HTMLInputElement> document.getElementById("id-group")).value,
        };
    }

    send(){
        let formObject : any = this.createObject();
        if (<HTMLInputElement> document.getElementById("id-challenge"))
            formObject.challenge = (<HTMLInputElement> document.getElementById("id-challenge")).value;

        let success = (content: any) => {
            if (content.return){
                if(content.star){
                    let time = "";
                    if(content.time)
                        time = "Com o tempo de "+content.time+" s";
                    window.cosmo.dialog.success("Estrela "+content.star, "A resposta está correta e você ganhou a estrela de "+
                    content.star+". "+time, () => {
<<<<<<< HEAD
                        window.location.href = window.base_url + window.history.back();
                    });
                } else
                    window.cosmo.dialog.success("Meus Parabéns", "A resposta está correta", () => {
                        window.location.href = window.base_url + window.history.back();
=======
                        window.history.back()
                    });
                } else
                    window.cosmo.dialog.success("Meus Parabéns", "A resposta está correta", () => {
                        window.history.back()
>>>>>>> daniel
                    });
            }else
                window.cosmo.dialog.error("Tente novamente!", content.message, () => {});
        };
        let fail = (content: any) => {
            if (content && content.message)
                window.cosmo.dialog.error("Erro", content.message, () => {});
            else
                console.log(content);
        };

        let ajax = window.cosmo.ajax.getDefaults();
        ajax.url = window.base_url + window.cosmo.routes_name.submit_activity;
        ajax.method = "POST";
        ajax.type = "json";
        ajax.success = success;
        ajax.error = fail;
        ajax.data = formObject;

        window.cosmo.ajax.send(ajax);
    }
}

class PluginChallenges {
    editor: any;

    constructor(){
        this.editor = ace.edit("editor");
        this.editor.setTheme("ace/theme/monokai");
        this.editor.getSession().setMode("ace/mode/lua");

        (<HTMLInputElement> document.getElementById("language-selector")).addEventListener("change", (event: any) => {
            switch(event.target.value){
                case "lua":
                    this.editor.getSession().setMode("ace/mode/lua");
                    break;
                case "cpp":
                    this.editor.getSession().setMode("ace/mode/c_cpp");
                    break;
                case "python":
                    this.editor.getSession().setMode("ace/mode/python");
                    break;
            }
        });

        (<HTMLInputElement> document.getElementById("submit-challenge")).addEventListener("click", () => {
            this.send();
        });
    }

    createObject(){
        return {
            id_activity: (<HTMLInputElement> document.getElementById("id-activity")).value,
            id_group: (<HTMLInputElement> document.getElementById("id-group")).value,
            source_code: this.editor.getValue(),
            language: (<HTMLInputElement> document.getElementById("language-selector")).value,
            type: (<HTMLInputElement> document.getElementById("input-frmactivity-type")).value,
            challenge_id: (<HTMLInputElement> document.getElementById("input-frmactivity-challenge-id")).value,
            level: (<HTMLInputElement> document.getElementById("input-frmactivity-level")).value
        };
    }

    send(){
        let formObject = this.createObject();
        let success = (content: any) => {
            if (content.return)
                window.cosmo.dialog.success("Desafio concluído", content.message, () => {
                    window.location.href = window.base_url + window.cosmo.routes_name.dashboard_index
                });
            else
                window.cosmo.dialog.error("Tente novamente!", content.message, () => {});
        };
        let fail = (content: any) => {
            window.cosmo.dialog.error("Erro", content.message, () => {});
        };
        let ajax = window.cosmo.ajax.getDefaults();
        ajax.url = window.base_url + window.cosmo.routes_name.submit_activity;
        ajax.method = "POST";
        ajax.type = "json";
        ajax.success = success;
        ajax.error = fail;
        ajax.data = formObject;
        window.cosmo.ajax.send(ajax);
    }
}

let plugin_problems;
if ((<HTMLInputElement> document.getElementById("submit-activity")))
    plugin_problems = new PluginProblems();
if ((<HTMLInputElement> document.getElementById("submit-challenge")))
    plugin_problems = new PluginChallenges();