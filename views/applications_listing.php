<?php $this->view('partials/head'); ?>

<div class="container">
  <div class="row">
    <div class="col-lg-12">

      <h3><span data-i18n="applications.reporttitle"></span> <span id="total-count" class='label label-primary'>…</span></h3>

      <table class="table table-striped table-condensed table-bordered">

        <thead>
          <tr>
            <th data-i18n="listing.computername" data-colname='machine.computer_name'></th>
            <th data-i18n="serial" data-colname='reportdata.serial_number'></th>
            <th data-i18n="name" data-colname='applications.name'></th>
            <th data-i18n="version" data-colname='applications.version'></th>
            <th data-i18n="applications.bundle_version" data-colname='applications.bundle_version'></th>
            <th data-i18n="applications.signed_by" data-colname='applications.signed_by'></th>
            <th data-i18n="applications.obtained_from" data-colname='applications.obtained_from'></th>
            <th data-i18n="applications.last_modified" data-colname='applications.last_modified'></th>
            <th data-i18n="applications.has64bit" data-colname='applications.has64bit'></th>
            <th data-i18n="applications.runtime_environment" data-colname='applications.runtime_environment'></th>
            <th data-i18n="path" data-colname='applications.path'></th>
            <th data-i18n="info" data-colname='applications.info'></th>
          </tr>
        </thead>

        <tbody>
          <tr>
            <td data-i18n="listing.loading" colspan="12" class="dataTables_empty"></td>
          </tr>
        </tbody>

      </table>
    </div> <!-- /span 12 -->
  </div> <!-- /row -->
</div>  <!-- /container -->

<script type="text/javascript">

    $(document).on('appUpdate', function(e){

        var oTable = $('.table').DataTable();
        oTable.ajax.reload();
        return;

    });

    $(document).on('appReady', function(e, lang) {

        // Get modifiers from data attribute
        var mySort = [], // Initial sort
            hideThese = [], // Hidden columns
            col = 0, // Column counter
            runtypes = [], // Array for runtype column
            columnDefs = [{ visible: false, targets: hideThese }]; //Column Definitions

        $('.table th').map(function(){

            columnDefs.push({name: $(this).data('colname'), targets: col, render: $.fn.dataTable.render.text()});

            if($(this).data('sort')){
              mySort.push([col, $(this).data('sort')])
            }

            if($(this).data('hide')){
              hideThese.push(col);
            }

            col++
        });

        oTable = $('.table').dataTable( {
            ajax: {
                url: appUrl + '/datatables/data',
                type: "POST",
                data: function(d){
                     d.mrColNotEmpty = "name";

                    // Check for column in search
                    if(d.search.value){
                        $.each(d.columns, function(index, item){
                            if(item.name == 'applications.' + d.search.value){
                                d.columns[index].search.value = '> 0';
                            }
                        });

                    }
                    // IDK what this does
                    if(d.search.value.match(/^\d+\.\d+(\.(\d+)?)?$/)){
                        var search = d.search.value.split('.').map(function(x){return ('0'+x).slice(-2)}).join('');
                        d.search.value = search;
                    }
                }
            },
            dom: mr.dt.buttonDom,
            buttons: mr.dt.buttons,
            order: mySort,
            columnDefs: columnDefs,
            createdRow: function( nRow, aData, iDataIndex ) {
                // Update name in first column to link
                var name=$('td:eq(0)', nRow).html();
                if(name == ''){name = "No Name"};
                var sn=$('td:eq(1)', nRow).html();
                var link = mr.getClientDetailLink(name, sn, '#tab_applications-tab');
                $('td:eq(0)', nRow).html(link);
                
                // Localize Obtained From
                var obtained_from=$('td:eq(6)', nRow).html();
                obtained_from = obtained_from == 'unknown' ? i18n.t('unknown') :
                obtained_from = obtained_from == 'mac_app_store' ? i18n.t('applications.mac_app_store') :
                obtained_from = obtained_from == 'apple' ? "Apple":
                (obtained_from === 'identified_developer' ? i18n.t('applications.identified_developer') : obtained_from)
                $('td:eq(6)', nRow).html(obtained_from)
                
                // Format date
                var event = parseInt($('td:eq(7)', nRow).html());
                if (event > 0){
                    var date = new Date(event * 1000);
                    $('td:eq(7)', nRow).html('<span title="' + moment(date).fromNow() + '">'+ moment(date).format('llll')+'</span>');
                }

                // runtime_environment
                var colbit=$('td:eq(8)', nRow).html();
                var colvar=$('td:eq(9)', nRow).html();
                colvar = colvar == 'arch_x86' && colbit == '1' ? 'Intel 64-bit' :
                colvar = colvar == 'arch_x86' && colbit == '0' ? 'Intel 32-bit' :
                colvar = colvar == 'arch_i64' ? 'Intel 64-bit' :
                colvar = colvar == 'arch_i32_i64' ? 'Intel 32/64-bit' :
                colvar = colvar == 'arch_i32' ? 'Intel 32-bit' :
                colvar = colvar == 'arch_arm_i64' ? 'Universal 2' :
                colvar = colvar == 'arch_ios' ? 'Apple Silicon' :
                colvar = colvar == 'arch_other' ? 'Unknown' :
                colvar = colvar == 'arch_arm' ? 'Apple Silicon' :
                (colvar == 'arch_arm' ? 'Apple Silicon' : colvar)
                $('td:eq(9)', nRow).html(colvar)

                // has64bit
                var colvar=$('td:eq(8)', nRow).html();
                colvar = colvar == '1' ? i18n.t('yes') :
                (colvar == '0' ? i18n.t('no') : '')
                $('td:eq(8)', nRow).html(colvar)
            }
        });

    });
</script>

<?php $this->view('partials/foot'); ?>
