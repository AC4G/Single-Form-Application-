<div class="col">
    <label class="form-label">Vorname</label>
    <input type="text" class="form-control<?= $processingInformation->firstNameIsValid?'':' is-invalid'?>" aria-label="First name" name="firstName" value="<?= $processingInformation->firstName ?>">
</div>