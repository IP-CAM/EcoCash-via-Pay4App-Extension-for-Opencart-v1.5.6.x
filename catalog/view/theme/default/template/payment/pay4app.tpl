
<form action="<?php echo $action; ?>" method="post">
  
  <input type="hidden" name="merchantid" value="<?php echo $merchantid; ?>" />
  <input type="hidden" name="orderid" value="<?php echo $orderid; ?>" />
  <input type="hidden" name="amount" value="<?php echo $total; ?>" />
  <input type="hidden" name="signature" value="<?php echo $signature; ?>" />
  <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
  <input type="hidden" name="transferpending" value="<?php echo $redirect; ?>" />     
  
  <div class="buttons">
    <div class="right">
      <input type="submit" value="<?php echo $button_confirm; ?>" class="button" />
    </div>
  </div>
</form>
