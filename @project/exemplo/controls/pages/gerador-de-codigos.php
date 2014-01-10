<?php
    Oraculum::Load('Models');
	$bancos=PDO::getAvailableDrivers();
	Oraculum_Register::set('bancos', $bancos);
	define('NL', "\n\r\t");
	$firstkey=NULL;
	
	if (post('send')):
		$banco=post('banco');
		$servidor=post('servidor');
		$usuario=post('usuario');
		$senha=post('senha');
		$base=post('base');
		$tabela=post('tabela');
		$arquivo=strtolower($tabela);
		$classe=ucwords($tabela);

		if ($base!=''):
			$model='<'.'?'.'php'.NL.'$this->_dsn=\''.$banco.'://'.$usuario.':'.$senha.'@'.$servidor.'/'.$base.'\';';

			try{
			    $db=new Oraculum_Models();
				$db->setDsn($banco.'://'.$usuario.':'.$senha.'@'.$servidor.'/'.$base);
				$db->PDO();
				$dbo=new DBO();
				DBO::execSQL('USE '.$base.';');
				$query=DBO::execSQL('SHOW KEYS FROM '.$tabela.' WHERE Key_name = \'PRIMARY\';')->fetchAll();

				//$d=$query->fetchAll();
				//var_dump($query);
				$keys=array();
				foreach($query as $r):
					if(is_null($firstkey))
						$firstkey=$r['Column_name'];
					$keys[]=$r['Column_name'];
				endforeach;
				$keys='array(\''.implode('\',\'', $keys).'\')';
					
				
				$control='<'.'?'.'php'.NL.
							'Oraculum::Load(\'Models\');'.NL.
							'Oraculum::Load(\'Plugins\');'.NL.
							'Oraculum::Load(\'HTTP\');'.NL.
							'// Carrega o arquivo de conexao com o banco'.NL.
							'$db=new Oraculum_Models(\''.$banco.'\');'.NL.NL.
							'// Carrega dinamicamente uma classe para a tabela '.$tabela.NL.
							'$db->LoadModelClass(\''.$tabela.'\');'.NL.
							'$action=Oraculum_Request::getvar(\''.$arquivo.'\');'.NL.NL.
							'// Verifica se a acao e\' permitida no sistema'.NL.
							'if (!(in_array($action, array(\'cadastrar\', \'listar\', \'alterar\', \'excluir\'))))'.NL.
							'	$action=\'listar\';'.NL.NL.
							'// Verifica se existe uma funcao com o mesmo nome da acao'.NL.
							'if (is_callable($action))'.NL.
							'    call_user_func($action); // Chama a funcao'.NL.
							'else'.NL.
							'// Senao chama a funcao listar (esse e\' um tratamento de seguranca)'.NL.
							'if (is_callable(\'listar\'))'.NL.
							'	call_user_func(\'listar\');'.NL.NL.					
							'function listar() {'.NL.
							'	// Carrega o plugin Oraculum_Datagrid'.NL.
							'	Oraculum_Plugins::Load(\'datagrid\');'.NL.
							'	// Cria uma instancia da classe'.NL.
							'	$tb=new '.$classe.'();'.NL.
							'   $tb->setKey('.$keys.');'.NL.
							'	// Carrega todos os registros da tabela/entidade'.NL.
							'	$regs=$tb->getAll();'.NL.
							'	// Cria uma instancia de do plugin Oraculum_Datagrid'.NL.
							'	$grid=new Oraculum_Datagrid($regs);'.NL.NL.
							'	// Define a classe CSS da tabela'.NL.
							'	$grid->setTableClass(\'table table-bordered table-striped\');'.NL.
							'	// Define a classe CSS do botao de atualizacao'.NL.
							'	$grid->setUpdateClass(\'btn btn-primary\');'.NL.
							'	// Define a classe CSS do botao de exclusao'.NL.
							'	$grid->setDeleteClass(\'btn btn-danger\');'.NL.
							'	// Determina o padrao de URL para o link de atualizar'.NL.
							'	$grid->setUpdateURL(URL.\''.$arquivo.'/alterar/%id%\');'.NL.
							'	// Determina o padrao de URL para o link de excluir'.NL.
							'	$grid->setDeleteURL(URL.\''.$arquivo.'/excluir/%id%#confirm%id%" onclick="if(confirm(\\\'Tem certeza que deseja excluir?\\\')){return true;}else{return false;}\');'.NL.
							'	// Define o label do botao de atualizacao'.NL.
							'	$grid->setUpdateLabel(\'<i class="icon-pencil icon-white"></i> Alterar\');'.NL.
							'	// Define o label do botao de exclusao'.NL.
							'	$grid->setDeleteLabel(\'<i class="icon-remove icon-white"></i> Excluir\');'.NL.
							/*'	// Adiciona codigo HTML na celula de acoes (onde ficam os botoes)'.NL.
							'	$grid->setAdictionalActionHTML(\'<div id="confirm%id%" class="modal hide fade">'.NL.
							'		<div class="modal-header">'.NL.
							'		  <button class="close" data-dismiss="modal">&times;</button>'.NL.
							'		  <h3>Confirma&ccedil;&atilde;o</h3>'.NL.
							'		</div>'.NL.
							'		<div class="modal-body">'.NL.
							'		  <p>Voc&ecirc; tem certeza que quer remover este registro?</p>'.NL.
							'		</div>'.NL.
							'		<div class="modal-footer">'.NL.
							'		  <a href="'.URL.''.$arquivo.'/excluir/%id%" class="btn btn-primary">OK</a>'.NL.
							'		  <a href="#" class="btn" data-dismiss="modal" >Cancelar</a>'.NL.
							'		</div></div>\');'.NL.*/
							'	// Define o texto que deve ser exibido caso nao existem registros'.NL.
							'	$grid->setNoRecordsFound(\'Nenhum registro encontrado!\');'.NL.
							'	// Gera o HTML do grid'.NL.
							'	$grid=$grid->generate();'.NL.
							'	// Armazena o grid num registrador chamado grid que sera\' lido na view'.NL.
							'	Oraculum_Register::set(\'grid\', $grid);'.NL.
							'	// Armazena os registros num registrador chamado regs que sera\' lido na view'.NL.
							'	Oraculum_Register::set(\'regs\', $regs);'.NL.
							'}'.NL.NL.

							'function excluir() {'.NL.
							'	// Cria uma instancia da classe'.NL.
							'	$tb=new '.$classe.'();'.NL.
							'   $tb->setKey('.$keys.');'.NL.
							'	// Captura o que estiver apos /excluir/ na URL'.NL.
							'	$id=(int)Oraculum_Request::getvar(\'excluir\');'.NL.
							'	// Carrega todos os registros que tiverem o ID relacionado'.NL.
							'	$reg=$tb->getBy'.ucwords($firstkey).'($id);'.NL.
							'	if (sizeof($reg)>0) {'.NL.
							'		// Se encontrar algum registro, o mesmo e\' apagado'.NL.
							'		$reg->delete();'.NL.
							'	}'.NL.
							'	// Redireciona para a pagina de listagem'.NL.
							'	Oraculum_HTTP::redirect(URL.\''.$arquivo.'\');'.NL.
							'}'.NL.NL.
							
							'function cadastrar() {'.NL.
							'  // No momento ainda e\' necessario criar manualmente o cadastro'.NL.
							'}'.NL.NL.
							
							'function alterar() {'.NL.
							'  // No momento ainda e\' necessario criar manualmente a alteracao'.NL.
							'}'.NL.NL.
							
							'if ($action==\'listar\'):'.NL.
							'  Oraculum_WebApp::LoadView()'.NL.
							'     ->AddTemplate(\'geral\')'.NL.
							'	  ->LoadPage(\''.$arquivo.'-listar\'); // Carrega a view de listagem'.NL.
							'endif;';
							
				$viewlistar='<'.'?'.'php $grid=Oraculum_Register::get(\'grid\'); ?'.'>'."\n\r".
							'<'.'?'.'php echo $grid; ?'.'>';
			
				Oraculum_Register::set('model', $model);
				Oraculum_Register::set('control', $control);
				Oraculum_Register::set('viewlistar', $viewlistar);
				Oraculum_Register::set('nomebanco', $banco);
				Oraculum_Register::set('arquivo', $arquivo);
			} catch(Exception $e){
				Oraculum_Register::set('msg', 'Ocorreu algum problema ao capturar as informa&ccedil;&otilde;es do banco');
				Oraculum::Load('Logs');
				Oraculum_Alias::LoadAlias('Logs');				
			}

		endif;
	endif;
	Oraculum_WebApp::LoadView()
		->AddTemplate('geral')
		->LoadPage('gerador-de-codigos');        
