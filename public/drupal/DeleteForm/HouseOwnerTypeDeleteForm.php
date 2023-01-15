<?php

namespace Drupal\residence\DeleteForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Url;
use Drupal\Core\Database\Database;
use Drupal\residence\Controller\UtilityController;

/**
 * Class HouseOwnerTypeDeleteForm
 * @package Drupal\residence\DeleteForm
 */
class HouseOwnerTypeDeleteForm extends ConfirmFormBase
{

    public $id;

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'house_owner_type_delete_form';
    }

    public function getQuestion()
    {
        return UtilityController::generateTitle($this->id, 'house_owner_types', 'name');
    }

    public function getCancelUrl()
    {
        return new Url('house_owner_type.index');
    }

    public function getDescription()
    {
        return t('Do you want to delete this house owner type?');
    }

    /**
     * {@inheritdoc}
     */
    public function getConfirmText()
    {
        return t('Delete it!');
    }

    /**
     * {@inheritdoc}
     */
    public function getCancelText()
    {
        return t('Cancel');
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state, $id = NULL)
    {
        $this->id = $id;
        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        parent::validateForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $con = Database::getConnection();
        $qry = $con->select('house_owner_types', 'h')
            ->condition('id', $this->id)
            ->fields('h');
        $data = $qry->execute()->fetchAssoc();

        $query = \Drupal::database();
        $query->delete('house_owner_types')
            ->condition('id', $this->id)
            ->execute();
            
        if ($query) {
            UtilityController::setLog("DELETE", "House owner type", $this->id, $data);
        }

        \Drupal::messenger()->addStatus('Succesfully deleted House Owner Type.');
        $form_state->setRedirect('house_owner_type.index');
    }
}
