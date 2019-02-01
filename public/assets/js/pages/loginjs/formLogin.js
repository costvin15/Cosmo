function formLogin() {}
formLogin.prototype.initialize = function() {
    $("#btnLogin").click(function() {
        cosmo.login.validate() && cosmo.login.auth()
    })
}, formLogin.prototype.validateEmail = function(o) {
    return /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(o)
}, formLogin.prototype.createObject = function() {
    var o = {};
    return o.username = $("#frmlogin-email").val(), o.password = $("#frmlogin-password").val(), o
}, formLogin.prototype.validate = function() {
    var o = this.createObject();
    return "" === o.username.trim() ? (cosmo.dialog.error("Erro", "O campo Email é obrigatório!"), !1) : "" !== o.password.trim() || (cosmo.dialog.error("Erro", "O campo Senha é obrigatório!"), !1);
}, formLogin.prototype.auth = function() {
    var o = this.createObject(),
        r = function(o) {
            window.location.href = o.callback
        },
        i = function(o) {
            if (o.responseJSON["callback"])
                cosmo.dialog.error("Erro", o.responseJSON[0], function(){
                    window.location.href = o.responseJSON["callback"];
                });
            else
                cosmo.dialog.error("Erro", o.responseJSON[0]);
        },
        n = cosmo.ajax.getDefaults();
    return n.url = cosmo.urlbase + cosmo.routes_name.AUTH, n.method = "POST", n.data = o, n.type = "json", n.sucess = r, n.error = i, cosmo.ajax.send(n), !0
}, window.cosmo = window.cosmo || {}, window.cosmo.login = window.cosmo.login || new formLogin, $("document").ready(function() {
    window.cosmo.login.initialize()
});
