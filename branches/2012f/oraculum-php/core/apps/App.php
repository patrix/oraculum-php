<?php
	class Oraculum_App
	{
		private $_templates=array();
		private $_content=NULL;
		private $_Views=NULL;
		private static $_requestClass;

		public static function LoadView() {
			Oraculum::Load('Views');
			return new Oraculum_Views();
		}

		public static function LoadControl() {
			Oraculum::Load('Controls');
			return new Oraculum_Controls();

		}

		public static function LoadModel($model) {
			Oraculum::Load('Models');
			return new Oraculum_Models($model);

		}

		/*public function CreateView() {

		}

		public function CreateControl() {

		}

		public function CreateModel() {

		}*/

		public function View() {
			Oraculum::Load('Views');
			return new Oraculum_Views();
		}

		public function Control() {
			Oraculum::Load('Controls');
			return new Oraculum_Controls();
		}

		public function Model() {
			Oraculum::Load('Models');
			return new Oraculum_Models();
		}

		public function setControlsDirectory($dir) {
			if (file_exists($dir)) {
				define('CONTROL_DIR', $dir);
			} else {
				throw new Exception('[Erro CAA51] Diretorio nao encontrado ('.$dir.')');
			}
		}

		public function setViewsDirectory($dir) {
			if (file_exists($dir)) {
				define('VIEW_DIR', $dir);
			} else {
				throw new Exception('[Erro CAA59] Diretorio nao encontrado ('.$dir.')');
			}
		}

		public function setModelsDirectory($dir) {
			if (file_exists($dir)) {
				define('MODEL_DIR', $dir);
			} else {
				throw new Exception('[Erro CAA67] Diretorio nao encontrado ('.$dir.')');
			}
		}


		public function FrontController() {
			Oraculum::Load('FrontController');
			return new Oraculum_FrontController();
		}
	}
