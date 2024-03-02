<div class="content-backdrop fade"></div>
</div>
<!-- Content wrapper -->

</div>
<!-- / Layout page -->
</div>

<!-- Overlay -->
<div class="layout-overlay layout-menu-toggle"></div>
</div>
<!-- / Layout wrapper -->

<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->
<script src="<?= asset('sneat/assets/libs/popper/popper.js'); ?>"></script>
<script src="<?= asset('sneat/assets/js/bootstrap.js'); ?>"></script>
<script src="<?= asset('sneat/assets/libs/perfect-scrollbar/perfect-scrollbar.js'); ?>"></script>

<script src="<?= asset('sneat/assets/js/menu.js'); ?>"></script>
<!-- endbuild -->

<!-- Vendors JS -->

<!-- Main JS -->
<script src="<?= asset('sneat/js/main.js'); ?>"></script>

<!-- Page JS -->
<script type="text/javascript">
    $(document).ready(function() {
        clock();
    });

    function clock() {
        $("#currentTime").html(getClock('12', 'my', true));
        setTimeout(clock, 1000);
    }

    async function signOut() {
        if (confirm("Are you sure you want to logout?")) {
            const res = await callApi('post', 'AuthController', {
                'action': 'logout'
            });

            const resCode = parseInt(res.data.code);
            noti(resCode, res.data.message);

            if (isSuccess(resCode)) {
                setTimeout(function() {
                    window.location.href = res.data.redirectUrl;
                }, 700);
            }
        }
    }
</script>

<?= include '_modalGeneral.php' ?>

</body>

</html>