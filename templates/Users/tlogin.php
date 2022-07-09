<?php

use Cake\Core\Configure;

?>

<?= $this->Html->script('https://unpkg.com/vue-telegram-login', ['block' => true]) ?>


<div class="col-md-4 offset-md-4">
  <div class="card">
    <img class="card-img-top mx-auto mt-4" style="max-width:200px;" src="<?= Configure::read('MailLogo'); ?>" alt="Accedi a Cyclomap">
    <div class="card-body">
      Benvenuto in <b>CycloMap</b>, il sistema di gestione del cicloturismo di BikeSquare.<br>
      Per accedere a questa pagina è necessario essere autorizzati con il proprio account Telegram.
    </div>
    <div class="card-footer" style="text-align:center">
        <div style="margin:0 auto;">  
        <vue-telegram-login 
            mode="callback"
            telegram-login="<?= Configure::read('Telegram.BotUsername'); ?>"
            @callback="telegramLogin" />
        </div>
        
    </div>

  </div>

</div>
