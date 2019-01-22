function frmHistory(){}

frmHistory.prototype.initialize = function(){
    $(".table-responsive").on("show.bs.dropdown", function() {
        $(".table-responsive").css("overflow", "inherit")
    });
    
    $(".table-responsive").on("hide.bs.dropdown", function() {
        $(".table-responsive").css("overflow", "auto")
    });

    this.draw();
};

frmHistory.prototype.draw = function(){
    var success = function(data){
        $("#table-history").DataTable().destroy();
        $("#table-history tbody").html("");
        
        data.reverse();
        data.forEach(function(element, index){
            var s = "";
            s += "<tr>";
            s += "  <td>" + (index + 1) + "</td>";
            s += "  <td>" + element.activity.title + "</td>";
            s += "  <td>" + element.time.toPrecision(3) + " segundos </td>";
            s += "  <td>";
            switch (element.language){
                case "lua":
                    s += "Lua";
                    break;
                case "cpp":
                    s += "C/C++";
                    break;
                case "python":
                    s += "Python";
                    break;
                default:
                    s += "Desconhecido";
            }
            s += "  </td>";
            s += "  <td>";
            s += "      <a type=\"button\" class=\"btn btn-default\" href=\"" + cosmo.urlbase + cosmo.routes_name.VIEW_ACTIVITY + element.id + "\">"
            s += "          <i class=\"fa fa-eye\" aria-hidden=\"true\"></i>";
            s += "      </a>";
            s += "  </td>";
            s += "</tr>";

            $("#table-history tbody").append(s);
        });

        $("#table-history").DataTable({
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Portuguese-Brasil.json"
            }
        });
    };

    var error = function(data){
        cosmo.dialog.error("Erro", data.responseJSON[0]);
    };

    var ajax = cosmo.ajax.getDefaults();
    ajax.url = cosmo.urlbase + cosmo.routes_name.GET_HISTORY;
    ajax.method = "POST";
    ajax.type = "json";
    ajax.sucess = success;
    ajax.error = error;
    ajax.target = $("#table-history");
    cosmo.ajax.send(ajax);
};

window.cosmo = window.cosmo || {};
window.cosmo.frmHistory = window.cosmo.frmHistory || new frmHistory();
$("document").ready(function(){
    window.cosmo.frmHistory.initialize();
});