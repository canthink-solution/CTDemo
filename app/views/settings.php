<?php
$loginRequired = true;
$permissionPage = NULL;
$titlePage = "Tetapan";
$currentPage = 'settings';
include 'template/header.php';
?>

<div class="container-fluid flex-grow-1 container-p-y">

    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light"> <?= strtoupper($titlePage) ?> </span>
    </h4>

    <div class="row">

        <div class="col-lg-3 order-2 mb-4">
            <div class="card h-100">
                <div class="card-body">

                </div>
            </div>
        </div>

        <div class="col-lg-9 order-2 mb-4">
            <div class="card h-100">
                <div class="card-body">

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