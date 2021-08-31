<div class="col-12">
    <label class="form-label">Telefonnummer</label>
    <input type="tel" class="form-control<?= $processingInformation->mobileNumberIsValid?'':' is-invalid'?>" name="mobileNumber" value="<?= $processingInformation->mobileNumber ?>">
</div>