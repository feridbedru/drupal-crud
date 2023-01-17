<?php

namespace Drupal\residence\DeleteForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Url;
use Drupal\residence\Controller\UtilityController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\residence\HousePurposeRepository;

/**
 * Class HousePurposeDeleteForm
 * @package Drupal\residence\DeleteForm
 */
class HousePurposeDeleteForm extends ConfirmFormBase
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
     * Construct a new controller.
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
        return 'house_purpose_delete_form';
    }

    public function getQuestion()
    {
        return UtilityController::generateTitle($this->id, 'house_purposes', 'name');
    }

    public function getCancelUrl()
    {
        return new Url('residence.house_purpose.index');
    }

    public function getDescription()
    {
        return t('Do you want to delete this house purpose?');
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
        $data = $this->repository->load(['id' => $this->id]);
        $this->repository->delete(['id' => $this->id]);
        UtilityController::setLog("DELETE", "House purpose", $this->id, $data);
        $form_state->setRedirect('residence.house_purpose.index');
    }
}
