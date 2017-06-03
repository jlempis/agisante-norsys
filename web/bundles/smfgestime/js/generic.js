(function() {


  $(function() {

    $.uniform.defaults.selectAutoWidth = false;
    
    $(".dTable").dataTable({
      bJQueryUI: false,
      bAutoWidth: false,
      sPaginationType: "full_numbers",
      sDom: "<\"table-header\"fl>t<\"table-footer\"ip>"
    });
    

    $("select.uniform, input:file, .dataTables_length select").uniform();
 
    $('[data-toggle="tab"]').on('shown', function(e) {
      var id;
      id = $(e.target).attr("href");
      return $(id).find(".iButton-icons-tab").iButton({
        labelOn: "<i class='icon-ok'></i>",
        labelOff: "<i class='icon-remove'></i>",
        handleWidth: 30
      });
    });

    $(".toggle-primary-sidebar").click(function(e) {
      return $.sparkline_display_visible();
    });

    $("#switch-sidebar").click(function() {
      return $('body').toggleClass('dropdown-sidebar');
    });


  });

}).call(this);
