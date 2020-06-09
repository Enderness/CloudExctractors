$(document).ready(function() { 
  'use strict';
  $(function() {
    var body = $('body');
    var mainWrapper = $('.main-wrapper');
    var footer = $('footer');
    var sidebar = $('.sidebar');
    var navbar = $('.navbar').not('.top-navbar');

    $.fn.dataTable.ext.errMode = function ( settings, helpPage, message ) { 
     Swal.fire({
      icon:"error",
      title:"Error",
      text:$.cookie("error")});
};

    //Scraper thingy
    $("#scraperForm").submit(function(event) {

        event.preventDefault();

        var data = $(this).serialize();


        //Datatable
        var url = "/apps/inc/format.php?scrape="+page+"&"+data;

        $.ajax({
            url:url,
            success:function(response)
            {
              $("#startScrape").removeAttr("disabled");
            },
            xhr: function(){
                var xhr = $.ajaxSettings.xhr() ;
                xhr.onprogress = function(evt){ 
                    $("#startScrape").attr("disabled","true");
                    var result_data = evt.currentTarget.responseText.split(",");
                    var entry = result_data[result_data.length-1]
                    $("#dataTableExample").DataTable().row.add(JSON.parse(entry)).draw(false);
                };
                return xhr ;
            }
        });

    });
    
    if(page=="facebook" && $.cookie("facebook_token") !== undefined)
    {
        $("#input_Token").val($.cookie("facebook_token"));
    }

    $('#dataTableExample').DataTable({
      "aLengthMenu": [
        [10, 30, 50, -1],
        [10, 30, 50, "All"]
      ],
      "iDisplayLength": 10,
      processing: true,
      "language": {
        search: ""
      }});

    $('#input_Query').tagsInput();

    $("input").on("keydown", function (e) {
        return e.which !== 32;
    });

    $('#dataTableExample').each(function() {
      var datatable = $(this);
      // SEARCH - Add the placeholder for Search and Turn this into in-line form control
      var search_input = datatable.closest('.dataTables_wrapper').find('div[id$=_filter] input');
      search_input.attr('placeholder', 'Search');
      search_input.removeClass('form-control-sm');
      // LENGTH - Inline-Form control
      var length_sel = datatable.closest('.dataTables_wrapper').find('div[id$=_length] select');
      length_sel.removeClass('form-control-sm');
    });

    // initializing bootstrap tooltip
    $('[data-toggle="tooltip"]').tooltip();

    // initialize clipboard plugin
    if ($('.btn-clipboard').length) {
      var clipboard = new ClipboardJS('.btn-clipboard');

      // Enabling tooltip to all clipboard buttons
      $('.btn-clipboard').attr('data-toggle', 'tooltip').attr('title', 'Copy to clipboard');

      // initializing bootstrap tooltip
      $('[data-toggle="tooltip"]').tooltip();

      // initially hide btn-clipboard and show after copy
      clipboard.on('success', function(e) {
        e.trigger.classList.value = 'btn btn-clipboard btn-current'
        $('.btn-current').tooltip('hide');
        e.trigger.dataset.originalTitle = 'Copied';
        $('.btn-current').tooltip('show');
        setTimeout(function(){
            $('.btn-current').tooltip('hide');
            e.trigger.dataset.originalTitle = 'Copy to clipboard';
            e.trigger.classList.value = 'btn btn-clipboard'
        },1000);
        e.clearSelection();
      });
    }



    // Applying perfect-scrollbar 
    if ($('.sidebar .sidebar-body').length) {
      const sidebarBodyScroll = new PerfectScrollbar('.sidebar-body');
    }
    if ($('.content-nav-wrapper').length) {
      const contentNavWrapper = new PerfectScrollbar('.content-nav-wrapper');
    }

    // Sidebar toggle to sidebar-folded
    $('.sidebar-toggler').on('click', function(e) {
      $(this).toggleClass('active');
      $(this).toggleClass('not-active');
      if (window.matchMedia('(min-width: 992px)').matches) {
        e.preventDefault();
        body.toggleClass('sidebar-folded');
      } else if (window.matchMedia('(max-width: 991px)').matches) {
        e.preventDefault();
        body.toggleClass('sidebar-open');
      }
    });


    // Settings sidebar toggle
    $('.settings-sidebar-toggler').on('click', function(e) {
      $('body').toggleClass('settings-open');
    });

    // Sidebar theme settings
    $("input:radio[name=sidebarThemeSettings]").click(function() {
      $('body').removeClass('sidebar-light sidebar-dark');
      $('body').addClass($(this).val());
     })


    // sidebar-folded on large devices
    function iconSidebar(e) {
      if (e.matches) {
        body.addClass('sidebar-folded');
      } else {
        body.removeClass('sidebar-folded');
      }
    }
    var desktopMedium = window.matchMedia('(min-width:992px) and (max-width: 1199px)');
    desktopMedium.addListener(iconSidebar);
    iconSidebar(desktopMedium);


    //Add active class to nav-link based on url dynamically
    function addActiveClass(element) {
        if (current === "") {
          //for root url
          if (element.attr('href').indexOf("index") !== -1) {
            element.parents('.nav-item').last().addClass('active');
            if (element.parents('.sub-menu').length) {
              element.closest('.collapse').addClass('show');
              element.addClass('active');
            }
          }
        } else {
          //for other url
          if (element.attr('href').indexOf(current) !== -1) {
            element.parents('.nav-item').last().addClass('active');
            if (element.parents('.sub-menu').length) {
              element.closest('.collapse').addClass('show');
              element.addClass('active');
            }
            if (element.parents('.submenu-item').length) {
              element.addClass('active');
            }
          }
        }
    }

      var current = location.pathname.split("/").slice(-1)[0].replace(/^\/|\/$/g, '');
      $('.nav li a', sidebar).each(function() {
        var $this = $(this);
        addActiveClass($this);
      });

    $('.horizontal-menu .nav li a').each(function() {
      var $this = $(this);
      addActiveClass($this);
    })


    //  open sidebar-folded when hover
    $(".sidebar .sidebar-body").hover(
    function () {
      if (body.hasClass('sidebar-folded')){
        body.addClass("open-sidebar-folded");
      }
    },
    function () {
      if (body.hasClass('sidebar-folded')){
        body.removeClass("open-sidebar-folded");
      }
    });

  // close sidebar when click outside on mobile/table    
    $(document).on('click touchstart', function(e){
      e.stopPropagation();

      // closing of sidebar menu when clicking outside of it
      if (!$(e.target).closest('.sidebar-toggler').length) {
        var sidebar = $(e.target).closest('.sidebar').length;
        var sidebarBody = $(e.target).closest('.sidebar-body').length;
        if (!sidebar && !sidebarBody) {
        if ($('body').hasClass('sidebar-open')) {
          $('body').removeClass('sidebar-open');
        }
        }
      }
    });

    // initializing popover
    $('[data-toggle="popover"]').popover();

    //checkbox and radios
    $(".form-check label,.form-radio label").append('<i class="input-frame"></i>');

    //Horizontal menu in mobile
    $('[data-toggle="horizontal-menu-toggle"]').on("click", function() {
      $(".horizontal-menu .bottom-navbar").toggleClass("header-toggled");
    });
    // Horizontal menu navigation in mobile menu on click
    var navItemClicked = $('.horizontal-menu .page-navigation >.nav-item');
    navItemClicked.on("click", function(event) {
      if(window.matchMedia('(max-width: 991px)').matches) {
        if(!($(this).hasClass('show-submenu'))) {
          navItemClicked.removeClass('show-submenu');
        }
        $(this).toggleClass('show-submenu');
      }        
    })

    $(window).scroll(function() {
      if(window.matchMedia('(min-width: 992px)').matches) {
        var header = $('.horizontal-menu');
        if ($(window).scrollTop() >= 60) {
          $(header).addClass('fixed-on-scroll');
        } else {
          $(header).removeClass('fixed-on-scroll');
        }
      }
    });

    $("#generateRand").click(function() {
        $("#serial_key_input").val(makeSerial());
    });

    $("#cancel").click(function() {
      window.location = window.location.href.split("?")[0];
      return false
    });

    

  });
});