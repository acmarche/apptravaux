<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 19/09/16
 * Time: 15:09
 */

namespace AcMarche\Travaux\Service;

use AcMarche\Travaux\Entity\Document;
use AcMarche\Travaux\Entity\Intervention;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileHelper
{
    /**
     * @var string
     */
    private $path;
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(\Swift_Mailer $mailer, ParameterBagInterface $parameterBag)
    {
        $this->mailer = $mailer;
        $this->parameterBag = $parameterBag;
        $this->path = $parameterBag->get('ac_marche_travaux.upload.directory');
    }

    public function uploadFile(Intervention $intervention, UploadedFile $file, $fileName)
    {
        $directory = $this->path.DIRECTORY_SEPARATOR.$intervention->getId();

        return $file->move($directory, $fileName);
    }

    public function deleteOneDoc(Document $document)
    {
        $intervention = $document->getIntervention();
        $id = $intervention->getId();
        if (!$id) {
            return false;
        }
        $file = $this->path.DIRECTORY_SEPARATOR.$id.DIRECTORY_SEPARATOR.$document->getFileName();

        $fs = new Filesystem();
        $fs->remove($file);
        $this->sendMail(
            "Delete one doc",
            "Document : ".$document->getId()." Intervention :".$intervention->getId()." Fichier ".$file
        );
    }

    public function deleteAllDocs(Intervention $intervention)
    {
        $id = $intervention->getId();
        $directory = $this->path.DIRECTORY_SEPARATOR.$id;
        if (!$id) {
            return false;
        }
        $fs = new Filesystem();
        $fs->remove($directory);
        $this->sendMail(
            "Delete all",
            "Intervention :".$intervention->getId()." Directory ".$directory
        );
    }

    private function sendMail($sujet, $body)
    {
        $message = new \Swift_Message($sujet, $body);
        $message->setFrom("jf@marche.be")->setTo("jf@marche.be");
        $this->mailer->send($message);
    }
}
