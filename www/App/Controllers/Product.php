<?php

namespace App\Controllers;

use App\Models\Articles;
use App\Utility\Upload;
use \Core\View;

/**
 * Product controller
 */
class Product extends \Core\Controller
{

    /**
     * Affiche la page d'ajout
     * @return void
     */
    public function indexAction()
    {
        $error = null;

        if(isset($_POST['submit'])) {

            try {
                $f = $_POST;

                // Validation de l'image
                if (!isset($_FILES['picture']) || $_FILES['picture']['error'] === UPLOAD_ERR_NO_FILE) {
                    throw new \Exception("Une image est obligatoire pour ajouter un produit.");
                }

                // TODO: Validation

                $f['user_id'] = $_SESSION['user']['id'];
                $id = Articles::save($f);

                $pictureName = Upload::uploadFile($_FILES['picture'], $id);

                Articles::attachPicture($id, $pictureName);

                header('Location: /product/' . $id);
                exit;
            } catch (\Exception $e){
                $error = $e->getMessage();
            }
        }

        View::renderTemplate('Product/Add.html', [
            'error' => $error
        ]);
    }

    /**
     * Affiche la page d'un produit
     * @return void
     */
    public function showAction()
    {
        $id = $this->route_params['id'];

        try {
            Articles::addOneView($id);
            $suggestions = Articles::getSuggest();
            $article = Articles::getOne($id);
        } catch(\Exception $e){
            var_dump($e);
        }

        View::renderTemplate('Product/Show.html', [
            'article' => $article[0],
            'suggestions' => $suggestions
        ]);
    }

    /**
     * @return void
     */
    public function contactAction()
    {
        $id = $this->route_params['id'];

        try {
            $suggestions = Articles::getSuggest();
            $article = Articles::getOne($id);
        } catch(\Exception $e){
            var_dump($e);
        }

        View::renderTemplate('Product/Contact.html', [
            'article' => $article[0],
            'suggestions' => $suggestions
        ]);
    }

    /**
     * Traite l'envoi du formulaire de contact
     * @return void
     */
    public function sendContactAction()
    {
        if(isset($_POST['submit'])) {
            $f = $_POST;
            
            if(empty($f['name']) || empty($f['email']) || empty($f['message'])) {
                $error = "Tous les champs sont obligatoires.";
                header('Location: /product/' . $f['product_id'] . '/contact');
                exit;
            }

            
            header('Location: /product/' . $f['product_id'] . '?contact=success');
            exit;
        }
        
        header('Location: /');
        exit;
    }
}
