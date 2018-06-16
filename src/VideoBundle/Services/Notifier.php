<?php

namespace VideoBundle\Services;

use Swift_Message;

/**
 * Description of Compteur
 *
 * @author eningabiye
 */
class Notifier {

    private $email_admin = null;
    private $mailer = null;

    public function __construct($mailer, $email_admin) {
        $this->email_admin = $email_admin;
        $this->mailer = $mailer;
    }

    /**
     * 
     * @param $view la vue qui contient le texte à envoyer
     *  si un film est ajouté ou supprimé
     */
    public function notify($view) {
        $notification = (new Swift_Message('Vidéotheque'))->setFrom('noreply@videotheque.com')
                ->setTo($this->email_admin)
                ->setBody($view, 'text/html');
        $this->mailer->send($notification);
    }

}
