<h3>Form Data</h3>
<div class="message-notification">
    <p id="error-message"></p>
    <p id="success-message"></p>
</div>
<form action="javascript:void(0)" id="ms_cu_form" enctype="multipart/form-data">
    <div class="uploader">
        <label for="csv_data_file">Upload CSV</label>
        <input type="file" name="csv_data_file">

    </div>
    <div class="controller">
        <input type="hidden" name="action" value="ms_cu_submit_form_data">
        <!-- <input type="reset" value="reset"> -->
        <button type="submit"> Submit </button>
    </div>
</form>