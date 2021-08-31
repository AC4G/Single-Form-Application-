<div class="col">
    <label class="form-label">Nachname</label>
    <input type="text" class="form-control<?= $processingInformation->lastNameIsValid?'':' is-invalid'?>" aria-label="Last name" name="lastName" value="<?= $processingInformation->lastName ?>">
</div>