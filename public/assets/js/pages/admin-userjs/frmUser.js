function frmUser() {
}

frmUser.prototype.initialize = function() {
    $('.table-responsive').on('show.bs.dropdown', function () {
        $('.table-responsive').css("overflow", "inherit");
    });

    $('.table-responsive').on('hide.bs.dropdown', function () {
        $('.table-responsive').css("overflow", "auto");
    });

    this.drawTable();

    $("#frmuser-search").on('keyup', function () {
        var table = $('#table-users').DataTable();
        table.search(this.value).draw();
    });
};

frmUser.prototype.drawTable = function() {
    var returnSuccess = function(data) {
        $('#table-users').DataTable().destroy();
        $("#table-users tbody").html('');

        data.forEach(function(element, index){
            var html = '';
            html += '<tr>';
            html += '   <td>';
            html += '       <div class="avatar">';
            html += '           <img src="' + element.avatar + '"/>';
            html += '       </div>';
            html += '   </td>';
            html += '   <td>' + element.fullname + '</td>';
            html += '   <td>' + element.username + '</td>';
            html += '   <td>';
            html += '       <div class="btn-group">';
            html += '           <button type="button" class="btn btn-default"><i class="fa fa-cog" aria-hidden="true"></i></button>';
            html += '           <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">';
            html += '               <span class="caret"></span>';
            html += '               <span class="sr-only">Toggle Dropdown</span>';
            html += '           </button>';
            html += '           <ul class="dropdown-menu" role="menu">';
            html += '               <li><a href="' + window.cosmo.urlbase + window.cosmo.routes_name.ADM_REGISTER_USER_MODIFY + '/' + element._id + '">Editar</a></li>';
            html += '               <li><a href="#">Excluir</a></li>';
            html += '               <li class="divider"></li>';
            html += '               <li><a href="#">Bloquear</a></li>';
            html += '           </ul>';
            html += '       </div>';
            html += '   </td>';
            html += '</tr>';

            $("#table-users tbody").append(html);
        });

        $("#table-users").DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Portuguese-Brasil.json"
            }
        });
    };

    var errorHandler = function(jqXHR) {
        cosmo.dialog.error('Erro', jqXHR.responseJSON[0]);
    };

    var configurationAjaxSend = cosmo.ajax.getDefaults();
    configurationAjaxSend.url = cosmo.urlbase + cosmo.routes_name.ADMIN_GET_ALL_USER;
    configurationAjaxSend.method = 'GET';
    configurationAjaxSend.type = 'json';
    configurationAjaxSend.sucess = returnSuccess;
    configurationAjaxSend.error = errorHandler;
    configurationAjaxSend.target = $("#table-users");

    cosmo.ajax.send(configurationAjaxSend);

    return true;
};

window.cosmo = window.cosmo || {};
window.cosmo.frmuser = window.cosmo.frmuser || new frmUser();

$('document').ready(function(){
    window.cosmo.frmuser.initialize();
});