<?php
$loginRequired = true;
$permissionPage = NULL;
$titlePage = "Dashboard";
$currentPage = 'dashboard';
include 'template/header.php';
?>

<div class="container-fluid flex-grow-1 container-p-y">

    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light"> <?= strtoupper($titlePage) ?> </span>
    </h4>

    <div class="col-lg-12 order-2 mb-4">
        <div class="card h-100">
            <div class="card-body">

                <div id="bodyDiv" class="row">
                    <div class="col-xl-12 mb-4">
                        <div id="nodataDiv"> <?= nodata() ?> </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

<script type="text/javascript">
    $(document).ready(async function() {

    });
</script>

<?php include 'template/footer.php' ?>