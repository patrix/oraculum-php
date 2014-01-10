<?php
	class Oraculum {
		public static function LoadContainer ($lib=NULL) {
			if(is_null($lib)):
				throw new Exception('[Erro RO6] Tipo de Container nao informado');
			else:
				$libfile='core/apps/'.$lib.'.php';
				if(file_exists(PATH.$libfile)):
					include_once($libfile);
				else:
					throw new Exception('[Erro RO12] Tipo de Container nao existente ('.$libfile.') ');
				endif;
			endif;
		}

		public static function Load($lib=NULL) {
			if (is_null($lib)):
				throw new Exception('[Erro RO19] Biblioteca nao informada');
			else:
				$libfile='core/general/'.$lib.'.php';
				if (file_exists(PATH.$libfile)):
					include_once($libfile);
				else:
					throw new Exception('[Erro RO25] Biblioteca nao encontrada ('.$libfile.') ');
				endif;
			endif;
		}
	}