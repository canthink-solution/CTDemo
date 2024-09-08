<?php
$loginRequired = false; // set this to true if this page required login session
$permissionPage = NULL;
$titlePage = "Test";
$currentPage = 'test';
include 'template/header.php';
?>

<div class="container-fluid flex-grow-1 container-p-y">

    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light"> <?= strtoupper($titlePage) ?> </span>
    </h4>

    <div class="row">
        <div class="col-lg-12 order-2 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <!-- FILTER -->
                    <div class="col-xl-12">
                        <div class="row">
                            <div class="col-xl-12 mb-4">
                                <button type="button" class="btn btn-warning btn-sm float-end ms-2" onclick="refresh()" title="Refresh">
                                    <i class="fa-solid fa-arrows-rotate"></i>
                                </button>

                                <button type="button" class="btn btn-outline-dark btn-sm float-end ms-2" onclick="loadModalOffcanvas()" title="load modal offcanvas">
                                    <i class="fa-regular fa-window-restore"></i> &nbsp; OffCanvas (BS5)
                                </button>

                                <button type="button" class="btn btn-outline-primary btn-sm float-end ms-2" onclick="loadModalDynamic('sm')" title="load modal small">
                                    <i class="fa-regular fa-window-restore"></i> &nbsp; Modal SM
                                </button>

                                <button type="button" class="btn btn-outline-danger btn-sm float-end ms-2" onclick="loadModalDynamic('md')" title="load modal medium">
                                    <i class="fa-regular fa-window-restore"></i> &nbsp; Modal MD
                                </button>

                                <button type="button" class="btn btn-outline-warning btn-sm float-end ms-2" onclick="loadModalDynamic('lg')" title="load modal large">
                                    <i class="fa-regular fa-window-restore"></i> &nbsp; Modal LG
                                </button>

                                <button type="button" class="btn btn-outline-info btn-sm float-end ms-2" onclick="loadModalDynamic('xl')" title="load modal xtra large">
                                    <i class="fa-regular fa-window-restore"></i> &nbsp; Modal XL
                                </button>

                                <button type="button" class="btn btn-outline-warning btn-sm float-end ms-2" onclick="loadModalDynamic('fullscreen')" title="load modal fullscreen">
                                    <i class="fa-regular fa-window-restore"></i> &nbsp; Modal FullScreen (BS5)
                                </button>

                                <button type="button" class="btn btn-info btn-sm float-end ms-2" onclick="testRunTask()" title="Task Runner Parallel">
                                    <i class="fa fa-tasks"></i> &nbsp; Task Runner
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- FILTER -->

                    <div class="col-lg-12 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div id="getTestDatabase"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script type="text/javascript">
    $(document).ready(async function() {
        // await refresh();
    });

    async function refresh() {
        await getSqlString();
    }

    async function getSqlString() {
        const res = await callApi('post', "TestController", {
            'action': 'testRunJob',
        });

        if (isSuccess(res)) {
            const data = res.data;
            $('#getTestDatabase').html(`<b>Test 1</b> : Return the SQL using toSql() method. <br> Result : ${data} <br> <hr>`);
        }
    }
</script>


<?php include 'template/footer.php' ?>