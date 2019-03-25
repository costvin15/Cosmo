import "../../main";

class CreatePVP {
    initialize(){
        (<HTMLButtonElement> document.getElementById("btn-form-send")).addEventListener("click", () => {
            this.save();
        });
    }

    createObject(){
        return {
            challenged: (<HTMLSelectElement> document.getElementById("form-challenged")).value,
            activity: (<HTMLSelectElement> document.getElementById("form-activity")).value
        };
    }

    save(){
        let formObject = this.createObject();
        let success = (content: any) => {
            window.cosmo.dialog.success("PVP", content.message, () => {
                location.href = content.callback;
            });
        };
        let fail = (content: any) => {
            console.log(content);
            window.cosmo.dialog.error("Oops", content.message, () => {});
        };
        let ajax = window.cosmo.ajax.getDefaults();
        ajax.url = window.base_url + window.cosmo.routes_name.submit_pvp;
        ajax.method = "POST";
        ajax.data = formObject;
        ajax.type = "json";
        ajax.success = success;
        ajax.error = fail;
        window.cosmo.ajax.send(ajax);
    }
}

let create_pvp = new CreatePVP();
create_pvp.initialize();