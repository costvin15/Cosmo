import $ from "jquery";
import swal from "sweetalert";

interface AjaxDefaults {
    method: string;
    url: string;
    data: object;
    type: string;
    success: Function;
    show: Function;
    hide: Function;
    error: Function;
    block: boolean;
    processData: boolean;
    cache: boolean;
    target: Function;
    contentType: string;
    async: boolean;
    beforeSend: Function;
}

class Ajax {
    getDefaults() : AjaxDefaults {
        return {
            method: "POST",
            url: "",
            data: {},
            type: "JSON",
            success: function(){},
            show: function(){},
            hide: function(){},
            error: function(){},
            block: true,
            processData: true,
            cache: true,
            target: function(){},
            contentType: "application/x-www-form-urlencoded; charset=UTF-8",
            async: true,
            beforeSend: function(){}
        };
    }

    send(config: AjaxDefaults){
        let defaults = this.getDefaults();
        config = window.$.extend(defaults, config);
        config.beforeSend = () => {
            (<HTMLDivElement> document.getElementById("loader-modal")).classList.remove("d-none");
            (<HTMLBodyElement> document.getElementsByTagName("body")[0]).classList.add("stop-scrolling");
        };
        let request = window.$.ajax(config);
        request.done((response: any) => {
            (<HTMLDivElement> document.getElementById("loader-modal")).classList.add("d-none");
            (<HTMLBodyElement> document.getElementsByTagName("body")[0]).classList.remove("stop-scrolling");
        });
        request.fail((jqXHR: any, status: any, error: any) => {
            (<HTMLDivElement> document.getElementById("loader-modal")).classList.add("d-none");
            (<HTMLBodyElement> document.getElementsByTagName("body")[0]).classList.remove("stop-scrolling");
            console.log(jqXHR, status, error);
        });

        return request;
    }
}

class Dialog {
    success(title: string, content: string, callback: () => any){
        swal({
            title: title,
            text: content,
            icon: "success",
            timer: 5e3
        }).then(callback);
    }

    error(title: string, content: string, callback: () => any){
        swal({
            title: title,
            text: content,
            icon: "error",
            timer: 5e3
        }).then(callback);
    }

    warning(title: string, content: string, callback: () => any){
        swal({
            title: title,
            text: content,
            icon: "warning"
        }).then(callback);
    }

    info(title: string, content: string, callback: () => any){
        swal({
            title: title,
            text: content,
            icon: "info"
        }).then(callback);
    }

    confirm(title: string, content: string, callback: (result: any) => any){
        swal({
            title: title,
            text: content,
            dangerMode: true,
            buttons: ["Não, cancelar", "Sim"],
        }).then(callback);
    }
}

declare global {
    interface Cosmo {
        ajax: Ajax;
        dialog: Dialog;
        routes_name: any;
    }
    
    interface Window {
        $: any;
        jQuery: any;
        cosmo: Cosmo;
    }
}

window.$ = $;
window.jQuery = $;
window.cosmo = {
    ajax: new Ajax(),
    dialog: new Dialog(),
    routes_name: {
        dashboard_index: "/dashboard/",
        auth: "/login/auth",
        available_username: "/login/register/search/username",
        register_user: "/login/register/save",
        user_close_profile: "/dashboard/profile/close",
        user_update_profile: "/dashboard/profile/update",
        view_activity: "/activities/",
        submit_activity: "/activities/submit",
        administrator_save_group_activity: "/admin/groupactivity/save",
        administrator_save_activity: "/admin/activity/save",
        administrator_register_user: "/admin/user/save",
        administrator_update_user: "/admin/user/update",
        administrator_close_user: "/admin/user/remove/"
    }
};