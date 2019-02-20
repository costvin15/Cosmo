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
            language: (<HTMLInputElement> document.getElementById("language-selector")).value
        };
    }

    send(){
        let formObject = this.createObject();
        let success = (content: any) => {
            if (content.return)
                window.cosmo.dialog.success("Meus Parabéns", "A resposta está correta", () => {
                    window.location.href = window.cosmo.routes_name.dashboard_index
                });
            else
                window.cosmo.dialog.error("Tente novamente!", content.message, () => {});
        };
        let fail = (content: any) => {
            window.cosmo.dialog.error("Erro", content.responseJSON[0], () => {});
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

let plugin_problems = new PluginProblems();