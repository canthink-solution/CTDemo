<?php
$loginRequired = false; // set this to true if this page required login session
$permissionPage = NULL;
$titlePage = "Example";
$currentPage = 'example';
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
                                <div id="getSqlString"></div>
                                <div id="getData"></div>
                                <div id="getDataWithLimit"></div>
                                <div id="getDataWithConditions"></div>
                                <div id="fetchDataWithConditions"></div>
                                <div id="getDataPaginate"></div>
                                <div id="getDataCount"></div>
                                <div id="getDataJoin"></div>

                                <div id="getDataSlaveDB">
                                    <div class="container">
                                        <h4> Example 9 : Bootstrap Table from paginate() </h4>
                                        <table class="table table-striped table-hover table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                </tr>
                                            </thead>
                                            <tbody id="table-body">
                                                <!-- Table rows will be dynamically added here -->
                                            </tbody>
                                        </table>
                                        <center>
                                            <div id="paginate" class="mt-3 text-center"></div>
                                        </center>
                                    </div>
                                </div>

                                <div id="getValidationData"></div>
                                <div id="testLogger"></div>
                                <div id="testInsert"></div>
                                <div id="testUpdate"></div>
                                <div id="testDelete"></div>
                                <div id="testRunTask"></div>
                                <div id="testUploadFiles">
                                    <form id="submitFile" action="ExampleController" method="post" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="file">Name</label><br>
                                                <input type="text" id="name" name="name" class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="file">Email</label><br>
                                                <input type="email" id="email" name="email" class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="file">Age</label><br>
                                                <input type="number" id="age" name="age" min="1" step="1" class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="file">Tel. No</label><br>
                                                <input type="text" id="phone_no" name="phone_no" class="form-control">
                                            </div>
                                        </div>
                                        <label for="file">Select file(s) to upload:</label><br>
                                        <input type="file" id="file" name="file"><br><br>
                                        <input type="hidden" id="action" name="action" value="testRequestFunc" class="form-control" readonly>
                                        <input type="submit" id="submitBtn" value="Upload">
                                    </form>
                                </div>
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
        await refresh();
    });

    async function refresh() {
        await getSqlString();
        await getData();
        await getDataWithLimit();
        await getDataWithConditions();
        await fetchDataWithConditions();
        await getDataPaginate();
        await getDataCount();
        // await getDataJoin();
        await getDataSlaveDB();
        await getValidationData();
        // await testLogger();
        // await testInsert();
        // await testUpdate();
        // await testDelete();
        // await testRunTask();
    }

    async function getSqlString() {
        const res = await callApi('post', "ExampleController", {
            'action': 'getSql',
        });

        if (isSuccess(res)) {
            const data = res.data;
            $('#getSqlString').html(`<b>Test 1</b> : Return the SQL using toSql() method. <br> Result : ${data} <br> <hr>`);
        }
    }

    async function getData() {
        const res = await callApi('post', "ExampleController", {
            'action': 'getData',
        });

        if (isSuccess(res)) {
            const data = res.data;
            $('#getData').html(`<b>Test 2</b> : Return the data using get() method. <br> Result : ${JSON.stringify(data)} <br> <hr>`);
        }
    }

    async function getDataWithLimit() {
        const res = await callApi('post', "ExampleController", {
            'action': 'getDataLimit',
            'limit': 2
        });

        if (isSuccess(res)) {
            const data = res.data;
            $('#getDataWithLimit').html(`<b>Test 3</b> : Return the data using get() method with limit. <br> Result : ${JSON.stringify(data)} <br> <hr>`);
        }
    }

    async function getDataWithConditions() {
        const res = await callApi('post', "ExampleController", {
            'action': 'getDataConditions'
        });

        if (isSuccess(res)) {
            const data = res.data;
            $('#getDataWithConditions').html(`<b>Test 4</b> : Return the data using get() method with conditions where(). <br> Result : ${JSON.stringify(data)} <br> <hr>`);
        }
    }

    async function fetchDataWithConditions() {
        const res = await callApi('post', "ExampleController", {
            'action': 'fetchDataConditions'
        });

        if (isSuccess(res)) {
            const data = res.data;
            $('#fetchDataWithConditions').html(`<b>Test 5</b> : Return the data using fetch() method with conditions where(). <br> Result : ${JSON.stringify(data)} <br> <hr>`);
        }
    }

    async function getDataPaginate(page = 1) {
        const res = await callApi('post', "ExampleController", {
            'action': 'getDataPagination',
            'page': page,
        });

        $('#getDataPaginate').empty();
        if (isSuccess(res)) {
            const data = res.data;
            $('#getDataPaginate').html(`<b>Test 6</b> : Return the data using paginate() method. <br> Result : ${JSON.stringify(data)} <br> <hr>`);
            $('#getDataPaginate').append(generateBsPagination(data));
        }
    }

    async function getDataCount() {
        const res = await callApi('post', "ExampleController", {
            'action': 'getDataCountTotal',
        });

        if (isSuccess(res)) {
            const data = res.data;
            $('#getDataCount').html(`<b>Test 7</b> : Return the total data using count() method. <br> Result : ${data} <br> <hr>`);
        }
    }

    async function getDataJoin() {
        const res = await callApi('post', "ExampleController", {
            'action': 'getDataJoinConditions',
            'id': 1,
            'measure_id': 2,
            'item_id': 2
        });

        if (isSuccess(res)) {
            const data = res.data;
            $('#getDataJoin').html(`<b>Test 8</b> : Return the join data using join() method. <br> Result : ${JSON.stringify(data)} <br> <hr>`);
        }
    }

    async function getValidationData() {
        const res = await callApi('post', "ExampleController", {
            'action': 'getValidationData'
        });

        if (isSuccess(res)) {
            const data = res.data;
            $('#getValidationData').html(`<b>Test 10</b> : Return the data validation. <br> Result : ${JSON.stringify(data)} <br> <hr>`);
        }
    }

    async function testLogger() {
        const res = await callApi('post', "ExampleController", {
            'action': 'testLoggerClass'
        });

        if (isSuccess(res)) {
            const data = res.data;
            $('#testLogger').html(`<b>Test 11</b> : No return, Please check storage/logs folder. <br> <hr>`);
        }
    }

    async function testInsert() {
        const res = await callApi('post', "ExampleController", {
            'action': 'testInsertFunc'
        });

        if (isSuccess(res)) {
            const data = res.data;
            $('#testInsert').html(`<b>Test 12</b> : Return the data insert. <br> Result : ${JSON.stringify(data)} <br> <br> <hr>`);
        }
    }

    async function testUpdate() {
        const res = await callApi('post', "ExampleController", {
            'action': 'testUpdateFunc'
        });

        if (isSuccess(res)) {
            const data = res.data;
            $('#testUpdate').html(`<b>Test 12</b> : Return the data update. <br> Result : ${JSON.stringify(data)} <br> <br> <hr>`);
        }
    }

    async function testDelete() {
        const res = await callApi('post', "ExampleController", {
            'action': 'testDeleteFunc'
        });

        if (isSuccess(res)) {
            const data = res.data;
            $('#testDelete').html(`<b>Test 13</b> : Return the data delete. <br> Result : ${JSON.stringify(data)} <br> <br> <hr>`);
        }
    }

    async function testRunTask() {
        const res = await callApi('post', "ExampleController", {
            'action': 'testRunnerFunc'
        });

        if (isSuccess(res)) {
            const data = res.data;
            $('#testRunTask').html(`<b>Test 14</b> : -. <br> Result : Please open log files in folder storage/logs after click Task Runner button <br> <br> <hr>`);
        }
    }

    $("#submitFile").submit(async function(event) {
        event.preventDefault();

        const form = $(this);
        let url = form.attr('action');
        const fr = $("#submitFile");
        const dataArr = new FormData(fr[0]);

        const res = await submitApi(url, form.serializeArray(), 'submitFile');
        if (isSuccess(res.data.code))
            noti(res.data.code, res.data.message);
    });

    // EXAMPLE FOR LOAD DYNAMIC MODAL

    function loadModalOffcanvas() {
        const dataToPass = {};
        loadFileContent('modals/_documentPreviewModal.php', '650px', 'VIEW MODAL DOCUMENT (OffCanvas)', dataToPass, 'offcanvas');
    }

    function loadModalDynamic(sizeModal = 'xs') {
        const dataToPass = {
            'size': sizeModal
        };
        loadFileContent('modals/_documentPreviewModal.php', sizeModal, `VIEW MODAL DOCUMENT (${strtoupper(sizeModal)})`, dataToPass);
    }

    // EXAMPLE FOR PAGINATE

    let pageS = 1;

    function updatePageS(page) {
        pageS = page;
        getDataSlaveDB();
    }

    async function getDataSlaveDB() {
        const res = await callApi('post', "ExampleController", {
            'action': 'getDataSlaveDBConditions',
            'current_page': pageS
        });

        if (isSuccess(res)) {
            const response = res.data;

            var tableBodyHtml = '';
            $.each(response.data, function(index, item) {
                tableBodyHtml += '<tr>' +
                    '<td>' + item.id + '</td>' +
                    '<td>' + item.name + '</td>' +
                    '<td>' + item.email + '</td>' +
                    '</tr>';
            });
            $('#table-body').html(tableBodyHtml); // Set the HTML content of tbody

            // $('#getDataSlaveDB').html(`<b>Test 9</b> : Return the data from different database connection. <br> Result : ${JSON.stringify(response)} <br> <hr>`);
            $('#paginate').html('<center>' + generateBsPagination(response, 'updatePageS') + '</center>');
        }
    }
</script>


<?php include 'template/footer.php' ?>