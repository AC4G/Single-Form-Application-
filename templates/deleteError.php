<?php if(0 === count($deleteRegistration->errors)):?>
    <div class="container">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Deine Reservierung wurde erfolgreich geloescht!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div> 
    </div>
<?php endif;?>
<?php if(count($deleteRegistration->errors) > 0):?>
    <div class="container">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            Der Link ist fehlerhaft!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
<?php endif;?>