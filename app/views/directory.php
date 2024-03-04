<?php
$loginRequired = true;
$permissionPage = 'dashboard-view';
$titlePage = "Direktori";
$currentPage = 'directory';
include 'template/header.php';
?>

<div class="container-fluid flex-grow-1 container-p-y">

    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light"> <?= strtoupper($titlePage) ?> </span>
    </h4>

    <div class="col-lg-12 order-2 mb-4">
        <div class="card h-100">
            <div class="card-body">

                <!-- FILTER -->
                <div class="row">
                    <div class="col-xl-12 mb-4">
                        <button type="button" class="btn btn-warning btn-sm float-end" onclick="getDataList()" title="Refresh">
                            <i class='bx bx-refresh'></i>
                        </button>

                        <button type="button" class="btn btn-info btn-sm me-2 float-end" onclick="userForm()" title="Tampah Kakitangan">
                            <i class='bx bx-plus'></i> Kakitangan
                        </button>

                        <select id="status" class="form-control form-control-sm me-2 float-end" style="width: 100px;" onchange="getDataList()">
                            <option value="" selected> Semua </option>
                            <option value="1"> Aktif </option>
                            <option value="0"> Tidak Aktif </option>
                        </select>
                    </div>
                </div>

                <!-- DATATABLE -->
                <div id="bodyDiv" class="row">
                    <div class="col-xl-12 mb-4">
                        <div id="nodataDiv" style="display: block;"> <?= nodata() ?> </div>
                        <div id="dataListDiv" class="table-responsive" style="display: none;">
                            <table id="dataList" class="table table-responsive table-hover table-striped table-bordered collapsed nowrap" width="100%">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="color:white"> Avatar </th>
                                        <th style="color:white"> Nama Penuh </th>
                                        <th style="color:white"> Email </th>
                                        <th style="color:white"> Bahagian </th>
                                        <th style="color:white"> Peranan </th>
                                        <th style="color:white"> Status </th>
                                        <th style="color:white"> Action </th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

<script type="text/javascript">
    $(document).ready(async function() {
        // await getDataList();
    });

    const getDataList = () => {
        log('test');
    }

    async function update(id) {
        const res = await callApi('post', "DirectoryController", {
            'action': 'getUserByID',
            'id': id,
        });

        if (isSuccess(res)) {
            userForm(res.data);
        }
    }

    function userForm(data = null) {
        const header = hasData(data) ? 'Kemaskini Kakitangan' : 'Tambah Kakitangan';
        const urlAction = hasData(data) ? 'updateUser' : 'createUser';

        const dataSent = {
            ...data,
            'action': urlAction
        };

        loadFormContent('modals/_userDirectoryForm.php', '500px', `DirectoryController`, header, dataSent, 'offcanvas');
    }
</script>

<?php include 'template/footer.php' ?>