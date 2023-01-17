<?php

namespace Drupal\residence\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\residence\HousePurposeRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;
use Drupal\residence\Controller\UtilityController;


/**
 * Class HousePurposeController
 * @package Drupal\residence\Controller
 */
class HousePurposeController extends ControllerBase
{
    /**
     * The repository for our specialized queries.
     *
     * @var \Drupal\residence\HousePurposeRepository
     */
    protected $repository;

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container)
    {
        $controller = new static($container->get('house_purpose.repository'));
        $controller->setStringTranslation($container->get('string_translation'));
        return $controller;
    }

    /**
     * Construct a new controller.
     *
     * @param \Drupal\residence\HousePurposeRepository $repository
     *   The repository service.
     */
    public function __construct(HousePurposeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return array
     */
    public function getTitle($id)
    {
        return UtilityController::generateTitle($id, 'house_purposes', 'name');
    }

    /**
     * @return array
     */
    public function show($id)
    {
        $build['collection'] = array(
            '#theme' => 'house_purpose_list',
            '#attached' => ['library' => ['residence/list']],
            '#items' => (array)$this->repository->load($id),
            '#backurl' => Url::fromRoute('residence.house_purpose.index', [])->toString()
        );

        return $build;
    }

}
