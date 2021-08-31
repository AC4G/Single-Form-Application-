<?php 
require_once TEMPLATES_DIR . '/header.php'; 
require_once FUNCTION_DIR . '/getInformation.php';
?> 
        <div class="container label-60">
            <div class="row">
                <div class="col-12">
                    <h3 class="text-align-center noHover">Reservierung</h1>
                    <?php require TEMPLATES_DIR . '/executeError.php';?>
                    <div class="container mt-5 mb-5" style="max-width: 576px;">
                        <form method="post" class="row g-3">
                            <div class="mb-3">
                                <label class="form-label">Tag</label>
                                <?php require TEMPLATES_DIR . '/date.php';?>
                            </div>
                            <div class="mb-3">
                                <label class="form-label label-10" style="margin-top: 26px;">Uhrzeit (Wir haben von 14:00 bis 22:00 geoeffnet(Reservierung bis 20:00)!)</label>
                                <?php require TEMPLATES_DIR . '/time.php';?>
                            </div>
                            <?php require TEMPLATES_DIR . '/persons.php';?>
                            <div class="mb-3">
                                <div class="row g-3">
                                    <?php require TEMPLATES_DIR . '/firstName.php';?>
                                    <?php require TEMPLATES_DIR . '/lastName.php';?>
                                </div>
                            </div>
                            <?php require TEMPLATES_DIR . '/email.php';?>
                            <?php require TEMPLATES_DIR . '/mobileNumber.php';?>
                            <div class="mb-3">
                                <label class="form-label">Vermerk</label>
                                <textarea class="form-control" name="note"><?= $processingInformation->note ?></textarea>
                                <p class="text-align-center pi">Besondere Wuensche zB. >> Tisch am Fenster...</p>
                            </div>
                            <div class="mb-3">
                                <input type="hidden" name="csrfToken" value="<?= $_SESSION['csrfToken']?>">
                                <button class="btn position-absolute start-50 translate-middle-x" type="submit" style="background: #de5c9d; color: white;">Absenden</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
<?php require_once TEMPLATES_DIR . '/footer.php'; ?> 