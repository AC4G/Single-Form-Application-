<?php if(isPost()): ?>
    <?php if(isset($_SESSION['noErrors'])):?>
        <?php if($_SESSION['noErrors'] == false):?>
            <div class="container">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Du hast einen oder mehrere wichtige Faelder nicht ausgefuehlt, oder irgendwas falsch eingetragen!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        <?php 
        unset($_SESSION['noErrors']);
        endif; ?>
    <?php endif;?>
<?php else: ?>
    <?php if(isset($_SESSION['noErrors'])):?>
        <?php if($_SESSION['noErrors'] == true):?>
            <div class="container">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Deine Reservierung wurde erfolgreich verschickt!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div> 
            </div>
        <?php 
        unset($_SESSION['noErrors']);
        endif;?>
    <?php endif;?>
<?php endif;?>