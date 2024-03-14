<form id="formUser" action="user/save" method="POST">

    <div class="row">
        <div class="col-md-12">
            <label class="form-label"> Full Name <span class="text-danger">*</span></label>
            <input type="text" id="name" name="name" class="form-control" maxlength="250" autocomplete="off" onKeyUP="this.value = this.value.toUpperCase();" required>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-md-6">
            <label class="form-label"> Preferred Name <span class="text-danger">*</span></label>
            <input type="text" id="user_preferred_name" name="user_preferred_name" class="form-control" maxlength="15" autocomplete="off" required>
        </div>
        <div class="col-md-6">
            <label class="form-label"> Email <span class="text-danger">*</span></label>
            <input type="email" id="email" name="email" class="form-control" maxlength="120" autocomplete="off" required>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-md-4">
            <label class="form-label"> Gender <span class="text-danger">*</span></label>
            <select id="user_gender" name="user_gender" class="form-control">
                <option value=""> - Select - </option>
                <option value="1"> Male </option>
                <option value="0"> Female </option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label"> Birth Date </label>
            <input type="date" id="user_dob" name="user_dob" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label"> Status </label>
            <select id="user_status" name="user_status" class="form-control">
                <option value=""> - Select - </option>
                <option value="1"> Active </option>
                <option value="0"> Inactive </option>
            </select>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-lg-12">
            <span class="text-danger">* Indicates a required field</span>
            <center>
                <input type="hidden" id="id" name="id" class="form-control" readonly>
                <input type="hidden" id="action" name="action" value="" class="form-control" readonly>
                <button type="submit" id="submitBtn" class="btn btn-info"> <i class='fa fa-save'></i> &nbsp; Simpan </button>
            </center>
        </div>
    </div>
</form>

<script>
    // This getPassData() method is REQUIRED in modal to replace $(document).ready
    async function getPassData(baseUrl, data) {
        // await getDataCount();
    }

    $("#formUser").submit(function(event) {
		event.preventDefault();

		if (validateData()) {

			const form = $(this);
			const url = form.attr('action');

			Swal.fire({
				title: 'Are you sure?',
				html: "Form will be submitted!",
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Yes, Confirm!',
				reverseButtons: true,
                customClass: {
					container: 'swal2-customCss'
				},
			}).then(
				async (result) => {
					if (result.isConfirmed) {
						const res = await submitApi(url, form.serializeArray(), 'formUser');
						if (isSuccess(res)) {
							if (isSuccess(res.data.code)) {
								noti(res.status, 'Save');
								getDataList();
							} else {
								noti(400, res.data.message)
							}
						}
					}
				});
		} else {
			validationJsError('toastr', 'single'); // single or multi
		}
	});

    function validateData() {

        const rules = {
            'name': 'required|min:5|max:255',
            'user_preferred_name': 'required|min:3|max:10',
            'email': 'required|email|min:5|max:150',
            'user_gender': 'required|integer',
            'user_dob': 'required',
            'user_status': 'required|integer',
        };

        const customMessage = {
            'name': 'Full Name',
            'user_preferred_name': 'Preferred Name',
            'email': 'Email',
            'user_gender': 'Gender',
            'user_dob': 'Birth Date',
            'user_status': 'Status',
        };

        return validationJs(rules, customMessage);
    }
</script>