$(document).ready(function() {
    $('#example').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            'copy', 'excelFlash', 'excel', 'pdf', 'print',{
            text: 'Reload',
            action: function ( e, dt, node, config ) {
                dt.ajax.reload();
            }
        }
        ]
    } );
} );