<?php

namespace Drupal\residence\Form;

use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\residence\Controller\UtilityController;
use Drupal\Core\StringTranslation\StringTranslationTrait;


class HouseOwnerTypeForm extends FormBase
{
    use StringTranslationTrait;

    public $id;

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'house_owner_type_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state, $id = NULL)
    {
        $con = Database::getConnection();
        $data = array();
        $this->id = $id;

        if (isset($id)) {
            $query = $con->select('house_owner_types', 'h')
                ->condition('id', $id)
                ->fields('h');
            $data = $query->execute()->fetchAssoc();
        }

        $util = new UtilityController();
        $selectedLangCode = $util->getLanguage()['selectedLanguage'];
        $language_list = $util->getLanguage()['languageList'];

        $form['group1'] = [
            '#type'  => 'container',
            '#open'  => true,
            '#attributes' => ['class' => 'row'],
        ];

        $form['group1']['language'] = [
            '#type' => 'select',
            '#title' => $this->t('Language'),
            '#options' => $language_list,
            '#attributes' => array('class' => array('form-control')),
            '#default_value' => $selectedLangCode,
            '#prefix' => '<div class="form-group col-md-2">',
            '#suffix' => '</div>',
        ];

        $form['group1']['name'] = [
            '#title' => $this->t('Name'),
            '#required' => TRUE,
            '#type' => 'textfield',
            '#maxlength' => 255,
            '#default_value' => (isset($data['name'])) ? $data['name'] : '',
            '#attributes' => array('class' => array('form-control')),
            '#prefix' => '<div class="form-group col-md-4">',
            '#suffix' => '</div>',
        ];

        $form['group1']['description'] = [
            '#title' => $this->t('Description'),
            '#required' => FALSE,
            '#type' => 'text_format',
            '#format' => 'basic_html',
            '#default_value' => (isset($data['description'])) ? $data['description'] : '',
            '#attributes' => array('class' => array('form-control')),
            '#prefix' => '<div class="form-group col-md-4">',
            '#suffix' => '</div>',
        ];


        $form['active'] = [
            '#type' => 'checkbox',
            '#title' => $this->t('Active'),
            '#default_value' => (isset($data['active'])) ? $data['active'] : '',
            '#attributes' => ['class' => ['mt-4 ']],
        ];

        $form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Save'),
            '#button_type' => 'primary'
        ];

        $form['#attached']['library'][] = 'residence/bootstrap';

        $form['#cache']['max-age'] = 0;

        return $form;
    }

    /**
     * @param array $form
     * @param FormStateInterface $form_state
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        if (!$form_state->getValue('name') || empty($form_state->getValue('name'))) {
            $form_state->setErrorByName('name', $this->t('name is required.'));
        }

    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $util = new UtilityController();
        $slug = $util->slugify('house_owner_types', $form_state->getValue('name'));
        $data = array(
            'name' => $form_state->getValue('name'),
            'description' => $form_state->getValue('description')['value'],
            'slug' => $slug,
            'active' => $form_state->getValue('active'),
            'language' => $form_state->getValue('language'),
        );

        $status = '';
        $id = $this->id;
        if (isset($id)) {
            $con = Database::getConnection();
            $qry = $con->select('house_owner_types', 'h')
                ->condition('id', $id)
                ->fields('h');
            $original = $qry->execute()->fetchAssoc();

            $edit = \Drupal::database()->update('house_owner_types')->fields($data)->condition('id', $id)->execute();

            if ($edit) {
                $util->logger("EDIT", "House owner type", $id, $original, $data);
                $status = 'Updated House owner type Succesfully';
            }
        } else {
            $new = \Drupal::database()->insert('house_owner_types')->fields($data)->execute();
            if ($new) {
                $util->logger("ADD", "House owner type", $new, $data);
                $status = 'Created House owner type Succesfully';
            }
        }

        \Drupal::messenger()->addStatus($status);
        $url = new Url('house_owner_type.index');
        $response = new RedirectResponse($url->toString());
        $response->send();
    }
}
