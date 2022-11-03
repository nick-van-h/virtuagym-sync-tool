<div class="row row--align box-xs">
    <span>Connected account:</span><span>{Testuser}</span>
</div>
<div class="row row--align box-xs">
    <button>Connect/Switch</button>
</div>
<div class="row row--align box-xs">
    <div class="col-2-3 select"><select name="calendar-agendas">
        <!-- Dynamically load calendars on page load -->
        <!-- <option value="1">xxx</option> -->
    </select></div>
    <div class="col-1-3">
        <button><i class="fas fa-refresh"></i></button> <!-- TODO: test icon -->
    </div>
</div>
<div class="status-message status-message--hidden">
    <span><?php echo($settings->getVirtuagymMessage('virtuagym')); ?></span>
</div>