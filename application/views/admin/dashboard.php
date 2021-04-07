<title>Admin - COS Exhibitor Graphics Site</title>

<main role="main" style="margin-top: 70px;margin-left: 20px;margin-right: 20px;">
    <div class="row">
        <div class="col-md-12">
            <h3>Presentations</h3>
            <p>Loaded presentations are listed here</p>

            <div id="lastUpdatedAlert" class="alert alert-warning alert-dismissible fade show" role="alert" style="display:none;">
                This list was last loaded on <strong><span id="lastUpdated"></span></strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

        </div>

        <div class="col-md-12">
            <button class="create-presentation-btn btn btn-success float-right"><i class="fas fa-plus"></i> Create</button>
            <table id="presentationTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                <tr>
                    <th>Status</th>
                    <th>ID</th>
                    <th>Company</th>
                    <th>Salutation</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Booth Style</th>
                    <th>Info</th>
                    <th>Actions</th>
                </tr>
                </thead>

                <tbody id="presenterBoothTableBody">
                <!-- Will be filled by JQuery AJAX -->
                </tbody>

            </table>
        </div>

    </div>

    <hr>
</main>

<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap4.min.css" crossorigin="anonymous" />


<script>
    $(document).ready(function() {

        loadPresenterBooths();

        $('#example-upload-btn').on('click', function () {
            toastr.warning('You need to click one of the similar buttons listed below to upload files.');
        });

        $('.change-pass-btn').on('click', function () {
            $('#changePasswordModal').modal('show');
        });

        $('#presenterBoothTableBody').on('click', '.files-btn', function () {

            let presenter_id = $(this).attr('presenter-id');
            let booth_id = $(this).attr('booth-id');
            let company_id = $(this).attr('company-id');
            let company_name = $(this).attr('company-name');
            let booth_style = $(this).attr('booth-style');

            showFiles(presenter_id, booth_id, company_id, company_name, booth_style);
        });

        $('#presenterBoothTableBody').on('click', '.details-btn', function () {

            let user_id = $(this).attr('user-id');
            let booth_id = $(this).attr('booth-id');
            let company_id = $(this).attr('company-id');

            showFiles(user_id, booth_id, company_id);
        });

        $('#presenterBoothTableBody').on('click', '.activate-presentation-btn', function () {
            let button = $(this);
            let booth_id = $(this).attr('booth-id');
            let company_id = $(this).attr('company-id');
            let presenter_id = $(this).attr('presenter-id');

            activatePresentation(button, booth_id, company_id, presenter_id);
        });

        $('#presenterBoothTableBody').on('click', '.disable-presentation-btn', function () {
            let button = $(this);
            let booth_id = $(this).attr('booth-id');
            let company_id = $(this).attr('company-id');
            let presenter_id = $(this).attr('presenter-id');

            disablePresentation(button, booth_id, company_id, presenter_id);
        });

        $('#presenterBoothTableBody').on('click', '.presentation-logs-btn', function () {
            toastr.warning("Under development");
        });

        $('#presenterBoothTableBody').on('click', '.edit-presentation-btn', function () {
            toastr.warning("Under development");
        });

        $('.create-presentation-btn').on('click', function () {
            toastr.warning("Under development");
        });

    } );

    function loadPresenterBooths() {
        $.get( "<?=base_url('admin/dashboard/getPresentersBooth')?>", function(response) {
            response = JSON.parse(response);
            console.log(response);
            if ( $.fn.DataTable.isDataTable('#presenterBoothTable') ) {
                $('#presenterBoothTable').DataTable().destroy();
            }

            $('#presenterBoothTableBody').html('');
            $.each(response.data, function(i, presenterBooth) {

                let statusBadge = (presenterBooth.uploadStatus)?'<span class="badge badge-success mr-1"><i class="fas fa-check-circle"></i> '+presenterBooth.uploadStatus+' File(s) uploaded</span>':'<span class="badge badge-warning mr-1"><i class="fas fa-exclamation-circle"></i> No Uploads</span>';
                statusBadge += (presenterBooth.active==1)?'<span class="active-status badge badge-success" presentation-id="'+presenterBooth.id+'"><i class="fas fa-check"></i> Active</span>':'<span class="disabled-status badge badge-danger" presentation-id="'+presenterBooth.id+'"><i class="fas fa-times"></i> Disabled</span>';

                let filesBtn = '<button class="files-btn btn btn-sm btn-info text-white"  presenter-id="'+presenterBooth.presenter_id+'" company-name="'+presenterBooth.name+'" company-id="'+presenterBooth.company_id+'" booth-id="'+presenterBooth.id+'"  booth-style="'+presenterBooth.style+'"  user-id="<?=$this->session->userdata('user_id')?>"><i class="fas fa-folder-open"></i> Files</button>';
                let logsBtn = '<button class="presentation-logs-btn btn btn-sm btn-warning text-white mt-1" session-name="'+presenterBooth.session_name+'" presentation-name="'+presenterBooth.name+'" user-id="<?=$this->session->userdata('user_id')?>" presentation-id="'+presenterBooth.id+'"><i class="fas fa-history"></i> Logs</button>';

                let editBtn = '<button class="edit-presentation-btn btn btn-sm btn-primary text-white"><i class="fas fa-edit"></i> Edit</button>';
                let disableBtn = (presenterBooth.active==0)?'<button class="activate-presentation-btn btn btn-sm btn-success text-white mt-1" booth-id="'+presenterBooth.id+'" company-id="'+presenterBooth.company_id+'" presenter-id="'+presenterBooth.presenter_id+'"><i class="fas fa-check"></i> Activate</button>':'<button class="disable-presentation-btn btn btn-sm btn-danger text-white mt-1" booth-id="'+presenterBooth.id+'" company-id="'+presenterBooth.company_id+'" presenter-id="'+presenterBooth.presenter_id+'"><i class="fas fa-times"></i> Disable</button>';

                $('#presenterBoothTableBody').append('' +
                    '<tr>\n' +
                    '  <td>\n' +
                    '    '+statusBadge+'\n'     +
                    '  </td>\n' +
                    '  <td>'+presenterBooth.id+'</td>\n' +
                    '  <td>'+presenterBooth.name+'</td>\n' +
                    '  <td>'+presenterBooth.name_prefix+'</td>\n' +
                    '  <td>'+presenterBooth.first_name+presenterBooth.last_name+'</td>\n' +
                    '  <td>'+presenterBooth.email+'</td>\n' +
                    '  <td>'+presenterBooth.style+'</td>\n' +
                    '  <td>\n' +
                    '    '+filesBtn+'\n' +
                    '    '+logsBtn+'\n' +
                    '  </td>\n' +
                    '  <td>\n' +
                    '   '+editBtn+'\n' +
                    '   '+disableBtn+'\n' +
                    '  </td>\n' +
                    '</tr>');
            });

            $('#presenterBoothTable').DataTable({
                initComplete: function() {
                    $(this.api().table().container()).find('input').attr('autocomplete', 'off');
                    $(this.api().table().container()).find('input').attr('type', 'text');
                    $(this.api().table().container()).find('input').val('upload');
                    //$(this.api().table().container()).find('input').val('');
                }
            });

            $('#lastUpdated').text(formatDateTime(response.data[0].created_on, false));
            $('#lastUpdatedAlert').show();
        })
            .fail(function(response) {
                $('#sessionsTable').DataTable();
                toastr.error("Unable to load your presentations data");
            });
    }

    function formatDateTime(datetimeStr, include_year = true) {
        let lastUpdatedDate = new Date(datetimeStr);
        let year = new Intl.DateTimeFormat('en', { year: 'numeric' }).format(lastUpdatedDate);
        let month = new Intl.DateTimeFormat('en', { month: 'long' }).format(lastUpdatedDate);
        let day = new Intl.DateTimeFormat('en', { day: '2-digit' }).format(lastUpdatedDate);
        let time = lastUpdatedDate.toLocaleTimeString('en-US', { hour: 'numeric', hour12: true, minute: 'numeric' });

        return ((include_year)?year+' ':'')+month+', '+day+'th '+time;
    }

    function activatePresentation(button, booth_id, company_id, presenter_id) {
        $.get( "<?=base_url('admin/dashboard/activatePresentation/')?>"+booth_id+"/"+company_id+"/"+presenter_id, function(response) {
            response = JSON.parse(response);

            if (response.status == 'success')
            {
                $('.disabled-status[booth-id="'+booth_id+'"]').html('<i class="fas fa-check"></i> Active');
                $('.disabled-status[booth-id="'+booth_id+'"]').removeClass('badge-danger');
                $('.disabled-status[booth-id="'+booth_id+'"]').addClass('badge-success');
                $('.disabled-status[booth-id="'+booth_id+'"]').addClass('active-status');
                $('.disabled-status[booth-id="'+booth_id+'"]').removeClass('disabled-status');

                button.removeClass('activate-presentation-btn');
                button.addClass('disable-presentation-btn');
                button.removeClass('btn-success');
                button.addClass('btn-danger');
                button.html('<i class="fas fa-times"></i> Disable');

                toastr.success(response.msg);
            }else{
                toastr.error(response.msg);
            }

        }).fail(function() {
            toastr.error('Unable activate the presentation');
        })
    }

    function disablePresentation(button, booth_id, company_id, presenter_id) {
        $.get( "<?=base_url('admin/dashboard/disablePresentation/')?>"+booth_id+"/"+company_id+"/"+presenter_id, function(response) {
            response = JSON.parse(response);

            if (response.status == 'success')
            {
                $('.active-status[booth-id="'+booth_id+'"]').html('<i class="fas fa-times"></i> Disabled');
                $('.active-status[booth-id="'+booth_id+'"]').removeClass('badge-success');
                $('.active-status[booth-id="'+booth_id+'"]').addClass('badge-danger');
                $('.active-status[booth-id="'+booth_id+'"]').addClass('disabled-status');
                $('.active-status[booth-id="'+booth_id+'"]').removeClass('active-status');

                button.removeClass('disable-presentation-btn');
                button.addClass('activate-presentation-btn');
                button.removeClass('btn-danger');
                button.addClass('btn-success');
                button.html('<i class="fas fa-check"></i> Activate');

                toastr.success(response.msg);
            }else{
                toastr.error(response.msg);
            }

        }).fail(function() {
            toastr.error('Unable disable the presentation');
        })
    }

</script>

