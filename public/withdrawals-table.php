<?php
if (isset($_POST['btnUnpaid']) && isset($_POST['enable'])) {
    for ($i = 0; $i < count($_POST['enable']); $i++) {
        
    
        $enable = $db->escapeString($fn->xss_clean($_POST['enable'][$i]));
        $sql = "UPDATE withdrawals SET status=0 WHERE id = $enable";
        $db->sql($sql);
        $result = $db->getResult();
    }
}
if (isset($_POST['btnPaid'])  && isset($_POST['enable'])) {
    for ($i = 0; $i < count($_POST['enable']); $i++) {
    
        $enable = $db->escapeString($fn->xss_clean($_POST['enable'][$i]));
        $sql = "UPDATE withdrawals SET status=1 WHERE id = $enable";
        $db->sql($sql);
        $result = $db->getResult();
    }
}
if (isset($_POST['btnCancel'])  && isset($_POST['enable'])) {
    for ($i = 0; $i < count($_POST['enable']); $i++) {
    
        $enable = $db->escapeString($fn->xss_clean($_POST['enable'][$i]));
        $sql = "UPDATE withdrawals SET status=2 WHERE id = $enable";
        $db->sql($sql);
        $result = $db->getResult();
    }
}

?>


<section class="content-header">
    <h1>Withdrawals /<small><a href="home.php"><i class="fa fa-home"></i> Home</a></small></h1>

</section>
    <!-- Main content -->
    <section class="content">
        <!-- Main row -->
        <form name="withdrawal_form" method="post" enctype="multipart/form-data">
        <div class="row">
            <!-- Left col -->
            <div class="col-12">
                <div class="box">
                      <div class="box-header">
                            <div class="row">
                                    <div class="form-group col-md-3">
                                            <h4 class="box-title">Filter by Status </h4>
                                            <select id='status' name="status" class='form-control'>
                                                    <option value="">All</option>
                                                    <option value="0">Unpaid</option>
                                                    <option value="1">Paid</option>
                                                    <option value="2">Cancelled</option>
                                            </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <h4 class="box-title">Filter by Name </h4>
                                            <select id='user_id' name="user_id" class='form-control'>
                                            <option value=''>All</option>
                                             
                                                    <?php
                                                    $sql = "SELECT id,name FROM `users`";
                                                    $db->sql($sql);
                                                    $result = $db->getResult();
                                                    foreach ($result as $value) {
                                                    ?>
                                                        <option value='<?= $value['id'] ?>'><?= $value['name'] ?></option>
                                                <?php } ?>
                                            </select>
                                    </div>
                            </div>
                       </div>
                       <!-- /.box-header -->
                    <div class="box-body table-responsive">
                            <div class="row">
                                <div class="text-left col-md-2">
                                    <input type="checkbox" onchange="checkAll(this)" name="chk[]" > Select All</input>
                                </div> 
                                <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary" name="btnUnpaid">Unpaid</button>
                                        <button type="submit" class="btn btn-success" name="btnPaid">Paid</button>
                                        <button type="submit" class="btn btn-danger" name="btnCancel">Cancelled</button>
                                        
                                 </div>

                            </div>
                        <table id='users_table' class="table table-hover" data-toggle="table" data-url="api-firebase/get-bootstrap-table-data.php?table=withdrawals" data-page-list="[5, 10, 20, 50, 100, 200]" data-show-refresh="true" data-show-columns="true" data-side-pagination="server" data-pagination="true" data-search="true" data-trim-on-search="false" data-filter-control="true" data-query-params="queryParams" data-sort-name="w.id" data-show-footer="true" data-sort-order="desc" data-show-export="false" data-export-types='["txt","excel"]' data-export-options='{
                            "fileName": "students-list-<?= date('d-m-Y') ?>",
                            "ignoreColumn": ["operate"] 
                        }'>
                            <thead>
                                <tr>
                                     <th data-field="column"> All</th>
                                    <th data-field="id" data-sortable="true">ID</th>
                                    <th data-field="name" data-sortable="true" data-visible="true" data-footer-formatter="totalFormatter">Name</th>
                                    <th data-field="amount" data-sortable="true" data-visible="true" data-footer-formatter="priceFormatter">Amount</th>
                                    <th data-field="datetime" data-sortable="true">DateTime</th>
                                    <th data-field="account_num" data-sortable="true">Account Number</th>
                                    <th data-field="holder_name" data-sortable="true">Holder Name</th>
                                    <th data-field="bank" data-sortable="true">Bank</th>
                                    <th data-field="branch" data-sortable="true">Branch</th>
                                    <th data-field="ifsc" data-sortable="true">IFSC</th>
                                    <th data-field="status" data-sortable="true">Status</th>
                                    <!-- <th  data-field="operate" data-events="actionEvents">Action</th> -->
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <div class="separator"> </div>
        </div>
        </form>

        <!-- /.row (main row) -->
    </section>
<script>
 function checkAll(ele) {
     var checkboxes = document.getElementsByTagName('input');
     if (ele.checked) {
         for (var i = 0; i < checkboxes.length; i++) {
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = true;
             }
         }
     } else {
         for (var i = 0; i < checkboxes.length; i++) {
             console.log(i)
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = false;
             }
         }
     }
 }
    
</script>
<script>
        $('#status').on('change', function() {
            id = $('#status').val();
            $('#users_table').bootstrapTable('refresh');
        });
        $('#user_id').on('change', function() {
            id = $('#user_id').val();
            $('#users_table').bootstrapTable('refresh');
        });

    function queryParams(p) {
        return {
            "status": $('#status').val(),
            "user_id": $('#user_id').val(),
            limit: p.limit,
            sort: p.sort,
            order: p.order,
            offset: p.offset,
            search: p.search
        };
    }
    function totalFormatter() {
        return '<span style="color:green;font-weight:bold;font-size:large;">TOTAL</span>'
    }

    var total = 0;

    function priceFormatter(data) {
        var field = this.field
        return '<span style="color:green;font-weight:bold;font-size:large;"> ' + data.map(function(row) {
                return +row[field]
            })
            .reduce(function(sum, i) {
                return sum + i
            }, 0);
    }
</script>
<script>
    $(document).ready(function () {
        $('#user_id').select2({
        width: 'element',
        placeholder: 'Type in name to search',

    });
    });

</script>
