import "bootstrap-table";
import "../../../../../node_modules/bootstrap-table/dist/locale/bootstrap-table-pt-BR";
import "../../../main";

abstract class AbstractUser {
    loadAvatar(){
        let image_result = new FileReader();
        let expression = /^(?:image\/bmp|image\/cis\-cod|image\/gif|image\/ief|image\/jpeg|image\/jpeg|image\/jpeg|image\/pipeg|image\/png|image\/svg\+xml|image\/tiff|image\/x\-cmu\-raster|image\/x\-cmx|image\/x\-icon|image\/x\-portable\-anymap|image\/x\-portable\-bitmap|image\/x\-portable\-graymap|image\/x\-portable\-pixmap|image\/x\-rgb|image\/x\-xbitmap|image\/x\-xpixmap|image\/x\-xwindowdump)$/i;
        let image_uploaded = (<HTMLInputElement> document.getElementById("input-frmuser-fileupload")).files[0];

        if (!expression.test(image_uploaded.type)){
            window.cosmo.dialog.error("Oops", "O arquivo selecionado é inválido.", () => {});
            return false;
        }

        image_result.onload = (object: any) => {
            let image = new Image();
            image.src = object.target.result;
            image.onload = () => {
                let canvas = document.createElement("canvas");
                canvas.width = image.width;
                canvas.height = image.height;
                canvas.getContext("2d").drawImage(image, 0, 0, image.width, image.height);
                (<HTMLImageElement> document.getElementById("img-frmuser-avatar")).setAttribute("src", canvas.toDataURL());
            };
        };

        if ((<HTMLInputElement> document.getElementById("input-frmuser-fileupload")).files.length !== 0)
            image_result.readAsDataURL(image_uploaded);
    }

    wrapImage(){
        (<HTMLButtonElement> document.getElementById("input-frmuser-fileupload")).click();
    }
};

class CreateUser extends AbstractUser {
    initialize(){
        if (<HTMLButtonElement> document.getElementById("btn-create-profile"))
            (<HTMLButtonElement> document.getElementById("btn-create-profile")).addEventListener("click", () => {
                if (this.validate())
                    this.save();
            });
        
        if (<HTMLButtonElement> document.getElementById("btn-alter-image"))
            (<HTMLButtonElement> document.getElementById("btn-alter-image")).addEventListener("click", () => {
                this.wrapImage();    
            });

        if (<HTMLButtonElement> document.getElementById("input-frmuser-fileupload"))
            (<HTMLButtonElement> document.getElementById("input-frmuser-fileupload")).addEventListener("change", () => {
                this.loadAvatar();
            });
    }

    createObject(){
        return {
            avatar: (<HTMLImageElement> document.getElementById("img-frmuser-avatar")).getAttribute("src"),
            fullname: (<HTMLInputElement> document.getElementById("input-frmuser-name")).value,
            nickname: (<HTMLInputElement> document.getElementById("input-frmuser-nickname")).value,
            username: (<HTMLInputElement> document.getElementById("input-frmuser-email")).value,
            password: (<HTMLInputElement> document.getElementById("input-frmuser-password")).value,
            admin: (<HTMLInputElement> document.getElementById("input-frmuser-administrator")).checked ? 1 : 0
        };
    }

    validate() : boolean {
        let formObject : any = this.createObject();

        if (formObject.fullname.trim() === ""){
            window.cosmo.dialog.error("Oops", "O campo Nome não pode ficar vazio", () => {});
            return false;
        }

        if (formObject.nickname.trim() === ""){
            window.cosmo.dialog.error("Oops", "O campo Nome de Usuário não pode ficar vazio", () => {});
            return false;
        }

        if (formObject.username.trim() === ""){
            window.cosmo.dialog.error("Oops", "O campo Email não pode ficar vazio", () => {});
            return false;
        }

        if (formObject.password.trim() === ""){
            window.cosmo.dialog.error("Oops", "O campo Senha não pode ficar vazio.", () => {});
            return false;
        }

        return true;
    }

    save(){
        let formObject : any = this.createObject();

        let success = function(content: any){
            window.cosmo.dialog.success("Criação de usuário", content.message, () => {
                window.location.href = content.callback;
            });
        }
        let fail = function(content: any){
            try {
                window.cosmo.dialog.error("Oops", content.responseJSON[0], () => {});
            } catch (e){
                window.cosmo.dialog.error("Erro", "Ocorreu um erro desconhecido", () => {});
            }
        }

        let ajax = window.cosmo.ajax.getDefaults();
        ajax.url = window.cosmo.routes_name.administrator_register_user;
        ajax.method = "POST";
        ajax.data = formObject;
        ajax.type = "json";
        ajax.success = success;
        ajax.error = fail;
        window.cosmo.ajax.send(ajax);
    }
}

class UpdateUser extends AbstractUser {
    initialize(){
        if (<HTMLButtonElement> document.getElementById("btn-update-profile"))
            (<HTMLButtonElement> document.getElementById("btn-update-profile")).addEventListener("click", () => {
                if (this.validate())
                    this.save();
            });
        
        if (<HTMLButtonElement> document.getElementById("btn-alter-image"))
            (<HTMLButtonElement> document.getElementById("btn-alter-image")).addEventListener("click", () => {
                this.wrapImage();    
            });

        if (<HTMLButtonElement> document.getElementById("input-frmuser-fileupload"))
            (<HTMLButtonElement> document.getElementById("input-frmuser-fileupload")).addEventListener("change", () => {
                this.loadAvatar();
            });

        if (<HTMLButtonElement> document.getElementById("btn-close-profile"))
            (<HTMLButtonElement> document.getElementById("btn-close-profile")).addEventListener("click", () => {
                window.cosmo.dialog.confirm("Encerrar esta conta", "Você tem certeza que deseja encerrar esta conta? Esta ação é irreversível.", (result: any) => {
                    if (result)
                        this.closeAccount();
                });
            });
    }

    createObject(){
        return {
            id: (<HTMLInputElement> document.getElementById("input-hidden-id")).value,
            avatar: (<HTMLImageElement> document.getElementById("img-frmuser-avatar")).getAttribute("src"),
            fullname: (<HTMLInputElement> document.getElementById("input-frmuser-name")).value,
            nickname: (<HTMLInputElement> document.getElementById("input-frmuser-nickname")).value,
            username: (<HTMLInputElement> document.getElementById("input-frmuser-email")).value,
            admin: (<HTMLInputElement> document.getElementById("input-frmuser-administrator")).checked ? 1 : 0,
            block_status: (<HTMLInputElement> document.getElementById("input-frmuser-block")).checked ? 1 : 0,
            block_reason: (<HTMLInputElement> document.getElementById("input-frmuser-reason")).value
        };
    }

    validate() : boolean {
        let formObject : any = this.createObject();

        if (formObject.fullname.trim() === ""){
            window.cosmo.dialog.error("Oops", "O campo Nome não pode ficar vazio", () => {});
            return false;
        }

        if (formObject.nickname.trim() === ""){
            window.cosmo.dialog.error("Oops", "O campo Nome de Usuário não pode ficar vazio", () => {});
            return false;
        }

        if (formObject.username.trim() === ""){
            window.cosmo.dialog.error("Oops", "O campo Email não pode ficar vazio", () => {});
            return false;
        }

        if (formObject.block_status == 1)
            if (formObject.block_reason.trim() === ""){
                window.cosmo.dialog.error("Oops", "Você está bloqueando este usuário, por isso é necessário preencher o campo Razão do Bloqueio", () => {});
                return false;
            }

        return true;
    }

    save(){
        let formObject : any = this.createObject();

        let success = function(content: any){
            window.cosmo.dialog.success("Atualização de usuário", content.message, () => {
                window.location.href = content.callback;
            });
        }
        let fail = function(content: any){
            try {
                window.cosmo.dialog.error("Oops", content.responseJSON[0], () => {});
            } catch (e){
                window.cosmo.dialog.error("Erro", "Ocorreu um erro desconhecido", () => {});
            }
        }

        let ajax = window.cosmo.ajax.getDefaults();
        ajax.url = window.cosmo.routes_name.administrator_update_user;
        ajax.method = "POST";
        ajax.data = formObject;
        ajax.type = "json";
        ajax.success = success;
        ajax.error = fail;
        window.cosmo.ajax.send(ajax);
    }

    closeAccount(){
        let formObject = this.createObject();
        let success = function(content: any){
            window.cosmo.dialog.success("Removido com sucesso", content.message, () => {
                window.location.href = content.callback;
            });
        };
        let fail = function(content: any){
            window.cosmo.dialog.error("Erro", content.responseJSON[0], () => {});
        };
        let ajax = window.cosmo.ajax.getDefaults();
        ajax.url = window.cosmo.routes_name.administrator_close_user + formObject.id;
        ajax.method = "POST";
        ajax.type = "json";
        ajax.success = success;
        ajax.error = fail;
        window.cosmo.ajax.send(ajax);
    }
}

let create_user = new CreateUser();
create_user.initialize();
let update_user = new UpdateUser();
update_user.initialize();