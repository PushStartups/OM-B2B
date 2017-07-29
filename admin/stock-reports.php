<?php
include "header.php";
$rolee = $_SESSION['b2b_admin_role'];

?>
<div id="main" role="main">

    <!-- MAIN CONTENT -->
    <div id="content">
        <!-- row -->

        <?php if ($rolee == 1) {?>
        <div class="row">

            <!-- col -->
            <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
                <h1 class="page-title txt-color-blueDark"><!-- PAGE HEADER --><i class="fa-fw fa fa-files-o "></i> Add Stock Invoice Files</h1>
            </div>

        </div>
        <div id="myform">
            <section id="widget-grid"  id="myform">
                <!-- row -->
                <div class="row">
                    <!-- NEW WIDGET START -->
                    <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                        <!-- Widget ID (each widget will need unique ID)-->

                        <div class="jarviswidget" id="wid-id-2" data-widget-colorbutton="false" data-widget-editbutton="false">

                            <header>
                            </header>

                            <div>

                                <div class="jarviswidget-editbox">
                                    <!-- This area used as dropdown edit box -->
                                </div>

                                <div class="widget-body">

                                    <form enctype="multipart/form-data"
                                          action="<?php print $_SERVER['PHP_SELF']?>" method="post">
                                        <fieldset>

                                            <div class="form-group">
                                                <label>Upload PDF File</label>
                                                <input class="form-control" id="pdfFile" name="pdfFile"   type="file" required>

                                            </div>

                                        </fieldset>
                                        <div class="form-actions">
                                            <input type="submit" value="Submit" class="btn btn-primary btn-lg">


                                        </div>
                                    </form>
                                </div>
                                <!-- end widget content -->
                            </div>
                            <!-- end widget div -->
                        </div>

                    </article>
                    <!-- WIDGET END -->
                </div>
                <div class="row">
                    <!-- a blank row to get started -->
                    <div class="col-sm-12">
                        <!-- your contents here -->
                    </div>

                </div>
                <!-- end row -->
            </section>
        </div>

        <?php } ?>
        <!-- end widget grid -->


        <section id="widget-grid" class="">
            <!-- row -->
            <div class="row">
                <!-- NEW WIDGET START -->
                <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <!-- Widget ID (each widget will need unique ID)-->
                    <div class="jarviswidget jarviswidget-color-blueDark" id="wid-id-1" data-widget-editbutton="false">

                        <header>
                            <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                            <h2>Stocks</h2>
                        </header>
                        <!-- widget div-->
                        <div>
                            <!-- widget edit box -->
                            <div class="jarviswidget-editbox">
                                <!-- This area used as dropdown edit box -->
                            </div>
                            <!-- end widget edit box -->
                            <!-- widget content -->
                            <div class="widget-body no-padding">

                                <table id="datatable_fixed_column" class="table table-striped table-bordered" width="100%">

                                    <thead>

                                    <tr>
                                        <th data-class="expand">ID</th>

                                        <th >URL</th>



                                        <!--                                            <th>Action</th>-->
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <?php $stocks = getAllStockInvoicesPDF();
                                    foreach ($stocks as $stock)
                                    {

                                        ?>
                                        <tr>
                                            <td><?=$stock['id']?></td>

                                            <td><a href="//<?=$stock['url']?>" target="_blank"><?=$stock['url']?></a></td>


                                        </tr>
                                    <?php } ?>
                                    </tbody>

                                </table>

                            </div>
                            <!-- end widget content -->

                        </div>
                        <!-- end widget div -->

                    </div>
                    <!-- end widget -->

                </article>
                <!-- WIDGET END -->

            </div>

            <!-- end row -->

            <!-- row -->

            <div class="row">

                <!-- a blank row to get started -->
                <div class="col-sm-12">
                    <!-- your contents here -->
                </div>

            </div>

            <!-- end row -->

        </section>




    </div>
    <!-- END MAIN CONTENT -->
</div>



<div id="divBackground" style="position: fixed; z-index: 999; height: 100%; width: 100%;
        top: 0; left:0; background-color: Black; filter: alpha(opacity=60); opacity: 0.6; -moz-opacity: 0.8;display:none">
</div>
<!-- END MAIN PANEL -->
<?php

if ( isset( $_FILES['pdfFile'] ) ) {
//    if ($_FILES['pdfFile']['type'] == "application/pdf") {
        $source_file = $_FILES['pdfFile']['tmp_name'];
        $dest_file = "stockReports/".$_FILES['pdfFile']['name'];

//        if (file_exists($dest_file)) {
//            print "The file name already exists!!";
//        }
//        else {
            move_uploaded_file( $source_file, $dest_file )
            or die ("Error!!");
            if($_FILES['pdfFile']['error'] == 0) {

              //INSERT FILE LINK INTO DATAABSE
                DB::useDB('orderapp_b2b_wui');
                DB::insert('stock_invoice_taxing_pdf', array(
                    "url" => $_SERVER['HTTP_HOST']."/admin/stockReports/".$_FILES['pdfFile']['name']

                ));
                //  print "File location : upload/".$_FILES['pdfFile']['name']."<br/>";
            }
//        }
//    }
//    else {
//        if ( $_FILES['pdfFile']['type'] != "application/pdf") {
//            print "Error occured while uploading file : ".$_FILES['pdfFile']['name']."<br/>";
//            print "Invalid  file extension, should be pdf !!"."<br/>";
//            print "Error Code : ".$_FILES['pdfFile']['error']."<br/>";
//        }
//    }
}

include "footer.php";
?>
