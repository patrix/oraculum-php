<?php
	Oraculum::Load('Register');
	Oraculum_Register::set('titulo', 'Suporte');
  $content=Oraculum_Register::get('content');
 ?>
<div id="content">
    <?php if(!is_null($content)): ?>
<div class="class">
    <?php echo $content; ?>
</div>
    <?php else: ?>
        Documenta&ccedil;&atilde;o n&atilde;o encontrada ;(
    <?php endif; ?>
</div>
