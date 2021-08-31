<div class="mb-3">
    <label class="form-label label-10">Zu erwartende Personen</label>
    <select class="form-select pointer<?= $processingInformation->personsIsValid?'':' is-invalid'?>" aria-label="Default select example" name="persons" required>
        <option class="disabled" selected disabled>Waehl die Anzahl deiner Gaeste aus!</option>
        <option value="1" <?= $processingInformation->selected($processingInformation->persons, '1');?>>1</option>
        <option value="2" <?= $processingInformation->selected($processingInformation->persons, '2');?>>2</option>
        <option value="3" <?= $processingInformation->selected($processingInformation->persons, '3');?>>3</option>
        <option value="4" <?= $processingInformation->selected($processingInformation->persons, '4');?>>4</option>
        <option value="5" <?= $processingInformation->selected($processingInformation->persons, '5');?>>5</option>
        <option value="6" <?= $processingInformation->selected($processingInformation->persons, '6');?>>6</option>
        <option value="7" <?= $processingInformation->selected($processingInformation->persons, '7');?>>7</option>
        <option value="8" <?= $processingInformation->selected($processingInformation->persons, '8');?>>8</option>
        <option value="9" <?= $processingInformation->selected($processingInformation->persons, '9');?>>9</option>
        <option value="10" <?= $processingInformation->selected($processingInformation->persons, '10');?>>10</option>
        <option value="11" <?= $processingInformation->selected($processingInformation->persons, '11');?>>11</option>
        <option value="12" <?= $processingInformation->selected($processingInformation->persons, '12');?>>12</option>
        <option value="13" <?= $processingInformation->selected($processingInformation->persons, '13');?>>13</option>
        <option value="14" <?= $processingInformation->selected($processingInformation->persons, '14');?>>14</option>
        <option value="15" <?= $processingInformation->selected($processingInformation->persons, '15');?>>15</option>
        <option value="16" <?= $processingInformation->selected($processingInformation->persons, '16');?>>16</option>
        <option value="17" <?= $processingInformation->selected($processingInformation->persons, '17');?>>17</option>
        <option value="18" <?= $processingInformation->selected($processingInformation->persons, '18');?>>18</option>
        <option value="19" <?= $processingInformation->selected($processingInformation->persons, '19');?>>19</option>
        <option value="20" <?= $processingInformation->selected($processingInformation->persons, '20');?>>20</option>
    </select>
</div>