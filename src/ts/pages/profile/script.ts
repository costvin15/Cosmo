import "../../main";

interface User {
    id: string;
    avatar: string;
    fullname: string;
    nickname: string;
    fulltitle: string;
}
class UpdateUser {
    initialize(){
        (<HTMLButtonElement> document.getElementById("btn-update-profile")).addEventListener("click", () => {
            if (this.validate())
                this.save();
        });
        
        (<HTMLButtonElement> document.getElementById("btn-alter-image")).addEventListener("click", () => {
            this.wrapImage();    
        });

        (<HTMLButtonElement> document.getElementById("input-frmuser-fileupload")).addEventListener("change", () => {
            this.loadAvatar();
        });

        (<HTMLButtonElement> document.getElementById("btn-close-profile")).addEventListener("click", () => {
            window.cosmo.dialog.confirm("Encerrar sua conta", "Você tem certeza que deseja encerrar sua conta? Esta ação é irreversível.", (result: any) => {
                if (result)
                    this.closeAccount();
            });
        });
    }

    createObject() : User {
        return {
            id: (<HTMLInputElement> document.getElementById("input-hidden-id")).value,
            avatar: (<HTMLInputElement> document.getElementById("img-frmuser-avatar")).getAttribute("src"),
            fullname: (<HTMLInputElement> document.getElementById("input-frmuser-name")).value,
            nickname: (<HTMLInputElement> document.getElementById("input-frmuser-nickname")).value,
            fulltitle: (<HTMLInputElement> document.getElementById("input-frmuser-fulltitle")).value
        }
    }

    validate() : boolean {
        let formObject = this.createObject();

        if (formObject.fullname.trim() === ""){
            window.cosmo.dialog.error("Oops", "O campo Nome não pode ficar vazio", () => {});
            return false;
        } else if (formObject.nickname.trim() === ""){
            window.cosmo.dialog.error("Oops", "O campo Nome de Usuário não pode ficar vazio", () => {});
            return false;
        
        } else if (!this.validateDataUrl(formObject.avatar)){
            window.cosmo.dialog.error("Oops", "O campo Imagem é obrigatório", () => {});
            return false;
        }

        return true;
    }

    validateDataUrl(data: any) : boolean {
        return data.match(/^\s*data:([a-z]+\/[a-z0-9\-\+]+(;[a-z\-]+\=[a-z0-9\-]+)?)?(;base64)?,[a-z0-9\!\$\&\'\,\(\)\*\+\,\;\=\-\.\_\~\:\@\/\?\%\s]*\s*$/i);
    }

    wrapImage(){
        (<HTMLButtonElement> document.getElementById("input-frmuser-fileupload")).click();
    }

    loadAvatar(){
        let image_result = new FileReader();
        let expression = /^(?:image\/bmp|image\/cis\-cod|image\/gif|image\/ief|image\/jpeg|image\/jpeg|image\/jpeg|image\/pipeg|image\/png|image\/svg\+xml|image\/tiff|image\/x\-cmu\-raster|image\/x\-cmx|image\/x\-icon|image\/x\-portable\-anymap|image\/x\-portable\-bitmap|image\/x\-portable\-graymap|image\/x\-portable\-pixmap|image\/x\-rgb|image\/x\-xbitmap|image\/x\-xpixmap|image\/x\-xwindowdump)$/i;
        let image_uploaded = (<HTMLInputElement> document.getElementById("input-frmuser-fileupload")).files[0];

        if (!expression.test(image_uploaded.type)){
            window.cosmo.dialog.error("Erro", "O campo selecionado é inválido.", () => {});
            return false;
        }

        image_result.onload = (object: any) => {
            let image = new Image();
            image.src = object.target.result;
            image.onload = () => {
                var canvas = document.createElement("canvas");
                canvas.width = image.width;
                canvas.height = image.height;
                canvas.getContext("2d").drawImage(image, 0, 0, image.width, image.height);
                (<HTMLImageElement> document.getElementById("img-frmuser-avatar")).setAttribute("src", canvas.toDataURL());
            };
        };

        if ((<HTMLInputElement> document.getElementById("input-frmuser-fileupload")).files.length !== 0)
            image_result.readAsDataURL(image_uploaded);
    }

    save(){
        let formObject : any = this.createObject();
        if (!(<HTMLInputElement> document.getElementById("input-frmuser-class")).disabled)
            formObject.code = (<HTMLInputElement> document.getElementById("input-frmuser-class")).value;
        let success = function(content: any){
            window.cosmo.dialog.success("Atualização do usuário", content.message, () => {
                location.reload();
            });
        };
        let fail = function(content: any){
            window.cosmo.dialog.error("Oops", content.message, () => {});
        };

        let ajax = window.cosmo.ajax.getDefaults();
        ajax.url = window.base_url + window.cosmo.routes_name.user_update_profile;
        ajax.method = "POST";
        ajax.data = formObject;
        ajax.type = "json";
        ajax.success = success;
        ajax.error = fail;
        window.cosmo.ajax.send(ajax);
    }

    closeAccount(){
        let success = function(content: any){
            window.cosmo.dialog.success("Sentiremos sua falta...", content.message, () => {
                window.location.href = content.callback;
            });
        };
        let fail = function(content: any){
            window.cosmo.dialog.error("Erro", content.responseJSON[0], () => {});
        };
        let ajax = window.cosmo.ajax.getDefaults();
        ajax.url = window.base_url + window.cosmo.routes_name.user_close_profile;
        ajax.method = "POST";
        ajax.type = "json";
        ajax.success = success;
        ajax.error = fail;
        window.cosmo.ajax.send(ajax);
    }
}

let update_user = new UpdateUser();
update_user.initialize();