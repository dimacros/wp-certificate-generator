<?php 

namespace Dimacros\Utils;

use Dimacros\Helpers;
use Dimacros\Models\User;
use Mpdf\Mpdf;
use Twig\Loader\ArrayLoader;
use Twig\Environment; 
use Exception;
use WP_Post;

class CertificateGenerator 
{
    public $pdf;
    private $userData, $attributes;

    public function __construct(User $user, WP_Post $template) 
    {    
        $this->userData = $this->mergeData($user);
        $this->pdf = $this->createPdf(
            get_field('page_size', $template->ID), 
            get_field('page_orientation', $template->ID)
        );
        $this->setBackgroundImage(
            get_the_post_thumbnail_url($template->ID, 'full')
        );
        $this->setContent($template->post_content);
        $this->setTitle($template->post_title);
    }

    private function mergeData(User $user) 
    {
        return array_merge(
            $user->details->toArray(), ['full_name' => $user->full_name]
        );
    }

    private function createPdf($pageSize, $pageOrientation) 
    {
        return new Mpdf([
            'mode' => 'UTF-8',
            'format' => "{$pageSize}-{$pageOrientation}"
        ]);
    }

    public function setBackgroundImage($backgroundImage) {
        $this->attributes['backgroundImage'] = $backgroundImage;
    }

    public function setContent($content) {
        $this->attributes['content'] = $this->renderContent($content);
    }

    public function setTitle($title) {
        $this->attributes['title'] = $title;
    }

    private function renderContent($content) 
    {
        $twig = new Environment(
            new ArrayLoader(['content' => $content])
        );

        return $twig->render('content', ['user' => $this->userData]);
    }

    public function generate($filename, $destination) 
    {
        $this->pdf->debug = true;
        $this->pdf->WriteHTML( $this->render() );

        return $this->pdf->Output($filename, $destination);
    }

    private function render() 
    {
        return Helpers\view('templates/default', $this->attributes);
    }
}