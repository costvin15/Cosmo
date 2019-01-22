function frmRanking(){}

frmRanking.prototype.initialize = function(){
    $(".table-responsive").on("show.bs.dropdown", function() {
        $(".table-responsive").css("overflow", "inherit")
    });
    
    $(".table-responsive").on("hide.bs.dropdown", function() {
        $(".table-responsive").css("overflow", "auto")
    });

    this.drawTable();

    $("#frmuser-search").on("keyup", function(){
        console.log($("#table-classification").DataTable().search(this.value));
        $("#table-classification").DataTable().search(this.value).draw();
    });
};

frmRanking.prototype.drawTable = function(){
    var draw = function(data){
        $("#table-classification").DataTable().destroy();
        $('#table-classification tbody').html("");
        
        data.forEach(function(element, index){
            var s = "";
            s += "<tr>";
            s += "  <td>" + (index + 1) + "</td>";
            s += "  <td>" + element.nickname + "</td>";
            s += "  <td>" + element.points + "</td>";
            s += "</tr>";

            $("#table-classification tbody").append(s);
        });

        $("#table-classification").DataTable({
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Portuguese-Brasil.json"
            }
        });
    };

    var error = function(message){
        cosmo.dialog.error("Erro", message.responseJSON[0]);
    };

    var ajax = cosmo.ajax.getDefaults();
    ajax.url = cosmo.urlbase + cosmo.routes_name.GET_RANKING;
    ajax.method = "POST";
    ajax.type = "json";
    ajax.sucess = draw;
    ajax.error = error;
    ajax.target = $("#table-classification");
    cosmo.ajax.send(ajax);
};

window.cosmo = window.cosmo || {};
window.cosmo.frmRanking = window.cosmo.frmRanking || new frmRanking();
$("document").ready(function(){
    window.cosmo.frmRanking.initialize();
});