function AjaxSend() {
    return!0
}

AjaxSend.prototype.getDefaults=function() {
    return {
        method:"POST",
        url:"",
        data: {},
        type: "json", sucess: void 0, show: void 0, hide: void 0, error: void 0, block: !1, processData: !0, cache: !0, target: void 0, contentType: "application/x-www-form-urlencoded; charset=UTF-8", async: !0
    }
},

AjaxSend.prototype.send=function(o) {
    var e=this.getDefaults(),
    t=$.extend(e, o);
    return $.ajax( {
        type:t.method, url:t.url, data:t.data, dataType:t.type, contentType:t.contentType, processData:t.processData, async:t.async, cache:t.cache, beforeSend:function() {
            void 0===t.show?t.block||(void 0!==t.target?t.target.block( {
                message: $("#sigu-loader-modal").html(), baseZ: 9999
            }
            ):$.blockUI( {
                message: $("#sigu-loader-modal").html(), baseZ: 9999
            }
            )):t.show()
        }
    }
    ).done(function(o) {
        void 0===t.show?void 0!==t.target?t.target.unblock(): $.unblockUI(): t.hide(), t.sucess(o)
    }
    ).fail(function(o, e, a) {
        console.log("Erro de Requisição", "error", a, ""), console.log(e), console.log(o), void 0===t.show?void 0!==t.target?t.target.unblock(): $.unblockUI(): t.hide(), void 0!==t.error?t.error(o): cosmo.dialog.error("Erro", o.responseJSON)
    }
    ),
    !0
},

window.cosmo=window.cosmo|| {},
window.cosmo.ajax=window.cosmo.ajax||new AjaxSend;

function Dialog() {}

Dialog.prototype.error=function(t, o, e = null) {
    var dialog = swal({
        title: t, html: o, type: "error", timer: 5e3
    });
    
    if (e != null)
        dialog.then(e, e);

    return dialog;
},

Dialog.prototype.success=function(t, o, e) {
    return void 0===e?swal( {
        title: t, html: o, type: "success", timer: 5e3
    }
    ):swal( {
        title: t, html: o, type: "success", timer: 5e3
    }
    ).then(e, e)
},

Dialog.prototype.warning=function(t, o) {
    return swal( {
        title: t, html: o, type: "warning", timer: 5e3
    }
    )
},

Dialog.prototype.info=function(t, o) {
    return swal( {
        title: t, html: o, type: "info", timer: 5e3
    }
    )
},

Dialog.prototype.question=function(t, o) {
    return swal( {
        title: t, html: o, type: "question", timer: 5e3
    }
    )
},

window.cosmo=window.cosmo|| {},
window.cosmo.dialog=window.cosmo.dialog||new Dialog;

var routes_name= {};
routes_name.AVALIABLE_USERNAME_COSMO="/login/register/search/username",
routes_name.REGISTER_SAVE="/login/register/save",
routes_name.REGISTER_RESCUE_PASSWORD="/login/register/password/rescue/send",
routes_name.USER_UPDATE_PROFILE = "/dashboard/profile/update",
routes_name.AUTH="/login/auth",
routes_name.ADMIN_GET_ALL_USER="/admin/user/all",
routes_name.ADM_REGISTER_USER_SAVE="/admin/user/save",
routes_name.ADM_REGISTER_USER_MODIFY="/admin/user/modify",
routes_name.ADM_REGISTER_USER_UPDATE="/admin/user/update",
routes_name.ADM_GET_PHOTO_USER = "/admin/user/image/";
window.cosmo = window.cosmo || {},

window.cosmo.routes_name = window.cosmo.routes_name || routes_name;