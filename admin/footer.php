<div class="page-footer">
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <span class="txt-color-white">OrderApp</span>
        </div>
    </div>
</div>
<!-- END PAGE FOOTER -->
<?php  require_once 'inc/initDb.php'; ?>
<!-- SHORTCUT AREA : With large tiles (activated via clicking user name tag)
Note: These tiles are completely responsive,
you can add as many as you like
-->

<!-- END SHORTCUT AREA -->

<!--================================================== -->

<!-- PACE LOADER - turn this on if you want ajax loading to show (caution: uses lots of memory on iDevices)-->
<script data-pace-options='{ "restartOnRequestAfter": true }' src="js/plugin/pace/pace.min.js"></script>

<!-- Link to Google CDN's jQuery + jQueryUI; fall back to local -->
<script src="js/custom/customap.js"></script>
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyC1lQDoUmh5UiXrGzkjQQjnl5FxujHvsZc&callback=initMap"></script>
<!--<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/geocomplete/1.7.0/jquery.geocomplete.js"></script>-->
<script>
    if (!window.jQuery) {
        document.write('<script src="js/libs/jquery-2.1.1.min.js"><\/script>');
    }
</script>

<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
<script>
    if (!window.jQuery.ui) {
        document.write('<script src="js/libs/jquery-ui-1.10.3.min.js"><\/script>');
    }
</script>

<!-- IMPORTANT: APP CONFIG -->
<script src="js/app.config.js"></script>

<!-- JS TOUCH : include this plugin for mobile drag / drop touch events-->
<script src="js/plugin/jquery-touch/jquery.ui.touch-punch.min.js"></script>

<!-- BOOTSTRAP JS -->

<!-- CUSTOM NOTIFICATION -->
<script src="js/notification/SmartNotification.min.js"></script>

<!-- JARVIS WIDGETS -->
<script src="js/smartwidgets/jarvis.widget.min.js"></script>

<!-- EASY PIE CHARTS -->
<script src="js/plugin/easy-pie-chart/jquery.easy-pie-chart.min.js"></script>

<!-- SPARKLINES -->
<script src="js/plugin/sparkline/jquery.sparkline.min.js"></script>

<!-- JQUERY VALIDATE -->
<script src="js/plugin/jquery-validate/jquery.validate.min.js"></script>

<!-- JQUERY MASKED INPUT -->
<script src="js/plugin/masked-input/jquery.maskedinput.min.js"></script>

<!-- JQUERY SELECT2 INPUT -->
<script src="js/plugin/select2/select2.min.js"></script>

<!-- JQUERY UI + Bootstrap Slider -->
<script src="js/plugin/bootstrap-slider/bootstrap-slider.min.js"></script>

<!-- browser msie issue fix -->
<script src="js/plugin/msie-fix/jquery.mb.browser.min.js"></script>

<!-- FastClick: For mobile devices -->
<script src="js/plugin/fastclick/fastclick.min.js"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<!--[if IE 8]>

<h1>Your browser is out of date, please update your browser by going to www.microsoft.com/download</h1>

<![endif]-->

<!-- Demo purpose only -->
<!--<script src="js/demo.min.js"></script>-->

<!-- MAIN APP JS FILE -->
<script src="js/app.min.js"></script>

<!-- ENHANCEMENT PLUGINS : NOT A REQUIREMENT -->
<!-- Voice command : plugin -->
<script src="js/speech/voicecommand.min.js"></script>

<!-- SmartChat UI : plugin -->
<script src="js/smart-chat-ui/smart.chat.ui.min.js"></script>
<script src="js/smart-chat-ui/smart.chat.manager.min.js"></script>

<!-- PAGE RELATED PLUGIN(S) -->
<script src="js/plugin/datatables/jquery.dataTables.min.js"></script>
<script src="js/plugin/datatables/dataTables.colVis.min.js"></script>
<script src="js/plugin/datatables/dataTables.tableTools.min.js"></script>
<script src="js/plugin/datatables/dataTables.bootstrap.min.js"></script>
<script src="js/plugin/datatable-responsive/datatables.responsive.min.js"></script>

<script src="js/custom/restaurants.js"></script>
<script src="js/custom/refund.js"></script>
<script src="js/custom/add-new-company.js"></script>
<script src="js/custom/clockpicker.js"></script>
<script src="js/custom/add-company-user.js"></script>
<script src="js/custom/add-new-restaurant.js"></script>
<script src="js/custom/add-new-category.js"></script>
<script src="js/custom/add-choices-addons.js"></script>
<script src="js/custom/add-subitem.js"></script>
<script src="js/custom/add-new-item.js"></script>
<script src="js/custom/edit-restaurant.js"></script>
<script src="js/custom/edit-item.js"></script>
<script src="js/custom/edit-category.js"></script>
<script src="js/custom/edit-extra.js"></script>
<script src="js/custom/edit-subitem.js"></script>
<script src="js/custom/add-new-timing.js"></script>
<script src="js/custom/add-new-tags.js"></script>
<script src="js/custom/edit-company.js"></script>
<!--<script src="js/custom/bootstrap-3.3.2.min.js"></script>-->
<script src="js/custom/bootstrap-multiselect.js"></script>
<script src="js/custom/edit-delivery-address.js"></script>
<script src="js/custom/edit-tags.js"></script>
<script src="js/custom/edit-b2b-rest-disc.js"></script>
<script src="js/custom/add-rest-company-discount.js"></script>
<script src="js/custom/add-company-delivery.js"></script>
<script src="js/custom/sweetalert-dev.js"></script>
<script src="js/custom/sweetalert.min.js"></script>
<script src="js/custom/add-new-city.js"></script>
<script src="js/custom/user-edit.js"></script>


<script type="text/javascript">
    hideLoading();
</script>
<script type="text/javascript">

    // DO NOT REMOVE : GLOBAL FUNCTIONS!

    $('.clockpicker').clockpicker();
    function search_company(company_id)
    {
        addLoading();
        $.ajax({
            type: "POST",
            url: "ajax/company_search.php",
            data: {
                company_id    : company_id,
            },
            dataType: "json",
            success: function (response) {
                $("#cool").hide();
                $("#target-content").html(response);
                hideLoading();
            }
        });
    }

    $(document).ready(function() {

//        $("#address").geocomplete({
//                details: "form",
//                types: ["geocode", "establishment"],
//            }
//        );
        pageSetUp();

        var responsiveHelper_dt_basic = undefined;
        var responsiveHelper_datatable_fixed_column = undefined;
        var responsiveHelper_datatable_col_reorder = undefined;
        var responsiveHelper_datatable_tabletools = undefined;

        var breakpointDefinition = {
            tablet : 1024,
            phone : 480
        };

        $('#dt_basic').dataTable({
            "sDom": "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'l>r>"+
            "t"+
            "<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-xs-12 col-sm-6'p>>",
            "autoWidth" : true,
            "oLanguage": {
                "sSearch": '<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>'
            },
            "preDrawCallback" : function() {
                // Initialize the responsive datatables helper once.
                if (!responsiveHelper_dt_basic) {
                    responsiveHelper_dt_basic = new ResponsiveDatatablesHelper($('#dt_basic'), breakpointDefinition);
                }
            },
            "rowCallback" : function(nRow) {
                responsiveHelper_dt_basic.createExpandIcon(nRow);
            },
            "drawCallback" : function(oSettings) {
                responsiveHelper_dt_basic.respond();
            }
        });

        /* END BASIC */

        /* COLUMN FILTER  */
        var otable = $('#datatable_fixed_column').DataTable({
            //"bFilter": false,
            //"bInfo": false,
            //"bLengthChange": false
            //"bAutoWidth": false,
            //"bPaginate": false,
            //"bStateSave": true // saves sort state using localStorage
            "sDom": "<'dt-toolbar'<'col-xs-12 col-sm-6 hidden-xs'f><'col-sm-6 col-xs-12 hidden-xs'<'toolbar'>>r>"+
            "t"+
            "<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-xs-12 col-sm-6'p>>",
            "autoWidth" : true,
            "oLanguage": {
                "sSearch": '<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>'
            },
            "preDrawCallback" : function() {
                // Initialize the responsive datatables helper once.
                if (!responsiveHelper_datatable_fixed_column) {
                    responsiveHelper_datatable_fixed_column = new ResponsiveDatatablesHelper($('#datatable_fixed_column'), breakpointDefinition);
                }
            },
            "rowCallback" : function(nRow) {
                responsiveHelper_datatable_fixed_column.createExpandIcon(nRow);
            },
            "drawCallback" : function(oSettings) {
                responsiveHelper_datatable_fixed_column.respond();
            }

        });

        // custom toolbar
        $("div.toolbar").html('<div class="text-right"><img src="img/logo.png" alt="SmartAdmin" style="width: 111px; margin-top: 3px; margin-right: 10px;"></div>');

        // Apply the filter
        $("#datatable_fixed_column thead th input[type=text]").on( 'keyup change', function () {

            otable
                .column( $(this).parent().index()+':visible' )
                .search( this.value )
                .draw();

        } );
        /* END COLUMN FILTER */

        /* COLUMN SHOW - HIDE */
        $('#datatable_col_reorder').dataTable({
            "sDom": "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-6 hidden-xs'C>r>"+
            "t"+
            "<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-sm-6 col-xs-12'p>>",
            "autoWidth" : true,
            "oLanguage": {
                "sSearch": '<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>'
            },
            "preDrawCallback" : function() {
                // Initialize the responsive datatables helper once.
                if (!responsiveHelper_datatable_col_reorder) {
                    responsiveHelper_datatable_col_reorder = new ResponsiveDatatablesHelper($('#datatable_col_reorder'), breakpointDefinition);
                }
            },
            "rowCallback" : function(nRow) {
                responsiveHelper_datatable_col_reorder.createExpandIcon(nRow);
            },
            "drawCallback" : function(oSettings) {
                responsiveHelper_datatable_col_reorder.respond();
            }
        });

        /* END COLUMN SHOW - HIDE */

        /* TABLETOOLS */
        $('#datatable_tabletools').dataTable({

            // Tabletools options:
            //   https://datatables.net/extensions/tabletools/button_options
            "sDom": "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-6 hidden-xs'T>r>"+
            "t"+
            "<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-sm-6 col-xs-12'p>>",
            "oLanguage": {
                "sSearch": '<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>'
            },
            "oTableTools": {
                "aButtons": [
                    "copy",
                    "csv",
                    "xls",
                    {
                        "sExtends": "pdf",
                        "sTitle": "SmartAdmin_PDF",
                        "sPdfMessage": "SmartAdmin PDF Export",
                        "sPdfSize": "letter"
                    },
                    {
                        "sExtends": "print",
                        "sMessage": "Generated by SmartAdmin <i>(press Esc to close)</i>"
                    }
                ],
                "sSwfPath": "js/plugin/datatables/swf/copy_csv_xls_pdf.swf"
            },
            "autoWidth" : true,
            "preDrawCallback" : function() {
                // Initialize the responsive datatables helper once.
                if (!responsiveHelper_datatable_tabletools) {
                    responsiveHelper_datatable_tabletools = new ResponsiveDatatablesHelper($('#datatable_tabletools'), breakpointDefinition);
                }
            },
            "rowCallback" : function(nRow) {
                responsiveHelper_datatable_tabletools.createExpandIcon(nRow);
            },
            "drawCallback" : function(oSettings) {
                responsiveHelper_datatable_tabletools.respond();
            }
        });

        /* END TABLETOOLS */

    })

</script>

<!-- Your GOOGLE ANALYTICS CODE Below -->
<script type="text/javascript">

    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-XXXXXXXX-X']);
    _gaq.push(['_trackPageview']);

    (function() {
        var ga = document.createElement('script');
        ga.type = 'text/javascript';
        ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(ga, s);
    })();
    var availableTags = [
        "ActionScript",
        "AppleScript",
        "Asp",
        "BASIC",
        "C",
        "C++",
        "Clojure",
        "COBOL",
        "ColdFusion",
        "Erlang",
        "Fortran",
        "Groovy",
        "Haskell",
        "Java",
        "JavaScript",
        "Lisp",
        "Perl",
        "PHP",
        "Python",
        "Ruby",
        "Scala",
        "Scheme"
    ];



    globalTag = null;


    //USER EMAIL
    var user_email = new Array();
    <?php
    DB::useDB('orderapp_b2b_wui');
    $arr1 = DB::query("select * from b2b_users");
    foreach($arr1 as $tag_name ){ ?>
    user_email.push("<?php echo $tag_name['smooch_id']; ?>");
    <?php } ?>

    $( "#search-user-email").autocomplete({
        source: user_email,
        select: function (event, ui) {
            addLoading();
            $.ajax({
                type: "POST",
                url: "ajax/user_email_search.php",
                data: {
                    user_email    :  ui.item.label,
                },
                dataType: "json",
                success: function (response) {
                    $("#target-content").html(response);
                    hideLoading();
                }
            });

        },
    });

    $("#search-start-date").datepicker({
        dateFormat: 'yy-mm-dd',
        onSelect: function(selected,evnt) {
            //alert(selected);
            addLoading();
            $.ajax({
                type: "POST",
                url: "ajax/start_date_search.php",
                data: {
                    start_date   :  selected,
                },
                dataType: "json",
                success: function (response) {
                    $("#target-content").html(response);
                    hideLoading();
                }
            });
        }
    });

    $("#search-end-date").datepicker({
        dateFormat: 'yy-mm-dd',
        onSelect: function(selected) {

            addLoading();
            $.ajax({
                type: "POST",
                url: "ajax/end_date_search.php",
                data: {
                    end_date   :  selected,
                },
                dataType: "json",
                success: function (response) {
                    $("#target-content").html(response);
                    hideLoading();
                }
            });

        }

    });




    // TAGS AUTOCOMPLETE


    var tags = new Array();
    <?php
    DB::useDB('orderapp_restaurants_b2b_wui');
    $arr1 = DB::query("select * from tags");
    foreach($arr1 as $tag_name ){ ?>
    tags.push("<?php echo $tag_name['name_en']; ?>");
    <?php } ?>

    $( "#tag_name_en").autocomplete({
        source: tags,
        select: function (event, ui) {
            globalTag = ui.item.label;
            auto_hebrew_tags(ui.item.label);

        },
    });

    // TAGS HE AUTOCOMPLETE
    var tags_he = new Array();
    <?php
    DB::useDB('orderapp_restaurants_b2b_wui');
    $arr1 = DB::query("select * from tags");
    foreach($arr1 as $tag_name ){ ?>
    tags_he.push("<?php echo $tag_name['name_he']; ?>");
    <?php } ?>

    $( "#tag_name_he" ).autocomplete({
        source: tags_he
    });




    // ADD REST COMPANY DISCOUNT >>>> company name
  var compnay_disc = new Array();
    <?php
    DB::useDB('orderapp_b2b_wui');
    $arr11 = DB::query("select * from company");
    foreach($arr11 as $company_name ){ ?>
    compnay_disc.push("<?php echo $company_name['name']; ?>");
    <?php } ?>

    $( "#company" ).autocomplete({
        source: compnay_disc
    });



    // ADD REST COMPANY DISCOUNT >>>>> restaurant name
  var rest_name = new Array();
    <?php
    DB::useDB('orderapp_restaurants_b2b_wui');
    $arr12 = DB::query("select * from restaurants");
    foreach($arr12 as $restaurant_name ){ ?>
    rest_name.push("<?php echo $restaurant_name['name_en']; ?>");
    <?php } ?>

    $( "#restaurant" ).autocomplete({
        source: rest_name
    });
    $(document).ready(function() {
        
        $('.multiselect-ui').multiselect({
            onChange: function(option, checked) {
                // Get selected options.
                var selectedOptions = $('.multiselect-ui option:selected');

                if (selectedOptions.length >= rest_limit) {
                    // Disable all other checkboxes.
                    var nonSelectedOptions = $('.multiselect-ui option').filter(function() {
                        return !$(this).is(':selected');
                    });

                    nonSelectedOptions.each(function() {
                        var input = $('input[value="' + $(this).val() + '"]');
                        input.prop('disabled', true);
                        input.parent('li').addClass('disabled');
                    });
                }
                else {
                    // Enable all checkboxes.
                    $('.multiselect-ui option').each(function() {
                        var input = $('input[value="' + $(this).val() + '"]');
                        input.prop('disabled', false);
                        input.parent('li').addClass('disabled');
                    });
                }
            },
            maxHeight: '300',
        });
//        var last_valid_selection = null;
//        alert(rest_limit);
//        $('#rest_name').change(function(event) {
//
//            if ($(this).val().length > rest_limit) {
//                alert('You can only choose' + rest_limit + 'restaurants');
//
//               this.checked = false;
//            } else {
//                last_valid_selection = $(this).val();
//            }
//        });

    });



</script>

</body>

</html>