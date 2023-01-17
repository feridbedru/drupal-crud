<?php

namespace Drupal\residence\Form;

use Drupal\Core\Database\Connection;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\residence\Controller\UtilityController;
use Drupal\residence\HousePurposeRepository;
use Drupal\residence\Controller\UtilityController;


class HousePurposeForm extends FormBase
{

    public $id;

    /**
     * The repository for our specialized queries.
     *
     * @var \Drupal\residence\HousePurposeRepository
     */
    protected $repository;

    /**
     * The Database Connection.
     *
     * @var \Drupal\Core\Database\Connection
     */
    protected $database;

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container)
    {
        $controller = new static($container->get('house_purpose.repository'), , $container->get('database'));
        $controller->setStringTranslation($container->get('string_translation'));
        return $controller;
    }

    /**
     * Construct a new form.
     *
     * @param \Drupal\residence\HousePurposeRepository $repository
     *   The repository service.
     * 
     * @param \Drupal\Core\Database\Connection $database
     *   The database connection.
     */
    public function __construct(HousePurposeRepository $repository, Connection $database)
    {
        $this->repository = $repository;
        $this->database = $database;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'house_purpose_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state, $id = NULL)
    {
        $con = $this->database;
        $data = array();
        $this->id = $id;

        if (isset($id)) {
            $query = $con->select('house_purposes', 'h')
                ->condition('id', $id)
                ->fields('h');
            $data = $query->execute()->fetchAssoc();
        }

        $form['group1'] = [
            '#type'  => 'container',
            '#open'  => true,
            '#attributes' => ['class' => 'row'],
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


        $form['status'] = [
            '#type' => 'checkbox',
            '#title' => $this->t('Active'),
            '#default_value' => (isset($data['status'])) ? $data['status'] : '',
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
        $data = [
            'name' => $form_state->getValue('name'),
            'description' => $form_state->getValue('description')['value'],
            'status' => $form_state->getValue('status'),
        ];

        $id = $this->id;
        if (isset($id)) {
            $qry = $this->database->select('house_purposes', 'h')
                ->condition('id', $id)
                ->fields('h');
            $original = $qry->execute()->fetchAssoc();
            $data['id'] = $id;
            $edit = $this->repository->update($data);
            if ($edit) {
                $util->setLog("EDIT", "House purpose", $id, $original, $data);
            }
        } else {
            $data['created_by'] = \Drupal::currentUser()->id();
            $data['created_at'] = date('Y-m-d H:i:s');
            $new = $this->repository->insert($data);
            if ($new) {
                $util->setLog("ADD", "House purpose", $new, $data);
            }
        }

        $url = new Url('residence.house_purpose.index');
        $response = new RedirectResponse($url->toString());
        $response->send();
    }
}
