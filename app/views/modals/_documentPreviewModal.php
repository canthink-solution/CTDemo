<div id="documentPreviewDiv"></div>

<script>
    // This getPassData() method is REQUIRED in modal to replace $(document).ready
    async function getPassData(baseUrl, data) {
        await getDataCount();
    }

    async function getDataCount() {
        const res = await callApi('post', "ExampleController", {
            'action': 'getDataModal',
            'id': 2
        });

        if (isSuccess(res)) {
            const dataRes = res.data;
            $('#documentPreviewDiv').html(`<b>Test 87</b> : Return data using fetch() method with conditions. <br> Result : ${JSON.stringify(dataRes)} <br> <hr>`);
        }
    }
</script>