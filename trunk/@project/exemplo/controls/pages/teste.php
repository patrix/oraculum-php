<?php
    Oraculum::Load('Register');
    Oraculum::Load('Plugins');
	Oraculum_Plugins::Load('contact');
        $campos=array(
            'Nome'=>'Patrick',
            'E-mail'=>'patrick@patrickkaminski.com',
            'Mensagem'=>'Testando 1 2 3');
	$obrigatorios=array('Nome','E-mail','Mensagem');
	$contact=new Oraculum_Contact('smtp.gmail.com', 'nao-responda@oraculumframework.org', 'pza0v9', 465, TRUE);
        $contact->addFields($campos);
        $contact->emailField('E-mail');
        $contact->setTo('patrick@patrickkaminski.com');
        //$contact->setFrom('patrick@sbsti.com');
        $contact->setFrom('nao-responda-para-este-webmaster@patrickkaminski.com');

        $contact->setFromName('Oraculum');
        $contact->setSubject('Teste 1 2 3');
            if ($contact->send()) {
                echo 'Mensagem enviada com sucesso!';
            } else {
                echo 'Ocorreu um erro ao enviar a mensagem!';

            }