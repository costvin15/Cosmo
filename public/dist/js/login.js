!function(e){var o={};function n(r){if(o[r])return o[r].exports;var t=o[r]={i:r,l:!1,exports:{}};return e[r].call(t.exports,t,t.exports,n),t.l=!0,t.exports}n.m=e,n.c=o,n.d=function(e,o,r){n.o(e,o)||Object.defineProperty(e,o,{enumerable:!0,get:r})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(e,o){if(1&o&&(e=n(e)),8&o)return e;if(4&o&&"object"==typeof e&&e&&e.__esModule)return e;var r=Object.create(null);if(n.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:e}),2&o&&"string"!=typeof e)for(var t in e)n.d(r,t,function(o){return e[o]}.bind(null,t));return r},n.n=function(e){var o=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(o,"a",o),o},n.o=function(e,o){return Object.prototype.hasOwnProperty.call(e,o)},n.p="",n(n.s=12)}({12:function(e,o){new(function(){function e(){var e=this;document.getElementById("login-form").addEventListener("submit",function(o){o.preventDefault(),e.validate()&&e.auth()})}return e.prototype.createObject=function(){return{username:document.getElementById("frmlogin-email").value,password:document.getElementById("frmlogin-password").value}},e.prototype.validate=function(){var e=this.createObject();return""===e.username.trim()?(window.cosmo.dialog.error("Oops","O campo Email ou Nome de Usuário não pode ficar vazio.",function(){}),!1):""===e.password.trim()?(window.cosmo.dialog.error("Oops","O campo Senha não pode ficar vazio.",function(){}),!1):!(e.password.length<=3)||(window.cosmo.dialog.error("Oops","A senha deve ter mais de 3 caracteres.",function(){}),!1)},e.prototype.auth=function(){var e=this.createObject(),o=window.cosmo.ajax.getDefaults();o.url=window.base_url+window.cosmo.routes_name.auth,o.method="POST",o.type="json",o.data=e,o.success=function(e){console.log(e)},o.error=function(e){console.log(e),e.responseJSON&&(e.responseJSON[0]&&e.responseJSON.callback?window.cosmo.dialog.error("Oops",e.responseJSON[0],function(){window.location.href=e.responseJSON.callback}):e.responseJSON[0]?window.cosmo.dialog.error("Oops",e.responseJSON[0],function(){}):window.cosmo.dialog.error("Oops","Erro desconhecido",function(){}))},window.cosmo.ajax.send(o)},e}())}});