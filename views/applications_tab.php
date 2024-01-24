<div id="applications-tab"></div>
<h2 data-i18n="applications.applications"></h2>
<div id="applications-msg" data-i18n="listing.loading" class="col-lg-12 text-center"></div>


<div id="applications-table-view" class="row hide" style="padding-left: 15px; padding-right: 15px;">
  <table class="table table-striped table-condensed table-bordered" id="applications-history-table">
    <thead>
      <tr>
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
            <td data-i18n="listing.loading" colspan="10" class="dataTables_empty"></td>
        </tr>
    </tbody>
  </table>
</div>


<script>
    $(document).on('appReady', function(e, lang) {

        // Get applications data
        $.getJSON( appUrl + '/module/applications/get_data/' + serialNumber, function( data ) {
            if( ! data ){
                $('#applications-msg').text(i18n.t('no_data'));
                $('#applications-cnt').text(0);

            } else {
               // Hide
                $('#applications-msg').text('');
                $('#applications-table-view').removeClass('hide');

                // Set count of applications
                $('#applications-cnt').text(data.length);

                $('#applications-history-table').DataTable({
                    data: data,
                    order: [[0,'asc']],
                    autoWidth: false,
                    columnDefs: [{
                        targets: "_all",
                        render: $.fn.dataTable.render.text()
                    }],
                    columns: [
                        { data: 'name' },
                        { data: 'version' },
                        { data: 'bundle_version' },
                        { data: 'signed_by' },
                        { data: 'obtained_from' },
                        { data: 'last_modified' },
                        { data: 'has64bit' },
                        { data: 'runtime_environment' },
                        { data: 'path' },
                        { data: 'info' }
                    ],
                    createdRow: function( nRow, aData, iDataIndex ) {
                        // Localize Obtained From
                        var obtained_from=$('td:eq(4)', nRow).html();
                        obtained_from = obtained_from == 'unknown' ? i18n.t('unknown') :
                        obtained_from = obtained_from == 'mac_app_store' ? i18n.t('applications.mac_app_store') :
                        obtained_from = obtained_from == 'apple' ? "Apple":
                        (obtained_from == 'identified_developer' ? i18n.t('applications.identified_developer') : obtained_from)
                        $('td:eq(4)', nRow).text(obtained_from)

                        // Format date
                        var event = parseInt($('td:eq(5)', nRow).html());
                        if (event > 0){
                            var date = new Date(event * 1000);
                            $('td:eq(5)', nRow).html('<span title="' + moment(date).fromNow() + '">'+moment(date).format('llll')+'</span>');
                        }

                        // runtime_environment
                        var colbit=$('td:eq(6)', nRow).html();
                        var colvar=$('td:eq(7)', nRow).html();
                        colvar = colvar == 'arch_x86' && colbit == '1' ? 'Intel 64-bit' :
                        colvar = colvar == 'arch_x86' && colbit == '0' ? 'Intel 32-bit' :
                        colvar = colvar == 'arch_i64' ? 'Intel 64-bit' :
                        colvar = colvar == 'arch_i32_i64' ? 'Intel 32/64-bit' :
                        colvar = colvar == 'arch_i32' ? 'Intel 32-bit' :
                        colvar = colvar == 'arch_arm_i64' ? 'Universal 2' :
                        colvar = colvar == 'arch_ios' ? 'Apple Silicon' :
                        colvar = colvar == 'arch_arm' ? 'Apple Silicon' :
                        colvar = colvar == 'arch_web' ? 'Web App' :
                        colvar = colvar == 'arch_other' ? 'Unknown' :
                        (colvar == 'arch_arm' ? 'Apple Silicon' : colvar)
                        $('td:eq(7)', nRow).text(colvar)

                        // has64bit
                        var colvar=$('td:eq(6)', nRow).html();
                        colvar = colvar == '1' ? i18n.t('yes') :
                        (colvar == '0' ? i18n.t('no') : '')
                        $('td:eq(6)', nRow).text(colvar)
                    }
                });
            }
        });
    });
</script>