import "../../../main";

interface GroupActivity {
    name: string;
    tags: string;
    visible: boolean;
}

abstract class AbstractGroup {
    createObject() : GroupActivity {
        return {
            name: (<HTMLInputElement> document.getElementById("input-frmgroup-title")).value,
            tags: (<HTMLInputElement> document.getElementById("input-frmgroup-tags")).value,
            visible: (<HTMLInputElement> document.getElementById("input-frmgroup-visible")).checked ? true:false
        };
    }

    validate() : boolean {
        let formObject = this.createObject();

        if (formObject.name.trim() === ""){
            window.cosmo.dialog.error("Oops", "O campo Título não pode ficar vazio", () => {});
            return false;
        } else if (formObject.tags.trim() === ""){
            window.cosmo.dialog.error("Oops", "O campo Tag não pode ficar vazio", () => {});
            return false;
        }

        return true;
    }
}

class CreateGroup extends AbstractGroup {
    initialize(){
        if (<HTMLButtonElement> document.getElementById("btn-create-group"))
            (<HTMLButtonElement> document.getElementById("btn-create-group")).addEventListener("click", () => {
                if (this.validate())
                    this.save();
            });
    }

    save(){
        let formObject = this.createObject();
        let success = function (content: any){
            window.cosmo.dialog.success("Cadatrado de Grupo de Atividades", content.message, () => {
                window.location.href = content.callback;
            });
        };
        let fail = function (content: any){
            window.cosmo.dialog.error("Erro", content.responseJSON[0], () => {});
        };

        let ajax = window.cosmo.ajax.getDefaults();
        ajax.url =  window.base_url + window.cosmo.routes_name.administrator_save_group_activity;
        ajax.method = "POST";
        ajax.data = formObject;
        ajax.type = "json";
        ajax.success = success;
        ajax.error = fail;
        window.cosmo.ajax.send(ajax);
    }
}

class UpdateGroup extends AbstractGroup {
    initialize(){
        if (<HTMLButtonElement> document.getElementById("btn-update-group"))
            (<HTMLButtonElement> document.getElementById("btn-update-group")).addEventListener("click", () => {
                if (this.validate())
                    this.save();
            });
    }

    save(){
        let formObject : any = this.createObject();
        formObject.id = (<HTMLInputElement> document.getElementById("input-frmgroup-id")).value
        let success = function(content: any){
            window.cosmo.dialog.success("Grupo de Atividades", content.message, () => {
                window.location.href = content.callback;
            });
        };
        let fail = function(content: any){
            window.cosmo.dialog.error("Erro", content.responseJSON[0], () => {});
        };

        let ajax = window.cosmo.ajax.getDefaults();
        ajax.url = window.base_url + window.cosmo.routes_name.administrator_save_group_activity;
        ajax.method = "POST";
        ajax.data = formObject;
        ajax.type = "json";
        ajax.success = success;
        ajax.error = fail;
        window.cosmo.ajax.send(ajax);
    }
}

let create_group = new CreateGroup();
create_group.initialize();
let update_group = new UpdateGroup();
update_group.initialize();