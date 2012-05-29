<?php

require_once "plugins/pagseguro/PagSeguroLibrary.php";

class Oraculum_Pagseguro {
  private $credentials = null;
  private $paymentRequest = null;
  private $_email = null;
  private $_token = null;
  private $_pedido = null;

  public function getEmail() {
    return $this->_email;
  }

  public function setEmail($_email) {
    $this->_email = $_email;
  }

  public function getToken() {
    return $this->_token;
  }

  public function setToken($_token) {
    $this->_token = $_token;
  }

  public function getPedido() {
    return $this->_pedido;
  }

  public function setPedido($_pedido) {
    $this->_pedido = $_pedido;
  }

  public function __construct($url) {
    $this->paymentRequest = new PagSeguroPaymentRequest();
    $this->paymentRequest->setCurrency('BRL');
    $this->paymentRequest->setShippingType(3);
    $this->paymentRequest->setRedirectUrl($url);
  }
  public function addItem($item,$produto,$qtd,$preco,$peso,$frete){
    $this->paymentRequest->addItem($item, $produto,$qtd,$preco,$peso,$frete);
  }
  public function entrega($nome,$email,$area,$telefone,$cep,$rua,$numero,$complemento,$bairro,$cidade,$estado){
    $this->paymentRequest->setShippingAddress($cep,$rua,$numero, $complemento, $bairro, $cidade, $estado, 'BRA');
    $this->paymentRequest->setSender($nome, $email, $area, $telefone);
  }
  public function finaliza() {
    $this->paymentRequest->setReference($this->_pedido);
    try {
        /*
         * #### Crendencials ##### 
         * Substitute the parameters below with your credentials (e-mail and token)
         * You can also get your credentails from a config file. See an example:
         * $credentials = PagSeguroConfig::getAccountCredentials();
         */
        $this->credentials = new PagSeguroAccountCredentials($this->_email,$this->_token);
        // Register this payment request in PagSeguro, to obtain the payment URL for redirect your customer.
        $url = $this->paymentRequest->register($this->credentials);
        $texto = '<h2>Criando requisi&ccedil;&atilde;o de pagamento</h2>';
        $texto .= '<p>URL do pagamento: <strong>'.$url.'</strong></p>';
        $texto .= '<p><a title="URL do pagamento" href="'.$url.'">Ir para URL do pagamento.</a></p>';
    } catch (PagSeguroServiceException $e) {
        die($e->getMessage());
    }
    return $url;
  }
  public function verificaTransacao($transacao){
    $this->credentials = new PagSeguroAccountCredentials($this->_email,$this->_token);
    $transaction = PagSeguroTransactionSearchService::searchByCode($this->credentials,$transacao);  
    return $transaction;
  }
}
