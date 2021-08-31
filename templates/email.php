<div class="mb-3">
    <label class="form-label">Email Adresse</label>
    <input type="email" class="form-control<?= $processingInformation->emailIsValid?'':' is-invalid'?>" id="exampleFormControlInput1" name="email" value="<?= $processingInformation->email ?>">
</div>